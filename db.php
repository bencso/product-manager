<?php

$HOST = "localhost";
$DB = "webfeladat";
$USER = "root";
$PASS = "";
$conn = new mysqli($HOST, $USER, $PASS, $DB);

if ($conn->connect_error) {
    die("Adatbázis hiba: " . $conn->connect_error);
}
$conn->set_charset("UTF8");

// -- Users tábla létrehozás
/*  Users tábla:
*   user_id INT AUTOINCREMENT PRIMARY KEY
*   username VARCHAR(50) NOT NULL UNIQUE
*   password VARCHAR(50) NOT NULL
*/

$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS users_tokens (
    user_id INT UNIQUE,
    token VARCHAR(255) NOT NULL,
    expires DATETIME DEFAULT (NOW() + INTERVAL 4 DAY),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

$conn->query($sql);

$sql = "SELECT COUNT(*) as count FROM users WHERE username = 'Teszt Elek'";
$result = $conn->query($sql);
$count = $result->fetch_assoc()['count'];

if ($count == 0) {
    $hashedPassword = password_hash("TesztElek123!", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES ('Teszt Elek', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hashedPassword);
    $stmt->execute();
    $stmt->close();
    $hashedPassword = password_hash("KisBela123!", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES ('Kis Béla!', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hashedPassword);
    $stmt->execute();
    $stmt->close();
}
