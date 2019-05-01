<!DOCTYPE HTML>
<?php
    $page_title = sprintf(_('site_title_format'), $title);
?>
<html>
    <head>
        <title><?= $page_title ?></title>
        <meta charset="utf-8" />
        <link rel=stylesheet href="/Content/Styles/main.css" />
        <?php
            foreach ($additional_resources as $resource)
            {
                print('<link rel="' . $resource['rel'] . '" href="' . $resource['href'] . '" />');
            }
        ?>
    </head>
    <body>
        <?php require('header.php'); ?>
        <main>
            <h1><?= $page_title ?></h1>
            <?= $content ?>
        </main>
        <?php require('footer.php'); ?>
        <?php require('scripts.php') ?>
    </body>
</html>