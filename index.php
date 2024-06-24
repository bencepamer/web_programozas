<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PET Adoption</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/index.css" rel="stylesheet">
</head>

<body>
  <?php include 'navigation.php'; ?>
  <div class="container mt-5">
    <div class="row">
      <div class="col">
        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="https://images.pexels.com/photos/16168083/pexels-photo-16168083/free-photo-of-blonde-woman-sitting-on-hill-with-dogs-with-lake-behind.jpeg" class="d-block mx-auto" alt="First Slide">
              <div class="carousel-caption d-block">
                <h5>Nem számít, milyen kevés pénzed vagy vagyonod van. Ha kutyád van, gazdag vagy.</h5>
                <p>Louis Sabin</p>
              </div>
            </div>
            <div class="carousel-item">
              <img src="https://images.pexels.com/photos/16299134/pexels-photo-16299134/free-photo-of-puppies-together-on-ground.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" class="d-block mx-auto" alt="Second Slide">
              <div class="carousel-caption d-block">
                <h5>A kutyának csak egy célja van az életben: elajándékozni a szívét.</h5>
                <p>Joe Randolph Ackerley</p>
              </div>
            </div>
            <div class="carousel-item">
              <img src="https://images.pexels.com/photos/17214047/pexels-photo-17214047/free-photo-of-kittens-on-bench.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" class="d-block mx-auto" alt="Third Slide">
              <div class="carousel-caption d-block">
                <h5>Amikor rosszul érzem magam, csak a macskáimat nézem, és visszatér a boldogságom.</h5>
                <p>Charles Bukowski</p>
              </div>
            </div>
            <div class="carousel-item">
              <img src="https://images.pexels.com/photos/6342305/pexels-photo-6342305.jpeg" class="d-block mx-auto" alt="Fourth Slide">
              <div class="carousel-caption d-block">
                <h5>Egy emberben néha elhal a remény, de egy állatban soha. Amíg él, él benne a remény, és él a hűség is.</h5>
                <p>Eric Knight</p>
              </div>
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </div>
    <div class="text-center mb-3">
      <a href="animals.php" class="adopt-now-btn d-block mx-auto">
        <img src="images/adopt.png" alt="AdoptNow" class="img-fluid">
      </a>
    </div>
  </div>

  <?php include 'footer.php'; ?>


</body>
</html>
