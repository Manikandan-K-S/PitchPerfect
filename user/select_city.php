<?php


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Start session to access session variables
session_start();
    // Retrieve the selected city from the form
    $city = $_POST['city'];

    // Store the selected city in the session variable
    $_SESSION['city'] = $city;

    // Redirect the user back to the index page
    header("Location: index.php");
    exit(); // Ensure script execution stops after redirection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select City</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Modal -->
<div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cityModalLabel">Select Your City</h5>
                
            </div>
            <div class="modal-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="cityForm">
                    <div class="mb-3">
                        <label for="city" class="form-label">City:</label>
                        <select class="form-select" id="city" name="city">
                            <option value="Erode">Erode</option>
                            <option value="Madurai">Madurai</option>
                            <option value="Coimbatore">Coimbatore</option>
                            <option value="Salem">Salem</option>
                            <option value="Namakkal">Namakkal</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Select</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Show the modal when the page is loaded
    window.onload = function() {
    var myModal = new bootstrap.Modal(document.getElementById('cityModal'), {
        backdrop: 'static'
    });
    myModal.show();
};

</script>

</body>
</html>
