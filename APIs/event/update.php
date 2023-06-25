<?php
include("../dbconn.php");

try {
    if (isset($_POST['eventId'])) {
        $currentTime = date('Y-m-d H:i:s');

        //FIND old data
        $sqlFind = "SELECT * FROM events WHERE EventId = ? ";
        $resultFind = $conn->execute_query($sqlFind, [$_POST['eventId']]);
        if ($resultFind) {
            while ($row = $resultFind->fetch_assoc()) {
                $eventTitle = $row['EventTitle'];
                $eventDescription = $row['EventDescription'];
                $eventImageSrc = $row['EventImageSrc'];
                $eventDateStart = $row['EventDateStart'];
                $eventDateEnd = $row['EventDateEnd'];
                $eventTimeStart = $row['EventTimeStart'];
                $eventTimeEnd = $row['EventTimeEnd'];
            }
            //setup old data against new data
            (isset($_POST['eventTitle'])) ? $eventTitle = $_POST['eventTitle'] : "";
            (isset($_POST['eventDescription'])) ? $eventDescription = $_POST['eventDescription'] : "";
            (isset($_POST['eventStartDate'])) ? $eventDateStart = $_POST['eventStartDate'] : "";
            (isset($_POST['eventEndDate'])) ? $eventDateEnd = $_POST['eventEndDate'] : "";
            (isset($_POST['eventStartTime'])) ? $eventTimeStart = $_POST['eventStartTime'] : "";
            (isset($_POST['eventEndTime'])) ? $eventTimeEnd = $_POST['eventEndTime'] : "";

            if (isset($_FILES['eventImage'])) {
                $uploadedFile = $_FILES['eventImage'];
                $fileName = $uploadedFile['name'];
                $tempFilePath = $uploadedFile['tmp_name'];

                $rootPath = $_SERVER['DOCUMENT_ROOT'];
                ;
                $uploadDirectory = '/images/event/';

                $newFileName = md5(uniqid()) . '_' . $fileName;

                $destinationPath = $rootPath . $uploadDirectory . $newFileName;

                if (move_uploaded_file($tempFilePath, $destinationPath)) {
                    $sql = "UPDATE events SET
                            EventTitle = ?,
                            EventDescription = ?,
                            EventImageSrc = ?,
                            EventDateStart = ?,
                            EventDateEnd = ?,
                            EventTimeStart = ?,
                            EventTimeEnd = ?,
                            UpdateBy = ?,
                            UpdateDate = ?
                            WHERE EventId = ?";

                    $result = $conn->execute_query($sql, [
                        $eventTitle,
                        $eventDescription,
                        "." . $uploadDirectory . $newFileName,
                        $eventDateStart,
                        $eventDateEnd,
                        $eventTimeStart,
                        $eventTimeEnd,
                        $_POST['userId'],
                        $currentTime,
                        $_POST['eventId']
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
                $sql = "UPDATE events SET
                            EventTitle = ?,
                            EventDescription = ?,
                            EventDateStart = ?,
                            EventDateEnd = ?,
                            EventTimeStart = ?,
                            EventTimeEnd = ?,
                            UpdateBy = ?,
                            UpdateDate = ?
                            WHERE EventId = ?";
                $result = $conn->execute_query($sql, [
                    $eventTitle,
                    $eventDescription,
                    $eventDateStart,
                    $eventDateEnd,
                    $eventTimeStart,
                    $eventTimeEnd,
                    $_POST['userId'],
                    $currentTime,
                    $_POST['eventId']
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