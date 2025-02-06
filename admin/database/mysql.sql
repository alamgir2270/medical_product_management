-- Drop the existing database and create a new one
DROP DATABASE IF EXISTS medical_supply_db;
CREATE DATABASE medical_supply_db;
USE medical_supply_db;

-- Create the User table with role column
CREATE TABLE User (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user' -- Added role column with default value 'user'
);

-- Create Orders table with status column
CREATE TABLE Orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    order_date DATE,
    status ENUM('pending', 'completed', 'shipped', 'cancelled') DEFAULT 'pending', -- Added status column
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Create Supplier table
CREATE TABLE Supplier (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(50),
    address VARCHAR(255)
);

-- Create Item table
CREATE TABLE Item (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2),
    supplier_id INT,
    FOREIGN KEY (supplier_id) REFERENCES Supplier(supplier_id)
);

-- Create Inventory table
CREATE TABLE Inventory (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    item_id INT,
    quantity INT NOT NULL,
    FOREIGN KEY (item_id) REFERENCES Item(item_id)
);

-- Create Order_Item table
CREATE TABLE Order_Item (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    item_id INT,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id),
    FOREIGN KEY (item_id) REFERENCES Item(item_id)
);

-- Insert Users (with roles for admin and regular users)
INSERT INTO User (username, email, password, role) VALUES
('admin1', 'admin1@example.com', 'adminpassword', 'admin'),
('user1', 'user1@example.com', 'password1', 'user'),
('user2', 'user2@example.com', 'password2', 'user'),
('user3', 'user3@example.com', 'password3', 'user'),
('user4', 'user4@example.com', 'password4', 'user'),
('user5', 'user5@example.com', 'password5', 'user'),
('user6', 'user6@example.com', 'password6', 'user'),
('user7', 'user7@example.com', 'password7', 'user'),
('user8', 'user8@example.com', 'password8', 'user'),
('user9', 'user9@example.com', 'password9', 'user'),
('user10', 'user10@example.com', 'password10', 'user'),
('user11', 'user11@example.com', 'password11', 'user'),
('user12', 'user12@example.com', 'password12', 'user'),
('user13', 'user13@example.com', 'password13', 'user'),
('user14', 'user14@example.com', 'password14', 'user'),
('user15', 'user15@example.com', 'password15', 'user');

-- Insert Suppliers
INSERT INTO Supplier (name, contact, address) VALUES
('Bangladesh Pharma Ltd.', '018XXXXXXXX', 'Dhaka, Bangladesh'),
('Medico Supplies Ltd.', '017XXXXXXXX', 'Chittagong, Bangladesh'),
('HealthPlus Supplies', '016XXXXXXXX', 'Sylhet, Bangladesh'),
('MedPro Distributors', '019XXXXXXXX', 'Rajshahi, Bangladesh'),
('Surgical House Ltd.', '015XXXXXXXX', 'Khulna, Bangladesh'),
('Bangla Healthcare', '014XXXXXXXX', 'Barisal, Bangladesh'),
('LifeCare Suppliers', '013XXXXXXXX', 'Mymensingh, Bangladesh'),
('CureMed Suppliers', '012XXXXXXXX', 'Rangpur, Bangladesh'),
('Bangladesh Surgical Co.', '011XXXXXXXX', 'Narayanganj, Bangladesh'),
('MedSupply Ltd.', '010XXXXXXXX', 'Jessore, Bangladesh');

-- Insert Items (Medical supplies)
INSERT INTO Item (name, description, price, supplier_id) VALUES 
('Surgical Gloves', 'Sterile surgical gloves for medical use', 100.50, 1),
('Face Mask', '3-ply face masks for safety and hygiene', 20.00, 2),
('Hand Sanitizer', 'Alcohol-based hand sanitizer for cleaning hands', 150.00, 3),
('Syringe 5ml', 'Disposable 5ml syringes for injections', 10.00, 4),
('Infusion Set', 'IV infusion sets for hospital use', 50.00, 5),
('Thermometer', 'Digital thermometer for body temperature measurement', 200.00, 6),
('Blood Pressure Monitor', 'Automatic blood pressure monitoring device', 2500.00, 7),
('Stethoscope', 'Professional stethoscope for medical examinations', 1500.00, 8),
('Wound Dressing', 'Sterile dressing for wound care', 80.00, 9),
('Surgical Mask', 'Medical grade surgical masks', 30.00, 10),
('Cotton Roll', 'Medical cotton roll for various uses', 20.00, 1),
('Bandage', 'Elastic bandages for dressing wounds', 40.00, 2),
('Oxygen Mask', 'Mask for delivering oxygen', 300.00, 3),
('Nebulizer', 'Portable nebulizer for respiratory treatments', 500.00, 4),
('Disinfectant Solution', 'Antiseptic disinfectant solution', 100.00, 5),
('Glucose Meter', 'Blood glucose monitoring device', 1200.00, 6),
('Injectable Insulin', 'Insulin for diabetes treatment', 800.00, 7),
('ECG Machine', 'Electrocardiogram machine for heart monitoring', 50000.00, 8),
('Stretcher', 'Emergency stretcher for patient transport', 1500.00, 9),
('Surgical Scalpel', 'Sterile surgical scalpel for cutting', 150.00, 10);

-- Insert Inventory (quantity of items)
INSERT INTO Inventory (item_id, quantity) VALUES
(1, 200),
(2, 300),
(3, 150),
(4, 500),
(5, 100),
(6, 50),
(7, 20),
(8, 30),
(9, 100),
(10, 400),
(11, 250),
(12, 300),
(13, 100),
(14, 150),
(15, 200),
(16, 75),
(17, 60),
(18, 25),
(19, 40),
(20, 15);

-- Insert Orders with status values
INSERT INTO Orders (user_id, order_date, status) VALUES
(1, '2024-11-01', 'pending'),
(2, '2024-11-03', 'completed'),
(3, '2024-11-05', 'shipped'),
(4, '2024-11-07', 'pending'),
(5, '2024-11-09', 'cancelled'),
(6, '2024-11-11', 'shipped'),
(7, '2024-11-13', 'completed'),
(8, '2024-11-15', 'pending'),
(9, '2024-11-17', 'shipped'),
(10, '2024-11-19', 'cancelled'),
(11, '2024-11-21', 'pending'),
(12, '2024-11-23', 'shipped'),
(13, '2024-11-25', 'completed'),
(14, '2024-11-27', 'cancelled'),
(15, '2024-11-29', 'pending');

-- Insert Order_Items (items ordered with quantities)
INSERT INTO Order_Item (order_id, item_id, quantity) VALUES
(1, 1, 20),
(1, 2, 50),
(1, 3, 30),
(1, 4, 10),
(2, 5, 20),
(2, 6, 10),
(3, 7, 15),
(3, 8, 5),
(4, 9, 10),
(4, 10, 5),
(5, 11, 50),
(5, 12, 25),
(6, 13, 30),
(6, 14, 10),
(7, 15, 20),
(7, 16, 10),
(8, 17, 5),
(8, 18, 2),
(9, 19, 10),
(9, 20, 1),
(10, 1, 10),
(10, 3, 40),
(11, 2, 25),
(11, 5, 10),
(12, 6, 20),
(12, 7, 15),
(13, 8, 5),
(13, 9, 15),
(14, 10, 25),
(14, 11, 20),
(15, 12, 30);

-- Update Inventory Quantities (based on the orders made)
UPDATE Inventory SET quantity = quantity - 20 WHERE item_id = 1;
UPDATE Inventory SET quantity = quantity - 50 WHERE item_id = 2;
UPDATE Inventory SET quantity = quantity - 30 WHERE item_id = 3;
UPDATE Inventory SET quantity = quantity - 10 WHERE item_id = 4;
UPDATE Inventory SET quantity = quantity - 20 WHERE item_id = 5;
UPDATE Inventory SET quantity = quantity - 10 WHERE item_id = 6;
UPDATE Inventory SET quantity = quantity - 15 WHERE item_id = 7;
UPDATE Inventory SET quantity = quantity - 5 WHERE item_id = 8;
UPDATE Inventory SET quantity = quantity - 10 WHERE item_id = 9;
UPDATE Inventory SET quantity = quantity - 5 WHERE item_id = 10;
UPDATE Inventory SET quantity = quantity - 50 WHERE item_id = 11;
UPDATE Inventory SET quantity = quantity - 25 WHERE item_id = 12;
UPDATE Inventory SET quantity = quantity - 30 WHERE item_id = 13;
UPDATE Inventory SET quantity = quantity - 10 WHERE item_id = 14;
UPDATE Inventory SET quantity = quantity - 20 WHERE item_id = 15;
UPDATE Inventory SET quantity = quantity - 10 WHERE item_id = 16;
UPDATE Inventory SET quantity = quantity - 5 WHERE item_id = 17;
UPDATE Inventory SET quantity = quantity - 2 WHERE item_id = 18;
UPDATE Inventory SET quantity = quantity - 10 WHERE item_id = 19;
UPDATE Inventory SET quantity = quantity - 1 WHERE item_id = 20;