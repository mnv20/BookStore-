<?php
include 'dbinit.php'; 

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "book_inventory"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables 
$bookName = '';
$author = '';
$description = '';
$quantity = 0;
$price = 0;
$genre = '';
$editBookID = null; 

// Handle Create, Update, and Delete requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add') {
            // Add a new book
            $bookName = $_POST['BookName'] ?? '';
            $author = $_POST['Author'] ?? '';
            $description = $_POST['Description'] ?? '';
            $quantity = intval($_POST['Quantity'] ?? 0);
            $price = floatval($_POST['Price'] ?? 0);
            $genre = $_POST['Genre'] ?? '';
            $productAddedBy = 'Manav'; 

            $stmt = $conn->prepare("INSERT INTO books (BookName, Author, Description, Quantity, Price, Genre, ProductAddedBy) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssids", $bookName, $author, $description, $quantity, $price, $genre, $productAddedBy);

            if ($stmt->execute()) {
                $addSuccess = true;
            } else {
                $addError = "An error occurred while adding the book: " . $stmt->error;
            }
            $stmt->close();
        } elseif ($action == 'edit') {
            // Update an existing book
            $editBookID = intval($_POST['editBookID']);
            $stmt = $conn->prepare("SELECT * FROM books WHERE BookID=?");
            $stmt->bind_param("i", $editBookID);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $book = $result->fetch_assoc();
                $bookName = $book['BookName'];
                $author = $book['Author'];
                $description = $book['Description'];
                $quantity = $book['Quantity'];
                $price = $book['Price'];
                $genre = $book['Genre'];
            }
            $stmt->close();
        } elseif ($action == 'delete') {
            // Delete a book
            $deleteBookID = intval($_POST['deleteBookID']);
            $stmt = $conn->prepare("DELETE FROM books WHERE BookID=?");
            $stmt->bind_param("i", $deleteBookID);

            if ($stmt->execute()) {
                $deleteSuccess = true;
            } else {
                $addError = "An error occurred while deleting the book: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Retrieve all books
$books = [];
$result = $conn->query("SELECT * FROM books");

if (!$result) {
    die("Query failed: " . $conn->error); 
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

// Close the connection 
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
<div class="container">
    <h2>Book Inventory</h2>
    <div class="card">
        <form action="index.php" method="post" style="border: none;">
            <div class="form-group mb-3">
                <input type="hidden" name="editBookID" value="<?= $editBookID; ?>">
                <label for="BookName">Book Name</label>
                <input type="text" name="BookName" class="form-control" value="<?= htmlspecialchars($bookName); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="Author">Author</label>
                <input type="text" name="Author" class="form-control" value="<?= htmlspecialchars($author); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="Description">Description</label>
                <textarea name="Description" class="form-control" required><?= htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group mb-3">
                <label for="Quantity">Quantity</label>
                <input type="number" name="Quantity" class="form-control" value="<?= htmlspecialchars($quantity); ?>" required>
            </div>
            <div class="form-group mb-4">
                <label for="Price">Price</label>
                <input type="text" name="Price" class="form-control" value="<?= htmlspecialchars($price); ?>" required>
            </div>
            <div class="form-group mb-4">
                <label for="Genre">Genre</label>
                <input type="text" name="Genre" class="form-control" value="<?= htmlspecialchars($genre); ?>" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" name="action" value="add" class="btn btn-primary">Add Book</button>
                <button type="submit" name="action" value="edit" class="btn btn-warning">Update Book</button>
            </div>
        </form>
    </div>
    <h3 class="mt-5">Books in Inventory</h3>
    <div class="row">
        <?php foreach ($books as $book): ?>
            <div class="col-md-4">
                <div class="card book-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($book['BookName']); ?></h5>
                        <p class="card-text">Author: <?= htmlspecialchars($book['Author']); ?></p>
                        <p class="card-text">Description: <?= htmlspecialchars($book['Description']); ?></p>
                        <p class="card-text">Quantity: <?= htmlspecialchars($book['Quantity']); ?></p>
                        <p class="card-text">Price: $<?= htmlspecialchars($book['Price']); ?></p>
                        <p class="card-text">Genre: <?= htmlspecialchars($book['Genre']); ?></p>
                        <form method="post" action="index.php" class="d-inline">
                            <input type="hidden" name="deleteBookID" value="<?= $book['BookID']; ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
                        </form>
                        <form method="post" action="index.php" class="d-inline">
                            <input type="hidden" name="editBookID" value="<?= $book['BookID']; ?>">
                            <button type="submit" name="action" value="edit" class="btn btn-success">Edit</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
