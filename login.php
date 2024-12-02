<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/database.php";

    $sql = sprintf("SELECT * FROM users WHERE email = '%s'", $mysqli->real_escape_string($_POST["email"]));

    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($_POST["password"], $user["password_hash"])) {
            if ($user["account_activation_hash"] !== null) {
                $is_invalid = true;
                $error_message = "Please activate your account before logging in.";
            } else {
                session_start();
                session_regenerate_id();
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["role"] = $user["role"];
                if ($user["role"] === "admin") {
                    header("Location: admin-dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            }
        }
    }
    $is_invalid = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="/js/login-validation.js" defer></script>
</head>
<body>
    <h1>Login</h1>
    <?php if ($is_invalid): ?>
        <em><?= isset($error_message) ? htmlspecialchars($error_message) : "Invalid login" ?></em>
    <?php endif; ?>
    <form id="login" method="post" novalidate>
        <div>
            <input type="email" id="email" name="email" placeholder="Email Address" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
        </div>
        <div>
            <input type="password" id="password" name="password" placeholder="Password">
        </div>
        <button>Login</button>
    </form>
    <p><a href="index.php" class="button">Back</a></p>
</body>
</html>