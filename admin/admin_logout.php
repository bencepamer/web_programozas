<?php
session_start();
session_destroy(); // Munkamenet lezárása, így kijelentkezetté válik a felhasználó.
header("Location: admin_login.php"); // Átirányítás a bejelentkezési oldalra.
exit();
?>
