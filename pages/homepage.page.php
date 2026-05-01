<?php
// homepage.page.php
// Initializes the home page.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

echo page_generate(json_decode('{"title":"$homepage.Title","categories":{},"threads":{"query":"sort_by=activity&sort_order=asc","limit":5}}',true));

include 'footer.php';
update_last_action("action.Homepage");

?>
