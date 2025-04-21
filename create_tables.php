<?php
require 'db.php';

$conn->query("
    CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(50) NOT NULL,
        age INT NOT NULL,
        college_level VARCHAR(10) NOT NULL,
        date_enrolled DATE DEFAULT CURRENT_DATE
    )
");

$conn->query("
    CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_name VARCHAR(100) NOT NULL
    )
");

$conn->query(
     "
     CREATE TABLE IF NOT EXISTS college_levels 
        (
          id INT AUTO_INCREMENT PRIMARY KEY,
          college_name VARCHAR(15) NOT NULL      

        )
     "   
);



$existing_level = $conn->query("SELECT * FROM college_levels");

if ($existing_level->num_rows == 0) {
    $year_level = [
       'First Year',
       'Second Year',
       'Third Year',
       'Fourth Year'
    ];

    foreach ($year_level as $lvl) {
        $stmt = $conn->prepare("INSERT INTO college_levels (college_name) VALUES (?)");
        $stmt->bind_param("s", $lvl);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->query("
    CREATE TABLE IF NOT EXISTS enrollments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        course_id INT NOT NULL,
        enrollment_date DATE DEFAULT CURRENT_DATE,
           FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    )
");

$existing_courses = $conn->query("SELECT * FROM courses");

if ($existing_courses->num_rows == 0) {
    $courses = [
        'BSIT',
        'BSBA',
        'BSIS',
        'ComSci',
    ];

    foreach ($courses as $course) {
        $stmt = $conn->prepare("INSERT INTO courses (course_name) VALUES (?)");
        $stmt->bind_param("s", $course);
        $stmt->execute();
        $stmt->close();
    }
}

echo "Tables have been successfully created and courses have been populated!";
?>