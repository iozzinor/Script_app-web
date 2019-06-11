<header>
    <nav id="main_navigation">
        <?php
            // display menus
            function display_fixed_menu($menu)
            {
                $item = $menu->get_items()[0];
                print('<div class="menu_fixed">');
                print('<button class="menu_title"><a href="' . $item->get_link() . '">' . $item->get_title() . '</a></button>');
                print('</div>');
            }

            function display_multilevel_menu($menu)
            {
                $submenus = $menu->get_items();

                print('<div class="menu_dropdown">');
                print('<button class="menu_title">' . $menu->get_title() . '</button>');
                print('<div class="dropdown_content">');

                $submenus_count = count($submenus);
                for ($i = 0; $i < $submenus_count; ++$i)
                {
                    $submenu = $submenus[$i];

                    print('<div class="submenu">');
                    print('<h1>' . $submenu->get_title() . '</h1>');
                    display_menu($submenu, false);
                    print('</div>');
                }

                print('</div>');
                print('</div>');
            }

            function display_menu($menu, $embbed_in_menu_dropdown = true)
            {
                $items = $menu->get_items();

                if ($embbed_in_menu_dropdown)
                {
                    print('<div class="menu_dropdown">');
                    print('<button class="menu_title">' . $menu->get_title() . '</button>');

                    print('<div class="dropdown_content">');
                }

                print('<ul>');
                $items_count = count($items);
                for ($i = 0; $i < $items_count; ++$i)
                {
                    $item = $items[$i];
                    $link = $item->get_link();
                    if ($link != null)
                    {
                        print('<li><a href="' . $link . '">' . $item->get_title() . '</a></li>');
                    }
                    else
                    {
                        print('<li class="current_language">' . $item->get_title() . '</li>');
                    }
                }
                print('</ul>');

                if ($embbed_in_menu_dropdown)
                {
                    print('</div>');

                    print('</div>');
                }
            }

            if (isset($navigation_menus))
            {
                foreach ($navigation_menus as $navigation_menu)
                {
                    if ($navigation_menu->is_fixed())
                    {
                        display_fixed_menu($navigation_menu);
                    }
                    else if ($navigation_menu->is_multilevel())
                    {
                        display_multilevel_menu($navigation_menu);
                    }
                    else
                    {
                        display_menu($navigation_menu);
                    }
                }
            }
        ?>
    </nav>
</header>