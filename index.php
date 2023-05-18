<?php
// Пушим дамп таблиці в базу
push_dump();

// Витягуєм дані таблиці з бази
$array = get_data();

// Вимірюємо час виконання
$start = microtime(true);
$tree = build_tree($array);
$end = microtime(true);
$executionTime = $end - $start;

echo "Час виконання скрипта: $executionTime секунд";

echo '<pre>';
var_export($tree);
echo '</pre>';

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

/**
 * Побудовує дерево на основі заданого масиву даних.
 *
 * Функція приймає масив даних у форматі:
 * [
 *     0 => ['categories_id' => 1, 'parent_id' => 0],
 *     1 => ['categories_id' => 2, 'parent_id' => 0],
 *     ...
 * ]
 *
 * Кожен елемент масиву представляє категорію з властивостями 'categories_id' та 'parent_id'.
 * 'categories_id' - ідентифікатор категорії
 * 'parent_id' - ідентифікатор батьківської категорії
 *
 * Функція будує дерево, де кожен елемент масиву виступає як вузол дерева. Категорії з батьківським
 * 'parent_id' рівним 0 вважаються кореневими вузлами. Категорії з неправильним 'parent_id' (які не
 * мають відповідного батьківського елемента в масиві) будуть проігноровані.
 *
 * У побудованому дереві кожен елемент представлений асоціативним масивом з ключами:
 * 'categories_id' - ідентифікатор категорії
 * 'parent_id' - ідентифікатор батьківської категорії
 * 'children' - масив дочірніх елементів, якщо вони є. Внутрішнє подерево також має таку структуру.
 *
 * @param array $array Масив даних для побудови дерева.
 * @return array Масив, що представляє побудоване дерево.
 */
function build_tree($array)
{
    $hashTable = []; // Хеш-таблиця для швидкого доступу до елементів за їх categories_id
    $tree = []; // Результат побудованого дерева

    // Заповнення хеш-таблиці
    foreach ($array as $key => $value) {
        $hashTable[$value['categories_id']] = &$array[$key];
    }

    // Побудова дерева
    foreach ($array as $key => $value) {
        $parent_id = $value['parent_id'];
        if ($parent_id == 0) {
            // Кореневий вузол
            $tree[$value['categories_id']] = &$array[$key];
        } else {
            // Пошук батьківського вузла за parent_id
            if (isset($hashTable[$parent_id])) {
                $parent = &$hashTable[$parent_id];
                // Додавання поточного вузла як дочірнього для батьківського вузла
                $parent['children'][$value['categories_id']] = &$array[$key];
            }
        }
    }

    return $tree;
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
