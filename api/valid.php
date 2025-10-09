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
        return;
    }

    $rawToken = $_COOKIE["token"];
    $hashedToken = hash('sha256', $rawToken);

    $sql = "SELECT COUNT(*) as count, expires FROM users_tokens WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hashedToken);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $count = $result["count"];
    $expires = $result["expires"];

    if (strtotime($expires) > time() && $count > 0) {
        echo json_encode(["status" => 200, "message" => "Érvényes token"]);
        return;
    } else {
        echo json_encode(["status" => 401, "message" => "Érvénytelen token"]);
        return;
    }
}
