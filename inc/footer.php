<footer>
    <nav class="footer">
    <?php
        echo afficheMenu('footer');
    ?>
    </nav>
</footer><!--This is an HTML code that creates a footer section and places a navigation menu within it. It starts by creating a <footer> tag and within that, it creates a <nav> tag with the class "footer" and it's an empty container for the navigation menu.
It then uses a PHP echo statement to call the afficheMenu function, passing in the argument 'footer' as the emplacement parameter. This will cause the function to return the HTML code for the menu that is defined in the $menu_footer array.
The returned HTML code is then displayed within the <nav> tag, creating the navigation menu in the website's footer.
It's important to note that the function uses a global array to create the menu, so if you want to add or modify the links, you will have to do it in the code and not dynamically.-->