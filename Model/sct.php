<?php
    require_once 'sct_subject.php';
    require_once 'sfm_subject.php';

    require_once(Configuration::get('root_path') . 'Utils/model.php');
    
    class Sct extends Model
    {
        /**
         * The user id that should be specified to retrieve SCT information.
         */
        private $user_id_ = null;

        /**
         * The identifier of the published SCT state.
         */
        private $published_state_id_ = null;

        public function __construct()
        {
            parent::__construct('sct');

            $this->retrieve_published_state_id_();
        }

        private function retrieve_published_state_id_()
        {
            $sql_result = $this->execute_request('SELECT id from sct_state WHERE name=:name', array(':name' => 'PUBLISHED'));
            if ($result = $sql_result->fetchArray(SQLITE3_ASSOC))
            {
                $this->published_state_id_ = $result['id'];
            }
        }

        /**
         * This function must be called before fetching scts.
         * Otherwise, scts function will return null.
         */
        public function set_user_id($user_id)
        {
            $this->user_id_ = $user_id;
        }

        // ---------------------------------------------------------------------
        // GET SCT
        // ---------------------------------------------------------------------
        /**
         * @return int The total number of SCTs.
         */
        public function get_scts_count()
        {
            $sql_result = $this->execute_request('SELECT count(id) AS scts_count FROM sct');

            if ($result = $sql_result->fetchArray(SQLITE3_ASSOC))
            {
                return $result['scts_count'];
            }
            return -1;
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
        // SCT TOPIC
        // ---------------------------------------------------------------------
        public function get_sct_topics()
        {
            $sql_result = $this->execute_request('SELECT id, name FROM sct_topic;');

            $result = array();
            while ($current_topic = $sql_result->fetchArray(SQLITE3_ASSOC))
            {
                array_push($result, $current_topic);
            }
            return $result;
        }

        /**
         * @return bool Whether the name has been added.
         */
        public function add_sct_topic(string $topic_name)
        {
            // check that the name does not exist
            $sql_result = $this->execute_request('SELECT count(id) AS topics_count FROM sct_topic WHERE name LIKE :name', array(':name' => $topic_name));
            if ($result = $sql_result->fetchArray(SQLITE3_ASSOC))
            {
                if ($result['topics_count'] > 0)
                {
                    return false;
                }
            }
            else
            {
                return false;
            }

            $this->execute_request('INSERT INTO sct_topic (name) VALUES (:name)', array(':name' => $topic_name));

            return true;
        }

        public function delete_sct_topic(string $topic_name)
        {
            $this->execute_request('DELETE FROM sct_topic WHERE name LIKE :name', array('name' => $topic_name));
        }

        // ---------------------------------------------------------------------
        // SCT TYPE
        // ---------------------------------------------------------------------
        public function get_sct_types()
        {
            $sql_result = $this->execute_request('SELECT id, name FROM sct_type;');

            $result = array();
            while ($current_type = $sql_result->fetchArray(SQLITE3_ASSOC))
            {
                array_push($result, $current_type);
            }
            return $result;
        }

        /**
         * @return bool Whether the name has been added.
         */
        public function add_sct_type(string $type_name)
        {
            // check that the name does not exist
            $sql_result = $this->execute_request('SELECT count(id) AS types_count FROM sct_type WHERE name LIKE :name', array(':name' => $type_name));
            if ($result = $sql_result->fetchArray(SQLITE3_ASSOC))
            {
                if ($result['types_count'] > 0)
                {
                    return false;
                }
            }
            else
            {
                return false;
            }

            $this->execute_request('INSERT INTO sct_type (name) VALUES (:name)', array(':name' => $type_name));

            return true;
        }

        public function delete_sct_type(string $type_name)
        {
            $this->execute_request('DELETE FROM sct_type WHERE name LIKE :name', array(':name' => $type_name));
        }

        // ---------------------------------------------------------------------
        // UTILS
        // ---------------------------------------------------------------------
        private function estimate_sct_duration_($items_count)
        {
            return $items_count * 30.0;
        }

        private function get_sct_information_request_()
        {
            return 'SELECT sct_type.name AS current_sct_type,'
            . 'user.username AS author_last_name,'
            . 'sct.release_date AS release_date,'
            . 'sct.questions_count AS questions_count,'
            . 'sct.items_count AS items_count,'
            // statistics
            . 'sct_statistic.mean_duration AS mean_duration,'
            . 'sct_statistic.mean_score AS mean_score,'
            . 'sct_statistic.mean_vote AS mean_vote,'
            . 'sct_statistic.completion_percent AS mean_completion_percent,'
            . 'sct_statistic.launches_count AS launches_count'
            // FROM tables
            . ' FROM user, sct, sct_type, sct_statistic'
            // CONDITIONS
            . ' WHERE'
            .   ' sct_type.id=sct.sct_type_id'
            .   ' AND sct.state_id=:published_state_id'
            .   ' AND sct.author_id=user.id'
            .   ' AND sct.sct_statistic_id=sct_statistic.id';
        }
    }
?>