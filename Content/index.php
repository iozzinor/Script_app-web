<?php
    session_start();
    
    $current_device = $_SESSION['device'] ?? 'Desktop';
    if (strlen($current_device) > 0)
    {
        $current_device[0] = strtoupper($current_device[0]);
    }

    $requested_file = str_replace('/Content', __DIR__  . '/' . $current_device, $_SERVER['REQUEST_URI']);
    
    if (file_exists($requested_file))
    {
        $requested_file_url = str_replace(__DIR__, $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Content', $requested_file);
        header('Location: ' . $requested_file_url);
        exit(0);
    }
    else
    {
        http_response_code(404);
    }
?>