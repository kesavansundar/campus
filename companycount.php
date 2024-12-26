<?php
require 'db.php';
function getTotalCompanies() {
    global $conn;
    
    $sql = "SELECT COUNT(*)  FROM companies";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    return 0;
}
?>