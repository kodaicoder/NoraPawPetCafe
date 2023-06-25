<?php
ini_set('session.gc_maxlifetime', 2592000);
session_start();
include("../dbconn.php");
try {
    // Retrieve the raw POST data
    $jsonData = file_get_contents('php://input');
    // Convert the JSON data to a PHP object or array
    $data = json_decode($jsonData);
    if (isset($data->email) && isset($data->password)) {
        $sql = "SELECT * FROM users WHERE Email = ? and Password = ?";
        $result = $conn->execute_query($sql, [$data->email, $data->password]);
        if ($result->num_rows <= 0) {
            http_response_code(200);
            echo json_encode(
                array(
                    "status" => "Unauthorized",
                    "code" => 200,
                    "message" => "Email or password is invalid.",
                )
            );
        } else {
            while ($row = $result->fetch_assoc()) {
                $userData = $row;
            }

            $_SESSION["userId"] = $userData["UserId"];
            $_SESSION["email"] = $userData["Email"];
            $_SESSION["roleId"] = $userData["RoleId"];
            $_SESSION["token"] = bin2hex(random_bytes(64));

            http_response_code(200);
            echo json_encode(
                array(
                    "status" => "OK",
                    "code" => 200,
                    "message" => "Success login as " . $_SESSION["email"],
                    "userData" => array(
                        "token" => $_SESSION["token"],
                        // "role" => $_SESSION["roleId"],
                    ),
                )
            );
        }

    } else {
        http_response_code(400);
        echo json_encode(
            array(
                "status" => "Bad Request",
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