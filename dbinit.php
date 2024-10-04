<?php
$servername = "localhost";
$username = " "; 
$password = " "; 
$dbname = " "; 

// Create connection
$servername="localhost";
$username = "root"; 
$password = ""; 
$dbname = "book_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
if ($conn->query("CREATE DATABASE IF NOT EXISTS $dbname") === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db($dbname);

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS books (
    BookID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    BookName VARCHAR(255) NOT NULL,
    Author VARCHAR(255) NOT NULL,
    Description TEXT NOT NULL,
    Quantity INT NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    Genre VARCHAR(100) NOT NULL,
    ProductAddedBy VARCHAR(100) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'books' created successfully!<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Close the connection
$conn->close();
?>
