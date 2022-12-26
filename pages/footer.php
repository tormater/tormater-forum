<?php
// footer.php
// Placed at the bottom of every page on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

?>

</div><!-- content -->
<?php listener("afterPageContent") ?>

<div id="footer">
<?php listener("beforeFooter")
echo formatFooter($config["footer"]);
listener("afterFooter") ?>
</div>
</div><!-- wrapper -->
</body>
</html>
