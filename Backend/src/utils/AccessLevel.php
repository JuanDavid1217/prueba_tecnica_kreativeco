<?php
    namespace App\Utils;
    
    use App\Utils\HttpException; 

    class AccessLevel {
        private static array $levels = [
            'básico'=>1,
            'medio'=>2,
            'medio alto'=>3,
            'alto medio'=>4,
            'alto'=>5
        ];

        public static function validateRole(int $required_level, array $data){
            $current_level=$data['role']??'';
            $value = self::$levels[mb_strtolower($current_level, 'UTF-8')]??0;
            if ($value < $required_level){
                throw new HttpException(403, 'Permisos insuficientes.');
            }
        }
    }
?>