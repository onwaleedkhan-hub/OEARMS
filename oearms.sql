-- Create database
CREATE DATABASE IF NOT EXISTS oearms;
USE oearms;

-- Admin table
CREATE TABLE admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin
INSERT INTO admin (username, password)
VALUES ('Waleed khan', 'Waleed@2003');

-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    agecny varchar(100) not null,
    phone VARCHAR(20),
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Agent table
CREATE TABLE Agent (
    Agent_ID INT PRIMARY KEY AUTO_INCREMENT,
    Agent_Name VARCHAR(100),
    Agency_Name VARCHAR(100),
    mobile_no VARCHAR(20),
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Sponsor table
CREATE TABLE Sponsor (
    Sponsor_ID INT PRIMARY KEY AUTO_INCREMENT,
    Sponsor_Number INT NOT NULL,
    Sponsor_Name VARCHAR(100)
);

-- Embassy table
CREATE TABLE Embassy (
    Embassy_ID INT PRIMARY KEY AUTO_INCREMENT,
    Location VARCHAR(100)
);

-- Insert default embassies
INSERT INTO Embassy (Location) 
VALUES ('Karachi'), ('Islamabad');

-- Visa table
CREATE TABLE Visa (
    Visa_ID INT PRIMARY KEY AUTO_INCREMENT,
    Visa_Type VARCHAR(20),
    Visa_No INT,
    Sponsor_ID INT NOT NULL,
    Embassy_ID INT NOT NULL,
    FOREIGN KEY (Sponsor_ID) REFERENCES Sponsor(Sponsor_ID) ,
    FOREIGN KEY (Embassy_ID) REFERENCES Embassy(Embassy_ID) 
);

-- Status table for Customers
CREATE TABLE Status (
    Status_ID INT PRIMARY KEY AUTO_INCREMENT,
    Status_Name VARCHAR(20) NOT NULL
);

-- Insert default statuses
INSERT INTO Status (Status_Name) VALUES ('Pending'), ('Approved');

-- Customers table
CREATE TABLE Customers (
    Customer_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    Father_Name VARCHAR(100),
    Passport_No CHAR(9),
    Phone VARCHAR(15),
    Send_Date DATE,
    Medical_Expiry DATE,
    E_Number VARCHAR(20),
    Permission VARCHAR(10),
    Amount INT,
    Agent_ID INT NOT NULL,
    Visa_ID INT NOT NULL,
    user_id INT NOT NULL,
    Status_ID INT NOT NULL,
    FOREIGN KEY (Agent_ID) REFERENCES Agent(Agent_ID) ,
    FOREIGN KEY (Visa_ID) REFERENCES Visa(Visa_ID) ,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ,
    FOREIGN KEY (Status_ID) REFERENCES Status(Status_ID)
);
