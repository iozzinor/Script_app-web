<?php
    require_once 'sct_item.php';
    
    class SctQuestion
    {
        private $wording_;
        private $type_;
        private $topics_;
        private $items_;

        public function __construct($wording, $type, $topics, $items)
        {
            $this->wording_ = $wording;
            $this->type_    = $type;
            $this->topics_  = $topics;
            $this->items_   = $items;
        }

        public function get_wording()
        {
            return $this->wording_;
        }

        public function get_items()
        {
            return $this->items_;
        }

        public function get_type()
        {
            return $this->type_;
        }

        public function get_topics()
        {
            return $this->topics_;
        }
    }
?>