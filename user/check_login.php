<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, display message and login/register links
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <link rel="icon" href="../base/images/logo.svg" type="image/gif" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Check</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    
<?php include_once("../base/nav_user.php"); ?>
    <div class="container text-center">
        <h1 class="display-4">You need to be logged in to access this page.</h1><br>
        <div class="alert alert-danger" role="alert">
            <p class="lead">
                If you already have an account, please <a href="login.php" class="alert-link">login</a>.
            </p>
            <p class="lead">
                If you don't have an account yet, you can <a href="register.php" class="alert-link">register here</a>.
            </p>
        </div>
    </div>

    <?php include_once("../base/footer_user.php"); ?>
    
    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>

    <?php
    exit(); // Exit to prevent further execution of code
}

?>