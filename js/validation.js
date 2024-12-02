const validation = new JustValidate("#signup");

validation
    .addField("#name", [
        { rule: "required", errorMessage: "Name is required" },
        { rule: "minLength", value: 3, errorMessage: "Name must be at least 3 characters" },
        { rule: "maxLength", value: 50, errorMessage: "Name must be less than 50 characters" }
    ])
    .addField("#email", [
        { rule: "required", errorMessage: "Email is required" },
        { rule: "email", errorMessage: "Enter a valid email" },
        {
            validator: async (value) => {
                const response = await fetch("validate-email.php?email=" + encodeURIComponent(value));
                const json = await response.json();
                return json.available;
            },
            errorMessage: "Email already taken"
        }
    ])
    .addField("#password", [
        { rule: "required", errorMessage: "Password is required" },
        { rule: "minLength", value: 8, errorMessage: "Password must be at least 8 characters" },
        { rule: "customRegexp", value: /[a-z]/i, errorMessage: "Password must contain at least one letter" },
        { rule: "customRegexp", value: /[0-9]/, errorMessage: "Password must contain at least one number" },
        { rule: "maxLength", value: 20, errorMessage: "Password must be less than 20 characters" }
    ])
    .addField("#confirm-password", [
        { rule: "required", errorMessage: "Confirm Password is required" },
        {
            validator: (value, fields) => value === fields["#password"].elem.value,
            errorMessage: "Passwords do not match"
        }
    ])
    .addField("#role", [
        { rule: "required", errorMessage: "Role is required" }
    ])
    .onSuccess((event) => {
        event.target.submit();
    });

document.getElementById("togglePassword").addEventListener("click", function() {
    const passwordField = document.getElementById("password");
    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
    passwordField.setAttribute("type", type);
    this.textContent = type === "password" ? "Show" : "Hide";
});

document.getElementById("toggleConfirmPassword").addEventListener("click", function() {
    const confirmPasswordField = document.getElementById("confirm-password");
    const type = confirmPasswordField.getAttribute("type") === "password" ? "text" : "password";
    confirmPasswordField.setAttribute("type", type);
    this.textContent = type === "password" ? "Show" : "Hide";
});