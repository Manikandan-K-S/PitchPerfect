<?php
include('check_login.php');
include('../base/db.php');


// Validate received data
if (
    isset($_POST['turf_id']) && isset($_POST['user_id']) &&
    isset($_POST['date']) && isset($_POST['start_time']) &&
    isset($_POST['end_time']) && isset($_POST['total_time']) &&
    isset($_POST['paymentMethod'])  && isset($_POST['total_amount']) 
) {
    // Retrieve data from POST method
    $turfId = $_POST['turf_id'];
    $userId = $_POST['user_id'];
    $date = $_POST['date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $totalTime = $_POST['total_time']; // Already calculated as float representing time difference in hours
    $paymentMethod = $_POST['paymentMethod'];
    $totalAmount = $_POST['total_amount'];
    $turfName = $_POST['turf_name'];

    // Validate date format
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj) {
        echo "Invalid date format";
        exit();
    }

    // Validate start and end time format
    // if (!preg_match('/^(0?[1-9]|2[0-4]):[0-5][0-9]$/', $startTime) || !preg_match('/^(0?[1-9]|1[0-2]):[0-5][0-9]$/', $endTime)) {
    //     echo "Invalid time format";
    //     exit();
    // }

    // Check if the given time range is available and no other time is collapsed
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE turf_id = ? AND date = ? AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))");
    $stmt->bind_param("isssss", $turfId, $date, $startTime, $startTime, $endTime, $endTime);
    $stmt->execute();
    $bookingCountResult = $stmt->get_result();

    $bookingCountRow = $bookingCountResult->fetch_assoc();
    $bookingCount = $bookingCountRow['count'];

    if ($bookingCount == 0) {
        // No bookings in the given range for the specified date
        // Insert data into bookings table
        $stmt = $conn->prepare("INSERT INTO bookings (turf_id, user_id, date, start_time, end_time, payment_method,price,time,turf_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssdds", $turfId, $userId, $date, $startTime, $endTime, $paymentMethod, $totalAmount,$totalTime,$turfName);
        $stmt->execute();
         // Get the booking ID
         $bookingId = $stmt->insert_id;

         // Close the statement
         $stmt->close();
 
         // Redirect to booking_success.php with booking ID
         header("Location: booking_success.php?booking_id=$bookingId");
         exit();
        echo "Booking successful!";
    } else {
        // There are existing bookings in the given range for the specified date
        echo "The selected time range is not available.";
    }

    // Close the statement and database connection
    
    $conn->close();

} else {
    echo "Invalid request";
    exit();
}
?>

