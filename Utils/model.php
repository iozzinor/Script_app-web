<?php
    require_once 'database_handler.php';

    abstract class Model
    {
        /**
         * The main database handler.
         */
        protected static $database_handler_;

        /**
         * Singleton getter.
         * 
         * @return DatabaseHandler The main database handler.
         */
        protected static function get_database_handler_()
        {
            if (!isset(self::$database_handler_))
            {
                $main_database_path = Configuration::get('database_path');
                self::$database_handler_ = new DatabaseHandler($main_database_path);
            }
            return self::$database_handler_;
        }

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
			$result = $this->query("SELECT * FROM sqlite_master WHERE type='table' AND name='$table_name'");
			
			if ($result->fetchArray() == false)
			{
				return false;
			}
			return true;
		}

        public function execute_request(string $sql_request, $parameters = null)
        {
            if ($parameters == null)
            {
                $result = $this->get_database_handler_()->query($sql_request);
            }
            else
            {
                $statement = $this->get_database_handler_()->prepare($sql_request);

                foreach ($parameters as $parameter => $value)
                {
                   $statement->bindValue($parameter, $value);
                }
                $result = $statement->execute();

            }
            return $result;
        }
    }
?>