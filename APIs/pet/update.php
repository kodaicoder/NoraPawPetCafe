<?php
include("../dbconn.php");

try {
    if (isset($_POST['petId'])) {
        $currentTime = date('Y-m-d H:i:s');

        // FIND old data
        $sqlFind = "SELECT * FROM `pet` WHERE `PetId` = ?";
        $resultFind = $conn->execute_query($sqlFind, [$_POST['petId']]);
        if ($resultFind) {
            while ($row = $resultFind->fetch_assoc()) {
                $petName = $row['PetName'];
                $petDescription = $row['PetDescription'];
                $petBreed = $row['PetBreed'];
                $petAge = $row['PetAge'];
                $petImageSrc = $row['PetImageSrc'];
                $petSex = $row['PetSex'];
                $petTypeId = $row['PetTypeId'];
            }
            //setup old data against new data
            (isset($_POST['petName'])) ? $petName = $_POST['petName'] : "";
            (isset($_POST['petDescription'])) ? $petDescription = $_POST['petDescription'] : "";
            (isset($_POST['petSex'])) ? $petSex = $_POST['petSex'] : "";
            (isset($_POST['petTypes'])) ? $petTypeId = $_POST['petTypes'] : "";
            (isset($_POST['petBreed'])) ? $petBreed = $_POST['petBreed'] : "";
            (isset($_POST['petAge'])) ? $petAge = $_POST['petAge'] : "";

            if (isset($_FILES['petImage'])) {
                $uploadedFile = $_FILES['petImage'];
                $currentTime = date('Y-m-d H:i:s');

                // Access the file details like name, type, size, etc.
                $fileName = $uploadedFile['name'];
                $tempFilePath = $uploadedFile['tmp_name'];

                // Set the directory where you want to save the file
                $rootPath = $_SERVER['DOCUMENT_ROOT'];

                $uploadDirectory = '/images/adopt/';

                // Generate a unique filename for the uploaded file
                $newFileName = md5(uniqid()) . '_' . $fileName;

                // Create the destination path by concatenating the upload directory and the new filename
                $destinationPath = $rootPath . $uploadDirectory . $newFileName;

                if (move_uploaded_file($tempFilePath, $destinationPath)) {
                    $sql = "UPDATE pet SET
                            PetName = ?,
                            PetDescription = ?,
                            PetBreed = ?,
                            PetAge  = ?,
                            PetImageSrc = ?,
                            PetSex = ?,
                            PetTypeId  = ?,
                            UpdateBy = ?,
                            UpdateDate = ?
                            WHERE PetId   = ?";

                    $result = $conn->execute_query($sql, [
                        $petName,
                        $petDescription,
                        $petBreed,
                        $petAge,
                        "." . $uploadDirectory . $newFileName,
                        $petSex,
                        $petTypeId,
                        $_POST['userId'],
                        $currentTime,
                        $_POST['petId']
                    ]);

                    if ($result === TRUE) {
                        http_response_code(200);
                        echo json_encode(
                            array(
                                "status" => "OK",
                                "code" => 200,
                                "message" => "Success update a pet.",
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
                $sql = "UPDATE pet SET
                            PetName = ?,
                            PetDescription = ?,
                            PetBreed = ?,
                            PetAge  = ?,
                            PetSex = ?,
                            PetTypeId  = ?,
                            UpdateBy = ?,
                            UpdateDate = ?
                            WHERE PetId   = ?";

                $result = $conn->execute_query($sql, [
                    $petName,
                    $petDescription,
                    $petBreed,
                    $petAge,
                    $petSex,
                    $petTypeId,
                    $_POST['userId'],
                    $currentTime,
                    $_POST['petId']
                ]);

                if ($result === TRUE) {
                    http_response_code(200);
                    echo json_encode(
                        array(
                            "status" => "OK",
                            "code" => 200,
                            "message" => "Success update a pet.",
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