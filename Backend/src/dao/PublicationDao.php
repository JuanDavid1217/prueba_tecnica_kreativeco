<?php
    namespace App\Dao;

    use App\Utils\HttpException;;
    use App\Database;

    class PublicationDao {
        private Database $database;

        public function __construct(Database $database){
            $this->database=$database;
        }

        public function createPublication(int $id_author, string $title, string $description){
            try{
                $conn = $this->database->getConn();
                $stmt = $conn->prepare(
                    "INSERT INTO Publications(id_author, title, description) VALUES (?, ?, ?)"
                );
                $stmt->bind_param('iss', $id_author, $title, $description);
                $stmt->execute();
                $id = $conn->insert_id;
                return [
                    'id'=>$id,
                    'title'=>$title,
                    'description'=>$description
                ];
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }    
        }

        public function findAll() {
            try{
                $conn = $this->database->getConn();
                $result = $conn->query(
                    "SELECT Publications.id, title, description, creation_date, CONCAT(name, ' ', last_name) as full_name, role FROM Publications INNER JOIN Users ON Publications.id_author=Users.id WHERE Publications.active=1"
                );
                $publications = [];
                while ($row = $result->fetch_assoc()){
                    $publications[] = $row;
                }
                return $publications;
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }
        }

        public function findById(int $id){
            try{
                $conn = $this->database->getConn();
                $stmt = $conn->prepare(
                    "SELECT title, description, creation_date, CONCAT(name, ' ', last_name) as full_name, role FROM Publications INNER JOIN Users ON Publications.id_author=Users.id WHERE Publications.id=? AND Publications.active=1"
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
                    "UPDATE Publications SET active=0 WHERE id=? and active=1"
                );
                $stmt->bind_param('i', $id);
                $stmt->execute();
                return $conn->affected_rows;
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }
        }

        public function update(int $id, string $title, string $description){
            try{
                $conn = $this->database->getConn();
                $stmt = $conn->prepare(
                    "UPDATE Publications SET title=?, description=? WHERE id=? and active=1"
                );
                $stmt->bind_param('ssi', $title, $description, $id);
                $stmt->execute();
            }catch(\mysqli_sql_exception $e){
                throw new HttpException(409, $e->getMessage());
            }
        }
    }
?>