<?php
require 'db.php';

$alert = "";

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $college_level = $_POST['college_level'];
    $course_id = $_POST['course_id'];

    $stmt = $conn->prepare("INSERT INTO students (name, email, age, college_level, date_enrolled) VALUES (?, ?, ?, ?, CURDATE())");
    $stmt->bind_param("ssii", $name, $email, $age, $college_level); // changed 'ssis' to 'ssii'
    $stmt->execute();
    $student_id = $stmt->insert_id;
    $stmt->close();

    $enroll = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
    $enroll->bind_param("ii", $student_id, $course_id);
    $enroll->execute();
    $enroll->close();

    $alert = "added";
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $college_level = $_POST['college_level'];

    $stmt = $conn->prepare("UPDATE students SET name=?, email=?, age=?, college_level=? WHERE id=?");
    $stmt->bind_param("ssiii", $name, $email, $age, $college_level, $id); // fixed types
    $stmt->execute();
    $stmt->close();

    $alert = "updated";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $alert = "deleted";
}

$students = $conn->query("
    SELECT
        students.*,
        courses.course_name,
        college_levels.college_name,
        enrollments.enrollment_date
    FROM students
    LEFT JOIN enrollments ON students.id = enrollments.student_id
    LEFT JOIN courses ON enrollments.course_id = courses.id
    LEFT JOIN college_levels ON students.college_level = college_levels.id
");

$courses = $conn->query("SELECT * FROM courses");
$college_levels = $conn->query("SELECT * FROM college_levels");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management</title>
    <link rel="stylesheet" href="style.css"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
      <style>
        body {
            font-family: "DM Sans", sans-serif;
            background-color: #f3fed0;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }

        h2 {
            color: black;
            font-weight: bold;
        }

        .container {
            background-color: #f4f4f4;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow-y: scroll;
            height: 100%;
            margin-bottom: 2rem;
            width: 90%;
            max-width: 70rem;
        }

        form {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"] {
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn {
            background-color: #ABF600;
            color: black;
            font-weight: bold;
            border: 1px solid black;
            padding: 12px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: filter 0.3s ease;
        }

        .btn:hover {
            filter: drop-shadow(0px 6px black);
        }

        .action {
            display: flex;
            gap: 1rem;
        }



        table {
            width: 100%;

            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #ABF600;
            color: black;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f0f0f0;
        }

        td input[type="text"],
        td input[type="email"],
        td input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        td button,
        td a {
            padding: 8px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: opacity 0.3s ease;
        }

        td button[name="update"] {
            background-color: #FFC607;
            border: 1px solid black;
            font-weight: bold;
            color: white;
            transition: filter 0.3s ease;
        }

        td button[name="update"]:hover {
            filter: drop-shadow(0px 3px black);
        }

        td .delete {
            background-color: #e74c3c;
            color: white;
            transition: filter 0.3s ease;
            border: 1px solid black;
        }

        td .delete:hover {
            background-color: #c0392b;
            filter: drop-shadow(0px 3px black);
        }

        @media (max-width: 600px) {
            .container,
            form {
                width: 95%;
                padding: 20px;
            }

            form {
                grid-template-columns: 1fr;
            }
        }

        footer {

         
            width: 100vw;
            background-color: #ABF600;
        }

        footer ul {
            font-weight: bold;
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 1.3rem;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #ABF600;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-track {
            background: white;
        }
    </style>

</head>
<body>

<h2>Student Management</h2>

<form method="post">
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="number" name="age" placeholder="Age" required>

    <select name="college_level" required >
        <option value="">Select College Level</option>
        <?php
      
        $college_levels_dropdown = $conn->query("SELECT * FROM college_levels");
        while ($lvl = $college_levels_dropdown->fetch_assoc()):
        ?>
            <option value="<?= $lvl['id'] ?>"><?= htmlspecialchars($lvl['college_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <select name="course_id" required>
        <option value="">Select Course</option>
        <?php
        $courses_dropdown = $conn->query("SELECT * FROM courses");
        while ($course = $courses_dropdown->fetch_assoc()):
        ?>
            <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <button class="btn" type="submit" name="add">Add Student</button>
</form>

<div class="container">
    <table border="1" cellpadding="10">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Age</th>
            <th>Course</th>
            <th>College Level</th>
            <th>Date Enrolled</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $students->fetch_assoc()): ?>
            <tr>
                <form method="post">
                    <td><input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"></td>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>"></td>
                    <td><input type="number" name="age" value="<?= $row['age'] ?>"></td>
                    <td><input type="text" readonly value="<?= htmlspecialchars($row['course_name']) ?>"></td>
                    <td>
                        <select name="college_level" required>
                            <?php
                            $college_level_options = $conn->query("SELECT * FROM college_levels");
                            while ($lvl = $college_level_options->fetch_assoc()):
                                $selected = $lvl['id'] == $row['college_level'] ? 'selected' : '';
                            ?>
                                <option value="<?= $lvl['id'] ?>" <?= $selected ?>><?= htmlspecialchars($lvl['college_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                    <td><input type="text" readonly value="<?= htmlspecialchars($row['enrollment_date']) ?>"></td>

                    <td class="action">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="update">Update</button>
                        <a class="delete" href="#" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</a>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>
    <ul>
        <li>Mae Monterola</li>
        <li>Kathleen Macahidhid</li>
        <li>Mitchua Reyes</li>
    </ul>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
<?php if ($alert === "added"): ?>
    Swal.fire({ icon: 'success', title: 'Student Added', showConfirmButton: false, timer: 1500 });
<?php elseif ($alert === "updated"): ?>
    Swal.fire({ icon: 'success', title: 'Student Updated', showConfirmButton: false, timer: 1500 });
<?php elseif ($alert === "deleted"): ?>
    Swal.fire({ icon: 'success', title: 'Student Deleted', showConfirmButton: false, timer: 1500 });
<?php endif; ?>
</script>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This student will be permanently removed.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?delete=' + id;
        }
    });
}
</script>

<?php $conn->close(); ?>
</body>
</html>
