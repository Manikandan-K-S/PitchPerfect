<?php
include('check_login.php');
include('../base/db.php');


// Validate received data
// Validate received data
if (
    isset($_POST['turf_id']) && isset($_POST['user_id']) &&
    isset($_POST['date']) && isset($_POST['start_time']) &&
    isset($_POST['end_time']) && isset($_POST['total_time'])
) {
    // Retrieve data from POST method
    $turfId = $_POST['turf_id'];
    $userId = $_POST['user_id'];
    $date = $_POST['date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $timeText = $_POST['time_text'];
    $totalTime = $_POST['total_time']; // Already calculated as float representing time difference in hours

    // Validate date format
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj) {
        echo "Invalid date format";
        exit();
    }

    // Validate start and end time format
    // if (!preg_match('/^(0?[1-9]|1[0-2]):[0-5][0-9]$/', $startTime) || !preg_match('/^(0?[1-9]|1[0-2]):[0-5][0-9]$/', $endTime)) {
    //     echo "Invalid time format";
    //     exit();
    // }

    // Fetch turf details from database
    $stmt = $conn->prepare("SELECT name, price FROM turf WHERE id = ?");
    $stmt->bind_param("i", $turfId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row was returned
    if ($result->num_rows > 0) {
        // Fetch the row and retrieve turf name and price
        $row = $result->fetch_assoc();
        $turfName = $row['name'];
        $pricePerHour = $row['price'];

        // Calculate convenience fee
        $convenienceFee = $totalTime * 20; // Convenience fee of 20 Rs per hour

        // Check if there are no bookings in the given range for the specified date
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE turf_id = ? AND date = ? AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))");
        $stmt->bind_param("isssss", $turfId, $date, $startTime, $startTime, $endTime, $endTime);
        $stmt->execute();
        $bookingCountResult = $stmt->get_result();

        $bookingCountRow = $bookingCountResult->fetch_assoc();
        $bookingCount = $bookingCountRow['count'];

        if ($bookingCount == 0) {
            // No bookings in the given range for the specified date
            // Calculate total amount
            $totalAmount = $pricePerHour * $totalTime + $convenienceFee;
        } else {
            // There are bookings in the given range for the specified date
            print_r($_POST);
            echo "There are existing bookings for the specified time range.";
            exit();
        }
    } else {
        // Handle the case where no turf with the given ID was found
        echo "Turf not found";
        exit();
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();

} else {
    header("location:index.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.10/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    
<?php include_once("../base/nav_user.php"); ?>
    <div class="container mx-auto mt-4">
        <div class="card border border-gray-300 shadow-lg rounded-lg">
            <h2 class="card-title bg-gray-200 text-lg font-semibold p-4"><?php echo $turfName; ?></h2>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Date:</div>
                    <div class="col-sm-8"><?php echo $date; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Start Time:</div>
                    <div class="col-sm-8"><?php echo $startTime; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">End Time:</div>
                    <div class="col-sm-8"><?php echo $endTime; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Price per Hour:</div>
                    <div class="col-sm-8">₹<?php echo $pricePerHour; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Total Time:</div>
                    <div class="col-sm-8"><?php echo $timeText; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Convenience Fee (per hour):</div>
                    <div class="col-sm-8">₹20</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Total Convenience Fee:</div>
                    <div class="col-sm-8">₹<?php echo $convenienceFee; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Total Amount:</div>
                    <div class="col-sm-8">₹<?php echo $totalAmount; ?></div>
                </div>
                <!-- Continue to payment button -->
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">Continue to Payment</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for payment -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Select Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden input fields to store data -->
                    <form id="paymentForm" action="process_payment.php" method="post">

                        <!-- Hidden input fields to store data -->
                        <input type="hidden" name="turf_id" value="<?php echo $turfId; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                        <input type="hidden" name="date" value="<?php echo $date; ?>">
                        <input type="hidden" name="start_time" value="<?php echo $startTime; ?>">
                        <input type="hidden" name="end_time" value="<?php echo $endTime; ?>">
                        <input type="hidden" name="total_time" value="<?php echo $totalTime; ?>">
                        <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">
                        <input type="hidden" name="turf_name" value="<?php echo $turfName; ?>">
                        <!-- Radio buttons for payment method -->
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <input class="form-check-input me-1" type="radio" id="cardPayment" name="paymentMethod" value="Card Payment">
                                <label class="form-check-label" for="cardPayment">Card Payment</label>
                            </li>
                            <li class="list-group-item">
                                <input class="form-check-input me-1" type="radio" id="upiPayment" name="paymentMethod" value="UPI">
                                <label class="form-check-label" for="upiPayment">UPI</label>
                            </li>
                            <li class="list-group-item">
                                <input class="form-check-input me-1" type="radio" id="netbankingPayment" name="paymentMethod" value="NetBanking">
                                <label class="form-check-label" for="netbankingPayment">NetBanking</label>
                            </li>
                        </ul>
                        <br>
                        <button type="submit" id="completePaymentBtn" class="btn btn-primary" disabled>Complete Payment</button>
                    </form>

                    <script>
                        // Enable "Complete Payment" button when a payment method is selected
                        const paymentForm = document.getElementById('paymentForm');
                        const completePaymentBtn = document.getElementById('completePaymentBtn');
                        const paymentMethodInputs = document.querySelectorAll('input[name="paymentMethod"]');

                        paymentMethodInputs.forEach(input => {
                            input.addEventListener('change', function() {
                                completePaymentBtn.disabled = false;
                            });
                        });
                    </script>
                        <!-- Your payment form content goes here -->
                    
                </div>
            </div>
        </div>
    </div>

    <?php include_once("../base/footer_user.php"); ?>
    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>