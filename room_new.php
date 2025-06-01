<?php
require_once '_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $name = trim($_POST['name'] ?? '');
    $capacity = intval($_POST['capacity'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    header('Content-Type: application/json');

    if (!$name || !$capacity || !$status) {
        echo json_encode(['result' => 'Error', 'message' => 'Всі поля обов’язкові']);
        exit;
    }

    try {
        $stmt = $db->prepare("INSERT INTO rooms (name, capacity, status) VALUES (?, ?, ?)");
        $stmt->execute([$name, $capacity, $status]);
        echo json_encode(['result' => 'OK']);
    } catch (Exception $e) {
        echo json_encode(['result' => 'Error', 'message' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Додати кімнату</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        label, input, select { display: block; margin-bottom: 10px; width: 100%; }
        input, select { padding: 5px; box-sizing: border-box; }
        .buttons { margin-top: 20px; }
    </style>
</head>
<body>

<h2>Додати нову кімнату</h2>

<form id="addRoomForm" method="post">
    <label for="name">Назва кімнати:</label>
    <input type="text" id="name" name="name" required />

    <label for="capacity">Місткість (кількість ліжок):</label>
    <select id="capacity" name="capacity" required>
        <option value="">-- Оберіть місткість --</option>
        <option value="1">1 (Одномісна)</option>
        <option value="2">2 (Двомісна)</option>
        <option value="4">4 (Сімейна)</option>
    </select>

    <label for="status">Статус кімнати:</label>
    <select id="status" name="status" required>
        <option value="">-- Оберіть статус --</option>
        <option value="Доступна">Доступна</option>
        <option value="Заброньована">Заброньована</option>
        <option value="В ремонті">В ремонті</option>
    </select>

    <div class="buttons">
        <button type="submit">Додати</button>
        <button type="button" onclick="closeModal(null)">Відмінити</button>
    </div>
</form>

<script>
function closeModal(result) {
    if (parent && parent.DayPilot && parent.DayPilot.ModalStatic) {
        parent.DayPilot.ModalStatic.close(result);
    }
}

$("#addRoomForm").submit(function(e) {
    e.preventDefault();
    $.post("", $(this).serialize(), function(response) {
        if (response.result === "OK") {
            closeModal(response);
        } else {
            alert("Помилка: " + response.message);
        }
    }, "json").fail(function() {
        alert("Сталася помилка при відправці запиту");
    });
});
</script>

</body>
</html>
