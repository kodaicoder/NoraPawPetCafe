<?php
include("../dbconn.php");
try {
    // Retrieve the raw POST data
    $jsonData = file_get_contents('php://input');
    // Convert the JSON data to a PHP object or array
    $data = json_decode($jsonData);
    if (isset($data->petId) && isset($data->userId)) {
        $currentTime = date('Y-m-d H:i:s');

        $sqlUpdate = "Update `pet`
            SET
            IsActive = 0,
            UpdateBy = ?,
            UpdateDate = ?
            WHERE
            PetId  = ? AND
            IsActive = 1";

        $resultUpdate = $conn->execute_query($sqlUpdate, [
            $data->userId,
            $currentTime,
            $data->petId,
        ]);

        if ($resultUpdate === TRUE) {
            http_response_code(200);
            echo json_encode(
                array(
                    "status" => "OK",
                    "code" => 200,
                    "message" => "Success flag delete transactions",
                )
            );
        } else {
            http_response_code(500);
            echo json_encode(
                array(
                    "status" => "INTERNAL ERROR",
                    "code" => 500,
                    "message" => "Something went wrong on UPDATE menu.",
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