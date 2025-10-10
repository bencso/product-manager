<?php
header('Content-Type: application/json');
switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        post_login();
        break;
}

function post_login()
{
    include "../db.php";

    $username = $_POST["username"];
    $password = $_POST["password"];

    if (empty($username)) {
        echo json_encode(["status" => 401, "message" => "Kérem, adja meg a felhasználónevét!"]);
        exit;
    }

    if (empty($password)) {
        echo json_encode(["status" => 401, "message" => "Kérem, adja meg a jelszavát!"]);
        exit;
    }


    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            //?  Mivel alapértelmezetten nincs "beépített jwt", gondoltam ez is egy biztonságos megoldás rá
            $rawToken = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $rawToken);

            $stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE user_id = ?");
            $stmt_check->bind_param("s", $user["user_id"]);
            $stmt_check->execute();
            $count = $stmt_check->get_result()->fetch_assoc()["count"];
            $stmt_token = $conn->prepare("SELECT token FROM users_tokens WHERE user_id = ?");
            $stmt_token->bind_param("s", $user["user_id"]);
            $stmt_token->execute();
            $token_result = $stmt_token->get_result();

            if ($token_result->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE users_tokens SET token = ?, expires = DATE_ADD(NOW(), INTERVAL 4 DAY) WHERE user_id = ?");
                $stmt->bind_param("ss", $hashedToken, $user["user_id"]);
            } else {
                $stmt = $conn->prepare("INSERT INTO users_tokens (user_id, token, expires) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 4 DAY))");
                $stmt->bind_param("ss", $user["user_id"], $hashedToken);
            }
            $stmt->execute();

            setcookie("token", $rawToken, time() + (4 * 24 * 60 * 60), "/", "", true, true);
            echo json_encode(["status" => 200, "message" => "Sikeres bejelentkezés"]);
            exit;
        } else {
            echo json_encode(["status" => 401, "message" => "Hibás bejelentkezési adatokat adott meg!"]);
            exit;
        }
    } else {
        echo json_encode(["status" => 401, "message" => "Hibás bejelentkezési adatokat adott meg!"]);
        exit;
    }
}
