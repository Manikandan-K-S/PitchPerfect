<?php
// Set cache control headers
header("Cache-Control: max-age=3600"); // Cache for 1 hour
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
?>
<?php
include('check_login.php');
include('../base/db.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve turf ID and selected date from form
    $turf_id = $_POST['turf_id'];
    $selected_date = $_POST['selected_date'];

    // Redirect to select_time.php with turf ID and selected date as POST parameters
    header("Location: select_time.php?turf_id=$turf_id&selected_date=$selected_date");
    exit;
}

// Retrieve turf ID from URL parameter
if (isset($_GET['id'])) {
    $turf_id = $_GET['id'];

    // Retrieve turf details from the database
    $query = "SELECT * FROM turf WHERE id = $turf_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Turf found, fetch turf details
        $turf = $result->fetch_assoc();

        // Close the result set
        $result->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Turf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
   <style>
    p{
        padding : 5px;
    }
   </style>
</head>
<body class="bg-gray-100">
<?php include_once("../base/nav_user.php"); ?>
<div class="container mx-auto py-10">
    <div class="max-w-7xl mx-auto">
        <div class="p-8 bg-white rounded-lg shadow-md">
            <h1 class="text-4xl font-semibold text-gray-800 mb-4"><?php echo $turf['name']; ?></h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="order-2 md:order-1">
                    <h3 class="text-2xl font-semibold mb-2">Turf Details</h3>
                    <p class="text-gray-700"><?php echo $turf['description']; ?></p>
                    <p class="text-gray-700"><b>Address:</b> <?php echo $turf['address']; ?></p>
                    <p class="text-gray-700"><b>Contact:</b> <?php echo $turf['contact']; ?></p>
                    <p class="text-gray-700"><b>Size:</b> <?php echo $turf['size']; ?> sq.ft</p>
                    <p class="text-gray-700"><b>Price:</b> ₹<?php echo $turf['price']; ?> per hour</p>
                    <p class="text-gray-700"><b>Capacity:</b> <?php echo $turf['capacity']; ?> people</p>
                    <p class="text-gray-700"><b>Amenities:</b> <?php echo $turf['amenities']; ?></p>
                </div>
                <div class="order-1 md:order-2">
                    <img class="w-full" src="<?php echo $turf['image1']; ?>" alt="Turf Image">
                    <div class="turf-details p-1 mt-4">
                      <h3 class="text-2xl font-semibold mb-4">Book Turf</h3>
                      <div class="flex flex-wrap items-center mb-3">
                      
                          <!-- <div style="padding-top: 20px;" > -->
                          <form action="select_time.php" method="post" class="mr-4">
                              <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
                              <input type="hidden" name="date" value="<?php echo date('d-m-Y'); ?>">
                              <button type="submit" name="selected_date" value="<?php echo date('d-m-Y'); ?>" class="px-4 py-2 bg-blue-500 text-white text-lg font-semibold rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Book Today</button>
                          </form>
                          <!-- </div> -->
                          <!-- <div style="padding-top: 20px;" > -->
                          <form action="select_time.php" method="post" class="mr-4">
                              <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
                              <input type="hidden" name="date" value="<?php echo date('d-m-Y', strtotime('+1 day')); ?>">
                              <button type="submit" name="selected_date" value="<?php echo date('d-m-Y', strtotime('+1 day')); ?>" class="px-4 py-2 bg-blue-500 text-white text-lg font-semibold rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Book Tomorrow</button>
                          </form>
                          <!-- </div> -->
                          
                          <div style="padding-top: 30px;">
                            <div class="flex flex-wrap items-center">
                                <label for="datepicker" class="block text-lg font-semibold mb-2 mr-4">Pick a Date:</label>
                                <form id="bookForm" action="select_time.php" method="post" class="flex items-center">
                                    <input type="text" autocomplete="off" id="datepicker" name="date" class="px-4 py-2 border rounded-lg mr-4">
                                    <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
                                    <button type="submit" id="bookButton" class="px-4 py-2 bg-blue-500 text-white text-lg font-semibold rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-600" disabled>Book</button>
                                </form>
                            </div>
                          </div>
                      </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(function() {
        var dateToday = new Date();
        var dayAfterTomorrow = new Date(dateToday);
        dayAfterTomorrow.setDate(dayAfterTomorrow.getDate() + 2);

        var nextWeek = new Date(dateToday);
        nextWeek.setDate(nextWeek.getDate() + 7);

        $("#datepicker").datepicker({
            minDate: dayAfterTomorrow,
            maxDate: nextWeek,
            dateFormat: 'dd-mm-yy', // Set the date format to d-m-Y
            onSelect: function(dateText) {
                $('#bookButton').prop('disabled', false);
                // Parse selected date and format it as d-m-Y
                
            }
        });

        // Function to format date as d-m-Y
        function formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            return day + '-' + month + '-' + year;
        }
    });
</script>

<?php include_once("../base/footer_user.php"); ?>
    

</body>
</html>
<?php
    } else {
        // Turf not found, display error message
        echo "<p>No turf found with ID: $turf_id</p>";
    }
} else {
    // ID parameter not provided in URL, display error message
    echo "<p>No turf ID specified</p>";
}
?>
