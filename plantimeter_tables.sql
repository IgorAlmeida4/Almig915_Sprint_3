DROP DATABASE IF EXISTS Plantimeter;
CREATE DATABASE Plantimeter;
USE Plantimeter;

CREATE TABLE Operator (
    pk_operator INT AUTO_INCREMENT PRIMARY KEY,
    emailAddress VARCHAR(100) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50),
    passwordHash VARCHAR(128) NOT NULL
);

CREATE TABLE PlantVariety (
    pk_plantVariety INT AUTO_INCREMENT PRIMARY KEY,
    commonName VARCHAR(100) NOT NULL,
    botanicalName VARCHAR(100),
    ambientBrightnessThreshold INT DEFAULT 50 NOT NULL,
    soilMoistureThreshold INT DEFAULT 40 NOT NULL,
    airTemperatureOptimal INT DEFAULT 23 NOT NULL,
    airHumidityOptimal INT DEFAULT 60 NOT NULL
);

CREATE TABLE Node (
    pk_node INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) DEFAULT NULL,
    macAddress VARCHAR(17) UNIQUE NOT NULL,
    fk_plantVariety_contains INT,
    plantingDate DATE,
    fk_operator_belongs INT DEFAULT 1 NOT NULL,
    CONSTRAINT fk_node_operator FOREIGN KEY (fk_operator_belongs) REFERENCES Operator(pk_operator),
    CONSTRAINT fk_node_plantvariety FOREIGN KEY (fk_plantVariety_contains) REFERENCES PlantVariety(pk_plantVariety)
);

CREATE TABLE Measurement (
    pk_measurement INT AUTO_INCREMENT PRIMARY KEY,
    recordDateTime DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    soilMoisture FLOAT,
    airHumidity FLOAT,
    airTemperature FLOAT,
    ambientBrightness FLOAT,
    lampBrightness INT DEFAULT 0,
    fk_node_isRecorded INT NOT NULL,
    CONSTRAINT fk_measurement_node FOREIGN KEY (fk_node_isRecorded) REFERENCES Node(pk_node)
);

CREATE TABLE Task (
    pk_task INT AUTO_INCREMENT PRIMARY KEY,
    scheduledDateTime DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    taskType ENUM('pump', 'light') NOT NULL,
    setPoint INT NOT NULL,
    completionFlag BOOLEAN DEFAULT 0 NOT NULL,
    fk_node_isAssigned INT NOT NULL,
    CONSTRAINT fk_task_node FOREIGN KEY (fk_node_isAssigned) REFERENCES Node(pk_node)
);