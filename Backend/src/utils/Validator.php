<?php
    namespace App\Utils;

    class Validator {
        public static function checkPassword(string $password): array {
            $len = mb_strlen($password)>=8 && mb_strlen($password)<=16;
            $spaces = strpos($password, ' ') == false;
            $uppercase = preg_match('/\p{Lu}/u', $password); 
            $lowercase = preg_match('/\p{Ll}/u', $password); 
            $number = preg_match('/\d/', $password); 
            $specialChars = preg_match('/[^\p{L}\d]/', $password);
            
            if ($len && $spaces && $uppercase && $lowercase && $number && $specialChars){
                return ['isValid'=>TRUE];
            }
            return [
                'isValid'=>FALSE,
                'message'=>'La contraeña debe tener entre 8 y 16 caracteres, al menos una letra minúscula, al menos una letra mayúscula, al menos un caracter especial, sin espacios en blanco.'
            ];
        }

        public static function checkEmail(string $email): array {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                return ['isValid'=>TRUE];
            }
            return ['isValid'=>FALSE, 'message'=>'Dirección email inválida'];
        }

        public static function encrypt_password(string $password): string {
            return password_hash($password, PASSWORD_ARGON2I);
        }

        public static function compare_passwords(string $password, string $encryptedPassword): bool {
            return password_verify($password, $encryptedPassword);
        }

        public static function validateStr($value){
            if (is_null($value) || trim($value)==''){
                return null;
            }
            return trim($value);
        }

        public static function validateInt($value){
            if (is_null($value) || filter_var($value, FILTER_VALIDATE_INT) == false) {
                return null;
            }
            return (int)$value;
        }
    }
?>