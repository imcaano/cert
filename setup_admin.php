<?php
// setup_admin.php - Database setup and admin user creation
$host = 'localhost';
$db   = 'certchain';
$user = 'root';
$pass = '';

try {
    // Create database if it doesn't exist
    $pdo_no_db = new PDO("mysql:host=$host", $user, $pass);
    $pdo_no_db->exec("CREATE DATABASE IF NOT EXISTS $db");
    echo "Database '$db' created or already exists.\n";
    
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables with new schema
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ethereum_address VARCHAR(255) UNIQUE NOT NULL,
        username VARCHAR(100) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Users table created/updated.\n";
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(255) NOT NULL,
        marks INT NOT NULL,
        average DECIMAL(5,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Subjects table created/updated.\n";
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        metamask_address VARCHAR(255) UNIQUE NOT NULL,
        email VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Students table created/updated.\n";
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS exams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        subject_id INT,
        total_marks INT,
        duration_minutes INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL
    )");
    echo "Exams table created/updated.\n";
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exam_id INT NOT NULL,
        question_text TEXT NOT NULL,
        option_a VARCHAR(255),
        option_b VARCHAR(255),
        option_c VARCHAR(255),
        option_d VARCHAR(255),
        correct_option CHAR(1),
        marks INT DEFAULT 1,
        FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
    )");
    echo "Questions table created/updated.\n";
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        exam_id INT NOT NULL,
        cert_id VARCHAR(255) UNIQUE,
        marks INT,
        average DECIMAL(5,2),
        total DECIMAL(5,2),
        passed TINYINT(1) DEFAULT 0,
        certificate_hash VARCHAR(255),
        date_issued TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
    )");
    echo "Results table created/updated.\n";
    
    // Insert default subjects
    $stmt = $pdo->prepare("INSERT IGNORE INTO subjects (subject_name, marks, average) VALUES (?, ?, ?)");
    $subjects = [
        ['Mathematics', 100, 0.00],
        ['Science', 100, 0.00],
        ['English', 100, 0.00]
    ];
    
    foreach ($subjects as $subject) {
        $stmt->execute($subject);
    }
    echo "Default subjects inserted.\n";
    
    // Create admin user
    $admin_eth_address = '0x1234567890123456789012345678901234567890'; // Placeholder address
    $admin_username = 'admin';
    
    // Check if admin already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$admin_username]);
    
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO users (ethereum_address, username, role) VALUES (?, ?, ?)');
        $stmt->execute([$admin_eth_address, $admin_username, 'admin']);
        echo "Admin user created successfully!\n";
        echo "Username: admin\n";
        echo "Ethereum Address: $admin_eth_address\n";
        echo "Role: admin\n";
    } else {
        echo "Admin user already exists.\n";
    }
    
    echo "\nSetup completed successfully!\n";
    echo "You can now access the application at: http://localhost/cert/\n";
    echo "Login with MetaMask using the Ethereum address: $admin_eth_address\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 