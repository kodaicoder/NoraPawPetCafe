<?php
include("../dbconn.php");
try {
    $sql = "SELECT A.*, B.MenuCategoryDescription, C.MenuTypeDescription
    FROM menu as A
    LEFT JOIN menu_categories as B
    ON A.MenuCategoryId= B.MenuCategoryId
    LEFT JOIN menu_types as C
    ON B.MenuTypeId = C.MenuTypeId
    WHERE A.IsActive = 1";

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
                "message" => "success get all menus.",
                "menus" => $data
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