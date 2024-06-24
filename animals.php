<?php

// Adatbázis kapcsolat beállítása
require_once 'db_config.php';

// Szűrő paraméterek kezelése
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$breedFilter = isset($_GET['breed']) ? $_GET['breed'] : '';

// Alapértelmezett SQL lekérdezés
$sql = "SELECT a.animal_id, a.name AS animal_name, a.description, a.image, a.age, a.gender, u.username AS owner_name, c.name AS category_name, b.name AS breed_name 
        FROM animals a
        INNER JOIN users u ON a.user_id = u.user_id
        INNER JOIN categories c ON a.category_id = c.category_id
        INNER JOIN breeds b ON a.breed_id = b.breed_id
        WHERE 1=1";

// Szűrés típus alapján, ha meg van adva
if (!empty($categoryFilter)) {
    $sql .= " AND a.category_id = :category_id";
}

// Szűrés fajta alapján, ha meg van adva
if (!empty($breedFilter)) {
    $sql .= " AND a.breed_id = :breed_id";
}

$sql .= " ORDER BY a.animal_id";

// Lekérdezés végrehajtása
try {
    $stmt = $pdo->prepare($sql);

    // Bind paraméterek típus és fajta alapján
    if (!empty($categoryFilter)) {
        $stmt->bindParam(':category_id', $categoryFilter, PDO::PARAM_INT);
    }

    if (!empty($breedFilter)) {
        $stmt->bindParam(':breed_id', $breedFilter, PDO::PARAM_INT);
    }

    $stmt->execute();
    $animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hiba a lekérdezés végrehajtása során: " . $e->getMessage());
}

// Ha a like gomb meg lett nyomva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    session_start();
    
    // Ellenőrizni, hogy be van-e jelentkezve a felhasználó (például munkamenet kezeléssel)
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $animal_id = $_POST['animal_id'];

    // Ellenőrizni, hogy még nincs-e kedvencnek hozzáadva
    $check_query = "SELECT * FROM favorite_animals WHERE user_id = :user_id AND animal_id = :animal_id";
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
    $check_stmt->execute();

    if ($check_stmt->rowCount() == 0) {
        // Ha még nincs kedvencek között, hozzáadás
        $insert_query = "INSERT INTO favorite_animals (user_id, animal_id) VALUES (:user_id, :animal_id)";
        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insert_stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        
        if ($insert_stmt->execute()) {
            // Sikeres hozzáadás esetén JSON választ küldünk vissza a frontend számára
            $response = [
                'status' => 'success',
                'message' => 'Az állatot hozzáadták kedvenceihez!'
            ];
            echo json_encode($response);
            exit(); // Kilépés, hogy ne kerüljön bele a HTML kód a válaszba
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Hiba történt a kedvenc állat hozzáadásakor.'
            ];
            echo json_encode($response);
            exit();
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Ez az állat már a kedvencek között van.'
        ];
        echo json_encode($response);
        exit();
    }
}

// Ellenőrizni, hogy a felhasználó be van-e jelentkezve
session_start();
$isLoggedIn = isset($_SESSION['user_id']);

// HTML kimenet generálása
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>PET Adoption</title>
    <link href="css/animals.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
    <?php include 'navigation.php';?>
    <div class="containerList" style="margin-bottom:400px;">
        <h1>Örökbefogadható állatok</h1>

        <!-- Szűrő form -->
        <form method="GET" action="" id="filterForm">
            <label for="category">Kategória:</label>
            <select name="category" id="category">
                <option value="">Válasszon kategóriát</option>
                <?php
                // Kategóriák lekérése az adatbázisból
                $categoryQuery = "SELECT * FROM categories";
                $categoryStmt = $pdo->query($categoryQuery);
                $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($categories as $cat) {
                    $selected = ($cat['category_id'] == $categoryFilter) ? 'selected' : '';
                    echo "<option value=\"" . $cat['category_id'] . "\" $selected>" . $cat['name'] . "</option>";
                }
                ?>
            </select>

            <div id="breedContainer">
                <!-- Fajták select elem itt fog dinamikusan generálódni AJAX segítségével -->
            </div>

            <button type="submit">Szűrés</button>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var categorySelect = document.getElementById('category');
            var breedContainer = document.getElementById('breedContainer');

            categorySelect.addEventListener('change', function() {
                var categoryId = this.value;
                if (categoryId) {
                    // AJAX kérés küldése
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', 'get_breeds.php?category_id=' + categoryId, true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                // Fajták listájának frissítése
                                breedContainer.innerHTML = xhr.responseText;
                            } else {
                                console.error('AJAX hiba: ' + xhr.status);
                            }
                        }
                    };
                    xhr.send();
                } else {
                    breedContainer.innerHTML = ''; // Ha nincs kiválasztva kategória, ürítjük a fajták listáját
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toastContainer = document.getElementById('toast-container');
            const likeButtons = document.querySelectorAll('.favorite-btn');
            const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;

            likeButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!isLoggedIn) {
                        window.location.href = 'login.php';
                        return;
                    }

                    const animalId = this.parentNode.querySelector('input[name="animal_id"]').value;

                    // AJAX kérés küldése
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                // Toast üzenet megjelenítése
                                const toast = document.createElement('div');
                                toast.classList.add('toast', 'show', 'bg-success', 'text-white');
                                toast.innerHTML = '<div class="toast-header"><strong class="me-auto">Kedvenc hozzáadva</strong></div><div class="toast-body">' + response.message + '</div>';
                                toastContainer.appendChild(toast);
                                setTimeout(function() {
                                    toast.remove();
                                }, 1000);
                            } else if (response.status === 'error') {
                                // Toast üzenet megjelenítése hiba esetén
                                const toast = document.createElement('div');
                                toast.classList.add('toast', 'show', 'bg-danger', 'text-white');
                                toast.innerHTML = '<div class="toast-header"><strong class="me-auto">Hiba történt</strong></div><div class="toast-body">' + response.message + '</div>';
                                toastContainer.appendChild(toast);
                                setTimeout(function() {
                                    toast.remove();
                                }, 1000);
                            }
                        }
                    };

                    xhr.send('like=true&animal_id=' + encodeURIComponent(animalId));
                });
            });
        });

        </script>

        <!-- Állatok listázása -->
<ul>
    <?php foreach ($animals as $animal): ?>
        <li>
            <?php if (!empty($animal['image'])): ?>
                <img class="photo" src="<?php echo htmlspecialchars($animal['image']); ?>" class="card-img-top" alt="...">
            <?php else: ?>
                <p>Kép nem elérhető</p>
            <?php endif; ?>
            <h2><?= htmlspecialchars($animal['animal_name']) ?></h2>
            
            <p>Kor: <?= htmlspecialchars($animal['age']) ?> év</p>
            <p>Nem: <?= htmlspecialchars($animal['gender']) ?></p>
            <p>Leírás: <?= htmlspecialchars($animal['description']) ?></p>

            <!-- Hirdető megjelenítése -->
            <p><strong>Hirdető: <?= htmlspecialchars($animal['owner_name']) ?></strong></p>

            <!-- Kedvenc gomb form -->
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="animal_id" value="<?php echo htmlspecialchars($animal['animal_id']); ?>">
                <button type="submit" name="like" class="favorite-btn">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                        <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                    </svg>
                    Kedvenc
                </button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

    </div>
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>
    <?php include 'footer.php';?>
</body>
</html>
