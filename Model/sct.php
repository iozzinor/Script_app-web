<?php
    require_once 'sct_subject.php';
    require_once 'sfm_subject.php';
    require_once 'sfm_language.php';

    class Sct
    {
        // ---------------------------------------------------------------------
        // SAVE SCT
        // ---------------------------------------------------------------------
        public function save_sct_subject($sct_subject, $is_new = false)
        {
            if ($is_new)
            {
                $sct_subject->set_id($this->get_new_sct_id());
            }

            $sfm_subject = new SfmSubject($sct_subject);
            $sfm_subject->write_file();

            $language_names = $sct_subject->get_language_names();
            $i = 0;
            $language_names_count = count($language_names);
            for ($i = 0; $i < $language_names_count; ++$i)
            {
                $language_name = $language_names[$i];
                $questions = $sct_subject->get_questions($language_name);

                $sfm_language = new SfmLanguage($sct_subject->get_id(), $language_name, $questions);
                $sfm_language->write_file();
            }
        }

        // ---------------------------------------------------------------------
        // GET SCT
        // ---------------------------------------------------------------------
        /**
         * @return int The total number of SCTs.
         */
        public function get_scts_count()
        {
            $folder_path = SctFileManager::get_scts_folder_path();

            $result = 0;
            $content = scandir($folder_path);
            foreach ($content as $current_file)
            {
                if (strncmp($current_file, 'Sct_', 4) == 0)
                {
                    ++$result;
                }
            }

            return $result;
        }

        /**
         * @return int The id of a new SCT.
         */
        public function get_new_sct_id()
        {
            return $this->get_scts_count() + 1;
        }

        /**
         * @param id The SCT id which should be greater or equal to 1.
         * 
         * @return array SCT information for the given id or null.
         */
        public function get_sct_information($sct_id)
        {
            // invalid sct id
            if ($sct_id < 1)
            {
                return null;
            }

            $sql_request = $this->get_sct_information_request_()
                . ' AND sct.id=:sct_id ';

            $sql_result = $this->execute_request($sql_request, array(
                ':sct_id' => $sct_id,
                ':published_state_id' => $this->published_state_id_));
            if ($result = $sql_result->fetchArray(SQLITE3_ASSOC))
            {
                $result['id'] = $sct_id;
                $result['author_first_name'] = '';

                $result['estimated_duration'] = $this->estimate_sct_duration_($result['items_count']);

                return $result;
            }

            return null;
        }

        /**
         * @return array The list of SCTs information for page_index (sorted by release date).
         */
        public function get_scts_information(int $page_index, int $scts_per_page)
        {
            $request = $this->get_sct_information_request_()
                . ' ORDER BY sct.release_date'
                . ' LIMIT :limit'
                . ' OFFSET :offset';
            $sql_request = $this->execute_request($request, array(
                ':published_state_id' => $this->published_state_id_,
                ':limit' => $scts_per_page,
                ':offset' => ($page_index - 1) * $scts_per_page
            ));

            $result = array();
            while ($sql_result = $sql_request->fetchArray(SQLITE3_ASSOC))
            {
                array_push($result, $sql_result);
            }
            return $result;
        }

        // ---------------------------------------------------------------------
        // SCT TYPE
        // ---------------------------------------------------------------------
        public function get_sct_types()
        {
            $result = array();
            $query_result = DatabaseHandler::database()->query('SELECT id, name FROM sct_type;');
            while ($current_row = $query_result->fetch(PDO::FETCH_ASSOC))
            {
                array_push($result, $current_row);
            }

            $query_result->closeCursor();
            
            return $result;
        }

        // ---------------------------------------------------------------------
        // SCT TOPIC
        // ---------------------------------------------------------------------
        public function get_sct_topics()
        {
            $result = array();
            $query_result = DatabaseHandler::database()->query('SELECT id, name FROM sct_topic;');
            while ($current_row = $query_result->fetch(PDO::FETCH_ASSOC))
            {
                array_push($result, $current_row);
            }

            $query_result->closeCursor();
            
            return $result;
        }
        
        // ---------------------------------------------------------------------
        // UTILS
        // ---------------------------------------------------------------------
        private function estimate_sct_duration_($items_count)
        {
            return $items_count * 30.0;
        }
    }
?>