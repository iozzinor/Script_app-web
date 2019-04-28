<?php
    class SctItem
    {
        private $hypothesis_;
        private $new_data_;

        public function __construct($hypothesis, $new_data)
        {
            $this->hypothesis_ = $hypothesis;
            $this->new_data_ = $new_data;
        }
    }
?>