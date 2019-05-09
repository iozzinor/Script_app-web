<?php
    class SctItem
    {
        /**
         * The hypothesis.
         */
        private $hypothesis_;

        /**
         * An associative array containing the new data and its type.
         */
        private $new_data_;

        public function __construct($hypothesis, $new_data)
        {
            $this->hypothesis_  = $hypothesis;
            $this->new_data_    = $new_data;
        }

        public function get_hypothesis()
        {
            return $this->hypothesis_;
        }

        public function get_new_data()
        {
            return $this->new_data_;
        }
    }
?>