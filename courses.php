<?php
include("connect.php");
$message = "";

// ADD COURSE
if (isset($_POST['add'])) {
    $course_code = sanitize_input($_POST["course_code"]);
    $course_title = sanitize_input($_POST["course_title"]);
    $units = sanitize_input($_POST["units"]);
    
    $stmt = $conn->prepare("INSERT INTO course (course_code, course_title, units) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $course_code, $course_title, $units);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Course added!</div>";
    } else {
        $message = "<div class='message error'>Error adding course!</div>";
    }
    $stmt->close();
}

// UPDATE COURSE
if (isset($_POST['update'])) {
    $old_code = sanitize_input($_POST["old_code"]);
    $course_code = sanitize_input($_POST["course_code"]);
    $course_title = sanitize_input($_POST["course_title"]);
    $units = sanitize_input($_POST["units"]);
    
    $stmt = $conn->prepare("UPDATE course SET course_code = ?, course_title = ?, units = ? WHERE course_code = ?");
    $stmt->bind_param("ssis", $course_code, $course_title, $units, $old_code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Course updated!</div>";
    } else {
        $message = "<div class='message error'>Error updating!</div>";
    }
    $stmt->close();
}

// DELETE COURSE
if (isset($_GET['delete'])) {
    $code = sanitize_input($_GET["delete"]);
    
    $stmt = $conn->prepare("DELETE FROM course WHERE course_code = ?");
    $stmt->bind_param("s", $code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Course deleted!</div>";
    } else {
        $message = "<div class='message error'>Error deleting!</div>";
    }
    $stmt->close();
}

// EDIT MODE
$edit_mode = false;
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $code = sanitize_input($_GET['edit']);
    
    $stmt = $conn->prepare("SELECT * FROM course WHERE course_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Navigation</h2>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="colleges.php">Colleges</a></li>
                <li><a href="programs.php">Programs</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="courses.php" class="active">Courses</a></li>
                <li><a href="grades.php">Grades</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <h1>Manage Courses</h1>
            
            <?php echo $message; ?>
            
            <div class="form-box">
                <h3><?php echo $edit_mode ? 'Edit Course' : 'Add New Course'; ?></h3>
                <form method="post">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="old_code" value="<?php echo htmlspecialchars($edit_data['course_code']); ?>">
                    <?php endif; ?>
                    
                    <label>Course Code:</label>
                    <input type="text" name="course_code" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['course_code']) : ''; ?>" required>
                    
                    <label>Course Title:</label>
                    <input type="text" name="course_title" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['course_title']) : ''; ?>" required>
                    
                    <label>Units:</label>
                    <input type="number" name="units" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['units']) : ''; ?>" min="1" max="6" required>
                    
                    <button type="submit" name="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php echo $edit_mode ? 'Update' : 'Add'; ?>
                    </button>
                    <?php if ($edit_mode): ?>
                        <a href="courses.php">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <table>
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Units</th>
                    <th>Actions</th>
                </tr>
                <?php
                $result = $conn->query("SELECT * FROM course ORDER BY course_code");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['course_code']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['course_title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['units']) . "</td>";
                    echo "<td class='action-links'>";
                    echo "<a href='courses.php?edit=" . urlencode($row['course_code']) . "'>Edit</a>";
                    echo "<a href='courses.php?delete=" . urlencode($row['course_code']) . "' class='delete'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>