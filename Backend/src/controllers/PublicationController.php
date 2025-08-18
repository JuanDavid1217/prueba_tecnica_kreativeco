<?php
    namespace App\Controllers;

    use App\Services\PublicationService;

    class PublicationController {
        private PublicationService $service;
        
        public function __construct(PublicationService $service){
            $this->service = $service;
        }

        public function createPublication(int $id_author){
            $data = json_decode(file_get_contents('php://input'), true);
            if (is_null($data)) {
                http_response_code(400);
            }else{
                $response = $this->service->createPublication($id_author, $data);
                if (is_null($response)){
                    http_response_code(404);
                    echo json_encode(['detail'=>'Usuario no encontrado.']);    
                }else{
                    http_response_code(201);
                    echo json_encode($response);
                }
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
                echo json_encode(['detail'=>'Publicación no encontrada.']);    
            }else{
                http_response_code(200);
                echo json_encode($response);
            }
        }

        public function delete(int $id){
            $affected_rows = $this->service->delete($id);
            if($affected_rows==0){
                http_response_code(404);
                echo json_encode(['detail'=>'Publicación no encontrada.']);
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
                    echo json_encode(['detail'=>'Publicacion no encontrada.']);
                }else{
                    http_response_code(204);
                }
            }
        }
    }
?>