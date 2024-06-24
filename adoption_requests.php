<?php
// adoption_requests.php

// Adatbázis kapcsolat beállítása
require_once 'db_config.php';
session_start();

// Ellenőrizni kell, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    // Ha a felhasználó nincs bejelentkezve, átirányítjuk a bejelentkezési oldalra
    header("Location: login.php");
    exit();
}

// Bejelentkezett felhasználó ID-jének lekérése
$user_id = $_SESSION['user_id'];

// AJAX kérés kezelése az örökbefogadási kérelem elfogadására vagy elutasítására
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'reject_request' && isset($_POST['request_id'])) {
        // Örökbefogadási kérelem elutasítása
        $request_id = $_POST['request_id'];

        try {
            // Ellenőrizzük, hogy a kérelem létezik és megfelelő-e az adatbázisban
            $stmt_check_request = $pdo->prepare("SELECT * FROM adoption_requests WHERE request_id = :request_id");
            $stmt_check_request->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $stmt_check_request->execute();
            $request = $stmt_check_request->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                $response = ['success' => false, 'message' => 'Hibás örökbefogadási kérelem azonosító'];
                echo json_encode($response);
                exit();
            }

            // Töröljük az adoption_requests táblából az örökbefogadási kérelmet
            $stmt_delete_request = $pdo->prepare("DELETE FROM adoption_requests WHERE request_id = :request_id");
            $stmt_delete_request->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $stmt_delete_request->execute();

            $response = ['success' => true, 'message' => 'Örökbefogadási kérelem sikeresen elutasítva'];
            echo json_encode($response);
            exit();
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Hiba az adatbázis művelet során: ' . $e->getMessage()];
            echo json_encode($response);
            exit();
        }
    } elseif ($_POST['action'] == 'accept_request' && isset($_POST['request_id'])) {
        // Örökbefogadási kérelem elfogadása
        $request_id = $_POST['request_id'];

        try {
            // Ellenőrizzük, hogy a kérelem létezik és megfelelő-e az adatbázisban
            $stmt_check_request = $pdo->prepare("SELECT * FROM adoption_requests WHERE request_id = :request_id");
            $stmt_check_request->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $stmt_check_request->execute();
            $request = $stmt_check_request->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                $response = ['success' => false, 'message' => 'Hibás örökbefogadási kérelem azonosító'];
                echo json_encode($response);
                exit();
            }

            // Átmozgatjuk az adatokat az adoptions táblába
            $animal_id = $request['animal_id'];
            $request_date = $request['request_date'];

            // Először töröljük az adoption_requests táblából
            $stmt_delete_request = $pdo->prepare("DELETE FROM adoption_requests WHERE request_id = :request_id");
            $stmt_delete_request->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $stmt_delete_request->execute();

            // Végül beszúrjuk az adoptions táblába az új rekordot
            $stmt_insert_adoption = $pdo->prepare("INSERT INTO adoptions (user_id, animal_id, adoption_date) VALUES (:user_id, :animal_id, :adoption_date)");
            $stmt_insert_adoption->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_insert_adoption->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
            $stmt_insert_adoption->bindParam(':adoption_date', $request_date, PDO::PARAM_STR);
            $stmt_insert_adoption->execute();

            // Sikeres válasz visszaküldése AJAX kérésre
            $response = ['success' => true, 'message' => 'Örökbefogadás sikeresen megtörtént'];
            echo json_encode($response);
            exit();
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Hiba az adatbázis művelet során: ' . $e->getMessage()];
            echo json_encode($response);
            exit();
        }
    }
}

// Adatbázis lekérdezés az örökbefogadási kérelmek lekéréséhez
try {
    // PDO objektum létrehozása már inicializált $pdo változóval
    $stmt = $pdo->prepare("SELECT adoption_requests.request_id, adoption_requests.user_id AS requester_id, adoption_requests.animal_id, adoption_requests.request_date,
                           animals.name AS animal_name, animals.description, animals.age, animals.image, users.username AS requester_name
                           FROM adoption_requests 
                           INNER JOIN animals ON adoption_requests.animal_id = animals.animal_id 
                           INNER JOIN users ON adoption_requests.user_id = users.user_id
                           WHERE animals.user_id = :user_id");

    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $adoption_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hiba az adatbázis lekérdezés során: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Örökbefogadási Kérelmek</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/adoption_requests.css" rel="stylesheet">
  <script src="js/adoption_requests.js"></script>
</head>
<body>

<?php include 'navigation.php'; ?>

<div class="container mt-4" style="margin-bottom: 600px;">
  <div class="row justify-content-center">
    <?php if (!empty($adoption_requests)): ?>
      <?php foreach ($adoption_requests as $request): ?>
        <div class="col-md-6 mb-4" id="request-card-<?php echo $request['request_id']; ?>">
          <div class="card">
            <img src="<?php echo htmlspecialchars($request['image']); ?>" class="card-img-top photo" alt="<?php echo htmlspecialchars($request['animal_name']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($request['animal_name']); ?></h5>
              <p class="card-text"><?php echo htmlspecialchars($request['description']); ?></p>
              <p class="card-text">Életkor: <?php echo htmlspecialchars($request['age']); ?> év</p>
              <p class="card-text"><strong>Kérelmező: <?php echo htmlspecialchars($request['requester_name']); ?></strong></p>
              <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-reject me-2" onclick="rejectRequest(<?php echo $request['request_id']; ?>)">
                  Elutasítás
                </button>
                <button type="button" class="btn btn-accept" onclick="acceptRequest(<?php echo $request['request_id']; ?>)">
                  Elfogadás
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

    <?php else: ?>
      <div class="col-12 text-center">
        <p><h2>Nincsenek beérkező örökbefogadási kérelmek.<h2></p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>


</body>
</html>
