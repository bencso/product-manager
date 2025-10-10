<?php

$HOST = "localhost";
$DB = "webfeladat";
$USER = "root";
$PASS = "";
$conn = new mysqli($HOST, $USER, $PASS);

if ($conn->connect_error) {
    die("Adatbázis hiba: " . $conn->connect_error);
}
$conn->set_charset("UTF8");

$sql = "CREATE DATABASE IF NOT EXISTS " . $DB . ";";
$conn->query($sql);

$conn = new mysqli($HOST, $USER, $PASS, $DB);
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

// -- Termekek tábla létrehozás
/*  Products tábla:
*   cikkszam VARCHAR(50) NOT NULL UNIQUE
*   cikk_megnevezes VARCHAR(50) NOT NULL 
*   nettoar int(5) NOT NULL
*   afa int(5) NOT NULL
*/
$sql = "CREATE TABLE IF NOT EXISTS products (
    cikkszam VARCHAR(50) NOT NULL UNIQUE,
    cikk_megnevezes VARCHAR(50) NOT NULL ,
    nettoar int(5) NOT NULL,
    afa int(5) NOT NULL
)";

$conn->query($sql);

$sql = "SELECT COUNT(*) as count FROM products";
$result = $conn->query($sql);
$count = $result->fetch_assoc()['count'];

if ($count == 0) {
    $items = [
        ["d12750689", "ESR Aura Wallet Stand Bright White", 6760, 27],
        ["d5025253", "RODE DeadKitten", 11390, 27],
        ["d13079115", "iPhone 17 Pro Max 256 GB Kozmosznarancs", 600000, 18],
        ["d7774652", "FIFINE BM63", 15890, 27],
        ["d7404138", "XP-Pen grafikus kesztyű - L", 5190, 15],
        ["d5269024", "Szarvasi SZV-624 Unipress bordó", 32990, 10],
        ["d6726929", "Siguro Espresso Thermo pohár, 90 ml, 2 db", 2990, 27],
        ["d5833929", "Lavazza Gusto Forte, szemes, 1000 g", 6290, 18]
    ];

    $stmt = $conn->prepare("INSERT INTO products (cikkszam, cikk_megnevezes, nettoar, afa) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->bind_param("ssii", $item[0], $item[1], $item[2], $item[3]);
        $stmt->execute();
    }
    $stmt->close();
}

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
