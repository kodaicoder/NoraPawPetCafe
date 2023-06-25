<?php
include("../dbconn.php");

try {
    if (isset($_POST['imageId'])) {
        $currentTime = date('Y-m-d H:i:s');

        // FIND old data
        $sqlFind = "SELECT * FROM gallery WHERE GalleryId = ?";
        $resultFind = $conn->execute_query($sqlFind, [$_POST['imageId']]);
        if ($resultFind) {
            while ($row = $resultFind->fetch_assoc()) {
                $isFeature = $row['IsFeature'];
            }

            //setup old data against new data
            (isset($_POST['isFeatured'])) ? $isFeature = $_POST['isFeatured'] : "";


            if (isset($_FILES['editImage'])) {
                $uploadedFile = $_FILES['editImage'];
                $currentTime = date('Y-m-d H:i:s');

                // Access the file details like name, type, size, etc.
                $fileName = $uploadedFile['name'];
                $tempFilePath = $uploadedFile['tmp_name'];

                // Set the directory where you want to save the file
                $rootPath = $_SERVER['DOCUMENT_ROOT'];
                ;

                $uploadDirectory = '/images/gallery/';

                // Generate a unique filename for the uploaded file
                $newFileName = md5(uniqid()) . '_' . $fileName;

                // Create the destination path by concatenating the upload directory and the new filename
                $destinationPath = $rootPath . $uploadDirectory . $newFileName;

                if (move_uploaded_file($tempFilePath, $destinationPath)) {
                    $sql = "UPDATE gallery SET
                            GalleryImageSrc = ?,
                            IsFeature = CAST(? AS UNSIGNED),
                            UpdateBy = ?,
                            UpdateDate = ?
                            WHERE GalleryId   = ?";

                    $result = $conn->execute_query($sql, [
                        "." . $uploadDirectory . $newFileName,
                        $isFeature,
                        $_POST['userId'],
                        $currentTime,
                        $_POST['imageId']
                    ]);

                    if ($result === TRUE) {
                        http_response_code(200);
                        echo json_encode(
                            array(
                                "status" => "OK",
                                "code" => 200,
                                "message" => "Success update a gallery image.",
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
                $sql = "UPDATE gallery SET
                            IsFeature = CAST(? AS UNSIGNED),
                            UpdateBy = ?,
                            UpdateDate = ?
                            WHERE GalleryId   = ?";

                $result = $conn->execute_query($sql, [
                    $isFeature,
                    $_POST['userId'],
                    $currentTime,
                    $_POST['imageId']
                ]);

                if ($result === TRUE) {
                    http_response_code(200);
                    echo json_encode(
                        array(
                            "status" => "OK",
                            "code" => 200,
                            "message" => "Success update a gallery image.",
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