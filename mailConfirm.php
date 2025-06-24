<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'assets/mailer/PHPMailer.php';
require 'assets/mailer/SMTP.php';
require 'assets/mailer/Exception.php';
require_once 'connect.php';

function sendConfirmationEmail($recipientEmail, $recipientName) {
    $mail = new PHPMailer(true);

    $query = "SELECT emailAddress, appPassword FROM credentials ORDER BY credentialId DESC LIMIT 1";
    $result = executeQuery($query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $gmailEmail = $row['emailAddress'];
        $gmailAppPassword = $row['appPassword'];
    } else {
        return false; 
    }

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $gmailEmail;
        $mail->Password   = $gmailAppPassword;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587; 

        $mail->setFrom($gmailEmail, 'Celestia Events');
        $mail->addAddress($recipientEmail, $recipientName);

        $mail->isHTML(true);
        $mail->Subject = 'Event Registration Confirmation';
        $mail->Body    = 'Hello ' . htmlspecialchars($recipientName) . ',<br>Thank you for registering for our event!<br>Best regards,<br>Celestia Events';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
