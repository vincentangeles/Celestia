<?php
session_start();
include("connect.php");

if (!isset($_GET['eventID'])) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?eventID=1");
    exit();
}

$eventID = $_GET['eventID'];
if (!is_numeric($eventID)) {
    die("Invalid event ID.");
}

$eventID = mysqli_real_escape_string($conn, $eventID);

$questions = []; 
$showModal = false;

$questionQuery = "SELECT * FROM eventquestions WHERE eventID = '$eventID'";
$questionResults = executeQuery($questionQuery);
if ($questionResults && mysqli_num_rows($questionResults) > 0) {
    while ($row = mysqli_fetch_assoc($questionResults)) {
        $questions[] = $row;
    }
}

if (isset($_SESSION['showModal']) && $_SESSION['showModal'] === true) {
    $showModal = true;
    unset($_SESSION['showModal']);
}

if (isset($_POST['btnSubmit'])) {
    $firstName = mysqli_real_escape_string($conn, trim($_POST['firstName']));
    $lastName = mysqli_real_escape_string($conn, trim($_POST['lastName']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $contactNumber = mysqli_real_escape_string($conn, trim($_POST['contactNumber']));

    $insertQuery = "
        INSERT INTO guests (eventID, guestFirstName, guestLastName, guestContactNumber, guestEmail)
        VALUES ('$eventID', '$firstName', '$lastName', '$contactNumber', '$email')
    ";
    $insertResult = executeQuery($insertQuery);

    if ($insertResult) {
        require_once 'mailConfirm.php';
        $fullName = $firstName . ' ' . $lastName;
        sendConfirmationEmail($email, $fullName);

        $_SESSION['showModal'] = true;
        header("Location: " . $_SERVER['PHP_SELF'] . "?eventID=$eventID");
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Attend Event Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/attend.css">
  <link rel="icon" href="assets/img/icon.gif">
</head>

<body>
  <nav class="navbar navbar-custom px-4 py-3 d-flex align-items-center">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="assets/img/icon.gif" alt="Logo" width="40" height="40" class="me-2 rounded-circle">
    </a>
    <span class="navbar-text mx-auto fw-bold fs-5">Event Attendance Form</span>
    <div class="d-flex align-items-center">
      <a href="#"><i class="fa-solid fa-circle-user fa-2xl" style="color: #ffe1eb;"></i></a>
    </div>
  </nav>

  <div class="container pt-3">
    <div class="row justify-content-center">
      <div class="col-12 col-md-12 col-lg-8">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?eventID=' . urlencode($eventID); ?>">
          <input type="hidden" name="eventID" value="<?php echo htmlspecialchars($eventID); ?>">

          <div class="row">
            <div class="col-md-6 py-2">
              <label for="first-name" class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" id="first-name" name="firstName" class="form-control" required>
            </div>
            <div class="col-md-6 py-2">
              <label for="last-name" class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" id="last-name" name="lastName" class="form-control" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6 py-2">
              <label for="contact-number" class="form-label">Contact Number <span class="text-danger">*</span></label>
              <input type="tel" id="contact-number" name="contactNumber" class="form-control" required pattern="[0-9\-]+" inputmode="numeric" maxlength="11">
            </div>
            <div class="col-md-6 py-2">
              <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" id="email" name="email" class="form-control" required>
            </div>
          </div>

          <?php foreach ($questions as $question): ?>
          <div class="mb-3">
            <div class="mb-3">
              <label class="form-label">
                <?php echo htmlspecialchars($question['questionText']); ?>
                <?php if ($question['isRequired']): ?><span style="color: red;">*</span><?php endif; ?>
              </label>

              <textarea class="form-control"
                        name="question_<?php echo $question['questionID']; ?>"
                        rows="3"
                        <?php if ($question['isRequired']) echo 'required'; ?>>
              </textarea>

              <?php if ($question['helpText']): ?>
                <div class="form-text">
                  <?php echo htmlspecialchars($question['helpText']); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>

          <div class="row mb-3">
            <div class="col-12 text-end mt-3">
              <button type="submit" name="btnSubmit" class="btn submit-btn">Submit</button>
            </div>
          </div>

          <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content custom-modal">
                <div class="modal-body">
                  <p class="modal-main-msg">Your attendance has been recorded successfully.</p>
                  <p class="modal-sub-msg">We look forward to seeing you at the event!</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" id="modalConfirmBtn">Confirm</button>
                </div>
              </div>
            </div>
          </div>

          <div class="text-center small text-muted">
            Privacy Note: We'll only use your info to confirm your attendance.
          </div>
        </form>
      </div>
    </div>
  </div>


<?php if ($showModal): ?>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();

    document.getElementById('modalConfirmBtn').addEventListener('click', function () {
      window.location.href = 'eventGuest.php';
    });

    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('submitted');
      window.history.replaceState({}, document.title, url.pathname + url.search);
    }
  });
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>
</html>
