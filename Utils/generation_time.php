<?php
    /**
     * Handle the generation time.
     * 
     * The page should call the static initialize function at the beginning.
     */
    class GenerationTime
    {
        /**
         * Singleton shared instance.
         */
        private static $shared_;

        /**
         * Singleton shared instance getter.
         */
        public static function shared()
        {
            if (!isset(self::$shared_))
            {
                self::$shared_ = new GenerationTime();
            }
            return self::$shared_;
        }

        /**
         * Construct the shared instance.
         */
        public static function initialize()
        {
            self::$shared_ = new GenerationTime();
        }

        /**
         * The start time in seconds.
         */
        private $start_time_;

        private function __construct()
        {
            $this->start_time_ = $this->get_micro_time_();
        }

        /**
         * @return float The time in seconds.
         */
        private function get_micro_time_()
        {
            $result = microtime();
            $result = explode(' ', $result);
            return $result[1] + $result[0];
        }

        /**
         * @return float The elapsed time.
         */
        public function get_time()
        {
            $end_time = $this->get_micro_time_();

            return $end_time - $this->start_time_;
        }
    }
?>