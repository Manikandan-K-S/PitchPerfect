<?php

include('login_check.php');

include('../base/db.php');


// Collect turf_id from POST method
if (isset($_POST['turf_id'])) {
    $turf_id = $_POST['turf_id'];

    $admin_id = $_SESSION['admin_id'];

    $query = "SELECT * FROM turf WHERE id = $turf_id AND admin_id = $admin_id";

    $result = $conn->query($query);

    if ($result) {

        if ($result->num_rows > 0) {
         
            $turf = $result->fetch_assoc();
            // Example: $turf_name = $turf['name'];
        } else {
            // Admin does not own the turf, handle accordingly (redirect, error message, etc.)
            echo "You do not own this turf.";
        }
    } else {
        // Error executing the query
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();

} else {
    // Turf ID not provided in POST method, handle accordingly (redirect, error message, etc.)
    header('location:index.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0"></script>

    <style>
        .sidebar {
            height: 100vh; /* Full height of the viewport */
            background-color: #333;
            padding-top: 79px;
            position:fixed;
        }

        .sidebar h2 {
            color: #ddd;
            text-align: center;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin-bottom: 6px;
            padding: px;
        }
        
        .sidebar ul li button {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 24px;
            text-align: center;
            background-color: #555;
            border: none;
            width: 100%;
            height: 21.5vh; /* Set height to 25% of the viewport height */
            border-radius: 5px;
            transition: background-color 0.3s;
            box-sizing: border-box; /* Include padding and border in the height calculation */
        }


        .sidebar ul li button:hover {
            background-color: #777;
        }

        


        /* Collapsible sidebar for smaller screens */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                width: 250px;
                transition: all 0.3s;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 220px;
            }

            .navbar-toggler-icon {
                color: #000;
            }
        }
        body {
    overflow-x: hidden; /* Hide horizontal scrollbar */
}
.navbar {
        position: fixed; /* Set the navbar to fixed position */
        top: 0; /* Position the navbar at the top of the page */
        width: 100%; 
        background-color: #ddd; /* Soft Blue background */
        z-index: 1;
    }
    </style>

    
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar">
                
                <ul>
                    <li>
                        <form method="POST" >
                            <input type="hidden" name="page" value="booking_details">
                            <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
                            <button type="submit" class="btn btn-dark btn-block">Booking Details</button>
                        </form>
                    </li>
                    <li>
                        <form method="POST" >
                            <input type="hidden" name="page" value="analytics">
                            <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
                            <button type="submit" class="btn btn-dark btn-block">Analytics</button>
                        </form>
                    </li>
                    <li>
                        <form method="POST" >
                            <input type="hidden" name="page" value="bookings">
                            <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
                            <button type="submit" class="btn btn-dark btn-block">Bookings</button>
                        </form>
                    </li>
                    <li>
                        <form method="POST" >
                            <input type="hidden" name="page" value="turf_details">
                            <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
                            <button type="submit" class="btn btn-dark btn-block">Turf Details</button>
                        </form>
                    </li>
                </ul>
            </div>
            <?php include('../base/nav_dashboard.php');?>
            <!-- Content Area -->
            <div class="col-md-9 content">
                
                <?php
                
                // Handle PHP includes based on POST data
                if(isset($_POST['page'])) {
                    $page = $_POST['page'];
                    switch($page) {
                        case 'booking_details':
                            include( 'sidebar/booking_details.php');
                            break;
                        case 'analytics':
                            include 'sidebar/analytics.php';
                            break;
                        case 'bookings':
                            include 'sidebar/bookings.php';
                            break;
                        case 'turf_details':
                            include 'sidebar/turf_details.php';
                            break;
                        default:
                            include 'sidebar/booking_details.php';
                    }
                } else {
                    include 'sidebar/booking_details.php';
                }
                ?>
            </div>
        </div>
    </div>
    <script>
        // Get the content area
const content = document.querySelector('.content');

// Function to update the margin dynamically based on viewport height
function updateMargin() {
    const vh = window.innerHeight * 0.01; // Calculate 1vh
    const sidebarWidth = document.querySelector('.sidebar').offsetWidth; // Get sidebar width
    const marginValue = sidebarWidth + 16 + 'px'; // Calculate margin value
    content.style.marginLeft = marginValue; // Apply margin
}

// Call the function initially and on window resize
updateMargin();
window.addEventListener('resize', updateMargin);

    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    
</body>
</html>
