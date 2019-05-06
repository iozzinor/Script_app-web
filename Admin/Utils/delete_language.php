<?php
    require_once(dirname(dirname(__DIR__)) . '/Utils/Route/router.php');
    require_once(Router::get_base_path() . '/Model/language.php');

    // check parameters existence
    if (!isset($_POST['language_id']))
    {
        print('Language id to delete was not provided.');
        http_response_code(400); // bad request
    }

    $language_id = intval($_POST['language_id']);

    $language = new Language();
    try
    {
        $new_language_id = $language->delete($language_id);
    }
    catch (Exception $exception)
    {
        http_response_code(400); // bad request
    }
?>