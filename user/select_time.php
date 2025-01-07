

<?php
include('check_login.php');
include('../base/db.php');

// Get date and turf ID from the URL parameters
$date_dmY = isset($_POST['date']) ? $_POST['date'] : date('d-m-Y');
$turf_id = isset($_POST['turf_id']) ? $_POST['turf_id'] : null;

// Convert date format from "d-m-Y" to "Y-m-d"
$date_Ymd = date('Y-m-d', strtotime($date_dmY));

// Fetch booked slots for the provided date and turf ID from the database
$bookedSlots = [];

    
    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT start_time, end_time FROM bookings WHERE turf_id = ? AND date = ?");
    $stmt->bind_param("is", $turf_id, $date_Ymd);
    $stmt->execute();
    
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $bookedSlots[] = $row;
        
    

    // Close the statement
    
}
$stmt->close();
// Convert the booked slots array to the required format for the table
$bookedSlotsFormatted = [];
foreach ($bookedSlots as $slot) {
    $bookedSlotsFormatted[] = [
        'start' => date('H:i', strtotime($slot['start_time'])),
        'end' => date('H:i', strtotime($slot['end_time']))
    ];
}

$date = new DateTime();

// Set the time to 00:00:00
$date->setTime(0, 0, 0);
$start = $date->format('H:i');
$currentDate = date('Y-m-d');

// Check if $date_Ymd is equal to the current date
if ($date_Ymd === $currentDate) {
  
  $bookedSlotsFormatted[] = [
    'start' => "00:00",
    'end' => date('H:i')
];
} 


?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Time Slot Selection</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      overflow: hidden;
      margin: 0;
    }

    .modal-content {
      height: calc(100vh - 40px);
    }

    .modal-body {
      overflow-y: auto;
    }

    .modal-footer {
      position: sticky;
      bottom: 0;
      background-color: #f8f9fa;
    }
    .modal-footer {
  display: flex;
  align-items: center;
}

#TimeBooked {
  margin-right: auto;
}


    table {
      border-collapse: collapse;
      width: 100%;
    }

    #time-error {
    color: red;
    display: block;
    margin-top: 5px;
}


    td {
      border: 1px solid #ddd;
      width: 50px; /* Adjust the width of the hour row */
      font-size: 10px;
      cursor: pointer;
      height: 2px;
      user-select: none;
    }

    td.hour-cell {
      width: 20px; /* Adjust the width of the hour cells */
    }

    td.selected {
      background-color: #aaf;
    }

    td.booked {
      background-color: #ff9999;
      cursor: not-allowed;
    }

    /* Additional styles for small screens and drag-and-drop */
    @media (max-width: 768px) {
      table {
        overflow-x: auto;
        white-space: nowrap;
      }

      td {
        white-space: nowrap;
      }

      td:hover {
        background-color: #f0f0f0;
      }

      td.draggable {
        cursor: grab;
      }

      td.dragging {
        background-color: #d9edf7;
      }

      
    }
  </style>
</head>
<body>
  <!-- Modal -->
  <div class="modal fade" id="timeSlotModal" tabindex="-1" role="dialog" aria-labelledby="timeSlotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="timeSlotModalLabel">Select Your Time Slot</h5>
          
        </div>
        <div class="modal-body">
          <div class="table-container">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Hour</th>
                  <th>Time Slot</th>
                </tr>
              </thead>
              <tbody>
              <?php
        $interval = 15;
        $hours_in_a_day = 24 * 60 / $interval;

        for ($i = 0; $i < $hours_in_a_day; $i++) {
            $start_time = sprintf("%02d:%02d", floor($i * $interval / 60), ($i * $interval) % 60);
            $end_time = sprintf("%02d:%02d", floor(($i + 1) * $interval / 60), (($i + 1) * $interval) % 60);

            // Check if the time slot is booked
            $isBooked = false;
            foreach ($bookedSlotsFormatted as $bookedSlot) {
                if (($start_time >= $bookedSlot['start'] && $start_time < $bookedSlot['end']) ||
                    ($end_time > $bookedSlot['start'] && $end_time <= $bookedSlot['end'])) {
                    $isBooked = true;
                    break;
                }
            }

            echo '<tr>';
            
            // Display hour with rowspan of 4
            if ($i % 4 == 0) {
                echo '<td rowspan="4" style="font-size: 14px;text-align: center;  vertical-align: middle;   align-items: center;
                justify-content: center;" class="hour-cell">' . sprintf("%02d:00", floor($i * $interval / 60)) . '</td>';
            }

            $cellClass = $isBooked ? 'booked' : 'time-slot-cell';
            echo '<td class="' . $cellClass . ' draggable" data-start-time="' . $start_time . '" data-end-time="' . $end_time . '"></td>';
            echo '</tr>';
        }
        ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
    <div style="flex-grow: 1;">
        <b><span id="TimeBooked"></span></b>
        <div><span id="totalTimeBooked"></span></div>
    </div>
    <div style="flex-grow: 1;">
        <button type="button" class="btn btn-primary" id="submitTimeSlot" disabled>Pay</button>
        <span id='time-error'></span>
    </div>
</div>

      </div>
    </div>
  </div>
  <?php
include('../base/db.php');

// Assuming you have already retrieved $turf_id from somewhere

// Prepare and execute the SQL query to fetch pricePerHour based on turf ID
$stmt = $conn->prepare("SELECT price FROM turf WHERE id = ?");
$stmt->bind_param("i", $turf_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if a row was returned
if ($result->num_rows > 0) {
    // Fetch the row and retrieve pricePerHour
    $row = $result->fetch_assoc();
    $pricePerHour = $row['price'];
} else {
    // Handle the case where no pricePerHour was found for the given turf ID
    $pricePerHour = 0; // Set a default value or handle the error as per your requirement
}

// Close the statement and database connection
$stmt->close();
$conn->close();


?>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
  <!-- Include your JavaScript code for time slot selection here -->
  <script>
  let isDragging = false;
  let startCell, endCell;
  const table = document.querySelector('table');
  const cells = document.querySelectorAll('.draggable');
  const tableContainer = document.querySelector('.table-container');
  const pricePerHour = <?= $pricePerHour ?>; // Get price per hour from PHP

  $(document).ready(function() {
    $('#timeSlotModal').modal({
      backdrop: 'static',
      keyboard: false
    });
  });

  document.addEventListener('mouseup', function() {
    isDragging = false;
    updateSelection();
    checkMinimumTimeSelected();
  });

  function handleStart(event) {
    event.preventDefault();
    isDragging = true;
    startCell = event.target;
    endCell = event.target;
    updateSelection();
    checkMinimumTimeSelected();
  }

  function handleMove(event) {
    if (isDragging) {
        const clientX = event.clientX || (event.touches && event.touches[0].clientX);
        const clientY = event.clientY || (event.touches && event.touches[0].clientY);
        const element = document.elementFromPoint(clientX, clientY);

        if (element && element.classList.contains('booked')) {
            isDragging = false;
            updateSelection();
            checkMinimumTimeSelected();
            return;
        }

        if (element && element.classList.contains('draggable')) {
            endCell = element;
            const threshold = 50;
            const rect = tableContainer.getBoundingClientRect();

            if (clientY > rect.bottom) {
                tableContainer.scrollTop += 10; // Scroll down
            } else if (clientY < rect.top) {
                tableContainer.scrollTop -= 10; // Scroll up
            }

            updateSelection();
            checkMinimumTimeSelected();
        }
    }
}


function smoothScroll(element, distance) {
  const start = element.scrollTop;
  const startTime = performance.now();
  const duration = 300; // Adjust the duration as needed (in milliseconds)

  function step(time) {
    const progress = (time - startTime) / duration;
    element.scrollTop = start + distance * easeInOutCubic(progress);

    if (progress < 1) {
      requestAnimationFrame(step);
    }
  }

  requestAnimationFrame(step);
}

function easeInOutCubic(t) {
  return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
}


  function handleEnd() {
    isDragging = false;
    const startTime = startCell.getAttribute('data-start-time');
    const endTime = endCell.getAttribute('data-end-time');

    if (compareTimes(startTime, endTime) > 0) {
      const temp = startCell;
      startCell = endCell;
      endCell = temp;
    }

    displaySelectedTimeRange();
    calculatePayment();
    SelectedTimeRange();
  }

  function updateSelection() {
    cells.forEach(cell => {
      const isSelected = isCellSelected(cell);
      cell.classList.toggle('selected', isSelected);
    });
  }

  function isCellSelected(cell) {
    const rowIndex = cell.parentElement.rowIndex;
    const startRowIndex = startCell.parentElement.rowIndex;
    const endRowIndex = endCell.parentElement.rowIndex;
    return rowIndex >= Math.min(startRowIndex, endRowIndex) && rowIndex <= Math.max(startRowIndex, endRowIndex);
  }

  function displaySelectedTimeRange() {
    const selectedTimeRangeElement = document.getElementById('totalTimeBooked');
    const startTime = startCell.getAttribute('data-start-time');
    const endTime = endCell.getAttribute('data-end-time');
    const totalTimeBooked = calculateTimeDifference(startTime, endTime);
    selectedTimeRangeElement.textContent = totalTimeBooked;
  }

  function SelectedTimeRange() {
  const TimeRangeElement = document.getElementById('TimeBooked');
  const startTime = startCell.getAttribute('data-start-time');
  const endTime = endCell.getAttribute('data-end-time');
  TimeRangeElement.textContent =formatTime(startTime) + ' - ' + formatTime(endTime);
}

function formatTime(time) {
  const [hours, minutes] = time.split(':').map(Number);
  const period = hours >= 12 ? 'PM' : 'AM';

  const formattedHours = hours % 12 === 0 ? 12 : hours % 12;
  const formattedMinutes = minutes.toString().padStart(2, '0');

  return `${formattedHours}:${formattedMinutes} ${period}`;
}

  function calculateTimeDifference(startTime, endTime) {
    const [startHour, startMinute] = startTime.split(':').map(Number);
    const [endHour, endMinute] = endTime.split(':').map(Number);
    const totalMinutes = (endHour - startHour) * 60 + (endMinute - startMinute);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return hours + ' hours ' + minutes + ' minutes';
  }

  function calculateTimeDifferencePayment(startTime, endTime) {
    const [startHour, startMinute] = startTime.split(':').map(Number);
    const [endHour, endMinute] = endTime.split(':').map(Number);
    const totalMinutes = (endHour - startHour) * 60 + (endMinute - startMinute);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return Math.abs(hours + (minutes/60));
  }

  function calculatePayment() {
    const startTime = startCell.getAttribute('data-start-time');
    const endTime = endCell.getAttribute('data-end-time');
    
    const totalHours = calculateTimeDifferencePayment(startTime, endTime)
    const paymentAmount = totalHours * pricePerHour;
    document.getElementById('submitTimeSlot').textContent = 'Pay ₹' + paymentAmount.toFixed(2);
    return paymentAmount;
  }

  function compareTimes(time1, time2) {
    const [hours1, minutes1] = time1.split(':').map(Number);
    const [hours2, minutes2] = time2.split(':').map(Number);

    if (hours1 < hours2) {
      return -1;
    } else if (hours1 > hours2) {
      return 1;
    } else {
      if (minutes1 < minutes2) {
        return -1;
      } else if (minutes1 > minutes2) {
        return 1;
      } else {
        return 0;
      }
    }
  }

  function checkMinimumTimeSelected() {
    const startTime = startCell.getAttribute('data-start-time');
    const endTime = endCell.getAttribute('data-end-time');
    const totalTimeBooked = calculateTimeDifferencePayment(startTime, endTime);
    const submitButton = document.getElementById('submitTimeSlot');
    submitButton.disabled = totalTimeBooked < 1;
    if(totalTimeBooked < 1){
      document.getElementById('time-error').textContent = "* Select Minimum of One hour";
    }else{
      document.getElementById('time-error').textContent = "";
    }
  }

  cells.forEach(cell => {
    cell.addEventListener('mousedown', handleStart);
    cell.addEventListener('mousemove', handleMove);
    cell.addEventListener('mouseup', handleEnd);
    cell.addEventListener('touchstart', handleStart);
    cell.addEventListener('touchmove', handleMove);
    cell.addEventListener('touchend', handleEnd);
  });

  document.getElementById('submitTimeSlot').addEventListener('click', function() {
    const startTime = startCell.getAttribute('data-start-time');
    const endTime = endCell.getAttribute('data-end-time');
    const totalTime = calculateTimeDifferencePayment(startTime, endTime);
    const turfId = <?= $turf_id ?>;
    const userId = <?= $_SESSION['user_id'] ?>; // Assuming user_id is stored in session
    const totalTimeText =  document.getElementById('totalTimeBooked').textContent;

    // Prepare the data to be sent
    const data = {
        date: '<?= $date_Ymd ?>',
        turf_id: turfId,
        user_id: userId,
        start_time: startTime,
        end_time: endTime,       
        total_time: totalTime,
        time_text: totalTimeText
    };

    // Create a form element
    const form = document.createElement('form');
    form.method = 'post';
    form.action = 'bill.php';

    // Append hidden input fields for each data item
    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        }
    }

    // Append the form to the document body and submit it
    document.body.appendChild(form);
    form.submit();
});


</script>

</body>
</html>
