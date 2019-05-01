<?php
    //require_once 'database_handler.php';

    abstract class Model
    {
        /**
         * The table name.
         */
        protected $table_name_;

        function __construct(string $table_name)
        {
            $this->table_name_ = $table_name;
        }

        /**
         * @return string The table name.
         */
        public function get_table_name()
        {
            return $this->$table_name_;
        }

        /**
         * @return bool Whether the table exists in the database.
         */
        public function check_if_table_exists(string $table_name)
		{
            return false;
		}

        public function execute_request(string $sql_request, $parameters = null)
        {
            return null;
        }
    }
?>