<?php
require_once '../db_config.php'; // Adatbázis konfigurációs fájl betöltése
include 'admin_navbar.php'; // Admin navigációs sáv inicializálása

// POST kérések kezelése
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrizze, hogy aszinkron kérés érkezett-e (AJAX kérés)
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'edit_animal':
                    editAnimal($pdo); // Állat szerkesztése funkció
                    break;

                case 'delete_animal':
                    deleteAnimal($pdo); // Állat törlése funkció
                    break;

                case 'filter_animals':
                    filterAnimals($pdo); // Állatok szűrése funkció
                    break;

                default:
                    echo json_encode(["status" => "error", "message" => "Invalid action"]); // Érvénytelen művelet esetén hibaüzenet
                    break;
            }
            exit; // AJAX kérés kezelésének befejezése
        }
    }
}

// Állatok lekérése az adatbázisból
$animals = fetchAnimals($pdo);

// Kategóriák lekérése szűréshez
$categories = fetchCategories($pdo);

// Fajták lekérése szűréshez
$breeds = fetchBreeds($pdo);

// Függvények definíciója

// Állat szerkesztése
function editAnimal($pdo) {
    if (isset($_POST['animal_id'], $_POST['name'], $_POST['description'], $_POST['age'], $_POST['gender'])) {
        $animal_id = $_POST['animal_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];

        $sql = "UPDATE animals SET name=?, description=?, age=?, gender=? WHERE animal_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $age, $gender, $animal_id]);

        echo json_encode(["status" => "success", "message" => "Állat szerkesztése sikeres volt."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Hiányzó paraméterek"]);
    }
}

// Állat törlése
function deleteAnimal($pdo) {
    if (isset($_POST['animal_id'])) {
        $animal_id = $_POST['animal_id'];

        $sql = "DELETE FROM animals WHERE animal_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$animal_id]);

        echo json_encode(["status" => "success", "message" => "Állat sikeresen törölve."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Hiányzó animal_id"]);
    }
}

// Állatok szűrése
function filterAnimals($pdo) {
    if (isset($_POST['category_id'], $_POST['breed_id'])) {
        $category_id = $_POST['category_id'];
        $breed_id = $_POST['breed_id'];

        $sql = "SELECT animals.*, users.username AS advertiser 
                FROM animals 
                JOIN users ON animals.user_id = users.user_id 
                WHERE (category_id = ? OR ? = 0) AND (breed_id = ? OR ? = 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category_id, $category_id, $breed_id, $breed_id]);

        $animals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($animals);
    } else {
        echo json_encode(["status" => "error", "message" => "Hiányzó paraméterek"]);
    }
}

// Állatok lekérése
function fetchAnimals($pdo) {
    $sql = "SELECT animals.*, users.username AS advertiser 
            FROM animals 
            JOIN users ON animals.user_id = users.user_id";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Kategóriák lekérése
function fetchCategories($pdo) {
    $categories_sql = "SELECT * FROM categories";
    $categories_stmt = $pdo->query($categories_sql);
    return $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fajták lekérése
function fetchBreeds($pdo) {
    $breeds_sql = "SELECT * FROM breeds";
    $breeds_stmt = $pdo->query($breeds_sql);
    return $breeds_stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pdo = null; // Adatbázis kapcsolat bezárása
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <title>PET Adoption</title>

    <link href="css/manage_animals.css" rel="stylesheet"> <!-- Stíluslap betöltése -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- jQuery betöltése -->
    
    <script src="js/manage_animals.js"></script> <!-- JavaScript fájl betöltése -->

</head>
<body>
    <div class="container">
        <h2>Állatok kezelése</h2>

        <!-- Szűrési opciók -->
        <div class="form-group">
            <label for="category_filter">Kategória:</label>
            <select id="category_filter" onchange="filterAnimals()">
                <option value="0">Mind</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="breed_filter">Fajta:</label>
            <select id="breed_filter" onchange="filterAnimals()">
                <option value="0">Mind</option>
                <?php foreach ($breeds as $breed) { ?>
                    <option value="<?php echo $breed['breed_id']; ?>"><?php echo $breed['name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <!-- Táblázat az állatok listázásához -->
        <table>
            <thead>
                <tr>
                    <th>Állat ID</th>
                    <th>Név</th>
                    <th>Leírás</th>
                    <th>Kor</th>
                    <th>Nem</th>
                    <th>Kép</th>
                    <th>Hirdető</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animals as $animal) { ?>
                    <tr id='row_<?php echo $animal['animal_id']; ?>'>
                        <td><?php echo $animal['animal_id']; ?></td>
                        <td><input type='text' id='name_<?php echo $animal['animal_id']; ?>' value='<?php echo $animal['name']; ?>' required></td>
                        <td><textarea id='description_<?php echo $animal['animal_id']; ?>' required><?php echo $animal['description']; ?></textarea></td>
                        <td><input type='number' id='age_<?php echo $animal['animal_id']; ?>' value='<?php echo $animal['age']; ?>' required></td>
                        <td>
                            <select id='gender_<?php echo $animal['animal_id']; ?>' required>
                                <option value='Male' <?php echo ($animal['gender'] == 'Male') ? 'selected' : ''; ?>>Hím</option>
                                <option value='Female' <?php echo ($animal['gender'] == 'Female') ? 'selected' : ''; ?>>Nőstény</option>
                                <option value='Unknown' <?php echo ($animal['gender'] == 'Unknown') ? 'selected' : ''; ?>>Ismeretlen</option>
                            </select>
                        </td>
                        <td><img src='../<?php echo $animal['image']; ?>' alt='Állat képe' class='animal-image'></td>
                        <td><?php echo $animal['advertiser']; ?></td>
                        <td>
                            <button onclick='editAnimal(<?php echo $animal['animal_id']; ?>)'>Szerkesztés</button>
                            <button onclick='deleteAnimal(<?php echo $animal['animal_id']; ?>)'>Törlés</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Toast üzenetek -->
    <div id="toast-container"></div>
</body>
</html>
