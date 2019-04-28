<?php
    require_once(dirname(__DIR__) . '/Utils/configuration.php');
    require_once(Configuration::get('root_path') . 'Model/user.php');
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?= Configuration::get('admin_title_prefix'); ?>Users</title>
        <meta charset="utf-8" />
        <base href="<?= Configuration::get_base_url(); ?>" />
        <link rel="stylesheet" href="Styles/main.css" />
    </head>
    <body>
        <main>
            <h1>Users</h1>
            <?php
                $user = new User();
                $all_users = $user->get_all_users();

                print('<table>');
                print('<tr>');
                print('<th>id</th>');
                print('<th>username</th>');
                print('<th>mail address</th>');
                print('<th>hashed password</th>');
                print('<th>activated</th>');
                print('</tr>');
                foreach ($all_users as $current_user)
                {
                    $user_id = $current_user['id'];

                    print('<tr>');
                    print('<td>' . $current_user['id'] . '</td>');
                    print('<td>' . $current_user['username'] . '</td>');
                    print('<td>' . $current_user['mail_address'] . '</td>');
                    print('<td>' . $current_user['password_hash'] . '</td>');
                    print('<td>' . ($user->is_activated($user_id) ? 'true' : 'false') . '</td>');
                }
                print('</table>');
            ?>
        </main>
    </body>
</html>