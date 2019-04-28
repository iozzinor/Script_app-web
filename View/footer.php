<footer>
    <?php
        $generation_time = GenerationTime::shared()->get_time();
        $generation_time_milliseconds = $generation_time * 1000;

        printf("<p>Page generated in %6.3f milliseconds.", $generation_time_milliseconds);
    ?>
</footer>