<!DOCTYPE HTML>
<html>
    <head>
        <title>Script odont - <?= $title ?></title>
        <meta charset="utf-8" />
        <link rel=stylesheet href="Styles/main.css" />
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
            <h1>The script odont site - <?= $title ?></h1>
            <?= $content ?>
        </main>
        <?php require('footer.php'); ?>

        <script src="Scripts/main.js"></script>

        <?php require('scripts.php') ?>
    </body>
</html>