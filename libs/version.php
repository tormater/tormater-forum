<?php
// version.php
// Define the forum's software version for extensions and themes

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$tormater_forum_version = array(
  "release" => "cruiser",
  "major" => "2",
  "minor" => "1",
  "patch" => "0"
);

$releases = array(
  "snader",
  "cruiser"
);

// Returns -1 if older, 0 if same, and 1 if newer
function compare_versions($version,$forum_version) {
    global $releases;
    $compare = array_search($version["release"],$releases) <=> array_search($forum_version["release"],$releases);
    if ($compare != 0 || !array_key_exists("major",$version)) return $compare;
    $compare = $version["major"] <=> $forum_version["major"];
    if ($compare != 0 || !array_key_exists("minor",$version)) return $compare;
    $compare = $version["minor"] <=> $forum_version["minor"];
    if ($compare != 0 || !array_key_exists("patch",$version)) return $compare;
    $compare = $version["patch"] <=> $forum_version["patch"];
    return $compare;
}

function version_to_string($version) {
    return implode("-",[$version["release"],implode(".",[$version["major"],$version["minor"],$version["patch"]])]);
}

function version_from_string($version) {
    $new_version = array();
    $split_release_from_version_number = explode("-",$version);
    $new_version["release"] = $split_release_from_version_number[0];
    if (count($split_release_from_version_number) == 1) return $new_version;
    $version_number = explode(".",$split_release_from_version_number[1]);
    if (count($version_number) >= 1 && $version_number[0] != "*") $new_version["major"] = $version_number[0];
    if (count($version_number) >= 2 && $version_number[1] != "*") $new_version["minor"] = $version_number[1];
    if (count($version_number) >= 3 && $version_number[2] != "*") $new_version["patch"] = $version_number[2];
    return $new_version;
}

?>
