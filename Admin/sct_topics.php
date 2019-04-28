<?php
    require_once(dirname(__DIR__) . '/Utils/configuration.php');
    require_once(Configuration::get('root_path') . 'Model/sct.php');
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?= Configuration::get('admin_title_prefix'); ?>SCT Topics</title>
        <meta charset="utf-8" />
        <base href="<?= Configuration::get_base_url(); ?>" />
        <link rel="stylesheet" href="Styles/main.css" />
        <link rel="stylesheet" href="Admin/sct_topics.css" />
    </head>
    <body>
        <main>
            <h1>SCT Topics</h1>

            <!-- CURRENT SCT TOPICS LIST -->
            <?php
                $sct = new Sct();

                // check sct topic name add
                function add_sct_topic_name($sct)
                {
                    if (isset($_POST['new_sct_topic_name']))
                    {
                        $new_topic_name = $_POST['new_sct_topic_name'];
                        if ($sct->add_sct_topic($new_topic_name))
                        {
                            return $new_topic_name;
                        }
                    }
                    return null;
                }
                $new_topic = add_sct_topic_name($sct);

                // check sct topic name delete
                function delete_sct_topic_name($sct)
                {
                    if (isset($_GET['delete_topic_name']))
                    {
                        $delete_topic_name = $_GET['delete_topic_name'];
                        $sct->delete_sct_topic($delete_topic_name);
                        header('location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
                        exit;
                    }
                }
                delete_sct_topic_name($sct);

                // display the sct topic list in a table
                print('<table>');
                print('<tr>');
                print('<th colspan="2">Topic Name</th>');
                print('</tr>');

                foreach ($sct->get_sct_topics() as $topic)
                {
                    print('<tr>');
                    if ($topic['name'] == $new_topic)
                    {
                        print('<td class="added_topic_name">');
                    }
                    else
                    {
                        print('<td class="topic_name">');
                    }
                    print($topic['name']);
                    print('</td>');

                    print('<td class="delete_topic">');
                    print('<input type="submit" value="delete" onclick="deleteSctTopicName(\'' . $topic["name"] . '\');" />');
                    print('</td>');
                    print('</tr>');
                }

                print('</table>');
            ?>

            <!--NEW SCT TOPIC -->
            <br />
            <p>Add a new topic:</p>
            <form onsubmit="return validateSctTopicName()" method="POST">
                <input id="new_sct_topic_field" name="new_sct_topic_name" type="text" placeholder="New Topic Name..." />
                <input id="new_sct_topic" type="submit" value="Add the new topic" />
                <label for="new_sct_topic"></label>
            </form>
        </main>

        <script src="Admin/sct_topics.js"></script>
    </body>
</html>