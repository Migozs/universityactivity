<?php
include("connect.php");
$message = "";

// ADD COLLEGE
if (isset($_POST['add'])) {
    $college_code = sanitize_input($_POST["college_code"]);
    $college_name = sanitize_input($_POST["college_name"]);
    
    $stmt = $conn->prepare("INSERT INTO college (college_code, college_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $college_code, $college_name);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>College added!</div>";
    } else {
        $message = "<div class='message error'>Error adding college!</div>";
    }
    $stmt->close();
}

// UPDATE COLLEGE
if (isset($_POST['update'])) {
    $old_code = sanitize_input($_POST["old_code"]);
    $college_code = sanitize_input($_POST["college_code"]);
    $college_name = sanitize_input($_POST["college_name"]);
    
    $stmt = $conn->prepare("UPDATE college SET college_code = ?, college_name = ? WHERE college_code = ?");
    $stmt->bind_param("sss", $college_code, $college_name, $old_code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>College updated!</div>";
    } else {
        $message = "<div class='message error'>Error updating!</div>";
    }
    $stmt->close();
}

// DELETE COLLEGE
if (isset($_GET['delete'])) {
    $code = sanitize_input($_GET["delete"]);
    
    $stmt = $conn->prepare("DELETE FROM college WHERE college_code = ?");
    $stmt->bind_param("s", $code);
    
    if ($stmt->execute()) {
        $message = "<div class='message success'>College deleted!</div>";
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
    
    $stmt = $conn->prepare("SELECT * FROM college WHERE college_code = ?");
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
    <title>Manage Colleges</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Navigation</h2>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="colleges.php" class="active">Colleges</a></li>
                <li><a href="programs.php">Programs</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="courses.php">Courses</a></li>
                <li><a href="grades.php">Grades</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <h1>Manage Colleges</h1>
            
            <?php echo $message; ?>
            
            <div class="form-box">
                <h3><?php echo $edit_mode ? 'Edit College' : 'Add New College'; ?></h3>
                <form method="post">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="old_code" value="<?php echo htmlspecialchars($edit_data['college_code']); ?>">
                    <?php endif; ?>
                    
                    <label>College Code:</label>
                    <input type="text" name="college_code" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['college_code']) : ''; ?>" required>
                    
                    <label>College Name:</label>
                    <input type="text" name="college_name" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['college_name']) : ''; ?>" required>
                    
                    <button type="submit" name="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php echo $edit_mode ? 'Update' : 'Add'; ?>
                    </button>
                    <?php if ($edit_mode): ?>
                        <a href="colleges.php">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <table>
                <tr>
                    <th>College Code</th>
                    <th>College Name</th>
                    <th>Actions</th>
                </tr>
                <?php
                $result = $conn->query("SELECT * FROM college ORDER BY college_code");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['college_code']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['college_name']) . "</td>";
                    echo "<td class='action-links'>";
                    echo "<a href='colleges.php?edit=" . urlencode($row['college_code']) . "'>Edit</a>";
                    echo "<a href='colleges.php?delete=" . urlencode($row['college_code']) . "' class='delete'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>