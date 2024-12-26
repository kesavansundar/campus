<?php
include 'db.php';
session_start();

$company_id = $_GET['id'] ?? null;

if ($company_id) {
    // Fetch company details
    $sql = "SELECT * FROM companies WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing the SQL query: " . $conn->error);
    }

    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $company = $result->fetch_assoc();
    } else {
        die("Company not found.");
    }
    $stmt->close();

    // Fetch available roles
    $sql_roles = "SELECT * FROM available_roles WHERE company_id = ?";
    $stmt_roles = $conn->prepare($sql_roles);
    
    if ($stmt_roles === false) {
        die("Error preparing the roles query: " . $conn->error);
    }

    $stmt_roles->bind_param("i", $company_id);
    $stmt_roles->execute();
    $result_roles = $stmt_roles->get_result();
    $roles = [];
    while ($role = $result_roles->fetch_assoc()) {
        $roles[] = $role;
    }
    $stmt_roles->close();

    // Fetch eligibility criteria
    $sql_criteria = "SELECT * FROM eligibility_criteria WHERE company_id = ?";
    $stmt_criteria = $conn->prepare($sql_criteria);
    
    if ($stmt_criteria === false) {
        die("Error preparing the criteria query: " . $conn->error);
    }

    $stmt_criteria->bind_param("i", $company_id);
    $stmt_criteria->execute();
    $result_criteria = $stmt_criteria->get_result();
    $criteria = [];
    while ($criterion = $result_criteria->fetch_assoc()) {
        $criteria[] = $criterion;
    }
    $stmt_criteria->close();

} else {
    die("Invalid company ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">
    <div class="container mx-auto bg-white p-8 rounded shadow">
        <h1 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars($company['company_name']); ?></h1>
        
        <!-- Company Image -->
        <img src="<?php echo htmlspecialchars(!empty($company['company_image_path']) && file_exists($company['company_image_path']) 
            ? $company['company_image_path'] 
            : 'default-placeholder.png'); ?>" 
            alt="Company Image" class="w-64 h-64 object-cover rounded mb-6">

        <p><strong>Location:</strong> <?php echo htmlspecialchars($company['location']); ?></p>
        <p><strong>Contact Email:</strong> <?php echo htmlspecialchars($company['contact_email']); ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($company['description'])); ?></p>
        <p><strong>Website:</strong> 
            <?php if (!empty($company['website_url'])): ?>
                <a href="<?php echo htmlspecialchars($company['website_url']); ?>" target="_blank" class="text-blue-500 hover:underline">
                    <?php echo htmlspecialchars($company['website_url']); ?>
                </a>
            <?php else: ?>
                Not Available
            <?php endif; ?>
        </p>
        <p><strong>Established Year:</strong> <?php echo htmlspecialchars($company['established_year']); ?></p>

        <!-- Available Roles -->
        <h2 class="text-2xl font-bold mt-8 mb-4">Available Roles</h2>
        <ul class="list-disc pl-5">
            <?php if (count($roles) > 0): ?>
                <?php foreach ($roles as $role): ?>
                    <li><?php echo htmlspecialchars($role['role_name']); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No roles available</li>
            <?php endif; ?>
        </ul>

        <!-- Eligibility Criteria -->
        <h2 class="text-2xl font-bold mt-8 mb-4">Eligibility Criteria</h2>
        <ul class="list-disc pl-5">
            <?php if (count($criteria) > 0): ?>
                <?php foreach ($criteria as $criterion): ?>
                    <li><?php echo nl2br(htmlspecialchars($criterion['criteria_description'])); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No eligibility criteria available</li>
            <?php endif; ?>
        </ul>

        <!-- Upload Form for Admin -->
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
            <h2 class="text-2xl font-bold mt-8 mb-4">Upload Company Image</h2>
            <form action="upload_image.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
                <label for="image" class="block mb-2 font-semibold">Choose an image:</label>
                <input type="file" name="image" id="image" class="mb-4 border p-2 rounded w-full" required>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
