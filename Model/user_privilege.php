<?php
    class UserPrivilege
    {
        public static $TEACHER  = 4;
        public static $EXPERT   = 2;
        public static $STUDENT  = 1;

        private static $ranks_;

        private static function initialize_ranks_()
        {
            if (self::$ranks_ == null)
            {
                self::$ranks_ = array(
                    'teacher'   => self::$TEACHER,
                    'expert'    => self::$EXPERT,
                    'student'   => self::$STUDENT
                );
            }
        }

        private $name_;
        private $associated_topics_;

        public function __construct($name, $associated_topics = null)
        {
            $this->name_ = $name;
            $this->associated_topics_ = $associated_topics;
        }

        public function get_name()
        {
            return $this->name_;
        }

        public function get_associated_topics()
        {
            if ($this->associated_topics_ == null)
            {
                return array();
            }
            return $this->associated_topics_;
        }

        public function get_rank()
        {
            self::initialize_ranks_();

            if (!array_key_exists($this->name_, self::$ranks_))
            {
                return -1;
            }
            return self::$ranks_[$this->name_];
        }
    }
?>