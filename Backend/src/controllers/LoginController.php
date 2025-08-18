<?php
    namespace App\Controllers;

    use App\Services\LoginService;

    class LoginController {
        private LoginService $service;
        
        public function __construct(LoginService $service){
            $this->service = $service;
        }

        public function login(){
            $data = json_decode(file_get_contents('php://input'), true);
            if (is_null($data)) {
                http_response_code(400);
            }else{
                $response = $this->service->login($data);
                if (is_null($response)){
                    http_response_code(401);
                    echo json_encode(['detail'=>'Usuario y/o contraseña incorrectos.']);
                }else{
                    http_response_code(200);
                    echo json_encode($response);
                }
            }
        }
    }
?>