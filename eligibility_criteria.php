<?php
// Database connection details
$host = "localhost";
$db_name = "campus_recruitment";
$username = "root";
$password = "";

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get company ID from query parameters
    $company_id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($company_id) {
        // Fetch company details
        $stmt = $conn->prepare("SELECT * FROM companies WHERE id = :id");
        $stmt->execute([':id' => $company_id]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$company) {
            die("<p class='text-center text-red-500'>Company not found.</p>");
        }

        // Fetch eligibility criteria
        $stmt = $conn->prepare("SELECT criteria_description FROM eligibility_criteria WHERE company_id = :id");
        $stmt->execute([':id' => $company_id]);
        $eligibility = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if data is fetched correctly
        if (empty($eligibility)) {
            die("<p class='text-center text-red-500'>No eligibility criteria available for this company.</p>");
        }
    } else {
        die("<p class='text-center text-red-500'>Invalid company ID.</p>");
    }
} catch (PDOException $e) {
    die("<p class='text-center text-red-500'>Error: " . htmlspecialchars($e->getMessage()) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eligibility Criteria - <?php echo htmlspecialchars($company['company_name'] ?? 'Company Details'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto py-8">
        <!-- Header -->
        <h1 class="text-3xl font-bold mb-6 text-center">
            Eligibility Criteria for <?php echo htmlspecialchars($company['company_name'] ?? 'Company'); ?>
        </h1>

        <!-- Eligibility Criteria -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Eligibility Criteria</h2>
            <ul class="list-disc pl-6">
                <?php if (!empty($eligibility)): ?>
                    <?php foreach ($eligibility as $criteria): ?>
                        <li><?php echo htmlspecialchars($criteria['criteria_description']); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No eligibility criteria available.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Back Button -->
        <div class="mt-4 text-center">
            <a href="company_details.php?id=<?php echo htmlspecialchars($company['id']); ?>" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Back to Company Details
            </a>
        </div>
    </div>
</body>
</html>
