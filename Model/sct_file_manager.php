<?php
    abstract class SctFileManager
    {
        public function get_scts_folder_path()
        {
            return Router::get_base_path() . '/Files/Sct';
        }

        /**
         * The sct id. 
         */
        protected $sct_id_;

        /**
         * The file name.
         */
        protected $file_name_;

        /**
         * The SCT folder path.
         */
        protected $sct_folder_path_;

        /**
         * The file path.
         * 
         * By default, files are stored in <SCT project root folder>/Files/Sct_<id>/<file_name>.sct
         */
        protected $file_path_;

        protected function __construct($sct_id, $file_name)
        {
            $this->sct_id_      = $sct_id;
            $this->file_name_   = $file_name;

            $this->sct_folder_path_ = SctFileManager::get_scts_folder_path() . '/Sct_' . $sct_id;
            $this->file_path_       = $this->sct_folder_path_ . '/'. $file_name . '.sct';
        }

        /**
         * @return string The file content to be written.
         */
        protected abstract function generate_file_content();

        /**
         * Assert the SCT folder exists, and create it if needed.
         */
        protected function create_sct_folder()
        {
            if (!file_exists($this->sct_folder_path_))
            {
                mkdir($this->sct_folder_path_);
            }
        }

        /**
         * Write the file, whose content has been generated using the 'generate_file_content' function.
         * 
         * @return bool Whether the file has been written.
         */
        public function write_file()
        {
            $this->create_sct_folder();

            $file_content = $this->generate_file_content();

            $file_handler = fopen($this->file_path_, 'wb');
            if ($file_handler == null)
            {
                return false;
            }

            return fwrite($file_handler, $file_content) != false;
        }

        protected function read_file()
        {
            $file_handler = fopen($this->file_path_, 'rb');

            $file_content = fread($file_handler, filesize($this->file_path_));
            return $file_content;
        }
    }
?>