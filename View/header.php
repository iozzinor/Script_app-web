<header>
    <nav id="main_navigation">
        <ul>
            <?php
                if (isset($navigation_links))
                {
                    foreach ($navigation_links as $navigation_link)
                    {
                        print('<li><a href="' . $navigation_link['href'] . '">' . $navigation_link['name'] . '</a></li>');
                    }
                }
            ?>
        </ul>
    </nav>
    <nav id="site_language">
        <p><?= _('Choose Language'); ?></p>
        <ul>
            <?php
                $base_url = Router::get_raw_base_url();
                $current_language = Language::get_current_language()->get_short_name();
                $query = Router::get_query();
                foreach (Language::get_supported_languages() as $supported_language)
                {
                    if ($supported_language->get_short_name() == $current_language)
                    {
                        print('<li>' . $supported_language->get_full_name() . '</li>');
                    }
                    else
                    {
                        print('<li><a href="' . $base_url . $supported_language->get_short_name() . '/' . $query . '">' . $supported_language->get_full_name() . '</a></li>');
                    }
                }
            ?>
        </ul>
    </nav>
</header>