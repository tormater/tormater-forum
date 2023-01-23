<?php
// search.page.php
// Allows users to search through all threads on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include "header.php";

if (isset($_GET["search"])) {
    $search = $_GET["search"];
    $search = urldecode($search);
}

if(!$search || strlen($search) < 1)
{
	echo $lang["search.EmptySearch"];
}

else
{
        printf('<h2>' . $lang["search.Header"] . '</h2>', htmlspecialchars($search));
		$result = $db->query("SELECT * FROM threads WHERE (`title` LIKE '%" . $db->real_escape_string($search) . "%') LIMIT " . $config["threadsPerPage"]);

		if(!$result)
		{
			message($lang["search.NoResults"]);
		}
		
		else
		{
			if($result->num_rows == 0)
			{
				message($lang["search.NoResults"]);
			}
			
			else
			{
				echo '<table><tr><th>' . $lang["category.Thread"] . '</th><th><center>' . $lang["category.Posts"] . '</center></th><th>' . $lang["category.LastPost"] . '</th></tr>';
					
				while($row = $result->fetch_assoc())
				{				
					echo '<tr><td class="leftpart">';

					if ($row["locked"] == 1)
					{
						echo '<span class="locked">' . $lang["label.Locked"] . '</span>';
					}
	
					if ($row["sticky"] == 1)
					{
						echo '<span class="sticky">' . $lang["label.Sticky"] . '</span>';
					}

                    			echo '<b><a href="' . genURL('thread/' . $row['threadid']) . '">' . htmlspecialchars($row['title']) . "</a></b>";	
					
					$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["startuser"] . "'");
					
					while ($u = $uinfo->fetch_assoc())
					{
						if ($u["deleted"] == 1) {
                            				$username = $lang["user.Deleted"] . $u["userid"];
                        			}
						else {
                            				$username = $u["username"];
                        			}
                        echo "<div class='tdinfo'>";
						printf("<span>" .$lang["thread.Info"] . "</span>", $u["role"], genURL("user/" . htmlspecialchars($row["startuser"])), htmlspecialchars($username), date('m-d-Y h:i:s A', $row['starttime']), relativeTime($row["starttime"]));
                        echo "</div>";
					}
                    
					
                    
                    echo '</td><td class="tdposts"><center>' . $row['posts'] . '</center></td><td>';
					
					$uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["lastpostuser"] . "'");
					
					while ($u = $uinfo->fetch_assoc())
					{
						if ($u["deleted"] == 1) {
                            				$username = $lang["user.Deleted"] . $u["userid"];
                        			}
                        			else {
                            				$username = $u["username"];
                        			}
						echo '<a href="' . genURL('user/' . $row['lastpostuser']) . '" id="' . $u["role"] . '">' . $username . '</a>';
					}
					
					echo '<div class="tddate" title="' . date('m-d-Y h:i:s A', $row['lastposttime']) . '">' . relativeTime($row["lastposttime"]) . '</div></td></tr>';
				}	
				echo "</table>";
            }
        }
}

include "footer.php";

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action($lang["action.Search"]);
}

?>
