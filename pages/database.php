<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>База данных Python</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../style/style.css">
    <style>
        table { border-collapse: collapse; width: 100%; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .action-btn { padding: 5px 10px; margin: 2px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .action-btn:hover { background-color: #45a049; }
        .delete-btn { background-color: #f44336; }
        .delete-btn:hover { background-color: #da190b; }
        .form-container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: inline-block; width: 150px; }
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
            <h2 class="section__title">База данных библиотек Python</h2>
            <?php
            // Подключение к БД
            $host = 'localhost';
            $user = 'root';
            $pass = ''; // Пароль по умолчанию в XAMPP пустой
            $dbname = 'python_db';
            $conn = new mysqli($host, $user, $pass, $dbname);
            if ($conn->connect_error) {
                echo "<p>Ошибка подключения к БД: " . $conn->connect_error . "</p>";
                exit();
            }
            $conn->set_charset("utf8");

            // Обработка добавления новой записи
            if (isset($_POST['add'])) {
                $lib_name = $_POST['lib_name'];
                $version = $_POST['version'];
                $category = $_POST['category'];
                $description = $_POST['description'];
                $use_case_name = $_POST['use_case_name'];
                $difficulty = $_POST['difficulty'];
                $example_code = $_POST['example_code'];

                $sql = "INSERT INTO libraries (name, version, category, description) VALUES ('$lib_name', '$version', '$category', '$description')";
                if ($conn->query($sql) === TRUE) {
                    $library_id = $conn->insert_id;
                    $sql = "INSERT INTO use_cases (library_id, use_case_name, difficulty, example_code) VALUES ('$library_id', '$use_case_name', '$difficulty', '$example_code')";
                    $conn->query($sql);
                }
            }

            // Обработка удаления записи
            if (isset($_GET['delete'])) {
                $use_case_id = $_GET['delete'];
                $sql = "DELETE FROM use_cases WHERE id='$use_case_id'";
                $conn->query($sql);
            }

            // Выборка данных
            $sql = "SELECT l.name, l.version, l.category, l.description, u.id AS use_case_id, u.use_case_name, u.difficulty, u.example_code 
                    FROM libraries l 
                    INNER JOIN use_cases u ON l.id = u.library_id";
            $result = $conn->query($sql);

            // Отображение данных в таблице
            echo "<table>";
            echo "<tr><th>Библиотека</th><th>Версия</th><th>Категория</th><th>Описание</th><th>Пример использования</th><th>Сложность</th><th>Код</th><th>Действия</th></tr>";
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    printf("<td>%s</td>", htmlspecialchars($row['name']));
                    printf("<td>%s</td>", htmlspecialchars($row['version']));
                    printf("<td>%s</td>", htmlspecialchars($row['category']));
                    printf("<td>%s</td>", htmlspecialchars($row['description']));
                    printf("<td>%s</td>", htmlspecialchars($row['use_case_name'] ?: 'Нет'));
                    printf("<td>%s</td>", htmlspecialchars($row['difficulty'] ?: 'Нет'));
                    printf("<td><pre>%s</pre></td>", htmlspecialchars($row['example_code'] ?: 'Нет'));
                    echo "<td>";
                    if ($row['use_case_id']) {
                        echo "<a href='database.php?delete=" . $row['use_case_id'] . "' class='action-btn delete-btn'>Удалить</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Нет данных для отображения</td></tr>";
            }
            echo "</table>";

            // Форма добавления
            echo "<div class='form-container'>";
            echo "<h3>Добавить новую библиотеку и пример</h3>";
            echo "<form method='POST'>";
            echo "<div class='form-group'><label>Название библиотеки:</label><input type='text' name='lib_name' required></div>";
            echo "<div class='form-group'><label>Версия:</label><input type='text' name='version'></div>";
            echo "<div class='form-group'><label>Категория:</label><input type='text' name='category'></div>";
            echo "<div class='form-group'><label>Описание:</label><textarea name='description'></textarea></div>";
            echo "<div class='form-group'><label>Пример использования:</label><input type='text' name='use_case_name' required></div>";
            echo "<div class='form-group'><label>Сложность:</label><select name='difficulty'><option value='Beginner'>Новичок</option><option value='Intermediate'>Средний</option><option value='Advanced'>Продвинутый</option></select></div>";
            echo "<div class='form-group'><label>Пример кода:</label><textarea name='example_code'></textarea></div>";
            echo "<button type='submit' name='add' class='action-btn'>Добавить</button>";
            echo "</form>";
            echo "</div>";

            $conn->close();
            ?>
        </section>
    </main>
    <footer class="footer">
        <address class="footer__address">
            <a href="mailto:kate_utkate@vk.com">kate_utkate@vk.com</a>
            <p>© 2025 Все права защищены.</p>
        </address>
    </footer>
</body>
</html>