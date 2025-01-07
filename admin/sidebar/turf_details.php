<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../base/db.php');

    // Retrieve turf details based on the provided turf_id
    
    if (isset($_POST["name"])) {
        // Include your database connection code here
        include('../base/db.php');

        // Update turf details in the database
        $turf_id = $_POST['turf_id'];
        $name = $_POST['name'];
        $size = $_POST['size'];
        $contact = $_POST['contact'];
        $city = $_POST['city'];
        $address = $_POST['address'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $capacity = $_POST['capacity'];
        $amenities = $_POST['amenities'];

        // Example SQL query to update turf details
        $sql = "UPDATE turf 
                SET name='$name', 
                    size='$size', 
                    contact='$contact', 
                    city='$city', 
                    address='$address', 
                    description='$description', 
                    price='$price', 
                    capacity='$capacity', 
                    amenities='$amenities' 
                WHERE id='$turf_id'";
        //echo "$sql";
        // Execute the SQL query to update turf details
        if (mysqli_query($conn, $sql)) {
            //echo "Record updated successfully";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }

        // Handle image upload if a new image is provided
        if ($_FILES['image']['size'] > 0) {
            // Image upload directory
            $target_dir = "../turf_images/";
            // Get the filename
            $filename = basename($_FILES["image"]["name"]);
            // Set the target file path
            $target_file = $target_dir . $filename;
            // Check if the file already exists and delete it
            if (file_exists($target_file)) {
                unlink($target_file);
            }
            // Upload the new image
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
                // Update the image path in the database
                $sql_update_image = "UPDATE turf SET image1='$target_file' WHERE id='$turf_id'";
                if (mysqli_query($conn, $sql_update_image)) {
                    //echo "Image path updated successfully.";
                } else {
                    echo "Error updating image path: " . mysqli_error($conn);
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        // Redirect to the same page after updating
       echo '<div class="fixed bottom-0 right-0 mb-4 mr-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md z-50" role="alert">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="text-lg font-bold">Success!</div>
                <div class="ml-2 text-sm">Turf details updated successfully.</div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-lg font-semibold">&times;</button>
        </div>
    </div>';
    }

    $turf_id = $_POST['turf_id'];
    $query = "SELECT * FROM turf WHERE id = $turf_id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Display the form with current turf details
?>

<body class="bg-gray-100">
<div class="container mx-auto p-8">
    <h2 class="text-3xl font-bold mb-4" align='center'>Update Turf Details</h2><br>
    <form method="post" class="space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="turf_id" value="<?php echo $row['id']; ?>">
        <input type="hidden" name="page" value="turf_details">
        <div class="mb-3">
            <label for="name" class="form-label">Turf Name</label>
            <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" class="form-control editable" readonly>
        </div>
        <!-- Add image input field and image preview -->
        <div class="mb-3">
            <label for="image" class="form-label">Current Image</label><br>
            <img src="<?php echo $row['image1']; ?>" alt="Current Image" class="mb-2">
            <input type="file" id="image" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label for="size" class="form-label">Size (in ft)</label>
            <input type="text" id="size" name="size" value="<?php echo $row['size']; ?>" class="form-control editable" readonly>
        </div>
        <div class="mb-3">
            <label for="contact" class="form-label">Contact</label>
            <input type="text" id="contact" name="contact" value="<?php echo $row['contact']; ?>" class="form-control editable" readonly>
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">City</label>
            <input type="text" id="city" name="city" value="<?php echo $row['city']; ?>" class="form-control editable" readonly>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea id="address" name="address" class="form-control editable" readonly><?php echo $row['address']; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control editable" readonly><?php echo $row['description']; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price (per hr)</label>
            <input type="text" id="price" name="price" value="<?php echo $row['price']; ?>" class="form-control editable" readonly>
        </div>
        <div class="mb-3">
            <label for="capacity" class="form-label">Capacity (No. persons)</label>
            <input type="text" id="capacity" name="capacity" value="<?php echo $row['capacity']; ?>" class="form-control editable" readonly>
        </div>
        <div class="mb-3">
            <label for="amenities" class="form-label">Amenities</label>
            <textarea id="amenities" name="amenities" class="form-control editable" readonly><?php echo $row['amenities']; ?></textarea>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
    </form>
</div>

<script>
    // Function to toggle readonly attribute on double click
    document.querySelectorAll('.editable').forEach(function(input) {
        input.addEventListener('dblclick', function() {
            this.readOnly = !this.readOnly;
        });
    });
</script>
</body>
<?php
    } else {
        echo "Turf not found!";
    }
}
?>
