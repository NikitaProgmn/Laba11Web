<?php
require_once '_db.php';

class Result {}

if (isset($_POST['id'])) {
    $stmt = $db->prepare("DELETE FROM reservations WHERE id = :id");
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->execute();

    $response = new Result();
    $response->result = 'OK';
    $response->message = 'Delete successful';

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
