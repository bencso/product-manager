<?php
header('Content-Type: application/json');
switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        post_valid();
        break;
}

function post_valid()
{
    include "../db.php";

    if (!isset($_COOKIE["token"])) {
        echo json_encode(["status" => 401, "message" => "Nincs token"]);
        exit;
    }

    $rawToken = $_COOKIE["token"];
    $hashedToken = hash('sha256', $rawToken);

    $sql = "SELECT expires, COUNT(*) as count FROM users_tokens WHERE token = ? AND user_id IS NOT NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hashedToken);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $count = $result["count"];
    $expires = $result["expires"];
    $stmt->close();

    if (strtotime($expires) > time() && $count > 0) {
        echo json_encode(["status" => 200, "message" => "Érvényes token"]);
    } else {
        setcookie("token", "", time() - 3600, "/", "", true, true);
        echo json_encode(["status" => 401, "message" => "Érvénytelen token"]);
    }
}
