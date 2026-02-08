<?php
include("connect.php");
$message = "";

// ADD PROGRAM
if (isset($_POST['add'])) {
    $program_code = sanitize_input($_POST["program_code"]);
    $program_name = sanitize_input($_POST["program_name"]);
    $college_code = sanitize_input($_POST["college_code"]);
    
    $stmt = $conn->prepare("INSERT INTO program (program_code, program_name, college_code) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $program_code, $program_name, $college_code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Program added!</div>";
    } else {
        $message = "<div class='message error'>Error adding program!</div>";
    }
    $stmt->close();
}

// UPDATE PROGRAM
if (isset($_POST['update'])) {
    $old_code = sanitize_input($_POST["old_code"]);
    $program_code = sanitize_input($_POST["program_code"]);
    $program_name = sanitize_input($_POST["program_name"]);
    $college_code = sanitize_input($_POST["college_code"]);
    
    $stmt = $conn->prepare("UPDATE program SET program_code = ?, program_name = ?, college_code = ? WHERE program_code = ?");
    $stmt->bind_param("ssss", $program_code, $program_name, $college_code, $old_code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Program updated!</div>";
    } else {
        $message = "<div class='message error'>Error updating!</div>";
    }
    $stmt->close();
}

// DELETE PROGRAM
if (isset($_GET['delete'])) {
    $code = sanitize_input($_GET["delete"]);
    
    $stmt = $conn->prepare("DELETE FROM program WHERE program_code = ?");
    $stmt->bind_param("s", $code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>Program deleted!</div>";
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
    
    $stmt = $conn->prepare("SELECT * FROM program WHERE program_code = ?");
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
    <title>Manage Programs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Navigation</h2>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="colleges.php">Colleges</a></li>
                <li><a href="programs.php" class="active">Programs</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="courses.php">Courses</a></li>
                <li><a href="grades.php">Grades</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <h1>Manage Programs</h1>
            
            <?php echo $message; ?>
            
            <div class="form-box">
                <h3><?php echo $edit_mode ? 'Edit Program' : 'Add New Program'; ?></h3>
                <form method="post">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="old_code" value="<?php echo htmlspecialchars($edit_data['program_code']); ?>">
                    <?php endif; ?>
                    
                    <label>Program Code:</label>
                    <input type="text" name="program_code" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['program_code']) : ''; ?>" required>
                    
                    <label>Program Name:</label>
                    <input type="text" name="program_name" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['program_name']) : ''; ?>" required>
                    
                    <label>College:</label>
                    <select name="college_code" required>
                        <option value="">Select College</option>
                        <?php
                        $colleges = $conn->query("SELECT * FROM college ORDER BY college_name");
                        while ($row = $colleges->fetch_assoc()) {
                            $selected = ($edit_mode && $row['college_code'] == $edit_data['college_code']) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($row['college_code']) . "' $selected>" . htmlspecialchars($row['college_name']) . "</option>";
                        }
                        ?>
                    </select>
                    
                    <button type="submit" name="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php echo $edit_mode ? 'Update' : 'Add'; ?>
                    </button>
                    <?php if ($edit_mode): ?>
                        <a href="programs.php">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <table>
                <tr>
                    <th>Program Code</th>
                    <th>Program Name</th>
                    <th>College</th>
                    <th>Actions</th>
                </tr>
                <?php
                $result = $conn->query("SELECT p.*, c.college_name FROM program p LEFT JOIN college c ON p.college_code = c.college_code ORDER BY p.program_code");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['program_code']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['program_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['college_name']) . "</td>";
                    echo "<td class='action-links'>";
                    echo "<a href='programs.php?edit=" . urlencode($row['program_code']) . "'>Edit</a>";
                    echo "<a href='programs.php?delete=" . urlencode($row['program_code']) . "' class='delete'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>