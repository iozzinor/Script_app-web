<?php
    require_once 'database_generator.php';

    class DatabaseGeneratorMysql extends DatabaseGenerator
    {
        public function generate_database()
        {
            $pdo_path = 'mysql:host=' .
                Configuration::get('database_host') .
                ';dbname=' . Configuration::get('database_name') .
                ';charset=utf8;';
            return new PDO($pdo_path, Configuration::get('database_user'), Configuration::get('database_password'));
        }
    }
?>