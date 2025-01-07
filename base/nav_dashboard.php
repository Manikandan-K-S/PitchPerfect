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
        padding-bottom: 65px; /* Add a top margin to avoid content overlay */
        padding-top: 20px; /* Add padding to the top of the content */
    }
    .navbar-brand {
        color: #000; /* Black color for brand */
    }
    .navbar-toggler-icon {
        background-color: #ddd; /* Grey color for toggler icon */
    }
    .navbar-nav .nav-link {
        color: #000; /* Black color for nav links */
    }
</style>

<div class="main">
    <!-- Your content here -->
</div>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <!-- Logo and Brand Name -->
        <a class="navbar-brand" href="index.php" style="font-family: 'MyCustomFont', Arial, sans-serif; font-size: 24px;">
            <img src="../base/images/logo.svg" alt="PitchPerfect Logo" width="40" height="40" class="d-inline-block align-top">
            PitchPerfect Admin
        </a>

        <!-- Toggler button for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Dashboard (For Logged-in Admin) -->
                
            </ul>
            <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <!-- Settings (For Logged-in Admin) -->
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#">Events</a>
                </li> -->
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>

            </ul>
        </div>
    </div>
</nav>
