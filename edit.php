<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Редагування бронювання</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        label, input, select { display: block; margin-bottom: 10px; }
        input, select { width: 100%; padding: 5px; }
        .buttons { margin-top: 20px; }
    </style>
</head>
<body>

<?php
require_once '_db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$statement = $db->prepare("SELECT * FROM reservations WHERE id = ?");
$statement->execute([$id]);
$event = $statement->fetch();

if (!$event) {
    echo "<p>Бронювання не знайдено.</p>";
    exit;
}

$rooms = $db->query('SELECT * FROM rooms')->fetchAll();

$statuses = ["New", "Confirmed", "Arrived", "CheckedOut"];

function formatDateTimeLocal($dt) {
    return date('Y-m-d\TH:i', strtotime($dt));
}
?>

<h1>Редагувати бронювання</h1>
<form id="editForm" action="backend_update.php">
    <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>" />

    <label for="name">Ім'я:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($event['name']) ?>" required />

    <label for="start">Початок:</label>
    <input type="datetime-local" id="start" name="start" value="<?= formatDateTimeLocal($event['start']) ?>" required />

    <label for="end">Кінець:</label>
    <input type="datetime-local" id="end" name="end" value="<?= formatDateTimeLocal($event['end']) ?>" required />

    <label for="room">Кімната:</label>
    <select id="room" name="room" required>
        <?php foreach ($rooms as $room): 
            $selected = ($room['id'] == $event['room_id']) ? ' selected' : '';
        ?>
            <option value="<?= $room['id'] ?>"<?= $selected ?>><?= htmlspecialchars($room['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="status">Статус:</label>
    <select id="status" name="status" required>
        <?php foreach ($statuses as $status): 
            $selected = ($status == $event['status']) ? ' selected' : '';
        ?>
            <option value="<?= htmlspecialchars($status) ?>"<?= $selected ?>><?= htmlspecialchars($status) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="paid">Оплата:</label>
    <select id="paid" name="paid" required>
        <option value="0" <?= ($event['paid'] == 0) ? 'selected' : '' ?>>0%</option>
        <option value="50" <?= ($event['paid'] == 50) ? 'selected' : '' ?>>50%</option>
        <option value="100" <?= ($event['paid'] == 100) ? 'selected' : '' ?>>100%</option>
    </select>

    <div class="buttons">
        <button type="submit">Зберегти</button>
        <button type="button" onclick="close(null)">Відмінити</button>
    </div>
</form>

<script>
    function close(result) {
        if (parent && parent.DayPilot && parent.DayPilot.ModalStatic) {
            parent.DayPilot.ModalStatic.close(result);
        }
    }

    $("#editForm").submit(function () {
        $.post($(this).attr("action"), $(this).serialize(), function (result) {
            // Очікуємо, що backend поверне JSON
            try {
                var data = JSON.parse(result);
                if (data.result === "OK") {
                    close(data);
                } else {
                    alert("Помилка: " + data.message);
                }
            } catch (e) {
                alert("Невірна відповідь сервера.");
            }
        });
        return false;
    });

    $(document).ready(function () {
        $("#name").focus();
    });
</script>

</body>
</html>
