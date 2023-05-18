<?php
push_dump();



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
