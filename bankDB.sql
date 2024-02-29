
DROP TABLE IF EXISTS `admin`;

CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(255) NOT NULL,
    pwd VARCHAR(255) NOT NULL
);
--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `pwd`) VALUES
(1, 'admin', 'admin');


CREATE TABLE admin_changes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(255) NOT NULL,
    admin_id INT NOT NULL,
    affected_user_id INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    account_total decimal(10,2) NOT NULL,
    failed_logins decimal(10,2) NULL,
    countdown_expiry decimal(10,2) NULL
);