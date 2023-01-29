<?php
// footer.php
// Placed at the bottom of every page on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;



echo '</div>';
listener("afterPageContent");
echo '<div id="footer">';

listener("beforeFooter");
echo formatFooter($config["footer"]);
listener("afterFooter"); 

echo '</div>';
echo '<div id="copyright">';
if(isset($config["forumTheme"]) and file_exists(dirname(__DIR__,1) . "/themes/" . $config["forumTheme"] . "/icon.svg"))
{
    echo '<img width="22" src="' . genURL('themes/' . $config["forumTheme"] . '/icon.svg') . '">';
}
echo '© 2022-2023 <a href="https://github.com/tormater/tormater-forum">Tormater Forum</a>';
echo '</div>';
listener("afterCopyright");

echo '</div>';
echo '</div>';

listener("pageEnd");

echo '</body>';
echo '</html>';
