<?php

class JWT
{
    private static function b64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64url_decode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Sign a JWT token
     * @param array $payload The ASOC array forming a JSON structure
     * @param mixed $key The key to use
     * @param mixed $alg Support none, HS256, RS256
     * @throws Exception
     * @return string The JWT token
     */
    public static function sign(array $payload, $key = null, $alg = 'HS256')
    {
        $header = [
            'typ' => 'JWT',
            'alg' => $alg
        ];

        $header_b64 = self::b64url_encode(json_encode($header));
        $payload_b64 = self::b64url_encode(json_encode($payload));

        $data = "$header_b64.$payload_b64";

        switch ($alg) {
            case 'none':
                $signature = '';

                break;

            case 'HS256':
                $signature = hash_hmac('sha256', $data, $key, true);
                break;

            case 'RS256':
                openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256);
                break;

            default:
                throw new Exception("Unsupported algorithm");
        }

        $signature_b64 = self::b64url_encode($signature);

        return "$data.$signature_b64";
    }

    public static function verify($jwt, $key = null)
    {
        [$header_b64, $payload_b64, $signature_b64] = explode('.', $jwt);

        $header = json_decode(self::b64url_decode($header_b64), true);

        $alg = $header['alg'] ?? null;

        $data = $header_b64 . '.' . $payload_b64;
        $signature = self::b64url_decode($signature_b64);

        switch ($alg) {

            case 'none':
                return true;

            case 'HS256':
                $expected = hash_hmac('sha256', $data, $key, true);
                return hash_equals($expected, $signature);

            case 'RS256':
                return openssl_verify($data, $signature, $key, OPENSSL_ALGO_SHA256) === 1;

            default:
                return false;
        }
    }

    public static function decode($jwt)
    {
        [$header_b64, $payload_b64, $signature_b64] = explode('.', $jwt);

        return [
            'header' => json_decode(self::b64url_decode($header_b64), true),
            'payload' => json_decode(self::b64url_decode($payload_b64), true),
            'signature' => $signature_b64
        ];
    }
}