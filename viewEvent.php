<?php
include("connect.php");

session_start();

if (!isset($_SESSION['organizerID'])) {
    header("Location: signin.php");
    exit();
}

$eventID = $_GET['eventID'];
// Upload function
function uploadImage($file, $directory)
{
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    $fileName = time() . '_' . basename($file['name']);
    $uploadPath = $directory . $fileName;

    return move_uploaded_file($file['tmp_name'], $uploadPath) ? $uploadPath : '';
}

if (isset($_POST['btnEdit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
    $startDateTime = $_POST['startDate'] . ' ' . $_POST['startTime'];
    $endDateTime = $_POST['endDate'] . ' ' . $_POST['endTime'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $ticketingOutletURL = mysqli_real_escape_string($conn, $_POST['ticketingOutletURL']);

    // Handle image uploads
    $posterURL = $_POST['currentPoster'];
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === 0) {
        $posterURL = uploadImage($_FILES['poster'], 'assets/img/posters/');
    }

    $bannerURL = $_POST['currentBanner'];
    if (isset($_FILES['bannerImage']) && $_FILES['bannerImage']['error'] === 0) {
        $bannerURL = uploadImage($_FILES['bannerImage'], 'assets/img/banners/');
    }

    $seatPlanURL = $_POST['currentSeatPlan'];
    if (isset($_FILES['seatPlanImage']) && $_FILES['seatPlanImage']['error'] === 0) {
        $seatPlanURL = uploadImage($_FILES['seatPlanImage'], 'assets/img/seatplans/');
    }

    $updateEventQuery = "UPDATE events SET 
                        title='$title', 
                        description='$description', 
                        location='$location',
                        latitude='$latitude',
                        longitude='$longitude',
                        startDateTime='$startDateTime',
                        endDateTime='$endDateTime',
                        posterURL='$posterURL',
                        bannerImage='$bannerURL',
                        seatPlanImage='$seatPlanURL',
                        ticketingOutletURL='$ticketingOutletURL'
                        WHERE eventID='$eventID'";
    
    executeQuery($updateEventQuery);
    
    // Update organizer email if needed
    $updateOrganizerQuery = "UPDATE organizers SET email='$email' WHERE organizerID = (SELECT organizerInfoID FROM events WHERE eventID='$eventID')";
    executeQuery($updateOrganizerQuery);

    header("Location: eventsorg.php");
}

$query = "SELECT e.*, o.email FROM events e LEFT JOIN organizers o ON e.organizerInfoID = o.organizerID WHERE e.eventID='$eventID'";
$results = executeQuery($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="icon" href="assets/img/icon.gif">
    <link rel="stylesheet" href="assets/css/eventForm.css">
</head>

<body>
    <?php
    if (mysqli_num_rows($results) > 0) {
        while ($event = mysqli_fetch_assoc($results)) {
            // Split datetime for form fields
            $startDate = date('Y-m-d', strtotime($event['startDateTime']));
            $startTime = date('H:i', strtotime($event['startDateTime']));
            $endDate = date('Y-m-d', strtotime($event['endDateTime']));
            $endTime = date('H:i', strtotime($event['endDateTime']));
    ?>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg rounded-4 overflow-hidden mt-4 mb-4">
                    <div class="text-white text-center py-4 px-3" style="background: var(--primary-color);">
                        <h2 class="mb-2 fw-bold" style="font-size: var(--heading); font-family: var(--primaryFont);">
                            Edit Event</h2>
                        <p class="mb-0 opacity-75" style="font-size: var(--lead); font-family: var(--primaryFont);">Update the details below to modify your event</p>
                    </div>

                    <!-- Form Section -->
                    <div class="card-body p-4" style="background-color: #f5f5f5;">
                        <form id="eventForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="currentPoster" value="<?php echo htmlspecialchars($event['posterURL']); ?>">
                            <input type="hidden" name="currentBanner" value="<?php echo htmlspecialchars($event['bannerImage']); ?>">
                            <input type="hidden" name="currentSeatPlan" value="<?php echo htmlspecialchars($event['seatPlanImage']); ?>">
                            
                            <div class="container-fluid p-0">

                                <!-- Banner Upload -->
                                <div class="position-relative">
                                    <?php if (!empty($event['bannerImage'])): ?>
                                        <img id="bannerPreview" src="<?php echo htmlspecialchars($event['bannerImage']); ?>" alt="Banner Image" class="w-100"
                                            style="max-height: 250px; object-fit: cover;">
                                    <?php else: ?>
                                        <img id="bannerPreview" src="#" alt="Banner Image" class="w-100 d-none"
                                            style="max-height: 250px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="p-2 bg-light">
                                        <label for="bannerImage" class="form-label fw-semibold"
                                            style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                            Update Banner Image
                                        </label>
                                        <input class="form-control rounded-3" type="file" name="bannerImage"
                                            id="bannerImage" accept="image/*" onchange="previewBanner()">
                                        <small class="text-muted">Leave empty to keep current banner</small>
                                    </div>
                                </div>

                                <!-- Title and Poster -->
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold"
                                            style="color: var(--text-color-dark); font-family: var(--primaryFont);">
                                            Event Title <span style="color: var(--primary-color);">*</span>
                                        </label>
                                        <input type="text" class="form-control rounded-3" name="title"
                                            style="background-color: #e0e0e0;" placeholder="Enter title" 
                                            value="<?php echo htmlspecialchars($event['title']); ?>" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold"
                                            style="color: var(--text-color-dark); font-family: var(--primaryFont);">
                                            Event Poster
                                        </label>
                                        <input type="file" name="poster" accept="image/*" class="form-control rounded-3"
                                            style="background-color: #e0e0e0;">
                                        <?php if (!empty($event['posterURL'])): ?>
                                            <small class="text-muted">Current: <?php echo basename($event['posterURL']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Seat Plan -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold"
                                        style="font-family: var(--primaryFont); color: var(--text-color-dark);">Seat
                                        Plan Image</label>
                                    <input type="file" class="form-control rounded-3" name="seatPlanImage"
                                        id="seatPlanImage" accept="image/*" onchange="showSeatPlanName()"
                                        style="background-color: #e0e0e0;">
                                    <div id="seatPlanName" class="mt-1 text-muted"
                                        style="font-size: 0.9rem; font-family: var(--primaryFont);">
                                        <?php if (!empty($event['seatPlanImage'])): ?>
                                            Current: <?php echo basename($event['seatPlanImage']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold"
                                            style="color: var(--text-color-dark); font-family: var(--primaryFont);">
                                            Description <span style="color: var(--primary-color);">*</span>
                                        </label>
                                        <textarea class="form-control rounded-3" style="background-color: #e0e0e0;"
                                            name="description" rows="3" placeholder="Describe event"
                                            required><?php echo htmlspecialchars($event['description']); ?></textarea>
                                    </div>
                                </div>

                                <!-- Ticketing Outlet URL -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold"
                                        style="font-family: var(--primaryFont); color: var(--text-color-dark);">Ticketing
                                        Outlet URL</label>
                                    <input type="url" class="form-control rounded-3" name="ticketingOutletURL"
                                        placeholder="https://example.com/ticket" style="background-color: #e0e0e0;"
                                        value="<?php echo htmlspecialchars($event['ticketingOutletURL']); ?>">
                                </div>

                                <!-- Date & Time -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold"
                                            style="color: var(--text-color-dark); font-family: var(--primaryFont);">
                                            Start Date & Time <span style="color: var(--primary-color);">*</span>
                                        </label>
                                        <div class="row g-2">
                                            <div class="col-7">
                                                <input type="date" class="form-control rounded-3" name="startDate"
                                                    style="background-color: #e0e0e0;" value="<?php echo $startDate; ?>" required>
                                            </div>
                                            <div class="col-5">
                                                <input type="time" class="form-control rounded-3" name="startTime"
                                                    style="background-color: #e0e0e0;" value="<?php echo $startTime; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold"
                                            style="color: var(--text-color-dark); font-family: var(--primaryFont);">
                                            End Date & Time <span style="color: var(--primary-color);">*</span>
                                        </label>
                                        <div class="row g-2">
                                            <div class="col-7">
                                                <input type="date" class="form-control rounded-3" name="endDate"
                                                    style="background-color: #e0e0e0;" value="<?php echo $endDate; ?>" required>
                                            </div>
                                            <div class="col-5">
                                                <input type="time" class="form-control rounded-3" name="endTime"
                                                    style="background-color: #e0e0e0;" value="<?php echo $endTime; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Section -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card rounded-4 bg-white">
                                            <div class="card-body p-4">
                                                <h4 class="text-center mb-4 fw-bold"
                                                    style="font-size: var(--subheading); color: var(--text-color-dark); font-family: var(--primaryFont);">
                                                    Event Location
                                                </h4>

                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <label class="form-label fw-semibold"
                                                            style="color: var(--text-color-dark); font-family: var(--primaryFont);">
                                                            Venue Name <span
                                                                style="color: var(--primary-color);">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" id="locationInput" name="location"
                                                                class="form-control rounded-start-3"
                                                                style="background-color: #e0e0e0;"
                                                                placeholder="Enter full address" 
                                                                value="<?php echo htmlspecialchars($event['location']); ?>" required
                                                                onkeydown="if(event.key === 'Enter') { event.preventDefault(); searchLocation(); }">
                                                            <button type="button"
                                                                class="btn text-white rounded-end-3 px-3"
                                                                style="background: var(--primary-color); font-family: var(--primaryFont);"
                                                                onclick="searchLocation()">
                                                                <i class="fas fa-map-marker-alt me-1"></i>Pin location
                                                            </button>
                                                            <input type="hidden" name="latitude" id="latitude" value="<?php echo htmlspecialchars($event['latitude']); ?>">
                                                            <input type="hidden" name="longitude" id="longitude" value="<?php echo htmlspecialchars($event['longitude']); ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <div
                                                            class="border border-2 border-secondary rounded-3 overflow-hidden">
                                                            <div id="map" style="width: 100%; height: 400px;"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Email Section -->
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <div class="card rounded-4"
                                                            style="background-color: #fafafa; border: 1px solid var(--primary-color);">
                                                            <div class="card-body p-3">
                                                                <label class="form-label fw-semibold"
                                                                    style="color: var(--primary-color); font-family: var(--primaryFont);">
                                                                    Email Address (Required)
                                                                </label>
                                                                <input type="email" name="email"
                                                                    class="form-control bg-white rounded-3"
                                                                    style="border: 2px solid var(--tertiary-color);"
                                                                    placeholder="What's your email address?" 
                                                                    value="<?php echo htmlspecialchars($event['email']); ?>" required>
                                                                <small
                                                                    style="color: var(--text-color-gray); font-family: var(--primaryFont);">*This
                                                                    question is required</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button type="button" class="btn btn-secondary rounded-3 px-4"
                                                style="font-family: var(--primaryFont);"
                                                onclick="window.location.href='eventsorg.php'">Cancel</button>
                                            <button type="submit" class="btn text-white rounded-3 px-4"
                                                name="btnEdit"
                                                style="background: var(--primary-color); font-family: var(--primaryFont);">Update
                                                Event</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        }
    } 
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // MAP FUNCTIONALITY
        let map;
        let marker;

        document.addEventListener("DOMContentLoaded", function () {
            map = L.map('map');

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors &copy; <a href="https://carto.com/">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);

            // Set existing location if available
            const lat = document.getElementById("latitude").value;
            const lon = document.getElementById("longitude").value;
            
            if (lat && lon) {
                const customIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });
                
                marker = L.marker([lat, lon], { icon: customIcon }).addTo(map);
                map.setView([lat, lon], 18);
            } else {
                // Default to view entire Philippines
                const philippinesBounds = [
                    [4.5, 116.0],
                    [21.0, 127.0]
                ];
                map.fitBounds(philippinesBounds);
            }
        });

        function searchLocation() {
            const query = document.getElementById("locationInput").value;
            if (!query) return;

            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        const displayName = data[0].display_name;

                        const customIcon = L.icon({
                            iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                            iconSize: [32, 32],
                            iconAnchor: [16, 32],
                            popupAnchor: [0, -32]
                        });

                        if (marker) {
                            map.removeLayer(marker);
                        }

                        marker = L.marker([lat, lon], { icon: customIcon })
                            .addTo(map)
                            .bindPopup(`<b>${displayName}</b>`)
                            .openPopup();

                        map.setView([lat, lon], 18);

                        // Set latitude and longitude in hidden fields
                        document.getElementById("latitude").value = lat;
                        document.getElementById("longitude").value = lon;
                    } else {
                        alert("Location not found.");
                    }
                })
                .catch(error => {
                    console.error("Geocoding error:", error);
                    alert("Error finding location.");
                });
        }

        // Form validation
        document.getElementById('eventForm').addEventListener('submit', function (e) {
            const startDate = document.querySelector('input[name="startDate"]').value;
            const startTime = document.querySelector('input[name="startTime"]').value;
            const endDate = document.querySelector('input[name="endDate"]').value;
            const endTime = document.querySelector('input[name="endTime"]').value;

            const startDateTime = new Date(startDate + 'T' + startTime);
            const endDateTime = new Date(endDate + 'T' + endTime);

            if (endDateTime <= startDateTime) {
                e.preventDefault();
                alert('End date and time must be after start date and time.');
                return false;
            }
        });

        function previewBanner() {
            const file = document.getElementById('bannerImage').files[0];
            const preview = document.getElementById('bannerPreview');
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        }

        function showSeatPlanName() {
            const file = document.getElementById('seatPlanImage').files[0];
            const display = document.getElementById('seatPlanName');
            display.textContent = file ? `Selected file: ${file.name}` : '';
        }
    </script>
</body>
</html>