<?php
require_once '_db.php';

class Result {}

$stmt = $db->prepare("SELECT * FROM reservations 
    WHERE NOT ((end <= :start) OR (start >= :end)) 
    AND id <> :id 
    AND room_id = :resource");
    
$stmt->bindParam(':start', $_POST['newStart']);
$stmt->bindParam(':end', $_POST['newEnd']);
$stmt->bindParam(':id', $_POST['id']);
$stmt->bindParam(':resource', $_POST['newResource']);
$stmt->execute();

$overlaps = $stmt->rowCount() > 0;

if ($overlaps) {
    $response = new Result();
    $response->result = 'Error';
    $response->message = 'Це бронювання перетинається з наявним.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// якщо немає конфлікту, оновлюємо дані
$stmt = $db->prepare("UPDATE reservations 
    SET start = :start, end = :end, room_id = :resource 
    WHERE id = :id");

$stmt->bindParam(':id', $_POST['id']);
$stmt->bindParam(':start', $_POST['newStart']);
$stmt->bindParam(':end', $_POST['newEnd']);
$stmt->bindParam(':resource', $_POST['newResource']);
$stmt->execute();

$response = new Result();
$response->result = 'OK';
$response->message = 'Бронювання оновлено успішно.';

header('Content-Type: application/json');
echo json_encode($response);
?>
