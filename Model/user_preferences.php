<?php
    class UserPreferences
    {
        protected $language_short_name_;

        public function __construct($language_short_name)
        {
            $this->language_short_name_ = $language_short_name;
        }

        public function get_language_short_name()
        {
            return $this->language_short_name_;
        }
    }
?>