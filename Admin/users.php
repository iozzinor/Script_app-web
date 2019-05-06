<?php
    require_once(dirname(__DIR__) . '/Utils/Route/router.php');
    require_once(Router::get_base_path() . '/Model/user.php');
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?= Configuration::get('admin_title_prefix'); ?>Users</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="Styles/main.css" />
    </head>
    <body>
        <main>
            <?php require 'Utils/header.php'; ?>
            <h1>Users</h1>
            <table>
                <tr>
                    <th>Id</th>
                    <th>Username</th>
                    <th>Hashed Password</th>
                </tr>
                <?php
                    $user = new User();

                    $all_users = $user->get_all_users();
                    $users_count = count($all_users);

                    for ($i = 0; $i < $users_count; ++$i)
                    {
                        $user_information = $all_users[$i];

                        // USER INFORMATION BEGIN
                        ?>

                        <tr>
                            <td><?= $user_information->id; ?></td>
                            <td><?= $user_information->username; ?></td>
                            <td><?= $user_information->password_hash; ?></td>
                        </tr>

                        <?php
                        // USER INFORMATION END
                    }
                ?>
            </table>
        </main>
    </body>
</html>