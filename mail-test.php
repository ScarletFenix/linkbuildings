<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'scarletfenix66@gmail.com';
    $mail->Password   = 'iionttzzppdhmkmb';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('schaharyar@gmail.com', 'Test Mailer');
    $mail->addAddress('YOUR-EMAIL-HERE@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test';
    $mail->Body    = 'This is a test email.';

    $mail->send();
    echo "✅ Test email sent!";
} catch (Exception $e) {
    echo "❌ Mailer Error: {$mail->ErrorInfo}";
}
