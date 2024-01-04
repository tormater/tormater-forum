<?php
// footer.php
// Placed at the bottom of every page on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;
global $template;

$data = array(
    "copyright" => "© 2022-" . date("Y") . " <a href='https://github.com/tormater/tormater-forum'>Tormater Forum</a>",
    "footer" => formatFooter($config["footer"]),
);

ob_start();
listener("beforeFooter");
$data["footer"] = $data["footer"] . ob_get_contents();
ob_end_clean();
ob_start();
listener("afterFooter"); 
$data["footer"] .= ob_get_contents();
ob_end_clean();

echo $template->render("templates/footer.html", $data);
