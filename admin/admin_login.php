<?php
session_start(); // Munkamenet indítása
require_once '../db_config.php'; // Adatbázis konfigurációs fájl betöltése

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; // Bejövő email cím
    $password = $_POST['password']; // Bejövő jelszó

    try {
        // Felhasználó ellenőrzése az adatbázisban
        $stmt = $pdo->prepare("SELECT user_id, username, password_hash, is_admin FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['is_admin']) {
                // Munkameneti változók beállítása sikeres bejelentkezés esetén
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = true;
                header("Location: index.php"); // Átirányítás az adminisztrációs felületre
                exit;
            } else {
                $message = "Access denied. Only admin users can log in here."; // Hibaüzenet, ha nem admin felhasználó próbál bejelentkezni
            }
        } else {
            $message = "Invalid email or password."; // Hibaüzenet, ha hibás adatokat adott meg a felhasználó
        }
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage()); // Adatbázis kapcsolat hibaüzenetének megjelenítése
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PET Adoption</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/admin_login.css"> <!-- Stíluslap betöltése -->
</head>
<body>
    
    <div class="login-container">
        <img src="../images/logo.png" alt="PET Logo">
        <h2><i class="fas fa-user"></i> Admin Login</h2>
        <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?> <!-- Hibaüzenet megjelenítése szükség esetén -->
        <form action="admin_login.php" method="post">
            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" id="email" required>
            <label for="password"><i class="fas fa-lock"></i> Password:</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" value="Login">
        </form>
    </div>
    
</body>
</html>
