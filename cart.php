<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_to_cart"])) {
        $product_id = $_POST["product_id"];
        $quantity = $_POST["quantity"];

        $sql = "SELECT * FROM Products WHERE product_id = $product_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'product_name' => $product['product_name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
            echo "<p style='color: green;'>Product added to cart!</p>";
        } else {
            echo "<p style='color: red;'>Product not found!</p>";
        }
    }

    if (isset($_POST["update_cart"])) {
        $product_id = $_POST["product_id"];
        $quantity = $_POST["quantity"];

        if (isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
            echo "<p style='color: green;'>Cart updated!</p>";
        } else {
            echo "<p style='color: red;'>Product not found in cart!</p>";
        }
    }

    if (isset($_POST["remove_from_cart"])) {
        $product_id = $_POST["product_id"];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            echo "<p style='color: green;'>Product removed from cart!</p>";
        } else {
            echo "<p style='color: red;'>Product not found in cart!</p>";
        }
    }
}

$products_sql = "SELECT * FROM Products";
$products_result = $conn->query($products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background-color: #f9f9f9;
            margin: 20px;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .cart-total {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 3px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Shopping Cart</h1>

        <h2>Add Product to Cart</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="product_id">Select Product:</label>
                <select name="product_id" required>
                    <option value="">Select a product</option>
                    <?php
                    if ($products_result->num_rows > 0) {
                        while ($row = $products_result->fetch_assoc()) {
                            echo "<option value='{$row['product_id']}'>{$row['product_name']} - {$row['price']} USD</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" value="1" min="1" required>
            </div>
            <button type="submit" name="add_to_cart">Add to Cart</button>
        </form>

        <h2>Your Cart</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php
                $cart_total = 0;
                foreach ($_SESSION['cart'] as $product_id => $item):
                    $total = $item['price'] * $item['quantity'];
                    $cart_total += $total;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['price']); ?> USD</td>
                        <td>
                            <form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='product_id' value='<?php echo $product_id; ?>'>
                                <input type='number' name='quantity' value='<?php echo $item['quantity']; ?>' min='1' required>
                                <button type='submit' name='update_cart'>Update</button>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($total); ?> USD</td>
                        <td>
                            <form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='product_id' value='<?php echo $product_id; ?>'>
                                <button type='submit' name='remove_from_cart'>Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div class="cart-total">
                Total Price: <?php echo $cart_total; ?> USD
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>