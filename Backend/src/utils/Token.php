<?php
    namespace App\Utils;

    use Firebase\JWT\JWT;
    use Firebase\JWT\KEY;

    class Token {

        public static function generateToken(array $payload){
            $token = [
                "exp"=> time() + $_ENV['TOKEN_EXP_TIME']??3600,
                "data"=>$payload
            ];
            $jwt = JWT::encode($token, $_ENV['TOKEN_KEY'], $_ENV['TOKEN_ALGORITHM']);
            return $jwt;
        } 

        public static function verifyToken(string $jwt){
            try {
                $decoded = JWT::decode($jwt, new Key($_ENV['TOKEN_KEY'], $_ENV['TOKEN_ALGORITHM']));
                $data = json_decode(json_encode($decoded), true);
                return $data['data'];
            }catch(\Throwable $e){
                return null;
            }
        }
    }
?>