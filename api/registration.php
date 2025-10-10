<?php
header('Content-Type: application/json');
switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        post_registration();
        break;
}

function post_registration()
{
    include "../db.php";

    $username = $_POST["username"];
    $password = $_POST["password"];
    $repassword = $_POST["repassword"];

    if (empty($username)) {
        echo json_encode(["status" => 401, "message" => "Kérem, adja meg a felhasználónevét!"]);
        exit;
    }

    if (empty($password)) {
        echo json_encode(["status" => 401, "message" => "Kérem, adja meg a jelszavát!"]);
        exit;
    }

    if (empty($repassword)) {
        echo json_encode(["status" => 401, "message" => "Kérem, adja meg újra a jelszavát!"]);
        exit;
    }


    if ($password !== $repassword) {
        echo json_encode(["status" => 401, "message" => "A megadott jelszavak nem egyeznek!"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc()["count"];

    if ($result === 0) {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?,?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("ss", $username, $hashedPassword);
        $stmt->execute();
        $stmt->close();


        $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $user_id = $stmt_check->get_result()->fetch_assoc()["user_id"];
        $stmt_check->close();

        $rawToken = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $rawToken);
        $stmt_token = $conn->prepare("INSERT INTO users_tokens (user_id, token) VALUES (?, ?)");
        $stmt_token->bind_param("ss", $user_id, $hashedToken);
        $stmt_token->execute();
        $stmt_token->close();

        setcookie("token", $rawToken, time() + (4 * 24 * 60 * 60), "/", "", true, true);
        echo json_encode(["status" => 200, "message" => "Sikeres regisztráció"]);
        exit;
    } else {
        echo json_encode(["status" => 401, "message" => "Már van ilyen felhasználó regisztrálva!"]);
        exit;
    }
}
