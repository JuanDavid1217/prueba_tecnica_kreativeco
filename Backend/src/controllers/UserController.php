<?php
    namespace App\Controllers;

    use App\Services\UserService;

    class UserController {
        private UserService $service;
        
        public function __construct(UserService $service){
            $this->service = $service;
        }

        public function createUser(){
            $data = json_decode(file_get_contents('php://input'), true);
            if (is_null($data)) {
                http_response_code(400);
            }else{
                $response = $this->service->createUser($data);
                http_response_code(201);
                echo json_encode($response);
            }
        }

        public function findAll(){
            $response = $this->service->findAll();
            http_response_code(200);
            echo json_encode($response);
        }

        public function findById(int $id){
            $response = $this->service->findById($id);
            if(is_null($response)){
                http_response_code(404);
                echo json_encode(['detail'=>'Usurio no encontrado.']);    
            }else{
                http_response_code(200);
                echo json_encode($response);
            }
        }

        public function delete(int $id){
            $affected_rows = $this->service->delete($id);
            if($affected_rows==0){
                http_response_code(404);
                echo json_encode(['detail'=>'Usurio no encontrado.']);
            }else{
                http_response_code(204);
            }
        }

        public function update(int $id){
            $data = json_decode(file_get_contents('php://input'), true);
            if (is_null($data)) {
                http_response_code(400);
            }else{
                $response = $this->service->update($id, $data);
                if(!$response){
                    http_response_code(404);
                    echo json_encode(['detail'=>'Usurio no encontrado.']);
                }else{
                    http_response_code(204);
                }
            }
        }
    }
?>