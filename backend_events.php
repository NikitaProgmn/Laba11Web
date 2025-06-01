<?php
require_once '_db.php';

date_default_timezone_set("UTC");

$start = isset($_POST['start']) ? $_POST['start'] : null;
$end = isset($_POST['end']) ? $_POST['end'] : null;

if (!$start || !$end) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing "start" or "end" parameters']);
    exit;
}

// Форматируем даты для MySQL (заменяем 'T' на пробел)
$start = str_replace('T', ' ', $start);
$end = str_replace('T', ' ', $end);

try {
    $stmt = $db->prepare("SELECT * FROM reservations WHERE NOT ((end <= :start) OR (start >= :end))");
    $stmt->bindParam(':start', $start);
    $stmt->bindParam(':end', $end);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = [];

    foreach ($result as $row) {
        $events[] = [
            "id" => $row['id'],
            "text" => $row['name'],
            "start" => $row['start'],
            "end" => $row['end'],
            "resource" => $row['room_id'],
            "bubbleHtml" => "Reservation details: " . htmlspecialchars($row['name']),
            "status" => $row['status'],
            "paid" => $row['paid']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($events);

} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $ex->getMessage()]);
}
?>
