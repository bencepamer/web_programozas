<?php
// Munkamenet kezdése a felhasználói adatok tárolásához
session_start();

// Adatbázis kapcsolódási konfiguráció beillesztése
require_once 'db_config.php';

// Mailer.php fájl beillesztése a levelezés funkciók használatához
require_once 'mailer.php';

// Üzenet változó inicializálása
$message = '';

// POST kérés ellenőrzése
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Űrlapról kapott adatok kiolvasása
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    // Ellenőrizzük, hogy az e-mail cím már foglalt-e
    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['count'] > 0) {
        $message = "A megadott e-mail cím már foglalt.";
    } else {
        // Ha az e-mail cím egyedi, generálunk egy aktiváló kódot
        $activation_token = bin2hex(random_bytes(50));
        $activation_link = "https://mam.stud.vts.su.ac.rs//activate.php?token=$activation_token";

        // Session változók tárolása
        $_SESSION['activation_token'] = $activation_token;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password; // A jelszó hashelése csak az activate.php oldalon történik
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;

        // Email küldése aktiváló linkkel
        $email_subject = 'Fiók aktiválás - PET Adoption';
        $email_body = '<!DOCTYPE html>
                        <html lang="hu">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Fiók aktiválás - PET Adoption</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    background-color: #f4f4f4;
                                    margin: 0;
                                    padding: 0;
                                }
                                .container {
                                    max-width: 600px;
                                    margin: 0 auto;
                                    padding: 20px;
                                    background: #ffffff;
                                    border-radius: 5px;
                                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                }
                                .header {
                                    background-color: #FBF7EB;
                                    color: #ffffff;
                                    text-align: center;
                                    padding: 10px;
                                    border-top-left-radius: 5px;
                                    border-top-right-radius: 5px;
                                }
                                .content {
                                    padding: 20px;
                                    text-align: left;
                                }
                                .button {
                                    display: inline-block;
                                    padding: 10px 20px;
                                    background-color: #5cb85c;
                                    color: #ffffff;
                                    text-decoration: none;
                                    border-radius: 5px;
                                    margin-top: 20px;
                                }
                                .button:hover {
                                    background-color: #4cae4c;
                                }
                                .footer {
                                    text-align: center;
                                    margin-top: 20px;
                                    color: #999999;
                                    font-size: 12px;
                                }
                                @media only screen and (max-width: 600px) {
                                    .container {
                                        max-width: 100%;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <table class="container" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="header" colspan="2">
                                        <img src="https://mam.stud.vts.su.ac.rs/images/logo.png" alt="PET Adoption Logo" style="max-width: 150px; display: block; margin: 0 auto;">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="content">
                                        <p style="font-size: 18px;">Kedves ' . $username . ',</p>
                                        <p>Köszönjük, hogy regisztráltál a PET Adoption oldalára.</p>
                                        <p>Kattints az alábbi gombra a fiókod aktiválásához:</p>
                                        <a class="button" href="' . $activation_link . '">Fiók aktiválása</a>
                                        <p style="margin-top: 20px;">A link 24 órán belül érvényes.</p>
                                        <p>Üdvözlettel,<br>PET Adoption Csapat</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="footer">
                                        <p>Ha bármilyen kérdésed van, kérlek lépj kapcsolatba velünk.</p>
                                    </td>
                                </tr>
                            </table>
                        </body>
                        </html>';

        // Email küldése és eredmény ellenőrzése
        if (sendEmail($email, $email_subject, $email_body)) {
            $message = "A regisztráció sikeres volt. Kérem ellenőrizze e-mail fiókját a fiók aktiválásához szükséges linkért.";
        } else {
            $message = "Email küldése sikertelen. Kérem próbálja újra később.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PET Adoption</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    
    <div class="register-container">
        <img src="images/logo.png" alt="PET Logo">
        <h2><i class="fas fa-user-plus"></i> Regisztráció</h2>
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>
        <form action="register.php" method="post">
            <label for="username"><i class="fas fa-user"></i> Felhasználónév:</label>
            <input type="text" name="username" id="username" required>
            <label for="email"><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" id="email" required>
            <label for="password"><i class="fas fa-lock"></i> Jelszó:</label>
            <input type="password" name="password" id="password" required>
            <label for="first_name"><i class="fas fa-user"></i> Keresztnév:</label>
            <input type="text" name="first_name" id="first_name" required>
            <label for="last_name"><i class="fas fa-user"></i> Vezetéknév:</label>
            <input type="text" name="last_name" id="last_name" required>
            <input type="submit" value="Regisztráció">
        </form>
        <p>Már van fiókja? <a href="login.php">Jelentkezzen be itt</a>.</p>
    </div>
    
</body>
</html>
