<?php
session_start();
session_destroy();
http_response_code(200);
echo json_encode(
    array(
        "status" => "OK",
        "code" => 200,
        "message" => "You has logout.",
    )
);
?>