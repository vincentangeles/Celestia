<?php
include("connect.php");

session_start();

$organizerInfoID = $_SESSION['organizerInfoID'] ?? null;

if (!isset($_SESSION['organizerID'])) {
    header("Location: signin.php");
    exit();
}

$organizerFilter = '';
$locationFilter = '';
$order = '';
$currentDate = date(format: "Y-m-d");

if (isset($_GET['btnSubmit'])) {
    $locationFilter = $_GET['location'];
    $organizerFilter = $_GET['organizedBy'];
    $order = $_GET['order'];

}

// Query to retrieve Organizer Name
$organizerNameQuery = "SELECT * FROM organizerInfo WHERE organizerInfoID = $organizerInfoID";
$organizerNameResult = executeQuery($organizerNameQuery);
$organizerRow = mysqli_fetch_assoc($organizerNameResult);
$organizerName = $organizerRow['organizerFirstName'] . " " . $organizerRow['organizerLastName'];

$archiveEventQuery = "SELECT * FROM events 
            LEFT JOIN organizerInfo ON events.organizerInfoID = organizerInfo.organizerInfoID
            WHERE events.endDateTime < '$currentDate'";

// For Filtering based on Location AND/OR Event Organizer
if ($locationFilter != '' || $organizerFilter != '') {
    $archiveEventQuery = $archiveEventQuery . " AND";

    if ($locationFilter != '') {
        $archiveEventQuery = $archiveEventQuery . " location ='$locationFilter'";
    }

    if ($locationFilter != '' && $organizerFilter != '') {
        $archiveEventQuery = $archiveEventQuery . " AND";
    }

    if ($organizerFilter != '') {
        $archiveEventQuery = $archiveEventQuery . " organizerInfo.organizerInfoID ='$organizerFilter'";
    }
}

$archiveEventQuery = $archiveEventQuery . " ORDER BY endDateTime";

if ($order != '') {
    $archiveEventQuery = $archiveEventQuery . " $order";
}

$archiveEventResults = executeQuery($archiveEventQuery);

$locationQuery = "SELECT DISTINCT(location) FROM events ORDER BY location ASC";
$locationResults = executeQuery($locationQuery);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Organizer | Archives</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/archive.css">
    <link rel="icon" href="assets/img/icon.gif">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="assets/img/icon.gif" alt="Logo" width="40" height="40" class="me-2 rounded-circle">
            </a>
            <a class="navbar-brand d-flex align-items-center" href="" style="color:#ffe1ebfa">
                <h2 class="m-0">Hello, <?php echo $organizerName?>!</h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"
                style="border: 2px solid #ffe1ebfa; background-color: #ffe1ebfa;">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-3">
                        <a class="nav-link active" aria-current="page" href="archive.php"
                            style="color:#ffe1ebfa">Archives</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link active" aria-current="page" href="eventsorg.php"
                            style="color:#ffe1ebfa">Events</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link active" aria-current="page" href="archive.php" style="color:#ffe1ebfa">Sign
                            Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="displayTitle" style="color: #FFDCE8">Archived Events</div>
        <div class="box rounded-3" style="background-color: #FFDCE8; width: 100%; height: 15px"></div>
    </div>

    <form>
        <div class="container my-4">
            <div class="row">
                <div class="col-12">
                    <!-- Organizer Filter -->
                    <div class="row d-flex justify-content-between">
                        <div class="col-12 col-md-4 my-3">
                            <div class="form-floating filter-box">
                                <select class="form-select form-control rounded-4" id="organizedBy" name="organizedBy"
                                    style="background-color: #FFDCE8; color:#D42661; ">
                                    <option value="">All</option>
                                    <option <?php if ($organizerInfoID) {
                                        echo "selected";
                                    } ?>   value="<?php echo $organizerInfoID ?>">Only Me</option>
                                </select>
                                <label for="organizedBy">Select Organizer</label>
                            </div>
                        </div>

                        <!-- Location Filter -->
                        <div class="col-12 col-md-4 my-3">
                            <div class="form-floating filter-box">
                                <select class="form-select form-control rounded-4" id="location" name="location"
                                    style="background-color: #FFDCE8; color:#D42661; ">
                                    <option value="">Any Location</option>
                                    <?php
                                    if (mysqli_num_rows($locationResults) > 0) {
                                        while ($locationRow = mysqli_fetch_assoc($locationResults)) {
                                            ?>

                                            <option <?php if ($locationFilter == $locationRow['location']) {
                                                echo "selected";
                                            } ?>
                                                value="<?php echo $locationRow['location'] ?>">
                                                <?php echo $locationRow['location'] ?>
                                            </option>

                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="location">Select Location</label>
                            </div>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-12 col-lg-3 col-md-4 my-3">
                            <div class="form-floating filter-box">
                                <select class="form-select form-control rounded-4" id="order" name="order"
                                    style="background-color: #FFDCE8; color:#D42661; ">
                                    <option value="DESC" <?php if ($order == "DESC") {
                                        echo "selected";
                                    } ?>> Most Recent
                                    </option>
                                    <option value="ASC" <?php if ($order == "ASC") {
                                        echo "selected";
                                    } ?>>Least Recent
                                    </option>
                                </select>
                                <label for="order">Select Time</label>
                            </div>
                        </div>

                        <div class="col-12 col-lg-1 my-3">
                            <button class="btn rounded-4 form-control p-3"
                                style="background-color: #FFDCE8; color:#D42661;" name="btnSubmit">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="container">
        <div class="row">
            <div class="col">

                <!-- Card Duplication -->
                <?php
                if (mysqli_num_rows($archiveEventResults) > 0) {
                    while ($eventRow = mysqli_fetch_assoc($archiveEventResults)) {

                        $eventID = $eventRow['eventID'];

                        // Query to get number of guests in specific event
                        $guestCountQuery = "SELECT COUNT(guestID) AS Attendees FROM guests WHERE eventID = $eventID";
                        $guestCountResults = executeQuery($guestCountQuery);
                        $guestRow = mysqli_fetch_assoc($guestCountResults);
                        $guestCount = ($guestRow['Attendees'] > 0) ? $guestRow['Attendees'] . " " . "Attendees" : "None";

                        // Change format from YYYY-MM-DD to MM-DD-YYYY Format
                        $eventStartDate = $eventRow['startDateTime'];
                        $eventEndDate = $eventRow['endDateTime'];
                        $newEventStartDate = date("F d, Y", strtotime($eventStartDate));
                        $newEventEndDate = date("F d, Y", strtotime($eventEndDate));
                        ?>

                        <div class="card mb-5 rounded-5" style="max-width: 100%; background-color:#FFD4E2;">
                            <div class="row g-0">
                                <div class="col-lg-4 my-2 p-4 d-flex justify-content-center">
                                    <img src="<?php echo $eventRow['posterURL'] ?>" class="img-fluid">
                                </div>

                                <div class="col-lg-8 my-3 ">
                                    <div class="card-body">
                                        <p class="card-title"><b>Event Title: </b><?php echo $eventRow['title'] ?></p>
                                        <p class="card-title my-3"><b>Event Organizer:
                                            </b><?php echo $eventRow['organizerFirstName'] . " " . $eventRow['organizerLastName'] ?>
                                        </p>
                                        <p class="card-title my-3"><b>Event Location: </b><?php echo $eventRow['location'] ?>
                                        </p>
                                        <p class="card-title my-3"><b>Event Date: </b>
                                            <?php echo $newEventStartDate . " - " . $newEventEndDate ?></p>
                                        <p class="card-title my-3"><b>Number of Guests: </b> <?php echo $guestCount ?></p>
                                        <div class="row">
                                            <div class="col">
                                                <h5 class="card-title mb-3 "><b>Event Description</b></h5>
                                                <div class="card p-3 mx-auto"
                                                    style="background-color: #FFB1CA; max-height: 270px; overflow: scroll; text-align: justify;">
                                                    <p><?php echo $eventRow['description'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                } else {
                    // Displays when there are no Events Found
                    ?>
                    <div class="alert alert-info text-center justify-content-center my-3 rounded-4 p-5" role="alert"
                        style="background-color: #FFD4E2; color: #D42661;">
                        <h3 class="m-0">No archived events found.</3>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>

    <div class="container">
        <footer class="py-3 my-4">
            <ul class="justify-content-center border-bottom p-0 mb-3"
                style="display: flex; align-items: center; justify-content: center;">
                <img src="assets/img/logo.png" style="max-width: 600px; width: 100%; max-height: 100%;">
            </ul>
            <p class="text-center footer" style="font-size: 1.2rem; color:rgba(255, 225, 235, 0.98);">©2025 Organization</p>
        </footer>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>