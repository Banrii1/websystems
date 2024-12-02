<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php'; // Ensure you have installed PHPMailer via Composer

function redirect_with_errors($errors, $name, $email) {
    $error_string = implode(", ", $errors);
    header("Location: signup.php?error=" . urlencode($error_string) . "&name=" . urlencode($name) . "&email=" . urlencode($email));
    exit;
}

$name = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];
$confirm_password = $_POST["confirm-password"];
$role = $_POST["role"] ?? 'user'; // Default role is 'user' if not provided

$errors = [];

if (empty($name)) {
    $errors[] = "Name is required!";
}

if (empty($email)) {
    $errors[] = "Email is required!";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid Email is required";
}

if (empty($password)) {
    $errors[] = "Password is required!";
} elseif (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters";
} elseif (!preg_match("/[a-z]/i", $password)) {
    $errors[] = "Password must contain at least one letter";
} elseif (!preg_match("/[0-9]/i", $password)) {
    $errors[] = "Password must contain at least one number";
}

if (empty($confirm_password)) {
    $errors[] = "Confirm Password is required!";
} elseif ($password !== $confirm_password) {
    $errors[] = "Passwords must match";
}

if (empty($role)) {
    $errors[] = "Role is required!";
}

if (!empty($errors)) {
    redirect_with_errors($errors, $name, $email);
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$activation_hash = bin2hex(random_bytes(16)); // Generate a random activation hash

$mysqli = require __DIR__ . "/database.php";

// Check if email already exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    $errors[] = "SQL error: " . $mysqli->error;
    redirect_with_errors($errors, $name, $email);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $errors[] = "Email already taken";
    redirect_with_errors($errors, $name, $email);
}

$sql = "INSERT INTO users (fullname, email, password_hash, role, account_activation_hash) VALUES (?, ?, ?, ?, ?)";

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    $errors[] = "SQL error: " . $mysqli->error;
    redirect_with_errors($errors, $name, $email);
}

$stmt->bind_param("sssss", $name, $email, $password_hash, $role, $activation_hash);

if ($stmt->execute()) {
    // Send activation email using PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
    $mail->SMTPAuth = true;
    $mail->Username = 'mailermailer00@gmail.com'; // SMTP username
    $mail->Password = 'nvpt rdag vqys sexi'; // Use the generated app password
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('no-reply@yourdomain.com', 'Mailer');
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Email Activation';
    $mail->Body = "Please activate your account by clicking the following link: <a href='https://intense-escarpment-90204-6b6c8736cad2.herokuapp.com/activate.php?code=" . htmlspecialchars($activation_hash, ENT_QUOTES, 'UTF-8') . "'>Activate Account</a>";

    if ($mail->send()) {
        header("Location: signup-success.html");
        exit;
    } else {
        $errors[] = "Failed to send activation email.";
        redirect_with_errors($errors, $name, $email);
    }
} else {
    if ($mysqli->errno === 1062) {
        $errors[] = "Email already taken";
    } else {
        $errors[] = $mysqli->error . " " . $mysqli->errno;
    }
    redirect_with_errors($errors, $name, $email);
}
?>