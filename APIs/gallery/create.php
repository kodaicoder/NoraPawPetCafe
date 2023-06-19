<?php
include("../dbconn.php");

try {
    if (isset($_FILES['galleryImages']) && isset($_POST['userId'])) {
        $currentTime = date('Y-m-d H:i:s');
        $imageFiles = $_FILES['galleryImages'];

        //print_r($imageFiles);
        // Iterate through uploaded files
        for ($i = 0; $i < count($imageFiles['name']); $i++) {
            $tmpFilePath = $imageFiles['tmp_name'][$i];
            $imageName = $imageFiles['name'][$i];

            // Check if file size is within limits (e.g., 5MB)
            $maxSizeInBytes = 5 * 1024 * 1024;
            if ($imageFiles['size'][$i] > $maxSizeInBytes) {
                // Handle file size limit exceeded
                http_response_code(400);
                echo json_encode(
                    array(
                        "status" => "BAD REQUEST",
                        "code" => 400,
                        "message" => "Image has exceeded size limit (5mb)",
                    )
                );
                exit();
            }
        }

        for ($i = 0; $i < count($imageFiles['name']); $i++) {
            //Check is image is featured or not
            $isFeaturedKey = 'isFeatured' . $i;
            if (isset($_POST[$isFeaturedKey])) {
                $isFeaturedValue = $_POST[$isFeaturedKey];
            } else {
                $isFeaturedValue = 0;
            }

            // Access the file details like name, type, size, etc.
            $fileName = $imageFiles['name'][$i];
            $tempFilePath = $imageFiles['tmp_name'][$i];

            // Set the directory where you want to save the file
            $rootPath = $_SERVER['DOCUMENT_ROOT'];
            $uploadDirectory = '/norapetcafe/images/gallery/';

            // Generate a unique filename for the uploaded file
            $newFileName = md5(uniqid()) . '_' . $fileName;

            // Create the destination path by concatenating the upload directory and the new filename
            $destinationPath = $rootPath . $uploadDirectory . $newFileName;

            if (move_uploaded_file($tempFilePath, $destinationPath)) {

                $sql = "INSERT INTO gallery VALUES (NULL,?,CAST(? AS UNSIGNED),1,?,?,?,?)";

                $result = $conn->execute_query($sql, [
                    $uploadDirectory . $newFileName,
                    $isFeaturedValue,
                    $_POST['userId'],
                    $currentTime,
                    $_POST['userId'],
                    $currentTime
                ]);

                if ($result === TRUE) {
                    http_response_code(200);
                    $responses = array(
                        "status" => "OK",
                        "code" => 200,
                        "message" => "Success adding new event.",
                    );

                } else {
                    http_response_code(400);
                    $responses = array(
                        "status" => "BAD REQUEST",
                        "code" => 400,
                        "message" => "Error on insert into database.",
                    );
                    break;
                }

                // http_response_code(400);
                // $responses[] = array(
                //     "status" => "BAD REQUEST",
                //     "code" => 400,
                //     "message" => "Error on insert into database.",
                // );
                // break;

            } else {
                http_response_code(500);
                $responses = array(
                    "status" => "BAD REQUEST",
                    "code" => 500,
                    "message" => "Error on upload image to storage.",
                );
                break;
            }
        }
        echo json_encode($responses);
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