<?php
    class NavigationMenu
    {
        /**
         * The menu title.
         */
        private $title_;

        /**
         * The menu subitems.
         */
        private $items_;

        public function __construct($title, $items)
        {
            $this->title_ = $title;
            $this->items_ = $items;
        }

        public function get_title()
        {
            return $this->title_;
        }

        public function get_items()
        {
            return $this->items_;
        }

        /**
         * @return bool Whether this menu should be presented as a fixed menu and not as a dropdown one.
         */
        public function is_fixed()
        {
            return $this->title_ === '' && count($this->items_) == 1;
        }

        /**
         * @return bool True if the items are of type NavigationMenu.
         */
        public function is_multilevel()
        {
            return count($this->items_) > 0 && $this->items_[0] instanceof NavigationMenu;
        }
    }
?>