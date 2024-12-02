<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    $sql = "UPDATE users SET fullname = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $mysqli->stmt_init();

    if (!$stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param("sssi", $fullname, $email, $role, $id);

    if ($stmt->execute()) {
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
            $stmt = $mysqli->stmt_init();

            if (!$stmt->prepare($sql)) {
                die("SQL error: " . $mysqli->error);
            }

            $stmt->bind_param("si", $password_hash, $id);

            if (!$stmt->execute()) {
                die("SQL error: " . $mysqli->error);
            }
        }
        header("Location: admin-dashboard.php");
        exit;
    } else {
        die("SQL error: " . $mysqli->error);
    }
} else {
    $id = $_GET["id"];
    $result = $mysqli->query("SELECT * FROM users WHERE id = $id");
    $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Edit User</h1>
    <form method="post" action="edit-user.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user["id"]) ?>">
        <div>
            <input type="text" name="fullname" placeholder="Fullname" value="<?= htmlspecialchars($user["fullname"]) ?>">
        </div>
        <div>
            <input type="email" name="email" placeholder="Email Address" value="<?= htmlspecialchars($user["email"]) ?>">
        </div>
        <div>
            <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
        </div>
        <div>
            <select name="role">
                <option value="user" <?= $user["role"] === "user" ? "selected" : "" ?>>User</option>
                <option value="admin" <?= $user["role"] === "admin" ? "selected" : "" ?>>Admin</option>
            </select>
        </div>
        <button>Save</button>
    </form>
</body>
</html>