<?php
include("connect.php");
$message = "";

// ADD STUDENT
if (isset($_POST['add'])) {
    $student_number = sanitize_input($_POST["student_number"]);
    $first_name = sanitize_input($_POST["first_name"]);
    $middle_name = sanitize_input($_POST["middle_name"]);
    $last_name = sanitize_input($_POST["last_name"]);
    $gender = sanitize_input($_POST["gender"]);
    $birthday = sanitize_input($_POST["birthday"]);
    $details = sanitize_input($_POST["details"]);
    $program_code = sanitize_input($_POST["program_code"]);
    
    $stmt = $conn->prepare("INSERT INTO student (student_number, first_name, middle_name, last_name, gender, birthday, details, program_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $student_number, $first_name, $middle_name, $last_name, $gender, $birthday, $details, $program_code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Student added!</div>";
    } else {
        $message = "<div class='message error'>Error adding student!</div>";
    }
    $stmt->close();
}

// UPDATE STUDENT
if (isset($_POST['update'])) {
    $old_student_number = sanitize_input($_POST["old_student_number"]);
    $student_number = sanitize_input($_POST["student_number"]);
    $first_name = sanitize_input($_POST["first_name"]);
    $middle_name = sanitize_input($_POST["middle_name"]);
    $last_name = sanitize_input($_POST["last_name"]);
    $gender = sanitize_input($_POST["gender"]);
    $birthday = sanitize_input($_POST["birthday"]);
    $details = sanitize_input($_POST["details"]);
    $program_code = sanitize_input($_POST["program_code"]);
    
    $stmt = $conn->prepare("UPDATE student SET student_number = ?, first_name = ?, middle_name = ?, last_name = ?, gender = ?, birthday = ?, details = ?, program_code = ? WHERE student_number = ?");
    $stmt->bind_param("isssssssi", $student_number, $first_name, $middle_name, $last_name, $gender, $birthday, $details, $program_code, $old_student_number);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Student updated!</div>";
    } else {
        $message = "<div class='message error'>Error updating!</div>";
    }
    $stmt->close();
}

// DELETE STUDENT
if (isset($_GET['delete'])) {
    $student_number = sanitize_input($_GET["delete"]);
    
    $stmt = $conn->prepare("DELETE FROM student WHERE student_number = ?");
    $stmt->bind_param("i", $student_number);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Student deleted!</div>";
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
    $student_number = sanitize_input($_GET['edit']);
    
    $stmt = $conn->prepare("SELECT * FROM student WHERE student_number = ?");
    $stmt->bind_param("i", $student_number);
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
    <title>Manage Students</title>
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
                <li><a href="students.php" class="active">Students</a></li>
                <li><a href="courses.php">Courses</a></li>
                <li><a href="grades.php">Grades</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <h1>Manage Students</h1>
            
            <?php echo $message; ?>
            
            <div class="form-box">
                <h3><?php echo $edit_mode ? 'Edit Student' : 'Add New Student'; ?></h3>
                <form method="post">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="old_student_number" value="<?php echo htmlspecialchars($edit_data['student_number']); ?>">
                    <?php endif; ?>
                    
                    <label>Student Number:</label>
                    <input type="number" name="student_number" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['student_number']) : ''; ?>" required>
                    
                    <label>First Name:</label>
                    <input type="text" name="first_name" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['first_name']) : ''; ?>" required>
                    
                    <label>Middle Name:</label>
                    <input type="text" name="middle_name" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['middle_name']) : ''; ?>">
                    
                    <label>Last Name:</label>
                    <input type="text" name="last_name" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['last_name']) : ''; ?>" required>
                    
                    <label>Gender:</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($edit_mode && $edit_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($edit_mode && $edit_data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                    
                    <label>Birthday:</label>
                    <input type="date" name="birthday" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['birthday']) : ''; ?>" required>
                    
                    <label>Program:</label>
                    <select name="program_code" required>
                        <option value="">Select Program</option>
                        <?php
                        $programs = $conn->query("SELECT * FROM program ORDER BY program_name");
                        while ($row = $programs->fetch_assoc()) {
                            $selected = ($edit_mode && $row['program_code'] == $edit_data['program_code']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['program_code']) . "' $selected>" . htmlspecialchars($row['program_name']) . "</option>";
                        }
                        ?>
                    </select>
                    
                    <label>Details (Optional):</label>
                    <textarea name="details" rows="3"><?php echo $edit_mode ? htmlspecialchars($edit_data['details']) : ''; ?></textarea>
                    
                    <button type="submit" name="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php echo $edit_mode ? 'Update' : 'Add'; ?>
                    </button>
                    <?php if ($edit_mode): ?>
                        <a href="students.php">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <table>
                <tr>
                    <th>Student Number</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Birthday</th>
                    <th>Program</th>
                    <th>Actions</th>
                </tr>
                <?php
                $result = $conn->query("SELECT s.*, p.program_name FROM student s LEFT JOIN program p ON s.program_code = p.program_code ORDER BY s.last_name");
                while ($row = $result->fetch_assoc()) {
                    $full_name = $row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name'];
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['student_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($full_name) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['birthday']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['program_name']) . "</td>";
                    echo "<td class='action-links'>";
                    echo "<a href='students.php?edit=" . htmlspecialchars($row['student_number']) . "'>Edit</a>";
                    echo "<a href='students.php?delete=" . htmlspecialchars($row['student_number']) . "' class='delete'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>