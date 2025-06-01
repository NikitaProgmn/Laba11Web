<?php
require_once '_db.php';

// Отримуємо дані з POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = $_POST['name'] ?? '';
$start = $_POST['start'] ?? '';
$end = $_POST['end'] ?? '';
$room = isset($_POST['room']) ? intval($_POST['room']) : 0;
$status = $_POST['status'] ?? '';
$paid = isset($_POST['paid']) ? intval($_POST['paid']) : 0;

// Валідація (можна доповнити)
if (!$id || !$name || !$start || !$end || !$room || !$status || !in_array($paid, [0,50,100])) {
    echo json_encode(['result' => 'Error', 'message' => 'Некоректні дані']);
    exit;
}

// Оновлюємо дані в базі
try {
    $stmt = $db->prepare("UPDATE reservations SET name = ?, start = ?, end = ?, room_id = ?, status = ?, paid = ? WHERE id = ?");
    $stmt->execute([$name, $start, $end, $room, $status, $paid, $id]);

    echo json_encode(['result' => 'OK']);
} catch (Exception $e) {
    echo json_encode(['result' => 'Error', 'message' => $e->getMessage()]);
}
