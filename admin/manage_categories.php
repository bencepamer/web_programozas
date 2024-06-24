<?php
require_once '../db_config.php'; // Adatbázis kapcsolat inicializálása
include 'admin_navbar.php'; // Adminisztrációs navigációs sáv include-olása

// POST kérések kezelése
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kategória hozzáadása
    if (isset($_POST['add_category'])) {
        $category_name = $_POST['category_name'];
        
        $insert_sql = "INSERT INTO categories (name) VALUES (?)";
        $insert_stmt = $pdo->prepare($insert_sql);
        $insert_stmt->execute([$category_name]);
        
        // Visszajelzés küldése JSON formátumban
        echo json_encode(['status' => 'success', 'message' => 'Kategória sikeresen hozzáadva.']);
        exit;
    }

    // Kategória törlése
    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        
        // Ellenőrizzük, hogy nincs-e olyan fajta, amely ehhez a kategóriához tartozik
        $check_breeds_sql = "SELECT * FROM breeds WHERE category_id=?";
        $check_breeds_stmt = $pdo->prepare($check_breeds_sql);
        $check_breeds_stmt->execute([$category_id]);
        $count = $check_breeds_stmt->rowCount();
        
        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Nem lehet törölni a kategóriát, mert hozzá vannak rendelve fajták.']);
        } else {
            $delete_sql = "DELETE FROM categories WHERE category_id=?";
            $delete_stmt = $pdo->prepare($delete_sql);
            if ($delete_stmt->execute([$category_id])) {
                // Visszajelzés küldése JSON formátumban
                echo json_encode(['status' => 'success', 'message' => 'Kategória sikeresen törölve.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Kategória törlése sikertelen.']);
            }
        }
        exit;
    }
}

// Összes kategória lekérdezése
$sql = "SELECT * FROM categories";
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PET Adoption</title>
    <link href="css/manage_categories.css" rel="stylesheet"> <!-- Egyedi stíluslap betöltése -->
    <script src="js/manage_categories.js"></script> <!-- Egyedi JavaScript fájl betöltése -->
</head>
<body>
<div class="manage-categories">
    <h2>Kategóriák kezelése</h2>

    <!-- Kategória hozzáadása űrlap -->
    <form id="add-category-form" method="POST">
        <input type="text" id="category_name" name="category_name" required>
        <button type="submit" name="add_category">Kategória hozzáadása</button>
    </form>

    <!-- Kategóriák listázása és törlése -->
    <ul>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
            <li>
                <?php echo htmlspecialchars($row['name']); ?>
                <form class="delete-category-form" method="POST">
                    <input type="hidden" name="category_id" value="<?php echo $row['category_id']; ?>">
                    <button type="submit" name="delete_category" class="delete-btn">Kategória törlése</button>
                </form>
            </li>
        <?php } ?>
    </ul>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>

<?php
$pdo = null; // Adatbázis kapcsolat lezárása
?>
