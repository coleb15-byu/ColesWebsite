<?php
// Replace with your actual reCAPTCHA secret key
$recaptchaSecret = '6LdVNhsrAAAAAK97NGI0YVbIiEqCcRdlLGntsqoG';

// Replace with your email address
$to = 'ccummings1245@gmail.com';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot check
    if (!empty($_POST['website'])) {
        // It's spam
        http_response_code(400);
        echo "Spam detected.";
        exit;
    }

    // Sanitize input
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST['phone']));
    $message = strip_tags(trim($_POST['message']));
    $captcha = $_POST['g-recaptcha-response'];

    // Check required fields
    if (empty($name) || empty($email) || empty($message) || empty($captcha)) {
        http_response_code(400);
        echo "Please fill out the form completely.";
        exit;
    }

    // Verify reCAPTCHA
    $verifyResponse = file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptchaSecret . '&response=' . $captcha
    );
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        http_response_code(400);
        echo "reCAPTCHA verification failed.";
        exit;
    }

    // Construct email
    $subject = "New Contact Message from $name";
    $emailContent = "Name: $name\n";
    $emailContent .= "Email: $email\n";
    $emailContent .= "Phone: $phone\n\n";
    $emailContent .= "Message:\n$message\n";

    $emailHeaders = "From: $name <$email>";

    // Send the email
    if (mail($to, $subject, $emailContent, $emailHeaders)) {
        http_response_code(200);
        echo "Your message has been sent!";
    } else {
        http_response_code(500);
        echo "Something went wrong. Please try again.";
    }
} else {
    http_response_code(403);
    echo "Invalid request method.";
}
?>
