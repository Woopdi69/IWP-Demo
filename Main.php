<?php
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        die("Environment file not found: " . $filePath);
    }
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

loadEnv('.env');
$servername = getenv('servername') ?: 'localhost';
$username = getenv('username') ?: 'root';
$password = getenv('password') ?: '';
$dbname = getenv('dbname') ?: 'IWP';
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$editRecord = null;

if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add' && !empty($_POST['name']) && !empty($_POST['email'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
        if ($conn->query($sql) === TRUE) {
            $message = "Record added successfully";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
    
    if ($action == 'update' && !empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['email'])) {
        $id = intval($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $message = "Record updated successfully";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
    
    if ($action == 'delete' && !empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM users WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $message = "Record deleted successfully";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM users WHERE id=$id");
    $editRecord = $result->fetch_assoc();
}

$sql = "SELECT id, name, email FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Manager - CRUD Operations</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-group { margin: 10px 0; }
        input[type="text"], input[type="email"] { padding: 5px; width: 200px; }
        button { padding: 8px 15px; margin: 2px; border: none; cursor: pointer; }
        .btn-add { background-color: #007cba; color: white; }
        .btn-update { background-color: #28a745; color: white; }
        .btn-edit { background-color: #ffc107; color: black; }
        .btn-delete { background-color: #dc3545; color: white; }
        .btn-cancel { background-color: #6c757d; color: white; }
        .message { color: green; font-weight: bold; }
        .edit-form { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Database Manager - CRUD Operations</h1>
    
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    
    <?php if ($editRecord): ?>
    <div class="edit-form">
        <h2>Edit Record</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $editRecord['id']; ?>">
            <div class="form-group">
                <label>Name:</label><br>
                <input type="text" name="name" value="<?php echo htmlspecialchars($editRecord['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email:</label><br>
                <input type="email" name="email" value="<?php echo htmlspecialchars($editRecord['email']); ?>" required>
            </div>
            <button type="submit" name="action" value="update" class="btn-update">Update Record</button>
            <a href="?"><button type="button" class="btn-cancel">Cancel</button></a>
        </form>
    </div>
    <?php else: ?>
    <h2>Add New Record</h2>
    <form method="POST">
        <div class="form-group">
            <label>Name:</label><br>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email:</label><br>
            <input type="email" name="email" required>
        </div>
        <button type="submit" name="action" value="add" class="btn-add">Add Record</button>
    </form>
    <?php endif; ?>
    
    <h2>Records</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row["id"]."</td>";
                echo "<td>".htmlspecialchars($row["name"])."</td>";
                echo "<td>".htmlspecialchars($row["email"])."</td>";
                echo "<td>";
                echo "<a href='?edit=".$row["id"]."'><button class='btn-edit'>Edit</button></a> ";
                echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this record?\")'>";
                echo "<input type='hidden' name='id' value='".$row["id"]."'>";
                echo "<button type='submit' name='action' value='delete' class='btn-delete'>Delete</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No records found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
