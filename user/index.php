<?php

session_start();

// Check if the user's city is set in the session
if (!isset($_SESSION['city'])) {
    // Redirect the user to the city selection page if the city is not set
    include("select_city.php");
    exit(); // Ensure script execution stops after redirection
}

// Include database connection
include('../base/db.php');

// Retrieve turf listings based on the user's city
$city = $_SESSION['city'];
$query = "SELECT * FROM turf WHERE city = '$city'";
$result = $conn->query($query);

  ?>
 
    <!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turf Listings - <?php echo $city; ?></title>
    <style>
        .fixed-image {
            height: 200px;
        }
        h5 {
            color: #000;
        }
        p {
            color: #0005;
        }
        /* Increase card height */
        
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once("../base/nav_user.php"); 
if ($result->num_rows > 0) {
?>
<div class="container mt-5">
    <h2 class="text-center">Turf Listings in <?php echo $city; ?></h2><br>
    <div class="row mt-3">
        <?php
        // Loop through each turf and display as card
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="col-md-4 mb-5">
                <a href="book_turf.php?id=<?php echo $row['id']; ?>" style="text-decoration: none !important;">
                    <div class="card">
                        <img src="<?php echo $row['image1']; ?>" class="card-img-top"  height="250" alt="Turf Image">
                        <div class="card-body">
                            <h5 class="card-title" ><?php echo $row['name']; ?></h5>                            
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

<?php include_once("../base/footer_user.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
} else {
    // No turfs found for the user's city
    echo "<h2 align='center'>No turfs found in $city</h2>";
}
?>
