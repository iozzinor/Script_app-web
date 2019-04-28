<?php
    require_once(dirname(__DIR__) . '/Utils/configuration.php');
    require_once(Configuration::get('root_path') . 'Model/sct.php');
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?= Configuration::get('admin_title_prefix'); ?>SCT Types</title>
        <meta charset="utf-8" />
        <base href="<?= Configuration::get_base_url(); ?>" />
        <link rel="stylesheet" href="Styles/main.css" />
        <link rel="stylesheet" href="Admin/sct_types.css" />
    </head>
    <body>
        <main>
            <h1>SCT Types</h1>

            <!-- CURRENT SCT TYPE LIST -->
            <?php
                $sct = new Sct();

                // check sct type name add
                function add_sct_type_name($sct)
                {
                    if (isset($_POST['new_sct_type_name']))
                    {
                        $new_type_name = $_POST['new_sct_type_name'];
                        if ($sct->add_sct_type($new_type_name))
                        {
                            return $new_type_name;
                        }
                    }
                    return null;
                }
                $new_type = add_sct_type_name($sct);

                // check sct type name delete
                function delete_sct_type_name($sct)
                {
                    if (isset($_GET['delete_type_name']))
                    {
                        $delete_type_name = $_GET['delete_type_name'];
                        $sct->delete_sct_type($delete_type_name);
                        header('location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
                        exit;
                    }
                }
                delete_sct_type_name($sct);

                // display the sct type list in a table
                print('<table>');
                print('<tr>');
                print('<th colspan="2">Type Name</th>');
                print('</tr>');

                foreach ($sct->get_sct_types() as $type)
                {
                    print('<tr>');
                    if ($type['name'] == $new_type)
                    {
                        print('<td class="added_type_name">');
                    }
                    else
                    {
                        print('<td class="type_name">');
                    }
                    print($type['name']);
                    print('</td>');

                    print('<td class="delete_type">');
                    print('<input type="submit" value="delete" onclick="deleteSctTypeName(\'' . $type["name"] . '\');" />');
                    print('</td>');
                    print('</tr>');
                }

                print('</table>');
            ?>

            <!--NEW SCT TYPE -->
            <br />
            <p>Add a new type:</p>
            <form onsubmit="return validateSctTypeName()" method="POST">
                <input id="new_sct_type_field" name="new_sct_type_name" type="text" placeholder="New Type Name..." />
                <input id="new_sct_type" type="submit" value="Add the new type" />
                <label for="new_sct_type"></label>
            </form>
        </main>

        <script src="Admin/sct_types.js"></script>
    </body>
</html>