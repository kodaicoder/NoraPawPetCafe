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
        $sqlFind = "SELECT * FROM adopt_transactions WHERE AdoptTransactionId = ?";
        $resultFind = $conn->execute_query($sqlFind, [$adoptId]);

        while ($row = $resultFind->fetch_assoc()) {
            $adoptTransactionld = $row['AdoptTransactionId'];
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

        $nextProcess = 7;

        $sqlUpdateOld = "UPDATE adopt_transactions SET IsActive = 0  WHERE AdoptTransactionId  = ?";
        $resultUpdateOld = $conn->execute_query($sqlUpdateOld, [$adoptId]);

        if ($resultUpdateOld === TRUE) {
            $sqlInsert = "INSERT INTO adopt_transactions VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,?)";

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
                $currentTime
            ]);

            if ($resultInsert === TRUE) {
                http_response_code(200);
                echo json_encode(
                    array(
                        "status" => "OK",
                        "code" => 200,
                        "message" => "Rejected request success",
                    )
                );
            } else {
                http_response_code(500);
                echo json_encode(
                    array(
                        "status" => "INTERNAL ERROR",
                        "code" => 500,
                        "message" => "Error on rejected request.",
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