<?php
include("connect.php");

session_start();

if (!isset($_SESSION['organizerID'])) {
    header("Location: signin.php");
    exit();
}
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

if (isset($_POST['btnSubmitform'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $startDateTime = $_POST['startDate'] . ' ' . $_POST['startTime'];
    $endDateTime = $_POST['endDate'] . ' ' . $_POST['endTime'];
    $email = $_POST['email'];
    $ticketingOutletURL = $_POST['ticketingOutletURL'];

    $posterURL = (isset($_FILES['poster']) && $_FILES['poster']['error'] === 0)
        ? uploadImage($_FILES['poster'], 'assets/img/posters/')
        : '';

    $bannerURL = (isset($_FILES['bannerImage']) && $_FILES['bannerImage']['error'] === 0)
        ? uploadImage($_FILES['bannerImage'], 'assets/img/banners/')
        : '';

    $seatPlanURL = (isset($_FILES['seatPlanImage']) && $_FILES['seatPlanImage']['error'] === 0)
        ? uploadImage($_FILES['seatPlanImage'], 'assets/img/seatplans/')
        : '';

    $insertOrganizerQuery = "INSERT IGNORE INTO organizers (email) VALUES ('$email')";
    mysqli_query($conn, $insertOrganizerQuery);

    $getOrganizerID = "SELECT organizerID FROM organizers WHERE email = '$email' LIMIT 1";
    $organizerResult = mysqli_query($conn, $getOrganizerID);
    $organizerRow = mysqli_fetch_assoc($organizerResult);
    $organizerID = $organizerRow['organizerID'] ?? 0;

    $eventQuery = "
        INSERT INTO events (
            title, description, location, latitude, longitude,
            startDateTime, endDateTime,
            posterURL, bannerImage, seatPlanImage,
            organizerInfoID, createdAt, ticketingOutletURL
        ) VALUES (
            '$title', '$description', '$location', '$latitude', '$longitude',
            '$startDateTime', '$endDateTime',
            '$posterURL', '$bannerURL', '$seatPlanURL',
            $organizerID, NOW(), '$ticketingOutletURL'
        )
    ";
    (executeQuery($eventQuery));

    $eventID = mysqli_insert_id($conn);

    $questionsData = json_decode($_POST['questions_data'] ?? '', true);


    if (!is_array($questionsData)) {
        return;
    }

    foreach ($questionsData as $questionData) {
        if (empty($questionData['title'])) {
            continue;
        }

        $questionText = $questionData['title'];
        $helpText = $questionData['helpText'] ?? '';
        $isRequired = !empty($questionData['required']) ? 1 : 0;

        $questionQuery = "
        INSERT INTO eventQuestions (eventID, questionText, helpText, isRequired)
        VALUES ($eventID, '$questionText', '$helpText', $isRequired)
    ";
        (executeQuery($questionQuery));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="icon" href="assets/img/icon.gif">
    <link rel="stylesheet" href="assets/css/eventForm.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg rounded-4 overflow-hidden mt-4 mb-4">
                    <div class="text-white text-center py-4 px-3" style="background: var(--primary-color);">
                        <h2 class="mb-2 fw-bold" style="font-size: var(--heading); font-family: var(--primaryFont);">
                            Create New Event</h2>
                        <p class="mb-0 opacity-75" style="font-size: var(--lead); font-family: var(--primaryFont);">Fill
                            out the details below to create your event</p>
                    </div>

                    <!-- Form Section -->
                    <div class="card-body p-4" style="background-color: #f5f5f5;">
                        <form id="eventForm" method="POST" enctype="multipart/form-data">
                            <div class="container-fluid p-0">

                                <!-- Banner Upload -->
                                <div class="position-relative">
                                    <img id="bannerPreview" src="#" alt="Banner Image" class="w-100 d-none"
                                        style="max-height: 250px; object-fit: cover;">
                                    <div class="p-2 bg-light">
                                        <label for="bannerImage" class="form-label fw-semibold"
                                            style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                            Upload Banner Image
                                        </label>
                                        <input class="form-control rounded-3" type="file" name="bannerImage"
                                            id="bannerImage" accept="image/*" onchange="previewBanner()">
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
                                            style="background-color: #e0e0e0;" placeholder="Enter title" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold"
                                            style="color: var(--text-color-dark); font-family: var(--primaryFont);">
                                            Event Poster
                                        </label>
                                        <input type="file" name="poster" accept="image/*" class="form-control rounded-3"
                                            style="background-color: #e0e0e0;">
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
                                        style="font-size: 0.9rem; font-family: var(--primaryFont);"></div>
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
                                            required></textarea>
                                    </div>
                                </div>

                                <!-- Ticketing Outlet URL -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold"
                                        style="font-family: var(--primaryFont); color: var(--text-color-dark);">Ticketing
                                        Outlet URL</label>
                                    <input type="url" class="form-control rounded-3" name="ticketingOutletURL"
                                        placeholder="https://example.com/ticket" style="background-color: #e0e0e0;">
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
                                                    style="background-color: #e0e0e0;" required>
                                            </div>
                                            <div class="col-5">
                                                <input type="time" class="form-control rounded-3" name="startTime"
                                                    style="background-color: #e0e0e0;" required>
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
                                                    style="background-color: #e0e0e0;" required>
                                            </div>
                                            <div class="col-5">
                                                <input type="time" class="form-control rounded-3" name="endTime"
                                                    style="background-color: #e0e0e0;" required>
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
                                                                placeholder="Enter full address" required
                                                                onkeydown="if(event.key === 'Enter') { event.preventDefault(); searchLocation(); }">
                                                            <button type="button"
                                                                class="btn text-white rounded-end-3 px-3"
                                                                style="background: var(--primary-color); font-family: var(--primaryFont);"
                                                                onclick="searchLocation()">
                                                                <i class="fas fa-map-marker-alt me-1"></i>Pin location
                                                            </button>
                                                            <input type="hidden" name="latitude" id="latitude">
                                                            <input type="hidden" name="longitude" id="longitude">
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
                                                                    placeholder="What's your email address?" required>
                                                                <small
                                                                    style="color: var(--text-color-gray); font-family: var(--primaryFont);">*This
                                                                    question is required</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="questionsContainer">
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="button"
                                                            class="btn text-white w-100 rounded-3 py-2"
                                                            style="background: var(--primary-color); font-family: var(--primaryFont);"
                                                            id="addQuestionBtn">
                                                            <i class="fas fa-plus me-2"></i>Add Question
                                                        </button>
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
                                            <!-- <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="consoleFormData()">Console Log Form</button> -->
                                            <button type="button" class="btn btn-secondary rounded-3 px-4"
                                                style="font-family: var(--primaryFont);"
                                                onclick="window.history.back()">Cancel</button>
                                            <button type="submit" class="btn text-white rounded-3 px-4"
                                                name="btnSubmitform"
                                                style="background: var(--primary-color); font-family: var(--primaryFont);">Create
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
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

            // Default to view entire Philippines
            const philippinesBounds = [
                [4.5, 116.0],
                [21.0, 127.0]
            ];
            map.fitBounds(philippinesBounds);
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

        function showPosterName() {
            const file = document.getElementById('poster').files[0];
            const display = document.getElementById('posterName');
            display.textContent = file ? `Selected file: ${file.name}` : '';
        }

        function showSeatPlanName() {
            const file = document.getElementById('seatPlanImage').files[0];
            const display = document.getElementById('seatPlanName');
            display.textContent = file ? `Selected file: ${file.name}` : '';
        }
    </script>
    <script src="assets/js/eventQuestion.js"></script>
</body>

</html>