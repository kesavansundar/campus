<?php
include 'db.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = $_POST['company_id'];
    $image = $_FILES['image'];

    if ($image['error'] === 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image["name"]);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Update database with the image path
            $sql = "UPDATE companies SET company_image_path = ? WHERE idPrimary = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $target_file, $company_id);
            $stmt->execute();
            $stmt->close();

            echo "Image uploaded successfully!";
            header("Location: company_details.php?id=$company_id");
        } else {
            echo "Failed to upload the image.";
        }
    } else {
        echo "Error uploading the image.";
    }
}
?>
