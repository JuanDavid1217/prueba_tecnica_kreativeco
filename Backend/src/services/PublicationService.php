<?php
    namespace App\Services;
    
    use App\Dao\PublicationDao;
    use App\Dao\UserDao;
    use App\Utils\Validator;
    use App\Utils\HttpException;

    class PublicationService {
        private PublicationDao $publicationDao;
        private UserDao $userDao;

        public function __construct(PublicationDao $publicationDao, UserDao $userDao){
            $this->publicationDao = $publicationDao;
            $this->userDao = $userDao;
        }

        public function createPublication(int $id_author, array $data) {
            $title = Validator::validateStr($data['title']??null);
            $description = Validator::validateStr($data['description']??null);
            
            if (is_null($title) || is_null($description)) {
                throw new HttpException(422, 'Los siguientes campos son requeridos: title y description.');
            }

            $exists = $this->userDao->findById($id_author);
            if(is_null($exists)){
                return null;
            }
            return $this->publicationDao->createPublication($id_author, $title, $description);  
        }

        public function findAll(){
            return $this->publicationDao->findAll();
        }

        public function findById(int $id){
            return $this->publicationDao->findById($id);
        }

        public function delete(int $id){
            return $this->publicationDao->delete($id);
        }

        public function update(int $id, array $data):bool {
            $exists = $this->findById($id);
            if (!$exists){
                return FALSE;
            }
            
            $title = Validator::validateStr($data['title']??null);
            $description = Validator::validateStr($data['description']??null);
            
            if (is_null($title) || is_null($description)) {
                throw new HttpException(422, 'Los siguientes campos son requeridos: title y description.');
            }
            
            $this->publicationDao->update($id, $title, $description);
            return TRUE;
        }
    }
?>