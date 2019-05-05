<?php
    require_once 'database_generator.php';

    class DatabaseGeneratorMysql extends DatabaseGenerator
    {
        public function generate_database()
        {
            define(PARAM_host_name, '172.21.0.3');
            define(PARAM_db_name, 'scriptapp_db');
            return new PDO('mysql:host=' . PARAM_host_name . ';dbname=' . PARAM_db_name . ';charset=utf8', 'root', 'example');
        }
    }
?>