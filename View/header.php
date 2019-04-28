<header>
    <nav>
        <ul>
            <?php
                foreach ($navigation_links as $navigation_link)
                {
                    print('<li><a href="' . $navigation_link['href'] . '">' . $navigation_link['name'] . '</a></li>');
                }
            ?>
        </ul>
    </nav>
</header>