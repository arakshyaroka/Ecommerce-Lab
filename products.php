<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_product"])) {
    $product_name = $conn->real_escape_string($_POST["product_name"]);
    $description = $conn->real_escape_string($_POST["description"]);
    $price = $conn->real_escape_string($_POST["price"]);
    $category_id = $conn->real_escape_string($_POST["category_id"]);

    $sql = "INSERT INTO Products (product_name, description, price, category_id)
            VALUES ('$product_name', '$description', '$price', '$category_id')";

    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Product created successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}

$categories_sql = "SELECT * FROM Categories";
$categories_result = $conn->query($categories_sql);

$selected_category_id = isset($_GET["category_id"]) ? $_GET["category_id"] : null;
$products_sql = "SELECT p.product_id, p.product_name, p.description, p.price, c.category_name 
FROM Products p
JOIN Categories c ON p.category_id = c.category_id";
if ($selected_category_id) {
    $products_sql .= " WHERE p.category_id = $selected_category_id";
}
$products_result = $conn->query($products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f8f8f8;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 20px auto;
        }
        form label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #5c85d6;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            margin-top: 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #3b6ec2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #5c85d6;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9e9e9;
        }
        .success {
            color: green;
            text-align: center;
            font-weight: bold;
        }
        .error {
            color: red;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Product Management</h1>
    
    <form method="POST" action="">
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" required>

        <label for="description">Description:</label>
        <textarea name="description" rows="3"></textarea>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" required>

        <label for="category_id">Category:</label>
        <select name="category_id" required>
            <option value="">Select a category</option>
            <?php
            if ($categories_result->num_rows > 0) {
                while ($row = $categories_result->fetch_assoc()) {
                    echo "<option value='{$row['category_id']}'>{$row['category_name']}</option>";
                }
            }
            ?>
        </select>
        
        <button type="submit" name="create_product">Create Product</button>
    </form>

    <h2>List of Products</h2>
    <form method="GET" action="" style="text-align: center;">
        <label for="category_id">Filter by Category:</label>
        <select name="category_id" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php
            $categories_result->data_seek(0);
            while ($row = $categories_result->fetch_assoc()) {
                $selected = ($row['category_id'] == $selected_category_id) ? "selected" : "";
                echo "<option value='{$row['category_id']}' $selected>{$row['category_name']}</option>";
            }
            ?>
        </select>
    </form>

    <?php
    if ($products_result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                </tr>";
        while ($row = $products_result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['product_id']}</td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['price']}</td>
                    <td>{$row['category_name']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align:center; font-weight:bold;'>No products found.</p>";
    }
    $conn->close();
    ?>
</body>
</html>
