<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Нове бронювання</title>
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

$rooms = $db->query('SELECT * FROM rooms')->fetchAll();

function formatDateTimeLocal($dt) {
    if (!$dt) return '';
    return date('Y-m-d\TH:i', strtotime($dt));
}

$start = isset($_GET['start']) ? formatDateTimeLocal($_GET['start']) : '';
$end = isset($_GET['end']) ? formatDateTimeLocal($_GET['end']) : '';
$resource = isset($_GET['resource']) ? $_GET['resource'] : '';
?>

<h1>Нове бронювання</h1>
<form id="reservationForm">
    <label for="name">Ім'я:</label>
    <input type="text" id="name" name="name" required />

    <label for="start">Початок:</label>
    <input type="datetime-local" id="start" name="start" value="<?= $start ?>" required />

    <label for="end">Кінець:</label>
    <input type="datetime-local" id="end" name="end" value="<?= $end ?>" required />

    <label for="room">Кімната:</label>
    <select id="room" name="room" required>
        <?php foreach ($rooms as $room): 
            $selected = ($resource == $room['id']) ? ' selected' : '';
        ?>
            <option value="<?= $room['id'] ?>"<?= $selected ?>><?= htmlspecialchars($room['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <div class="buttons">
        <button type="submit">Зберегти</button>
        <button type="button" onclick="window.top.DayPilot.Modal.close(null);">Відмінити</button>
    </div>
</form>

<script>
    $('#reservationForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'backend_create.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.result === 'OK') {
                    alert('Бронювання успішно додано');
                    window.top.DayPilot.Modal.close({result: 'OK'});
                } else {
                    alert('Помилка: ' + response.message);
                }
            },
            error: function() {
                alert('Сталася помилка при збереженні бронювання.');
            }
        });
    });
</script>

</body>
</html>
