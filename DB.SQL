-- setup.sql

-- Create the database
CREATE DATABASE IF NOT EXISTS book_inventory;

-- Use the created database
USE book_inventory;

-- Create the books table
CREATE TABLE IF NOT EXISTS books (
    BookID INT AUTO_INCREMENT PRIMARY KEY,
    BookName VARCHAR(255) NOT NULL,
    Author VARCHAR(255) NOT NULL,
    Description TEXT NOT NULL,
    Quantity INT NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    Genre VARCHAR(100) NOT NULL,
    ProductAddedBy VARCHAR(100) NOT NULL DEFAULT 'Manav'
);
