<?php
// db_config.php

$host = 'localhost'; // adatbázis host
$dbname = 'mam'; // adatbázis neve
$username = 'mam'; // adatbázis felhasználónév
$password = 'MkmGXuJy0fa9N8f'; // adatbázis jelszó

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // PDO hibakivétel bekapcsolása
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Hiba az adatbázis kapcsolódás során: " . $e->getMessage());
}
?>
