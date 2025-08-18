<?php
    namespace App;

    class Database {
        private static ?Database $instance = null;
        private \mysqli $conn;

        private function __construct() {

            $this->conn = new \mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);

            if ($this->conn->connect_error) {
                die("Connection error: " . $this->conn->connect_error);
            }
        }

        public static function getInstance(): ?Database {
            if (self::$instance === null) {
                self::$instance = new Database();
            }

            return self::$instance;
        }

        public function getConn(): \mysqli {
            return $this->conn;
        }
    }
?>