<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJAX-доступ к базе данных Python</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../style/style.css">
    <style>
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .filter-container { margin: 20px; text-align: center; }
        select { padding: 5px; }
        #browserInfo { margin: 10px; font-style: italic; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header__inner container">
            <div class="header__logo-title">
                <img class="header__logo" src="../images/header_hat.jpg" alt="Шапка сайта">
                <h1 class="header__title">Язык программирования Python</h1>
            </div>
            <nav class="header__menu">
                <ul class="header__menu-list">
                    <li class="header__menu-item"><a class="header__menu-link" href="../index.html">Главная</a></li>
                    <li class="header__menu-item"><a class="header__menu-link" href="characteristics.html">Таблица характеристик</a></li>
                    <li class="header__menu-item"><a class="header__menu-link" href="sources.html">Источники</a></li>
                    <li class="header__menu-item"><a class="header__menu-link" href="anketa.html">Анкета</a></li>
                    <li class="header__menu-item"><a class="header__menu-link" href="database.php">База данных</a></li>
                    <li class="header__menu-item"><a class="header__menu-link" href="ajax_database.php">AJAX-доступ</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <hr>
    <main class="main">
        <section class="section container">
            <h2 class="section__title">AJAX-доступ к базе данных библиотек Python</h2>
            <div class="filter-container">
                <label for="categoryFilter">Выберите категорию:</label>
                <select id="categoryFilter" onchange="loadData()">
                    <option value="">Все категории</option>
                    <?php
                    // Подключение к БД
                    $host = 'localhost';
                    $user = 'root';
                    $pass = '';
                    $dbname = 'python_db';
                    $conn = new mysqli($host, $user, $pass, $dbname);
                    if ($conn->connect_error) {
                        echo "<option value=''>Ошибка подключения к БД</option>";
                    } else {
                        $conn->set_charset("utf8");
                        // Получаем уникальные категории только для библиотек с примерами использования
                        $sql = "SELECT DISTINCT l.category 
                                FROM libraries l 
                                INNER JOIN use_cases u ON l.id = u.library_id 
                                ORDER BY l.category";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $category = htmlspecialchars($row['category']);
                                echo "<option value='$category'>$category</option>";
                            }
                        }
                        $conn->close();
                    }
                    ?>
                </select>
            </div>
            <div id="browserInfo"></div>
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>Библиотека</th>
                        <th>Версия</th>
                        <th>Категория</th>
                        <th>Описание</th>
                        <th>Пример использования</th>
                        <th>Сложность</th>
                        <th>Код</th>
                    </tr>
                </thead>
                <tbody id="dataBody"></tbody>
            </table>
        </section>
    </main>
    <footer class="footer">
        <address class="footer__address">
            <a href="mailto:kate_utkate@vk.com">kate_utkate@vk.com</a>
            <p>© 2025 Все права защищены.</p>
        </address>
    </footer>
    <script>
        function loadData() {
            // Определяем объект для AJAX
            let xhr;
            if (window.XMLHttpRequest) {
                xhr = new XMLHttpRequest();
            } else {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }

            // Определяем браузер
            let browser = navigator.userAgent;
            let browserName = "Unknown";
            if (browser.includes("Edg")) browserName = "Microsoft Edge";
            else if (browser.includes("Chrome")) browserName = "Chrome";
            else if (browser.includes("Firefox")) browserName = "Firefox";
            else if (browser.includes("Safari")) browserName = "Safari";

            // Выводим информацию о браузере и объекте
            document.getElementById("browserInfo").innerHTML = 
                "Объект для асинхронного обмена: " + xhr.constructor.name + 
                ", Браузер: " + browserName;

            // Получаем выбранную категорию
            let category = document.getElementById("categoryFilter").value;
            let url = "../get_data.php" + (category ? "?category=" + encodeURIComponent(category) : "");

            // Настраиваем запрос
            xhr.open("GET", url, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let data = JSON.parse(xhr.responseText);
                    let tbody = document.getElementById("dataBody");
                    tbody.innerHTML = ""; // Очищаем таблицу

                    if (data.error) {
                        tbody.innerHTML = "<tr><td colspan='7'>" + data.error + "</td></tr>";
                        return;
                    }

                    // Заполняем таблицу
                    data.forEach(row => {
                        let tr = document.createElement("tr");
                        tr.innerHTML = 
                            "<td>" + row.name + "</td>" +
                            "<td>" + row.version + "</td>" +
                            "<td>" + row.category + "</td>" +
                            "<td>" + row.description + "</td>" +
                            "<td>" + (row.use_case_name || "Нет") + "</td>" +
                            "<td>" + (row.difficulty || "Нет") + "</td>" +
                            "<td><pre>" + (row.example_code || "Нет") + "</pre></td>";
                        tbody.appendChild(tr);
                    });
                }
            };
            xhr.send();
        }

        // Загружаем данные при загрузке страницы
        window.onload = loadData;
    </script>
</body>
</html>