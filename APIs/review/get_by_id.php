<?php

include("../dbconn.php");
try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "SELECT A.*, B.AdoptTransactionId ,C.PetName, D.Fullname FROM adopter_review AS A
        LEFT JOIN adopt_transactions AS B
        ON A.AdoptTransactionId =B.AdoptTransactionId
        LEFT JOIN pet AS C
        ON B.PetId= C.PetId
        LEFT JOIN users AS D
        ON B.UserId = D.UserId
        WHERE A.IsActive = 1 AND A.AdopterReviewId = ?";

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
                    "message" => "success get review data.",
                    "review" => $data
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