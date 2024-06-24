<?php
// Munkamenet kezdése a felhasználói adatok tárolásához
session_start();

// Adatbázis kapcsolódási konfiguráció beillesztése
require_once 'db_config.php';

// Alapértelmezett időzóna beállítása Europe/Budapest-re
date_default_timezone_set('Europe/Budapest');

// Ellenőrizze, hogy a kérés GET metódussal érkezett-e és van-e 'token' paraméter a URL-ben
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    // A reset token kiolvasása a URL-ben kapott paraméterből
    $resetToken = $_GET['token'];

    try {
        // Jelenlegi dátum és idő lekérdezése 'Y-m-d H:i:s' formátumban
        $currentDateTime = date('Y-m-d H:i:s');

        // SQL utasítás előkészítése a reset token ellenőrzésére, hogy létezik-e és érvényes-e
        $stmt = $pdo->prepare("SELECT * FROM password_reset_requests WHERE reset_token = ? AND token_expiry > ?");
        // SQL utasítás végrehajtása a token és jelenlegi dátum paraméterekkel
        $stmt->execute([$resetToken, $currentDateTime]);
        // Eredmény sor kiolvasása
        $request = $stmt->fetch();

        // Ellenőrzés, hogy van-e érvényes sor (kérelem)
        if ($request) {
            // Ha érvényes a token, felhasználói ID elmentése munkamenetbe jelszó resethez
            $_SESSION['reset_user_id'] = $request['user_id'];
            // Átirányítás a jelszó változtatás oldalra
            header("Location: change_password.php");
            exit();
        } else {
            // Ha érvénytelen vagy lejárt a token, hibaüzenet mentése munkamenetbe
            $_SESSION['error_message'] = "Érvénytelen vagy lejárt jelszó visszaállító link. Kérj újat.";
            // Átirányítás a jelszó elfelejtő oldalra
            header("Location: forgot_password.php");
            exit();
        }
    } catch (PDOException $e) {
        // Adatbázis kapcsolódási hiba kezelése
        die("Adatbázis kapcsolódási hiba: " . $e->getMessage());
    }
} else {
    // Ha nem GET metódussal érkezett a kérés vagy hiányzik a 'token' paraméter, hibaüzenet mentése munkamenetbe
    $_SESSION['error_message'] = "Érvénytelen kérés.";
    // Átirányítás a jelszó elfelejtő oldalra
    header("Location: forgot_password.php");
    exit();
}
?>
