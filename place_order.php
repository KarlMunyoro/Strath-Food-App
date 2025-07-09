<?php
session_start();
require_once "connection.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$message = "";

// Only process form if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmed'])) {
    $menu_id = $_POST['menu_id'];
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];

    // Check price and availability
    $stmt = $conn->prepare("SELECT name, price, available FROM menu WHERE id = ?");
    $stmt->bind_param("i", $menu_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    if ($item && $item['available']) {
        $total_price = $item['price'] * $quantity;

        // Save order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, menu_id, quantity, total_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $user_id, $menu_id, $quantity, $total_price);
        $stmt->execute();
        $stmt->close();

        $message = "✅ Order placed for <strong>{$item['name']}</strong>.<br>Please pay <strong>Ksh " . number_format($total_price, 2) . "</strong>";
    } else {
        $message = "❌ The selected item is not available.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Order</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function confirmPayment(event) {
            event.preventDefault();

            const menu = document.querySelector('select[name="menu_id"]');
            const selectedOption = menu.options[menu.selectedIndex];
            const quantity = parseInt(document.querySelector('input[name="quantity"]').value);

            if (!menu.value) {
                alert("Please select a menu item.");
                return;
            }

            if (quantity <= 0) {
                alert("Quantity must be at least 1.");
                return;
            }

            // Get price from option text using RegExp
            const priceMatch = selectedOption.text.match(/Ksh (\d+(?:\.\d+)?)/);
            if (!priceMatch) {
                alert("Could not find price.");
                return;
            }

            const price = parseFloat(priceMatch[1]);
            const total = price * quantity;

            const confirmOrder = confirm(`You are about to place an order worth Ksh ${total.toFixed(2)}.\nDo you want to continue and pay now?`);

            if (confirmOrder) {
                const form = document.getElementById('orderForm');

                
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'confirmed';
                hidden.value = '1';
                form.appendChild(hidden);

                form.submit();
            }
        }
    </script>
</head>
<body>
    <h2>🛒 Place an Order</h2>

    <?php if ($message): ?>
        <div style="background: #e7ffe7; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="orderForm">
        <label for="menu_id">Choose Item:</label><br>
        <select name="menu_id" required>
            <option value="">-- Select an item --</option>
            <?php
            $result = $conn->query("SELECT id, name, price, available FROM menu ORDER BY name ASC");
            while ($row = $result->fetch_assoc()) {
                $status = $row['available'] ? "Available ✅" : "Unavailable ❌";
                $disabled = $row['available'] ? "" : "disabled";
                echo "<option value='{$row['id']}' $disabled>{$row['name']} - Ksh {$row['price']} ({$status})</option>";
            }
            ?>
        </select><br><br>

        <label for="quantity">Quantity:</label><br>
        <input type="number" name="quantity" value="1" min="1" required><br><br>

        <button onclick="confirmPayment(event)">Place Order</button>
    </form>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>
</body>
</html>
