<?php

include("../dbconn.php");
try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "SELECT A.*, B.PetName FROM adopt_transactions as A
        LEFT JOIN pet as B ON A.PetId = B.PetId
        where A.UserId = ?
        AND A.AdoptProcessId != 6 AND A.IsActive = 1 ORDER BY A.CreateDate ASC";

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