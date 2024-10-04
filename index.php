<?php
include 'dbinit.php'; // Include the database initialization script

// Initialize variables
$addSuccess = false;
$addError = false;
$updateSuccess = false;
$deleteSuccess = false;

// Initialize variables for book form
$bookName = "";
$author = "";
$description = "";
$quantity = "";
$price = "";
$genre = "";
$editBookID = 0;

// Handle Create, Update, and Delete requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if 'action' is set
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
            $stmt->bind_param("ssssids", $bookName, $author, $description, $quantity, $price, $genre, $productAddedBy);

            if ($stmt->execute()) {
                $addSuccess = true;
                $bookName = $author = $description = $quantity = $price = $genre = ""; // Clear form fields
            } else {
                $addError = "An error occurred while adding the book: " . $stmt->error;
            }
            $stmt->close();
        } elseif ($action == 'edit') {
            // Update an existing book
            $editBookID = intval($_POST['editBookID']);
            $bookName = $_POST['BookName'] ?? '';
            $author = $_POST['Author'] ?? '';
            $description = $_POST['Description'] ?? '';
            $quantity = intval($_POST['Quantity'] ?? 0);
            $price = floatval($_POST['Price'] ?? 0);
            $genre = $_POST['Genre'] ?? '';

            $stmt = $conn->prepare("UPDATE books SET BookName=?, Author=?, Description=?, Quantity=?, Price=?, Genre=? WHERE BookID=?");
            $stmt->bind_param("sssid", $bookName, $author, $description, $quantity, $price, $genre, $editBookID);

            if ($stmt->execute()) {
                $updateSuccess = true;
            } else {
                $addError = "An error occurred while updating the book: " . $stmt->error;
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
    <title>Book Inventory Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->
</head>
<body>
<div class="container">
    <h2>Book Inventory Management</h2>
    
    <!-- Add / Edit Book Form -->
    <div class="card">
        <form action="index.php" method="post">
            <div class="form-group">
                <input type="hidden" name="editBookID" value="<?= $editBookID; ?>">
                <label for="BookName">Book Name</label>
                <input type="text" name="BookName" class="form-control" value="<?= htmlspecialchars($bookName); ?>" required>
            </div>
            <div class="form-group">
                <label for="Author">Author</label>
                <input type="text" name="Author" class="form-control" value="<?= htmlspecialchars($author); ?>" required>
            </div>
            <div class="form-group">
                <label for="Description">Description</label>
                <textarea name="Description" class="form-control" rows="3" required><?= htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="Quantity">Quantity</label>
                <input type="number" name="Quantity" class="form-control" value="<?= htmlspecialchars($quantity); ?>" required>
            </div>
            <div class="form-group">
                <label for="Price">Price</label>
                <input type="number" step="0.01" name="Price" class="form-control" value="<?= htmlspecialchars($price); ?>" required>
            </div>
            <div class="form-group">
                <label for="Genre">Genre</label>
                <input type="text" name="Genre" class="form-control" value="<?= htmlspecialchars($genre); ?>" required>
            </div>
            <input type="hidden" name="action" value="<?= $editBookID ? 'edit' : 'add'; ?>">
            <button type="submit" class="btn btn-custom"><?= $editBookID ? 'Update Book' : 'Add New Book'; ?></button>
            <?php if ($addSuccess): ?>
                <div class="alert alert-success">Book added successfully!</div>
            <?php elseif ($updateSuccess): ?>
                <div class="alert alert-success">Book updated successfully!</div>
            <?php elseif ($deleteSuccess): ?>
                <div class="alert alert-success">Book deleted successfully!</div>
            <?php elseif ($addError): ?>
                <div class="alert alert-danger"><?= $addError; ?></div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Display All Books -->
    <h3>Current Inventory</h3>
    <?php foreach ($books as $book): ?>
        <div class="card book-card">
            <h5><?= htmlspecialchars($book['BookName']); ?></h5>
            <p><strong>Author:</strong> <?= htmlspecialchars($book['Author']); ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($book['Description']); ?></p>
            <p><strong>Quantity:</strong> <?= htmlspecialchars($book['Quantity']); ?></p>
            <p><strong>Price:</strong> $<?= htmlspecialchars($book['Price']); ?></p>
            <p><strong>Genre:</strong> <?= htmlspecialchars($book['Genre']); ?></p>
            <p><strong>Added By:</strong> <?= htmlspecialchars($book['ProductAddedBy']); ?></p>
            <form action="index.php" method="post" style="display: inline;">
                <input type="hidden" name="editBookID" value="<?= $book['BookID']; ?>">
                <button type="submit" class="btn btn-edit btn-warning" name="action" value="edit">Edit</button>
            </form>
            <form action="index.php" method="post" style="display: inline;">
                <input type="hidden" name="deleteBookID" value="<?= $book['BookID']; ?>">
                <button type="submit" class="btn btn-delete btn-danger" name="action" value="delete">Delete</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
