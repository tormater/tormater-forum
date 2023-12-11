<?php
// templates.php
// Template engine for pages.
if (!defined("INDEXED")) exit;

$menu_pages = array();

class Template
{
    function render($path, $strings)
    {
        global $config;
        $page = trim(file_get_contents($path));
        
        // Global loading of other templates. 
        // There's no protection against recursion so good luck!
        $template_global_tags = array("header", "footer");
        $template_var_tags = array(
            "forumTheme" => $config["forumTheme"], 
            "forumName" => $config["forumName"],
            "forumDescription" => $config["forumDescription"],
            "forumFooter" => $config["footer"],
            "baseURL" => $config["baseURL"],
        );

        foreach ($template_global_tags as $k) {
            if (!str_contains($page, "[[ #" . $k . " ]]")) continue;
            $page = str_replace("[[ #" . $k . " ]]", $this->render("templates/" . $k . ".html", $menu_pages), $page);
        }
        foreach ($template_var_tags as $k => $v) {
            $page = str_replace("[[ $" . $k . " ]]", $v, $page);
        }

        if (!$strings) return $page;

        foreach ($strings as $k => $v) {
            if (!$v) $v = "";
            $page = str_replace("[[ " . $k . " ]]", $v, $page);
        }
        return $page;
    }
    function error($type)
    {
        global $cfg, $lang;
        $data = array(
            "title" => $lang["error." . $type],
            "desc" => sprintf($lang["error.desc"], "javascript:history.back()", $cfg->get("baseURL")),
        );
        echo $this->render("templates/error.html", $data);
        exit;
    }
}

$template = new Template;
