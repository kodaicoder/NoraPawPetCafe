<?php
include("../dbconn.php");
try {
    if (
        isset($_POST['eventStartDate']) &&
        isset($_POST['eventEndDate']) &&
        isset($_POST['eventEndTime']) &&
        isset($_POST['eventStartTime']) &&
        isset($_POST['eventTitle']) &&
        isset($_POST['eventDescription']) &&
        isset($_POST['userId']) &&
        isset($_FILES['eventImage'])
    ) {
        $uploadedFile = $_FILES['eventImage'];
        $currentTime = date('Y-m-d H:i:s');

        // Access the file details like name, type, size, etc.
        $fileName = $uploadedFile['name'];
        // $fileType = $uploadedFile['type'];
        // $fileSize = $uploadedFile['size'];
        $tempFilePath = $uploadedFile['tmp_name'];

        // Set the directory where you want to save the file
        $rootPath = $_SERVER['DOCUMENT_ROOT'];
        ;
        $uploadDirectory = '/images/event/';

        // Generate a unique filename for the uploaded file
        $newFileName = md5(uniqid()) . '_' . $fileName;

        // Create the destination path by concatenating the upload directory and the new filename
        $destinationPath = $rootPath . $uploadDirectory . $newFileName;

        // Move the uploaded file to the destination directory
        if (move_uploaded_file($tempFilePath, $destinationPath)) {

            $sql = "INSERT INTO events VALUES (NULL,?,?,?,?,?,?,?,1,?,?,?,?)";

            $result = $conn->execute_query($sql, [
                $_POST['eventTitle'],
                $_POST['eventDescription'],
                "." . $uploadDirectory . $newFileName,
                $_POST['eventStartDate'],
                $_POST['eventEndDate'],
                $_POST['eventStartTime'],
                $_POST['eventEndTime'],
                $_POST['userId'],
                $currentTime,
                $_POST['userId'],
                $currentTime
            ]);

            if ($result === TRUE) {
                http_response_code(200);
                echo json_encode(
                    array(
                        "status" => "OK",
                        "code" => 200,
                        "message" => "Success adding new event.",
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