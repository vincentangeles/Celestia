<?php
session_start();
include 'connect.php';

$organizerInfoID = $_SESSION['organizerInfoID'] ?? null;
$organizerName = 'Guest';

if (!$organizerInfoID) {
  header("Location: signIn.php");
  exit();
}

// DELETE event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event_id'])) {
  $deleteEventID = intval($_POST['delete_event_id']);
  $deleteQuery = "DELETE FROM events WHERE eventID = $deleteEventID AND organizerInfoID = $organizerInfoID";
  mysqli_query($conn, $deleteQuery);
  header("Location: " . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
  exit();
}

// Organizer Name
$organizerQuery = "SELECT organizerFirstName, organizerLastName FROM organizerinfo WHERE organizerInfoID = $organizerInfoID";
$organizerResults = executeQuery($organizerQuery);
if ($row = mysqli_fetch_assoc($organizerResults)) {
  $organizerName = $row['organizerFirstName'] . ' ' . $row['organizerLastName'];
}

// Filters
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';
$view = $_GET['view'] ?? 'all'; // 'all' or 'mine'

// Dropdown locations
$locationQuery = "SELECT DISTINCT location FROM events ORDER BY location ASC";
$locationResult = executeQuery($locationQuery);
$locations = [];
while ($row = mysqli_fetch_assoc($locationResult)) {
  $locations[] = $row['location'];
}

// Build query
$query = "SELECT e.eventID, e.title, e.startDateTime, e.location, e.posterURL,
                 e.organizerInfoID,
                 o.organizerFirstName, o.organizerLastName
          FROM events e
          JOIN organizerinfo o ON e.organizerInfoID = o.organizerInfoID";

$conditions = [];

if ($view === 'mine') {
  $conditions[] = "e.organizerInfoID = $organizerInfoID";
}

if (!empty($search)) {
  $safeSearch = mysqli_real_escape_string($conn, $search);
  $conditions[] = "(e.title LIKE '%$safeSearch%' 
                  OR e.location LIKE '%$safeSearch%' 
                  OR o.organizerFirstName LIKE '%$safeSearch%' 
                  OR o.organizerLastName LIKE '%$safeSearch%')";
}
if (!empty($location)) {
  $safeLocation = mysqli_real_escape_string($conn, $location);
  $conditions[] = "e.location = '$safeLocation'";
}

if (!empty($conditions)) {
  $query .= " WHERE " . implode(' AND ', $conditions);
}

$result = executeQuery($query);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Events Page - Organizer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/eventsorg.css">
  <link rel="icon" href="assets/img/icon.gif">
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="assets/img/icon.gif" alt="Logo" width="40" height="40" class="me-2 rounded-circle">
      </a>
      <a class="navbar-brand d-flex align-items-center" href="" style="color:#ffe1ebfa">
        <h2 class="m-1">Hi, <?= htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</h2>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"
        style="border: 2px solid #ffe1ebfa; background-color: #ffe1ebfa;">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item mx-3">
            <a class="nav-link active" aria-current="page" href="archive.php" style="color:#ffe1ebfa">Archives</a>
          </li>
          <li class="nav-item mx-3">
            <a class="nav-link active" aria-current="page" href="eventsorg.php" style="color:#ffe1ebfa">Events</a>
          </li>
          <li class="nav-item mx-3">
            <a class="nav-link active" aria-current="page" href="signIn.php" style="color:#ffe1ebfa">Sign
              Out</a>
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

  <!-- Search & Filter -->
  <div class="search-bar-wrapper d-flex align-items-center justify-content-between mt-4 px-3">
    <form method="GET" class="d-flex align-items-center w-100" id="searchForm">
      <div class="search-location d-flex align-items-center flex-grow-1">
        <input type="text" name="search" class="search-input" placeholder="Search Events / Organizer"
          value="<?= htmlspecialchars($search); ?>" />
        <input type="hidden" name="location" id="locationInput" value="<?= htmlspecialchars($location); ?>">
        <input type="hidden" name="view" id="viewInput" value="<?= htmlspecialchars($view); ?>">

        <!-- Location dropdown -->
        <div class="dropdown ms-2">
          <button id="locationDropdownBtn" class="btn btn-location dropdown-toggle" type="button"
            data-bs-toggle="dropdown">
            <i class="bi bi-geo-alt-fill me-1"></i>
            <span id="selectedLocation"><?= !empty($location) ? htmlspecialchars($location) : 'All Locations'; ?></span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item location-option" href="#" data-value="">All Locations</a></li>
            <?php foreach ($locations as $loc): ?>
              <li><a class="dropdown-item location-option" href="#" data-value="<?= htmlspecialchars($loc); ?>">
                  <?= htmlspecialchars($loc); ?>
                </a></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- View dropdown -->
        <div class="dropdown ms-2">
          <button id="viewDropdownBtn" class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <?= $view === 'mine' ? 'My Events Only' : 'All Events'; ?>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item view-option" href="#" data-view="all">All Events</a></li>
            <li><a class="dropdown-item view-option" href="#" data-view="mine">My Events Only</a></li>
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
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      <!-- Create Event -->
      <div class="col h-100 d-flex">
        <div
          class="card event-card create-event-card shadow-sm text-center h-100 w-100 d-flex flex-column justify-content-between p-3">
          <div class="d-flex flex-grow-1 justify-content-center align-items-center w-100">
            <span class="plus-icon">+</span>
          </div>
          <a href="eventForm.php" class="btn btn-edit w-100 mt-auto">Create Event</a>
        </div>
      </div>

      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col h-100 d-flex">
          <div class="card event-card shadow-sm text-center h-100 w-100 d-flex flex-column">
            <img src="<?= htmlspecialchars($row['posterURL']) ?>" alt="Event Poster" class="card-img-top">
            <div class="card-body d-flex flex-column justify-content-between flex-grow-1">
              <div>
                <h6 class="card-title text-truncate"><?= htmlspecialchars($row['title']) ?></h6>
                <p class="card-text text-muted small">
                  <?= date('M d, Y', strtotime($row['startDateTime'])) ?> · <?= htmlspecialchars($row['location']) ?>
                </p>
              </div>
              <?php if ((int) $row['organizerInfoID'] === (int) $organizerInfoID): ?>
                <div class="d-flex gap-2 mt-auto">
                  <form method="post" onsubmit="return confirm('Are you sure you want to delete this event?');"
                    class="w-100">
                    <input type="hidden" name="delete_event_id" value="<?= $row['eventID'] ?>">
                    <button type="submit" class="btn btn-delete w-100">Delete</button>
                  </form>
                  <form action="viewEvent.php" method="get" class="w-100">
                    <input type="hidden" name="eventID" value="<?= $row['eventID'] ?>">
                    <button type="submit" class="btn btn-edit w-100">Edit</button>
                  </form>
                </div>
              <?php else: ?>
                <p class="text-muted small mt-auto">Owned by another organizer</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelectorAll('.location-option').forEach(option => {
      option.addEventListener('click', function (e) {
        e.preventDefault();
        const value = this.getAttribute('data-value');
        document.getElementById('selectedLocation').textContent = value || 'All Locations';
        document.getElementById('locationInput').value = value;
        document.getElementById('searchForm').submit();
      });
    });

    document.querySelectorAll('.view-option').forEach(option => {
      option.addEventListener('click', function (e) {
        e.preventDefault();
        const view = this.getAttribute('data-view');
        document.getElementById('viewInput').value = view;
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