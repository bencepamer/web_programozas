<?php
include 'admin_navbar.php'; // Admin navbar include, amely tartalmazza az adminisztrációs menüt
require_once '../db_config.php'; // Adatbázis konfigurációs fájl betöltése

// Check if the admin user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php"); // Átirányítás a bejelentkezési oldalra, ha nem adminisztrátor van bejelentkezve
    exit;
}

// Felhasználó adatainak lekérdezése az adatbázisból
try {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Példa: Az összes felhasználó lekérdezése
try {
    $users_stmt = $pdo->query("SELECT * FROM users");
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Példa: Az összes állat lekérdezése
try {
    $animals_stmt = $pdo->query("SELECT * FROM animals");
    $animals = $animals_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PET Adoption</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/index.css"> <!-- Egyedi stíluslap betöltése -->
</head>
<body>
    
</body>
</html>
