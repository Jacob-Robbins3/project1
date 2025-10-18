<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Username and password required"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(["error" => "Username already exists"]);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
if ($stmt->execute([$username, $hash])) {
    $user_id = $pdo->lastInsertId();
    $token = create_jwt(['user_id' => $user_id], $jwt_secret, $jwt_expiration_time);
    echo json_encode([
        "message" => "User registered successfully",
        "token" => $token
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to register user"]);
}
?>
