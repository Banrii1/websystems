<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('529934975236-d5tnurm5ad4mm3t6tj5cp8p2ikvprulm.apps.googleusercontent.com'); 
$client->setClientSecret('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'); 
$client->setRedirectUri('http://localhost/google-login.php'); 

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    $email = $userInfo->email;
    $name = $userInfo->name;

    $mysqli = require __DIR__ . "/database.php";

    // Check if the user already exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $mysqli->stmt_init();

    if (!$stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // User exists, log them in
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
    } else {
        // User does not exist, create a new user
        $sql = "INSERT INTO users (fullname, email, role) VALUES (?, ?, 'user')";
        $stmt = $mysqli->stmt_init();

        if (!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }

        $stmt->bind_param("ss", $name, $email);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['role'] = 'user';
        } else {
            die("SQL error: " . $mysqli->error);
        }
    }

    header('Location: index.php');
    exit;
}
?>