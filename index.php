<?php
include('connect.php');

$search = isset($_GET['search']) ? $_GET['search'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';

$today = date('Y-m-d');

$locationQuery = "SELECT DISTINCT location FROM events ORDER BY location ASC";
$locationResult = executeQuery($locationQuery);
$locations = [];
while ($row = mysqli_fetch_assoc($locationResult)) {
    $locations[] = $row['location'];
}

$query = "SELECT e.eventID, e.title, e.startDateTime, e.location, e.posterURL,
                 o.organizerFirstName, o.organizerLastName
          FROM events e
          JOIN organizerinfo o ON e.organizerInfoID = o.organizerInfoID
          WHERE DATE(e.startDateTime) >= '$today'";

$conditions = [];
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $conditions[] = "(e.title LIKE '%$search%' 
                  OR e.location LIKE '%$search%' 
                  OR o.organizerFirstName LIKE '%$search%' 
                  OR o.organizerLastName LIKE '%$search%')";
}
if (!empty($location)) {
    $location = mysqli_real_escape_string($conn, $location);
    $conditions[] = "e.location = '$location'";
}

if (!empty($conditions)) {
    $query .= " AND " . implode(' AND ', $conditions);
}

$query .= " ORDER BY e.startDateTime ASC";

$result = executeQuery($query);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events Page - Guest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="icon" href="assets/img/icon.gif">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-primary sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/img/icon.gif" alt="Logo" width="40" height="40" class="me-2 rounded-circle">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"
                style="border: 2px solid #ffe1ebfa; background-color: #ffe1ebfa;">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-3">
                        <a class="nav-link active" aria-current="page" href="signIn.php" style="color:#ffe1ebfa">Sign
                            In</a>
                    </li>
                </ul>
            </div>
        </div>
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
        <form method="GET" class="d-flex align-items-center w-100" id="searchForm">
            <div class="search-location d-flex align-items-center flex-grow-1">
                <input type="text" name="search" class="search-input me-2" placeholder="Search Events / Organizer"
                    value="<?php echo htmlspecialchars($search); ?>" />
                <input type="hidden" name="location" id="locationInput"
                    value="<?php echo htmlspecialchars($location); ?>">
                <div class="dropdown ms-2">
                    <button id="locationDropdownBtn" class="btn btn-location dropdown-toggle" type="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-geo-alt-fill me-1"></i>
                        <span
                            id="selectedLocation"><?php echo !empty($location) ? htmlspecialchars($location) : 'All Locations'; ?></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item location-option" href="#" data-value="">All Locations</a></li>
                        <?php foreach ($locations as $loc): ?>
                            <li><a class="dropdown-item location-option" href="#"
                                    data-value="<?php echo htmlspecialchars($loc); ?>">
                                    <?php echo htmlspecialchars($loc); ?>
                                </a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="submit" class="btn ms-2">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Events Grid -->
    <div class="container mt-4">
        <?php if (mysqli_num_rows($result) === 0): ?>
            <div class="text-center text-muted mt-5">No upcoming events found.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col h-100 d-flex">
                        <div class="card event-card shadow-sm text-center h-100 w-100 d-flex flex-column">
                            <img src="<?= htmlspecialchars($row['posterURL']) ?>" alt="Event Poster" class="card-img-top"
                                loading="lazy">
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
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.location-option').forEach(option => {
            option.addEventListener('click', function (e) {
                e.preventDefault();
                const value = this.getAttribute('data-value');
                const text = value || 'All Locations';
                document.getElementById('selectedLocation').textContent = text;
                document.getElementById('locationInput').value = value;
                document.getElementById('searchForm').submit();
            });
        });

        document.querySelector('.search-input').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchForm').submit();
            }
        });
    </script>
</body>

</html>