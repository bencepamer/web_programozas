<?php
// Az 'db_config.php' fájl importálása, amelyben a $pdo PDO objektum van inicializálva.
require_once 'db_config.php';

// Ellenőrizzük, hogy van-e 'category_id' nevű GET paraméter küldve a kérésben.
if (isset($_GET['category_id'])) {
    // A 'category_id' GET paraméter értékének eltárolása.
    $categoryFilter = $_GET['category_id'];

    // SQL lekérdezés összeállítása, amely kiválasztja az adott kategóriához tartozó fajtákat.
    $breedQuery = "SELECT * FROM breeds WHERE category_id = :category_id";

    // Lekérdezés előkészítése a PDO objektumon keresztül.
    $breedStmt = $pdo->prepare($breedQuery);

    // A lekérdezés végrehajtása a megfelelő paraméterekkel (category_id).
    $breedStmt->execute(['category_id' => $categoryFilter]);

    // Az eredményhalmaz lekérése, és asszociatív tömbök formájában tárolása.
    $breeds = $breedStmt->fetchAll(PDO::FETCH_ASSOC);

    // HTML kód generálása a kiválasztó mezőhöz (dropdown lista) a fajták kiválasztásához.
    echo '<label for="breed">Fajta:</label>';
    echo '<select name="breed" id="breed">';
    echo '<option value="">Válasszon fajtát</option>';

    // Végigmegyünk az összes fajtán és generálunk egy option-t mindegyikhez.
    foreach ($breeds as $breed) {
        // Ellenőrizzük, hogy az adott fajta van-e előzőleg kiválasztva (selected).
        // Ha igen, akkor a 'selected' attribútumot adjuk hozzá az option-höz.
        $selected = ($breed['breed_id'] == $breedFilter) ? 'selected' : '';
        
        // Option elem létrehozása, amely tartalmazza a fajta nevét és értékét.
        echo "<option value=\"" . $breed['breed_id'] . "\" $selected>" . $breed['name'] . "</option>";
    }

    // A select elem lezárása.
    echo '</select>';
}
?>
