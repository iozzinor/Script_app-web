<?php
    class JavascriptGenerator
    {
        /**
         * @param string $content The script.
         * 
         * @return string The javascript script, encapsulated in 'script' tags.
         */
        public static function enclose_in_script_tags($content)
        {
            return '<script>' . $content . '</script>';
        }

        /**
         * @param string $namespace_name The namespace name.
         * @param string $content The script.
         * 
         * @return string The javascript script, enclosed in a namespace.
         */
        public static function generate_namespace($namespace_name, $content)
        {
            return '(function(' . $namespace_name . '){' . $content . '})(window.' . $namespace_name . '=window.' . $namespace_name . '||{});';
        }

        /**
         * Generate a javascript array from the values.
         * Use the transform function to create the javascript value from the PHP one.
         * The array is stored in the variable whose name is $array_name and whose parent is $namespace_name.
         * 
         */
        public static function create_array($namespace_name, $array_name, $values, $transform)
        {
            // create the array
            $result = $namespace_name . '.' . $array_name . '=[];';

            $values_count = count($values);
            for ($i = 0; $i < $values_count; ++$i)
            {
                $current_value = $values[$i];
                $result .= ' ' . $namespace_name . '.' . $array_name . '.push(' . $transform($current_value) . ');';
            }
            return $result;
        }

        // ---------------------------------------------------------------------
        // MAKE JAVASCRIPT OBJECTS
        // ---------------------------------------------------------------------
        public static function create_sct_type($sct_type)
        {
            $identifier     = $sct_type['name'];
            $id             = $sct_type['id'];
            $sct_type_name  = _d('sct_types', $identifier);
            return '{id:' . $id . ',name:"' . $sct_type_name . '",identifier:"' . $identifier . '"}';
        }

        public static function create_sct_topic($sct_topic)
        {
            $identifier     = $sct_topic['name'];
            $id             = $sct_topic['id'];
            $sct_topic_name  = _d('sct_topics', $identifier);
            return '{id:' . $id . ',name:"' . $sct_topic_name . '",identifier:"' . $identifier . '"}';
        }

        public static function create_privilege_type($privilege_type)
        {
            $identifier     = $privilege_type['name'];
            $id             = $privilege_type['id'];
            $privilege_name = _d('privilege_types', $identifier);

            return '{id:' . $id . ',name:"'. $privilege_name . '",identifier:"' . $identifier . '"}';
        }
    }
?>