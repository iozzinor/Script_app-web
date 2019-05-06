<?php
    require_once(dirname(__DIR__) . '/Utils/Route/router.php');
    require_once(Router::get_base_path() . '/Model/language.php');
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?= Configuration::get('admin_title_prefix'); ?>Languages</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="/Content/Styles/main.css" />
        <link rel="stylesheet" href="/Content/Styles/hoverable_button.css" />
        <link rel="stylesheet" href="Styles/main.css" />
        <link rel="stylesheet" href="Styles/languages.css" />
    </head>
    <body>
        <main>
            <?php require 'Utils/header.php'; ?>
            <h1>Languages</h1>
            <!-- LANGUAGES LIST -->
            <table id="languages_table">
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Short Name</th>
                </tr>
                <?php
                    $language = new Language();

                    $all_languages = $language->get_all_languages();
                    $languages_count = count($all_languages);

                    for ($i = 0; $i < $languages_count; ++$i)
                    {
                        $language_information = $all_languages[$i];

                        // LANGUAGE INFORMATION BEGIN
                        ?>

                        <tr>
                            <td><input type="checkbox" id="checkbox_<?= ($i + 1); ?>" /></td>
                            <td><?= $language_information->id; ?></td>
                            <td><?= $language_information->name; ?></td>
                            <td><?= $language_information->short_name; ?></td>
                        </tr>

                        <?php
                        // LANGUAGE INFORMATION END
                    }
                ?>
            </table>

            <!-- REMOVE LANGUAGE -->
            <div id="remove_languages">
                <input id="remove_button" type="button" value="Delete" disabled="false" />
            </div>

            <!-- ADD LANGUAGE -->
            <form id="add_language_form" onsubmit="return Language.addLanguage();">
                <table>
                    <tr>
                        <td><label for="new_language_name">New Language Name:</label></td>
                        <td><input type="text" id="new_language_name" name="new_language_name" placeholder="The new language name..." /></td>
                    </tr>

                    <tr>
                        <td><label for="new_language_short_name">New Language Short Name:</label></td>
                        <td><input type="text" id="new_language_short_name" name="new_language_short_name" placeholder="The new language short name..." /></td>
                    </tr>
                </table>

                <p class="new_language_error" style="visible: hidden;"></p>
                <input id="new_language_button" type="submit" value="Add New Language" /></td>
            </form>

            <script src="/Content/Scripts/Dialog/dialog.js"></script>
            <script src="/Content/Scripts/disable_button.js"></script>
            <script src="/Content/Scripts/hoverable_button.js"></script>
            <script src="Scripts/languages.js"></script>
        </main>
    </body>
</html>