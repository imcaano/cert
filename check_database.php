<?php
require_once 'config.php';

echo "<h2>Database Structure Check</h2>";

try {
    // Check what columns exist in results table
    $stmt = $pdo->query("SHOW COLUMNS FROM results");
    $existing_columns = $stmt->fetchAll();
    
    echo "<h3>Current Results Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($existing_columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if there's any data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM results");
    $count = $stmt->fetch()['count'];
    
    echo "<h3>Data Count:</h3>";
    echo "<p>Total records in results table: " . $count . "</p>";
    
    if ($count > 0) {
        echo "<h3>Sample Data:</h3>";
        $stmt = $pdo->query("SELECT * FROM results LIMIT 3");
        $sample_data = $stmt->fetchAll();
        
        if (!empty($sample_data)) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            $first = true;
            foreach ($sample_data as $row) {
                if ($first) {
                    echo "<tr>";
                    foreach (array_keys($row) as $key) {
                        echo "<th>" . htmlspecialchars($key) . "</th>";
                    }
                    echo "</tr>";
                    $first = false;
                }
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<h3>Next Steps:</h3>";
    echo "<p><a href='update_database.php'>Run Database Update</a></p>";
    echo "<p><a href='admin/dashboard.php'>Go to Admin Dashboard</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h2, h3 { color: #333; }
table { margin: 20px 0; }
th { background-color: #f0f0f0; padding: 8px; }
td { padding: 8px; }
</style> 