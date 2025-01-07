<?php
include('check_login.php');
include('../base/db.php');

$user_id = $_SESSION['user_id'];

// Retrieve user's current orders from the database
$current_orders_query = "SELECT * FROM bookings WHERE user_id = $user_id AND (date > CURDATE() OR (date = CURDATE() AND end_time > NOW())) ORDER BY date ASC, end_time ASC";
$current_orders_result = $conn->query($current_orders_query);

// Retrieve user's completed orders from the database
$completed_orders_query = "SELECT * FROM bookings WHERE user_id = $user_id AND (date < CURDATE() OR (date = CURDATE() AND end_time <= NOW())) ORDER BY date DESC, end_time DESC";
$completed_orders_result = $conn->query($completed_orders_query);

// Retrieve user's canceled orders from the database
$canceled_orders_query = "SELECT turf_name,amount_refunded,booking_id, date, start_time, end_time FROM canceled_bookings WHERE user_id = $user_id ORDER BY date DESC";
$canceled_orders_result = $conn->query($canceled_orders_query);

// Function to calculate total hours between start and end time
function calculateTotalHours($start_time, $end_time)
{
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    $hours = ($end - $start) / (60 * 60);
    return $hours;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for the collapsible titles */
        .collapsible-title {
            font-size: 1.5rem;
            cursor: pointer;
            position: relative;
            padding-right: 30px; /* Adjust based on the icon size */
        }

        .collapsible-box {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .collapsible-arrow {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
        }

        .collapsed .collapsible-arrow::before {
            content: "-";
        }

        .collapsible-arrow::before {
            content: "+";
        }
    </style>
</head>

<body>
    
<?php include_once("../base/nav_user.php"); ?>
    <div class="container mt-5">
        <h1 class="text-center mb-5">My Orders</h1>
        <div class="collapsible-box">
            <h3 class="collapsible-title" data-bs-toggle="collapse" data-bs-target="#currentOrders" aria-expanded="true" aria-controls="currentOrders">
                Current Orders
                <span class="collapsible-arrow"></span>
            </h3>
            <div class="collapse show" id="currentOrders">
                <?php while ($row = $current_orders_result->fetch_assoc()) : ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $row['turf_name']; ?></h5>
                                    <p class="card-text">Total Hours: <?php echo calculateTotalHours($row['start_time'], $row['end_time']); ?></p>
                                    <p class="card-text">Date: <?php echo $row['date']; ?></p>
                                    <a href="view_order.php?booking_id=<?php echo $row['id']; ?>" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="collapsible-box">
            <h3 class="collapsible-title collapsed" data-bs-toggle="collapse" data-bs-target="#completedOrders" aria-expanded="false" aria-controls="completedOrders">
                Completed Orders
                <span class="collapsible-arrow"></span>
            </h3>
            <div class="collapse" id="completedOrders">
                <?php while ($row = $completed_orders_result->fetch_assoc()) : ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $row['turf_name']; ?></h5>
                                    <p class="card-text">Total Hours: <?php echo calculateTotalHours($row['start_time'], $row['end_time']); ?></p>
                                    <p class="card-text">Date: <?php echo $row['date']; ?></p>
                                    <a href="view_order.php?booking_id=<?php echo $row['id']; ?>" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        


        
        <div class="collapsible-box">
            <h3 class="collapsible-title collapsed" data-bs-toggle="collapse" data-bs-target="#canceledOrders" aria-expanded="false" aria-controls="canceledOrders">
                Canceled Orders
                <span class="collapsible-arrow"></span>
            </h3>
            
            <div class="collapse" id="canceledOrders">
            <?php while ($row = $canceled_orders_result->fetch_assoc()) : ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $row['turf_name']; ?></h5>
                                    <p class="card-text">
                                        <b>Date:</b> <?php echo $row['date']; ?>
                                    <br><b>Time: </b><?php echo date('H:i', strtotime($row['start_time'])); ?> - <?php echo date('H:i', strtotime($row['end_time'])); ?><br>
                                    <b>Amount Refunded : </b><?php echo $row['amount_refunded']; ?>
                                </p>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>

            </div>
            
        </div>
        






    </div>

    <?php include_once("../base/footer_user.php"); ?>
    
    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
