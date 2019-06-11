<footer>
    <?php
        // generation time
        $generation_time = GenerationTime::shared()->get_time();
        $generation_time_milliseconds = $generation_time * 1000;

        $time = sprintf(_('page_generated_in_milliseconds'), $generation_time_milliseconds);
        print('<p>' . $time . '</p>');
    ?>
    <?php
        // switch view
        $current_uri = $_SERVER['REQUEST_URI'];
        $current_uri = preg_replace('/[?].*/', '', $current_uri);
        
        $new_device = ($_SESSION['device'] == 'desktop' ? 'mobile' : 'desktop');

        $switch_link    = '//' . $_SERVER['HTTP_HOST'] . $current_uri . '?device=' . $new_device;
        $message_format = _d('common', 'Switch to %s view.');

        $message = sprintf($message_format, _d('common', $new_device));

        print('<p><a href="' . $switch_link . '">' . $message . '</a></p>');
    ?>
</footer>