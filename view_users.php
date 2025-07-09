<?php
<<<<<<< HEAD
=======
include "header.php";
>>>>>>> 3aba2fb0a28d917d1689891444b4ba87943182fe
require_once "connection.php";

// Fetch all users
$sql = "SELECT id, fullname, email, role, status, registration_date FROM users ORDER BY registration_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users - Strathmore Ordering</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Registered Users</h1>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registered On</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["id"]) ?></td>
                            <td><?= htmlspecialchars($row["fullname"]) ?></td>
                            <td><?= htmlspecialchars($row["email"]) ?></td>
                            <td><?= ucfirst($row["role"]) ?></td>
                            <td><?= ucfirst($row["status"]) ?></td>
                            <td><?= date("Y-m-d H:i", strtotime($row["registration_date"])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="back-button">Back</a>
    </div>
    <footer class="main-footer">
        <p>&copy; 2025 Strathmore University Cafeteria Ordering System. All rights reserved.</p>
    </footer>
</body>
</html>
