<?php
    sleep(1);
    require_once(dirname(dirname(__DIR__)) . '/Utils/Route/router.php');
    require_once(Router::get_base_path() . '/Model/language.php');

    // check parameters existence
    if (!isset($_POST['new_language_name']))
    {
        print('New language name was not provided.');
        http_response_code(400); // bad request
    }
    else if (!isset($_POST['new_language_short_name']))
    {
        print('New language short name was not provided.');
        http_response_code(400); // bad request
    }

    $new_language_name          = $_POST['new_language_name'];
    $new_language_short_name    = $_POST['new_language_short_name'];

    $language = new Language();
    try
    {
        $new_language_id = $language->add_language($new_language_name, $new_language_short_name);
        print($new_language_id);
    }
    catch (Exception $exception)
    {
        print($exception->getMessage());
        http_response_code(400); // bad request
    }
?>