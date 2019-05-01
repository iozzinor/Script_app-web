<p><?= $error_message; ?></p>
<?php
    $home_page_link = '<a href="' . Router::get_base_url() . 'home">' . _d('not_found', 'not_found_home_page') . '</a>';
    print('<p>');
    printf(_d('not_found', 'not_found_home_format'), $home_page_link);
    print('</p>');
?>