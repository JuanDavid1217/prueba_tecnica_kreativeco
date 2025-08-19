<?php
    namespace App\Utils;
    
    use App\Utils\HttpException; 

    class AccessLevel {
        private static array $levels = [
            'BASICO'=>1,
            'MEDIO'=>2,
            'MEDIO ALTO'=>3,
            'ALTO MEDIO'=>4,
            'ALTO'=>5
        ];

        public static function validateRole(int $required_level, array $data){
            $current_level=$data['role']??'';
            $value = self::$levels[mb_strtoupper($current_level, 'UTF-8')]??0;
            if ($value < $required_level){
                throw new HttpException(403, 'Permisos insuficientes.');
            }
        }

        public static function existsRole(string $role){
            $role = mb_strtoupper($role, 'UTF-8');
            $level = self::$levels[$role]??0;
            if ($level>0){
                return $role;
            }
            return null;
        }
    }
?>