<?php

include("../dbconn.php");
try {
    // Retrieve the raw POST data
    $jsonData = file_get_contents('php://input');
    // Convert the JSON data to a PHP object or array
    $data = json_decode($jsonData);

    if (isset($data->adoptId) && isset($data->userId)) {
        $currentTime = date('Y-m-d H:i:s');
        $adoptId = $data->adoptId;
        $userId = $data->userId;

        // FIND old data
        $sqlFind = "SELECT * FROM `adopt_transactions` WHERE `AdoptTransactionId` = ?";
        $resultFind = $conn->execute_query($sqlFind, [$adoptId]);

        while ($row = $resultFind->fetch_assoc()) {
            $adoptTransactionId = $row['AdoptTransactionId'];
            $userId = $row['UserId'];
            $petId = $row['PetId'];
            $adopterAddress = $row['AdopterAddress'];
            $adopterHasPet = $row['AdopterHasPet'];
            $adopterHasChild = $row['AdopterHasChild'];
            $adopterChildDetail = $row['AdopterChildDetail'];
            $adopterTenancyAllow = $row['AdopterTenancyAllow'];
            $adopterAttention = $row['AdopterAttention'];
            $adopterVetClinic = $row['AdopterVetClinic'];
            $adopterPetInsurance = $row['AdopterPetInsurance'];
            $adopterDayTime = $row['AdopterDayTime'];
            $adopterRelocate = $row['AdopterRelocate'];
            $adopterPetLocation = $row['AdopterPetLocation'];
            $adopterOnHoliday = $row['AdopterOnHoliday'];
            $adoptProcessId = $row['AdoptProcessId'];
            $isActive = $row['IsActive'];
            $createDate = $row['CreateDate'];
        }

        $nextProcess = $adoptProcessId + 1;
        if ($nextProcess < 5) {
            $isActive = 1;
        } else {
            $isActive = 0;
        }

        $sqlUpdateOld = "UPDATE adopt_transactions SET IsActive = 0  WHERE AdoptTransactionId  = ?";
        $resultUpdateOld = $conn->execute_query($sqlUpdateOld, [$adoptId]);

        if ($resultUpdateOld === TRUE) {
            $sqlInsert = "INSERT INTO adopt_transactions VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,CAST(? AS UNSIGNED),?)";

            $resultInsert = $conn->execute_query($sqlInsert, [
                $userId,
                $petId,
                $adopterAddress,
                $adopterHasPet,
                $adopterHasChild,
                $adopterChildDetail,
                $adopterTenancyAllow,
                $adopterAttention,
                $adopterVetClinic,
                $adopterPetInsurance,
                $adopterDayTime,
                $adopterRelocate,
                $adopterPetLocation,
                $adopterOnHoliday,
                $nextProcess,
                $isActive,
                $currentTime
            ]);

            if ($resultInsert === TRUE) {
                //Update pet if success receive pet
                if ($nextProcess >= 5) {
                    $sqlUpdatePet = "UPDATE pet SET IsActive = 0  WHERE PetId = ?";
                    $resultUpdatePet = $conn->execute_query($sqlUpdatePet, [$petId]);
                    if ($resultUpdatePet === TRUE) {
                        http_response_code(200);
                        echo json_encode(
                            array(
                                "status" => "OK",
                                "code" => 200,
                                "message" => "Approve request success",
                            )
                        );
                        exit();
                    } else {
                        http_response_code(500);
                        echo json_encode(
                            array(
                                "status" => "INTERNAL ERROR",
                                "code" => 500,
                                "message" => "Error on approve request. (update pet id is error)",
                            )
                        );
                        exit();
                    }
                }

                http_response_code(200);
                echo json_encode(
                    array(
                        "status" => "OK",
                        "code" => 200,
                        "message" => "Approve request success",
                    )
                );
            } else {
                http_response_code(500);
                echo json_encode(
                    array(
                        "status" => "INTERNAL ERROR",
                        "code" => 500,
                        "message" => "Error on approve request.",
                    )
                );
            }

        } else {
            http_response_code(500);
            echo json_encode(
                array(
                    "status" => "INTERNAL ERROR",
                    "code" => 500,
                    "message" => "An error occurred: update old transaction failed",
                )
            );
        }

    } else {
        http_response_code(400);
        echo json_encode(
            array(
                "status" => "BAD REQUEST",
                "code" => 400,
                "message" => "An error occurred: required parameter not found",
            )
        );
    }
} catch (Exception $ex) {
    http_response_code(400);
    echo json_encode(
        array(
            "status" => "BAD REQUEST",
            "code" => 400,
            "message" => "An error occurred: " . $ex->getMessage(),
        )
    );
} finally {
    $conn->close();
}
?>