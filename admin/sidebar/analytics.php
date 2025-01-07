<?php
// Connect to the database
include('login_check.php');
include($_SERVER['DOCUMENT_ROOT'] . '/pitchperfect/base/db.php');
// Check connection

?>
<style>
    p{
        padding : 5px;
    }
   </style>

<?php
// Check if turfId is set
if(isset($_POST['turf_id'])) {
    $turfId = $_POST['turf_id'];

    // Connect to the database
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $query = "SELECT COUNT(*) AS total_bookings, SUM(price) AS total_revenue, 
              MAX(price) AS max_price, MIN(price) AS min_price, 
              AVG(price) AS avg_price, SUM(time) AS total_hours 
              FROM bookings WHERE turf_id = $turfId";

    // Check if start date and end date are provided
    if(isset($_POST['start_date']) && isset($_POST['end_date'])) {
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];

        // Add where clause for the date range
        $query .= " AND date BETWEEN '$startDate' AND '$endDate'";
    }

    // Execute the query
    $result = $conn->query($query);

    // Fetch the result
    $row = $result->fetch_assoc();
    $totalBookings = $row['total_bookings'];
    $totalRevenue = $row['total_revenue'];
    $maxPrice = $row['max_price'];
    $minPrice = $row['min_price'];
    $avgPrice = $row['avg_price'];
    $totalHours = $row['total_hours'];

    // Fetch total number of distinct users
    $sqlTotalUsers = "SELECT COUNT(DISTINCT user_id) AS total_users FROM bookings WHERE turf_id = $turfId";
    // Add where clause for the date range
    if(isset($_POST['start_date']) && isset($_POST['end_date'])) {
        $sqlTotalUsers .= " AND date BETWEEN '$startDate' AND '$endDate'";
    }
    $resultTotalUsers = $conn->query($sqlTotalUsers);
    $rowTotalUsers = $resultTotalUsers->fetch_assoc();
    $totalUsers = $rowTotalUsers['total_users'];

    // Fetch count of bookings by payment method
    $sqlPaymentMethodCount = "SELECT payment_method, COUNT(*) AS count FROM bookings WHERE turf_id = $turfId GROUP BY payment_method";
    // Add where clause for the date range
    if(isset($_POST['start_date']) && isset($_POST['end_date'])) {
        $sqlPaymentMethodCount .= " AND date BETWEEN '$startDate' AND '$endDate'";
    }
    $resultPaymentMethodCount = $conn->query($sqlPaymentMethodCount);
    $cardPaymentCount = 0;
    $upiCount = 0;
    $netBankingCount = 0;
    while ($row = $resultPaymentMethodCount->fetch_assoc()) {
        if ($row['payment_method'] == 'Card Payment') {
            $cardPaymentCount = $row['count'];
        } elseif ($row['payment_method'] == 'UPI') {
            $upiCount = $row['count'];
        } elseif ($row['payment_method'] == 'NetBanking') {
            $netBankingCount = $row['count'];
        }
    }

    // Fetch average booking price by weekday
    $sqlAveragePriceByWeekday = "SELECT WEEKDAY(date) AS weekday, AVG(price) AS average_price FROM bookings WHERE turf_id = $turfId GROUP BY WEEKDAY(date)";
    // Add where clause for the date range
    if(isset($_POST['start_date']) && isset($_POST['end_date'])) {
        $sqlAveragePriceByWeekday .= " AND date BETWEEN '$startDate' AND '$endDate'";
    }
    $resultAveragePriceByWeekday = $conn->query($sqlAveragePriceByWeekday);
    $averagePriceByWeekday = [];
    while ($row = $resultAveragePriceByWeekday->fetch_assoc()) {
        $averagePriceByWeekday[$row['weekday']] = $row['average_price'];
    }

    // Fetch average number of bookings by weekday
    $sqlAverageBookingsByWeekday = "SELECT WEEKDAY(date) AS weekday, COUNT(*) AS average_bookings FROM bookings WHERE turf_id = $turfId GROUP BY WEEKDAY(date)";
    // Add where clause for the date range
    if(isset($_POST['start_date']) && isset($_POST['end_date'])) {
        $sqlAverageBookingsByWeekday .= " AND date BETWEEN '$startDate' AND '$endDate'";
    }
    $resultAverageBookingsByWeekday = $conn->query($sqlAverageBookingsByWeekday);
    $averageBookingsByWeekday = [];
    while ($row = $resultAverageBookingsByWeekday->fetch_assoc()) {
        $averageBookingsByWeekday[$row['weekday']] = $row['average_bookings'];
    }

    // Close connection
    $conn->close();
} else {
    // turfId not set, handle the error or provide a default behavior
    echo "Turf ID not provided.";
}
?>


<div class="container mx-auto">
    <!-- Display table with booking details -->
    <h1 class="text-4xl font-bold text-center my-4">Analytics</h1>
    <div class="flex justify-center">
        <form method="POST" class="flex items-center space-x-4">
            <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
            <input type="hidden" name="page" value="analytics">
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


<!-- Analytics -->
<div class="container mx-auto mt-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border border-gray-300 rounded-md p-4">
                <h2 class="text-xl font-semibold mb-3">User Statistics</h2>
                <p>Total Number of Users: <?php echo $totalUsers; ?></p><br><br>
                <h2 class="text-xl font-semibold mb-3">Booking Statistics</h2>
                <p>Total Bookings: <?php echo $totalBookings; ?></p>
                <p>Total Revenue: ₹<?php echo number_format($totalRevenue, 2); ?></p>
                <p>Maximum Price: ₹<?php echo number_format($maxPrice, 2); ?></p>
                <p>Minimum Price: ₹<?php echo number_format($minPrice, 2); ?></p>
                <p>Average Price: ₹<?php echo number_format($avgPrice, 2); ?></p>
                <p>Total Hours Booked: <?php echo $totalHours; ?></p>
            </div>
            <div class="border border-gray-300 rounded-md p-4">
                <h2 class="text-xl font-semibold mb-3">Payment Method Distribution</h2>
                <canvas id="paymentMethodChart" width="400" height="400"></canvas>
            </div>
            <div class="border border-gray-300 rounded-md p-4 col-span-2">
                <h2 class="text-xl font-semibold mb-3">Average Booking Price by Weekday</h2>
                <canvas id="averagePriceByWeekdayChart" width="400" height="400"></canvas>
            </div>
            <div class="border border-gray-300 rounded-md p-4 col-span-2">
                <h2 class="text-xl font-semibold mb-3">Average Number of Bookings by Weekday</h2>
                <canvas id="averageBookingsByWeekdayChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Data for payment method distribution (example)
        var paymentMethodData = {
            labels: ['Card Payment', 'UPI', 'NetBanking'],
            datasets: [{
                label: 'Payment Method Distribution',
                data: [<?php echo $cardPaymentCount; ?>, <?php echo $upiCount; ?>, <?php echo $netBankingCount; ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Options for payment method distribution chart
        var paymentMethodOptions = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Create payment method distribution chart
        var ctx1 = document.getElementById('paymentMethodChart').getContext('2d');
        var paymentMethodChart = new Chart(ctx1, {
            type: 'pie',
            data: paymentMethodData,
            options: paymentMethodOptions
        });

        // Data for average booking price by weekday (example)
        var averagePriceByWeekdayData = {
            labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            datasets: [{
                label: 'Average Booking Price by Weekday',
                data: [<?php echo implode(',', $averagePriceByWeekday); ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        // Options for average booking price by weekday chart
        var averagePriceByWeekdayOptions = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Create average booking price by weekday chart
        var ctx2 = document.getElementById('averagePriceByWeekdayChart').getContext('2d');
        var averagePriceByWeekdayChart = new Chart(ctx2, {
            type: 'bar',
            data: averagePriceByWeekdayData,
            options: averagePriceByWeekdayOptions
        });

        // Data for average number of bookings by weekday (example)
        var averageBookingsByWeekdayData = {
            labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            datasets: [{
                label: 'Average Number of Bookings by Weekday',
                data: [<?php echo implode(',', $averageBookingsByWeekday); ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };

        // Options for average number of bookings by weekday chart
        var averageBookingsByWeekdayOptions = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };
        var ctx3 = document.getElementById('averageBookingsByWeekdayChart').getContext('2d');
var averageBookingsByWeekdayChart = new Chart(ctx3, {
type: 'bar',
data: averageBookingsByWeekdayData,
options: averageBookingsByWeekdayOptions
});
</script>

