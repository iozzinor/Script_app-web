<?php
    class UserPrivilege
    {
        public static $TEACHER  = 4;
        public static $EXPERT   = 2;
        public static $STUDENT  = 1;

        private static $rank_parameters_;

        private static function add_rank_parameter_(string $name, int $rank, bool $needs_associated_topics)
        {
            self::$rank_parameters_[$name] = array(
                'rank'                      => $rank,
                'needs_associated_topics'   => $needs_associated_topics
            );
        }

        private static function initialize_rank_parameters_()
        {
            if (self::$rank_parameters_ == null)
            {
                self::$rank_parameters_ = array();
                self::add_rank_parameter_('teacher',    self::$TEACHER, true);
                self::add_rank_parameter_('expert',     self::$EXPERT, true);
                self::add_rank_parameter_('student',    self::$STUDENT, false);
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
            self::initialize_rank_parameters_();

            if (!array_key_exists(strtolower($this->name_), self::$rank_parameters_))
            {
                return -1;
            }
            return self::$rank_parameters_[strtolower($this->name_)]['rank'];
        }

        public function needs_associated_topics()
        {
            self::initialize_rank_parameters_();

            if (!array_key_exists(strtolower($this->name_), self::$rank_parameters_))
            {
                return -1;
            }
            return self::$rank_parameters_[strtolower($this->name_)]['needs_associated_topics'];
        }
    }
?>