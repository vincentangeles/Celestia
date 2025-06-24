<?php
include('connect.php');

$query = "SELECT eventID, title, startDateTime, location, posterURL FROM events";
$result = executeQuery($query);
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events Page - Guest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/events-page-guest.css">
</head>

<body>

    <nav class="navbar">
        <span class="navbar-text ms-auto">
            <span class="ms-2">Hi, John Doe!</span>
            <a href="#">
                <img src="assets/img/login-icon.png" alt="Profile" class="profile-pic">
            </a>
        </span>
    </nav>

    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/img/carousel1.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="assets/img/carousel2.jpg" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="assets/img/carousel3.jpg" class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="search-bar-wrapper d-flex align-items-center justify-content-between mt-4 px-3">
        <div class="search-location d-flex align-items-center">
            <input type="text" class="search-input" placeholder="Search Events / Organizer" />
            <div class="dropdown ms-2">
                <button id="locationDropdownBtn" class="btn btn-location dropdown-toggle" type="button"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-geo-alt-fill me-1"></i> <span id="selectedLocation">Manila</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item location-option" href="#" data-value="Cebu">Cebu</a></li>
                    <li><a class="dropdown-item location-option" href="#" data-value="Davao">Davao</a></li>
                    <li><a class="dropdown-item location-option" href="#" data-value="Makati">Makati</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="container mt-4">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col h-100 d-flex">
                    <div class="card event-card shadow-sm text-center h-100 w-100 d-flex flex-column">
                        <img src="<?= $row['posterURL'] ?>" alt="Event Poster" class="card-img-top">
                        <div class="card-body d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h6 class="card-title text-truncate"><?= htmlspecialchars($row['title']) ?></h6>
                                <p class="card-text text-muted small">
                                    <?= date('M d, Y', strtotime($row['startDateTime'])) ?> ·
                                    <?= htmlspecialchars($row['location']) ?>
                                </p>
                            </div>
                            <a href="eventInfoPage.php?eventID=<?= $row['eventID'] ?>"
                                class="btn w-100 attend-btn mt-auto">Attend</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>