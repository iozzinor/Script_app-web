<?php
    class NavigationMenuItem
    {
        /**
         * The item title.
         */
        private $title_;

        /**
         * The item link.
         */
        private $link_;

        public function __construct($title, $link = null)
        {
            $this->title_ = $title;
            $this->link_ = $link;
        }

        public function get_title()
        {
            return $this->title_;
        }

        public function get_link()
        {
            return $this->link_;
        }
    }
?>