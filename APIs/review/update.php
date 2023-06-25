<?php
include("../dbconn.php");

try {
    if (isset($_POST['reviewId'])) {
        $currentTime = date('Y-m-d H:i:s');
        $sqlFind = "SELECT * FROM adopter_review WHERE AdopterReviewId = ?";
        $resultFind = $conn->execute_query($sqlFind, [$_POST['reviewId']]);

        if ($resultFind) {
            while ($row = $resultFind->fetch_assoc()) {
                $adoptTransactionId = $row["AdoptTransactionId"];
                $adopterShortPhrase = $row["AdopterShortPhrase"];
                $adopterStory = $row["AdopterStory"];
                $adopterImageSrc = $row["AdopterImageSrc"];
                $isFeatured = $row["isFeatured"];
            }
            //setup old data against new data
            (isset($_POST['shortPhase'])) ? $adopterShortPhrase = $_POST['shortPhase'] : "";
            (isset($_POST['reviewIsFeatured'])) ? $isFeatured = $_POST['reviewIsFeatured'] : "";
            (isset($_POST['story'])) ? $adopterStory = $_POST['story'] : "";

            if (isset($_FILES['reviewImage'])) {
                $uploadedFile = $_FILES['reviewImage'];
                $fileName = $uploadedFile['name'];
                $tempFilePath = $uploadedFile['tmp_name'];

                $rootPath = $_SERVER['DOCUMENT_ROOT'];
                ;
                $uploadDirectory = '/images/adopter/';

                $newFileName = md5(uniqid()) . '_' . $fileName;

                $destinationPath = $rootPath . $uploadDirectory . $newFileName;

                if (move_uploaded_file($tempFilePath, $destinationPath)) {
                    $sql = "UPDATE adopter_review SET
                            AdopterShortPhrase = ?,
                            AdopterStory = ?,
                            AdopterImageSrc = ?,
                            isFeatured = CAST(? AS UNSIGNED),
                            UpdateBy  = ?,
                            UpdateDate = ?
                            WHERE AdopterReviewId = ?";

                    $result = $conn->execute_query($sql, [
                        $adopterShortPhrase,
                        $adopterStory,
                        "." . $uploadDirectory . $newFileName,
                        $isFeatured,
                        $_POST['userId'],
                        $currentTime,
                        $_POST['reviewId']
                    ]);

                    if ($result === TRUE) {
                        http_response_code(200);
                        echo json_encode(
                            array(
                                "status" => "OK",
                                "code" => 200,
                                "message" => "Success update a review.",
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
                $sql = "UPDATE adopter_review SET
                            AdopterShortPhrase = ?,
                            AdopterStory = ?,
                            isFeatured = CAST(? AS UNSIGNED),
                            UpdateBy  = ?,
                            UpdateDate = ?
                            WHERE AdopterReviewId  = ?";

                $result = $conn->execute_query($sql, [
                    $adopterShortPhrase,
                    $adopterStory,
                    $isFeatured,
                    $_POST['userId'],
                    $currentTime,
                    $_POST['reviewId']
                ]);

                if ($result === TRUE) {
                    http_response_code(200);
                    echo json_encode(
                        array(
                            "status" => "OK",
                            "code" => 200,
                            "message" => "Success update a review.",
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