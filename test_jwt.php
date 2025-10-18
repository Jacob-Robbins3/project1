<?php
require_once __DIR__ . '/config/jwt.php';

$token = create_jwt(['user_id' => 1], $jwt_secret, $jwt_expiration_time);
echo "Generated Token: $token\n";

$payload = verify_jwt($token, $jwt_secret);
if ($payload) {
    echo "\n✅ Token is valid! Payload:\n";
    print_r($payload);
} else {
    echo "\n❌ Token verification failed!";
}
?>
