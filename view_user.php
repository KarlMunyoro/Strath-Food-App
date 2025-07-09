<?php
// Include database connection
require_once "connection.php";

// Check if status column exists, if not, don't include it in query
$statusExists = false;
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
if ($result && $result->num_rows > 0) {
    $statusExists = true;
}

// Build query based on available columns
if ($statusExists) {
    $sql = "SELECT id, fullname, email, role, status, registration_date FROM users ORDER BY registration_date DESC";
} else {
    $sql = "SELECT id, fullname, email, role, registration_date FROM users ORDER BY id DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users - Strathmore University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="main-header">
        <h1>Strathmore University Cafeteria Ordering System</h1>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="registration.html">Register</a></li>
                <li><a href="login.html">Login</a></li>
                <li><a href="view_user.php">View Users</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="container">
        <h2>Registered Users</h2>
        
        <?php
        if (!$result) {
            echo "<div class='error'>Error retrieving users: " . htmlspecialchars($conn->error) . "</div>";
        } else {
            $userCount = $result->num_rows;
            echo "<div class='user-count'>Total Users: $userCount</div>";
            
            if ($userCount > 0) {
                echo "<div style='overflow-x: auto;'>";
                echo "<table class='users-table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Full Name</th>";
                echo "<th>Email</th>";
                echo "<th>Role</th>";
                if ($statusExists) {
                    echo "<th>Status</th>";
                }
                echo "<th>Registration Date</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                while ($row = $result->fetch_assoc()) {
                    $roleClass = "role-" . $row["role"];
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td class='$roleClass'>" . ucfirst(htmlspecialchars($row["role"])) . "</td>";
                    
                    if ($statusExists) {
                        echo "<td>" . ucfirst(htmlspecialchars($row["status"] ?? 'active')) . "</td>";
                    }
                    
                    if (isset($row["registration_date"])) {
                        echo "<td>" . date('M j, Y g:i A', strtotime($row["registration_date"])) . "</td>";
                    } else {
                        echo "<td>N/A</td>";
                    }
                    echo "</tr>";
                }
                
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            } else {
                echo "<div class='error'>No users found. <a href='registration.html'>Register the first user</a>.</div>";
            }
        }
        ?>
    </main>
    
    <footer class="main-footer">
        <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>