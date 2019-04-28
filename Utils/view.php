<?php
    /**
     * Load and display a view.
     */
    class View
    {
        private $file_path_;

        public function __construct(string $file_path)
        {
            $this->file_path_ = $file_path;
        }

        public function generate($data = array(), string $template = null)
        {
            $view = $this->generate_file($this->file_path_, $data);
            
            /* If the template is
            set then use the template file
            as a template.
            
            The custom file view that was generated is passed to the template
            using the 'content' variable.
            */
            if (isset($template))
            {
                $template_data = array_merge(array('content' => $view), $data);
                $view = $this->generate_file($template, $template_data);
            }
            
            // display the created view
            echo $view;
        }

        private function generate_file($file_path, $data)
        {
            if (file_exists($file_path))
            {
                extract($data);
                ob_start();

                require($file_path);

                return ob_get_clean();
            }
            else
            {
                throw new Exception('Can not get file \'' . $file_path . '\'.');
            }
        }
    }
?>