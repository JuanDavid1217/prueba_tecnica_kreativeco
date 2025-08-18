<?php
    namespace App\Services;
    
    use App\Dao\UserDao;
    use App\Utils\Validator;
    use App\Utils\HttpException;
    use App\Utils\Token;

    class LoginService {
        private UserDao $dao;

        public function __construct(UserDao $dao){
            $this->dao=$dao;
        }

        public function login(array $data) {
            $email = Validator::validateStr($data['email']??null);
            $password = Validator::validateStr($data['password']??null);

            if (is_null($email) || is_null($password)){
                throw new HttpException(422, 'Los siguientes campos son requeridos: email y password.');
            }

            $exists = $this->dao->findByEmail($email);
            
            if(!is_null($exists)){
                if (Validator::compare_passwords($password, $exists['password'])){
                    $data = [
                        'id'=>$exists['id'],
                        'name'=>$exists['name'],
                        'last_name'=>$exists['last_name'],
                        'role'=>$exists['role']
                    ];
                    $token = Token::generateToken($data);
                    return ['token'=>$token, 'data'=>$data];
                }
            }
            return null;
        }
        
    }
?>