<?php
require 'db.php';


if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $age = $_POST['age'];

  $stmt = $conn->prepare("INSERT INTO students (name, email, age) VALUES (?, ?, ?)");
  $stmt->bind_param("ssi", $name, $email, $age);
  $stmt->execute();
  $stmt->close();
}

if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $age = $_POST['age'];

  $stmt = $conn->prepare("UPDATE students SET name=?, email=?, age=? WHERE id=?");
  $stmt->bind_param("ssii", $name, $email, $age, $id);
  $stmt->execute();
  $stmt->close();
}

if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM students WHERE id=$id");
}

$students = $conn->query("SELECT * FROM students");
?>

<h2>Student Management</h2>

<form method="post">
  <input type="text" name="name" placeholder="Name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="number" name="age" placeholder="Age" required>
  <button type="submit" name="add">Add Student</button>
</form>

<br><hr><br>

<table border="1" cellpadding="10">
  <tr>
    <th>Name</th>
    <th>Email</th>
    <th>Age</th>
    <th>Actions</th>
  </tr>

  <?php while ($row = $students->fetch_assoc()): ?>
    <tr>
      <form method="post">
        <td><input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"></td>
        <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>"></td>
        <td><input type="number" name="age" value="<?= $row['age'] ?>"></td>
        <td>
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <button type="submit" name="update">Update</button>
          <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>
        </td>
      </form>
    </tr>
  <?php endwhile; ?>
</table>

<?php $conn->close(); ?>
