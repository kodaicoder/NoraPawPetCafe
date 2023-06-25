<?php
include("../dbconn.php");
try {
    // Retrieve the raw POST data
    $jsonData = file_get_contents('php://input');
    // Convert the JSON data to a PHP object or array
    $data = json_decode($jsonData);
    if (isset($data->email) && isset($data->password) && isset($data->fullname) && isset($data->telephone) && isset($data->dob)) {
        $currentTime = date('Y-m-d H:i:s');
        $dateTime = new DateTime($data->dob);
        $formattedDate = $dateTime->format('Y-m-d');

        $sqlFind = "SELECT * FROM users WHERE Email = ?";
        $resultFind = $conn->execute_query($sqlFind, [$data->email]);

        if ($resultFind->num_rows <= 0) {
            $sql = "INSERT INTO users VALUES (NULL, ?, ?, 2, ?, ?, ?, 1, ?)";
            $result = $conn->execute_query($sql, [
                $data->email,
                $data->password,
                $data->fullname,
                $data->telephone,
                $formattedDate,
                $currentTime
            ]);

            if ($result === TRUE) {
                http_response_code(200);
                echo json_encode(
                    array(
                        "status" => "OK",
                        "code" => 200,
                        "message" => "Success registered as " . $data->email,
                        "userData" => array(
                            "email" => $data->email,
                        ),
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
                    "message" => "Email has been used, please change your email.",
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