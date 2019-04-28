<?php
    foreach ($additional_scripts as $additional_script)
    {
        print('<script src="' . $additional_script['src'] . '" defer="' . ($additional_script['defer'] ?? 'true') . '"></script>');
    }
?>