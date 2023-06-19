<?php

include("../dbconn.php");
try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM menu WHERE MenuId = ? AND IsActive = 1";
        $result = $conn->execute_query($sql, [$id]);
        if ($result->num_rows <= 0) {
            http_response_code(200);
            echo json_encode(
                array(
                    "status" => "NOT FOUND",
                    "code" => 404,
                    "message" => "ไม่พบข้อมูล"
                )
            );
        } else {

            while ($row = $result->fetch_assoc()) {
                $data = $row;
            }
            http_response_code(200);
            echo json_encode(
                array(
                    "status" => "OK",
                    "code" => 200,
                    "message" => "success get menu data.",
                    "menu" => $data
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