<table class="privileges_information">
    <tr>
        <th></th>
        <?php
            foreach ($privileges['type_names'] as $privilege_type)
            {
                print('<th>' . $privilege_type . '</th>');
            }
        ?>
    </tr>
    <?php
        $base_url = Router::get_raw_base_url();
        $allowed_image  = $base_url . 'Content/Attachments/Images/allowed.png';
        $disabled_image = $base_url . 'Content/Attachments/Images/disabled.png';

        foreach ($privileges['rights'] as $right)
        {
            print('<tr>'); // begin row

            print('<td>' . $right['name'] . '</td>'); // right name

            $values_count = count($right['values']);
            for ($i = 0; $i < $values_count; ++$i)
            {
                $value = $right['values'][$i];
                print('<td class="' . ($i % 2 == 0 ? 'even' : 'odd') . '">');

                if ($value)
                {
                    print('<img src="' . $allowed_image . '" />');
                }
                else
                {
                    print('<img src="' . $disabled_image . '" />');
                }

                print('</td>');
            }

            print('</tr>'); // end row
        }
    ?>
</table>