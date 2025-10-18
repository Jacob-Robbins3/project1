<?php

$jwt_secret = "your_super_secret_key_here";

$jwt_expiration_time = 3600;

function create_jwt($payload, $secret, $expiry_seconds) {
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $payload['exp'] = time() + $expiry_seconds;

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

    $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
}

function verify_jwt($jwt, $secret) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;

    [$header, $payload, $signature] = $parts;

    $checkSig = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(
        hash_hmac('sha256', "$header.$payload", $secret, true)
    ));

    if (!hash_equals($checkSig, $signature)) return false;

    $payloadData = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    return ($payloadData['exp'] ?? 0) >= time() ? $payloadData : false;
}
?>
