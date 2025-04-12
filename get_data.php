<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'python_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Ошибка подключения к БД: ' . $conn->connect_error]);
    exit();
}
$conn->set_charset("utf8");

// Получаем категорию из GET-запроса
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Запрос к БД
if ($category) {
    $sql = "SELECT l.name, l.version, l.category, l.description, u.use_case_name, u.difficulty, u.example_code 
            FROM libraries l 
            INNER JOIN use_cases u ON l.id = u.library_id 
            WHERE l.category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT l.name, l.version, l.category, l.description, u.use_case_name, u.difficulty, u.example_code 
            FROM libraries l 
            INNER JOIN use_cases u ON l.id = u.library_id";
    $result = $conn->query($sql);
}

// Формируем массив данных
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Возвращаем данные в формате JSON
echo json_encode($data);

$conn->close();
?>