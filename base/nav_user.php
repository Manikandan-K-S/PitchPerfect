<?php
if (session_status() === PHP_SESSION_NONE) {
    // Start the session
    session_start();
}

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
} else {
    $isLoggedIn = false;
}
?>
<style>
    @font-face {
        font-family: MyCustomFont;
        src: url('../base/font.otf') format('opentype');
    }

    .navbar {
        position: fixed; /* Set the navbar to fixed position */
  top: 0; /* Position the navbar at the top of the page */
  width: 100%; 
    background-color: #ddd; /* Soft Blue background */
    z-index: 1;
}
.main {
    padding-bottom: 65px; ; /* Add a top margin to avoid content overlay */
}
    .navbar-brand {
        color: #000; /* White color for brand */
    }

    .navbar-toggler-icon {
        background-color: #ddd; /* White color for toggler icon */
    }

    .navbar-nav .nav-link {
        color: #000; /* White color for nav links */
    }
</style>

<div class="main"></div>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <!-- Logo and Brand Name -->
        <a class="navbar-brand" href="index.php" style="font-family: 'MyCustomFont', Arial, sans-serif; font-size: 24px;">
            <img src="../base/images/logo.svg" alt="PitchPerfect Logo" width="40" height="40" class="d-inline-block align-top">
            PitchPerfect
        </a>

        <!-- Select City (Always Visible) -->
        <!-- <ul class="navbar-nav me-auto">
            <li class="nav-item">
            <a class="nav-link" href="select_city.php">Select City</a>
            </li>
        </ul> -->

        <!-- Toggler button for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if ($isLoggedIn): ?>
                <!-- Orders (For Logged-in Users) -->
                <li class="nav-item">
                    <a class="nav-link" href="orders.php">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="select_city.php">Select City</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <!-- User Avatar and Dropdown (For Logged-in Users) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../base/images/avatar.jpg" alt="User Avatar" width="30" height="30"
                            class="rounded-circle d-inline-block align-top">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <!-- Change Mobile Number -->
                        <li><a class="dropdown-item" href="#">Change Mobile Number</a></li>
                        <!-- Change Password -->
                        <li><a class="dropdown-item" href="#">Change Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- Logout -->
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <!-- Login (For Non-Logged-in Users) -->
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <!-- Register (For Non-Logged-in Users) -->
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>




