<?php
// Adatbázis kapcsolat beállítása
require_once 'db_config.php';

// Bejelentkezés kezelése
if (session_id() === '') {
    session_start();
}

// Felhasználónév meghatározása a bejelentkezett felhasználó alapján vagy "Guest" ha nincs bejelentkezve
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = 'Guest';
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>PET Adoption</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/navigation.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #FCF8ED;">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.png" alt="Állatok Örökbefogadása" style="border-radius: 10px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="animals.php">Kiskedvencek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about_us.php">Rólunk</a>
                </li>
                <li class="nav-item dropdown">
                    <?php if ($username != 'Guest') { ?>
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="favorites.php">Kedvencek</a></li>
                            <li><a class="dropdown-item" href="add_animal.php">Hirdetés feladása</a></li>
                            <li><a class="dropdown-item" href="manage_animals.php">Meghirdetett állataim</a></li>
                            <li><a class="dropdown-item" href="adoptions.php">Örökbefogadott állataim</a></li>
                            <li><a class="dropdown-item" href="messages.php">Beérkező üzenetek</a></li>
                            <li><a class="dropdown-item" href="adoption_requests.php">Beérkező örökbefogadási kérelmek</a></li>
                            <li><a class="dropdown-item" href="compose_message.php">Üzenet küldése</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="profile.php">Profil</a></li>
                            <li><a class="dropdown-item" href="logout.php">Kijelentkezés</a></li>
                        </ul>
                    <?php } else { ?>
                        <a class="nav-link" href="login.php">Bejelentkezés</a>
                    <?php } ?>
                </li>
                <li class="nav-item">
                    <!-- Keresőmező form -->
                    <form class="search-box" action="search_results.php" method="get">
                        <input class="form-control search-input" type="search" name="search" placeholder="Keresés"
                               aria-label="Keresés">
                    </form>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                         class="bi bi-search search-icon" viewBox="0 0 16 16">
                        <path
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <!-- Itt jelenítjük meg az állatokat, de most csak a keresőmező formáját módosítjuk -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
