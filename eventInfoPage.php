<?php
include("connect.php");

if (isset($_GET['eventID'])) {
    $eventID = $_GET['eventID'];
}

// EVENT DETAILS
$eventQuery = "
SELECT events.*, organizerinfo.organizerFirstName, organizerinfo.organizerLastName
FROM events
JOIN organizerinfo ON events.organizerInfoID = organizerinfo.organizerInfoID
WHERE eventID = $eventID
";
$eventResult = executeQuery($eventQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta property="og:title" content="<?php echo htmlspecialchars($events['title']); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url"
        content="https://github.com/vincentangeles/Celestia.git/guest/eventInfo.php?eventId=<?php echo $events['eventId']; ?>">
    <meta property="og:image"
        content="https://github.com/vincentangeles/Celestia.git/assets/img/<?php echo $events['bannerImage']; ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($events['description']); ?>">
    <meta property="og:site_name" content="Celestia">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Info Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/event-info-page.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
</head>

<body>
    <!-- NAVBAR -->
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

    <!-- MAIN CONTENT -->
    <?php
    if (mysqli_num_rows($eventResult) > 0) {
        while ($eventData = mysqli_fetch_assoc($eventResult)) {
            ?>
            <div class="main-container">
                <div class="banner-container">
                    <img src="<?php echo $eventData['bannerImage']; ?>" alt="Banner Image" class="banner-img">
                </div>

                <div class="header-container">
                    <div class="row">
                        <div class="col-lg-10 col-9">
                            <div class="event-header h-100 d-flex flex-column justify-content-center">
                                <div class="event-title"><?php echo strtoupper($eventData['title']); ?></div>
                                <div class="event-location"><i class="bi bi-geo-alt-fill"></i>
                                    <?php echo $eventData['location']; ?></div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-3 d-flex flex-column justify-content-end align-items-end">
                            <a href="attendForm.php" class="btn btn-attend-event mt-auto">ATTEND</a>
                        </div>
                    </div>
                </div>

                <hr class="line-break">

                <div class="section-container">
                    <div class="section-header">ABOUT</div>

                    <div class="section-content-container">
                        <div class="row">
                            <div class="col-lg-8 col-md-12 about-event-description">
                                <div class="row mb-3">
                                    <div class="col">
                                        <div>
                                            <strong>Organizer:</strong>
                                            <?= $eventData['organizerFirstName'] . ' ' . $eventData['organizerLastName']; ?>
                                        </div>
                                        <div>
                                            <strong>Start:</strong>
                                            <?php echo date("F j, Y", strtotime($eventData['startDateTime'])); ?>
                                        </div>
                                        <div>
                                            <strong>End:</strong>
                                            <?php echo date("F j, Y", strtotime($eventData['endDateTime'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div><?php echo $eventData['description']; ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class=" col-lg-4 col-md-12">
                                <img class="seat-plan-image" src="<?php echo $eventData['seatPlanImage']; ?>"
                                    alt="Seat Plan Image">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="line-break">

                <div class="section-container">
                    <div class="section-header">LOCATION</div>

                    <div class="section-content-container">
                        <div class="row">
                            <div class="col">
                                <div class="leaflet-map" id="map"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="line-break">

                <div class="section-container">
                    <div class="section-header">GET TICKETS</div>

                    <div class="section-content-container">
                        <div class="row">

                            <div class="col-lg-8 col-12">
                                <div class="row mb-4">
                                    <div class="col ticketing-outlet-description">
                                        Tickets are now available for this event. Click
                                        the button below to buy your tickets and secure your spot before they run out.
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col ticketing-outlet-instruction">
                                        <strong>Instructions for Online Purchase:</strong> To ensure a smooth transaction,
                                        the name on your ticketing account, payment card, and valid ID must match. Only the
                                        cardholder can claim the tickets; representatives are not allowed. By proceeding with
                                        payment, you agree to these terms. Prices include standard ticket charges.
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-12" style="padding-bottom: 1rem;">
                                <img class="ticketing-outlet" src="assets/img/ticketing-outlet.jpg">
                                <a href="<?php echo $eventData['ticketingOutletURL']; ?>" class="btn buy-tickets-available">BUY
                                    TICKETS</a>
                            </div>

                        </div>
                    </div>
                </div>

                <hr class="line-break">

                <div class="section-container">
                    <div class="section-header">SHARE THIS EVENT</div>

                    <div class="section-content-container">
                        <div class="row">
                            <div class="col-lg-4 col-12" style="padding-bottom: 1rem;">
                                <img class="event-info-qr-code"
                                    src="http://api.qrserver.com/v1/create-qr-code/?data=http://localhost/adet-grp-2/eventInfoPage.php?eventID=<?php echo $eventData['eventID']; ?>&size=500x500&margin=10">
                            </div>

                            <div class="col-lg-8 col-12">
                                <div class="row mb-4">
                                    <div class="col event-info-qr-code-title">
                                        <strong>EXCITED TO BE PART OF THIS EVENT?</strong>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col event-info-qr-code-description">
                                        Be one of the guests at <strong><?php echo strtoupper($eventData['title']); ?></strong>.
                                        Just scan the QR code and share the experience with your friends and followers. Whether
                                        it's a night to remember, a celebration worth spreading, or a moment you don't want
                                        others to miss, let them know where the action is. Sharing is just a scan away. Invite
                                        others to join the fun.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LEAFLET - LOCATION MAPPING -->
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

            <script>
                var map = L.map('map').setView([<?php echo $eventData['latitude']; ?>, <?php echo $eventData['longitude']; ?>], 15);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([<?php echo $eventData['latitude']; ?>, <?php echo $eventData['longitude']; ?>]).addTo(map)
                    .bindPopup('<?php echo $eventData['location']; ?>')
                    .openPopup();
            </script>
            <?php
        }
    }
    ?>
</body>

</html>