<footer>
    <?php
        $generation_time = GenerationTime::shared()->get_time();
        $generation_time_milliseconds = $generation_time * 1000;

        $time = sprintf(_('page_generated_in_milliseconds'), $generation_time_milliseconds);
        print('<p>' . $time . '</p>');
    ?>
</footer>