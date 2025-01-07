<?php
// Include db.php for database connection
include('../base/db.php');

// Initialize variables for error messages
$errors = array();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    // Validate form fields
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
    if (empty($mobile)) {
        $errors['mobile'] = "Mobile number is required";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors['mobile'] = "Invalid mobile number";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif ($password != $password_confirmation) {
        $errors['password'] = "Passwords do not match";
    }

    // Check if email or mobile already exists
    $existing_user_query = "SELECT * FROM user WHERE email='$email' OR mobile='$mobile' LIMIT 1";
    $result = $conn->query($existing_user_query);
    $existing_user = $result->fetch_assoc();
    if ($existing_user) {
        if ($existing_user['email'] === $email) {
            $errors['email'] = "Email already exists";
        }
        if ($existing_user['mobile'] === $mobile) {
            $errors['mobile'] = "Mobile number already exists";
        }
    }

    // If no errors, insert user data into database
    if (empty($errors)) {
        // Hash the password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into user table
        $insert_query = "INSERT INTO user (name, email, mobile, password) VALUES ('$name', '$email', '$mobile', '$hashed_password')";
        if ($conn->query($insert_query) === TRUE) {
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } else {
            $errors['db'] = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PitchPerfect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .form-error {
            color: red;
            font-size: 0.875rem;
        }
    </style>
    <script>
        function validateForm() {
            var name = document.getElementById("name").value.trim();
            var email = document.getElementById("email").value.trim();
            var mobile = document.getElementById("mobile").value.trim();
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("password_confirmation").value;
            var errorMessages = document.getElementsByClassName("form-error");

            // Reset previous error messages
            for (var i = 0; i < errorMessages.length; i++) {
                errorMessages[i].innerText = "";
            }

            var isValid = true;

            if (name === "") {
                document.getElementById("nameError").innerText = "Please enter your name.";
                isValid = false;
            }
            if (email === "") {
                document.getElementById("emailError").innerText = "Please enter your email.";
                isValid = false;
            }
            if (mobile === "") {
                document.getElementById("mobileError").innerText = "Please enter your mobile number.";
                isValid = false;
            }
            if (password === "") {
                document.getElementById("passwordError").innerText = "Please enter a password.";
                isValid = false;
            }
            if (confirmPassword === "") {
                document.getElementById("confirmPasswordError").innerText = "Please confirm your password.";
                isValid = false;
            }
            if (password !== confirmPassword) {
                document.getElementById("confirmPasswordError").innerText = "Passwords do not match.";
                isValid = false;
            }

            return isValid;
        }

        // Function to allow only numeric input in the mobile field
        function allowOnlyNumeric(event) {
            var charCode = event.which ? event.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                event.preventDefault();
            }
        }
    </script>
</head>
<body class="bg-gray-200 font-sans leading-normal tracking-normal">
<?php include_once("../base/nav_user.php"); ?>
<div class="flex h-screen items-center justify-center bg-gray-200">
    <div class="bg-white p-8 rounded shadow-md w-full md:w-1/2 lg:w-1/3">
        <h2 class="text-2xl font-bold mb-4">Register</h2>
        <?php if (!empty($errors)) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-3 rounded mb-4" role="alert">
                <?php foreach ($errors as $error) : ?>
                    <p class="text-red-500">* <?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return validateForm()">
            <div class="mb-4">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>">
                <span id="nameError" class="form-error"></span>
            </div>
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>">
                <span id="emailError" class="form-error"></span>
            </div>
            <div class="mb-4">
                <label for="mobile" class="form-label">Mobile</label>
                <input type="text" id="mobile" name="mobile" class="form-control" value="<?php echo isset($mobile) ? $mobile : ''; ?>" onkeypress="allowOnlyNumeric(event)">
                <span id="mobileError" class="form-error"></span>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control">
                <span id="passwordError" class="form-error"></span>
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                <span id="confirmPasswordError" class="form-error"></span>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<?php include_once("../base/footer_user.php"); ?>
    
</body>
</html>
