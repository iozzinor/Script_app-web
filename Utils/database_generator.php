<?php
    abstract class DatabaseGenerator
    {
        /**
         * @return PDO The database.
         */
        public abstract function generate_database();
    }
?>