<?php

include("../dbconn.php");
try {
    $sql = "SELECT A.*, B.Fullname, C.PetName FROM `adopt_transactions` AS A
        LEFT JOIN users AS B
        ON A.UserId = B.UserId
        LEFT JOIN pet AS C
        ON A.PetId = C.PetId
        WHERE A.IsActive = 0 AND A.AdoptProcessId = 5";

    $result = $conn->execute_query($sql);

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
                "message" => "success get done adopt transactions.",
                "transactions" => $data
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