<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
</head>
<body>
    <h1>Форма</h1>
    <form method="post">
        <label>Введи текст </label><input type="text" name="text" value="<?php echo isset($text) ? htmlspecialchars($text) : ''; ?>"/><br><br>
        <label>Введи почту </label><input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"/><br><br>
        <label>Введи номер </label><input type="number" name="number" value="<?php echo isset($number) ? htmlspecialchars($number) : ''; ?>"/><br><br>
        <label>Выбери марку тачки </label>
        <select name="car">
            <option value="audi" <?php if (isset($car) && $car == 'audi') echo 'selected'; ?>>Audi</option>
            <option value="bmw" <?php if (isset($car) && $car == 'bmw') echo 'selected'; ?>>BMW</option>
            <option value="mercedes" <?php if (isset($car) && $car == 'mercedes') echo 'selected'; ?>>Mercedes</option>
            <option value="toyota" <?php if (isset($car) && $car == 'toyota') echo 'selected'; ?>>Toyota</option>
        </select><br><br>
        <label>Выбери цвет тачки</label>
        <input type="radio" name="color" value="black" <?php if (isset($color) && $color == 'black') echo 'checked'; ?>><label>черный</label>
        <input type="radio" name="color" value="white" <?php if (isset($color) && $color == 'white') echo 'checked'; ?>><label>белый</label>
        <input type="radio" name="color" value="red" <?php if (isset($color) && $color == 'red') echo 'checked'; ?>><label>красный</label>
        <input type="radio" name="color" value="blue" <?php if (isset($color) && $color == 'blue') echo 'checked'; ?>><label>синий</label><br><br>
        <label>Выбери ништяки</label>
        <input type="checkbox" name="light" value="light" <?php if (isset($_POST['light'])) echo 'checked'; ?>><label>подсветка</label>
        <input type="checkbox" name="fridge" value="fridge" <?php if (isset($_POST['fridge'])) echo 'checked'; ?>><label>холодильник</label>
        <input type="checkbox" name="termocup" value="termocup" <?php if (isset($_POST['termocup'])) echo 'checked'; ?>><label>термокружка</label>
        <input type="checkbox" name="subwoofer" value="subwoofer" <?php if (isset($_POST['subwoofer'])) echo 'checked'; ?>><label>сабвуфер</label><br><br>
        <label>Введите пароль </label><input type="password" name="password"/><br><br>
        <input type="submit" value="Отправить">
    </form>
</body>
</html>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errors = [];

        $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($text)) {
            $errors[] = "Поле 'текст' обязательно для заполнения";
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Некорректный email";
        }

        $number = filter_input(INPUT_POST, 'number', FILTER_VALIDATE_INT);
        if ($number === false) {
            $errors[] = "Поле 'номер' должно быть целым числом";
        }

        $car = filter_input(INPUT_POST, 'car', FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($car)) {
            $errors[] = "Выберите марку автомобиля";
        }

        $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_SPECIAL_CHARS);
        if (empty($color)) {
            $errors[] = "Выберите цвет автомобиля";
        }

        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        if (strlen($password) < 6) {
            $errors[] = "Пароль должен быть не менее 6 символов";
        }

        $options = [];
        if (isset($_POST["light"])) $options[] = "Подсветка";
        if (isset($_POST["fridge"])) $options[] = "Холодильник";
        if (isset($_POST["termocup"])) $options[] = "Термокружка";
        if (isset($_POST["subwoofer"])) $options[] = "Сабвуфер";

        if (empty($errors)) {
            $data = [
                "timestamp" => date(DATE_ATOM),
                "text" => $text,
                "email" => $email,
                "number" => $number,
                "car" => $car,
                "color" => $color,
                "options" => $options,
                "password" => password_hash($password, PASSWORD_DEFAULT) 
            ];

            $jsonData = json_encode($data, JSON_PRETTY_PRINT);
            $logFile = "log.json";

            
            if (!file_exists($logFile)) {
                if (!touch($logFile)) {
                    $errors[] = "Не удалось создать лог-файл!";
                }
            }

            if (file_put_contents($logFile, $jsonData . PHP_EOL, FILE_APPEND | LOCK_EX) === false) {
                $errors[] = "Ошибка записи в лог-файл!";
            } else {
                echo "Данные сохранены в файл log.json";
            }
        }
    }


    if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    