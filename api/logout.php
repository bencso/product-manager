<?php
header('Content-Type: application/json');
switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        post_logout();
        break;
}

function post_logout()
{
    setcookie("token", "", time() - 3600, "/", "", true, true);
}
