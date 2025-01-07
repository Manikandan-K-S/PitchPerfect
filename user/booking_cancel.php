<?php
// Connect to the database
include('check_login.php');
include('../base/db.php');

// Check if the connection is successful
if (!$conn) {
    echo "Database connection failed.";
    exit();
}

// Check if booking ID is provided via POST
if (!isset($_POST['booking_id'])) {
    echo "Booking ID is missing.";
    exit();
}

// Retrieve booking ID from the POST data
$bookingId = $_POST['booking_id'];

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

// Retrieve booking details from the bookings table
$query = "SELECT * FROM bookings WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

// Calculate the refundable amount based on terms and conditions
$refundableAmount = 0;
$bookingDate = strtotime($booking['date']);
$currentDate = time();
$daysDifference = floor(($bookingDate - $currentDate) / (60 * 60 * 24));

if ($daysDifference >= 2) {
    // Calculate 75% refund
    $refundableAmount = ($booking['price'] - (20 * $booking['time'])) * 0.75;
} else {
    // Calculate 50% refund
    $refundableAmount =  ($booking['price'] - (20 * $booking['time'])) * 0.5;
} 


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_confirm'])) {
    // Assume you have a function to validate and sanitize input
    
    // Retrieve booking details from the bookings table
    $query = "SELECT * FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $bookingId);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    if ($booking) {
        // Insert the booking details into the canceled_bookings table
        $insertQuery = "INSERT INTO canceled_bookings (booking_id, user_id, date, start_time, end_time, price, time, payment_method, turf_name, amount_refunded, cancellation_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param('iisssssssss', $booking['id'], $booking['user_id'], $booking['date'], $booking['start_time'], $booking['end_time'], $booking['price'], $booking['time'], $booking['payment_method'], $booking['turf_name'], $amountRefunded, $cancellationReason);
        
        // Set the initial values for amount refunded and cancellation reason
        $amountRefunded = $_POST["refund"];
        $cancellationReason = $_POST["cancellation_reason"];
        
        $insertStmt->execute();

        // Delete the booking from the bookings table
        $deleteQuery = "DELETE FROM bookings WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param('i', $bookingId);
        
        if ($deleteStmt->execute()) {
            // Send a success response
            echo '<script>
                alert("Cancelled Successfully!!");
                window.location.href = "orders.php";
            </script>';
            exit;
        } else {
            // Send an error response
            echo json_encode(array("success" => false));
            exit;
        }
    } else {
        // Send an error response if the booking does not exist
        echo json_encode(array("success" => false, "message" => "Booking not found"));
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-5">
        <div class="max-w-md mx-auto bg-white rounded p-5 shadow-md">
            <h1 class="text-2xl font-semibold mb-3">Cancel Booking</h1>
            <p class="mb-3">Please review the terms and conditions for cancellation before proceeding:</p>
            <ul class="list-disc pl-5 mb-5">
                <li class="mb-2">Convenience fee is not returned.</li>
                <li class="mb-2">Only 50% refund if cancelled two days before the booking date.</li>
                <li class="mb-2">75% refund if cancellation is made more than two days before the booking date.</li>
            </ul>
            <p class="mb-3">Total amount refundable: ₹<?php echo number_format($refundableAmount, 2); ?></p>
            <form id="cancelForm" method="post" action="booking_cancel.php">
                <input type="hidden" name="booking_id" value="<?php echo $bookingId; ?>">
                <button id="confirmCancellationBtn" type="button" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Confirm Cancellation</button>
            </form>
        </div>
    </div>

    <!-- Modal for cancellation confirmation -->
    <div id="cancelModal" class="fixed top-0 left-0 w-full h-full bg-opacity-75 bg-gray-900 flex items-center justify-center z-50 hidden backdrop-filter backdrop-blur-lg">
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full h-auto">
        <div class="bg-gray-900 text-white px-4 py-2">
            <h2 class="text-xl font-semibold">Confirm Cancellation</h2>
        </div>
        <div class="p-4">
            <form id="cancelFormModal" method="post" action="booking_cancel.php">
                <input type="hidden" name="booking_id" value="<?php echo $bookingId; ?>">
                <input type="hidden" name="refund" value="<?php echo $refundableAmount; ?>">
                <input type="hidden" name="cancel_confirm" value="True">
                <textarea id="cancellationReason" name="cancellation_reason" class="w-full mt-2 p-2 border rounded-md"></textarea>
                <div class="mt-4 flex justify-end">
                    <button id="cancelConfirmBtn" type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Confirm</button>
                    <button id="cancelCloseBtn" type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md ml-2">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <!-- JavaScript -->
    <script>
        // Show modal on clicking confirm cancellation button
        document.getElementById('confirmCancellationBtn').addEventListener('click', function() {
            document.getElementById('cancelModal').classList.remove('hidden');
        });

        // Close modal on clicking close button
        document.getElementById('cancelCloseBtn').addEventListener('click', function() {
            document.getElementById('cancelModal').classList.add('hidden');
        });

        // Handle cancellation confirmation
        document.getElementById('cancelConfirmBtn').addEventListener('click', function() {
            // Get the reason for cancellation
            var cancellationReason = document.getElementById('cancellationReason').value;

            // Append cancellation reason to form data
            var form = document.getElementById('cancelFormModal');
            var formData = new FormData(form);
            formData.append('cancellation_reason', cancellationReason);

            // Submit the form
            form.submit();
        });
    </script>
</body>
</html>
