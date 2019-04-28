<?php
    header('Content-Type: application/json');

    $error_message = $exception->getMessage();
    $error_message = htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8', false);

    $exception_array = array();
    $exception_array['description'] = $error_message;
    $exception_array['code'] = $exception->getCode();

    $result = json_encode(array('exception' => $exception_array));
    print($result);
?>