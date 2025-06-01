<?php
require_once '_db.php';

$name = $_POST['name'] ?? '';
$start = $_POST['start'] ?? '';
$end = $_POST['end'] ?? '';
$room = $_POST['room'] ?? '';

header('Content-Type: application/json');

if (!$name || !$start || !$end || !$room) {
    echo json_encode(['result' => 'Error', 'message' => 'Всі поля обов’язкові']);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO reservations (name, start, end, room_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $start, $end, $room]);

    echo json_encode(['result' => 'OK']);
} catch (Exception $e) {
    echo json_encode(['result' => 'Error', 'message' => $e->getMessage()]);
}
