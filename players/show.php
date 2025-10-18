<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/jwt.php';

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$token = $matches[1];
$payload = verify_jwt($token, $jwt_secret);
if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or expired token"]);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "Player ID required"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, level, joined_at FROM players WHERE id = ?");
$stmt->execute([$id]);
$player = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$player) {
    http_response_code(404);
    echo json_encode(["error" => "Player not found"]);
    exit;
}

echo json_encode($player);
?>
