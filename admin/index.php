<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .no-turfs {
            text-align: center;
            font-size: 1.5rem;
            margin-top: 100px;
        }

        .fixed-image {
            height: 200px;
        }

        .card {
            padding: 10px; /* Adjust card padding */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add shadow for better visibility */
            transition: box-shadow 0.3s ease; /* Add transition effect */
        }

        .card:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15); /* Add shadow on hover */
        }

        /* Position floating menu buttons */
        .floating-menu {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .floating-menu-btn {
            margin-bottom: 10px;
        }
        .card-title {
            font-size: 20px; /* Set font size */
            font-family: 'Roboto', sans-serif; /* Use Roboto font */
            font-weight: 600; /* Set font weight */
            color: #333; /* Set font color */
        }
        .container{
            padding : 20px;
            padding-right: 100px;
            padding-bottom: 50px;
            padding-left: 100px;
        }
    </style>
</head>
<body>
<?php 
    include('login_check.php');
    include('../base/nav_dashboard.php');
?>
<div class="container">
    <?php
    include('../base/db.php'); // Include database connection
    
    // Admin is logged in, retrieve turfs owned by admin
    $admin_id = $_SESSION['admin_id'];
    $query = "SELECT * FROM turf WHERE admin_id = $admin_id";
    $result = $conn->query($query);

    // Check if turfs are found
    if ($result && $result->num_rows > 0) {
        echo "<br><h2 class='text-3xl font-bold mb-4'>Turf(s) List</h2>";
        echo "<div class='row justify-content g-2'>";
        // Loop through each turf and display as horizontal card
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="col-md-4 mb-10 d-flex justify-content-center">
                <form action="dashboard.php" method="POST">
                    <input type="hidden" name="turf_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                    <button type="submit" class="btn bg-transparent">
                        <div class="card h-100 w-90">
                            <img src="<?php echo $row['image1']; ?>" class="fixed-image card-img-top" style="width:330px;" alt="Turf Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            </div>
                        </div>
                    </button>
                </form>
            </div>
            <?php
        }
        echo "</div>";
    } else {
        // No turfs found for the admin, provide link to add turf
        ?>
        <div class="col">
            <p class="no-turfs">No turfs found. <a href="add_turf.php" class="text-blue-700">Add a turf</a>.</p>
        </div>
        <?php
    }
    ?>
</div>

<!-- Floating menu buttons -->
<div class="floating-menu">
    <a href="add_turf.php" class="btn btn-primary floating-menu-btn">Add Turf</a>
    <!-- <a href="add_event.php" class="btn btn-success floating-menu-btn">Add Event</a> -->
</div>

</body>
</html>
