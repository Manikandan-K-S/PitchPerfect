<?php
// Include necessary files and initialize session
include('../login_check.php');
include($_SERVER['DOCUMENT_ROOT'] . '/pitchperfect/base/db.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/pitchperfect/vendor/autoload.php'; // Include Composer autoloader for libraries

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spipu\Html2Pdf\Html2Pdf;

// Check if format, turf_id, start_date, and end_date are provided via GET
if (isset($_GET['format'], $_GET['turf_id'], $_GET['start_date'], $_GET['end_date'])) {
    $format = $_GET['format'];
    $turf_id = $_GET['turf_id'];
    $start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : null;

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
                ORDER BY b.start_time";

    $result = $conn->query($query);

    if ($result) {
        switch ($format) {
            case 'csv':
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="booking_details.csv"');
                generateCsv($result);
                break;
            case 'xlsx':
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="booking_details.xlsx"');
                generateXlsx($result);
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="booking_details.pdf"');
                
                generatePdf($result);
                break;
            default:
                // Invalid format, handle as needed
                break;
        }
    } else {
        // Handle database query error
    }
} else {
    // Required parameters are missing, handle as needed
}

// Function to generate and output CSV content
function generateCsv($result)
{
    $output = fopen('php://output', 'w');
    $headers = array('Booking ID', 'Date', 'Start Time', 'End Time', 'Total Time', 'Username', 'Amount Collected');
    fputcsv($output, $headers);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
}

// Function to generate and output XLSX content
function generateXlsx($result)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Booking ID');
    $sheet->setCellValue('B1', 'Date');
    $sheet->setCellValue('C1', 'Start Time');
    $sheet->setCellValue('D1', 'End Time');
    $sheet->setCellValue('E1', 'Total Time');
    $sheet->setCellValue('F1', 'Username');
    $sheet->setCellValue('G1', 'Amount Collected');
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['booking_id']);
        $sheet->setCellValue('B' . $row, $data['date']);
        $sheet->setCellValue('C' . $row, $data['start_time']);
        $sheet->setCellValue('D' . $row, $data['end_time']);
        $sheet->setCellValue('E' . $row, $data['total_time']);
        $sheet->setCellValue('F' . $row, $data['username']);
        $sheet->setCellValue('G' . $row, $data['amount_collected']);
        $row++;
    }
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
}

// Function to generate and download PDF content
// Function to generate and download PDF content
function generatePdf($result)
{
    $rows = []; // Initialize an empty array to store rows

    // Fetch all rows from the result set
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row; // Append each row to the array
    }

    $start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = !empty($_GET['end_date']) ? $_GET['end_date'] : null;
    include($_SERVER['DOCUMENT_ROOT'] . '/pitchperfect/base/db.php');

    // Fetch turf name
    $turf_name = false;
    if (isset($_GET['turf_id'])) {
        $turf_id = $_GET['turf_id'];

        // Query to fetch turf name based on turf_id
        $query = "SELECT name FROM turf WHERE id = $turf_id";

        // Execute the query
        $result = $conn->query($query);

        // Check if query execution was successful
        if ($result) {
            // Fetch the turf name
            $row = $result->fetch_assoc();
            $turf_name = $row['name'];
        } else {
            // Error handling if query execution fails
            echo "Error: " . $conn->error;
        }

        // Close the database connection
        $conn->close();
    }

    ob_start();
    ?>
    <html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; }
            th { background-color: #f2f2f2; }
            .center { text-align: center; margin: auto; }
        </style>
    </head>
    <body>
        <div class="center">
            <?php 
            if($turf_name) {
                echo "<h1>$turf_name</h1>";
            }
            if($start_date && $end_date){
                echo "<h4>Date Range: $start_date to $end_date</h4><br>";
            } else {
                echo "<p>Overall Data</p>";
            }
            ?>
            <table class="table table-bordered center table-striped">
                <thead class="thead-light">
                    <tr>
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
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo $row['booking_id']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['start_time']; ?></td>
                            <td><?php echo $row['end_time']; ?></td>
                            <td><?php echo $row['total_time']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['amount_collected']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    
     // Set HTTP headers for download
     header('Content-Type: application/pdf');
     header('Content-Disposition: attachment; filename="booking_details.pdf"');

    try {
        // Generate PDF using Html2Pdf
        $pdf = new Html2Pdf();
        $pdf->writeHTML($html);
        $pdf->output();
    } catch (Html2PdfException $e) {
        echo $e;
    }
}
