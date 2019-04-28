<?php

	class DatabaseHandler extends SQLite3
	{
		/**
		 * The database path.
		 */
		private $database_path_;

		/**
		 * Create a new database handler.
		 * 
		 * @param database_path The database path.
		 */
		public function __construct(string $database_path)
		{
			$this->database_path_ = $database_path;
			parent::__construct($database_path, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
		}

		/**
		 * @return string The database path.
		 */
		public function get_database_path()
		{
			return $this->database_path_;
		}
	}

?>
