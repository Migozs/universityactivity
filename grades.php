<?php
include("connect.php");
$message = "";

// ADD GRADE
if (isset($_POST['add'])) {
    $semester = sanitize_input($_POST["semester"]);
    $school_year = sanitize_input($_POST["school_year"]);
    $grade = sanitize_input($_POST["grade"]);
    $student_number = sanitize_input($_POST["student_number"]);
    $course_code = sanitize_input($_POST["course_code"]);
    
    $stmt = $conn->prepare("INSERT INTO grade (semester, school_year, grade, student_number, course_code) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $semester, $school_year, $grade, $student_number, $course_code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Grade added!</div>";
    } else {
        $message = "<div class='message error'>Error adding grade!</div>";
    }
    $stmt->close();
}

// UPDATE GRADE
if (isset($_POST['update'])) {
    $grade_id = sanitize_input($_POST["grade_id"]);
    $semester = sanitize_input($_POST["semester"]);
    $school_year = sanitize_input($_POST["school_year"]);
    $grade = sanitize_input($_POST["grade"]);
    $student_number = sanitize_input($_POST["student_number"]);
    $course_code = sanitize_input($_POST["course_code"]);
    
    $stmt = $conn->prepare("UPDATE grade SET semester = ?, school_year = ?, grade = ?, student_number = ?, course_code = ? WHERE grade_id = ?");
    $stmt->bind_param("ssdisi", $semester, $school_year, $grade, $student_number, $course_code, $grade_id);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Grade updated!</div>";
    } else {
        $message = "<div class='message error'>Error updating!</div>";
    }
    $stmt->close();
}

// DELETE GRADE
if (isset($_GET['delete'])) {
    $id = sanitize_input($_GET["delete"]);
    
    $stmt = $conn->prepare("DELETE FROM grade WHERE grade_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Grade deleted!</div>";
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
    $id = sanitize_input($_GET['edit']);
    
    $stmt = $conn->prepare("SELECT * FROM grade WHERE grade_id = ?");
    $stmt->bind_param("i", $id);
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
    <title>Manage Grades</title>
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
                <li><a href="courses.php">Courses</a></li>
                <li><a href="grades.php" class="active">Grades</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <h1>Manage Grades</h1>
            
            <?php echo $message; ?>
            
            <div class="form-box">
                <h3><?php echo $edit_mode ? 'Edit Grade' : 'Add New Grade'; ?></h3>
                <form method="post">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="grade_id" value="<?php echo htmlspecialchars($edit_data['grade_id']); ?>">
                    <?php endif; ?>
                    
                    <label>Semester:</label>
                    <select name="semester" required>
                        <option value="">Select Semester</option>
                        <option value="1st Semester" <?php echo ($edit_mode && $edit_data['semester'] == '1st Semester') ? 'selected' : ''; ?>>1st Semester</option>
                        <option value="2nd Semester" <?php echo ($edit_mode && $edit_data['semester'] == '2nd Semester') ? 'selected' : ''; ?>>2nd Semester</option>
                        <option value="Summer" <?php echo ($edit_mode && $edit_data['semester'] == 'Summer') ? 'selected' : ''; ?>>Summer</option>
                    </select>
                    
                    <label>School Year:</label>
                    <input type="text" name="school_year" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['school_year']) : ''; ?>" placeholder="2023-2024" required>
                    
                    <label>Grade:</label>
                    <input type="number" step="0.01" name="grade" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['grade']) : ''; ?>" min="1.0" max="5.0" required>
                    
                    <label>Student:</label>
                    <select name="student_number" required>
                        <option value="">Select Student</option>
                        <?php
                        $students = $conn->query("SELECT student_number, first_name, last_name FROM student ORDER BY last_name");
                        while ($row = $students->fetch_assoc()) {
                            $selected = ($edit_mode && $row['student_number'] == $edit_data['student_number']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['student_number']) . "' $selected>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
                        }
                        ?>
                    </select>
                    
                    <label>Course:</label>
                    <select name="course_code" required>
                        <option value="">Select Course</option>
                        <?php
                        $courses = $conn->query("SELECT course_code, course_title FROM course ORDER BY course_code");
                        while ($row = $courses->fetch_assoc()) {
                            $selected = ($edit_mode && $row['course_code'] == $edit_data['course_code']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['course_code']) . "' $selected>" . htmlspecialchars($row['course_code']) . " - " . htmlspecialchars($row['course_title']) . "</option>";
                        }
                        ?>
                    </select>
                    
                    <button type="submit" name="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php echo $edit_mode ? 'Update' : 'Add'; ?>
                    </button>
                    <?php if ($edit_mode): ?>
                        <a href="grades.php">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <table>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>School Year</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
                <?php
                $result = $conn->query("SELECT g.*, s.first_name, s.last_name, c.course_title FROM grade g LEFT JOIN student s ON g.student_number = s.student_number LEFT JOIN course c ON g.course_code = c.course_code ORDER BY g.school_year DESC");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['grade_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['course_title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['semester']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['school_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
                    echo "<td class='action-links'>";
                    echo "<a href='grades.php?edit=" . htmlspecialchars($row['grade_id']) . "'>Edit</a>";
                    echo "<a href='grades.php?delete=" . htmlspecialchars($row['grade_id']) . "' class='delete'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>