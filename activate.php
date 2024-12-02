<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . "/database.php";

$activation_hash = $_GET["code"];

$sql = "SELECT * FROM users WHERE account_activation_hash = ?";
$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("s", $activation_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Activation</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Account Activation</h1>
    <?php if ($user): ?>
        <?php
        $sql = "UPDATE users SET account_activation_hash = NULL WHERE account_activation_hash = ?";
        $stmt = $mysqli->stmt_init();

        if (!$stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }

        $stmt->bind_param("s", $activation_hash);

        if ($stmt->execute()): ?>
            <p>Account activated successfully. You can now <a href='https://intense-escarpment-90204-6b6c8736cad2.herokuapp.com/login.php'>log in</a>.</p>
        <?php else: ?>
            <p>SQL error: <?= htmlspecialchars($mysqli->error) ?></p>
        <?php endif; ?>
    <?php else: ?>
        <p>Invalid activation code.</p>
    <?php endif; ?>
</body>
</html>