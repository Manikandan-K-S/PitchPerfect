<?php
session_start(); // Start the session

// Include your database connection
include('../base/db.php');

$errors = []; // Initialize an empty array for errors

// Check if the user is already logged in, then redirect to index.php
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check user credentials
    $sql = "SELECT id, password FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // If user exists and password matches, set user ID in session and redirect to index.php
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit();
    } elseif ($result->num_rows == 0) {
        // If email is not registered, display error message
        $errors[] = "Email is not registered";
    } else {
        // If password does not match, display error message
        $errors[] = "Invalid email or password";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PitchPerfect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200 font-sans leading-normal tracking-normal">

<?php include_once("../base/nav_user.php"); ?>
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
                <label for="email" class="block text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 rounded border border-gray-300 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 rounded border border-gray-300 focus:border-blue-500">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Login</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<?php include_once("../base/footer_user.php"); ?>
</body>
</html>

