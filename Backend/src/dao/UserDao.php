<?php
    namespace App\Dao;

    use App\Utils\HttpException;
    use App\Database;

    class UserDao {
        private Database $database;

        public function __construct(Database $database){
            $this->database=$database;
        }

        public function createUser(string $name, string $last_name, string $email, string $password, string $role){
            try{
                $conn = $this->database->getConn();
                $stmt = $conn->prepare(
                    "INSERT INTO Users(name, last_name, email, password, role)VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->bind_param('sssss', $name, $last_name, $email, $password, $role);
                $stmt->execute();
                $id = $conn->insert_id;
                return [
                    'id'=>$id,
                    'name'=>$name,
                    'last_name'=>$last_name,
                    'email'=>$email,
                    'role'=>$role
                ];
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }    
        }

        public function findAll() {
            try{
                $conn = $this->database->getConn();
                $result = $conn->query(
                    "SELECT id, name, last_name, email, role FROM Users WHERE active=1"
                );
                $users = [];
                while ($row = $result->fetch_assoc()){
                    $users[] = $row;
                }
                return $users;
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }
        }

        public function findByEmail(string $email){
            try{
                $conn = $this->database->getConn();
                $stmt = $conn->prepare(
                    "SELECT * FROM Users WHERE email=? and active=1"
                );
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                return $row;
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }
        }

        public function findById(int $id){
            try{
                $conn = $this->database->getConn();
                $stmt = $conn->prepare(
                    "SELECT name, last_name, email, role FROM Users WHERE id=? and active=1"
                );
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                return $row;
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }
        }

        public function delete(int $id){
            try{
                $conn = $this->database->getConn();
                $stmt = $conn->prepare(
                    "UPDATE Users SET active=0 WHERE id=? and active=1"
                );
                $stmt->bind_param('i', $id);
                $stmt->execute();
                return $conn->affected_rows;
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }
        }

        public function update(int $id, string $name, string $last_name, string $role){
            try{
                $conn = $this->database->getConn();
                $stmt = $conn->prepare(
                    "UPDATE Users SET name=?, last_name=?, role=? WHERE id=? and active=1"
                );
                $stmt->bind_param('sssi', $name, $last_name, $role, $id);
                $stmt->execute();
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }
        }
    }
?>