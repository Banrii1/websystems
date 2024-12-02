<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

$id = $_GET["id"];

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin-dashboard.php");
    exit;
} else {
    die("SQL error: " . $mysqli->error);
}
?>