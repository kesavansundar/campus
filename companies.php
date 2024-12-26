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

    // Fetch companies from the database
    $stmt = $conn->prepare("SELECT id, company_name, location FROM companies");
    $stmt->execute();
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-4 text-center">Companies</h1>
        
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">List of Companies</h2>
            <ul class="list-disc pl-6">
                <?php
                // Fetch companies from the database
                $stmt = $conn->prepare("SELECT id, company_name FROM companies");
                $stmt->execute();
                $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($companies)) {
                    foreach ($companies as $company) {
                        echo "<li><a href='company_details.php?id=" . htmlspecialchars($company['id']) . "' class='text-blue-600 hover:underline'>" . htmlspecialchars($company['company_name']) . "</a></li>";
                    }
                } else {
                    echo "<li>No companies available.</li>";
                }
                ?>
            </ul>
        </div>

        <div class="mt-4 text-center">
            <a href="index.html" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mt-4 inline-block">Home</a>
        </div>
    </div>
</body>
</html>
