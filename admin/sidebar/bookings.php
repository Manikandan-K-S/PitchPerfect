<?php
// Include necessary files and initialize session
include('login_check.php');
include($_SERVER['DOCUMENT_ROOT'] . '/pitchperfect/base/db.php');
require_once '../vendor/autoload.php'; // Include Composer autoloader for libraries

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spipu\Html2Pdf\Html2Pdf;

// Check if turf_id is provided via POST
if (isset($_POST['turf_id'])) {
    $turf_id = $_POST['turf_id'];

    // Retrieve start and end dates if provided
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

    // Initialize the WHERE clause for the query
    $whereClause = "WHERE b.turf_id = $turf_id";

    // Add start and end dates to the WHERE clause if provided
    if ($start_date && $end_date) {
        $whereClause .= " AND b.date BETWEEN '$start_date' AND '$end_date'";
    }

    // Query to retrieve bookings for the specified turf within the given date range
    $query = "SELECT b.id AS booking_id, b.date, b.start_time, b.end_time, b.time AS total_time, 
                IFNULL(u.name, 'Admin') AS username, b.price AS amount_collected
                FROM bookings b
                LEFT JOIN user u ON b.user_id = u.id
                $whereClause
                ORDER BY b.id";


    $result = $conn->query($query);

    // Create an array to store booking data
    $bookings = [];

    // Fetch bookings and store in array
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
}
?>
<style>
    .content-down {
    padding-bottom: 30px; /* Adjust as needed */
}
</style>
<div class="container mx-auto">
    <!-- Display table with booking details -->
    <h1 class="text-4xl font-bold text-center my-4">Bookings</h1>
    <div class="flex justify-center">
        <form method="POST" class="flex items-center space-x-4">
            <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
            <input type="hidden" name="page" value="bookings">
            <div class="flex items-center">
                <label for="start_date" class="block mr-2">Start Date:</label>
                <input type="date" id="start_date" name="start_date" class="form-input px-4 py-2 w-64" required>
            </div>
            <div class="flex items-center">
                <label for="end_date" class="block mr-2">End Date:</label>
                <input type="date" id="end_date" name="end_date" class="form-input px-4 py-2 w-64" required>
            </div>
            <button type="submit" class="btn btn-primary px-4 py-2">Submit</button>
        </form>
    </div>
</div>
<br>

    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Booking ID</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Time</th>
                <th>Username</th>
                <th>Amount Collected</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($bookings)) {
                $sno = 1;
                foreach ($bookings as $booking) {
                    echo "<tr>";
                    echo "<td>{$sno}</td>";
                    echo "<td>{$booking['booking_id']}</td>";
                    echo "<td>{$booking['date']}</td>";
                    echo "<td>{$booking['start_time']}</td>";
                    echo "<td>{$booking['end_time']}</td>";
                    echo "<td>{$booking['total_time']}</td>";
                    echo "<td>{$booking['username']}</td>";
                    echo "<td>{$booking['amount_collected']}</td>";
                    echo "</tr>";
                    $sno++;
                }
            } else {
                echo "<tr><td colspan='8'>No bookings found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Download options -->
   
<div class="mt-2">
    <p>Download all the booking details in:</p>
    <div class="btn-group" role="group" aria-label="Download Options">
        <a href="sidebar/download.php?format=csv&turf_id=<?php echo $turf_id; ?>&start_date=<?php echo $start_date ?? ''; ?>&end_date=<?php echo $end_date ?? ''; ?>" class="btn btn-secondary" role="button" download>CSV</a>
        <a href="sidebar/download.php?format=xlsx&turf_id=<?php echo $turf_id; ?>&start_date=<?php echo $start_date ?? ''; ?>&end_date=<?php echo $end_date ?? ''; ?>" class="btn btn-secondary" role="button" download>XLSX</a>
        <a href="sidebar/download.php?format=pdf&turf_id=<?php echo $turf_id; ?>&start_date=<?php echo $start_date ?? ''; ?>&end_date=<?php echo $end_date ?? ''; ?>" class="btn btn-secondary" role="button" download>PDF</a>
    </div>
</div>

</div>
<div class="content-down">
    <!-- Content above the footer -->
</div>
