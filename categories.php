<?php
// Database connection
$host = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = ""; // Change to your actual database name

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle category submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["category_name"], $_POST["description"])) {
    $category_name = trim($_POST["category_name"]);
    $description = trim($_POST["description"]);

    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO Categories (category_name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $category_name, $description);
        if ($stmt->execute()) {
            echo "<p class='success'>Category added successfully!</p>";
        } else {
            echo "<p class='error'>Error: " . $conn->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='error'>Category name cannot be empty!</p>";
    }
}

// Fetch all categories
$result = $conn->query("SELECT * FROM ategories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 50%;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: #f9f9f9;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Category Management</h2>

    <!-- Form to add a category -->
    <form action="" method="post">
        <input type="text" name="category_name" placeholder="Enter category name" required>
        <textarea name="description" placeholder="Enter category description" rows="3"></textarea>
        <button type="submit">Add Category</button>
    </form>

    <h3>Category List</h3>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<li><strong>" . htmlspecialchars($row["category_name"]) . "</strong>: " . htmlspecialchars($row["description"]) . "</li>";
            }
        } else {
            echo "<li>No categories found</li>";
        }
        ?>
    </ul>
</div>

</body>
</html>

<?php
$conn->close();
?>
