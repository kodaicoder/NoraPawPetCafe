<?php
include("../dbconn.php");
try {
    if (
        isset($_POST['petName']) &&
        isset($_POST['petDescription']) &&
        isset($_POST['petSex']) &&
        isset($_POST['petTypes']) &&
        isset($_POST['petBreed']) &&
        isset($_POST['petAge']) &&
        isset($_POST['userId']) &&
        isset($_FILES['petImage'])
    ) {
        $uploadedFile = $_FILES['petImage'];
        $currentTime = date('Y-m-d H:i:s');

        // Access the file details like name, type, size, etc.
        $fileName = $uploadedFile['name'];
        $tempFilePath = $uploadedFile['tmp_name'];

        // Set the directory where you want to save the file
        $rootPath = $_SERVER['DOCUMENT_ROOT'];

        $uploadDirectory = '/norapetcafe/images/adopt/';

        // Generate a unique filename for the uploaded file
        $newFileName = md5(uniqid()) . '_' . $fileName;

        // Create the destination path by concatenating the upload directory and the new filename
        $destinationPath = $rootPath . $uploadDirectory . $newFileName;

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($tempFilePath, $destinationPath)) {
            //(NULL,PetName,PetDescription,PetBreed,PetAge,PetImageSrc,PetSex,PetTypeId ,IsActive,CreateDate,CreateBy ,UpdateDate, UpdateBy)
            $sql = "INSERT INTO pet VALUES (NULL,?,?,?,?,?,?,?,1,?,?,?,?)";

            $result = $conn->execute_query($sql, [
                $_POST['petName'],
                $_POST['petDescription'],
                $_POST['petBreed'],
                $_POST['petAge'],
                $uploadDirectory . $newFileName,
                $_POST['petSex'],
                $_POST['petTypes'],
                $currentTime,
                $_POST['userId'],
                $currentTime,
                $_POST['userId'],
            ]);

            if ($result === TRUE) {
                http_response_code(200);
                echo json_encode(
                    array(
                        "status" => "OK",
                        "code" => 200,
                        "message" => "Success adding new pet.",
                    )
                );
            } else {
                http_response_code(400);
                echo json_encode(
                    array(
                        "status" => "BAD REQUEST",
                        "code" => 400,
                        "message" => "Error on insert into database.",
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