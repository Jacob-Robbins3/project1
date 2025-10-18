<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/jwt.php';

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["error"=>"Unauthorized"]);
    exit;
}

$token = $matches[1];
$payload = verify_jwt($token, $jwt_secret);
if (!$payload) {
    http_response_code(401);
    echo json_encode(["error"=>"Invalid or expired token"]);
    exit;
}

$stmt = $pdo->query("SELECT id, title, genre FROM games");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($games);
?>
