<?php

include("../dbconn.php");
try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "SELECT A.*, B.* ,C.* ,D.* FROM `adopt_transactions` AS A
        LEFT JOIN adopt_process AS B
        ON A.AdoptProcessId = B.AdoptProcessId
        LEFT JOIN users AS C
        ON A.UserId = C.UserId
        LEFT JOIN pet AS D
        ON A.PetId = D.PetId
        WHERE
        A.IsActive = 1 AND
        A.PetId = ?
        ";

        $result = $conn->execute_query($sql, [$id]);

        if ($result->num_rows <= 0) {
            http_response_code(200);
            echo json_encode(
                array(
                    "status" => "NOT FOUND",
                    "code" => 200,
                    "message" => "ไม่พบข้อมูล"
                )
            );
        } else {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            http_response_code(200);
            echo json_encode(
                array(
                    "status" => "OK",
                    "code" => 200,
                    "message" => "success get adopt transactions.",
                    "transactions" => $data
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