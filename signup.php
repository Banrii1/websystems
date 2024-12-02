<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js" defer></script>
    <script src="js/validation.js" defer></script>
    <style>
        .toggle-password {
            cursor: pointer;
            user-select: none;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .password-container {
            position: relative;
        }
    </style>
</head>
<body>
    <h1>Signup</h1>
    <form id="signup" action="process-signup.php" method="post" novalidate>
        <div>
            <input type="text" id="name" name="name" placeholder="Fullname">
        </div>
        <div>
            <input type="email" id="email" name="email" placeholder="Email Address">
        </div>
        <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Password">
            <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
        </div>
        <div class="password-container">
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password">
            <span class="toggle-password" onclick="togglePassword('confirm-password', this)">👁️</span>
        </div>
        <div>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button>Signup</button>
    </form>

    <script>
        function togglePassword(fieldId, iconElement) {
            const field = document.getElementById(fieldId);
            if (field.type === "password") {
                field.type = "text"; // Show password
                iconElement.textContent = "🙈"; // Change icon to "hide" symbol
            } else {
                field.type = "password"; // Hide password
                iconElement.textContent = "👁️"; // Change back to "show" symbol
            }
        }
    </script>
</body>
</html>