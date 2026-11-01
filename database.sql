-- Hospital Management System
-- MySQL 8.0+ | Import this file in phpMyAdmin or MySQL Workbench

CREATE DATABASE IF NOT EXISTS hospital_management_system
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hospital_management_system;

SET FOREIGN_KEY_CHECKS = 0;
DROP VIEW IF EXISTS pending_bills;
DROP TRIGGER IF EXISTS trg_create_bill_after_completion;
DROP TABLE IF EXISTS Bill;
DROP TABLE IF EXISTS Appointment;
DROP TABLE IF EXISTS Patient;
DROP TABLE IF EXISTS Doctor;
DROP TABLE IF EXISTS Department;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE Department (
  dept_id INT AUTO_INCREMENT PRIMARY KEY,
  dept_name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE Doctor (
  doctor_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  specialization VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  dept_id INT,
  CONSTRAINT fk_doctor_department FOREIGN KEY (dept_id)
    REFERENCES Department(dept_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE Patient (
  patient_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  age INT,
  gender ENUM('Male','Female','Other') DEFAULT 'Other',
  phone VARCHAR(20),
  address VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_patient_age CHECK (age IS NULL OR age BETWEEN 1 AND 120)
) ENGINE=InnoDB;

CREATE TABLE Appointment (
  appt_id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  doctor_id INT NOT NULL,
  appt_date DATETIME NOT NULL,
  reason VARCHAR(255),
  status ENUM('Booked','Completed','Cancelled') NOT NULL DEFAULT 'Booked',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_appointment_patient FOREIGN KEY (patient_id)
    REFERENCES Patient(patient_id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_appointment_doctor FOREIGN KEY (doctor_id)
    REFERENCES Doctor(doctor_id) ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uq_doctor_appointment_time (doctor_id, appt_date),
  INDEX idx_appointment_date (appt_date),
  INDEX idx_appointment_status (status)
) ENGINE=InnoDB;

CREATE TABLE Bill (
  bill_id INT AUTO_INCREMENT PRIMARY KEY,
  appt_id INT NOT NULL UNIQUE,
  amount DECIMAL(10,2) NOT NULL DEFAULT 500.00,
  pay_status ENUM('Pending','Paid') NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  paid_at DATETIME NULL,
  CONSTRAINT fk_bill_appointment FOREIGN KEY (appt_id)
    REFERENCES Appointment(appt_id) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_bill_amount CHECK (amount >= 0)
) ENGINE=InnoDB;

INSERT INTO Department (dept_name) VALUES
 ('Cardiology'), ('Orthopedics'), ('Neurology'), ('Pediatrics'), ('General Medicine');

INSERT INTO Doctor (name, specialization, phone, dept_id) VALUES
 ('Dr. Mehta', 'Cardiologist', '9876500001', 1),
 ('Dr. Singh', 'Orthopedic Surgeon', '9876500002', 2),
 ('Dr. Sharma', 'Neurologist', '9876500003', 3),
 ('Dr. Kaur', 'Pediatrician', '9876500004', 4),
 ('Dr. Verma', 'General Physician', '9876500005', 5);

INSERT INTO Patient (name, age, gender, phone, address) VALUES
 ('Ravi Kumar', 45, 'Male', '9876511111', 'Mohali'),
 ('Anita Sharma', 32, 'Female', '9876522222', 'Chandigarh');

-- Example appointments are in the future relative to the sample project date.
INSERT INTO Appointment (patient_id, doctor_id, appt_date, reason) VALUES
 (1, 1, '2026-11-02 10:00:00', 'Routine check-up'),
 (2, 2, '2026-11-03 11:00:00', 'Knee pain');

DELIMITER $$
CREATE TRIGGER trg_create_bill_after_completion
AFTER UPDATE ON Appointment
FOR EACH ROW
BEGIN
  IF NEW.status = 'Completed' AND OLD.status <> 'Completed' THEN
    INSERT INTO Bill (appt_id, amount, pay_status)
    VALUES (NEW.appt_id, 500.00, 'Pending')
    ON DUPLICATE KEY UPDATE appt_id = NEW.appt_id;
  END IF;
END$$
DELIMITER ;

CREATE VIEW pending_bills AS
SELECT b.bill_id, b.appt_id, p.name AS patient_name,
       d.name AS doctor_name, a.appt_date, b.amount, b.pay_status
FROM Bill b
JOIN Appointment a ON a.appt_id = b.appt_id
JOIN Patient p ON p.patient_id = a.patient_id
JOIN Doctor d ON d.doctor_id = a.doctor_id
WHERE b.pay_status = 'Pending';

-- Useful demonstration queries:
-- SELECT a.appt_id, p.name AS patient, d.name AS doctor, a.appt_date, a.status
-- FROM Appointment a JOIN Patient p ON p.patient_id=a.patient_id
-- JOIN Doctor d ON d.doctor_id=a.doctor_id ORDER BY a.appt_date;
-- SELECT d.name, COUNT(a.appt_id) AS total_appointments
-- FROM Doctor d LEFT JOIN Appointment a ON a.doctor_id=d.doctor_id GROUP BY d.doctor_id,d.name;
-- UPDATE Appointment SET status='Completed' WHERE appt_id=1;
-- SELECT * FROM pending_bills;
