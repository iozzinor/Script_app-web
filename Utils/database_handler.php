<?php
    require_once 'database_generator_mysql.php';

    class DatabaseHandler
    {
        /**
         * The singleton handler.
         */
        protected static $handler_;

        /**
         * The database connection.
         */
        protected $database_;

        /**
         * Private constructor of the database handler, implementing the singleton pattern.
         */
        protected function __construct()
        {
            $generator = new DatabaseGeneratorMysql();

            $this->database_ = $generator->generate_database();
        }

        public static function &get_handler()
        {
            if (self::$handler_ == null)
            {
                self::$handler_ = new DatabaseHandler();
            }
            return self::$handler_;
        }

        public static function &database()
        {
            return self::get_handler()->database_;
        }
    }
?>