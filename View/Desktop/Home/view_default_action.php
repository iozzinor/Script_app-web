<p><?=l('welcome_message')?></p>
<?php
    if ($display_new_sct_subject_link)
    {
        $new_sct_string = _d('home', 'new_sct');
        $create_new_sct_format = _d('home', 'create_new_sct_format');

        print('<p>');
        printf($create_new_sct_format, '<a href="new_sct_subject">' . $new_sct_string . '</a>');
        print('</p>');
    }
?>