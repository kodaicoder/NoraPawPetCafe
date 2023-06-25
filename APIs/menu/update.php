<?php
include("../dbconn.php");

try {
    if (isset($_POST['menuId'])) {
        $currentTime = date('Y-m-d H:i:s');

        // FIND old data
        $sqlFind = "SELECT * FROM `menu` WHERE `MenuId` = ?";
        $resultFind = $conn->execute_query($sqlFind, [$_POST['menuId']]);
        if ($resultFind) {
            while ($row = $resultFind->fetch_assoc()) {
                $menuTitle = $row['MenuTitle'];
                $menuDescription = $row['MenuDescription'];
                $price = $row['Price'];
                $menuCategoryId = $row['MenuCategoryId'];
                $menuImageSrc = $row['MenuImageSrc'];
                $menuFeatured = $row['MenuFeatured'];
            }

            //setup old data against new data
            (isset($_POST['menuTitle'])) ? $menuTitle = $_POST['menuTitle'] : "";
            (isset($_POST['menuIsFeatured'])) ? $menuFeatured = $_POST['menuIsFeatured'] : "";
            (isset($_POST['menuDescription'])) ? $menuDescription = $_POST['menuDescription'] : "";
            (isset($_POST['menuPrice'])) ? $price = $_POST['menuPrice'] : "";
            (isset($_POST['menuCategory'])) ? $menuCategoryId = $_POST['menuCategory'] : "";

            if (isset($_FILES['menuImage'])) {
                $uploadedFile = $_FILES['menuImage'];
                $currentTime = date('Y-m-d H:i:s');

                //Check Menu type with Category
                $sqlFind = "SELECT MenuTypeId FROM menu_categories WHERE MenuCategoryId = ?";
                $resultFind = $conn->execute_query($sqlFind, [$_POST['menuCategory']]);
                if ($resultFind->num_rows <= 0) {
                    http_response_code(404);
                    echo json_encode(
                        array(
                            "status" => "NOT FOUND",
                            "code" => 404,
                            "message" => "Menu type is not found.",
                        )
                    );
                    exit();
                } else {
                    while ($row = $resultFind->fetch_assoc()) {
                        $foundData = $row;
                    }
                }
                $menuTypeId = $foundData["MenuTypeId"];

                // Access the file details like name, type, size, etc.
                $fileName = $uploadedFile['name'];
                // $fileType = $uploadedFile['type'];
                // $fileSize = $uploadedFile['size'];
                $tempFilePath = $uploadedFile['tmp_name'];

                // Set the directory where you want to save the file
                $rootPath = $_SERVER['DOCUMENT_ROOT'];

                if ($menuTypeId == 1) {
                    $menuTypeSubPath = '/edible';
                } else {
                    $menuTypeSubPath = '/unedible';
                }

                switch ($_POST['menuCategory']) {
                    case 1:
                        $menuCategorySubPath = '/coffee';
                        break;
                    case 2:
                        $menuCategorySubPath = '/tea';
                        break;
                    case 3:
                        $menuCategorySubPath = '/pastry';
                        break;
                    case 4:
                        $menuCategorySubPath = '/sweets';
                        break;
                    default:
                        $menuCategorySubPath = '';
                        break;
                }
                $uploadDirectory = '/images/menu' . $menuTypeSubPath . $menuCategorySubPath . '/';

                // Generate a unique filename for the uploaded file
                $newFileName = md5(uniqid()) . '_' . $fileName;

                // Create the destination path by concatenating the upload directory and the new filename
                $destinationPath = $rootPath . $uploadDirectory . $newFileName;

                if (move_uploaded_file($tempFilePath, $destinationPath)) {
                    $sql = "UPDATE menu SET
                            MenuTitle = ?,
                            MenuDescription = ?,
                            Price = ?,
                            MenuCategoryId  = ?,
                            MenuImageSrc = ?,
                            MenuFeatured = ?,
                            UpdateBy = ?,
                            UpdateDate = ?
                            WHERE MenuId  = ?";

                    $result = $conn->execute_query($sql, [
                        $menuTitle,
                        $menuDescription,
                        $price,
                        $menuCategoryId,
                        "." . $uploadDirectory . $newFileName,
                        $menuFeatured,
                        $_POST['userId'],
                        $currentTime,
                        $_POST['menuId']
                    ]);

                    if ($result === TRUE) {
                        http_response_code(200);
                        echo json_encode(
                            array(
                                "status" => "OK",
                                "code" => 200,
                                "message" => "Success update a event.",
                            )
                        );
                    } else {
                        http_response_code(400);
                        echo json_encode(
                            array(
                                "status" => "BAD REQUEST",
                                "code" => 400,
                                "message" => "Error on update into database.",
                            )
                        );
                    }

                } else {
                    http_response_code(500);
                    echo json_encode(
                        array(
                            "status" => "BAD REQUEST",
                            "code" => 500,
                            "message" => "Error on upload image to storage.",
                        )
                    );
                }
            } else {
                $sql = "UPDATE menu SET
                            MenuTitle = ?,
                            MenuDescription = ?,
                            Price = ?,
                            MenuCategoryId  = ?,
                            MenuFeatured = CAST(? AS UNSIGNED),
                            UpdateBy = ?,
                            UpdateDate = ?
                            WHERE MenuId  = ?";

                $result = $conn->execute_query($sql, [
                    $menuTitle,
                    $menuDescription,
                    $price,
                    $menuCategoryId,
                    $menuFeatured,
                    $_POST['userId'],
                    $currentTime,
                    $_POST['menuId']
                ]);

                if ($result === TRUE) {
                    http_response_code(200);
                    echo json_encode(
                        array(
                            "status" => "OK",
                            "code" => 200,
                            "message" => "Success update a event.",
                        )
                    );
                } else {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            "status" => "BAD REQUEST",
                            "code" => 400,
                            "message" => "Error on update into database.",
                        )
                    );
                }
            }
        }

    } else {
        http_response_code(400);
        echo json_encode(
            array(
                "status" => "BAD REQUEST",
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