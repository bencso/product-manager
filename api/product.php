<?php
header('Content-Type: application/json');
switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        get_product();
        break;
}

function get_product()
{
    include "../db.php";
    try {
        $sort = "";
        $search = "";

        if (isset($_GET["cikkszam"])) $sort .= ($sort ? '|' : '') . "cikkszam " . $_GET["cikkszam"];
        if (isset($_GET["cikk_megnevezes"])) $sort .= ($sort ? '|' : '') . "cikk_megnevezes " . $_GET["cikk_megnevezes"];
        if (isset($_GET["nettoar"])) $sort .= ($sort ? '|' : '') . "nettoar " . $_GET["nettoar"];
        if (isset($_GET["afa"])) $sort .= ($sort ? '|' : '') . "afa " . $_GET["afa"];

        $search = (isset($_GET['search'])) ? trim($_GET['search']) : "";
        $sorts = array_filter(explode('|', $sort));
        $stmt;

        $sqlselect = "SELECT cikkszam, cikk_megnevezes, nettoAr, afa FROM products";
        $sqlorder = count($sorts) ? ' ORDER BY ' . implode(', ', $sorts) : '';
        if ($search !== "") {
            $sql = $sqlselect . " WHERE cikkszam LIKE ? 
            OR cikk_megnevezes LIKE ? 
            OR nettoAr LIKE ? 
            OR afa LIKE ?" . $sqlorder;
            $stmt = $conn->prepare($sql);
            $like = '%' . $search . '%';
            $stmt->bind_param('ssss', $like, $like, $like, $like);
        } else {
            $sql = $sqlselect . $sqlorder;
            $stmt = $conn->prepare($sql);
        }
        $stmt->execute();
        $results = $stmt->get_result();
        $rows = $results ? $results->fetch_all(MYSQLI_ASSOC) : [];

        echo json_encode(['status' => 200, 'data' => $rows]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 500, 'error' => $e->getMessage()]);
    }
}
