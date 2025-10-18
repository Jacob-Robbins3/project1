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
if (!$payload || !($payload['is_admin'] ?? false)) {
    http_response_code(403);
    echo json_encode(["error"=>"Admin privileges required"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$genre = trim($data['genre'] ?? '');

if (!$title || !$genre) {
    http_response_code(400);
    echo json_encode(["error"=>"All fields required"]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO games (title, genre) VALUES (?, ?)");
$stmt->execute([$title, $genre]);

echo json_encode([
    "message"=>"Game added successfully",
    "game_id"=>$pdo->lastInsertId()
]);
?>
