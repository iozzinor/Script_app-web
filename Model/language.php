<?php
    class Language
    {
        // ---------------------------------------------------------------------
        // LIST
        // ---------------------------------------------------------------------
        public function get_all_languages()
        {
            $statement = DatabaseHandler::database()->query('SELECT id, name, short_name FROM language');
            $result = $statement->fetchAll(PDO::FETCH_CLASS);
            $statement->closeCursor();
            return $result;
        }

        // ---------------------------------------------------------------------
        // ADD
        // ---------------------------------------------------------------------
        /**
         * Attempt to add a new language.
         * 
         * Might throw an exception :
         * - if a language with the same name already exists
         * 
         * @return int The id of the new language.
         */
        public function add_language($name, $short_name)
        {
            // check if the database contains language with such a name
            $already_exists = DatabaseHandler::database()->prepare('SELECT COUNT(*) AS languages_count FROM language WHERE name LIKE :name OR short_name LIKE :short_name;');
            $already_exists->execute(array(':name' => $name, ':short_name' => $short_name));
            if (!($result = $already_exists->fetch(PDO::FETCH_ASSOC)))
            {
                throw new Exception("Can not get the number of languages.");
            }
            
            if ($result['languages_count'] > 0)
            {
                throw new Exception("Can not add the language with name " . $name . ": another one with the same name already exists.");
            }
            $already_exists->closeCursor();

            // perform the insertion
            $statement = DatabaseHandler::database()->prepare('INSERT INTO language (name, short_name) VALUES(:name, :short_name);');
            $statement->execute(array(
                'name'          => $name,
                'short_name'    => $short_name
            ));

            return DatabaseHandler::database()->lastInsertId();
        }

        // ---------------------------------------------------------------------
        // DELETE
        // ---------------------------------------------------------------------
        public function delete($language_id)
        {
            $statement = DatabaseHandler::database()->prepare('DELETE FROM language WHERE id=:id;');
            $statement->execute(array(':id' => $language_id));
        }
    }
?>