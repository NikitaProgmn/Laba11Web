<?php
require_once '_db.php';

$capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 0;

if ($capacity === 0) {
    // Без фільтра — всі кімнати
    $stmt = $db->prepare("SELECT * FROM rooms ORDER BY name");
} else {
    // Фільтрація по місткості
    $stmt = $db->prepare("SELECT * FROM rooms WHERE capacity = :capacity ORDER BY name");
    $stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
}

$stmt->execute();
$rooms = $stmt->fetchAll();

class Room {}
$result = [];

foreach($rooms as $room) {
    $r = new Room();
    $r->id = $room['id'];
    $r->name = $room['name'];
    $r->capacity = $room['capacity'];
    $r->status = $room['status'];
    $result[] = $r;
}

header('Content-Type: application/json');
echo json_encode($result);
