<?php
    class PdoSingleton {
        private static PDO | NULL $pdo = null;

        private function __construct() {}

        public static function getInstance() {
            if(self::$pdo) {
                return self::$pdo;
            }

            self::$pdo = new PDO(
                'mysql:host=localhost:3306;dbname=acme', 'root', 'root', 
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            return self::$pdo;
        }
    }
