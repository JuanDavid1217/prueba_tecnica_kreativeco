<?php
    namespace App\Utils;

    class HttpException extends \Exception {
        private int $status_code;

        public function __construct(int $status_code, string $message){
            parent::__construct($message??'');
            $this->status_code = $status_code;
        }

        public function getStatusCode():int {
            return $this->status_code;
        }
    }
?>