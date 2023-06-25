<?php
include("../dbconn.php");
try {
    // Retrieve the raw POST data
    $jsonData = file_get_contents('php://input');
    // Convert the JSON data to a PHP object or array
    $data = json_decode($jsonData);
    if (
        isset($data->customerId)
        && isset($data->petId)
        && isset($data->address)
        && isset($data->hasChildren)
        && isset($data->isTenancyAllow)
        && isset($data->isAttentionAgree)
        && isset($data->vetClinic)
        && isset($data->isInsurance)
        && isset($data->isHasSomeOne)
        && isset($data->isRelocate)
        && isset($data->onHolidayPlace)
    ) {
        $currentTime = date('Y-m-d H:i:s');

        $sqlFind = "SELECT * FROM adopt_transactions WHERE
        UserId = ? AND
        PetId = ? AND
        AdoptProcessId <5 AND
        IsActive = 1";

        $resultFind = $conn->execute_query($sqlFind, [$data->customerId, $data->petId]);

        if ($resultFind->num_rows <= 0) {
            $sql = "INSERT INTO adopt_transactions VALUES (NULL,?,?,?,?,CAST(? AS UNSIGNED),?,
            CAST(? AS UNSIGNED),CAST(? AS UNSIGNED),?,CAST(? AS UNSIGNED),CAST(? AS UNSIGNED),CAST(? AS UNSIGNED),?,?,1,1,?)";

            $result = $conn->execute_query($sql, [
                $data->customerId,
                $data->petId,
                $data->address,
                $data->hasPet,
                $data->hasChildren,
                $data->childBrief,
                $data->isTenancyAllow,
                $data->isAttentionAgree,
                $data->vetClinic,
                $data->isInsurance,
                $data->isHasSomeOne,
                $data->isRelocate,
                $data->placeForPet,
                $data->onHolidayPlace,
                $currentTime
            ]);

            if ($result === TRUE) {
                http_response_code(200);
                echo json_encode(
                    array(
                        "status" => "OK",
                        "code" => 200,
                        "message" => "Success adding data form",
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
            http_response_code(400);
            echo json_encode(
                array(
                    "status" => "BAD REQUEST",
                    "code" => 400,
                    "message" => "We found you has request this pet.",
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