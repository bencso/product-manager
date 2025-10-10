<?php
header('Content-Type: application/json');
switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        get_logout();
        break;
}

function get_logout()
{
    include "../db.php";

    if (!isset($_COOKIE["token"])) {
        header("Location: /termekek_feladat");
        echo json_encode(["status" => 401, "message" => "Nincs token"]);
        exit;
    }

    $rawToken = $_COOKIE["token"];
    $hashedToken = hash('sha256', $rawToken);

    $sql = "DELETE FROM users_tokens WHERE token = ? AND user_id IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hashedToken);
    $stmt->execute();

    setcookie("token", "", time() - 3600, "/", "", true, true);
    echo json_encode(["status" => 200, "message" => "Sikeres kijelentkezés"]);
}
