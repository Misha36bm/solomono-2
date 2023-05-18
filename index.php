<?php
// Пушим дамп таблиці в базу
push_dump();

// Витягуєм дані таблиці з бази
$array = get_data();

// Вимірюємо час виконання
$start = microtime(true);
$tree = buildTree($array);
$end = microtime(true);
$executionTime = $end - $start;

echo "Час виконання скрипта: $executionTime секунд";

// Видаляємо таблицю після виконання скрипта
drop_table();

function push_dump()
{
    $hostname = 'localhost';
    $username = 'root';
    $password = '123';
    $database = 'tz_2';

    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }

    $sql = file_get_contents(__DIR__ . '/dump.sql');

    $statements = explode(';', $sql);

    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            $pdo->exec($statement);
        }
    }

    $pdo = null;
}

function get_data()
{
    $hostname = 'localhost';
    $username = 'root';
    $password = '123';
    $database = 'tz_2';

    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    $sql = "SELECT * FROM categories";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $results;
}

function buildTree($array)
{
}

function drop_table()
{
    $hostname = 'localhost';
    $username = 'root';
    $password = '123';
    $database = 'tz_2';

    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    $sql = "DROP TABLE categories";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
