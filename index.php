<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Бронювання кімнат в готелі</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/daypilot-all.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        header, footer {
            background-color: #f0f0f0;
            padding: 10px 20px;
            text-align: center;
        }

        #filterBox {
            text-align: center;
            margin: 10px;
        }

        #dp {
            width: 100%;
            height: 600px;
            margin: 20px auto;
        }

        .scheduler_default_rowheader_inner {
            border-right: 1px solid #ccc;
        }

        .scheduler_default_rowheader.scheduler_default_rowheadercol2 {
            background: #fff;
        }

        .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            background-color: transparent;
            border-left: 5px solid #1a9d13;
            border-right: 0;
        }

        .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            border-left: 5px solid #ea3624;
        }

        .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner {
            border-left: 5px solid #f9ba25;
        }

        /* Модальне вікно */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 1000;
        }

        .modal {
            background: #fff;
            border-radius: 8px;
            width: 300px;
            margin: 10% auto;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            position: relative;
        }

        .modal input, .modal select {
            width: 100%;
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .modal-buttons {
            text-align: right;
        }

        .modal-buttons button {
            margin-left: 5px;
        }

        button {
            padding: 6px 10px;
            background: #1691f4;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0c7cd5;
        }
    </style>
</head>
<body>

<header>
    <h1>HTML5 Бронювання кімнат в готелі (JavaScript/PHP)</h1>
    <p>AJAX-календар з JavaScript/HTML5/jQuery</p>
</header>

<main>
    <div id="filterBox">
        <label for="filter">Показати кімнати:</label>
        <select id="filter">
            <option value="0">Всі</option>
            <option value="1">Одномісні</option>
            <option value="2">Двомісні</option>
            <option value="4">Сімейні</option>
        </select>
    </div>

    <div style="text-align:center; margin-bottom: 10px;">
        <button id="addRoomBtn">➕ Додати кімнату</button>
    </div>

    <div id="dp"></div>
</main>

<footer>
    <address>(с) Автор: студент ПЗіС-24005м, Мошнін Микита Андрійович</address>
</footer>

<!-- Модальне вікно -->
<div id="roomModal" class="modal-overlay">
  <div class="modal">
    <h3>Нова кімната</h3>
    
    <label>Назва кімнати:</label><br>
    <input type="text" id="roomName"><br>

    <label>Місткість:</label><br>
    <input type="number" id="roomCapacity"><br>

    <label>Статус:</label><br>
    <select id="roomStatus">
      <option value="вільна">Вільна</option>
      <option value="брудна">Брудна</option>
      <option value="прибирається">Прибирається</option>
    </select><br>

    <div class="modal-buttons">
      <button id="saveRoomBtn">Зберегти</button>
      <button id="closeRoomModal">Скасувати</button>
    </div>
  </div>
</div>

<script>
    var dp = new DayPilot.Scheduler("dp");

    dp.startDate = new DayPilot.Date("2025-06-01");
    dp.days = dp.startDate.daysInMonth();
    dp.scale = "Day";

    dp.timeHeaders = [
        { groupBy: "Month", format: "MMMM yyyy" },
        { groupBy: "Day", format: "d" }
    ];

    dp.rowHeaderColumns = [
        { title: "Кімната", width: 80 },
        { title: "Місткість", width: 80 },
        { title: "Статус", width: 80 }
    ];

    dp.allowEventOverlap = false;

    dp.onBeforeResHeaderRender = function(args) {
        var beds = function(count) {
            return count + " ліжко" + (count > 1 ? "в" : "");
        };
        args.resource.columns[0].html = args.resource.name;
        args.resource.columns[1].html = beds(args.resource.capacity);
        args.resource.columns[2].html = args.resource.status;

        switch (args.resource.status.toLowerCase()) {
            case "брудна":
                args.resource.cssClass = "status_dirty";
                break;
            case "прибирається":
                args.resource.cssClass = "status_cleanup";
                break;
            default:
                args.resource.cssClass = "";
                break;
        }
    };

    dp.onBeforeEventRender = function(args) {
        var start = new DayPilot.Date(args.e.start);
        var end = new DayPilot.Date(args.e.end);
        var today = DayPilot.Date.today();
        var now = new DayPilot.Date();

        args.e.html = args.e.text + " (" + start.toString("M/d/yyyy") + " - " + end.toString("M/d/yyyy") + ")";

        switch (args.e.status) {
            case "New":
                var in2days = today.addDays(1);
                if (start < in2days) {
                    args.e.barColor = 'red';
                    args.e.toolTip = 'Застаріле';
                } else {
                    args.e.barColor = 'orange';
                    args.e.toolTip = 'Новий';
                }
                break;
            case "Confirmed":
                args.e.barColor = "green";
                args.e.toolTip = "Підтверджено";
                break;
            case "Arrived":
                args.e.barColor = "#1691f4";
                args.e.toolTip = "Прибув";
                break;
            case "CheckedOut":
                args.e.barColor = "gray";
                args.e.toolTip = "Перевірено";
                break;
            default:
                args.e.barColor = "#999999";
                args.e.toolTip = "Невідомий стан";
                break;
        }

        args.e.html += "<br /><span style='color:gray'>" + args.e.toolTip + "</span>";
    };

    dp.onTimeRangeSelected = function(args) {
        var modal = new DayPilot.Modal({
            onClosed: function(modalArgs) {
                dp.clearSelection();
                if (modalArgs.result && modalArgs.result.result === "OK") {
                    loadEvents();
                }
            }
        });
        modal.showUrl("new.php?start=" + args.start.toString() + "&end=" + args.end.toString() + "&resource=" + args.resource);
    };

    dp.onEventClick = function(args) {
        var modal = new DayPilot.Modal({
            onClosed: function(modalArgs) {
                if (modalArgs.result && modalArgs.result.result === "OK") {
                    loadEvents();
                }
            }
        });
        modal.showUrl("edit.php?id=" + args.e.id());
    };

    dp.eventDeleteHandling = "Update";
    dp.onEventDeleted = function(args) {
        $.post("backend_delete.php", { id: args.e.id() }, function(data) {
            if (data.result === "OK") {
                dp.message("Бронювання видалено");
                loadEvents();
            } else {
                dp.message("Помилка: " + data.message);
            }
        }, "json");
    };

    function loadResources() {
        var selectedCapacity = $("#filter").val();
        $.post("backend_rooms.php", { capacity: selectedCapacity }, function(data) {
            dp.resources = data;
            dp.update();
            loadEvents();
        }, "json");
    }

    function loadEvents() {
        var start = dp.visibleStart().toString();
        var end = dp.visibleEnd().toString();
        $.post("backend_events.php", { start: start, end: end }, function(data) {
            dp.events.list = data;
            dp.update();
        }, "json");
    }

    $(document).ready(function() {
        $("#filter").change(function() {
            loadResources();
        });

        $("#addRoomBtn").click(function() {
            $("#roomModal").fadeIn();
        });

        $("#closeRoomModal").click(function() {
            $("#roomModal").fadeOut();
        });

        $("#saveRoomBtn").click(function() {
            const name = $("#roomName").val();
            const capacity = $("#roomCapacity").val();
            const status = $("#roomStatus").val();

            $.post("room_new.php", {
                name: name,
                capacity: capacity,
                status: status
            }, function(response) {
                if (response.result === "OK") {
                    $("#roomModal").fadeOut();
                    loadResources();
                } else {
                    alert("Помилка: " + response.message);
                }
            }, "json");
        });

        dp.init();
        loadResources();
    });
</script>

</body>
</html>
