
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="bootstrap.min.css">
<!-- Tailwind CSS -->
<link rel="stylesheet" href="tailwind.min.css">
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="jquery-ui.css">


<!-- Bootstrap JS -->
<script src="bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="book_turf/jquery-3.6.0.min.js"></script>
<!-- jQuery UI -->
<script src="book_turf/jquery-ui.min.js"></script>




<?php

session_start();

// Check if the user's city is set in the session
if (!isset($_SESSION['city'])) {
    // Redirect the user to the city selection page if the city is not set
    include("select_city.php");
    exit(); // Ensure script execution stops after redirection
}

// Include database connection
include('../db.php');

// Retrieve turf listings based on the user's city
$city = $_SESSION['city'];
$query = "SELECT * FROM turf WHERE city = '$city'";
$result = $conn->query($query);

// Check if turfs are found
if ($result->num_rows > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Turf Listings<?php echo $city; ?></title>
    
        
    </head>
    <body>

    <div class="container mt-5">
        <h3 class="text-center">Turf Listings in <?php echo $city; ?></h3><br>
        <div class="row mt-3">
            <?php
            // Loop through each turf and display as card
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4 mb-4">
                    <a href="book_turf.php?id=<?php echo $row['id']; ?>" style="text-decoration: none !important;">
                        <div class="card">
                            <img src="<?php echo $row['image1']; ?>" class="card-img-top" alt="Turf Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['name']; ?></h5>                            
                                <p class="card-text">₹<?php echo $row['price']; ?> (per hour)</p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    </body>
    </html>
    <?php
} else {
    // No turfs found for the user's city
    echo "<p>No turfs found in $city</p>";
}
?>
