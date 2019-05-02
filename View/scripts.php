<?php
    // attemp to serve the JSON language locale
    $locale_file = Router::get_base_path() . '/Content/Locale/' . Language::get_current_language()->get_medium_name() . '.js';
    $locale_url = Router::get_raw_base_url() . 'Content/Locale/' . Language::get_current_language()->get_medium_name() . '.js';
    if (file_exists($locale_file))
    {
        print('<script src="' . $locale_url . '"></script>');
    }
?>
<script src="/Content/Scripts/Locale/gettext.js"></script>
<script src="/Content/Scripts/main.js"></script>
<?php
    // load additional scripts
    foreach ($additional_scripts as $additional_script)
    {
        print('<script src="' . $additional_script['src'] . '" defer="' . ($additional_script['defer'] ?? 'true') . '"></script>');
    }
?>