<style>
    .timeline-1 {
  border-left: 3px solid #555;
  border-bottom-right-radius: 4px;
  border-top-right-radius: 4px;
  background: #fff;
  margin: 0 auto;
  position: relative;
  padding: 50px;
  list-style: none;
  text-align: left;
  max-width: 40%;
}

@media (max-width: 767px) {
  .timeline-1 {
    max-width: 98%;
    padding: 25px;
  }
}

.timeline-1 .event {
  border-bottom: 1px dashed #000;
  padding-bottom: 25px;
  margin-bottom: 25px;
  position: relative;
}

@media (max-width: 767px) {
  .timeline-1 .event {
    padding-top: 30px;
  }
}

.timeline-1 .event:last-of-type {
  padding-bottom: 0;
  margin-bottom: 0;
  border: none;
}

.timeline-1 .event:before,
.timeline-1 .event:after {
  position: absolute;
  display: block;
  top: 0;
}

.timeline-1 .event:before {
  left: -207px;
  content: attr(data-date);
  text-align: right;
  font-weight: 100;
  font-size: 0.9em;
  min-width: 120px;
}

@media (max-width: 767px) {
  .timeline-1 .event:before {
    left: 0px;
    text-align: left;
  }
}

.timeline-1 .event:after {
  -webkit-box-shadow: 0 0 0 3px #b565a7;
  box-shadow: 0 0 0 3px #b565a7;
  left: -55.8px;
  background: #fff;
  border-radius: 50%;
  height: 9px;
  width: 9px;
  content: "";
  top: 5px;
}
body {
    overflow-x: hidden; /* Hide horizontal scrollbar */
}
@media (max-width: 767px) {
  .timeline-1 .event:after {
    left: -31.8px;
  }
}
</style>
   

<?php
// Include login check and database connection
include('login_check.php');
include($_SERVER['DOCUMENT_ROOT'] . '/pitchperfect/base/db.php');

if (isset($_POST['date'])) {
    $date = $_POST['date'];
    // You may need to validate and sanitize the date input here
    // ...
} else {
    $date = date('Y-m-d');
}

$query = "SELECT b.id, b.start_time, b.end_time, IFNULL(u.name, 'Admin') AS customer_name, IFNULL(u.mobile, 'N/A') AS customer_mobile
          FROM bookings b
          LEFT JOIN user u ON b.user_id = u.id
          WHERE b.date = '$date' and b.turf_id=$turf_id
          ORDER BY b.start_time";

$result = $conn->query($query);
$newDate = date("d.m.Y", strtotime($date));

?>


    <!-- Heading for the buttons with dynamic date -->
<h2 class="text-3xl font-semibold mb-4 text-center">Booking Details - <?php echo $newDate; ?></h2>

    <!-- Buttons for searching bookings -->
<div class="flex justify-between items-center mb-4">
    <!-- Buttons for Today and Tomorrow -->
    <div class="flex">
        <form method="POST"  class="mr-2">
            <input type="hidden" name="page" value="booking_details">
            <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
            <button type="submit" name="date" value="<?php echo date('Y-m-d'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-1 px-2 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">Today</button>
        </form>
        <form method="POST" >
            <input type="hidden" name="page" value="booking_details">
            <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
            <button type="submit" name="date" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-1 px-2 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">Tomorrow</button>
        </form>
    </div>
    <!-- Date Picker --><div style='padding: 13px;'> <!-- Corrected style attribute -->
    <form action="" method="POST" > <!-- Added ml-2 class for left margin -->
        <input type="hidden" name="page" value="booking_details">
        <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
        <input type="date" name="date" class="border border-gray-300 p-1 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent">
        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-1 px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">Search</button>
    </form>
</div>


</div>

<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div id="content">
                <ul class="timeline-1 text-black">
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $startTime = date('h:i A', strtotime($row['start_time']));
                            $endTime = date('h:i A', strtotime($row['end_time']));
                            $customerName = $row['customer_name'];
                            $customerMobile = $row['customer_mobile'];
                            $bid = $row['id'];
                    ?>
                            <li class="event" data-date="<?php echo "$startTime - $endTime"; ?>">
                                <h3 class="mb-3"><b>Booking ID :</b> <?=$bid?></h3>
                                <p class="mb-3"><b>Name : </b><?php echo $customerName; ?><p>
                                <p><b>Mobile : </b><?php echo "$customerMobile"; ?></p>
                            </li>
                    <?php
                        }
                    } else {
                        // No bookings found
                        echo "<li class='event'>No bookings found for today.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
