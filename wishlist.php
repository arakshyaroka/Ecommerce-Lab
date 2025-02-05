<?php
session_start(); 

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "ecommerce";

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize wishlist session
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Add product to wishlist
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_wishlist"])) {
    $product_id = $_POST["product_id"];

    $sql = "SELECT * FROM Products WHERE product_id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        if (!isset($_SESSION['wishlist'][$product_id])) {
            $_SESSION['wishlist'][$product_id] = [
                'product_name' => $product['product_name'],
                'price' => $product['price']
            ];
            echo "<script>alert('Product added to wishlist!');</script>";
        } else {
            echo "<script>alert('Product is already in your wishlist!');</script>";
        }
    } else {
        echo "<script>alert('Product not found!');</script>";
    }
}

// Remove product from wishlist
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_from_wishlist"])) {
    $product_id = $_POST["product_id"];

    if (isset($_SESSION['wishlist'][$product_id])) {
        unset($_SESSION['wishlist'][$product_id]);
        echo "<script>alert('Product removed from wishlist!');</script>";
    } else {
        echo "<script>alert('Product not found in wishlist!');</script>";
    }
}

// Fetch products from database
$products_sql = "SELECT * FROM Products";
$products_result = $conn->query($products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1, h2 {
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        select, button {
            padding: 10px;
            font-size: 16px;
            margin-top: 10px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .remove-btn {
            background-color: #dc3545;
        }
        .remove-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <h1>Wishlist</h1>

    <h2>Add Product to Wishlist</h2>
    <form method="POST" action="">
        <label for="product_id">Select Product:</label>
        <select name="product_id" required>
            <option value="">Select a product</option>
            <?php
            if ($products_result->num_rows > 0) {
                while ($row = $products_result->fetch_assoc()) {
                    echo "<option value='{$row['product_id']}'>{$row['product_name']} - {$row['price']} </option>";
                }
            }
            ?>
        </select>
        <br><br>
        <button type="submit" name="add_to_wishlist">Add to Wishlist</button>
    </form>

    <h2>Your Wishlist</h2>

    <?php if (!empty($_SESSION['wishlist'])): ?>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
            <?php foreach ($_SESSION['wishlist'] as $product_id => $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <button type="submit" name="remove_from_wishlist" class="remove-btn">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center;">Your wishlist is empty.</p>
    <?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>