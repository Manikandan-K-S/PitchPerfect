<?php
include('check_login.php');
include('../base/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if booking ID is provided in the URL
if (!isset($_GET['booking_id'])) {
    echo "Booking ID is missing.";
    exit();
}

// Retrieve booking ID from the URL
$bookingId = $_GET['booking_id'];

// Query to check if the booking ID belongs to the logged-in user
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $bookingId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Check if the booking ID belongs to the logged-in user
if ($result->num_rows == 0) {
    echo "Unauthorized access to booking information.";
    exit();
}

// Fetch booking details
$booking = $result->fetch_assoc();

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.10/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for responsiveness and styling */
        @media (max-width: 576px) {
            .card {
                width: 90%;
            }
        }

        @media (min-width: 576px) {
            .card {
                width: 80%;
            }
        }

        @media (min-width: 768px) {
            .card {
                width: 70%;
            }
        }

        @media (min-width: 992px) {
            .card {
                width: 60%;
            }
        }

        @media (min-width: 1200px) {
            .card {
                width: 50%;
            }
        }

        .qr-code {
            width: 200px;
            height: auto;
        }

        /* CSS styles for printing */
        @media print {
            body * {
                visibility: hidden;
            }

            #printableArea, #printableArea * {
                visibility: visible;
            }

            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .card {
                width: 100%;
                box-shadow: none;
                border: none;
            }

            .card-title {
                background-color: #f3f4f6;
                font-size: 1.25rem;
                font-weight: bold;
                padding: 1rem;
                margin: 0;
            }

            .card-body {
                padding: 1rem;
                margin: 0;
            }

            .card-body .row {
                margin-bottom: 0.75rem;
            }

            .card-body .row .col-sm-4 {
                font-weight: bold;
            }

            .card-footer {
                background-color: #f3f4f6;
                padding: 1rem;
                margin: 0;
            }

            .qr-code {
                width: 100%;
            }

            .btn-container {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    
<?php include_once("../base/nav_user.php"); ?>
    <div class="container mx-auto mt-4">
        <!-- Display Booking Successful message -->
        <div class="alert alert-success" role="alert">
            <h3 align="center">Booking Successful!</h3>
        </div>

        <!-- Booking Details Card -->
        <div class="card border border-gray-300 shadow-lg rounded-lg mx-auto" id="printableArea">
            <h2 class="card-title bg-gray-200 text-lg font-semibold p-4">Booking Details</h2>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Turf Name:</div>
                    <div class="col-sm-8"><?php echo $booking['turf_name']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Date:</div>
                    <div class="col-sm-8"><?php echo $booking['date']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Start Time:</div>
                    <div class="col-sm-8"><?php echo $booking['start_time']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">End Time:</div>
                    <div class="col-sm-8"><?php echo $booking['end_time']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Payment Method:</div>
                    <div class="col-sm-8"><?php echo $booking['payment_method']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Total Amount:</div>
                    <div class="col-sm-8"><?php echo $booking['price']; ?> Rs</div>
                </div>
            </div>
            <!-- QR Code Section -->
            <div class="card-footer text-center">
                <div class="row">
                    <div class="col-sm-6">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo urlencode($bookingId); ?>&amp;size=200x200" alt="QR Code" class="qr-code">
                    </div>
                    <div class="col-sm-6">
                        <!-- Display booking ID below QR Code -->
                        <p>Booking ID: <?php echo $bookingId; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Buttons -->
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary mx-2">Go to Home</a>
            <button class="btn btn-primary mx-2" id='print' onClick="printdiv('printableArea')">Print Bill</button>
        </div>
    </div>
    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    

    <script>
    function printdiv(divID) {
    var printContents = document.getElementById(divID).outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>


    
    <?php include_once("../base/footer_user.php"); ?>
    
</body>

</html>
