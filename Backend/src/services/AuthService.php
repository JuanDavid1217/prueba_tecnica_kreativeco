<?php
    namespace App\Services;

    use App\Dao\UserDao;
    use App\Utils\HttpException;
    use App\Utils\Token;

    class AuthService {
        private UserDao $dao;

        public function __construct(UserDao $dao){
            $this->dao = $dao;
        }

        public function isAuthorized(){
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization']??null;
            if (!is_null($authHeader)){
                $token = substr($authHeader, 7);
                $data = Token::verifyToken($token);
                if (!is_null($data)){
                    $exists = $this->dao->findById($data['id']??0);
                    if (!is_null($exists)){
                        return $data;
                    }
                }
            }
            throw new HttpException(401, 'Token inválido.');
        }
    }
?>