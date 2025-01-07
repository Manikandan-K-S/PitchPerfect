<?php
session_start();

// Include db.php for database connection
include('../base/db.php');

// Initialize variables for error messages
$errors = array();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate form fields
    if (empty($email)) {
        $errors[] = "Email is required";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // If no errors, verify user credentials
    if (empty($errors)) {
        // Query to retrieve user with matching email
        $query = "SELECT * FROM admin WHERE email='$email' LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows == 1) {
            // User found, verify password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password verified, set user ID in session and redirect to index page
                $_SESSION['admin_id'] = $user['id'];
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Invalid email or password";
            }
        } else {
            $errors[] = "Invalid email or password";
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
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .form-error {
            color: red;
            font-size: 0.875rem;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-200 font-sans leading-normal tracking-normal">

<div class="flex h-screen items-center justify-center bg-gray-200">
    <div class="bg-white p-8 rounded shadow-md w-full md:w-1/2 lg:w-1/3">
        <h2 class="text-2xl font-bold mb-4">Login</h2>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>

</body>
</html>
