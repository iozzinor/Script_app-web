<?php
    require_once 'sct_item.php';
    
    class SctQuestion
    {
        private $wording_;
        private $type_;
        private $items_;

        public function __construct($wording, $type, $items)
        {
            $this->wording_ = $wording;
            $this->type_ = $type;
            $this->items_ = $items;
        }
    }
?>