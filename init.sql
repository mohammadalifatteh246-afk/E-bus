CREATE DATABASE IF NOT EXISTS ebus_db;
USE ebus_db;

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_number VARCHAR(50) NOT NULL,
    route_name VARCHAR(255) NOT NULL,
    from_location VARCHAR(255) NOT NULL,
    to_location VARCHAR(255) NOT NULL,
    departure_time TIME NOT NULL
);

CREATE TABLE IF NOT EXISTS passes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255),
    age INT,
    gender VARCHAR(10),
    email VARCHAR(255),
    from_location VARCHAR(255),
    to_location VARCHAR(255),
    distance_km DECIMAL(10,2) DEFAULT NULL,
    route VARCHAR(255),
    pass_type VARCHAR(50),
    pass_duration VARCHAR(50),
    pass_from DATE,
    pass_to DATE,
    fee DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bus_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_type_name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    bus_type_id INT,
    from_location VARCHAR(255),
    to_location VARCHAR(255),
    distance_km DECIMAL(10,2) DEFAULT NULL,
    seat_number VARCHAR(10),
    booking_date DATE,
    amount DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'Pending'
);

INSERT INTO admin (email, password) VALUES ('admin@admin.com', 'admin');

-- For existing databases: add distance_km column if not present
-- Run these manually if tables already exist:
-- ALTER TABLE passes ADD COLUMN distance_km DECIMAL(10,2) DEFAULT NULL AFTER to_location;
-- ALTER TABLE bookings ADD COLUMN distance_km DECIMAL(10,2) DEFAULT NULL AFTER to_location;
