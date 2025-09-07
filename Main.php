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

if ($_POST['action'] == 'add' && !empty($_POST['name']) && !empty($_POST['email'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
    if ($conn->query($sql) === TRUE) {
        $message = "Record added successfully";
    } else {
        $message = "Error: " . $conn->error;
    }
}

$sql = "SELECT id, name, email FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-group { margin: 10px 0; }
        input[type="text"], input[type="email"] { padding: 5px; width: 200px; }
        button { padding: 8px 15px; background-color: #007cba; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Database Manager</h1>
    
    <?php if (isset($message)) echo "<p style='color: green;'>$message</p>"; ?>
    
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
        <button type="submit" name="action" value="add">Add Record</button>
    </form>
    
    <h2>Records</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["email"]."</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No records found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
