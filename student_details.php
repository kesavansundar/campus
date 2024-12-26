<?php
require 'db.php';

// Initialize SQL variable to avoid undefined error
$sql = "";

// Fetch filtered data based on form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $program = $_POST['program'];
    $department = $_POST['department'];
    $section = $_POST['section'];

    $sql = "SELECT * FROM students_details WHERE program = '$program' AND department = '$department' AND section = '$section'";
}

// Ensure that $sql is not empty before running the query
if ($sql !== "") {
    $result = $conn->query($sql);
} else {
    $result = null;  // You can handle the case where there's no query or provide a default query
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List - Campus Recruitment</title>
<style>
:root {
    --primary-color: #4f46e5;
    --primary-dark: #4338ca;
    --primary-light: #eef2ff;
    --secondary-color: #1e293b;
    --background-color: #f8fafc;
    --card-background: #ffffff;
    --text-color: #334155;
    --border-color: #e2e8f0;
    --success-color: #10b981;
    --error-color: #ef4444;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    background-color: var(--background-color);
    color: var(--text-color);
    line-height: 1.6;
}

.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.header {
    background-color: var(--card-background);
    padding: 2.5rem;
    border-radius: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
}

h1 {
    color: var(--secondary-color);
    font-size: 2.25rem;
    margin-bottom: 1.5rem;
    text-align: center;
    font-weight: 700;
    letter-spacing: -0.025em;
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 1.25rem;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    background-color: var(--primary-light);
    border-radius: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

select {
    padding: 0.875rem 1.25rem;
    border: 2px solid var(--border-color);
    border-radius: 0.75rem;
    font-size: 1rem;
    background-color: var(--card-background);
    min-width: 220px;
    cursor: pointer;
    transition: all 0.2s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234f46e5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1.5em;
}

select:hover {
    border-color: var(--primary-color);
    background-color: var(--primary-light);
}

select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
}

button {
    padding: 0.875rem 2.5rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 0.75rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
}

button:hover {
    background-color: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
}

button:active {
    transform: translateY(0);
}

.table-container {
    background-color: var(--card-background);
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-top: 2rem;
    border: 1px solid var(--border-color);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 1.25rem 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
    position: sticky;
    top: 0;
}

tr:nth-child(even) {
    background-color: var(--primary-light);
}

tr:hover {
    background-color: rgba(79, 70, 229, 0.05);
}

td {
    font-size: 0.95rem;
}

.no-results {
    text-align: center;
    padding: 3rem;
    color: var(--secondary-color);
    font-size: 1.1rem;
    background-color: var(--primary-light);
}

.loading {
    display: none;
    text-align: center;
    padding: 3rem;
}

.loading-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--primary-light);
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

.loading p {
    color: var(--secondary-color);
    font-size: 1.1rem;
    font-weight: 500;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .container {
        padding: 0 1rem;
        margin: 1rem auto;
    }

    .header {
        padding: 1.5rem;
        border-radius: 1rem;
    }

    h1 {
        font-size: 1.75rem;
    }

    .filter-form {
        flex-direction: column;
        align-items: stretch;
        padding: 1.5rem;
        gap: 1rem;
    }

    select, button {
        width: 100%;
        min-width: unset;
    }

    .table-container {
        border-radius: 0.75rem;
        margin-top: 1.5rem;
    }

    th, td {
        padding: 0.875rem;
        font-size: 0.875rem;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
    }
}

/* Custom scrollbar for better appearance */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--background-color);
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}
</style>
</head>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List - Campus Recruitment</title>
    <style>
        /* Styles as is */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Student Information System</h1>
            <form method="POST" action="" class="filter-form">
                <select name="program" required>
                    <option value="" disabled selected>Select Program</option>
                    <option value="ug">Undergraduate</option>
                    <option value="pg">Postgraduate</option>
                </select>

                <select name="department" required>
                    <option value="" disabled selected>Select Department</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Mechanical">Mechanical Engineering</option>
                    <option value="Electrical">Electrical Engineering</option>
                    <option value="Civil">Civil Engineering</option>
                </select>

                <select name="section" required>
                    <option value="" disabled selected>Select Section</option>
                    <option value="A">Section A</option>
                    <option value="B">Section B</option>
                    <option value="C">Section C</option>
                </select>

                <button type="submit">Search Students</button>
            </form>
        </div>

        <div class="loading">
            <div class="loading-spinner"></div>
            <p>Loading results...</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Sl No</th>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Mobile</th>
                        <th>College ID</th>
                        <th>Program</th>
                        <th>Department</th>
                        <th>Section</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Populate table if there are results
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['sl_no'] . "</td>"; // Adjust based on your DB structure
                            echo "<td>" . $row['roll_no'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['gender'] . "</td>";
                            echo "<td>" . $row['dob'] . "</td>";
                            echo "<td>" . $row['student_mob'] . "</td>";
                            echo "<td>" . $row['college_id'] . "</td>";
                            echo "<td>" . $row['program'] . "</td>";
                            echo "<td>" . $row['department'] . "</td>";
                            echo "<td>" . $row['section'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' class='no-results'>No results found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <form action="admin_index.html">
    <div class="button-container">
    <div style="align-items: center;">
    <button type="submit">Back to dashboard</button>
</div>
    </div>
    </form>
</body>
</html>