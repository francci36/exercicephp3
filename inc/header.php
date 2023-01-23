<header>
    <nav>
        <?php
        echo afficheMenu();
        ?>
    </nav>
</header><!--This is an HTML code that creates a header section and places a navigation menu within it. It starts by creating a <header> tag and within that, it creates a <nav> tag which is an empty container for the navigation menu. It then uses a PHP echo statement to call the afficheMenu function. As the function has a default parameter "emplacement" with a default value of "header", the function will return the HTML code for the menu that is defined in the $menu_header array.
The returned HTML code is then displayed within the <nav> tag, creating the navigation menu in the website's header.
It's important to note that the function uses a local array to create the menu, so if you want to add or modify the links, you will have to do it in the code and not dynamically.-->