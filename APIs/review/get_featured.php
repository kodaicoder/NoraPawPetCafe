<?php
include("../dbconn.php");
try {
    $sql = "SELECT A.* ,C.Fullname,D.PetName FROM adopter_review as A 
    LEFT JOIN adopt_transactions as B
     ON A.AdoptTransactionId = B.AdoptTransactionId 
     LEFT JOIN users as C 
     ON B.UserId = C.UserId LEFT JOIN pet as D ON B.PetId = D.PetId 
     WHERE A.isFeatured = 1 AND A.IsActive = 1";
    $result = $conn->query($sql);
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
                "message" => "success get featured reviews.",
                "featuredReviews" => $data
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