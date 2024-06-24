<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer autoload file

function sendEmail($recipient, $subject, $body) {
    $mail = new PHPMailer(true); // Create a new PHPMailer instance

    try {
        // Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.mailtrap.io';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = '486464adde19a0';                 // SMTP username
        $mail->Password = '042840bb57da83';                           // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port = 2525;                                    // TCP port to connect to

        $mail->CharSet = 'UTF-8';


        // Recipients
        $mail->setFrom('from@example.com', 'PET Adoption');
        $mail->addAddress($recipient);     // Add a recipient

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
