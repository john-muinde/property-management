-- Create database
CREATE DATABASE IF NOT EXISTS lakeside_resorts;

USE lakeside_resorts;

-- Rooms table
CREATE TABLE
  rooms (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    capacity INT NOT NULL,
    image VARCHAR(255),
    amenities TEXT,
    is_available TINYINT (1) DEFAULT 1,
    PRIMARY KEY (id)
  );

-- Spa Services table
CREATE TABLE
  spa_services (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL,
    image VARCHAR(255),
    is_available TINYINT (1) DEFAULT 1,
    PRIMARY KEY (id)
  );

-- Room Bookings table
CREATE TABLE
  room_bookings (
    id INT NOT NULL AUTO_INCREMENT,
    room_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    arrival_date DATE NOT NULL,
    departure_date DATE NOT NULL,
    adults INT NOT NULL DEFAULT 1,
    children INT NOT NULL DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (room_id) REFERENCES rooms (id)
  );

-- Spa Bookings table
CREATE TABLE
  spa_bookings (
    id INT NOT NULL AUTO_INCREMENT,
    service_id INT NOT NULL,
    guest_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (service_id) REFERENCES spa_services (id)
  );

-- Contact form submissions
CREATE TABLE
  contact_submissions (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    is_read TINYINT (1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
  );

-- Admin users
CREATE TABLE
  users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
  );

-- Sample data
INSERT INTO
  rooms (
    name,
    description,
    price,
    capacity,
    image,
    amenities
  )
VALUES
  (
    'Lakeside Suite',
    'Luxurious suite with panoramic views of the lake. Features a king-size bed, private balcony, and spa-inspired bathroom.',
    299.99,
    2,
    'room1.jpg',
    'King bed, Lake view, Private balcony, Jacuzzi tub, Free WiFi'
  ),
  (
    'Garden Villa',
    'Peaceful villa surrounded by lush gardens with a queen-size bed and private patio.',
    199.99,
    2,
    'room2.jpg',
    'Queen bed, Garden view, Private patio, Mini bar, Free WiFi'
  ),
  (
    'Family Cabin',
    'Spacious cabin with two bedrooms perfect for families, featuring a full kitchen and fireplace.',
    349.99,
    4,
    'room3.jpg',
    'Two bedrooms, Full kitchen, Fireplace, Lake view, Free WiFi'
  );

INSERT INTO
  spa_services (name, description, price, duration, image)
VALUES
  (
    'Deep Tissue Massage',
    'A therapeutic massage targeting deeper muscle layers to relieve tension and chronic muscle pain.',
    120.00,
    60,
    'banner1.jpg'
  ),
  (
    'Aromatherapy Facial',
    'A rejuvenating facial treatment using essential oils to cleanse, exfoliate, and nourish the skin.',
    95.00,
    45,
    'banner2.jpg'
  ),
  (
    'Hot Stone Therapy',
    'A relaxing massage using heated stones to melt away tension and promote deep relaxation.',
    135.00,
    75,
    'banner3.jpg'
  );

-- Create admin user (password: admin123)
INSERT INTO
  users (username, password, name)
VALUES
  (
    'admin',
    '$2y$10$8K1p/a4OZwTMh6gddkE7UuEJK4bj6r4S3wx1i6UfZHmaeK1B3kzWa',
    'Admin User'
  );