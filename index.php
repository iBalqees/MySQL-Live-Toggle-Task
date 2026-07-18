<?php
$servername = "sql302.infinityfree.com"; 
$username = "if0_42440788";            
$dbname = "if0_42440788_task";         
$password = "***********";   

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $name = $_POST['name'];
        $age = $_POST['age'];
        
        $sql = "INSERT INTO users (name, age, status) VALUES ('$name', '$age', 0)";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit();
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'toggle') {
        $id = $_POST['id'];
        
        $result = $conn->query("SELECT status FROM users WHERE id = $id");
        if ($row = $result->fetch_assoc()) {
            $new_status = ($row['status'] == 0) ? 1 : 0;
            $conn->query("UPDATE users SET status = $new_status WHERE id = $id");
            
            echo $new_status;
            exit();
        }
    }
}

$sql = "SELECT * FROM users ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
            background-color: #f9f9f9;
        }
        .form-container {
            margin-bottom: 20px;
            background: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .form-inline {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .form-inline input[type="text"], .form-inline input[type="number"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
            width: 120px;
        }
        .form-inline button {
            padding: 5px 15px;
            cursor: pointer;
        }
        table {
            border-collapse: collapse;
            width: 500px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .toggle-btn {
            padding: 3px 10px;
            cursor: pointer;
            border: 1px solid #999;
            background: #eee;
            border-radius: 3px;
        }
        .toggle-btn:hover {
            background: #ddd;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <form class="form-inline" method="POST" action="index.php">
            <input type="hidden" name="action" value="add">
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>
            
            <button type="submit">Submit</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['age']; ?></td>
						<td id="status-<?php echo $row['id']; ?>"><?php echo $row['status']; ?></td>
                        <td>
                            <button class="toggle-btn" onclick="toggleStatus(<?php echo $row['id']; ?>)">Toggle</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function toggleStatus(userId) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "index.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById("status-" + userId).innerText = xhr.responseText;
            }
        };
        
        xhr.send("action=toggle&id=" + userId);
    }
    </script>

</body>
</html>