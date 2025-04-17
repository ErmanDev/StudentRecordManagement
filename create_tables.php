<?php
require 'db.php';

echo "Connected successfully<br>";

$tables = [
  "students" => "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    age INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )",

  "courses" => "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )"
];

foreach ($tables as $tableName => $sql) {
  if ($conn->query($sql) === TRUE) {
    echo "Table '$tableName' created successfully<br>";
  } else {
    echo "Error creating table '$tableName': " . $conn->error . "<br>";
  }
}

$conn->close();
?>
