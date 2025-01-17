<?php
// Подключение к базе данных
$db = new PDO('mysql:host=localhost;dbname=u67392', 'u67392', '8418636', array(PDO::ATTR_PERSISTENT => true));

// Проверка, был ли передан параметр id через GET-запрос
if (!isset($_GET['id'])) {
    echo "Ошибка: ID пользователя не указан.";
    exit();
}

// Получение данных о пользователе по его ID
$stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
$stmt->execute([$_GET['id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Если пользователь с указанным ID не найден
if (!$userData) {
    echo "Пользователь с указанным ID не найден.";
    exit();
}

// Если форма была отправлена для обновления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Обработка данных формы

    // Пример обновления данных пользователя в базе данных
    $stmt = $db->prepare("UPDATE application SET names = ?, phones = ?, email = ?, dates = ?, gender = ?, biography = ? WHERE id = ?");
    $stmt->execute([
        $_POST['names'],
        $_POST['phones'],
        $_POST['email'],
        $_POST['dates'],
        $_POST['gender'],
        $_POST['biography'],
        $_GET['id']
    ]);

    header("Location: admin.php");
    exit();
}

// Если форма была отправлена для удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    // Получаем идентификатор пользователя, которого нужно удалить
    $userId = $_GET['id'];

    // Удаление связанных записей из таблицы application_languages
    $stmt = $db->prepare("DELETE FROM application_languages WHERE id_app = ?");
    $stmt->execute([$userId]);

    // Затем удаляем пользователя из таблицы application
    $stmt = $db->prepare("DELETE FROM application WHERE id = ?");
    $stmt->execute([$userId]);

    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Редактирование пользователя</h1>
    <form method="POST">
        <label for="names">Имя:</label><br>
        <input type="text" id="names" name="names" value="<?php echo $userData['names']; ?>"><br>
        <label for="phones">Телефон:</label><br>
        <input type="tel" id="phones" name="phones" value="<?php echo $userData['phones']; ?>"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo $userData['email']; ?>"><br>
        <label for="dates">Дата рождения:</label><br>
        <input type="date" id="dates" name="dates" value="<?php echo $userData['dates']; ?>"><br>
        <label for="gender">Пол:</label><br>
        <select id="gender" name="gender">
            <option value="M" <?php if ($userData['gender'] == 'M') echo 'selected'; ?>>Мужской</option>
            <option value="F" <?php if ($userData['gender'] == 'F') echo 'selected'; ?>>Женский</option>
        </select><br>
        <label for="biography">Биография:</label><br>
        <textarea id="biography" name="biography"><?php echo $userData['biography']; ?></textarea><br>
        <input type="submit" name="update" value="Сохранить изменения">
        <input type="submit" name="delete" value="Удалить пользователя" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">
    </form>
</body>
</html>
