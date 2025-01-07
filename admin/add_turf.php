<?php
// Check if admin is logged in
include('login_check.php');

// Include database connection
include('../base/db.php');

// Initialize variables for error messages
$errors = array();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $size = $_POST['size'];
    $contact = $_POST['contact'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $amenities = $_POST['amenities'];

    // Validate form fields
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($size)) {
        $errors[] = "Size is required";
    } elseif (!is_numeric($size)) {
        $errors[] = "Size must be a number";
    }
    if (empty($contact)) {
        $errors[] = "Contact number is required";
    } elseif (!preg_match("/^\d{10}$/", $contact)) {
        $errors[] = "Contact number must be 10 digits";
    }
    if (empty($city)) {
        $errors[] = "City is required";
    } elseif (!in_array($city, ['Erode', 'Madurai', 'Coimbatore', 'Salem', 'Namakkal'])) {
        $errors[] = "Invalid city";
    }
    if (empty($price)) {
        $errors[] = "Price is required";
    } elseif (!is_numeric($price)) {
        $errors[] = "Price must be a number";
    }
    if (empty($capacity)) {
        $errors[] = "Capacity is required";
    } elseif (!is_numeric($capacity)) {
        $errors[] = "Capacity must be a number";
    }

    // Check if image file is uploaded
    if ($_FILES['image1']['error'] === UPLOAD_ERR_OK) {
        $image1_name = uniqid('turf_') . '_' . $_FILES['image1']['name'];
        $image1_tmp_name = $_FILES['image1']['tmp_name'];
        $image1_path = '../turf_images/' . $image1_name;
        if(!move_uploaded_file($image1_tmp_name, $image1_path)){
            $errors[] = "Error uploading image file";
        }
    } else {
        $errors[] = "Error uploading image file";
    }

    // If no errors, insert turf data into database
    if (empty($errors)) {
        $admin_id = $_SESSION['admin_id'];
        $insert_query = "INSERT INTO turf (admin_id, name, size, contact, city, address, description, price, capacity, image1, amenities) VALUES ('$admin_id', '$name', '$size', '$contact', '$city', '$address', '$description', '$price', '$capacity', '$image1_path', '$amenities')";
        if ($conn->query($insert_query) === TRUE) {
            // Redirect to index page after successful insertion
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../base/images/logo.svg" type="image/gif" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Turf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .form-error {
            color: red;
            font-size: 0.875rem;
        }
    </style>
    <script>
        function validateForm() {
            var name = document.getElementById("name").value.trim();
            var size = document.getElementById("size").value.trim();
            var contact = document.getElementById("contact").value.trim();
            var price = document.getElementById("price").value.trim();
            var capacity = document.getElementById("capacity").value.trim();
            var errorMessages = document.getElementsByClassName("form-error");

            // Reset previous error messages
            for (var i = 0; i < errorMessages.length; i++) {
                errorMessages[i].innerText = "";
            }

            var isValid = true;

            if (name === "") {
                document.getElementById("nameError").innerText = "Turf Name is required";
                isValid = false;
            }
            if (size === "" || !/^\d+$/.test(size)) {
                document.getElementById("sizeError").innerText = "Please enter a valid numeric value for size";
                isValid = false;
            }
            if (contact === "" || !/^\d{10}$/.test(contact)) {
                document.getElementById("contactError").innerText = "Please enter a valid 10-digit contact number";
                isValid = false;
            }
            if (price === "" || !/^\d+(\.\d{1,2})?$/.test(price)) {
                document.getElementById("priceError").innerText = "Please enter a valid price";
                isValid = false;
            }
            if (capacity === "" || !/^\d+$/.test(capacity)) {
                document.getElementById("capacityError").innerText = "Please enter a valid numeric value for capacity";
                isValid = false;
            }

            return isValid;
        }

        // Function to allow only numeric input in certain fields
        function allowOnlyNumeric(event) {
            var charCode = event.which ? event.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                event.preventDefault();
            }
        } 
    </script>
</head>

<body class="bg-gray-200 font-sans leading-normal tracking-normal">
<?php include('../base/nav_dashboard.php');?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-10">
            <div class="bg-white p-8 rounded shadow-md">
                <h2 class="text-2xl font-bold mb-4" align="center">Add Turf</h2>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <!-- Turf Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label">Turf Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>">
                        <span id="nameError" class="form-error"></span>
                    </div>

                    <!-- Size -->
                    <div class="mb-4">
                        <label for="size" class="form-label">Size (in ft)</label>
                        <input type="text" id="size" name="size" class="form-control" onkeypress="allowOnlyNumeric(event)" value="<?php echo isset($size) ? $size : ''; ?>">
                        <span id="sizeError" class="form-error"></span>
                    </div>

                    <!-- Contact -->
                    <div class="mb-4">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" id="contact" name="contact" class="form-control" pattern="[0-9]{10}" onkeypress="allowOnlyNumeric(event)" value="<?php echo isset($contact) ? $contact : ''; ?>">
                        <span id="contactError" class="form-error"></span>
                    </div>

                    <!-- City -->
                    <div class="mb-4">
                        <label for="city" class="form-label">City</label>
                        <select class="form-select" id="city" name="city">
                            <option value="">Select City</option>
                            <option value="Erode" <?php echo (isset($city) && $city == 'Erode') ? 'selected' : ''; ?>>Erode</option>
                            <option value="Madurai" <?php echo (isset($city) && $city == 'Madurai') ? 'selected' : ''; ?>>Madurai</option>
                            <option value="Coimbatore" <?php echo (isset($city) && $city == 'Coimbatore') ? 'selected' : ''; ?>>Coimbatore</option>
                            <option value="Salem" <?php echo (isset($city) && $city == 'Salem') ? 'selected' : ''; ?>>Salem</option>
                            <option value="Namakkal" <?php echo (isset($city) && $city == 'Namakkal') ? 'selected' : ''; ?>>Namakkal</option>
                        </select>
                        <span id="cityError" class="form-error"></span>
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address"><?php echo isset($address) ? $address : ''; ?></textarea>
                        <span id="addressError" class="form-error"></span>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"><?php echo isset($description) ? $description : ''; ?></textarea>
                        <span id="descriptionError" class="form-error"></span>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <label for="price" class="form-label">Price (per hr)</label>
                        <input type="text" class="form-control" id="price" onkeypress="allowOnlyNumeric(event)" name="price" value="<?php echo isset($price) ? $price : ''; ?>">
                        <span id="priceError" class="form-error"></span>
                    </div>

                    <!-- Capacity -->
                    <div class="mb-4">
                        <label for="capacity" class="form-label">Capacity (No. persons)</label>
                        <input type="text" class="form-control" id="capacity" onkeypress="allowOnlyNumeric(event)" name="capacity" value="<?php echo isset($capacity) ? $capacity : ''; ?>">
                        <span id="capacityError" class="form-error"></span>
                    </div>

                    <!-- Turf Image -->
                    <div class="mb-4">
                        <label for="image1" class="form-label">Turf Image</label>
                        <input type="file" class="form-control" id="image1" name="image1">
                        <span id="imageError" class="form-error"></span>
                    </div>

                    <!-- Amenities -->
                    <div class="mb-4">
                        <label for="amenities" class="form-label">Amenities</label>
                        <textarea class="form-control" id="amenities" name="amenities"><?php echo isset($amenities) ? $amenities : ''; ?></textarea>
                        <span id="amenitiesError" class="form-error"></span>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Turf</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
