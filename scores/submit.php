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

$user_id = $payload['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$game_id = $data['game_id'] ?? null;
$score = $data['score'] ?? null;

if (!$game_id || !$score) {
    http_response_code(400);
    echo json_encode(["error"=>"Game ID and score required"]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO scores (player_id, game_id, score) VALUES (?, ?, ?)");
$stmt->execute([$user_id, $game_id, $score]);

echo json_encode([
    "message"=>"Score submitted successfully",
    "score_id"=>$pdo->lastInsertId()
]);
?>
