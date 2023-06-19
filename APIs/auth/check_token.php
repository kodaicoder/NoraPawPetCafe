<?php
session_start();
try {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData);

    if (isset($_SESSION["token"]) && isset($data->token)) {

        if ($_SESSION["token"] === $data->token) {
            http_response_code(200);
            echo json_encode(
                array(
                    "status" => "OK",
                    "code" => 200,
                    "message" => "token match.",
                    "role" => $_SESSION["roleId"],
                    "userId" => $_SESSION["userId"],
                )
            );
        } else {
            http_response_code(401);
            echo json_encode(
                array(
                    "status" => "Unauthorized",
                    "code" => 401,
                    "message" => "You has unauthorized.",
                )
            );
        }
    } else {
        http_response_code(401);
        echo json_encode(
            array(
                "status" => "Unauthorized",
                "code" => 401,
                "message" => "You has unauthorized."
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
}
?>