<?php
include("../dbconn.php");
try {
    // Retrieve the raw POST data
    $jsonData = file_get_contents('php://input');
    // Convert the JSON data to a PHP object or array
    $data = json_decode($jsonData);
    if (isset($data->id) && isset($data->userId)) {
        $currentTime = date('Y-m-d H:i:s');
        // GET OLD DATA
        $sqlFind = "SELECT * FROM adopt_transactions WHERE AdoptTransactionId = ?";
        $resultFind = $conn->execute_query($sqlFind, [$data->id]);
        if ($resultFind) {
            while ($row = $resultFind->fetch_assoc()) {
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
            }

            // SET ALL TRANSACTION ISACTIVE = 0
            $sqlUpdate = "UPDATE adopt_transactions
                          SET IsActive = 0
                          WHERE
                          UserId = ? AND
                          PetId = ? AND
                          AdoptProcessId < 5 AND
                          IsActive = 1";
            $resultUpdate = $conn->execute_query($sqlUpdate, [$userId, $petId]);

            if ($resultUpdate === FALSE) {
                http_response_code(500);
                echo json_encode(
                    array(
                        "status" => "INTERNAL ERROR",
                        "code" => 500,
                        "message" => "Something went wrong on UPDATE old transactions.",
                    )
                );
                exit();
            }

            // INSERT NEW CANCELATION TRANSACTION
            $sqlInsert = "INSERT INTO adopt_transactions VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 6, 0, ?)";

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
                $currentTime
            ]);

            if ($resultInsert === TRUE) {
                http_response_code(200);
                echo json_encode(
                    array(
                        "status" => "OK",
                        "code" => 200,
                        "message" => "Success cancel transactions",
                    )
                );
            } else {
                http_response_code(400);
                echo json_encode(
                    array(
                        "status" => "BAD REQUEST",
                        "code" => 400,
                        "message" => "Error on insert into database.",
                    )
                );
            }

        } else {
            http_response_code(404);
            echo json_encode(
                array(
                    "status" => "NOT FOUND",
                    "code" => 404,
                    "message" => "Not found related request form",
                )
            );
        }

    } else {
        http_response_code(400);
        echo json_encode(
            array(
                "status" => "BAD REQUEST",
                "code" => 400,
                "message" => "Invalid input for parameter.",
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