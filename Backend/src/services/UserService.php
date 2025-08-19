<?php
    namespace App\Services;
    
    use App\Dao\UserDao;
    use App\Utils\Validator;
    use App\Utils\HttpException;
    use App\Utils\AccessLevel;

    class UserService {
        private UserDao $dao;

        public function __construct(UserDao $dao){
            $this->dao=$dao;
        }

        public function createUser(array $data) {
            $name = Validator::validateStr($data['name']??null);
            $last_name = Validator::validateStr($data['last_name']??null);
            $email = Validator::validateStr($data['email']??null);
            $password = Validator::validateStr($data['password'??null]);
            $role = Validator::validateStr($data['role']??null);

            if (is_null($name) || is_null($last_name) || is_null($email) || is_null($password) || is_null($role)){
                throw new HttpException(422, 'Los siguientes campos son requeridos: name, last_name, email, password y role.');    
            }

            $flag = Validator::checkPassword($data['password']);
            if (!$flag['isValid']) {
                throw new HttpException(409, $flag['message']);
            }

            $flag = Validator::checkEmail($data['email']);
            if (!$flag['isValid']){
                throw new HttpException(409, $flag['message']);
            }

            $role = AccessLevel::existsRole($role);
            if (is_null($role)){
                throw new HttpException(409, 'Rol invalido.');
            }
            
            return $this->dao->createUser($name, $last_name, $email, Validator::encrypt_password($password), $role);  
        }

        public function findAll(){
            return $this->dao->findAll();
        }

        public function findById(int $id){
            return $this->dao->findById($id);
        }

        public function delete(int $id){
            return $this->dao->delete($id);
        }

        public function update(int $id, array $data):bool {
            $exists = $this->findById($id);
            if (is_null($exists)){
                return FALSE;
            }

            $name = Validator::validateStr($data['name']??null);
            $last_name = Validator::validateStr($data['last_name']??null);
            $role = Validator::validateStr($data['role']??null);

            if (is_null($name) || is_null($last_name) || is_null($role)){
                throw new HttpException(422, 'Los siguientes campos son requeridos: name, last_name y role.');
            }

            $role = AccessLevel::existsRole($role);
            if (is_null($role)){
                throw new HttpException(409, 'Rol invalido.');
            }

            $this->dao->update($id, $name, $last_name, $role);
            return TRUE;
        }
    }
?>