<?php
/**
 * Define the pages of the app
 */
define("PAGES", ["home" => ["file" => "home.php", "name" => "Home"],
                                      "lfi" => ["file" => "lfi.php", "name" => "LFI"],
                                      "auth" => ["file" => "authentication_bypass.php", "name" => "Authentication Bypass"],
                                      "upload" => ["file" => "upload.php", "name" => "Vulnerable Upload"],
                                      "rce" => ["file" => "rce.php", "name" => "RCE"],
                                      "&SEP&" => []]);

/**
 * Default page
 * @var string
 */
$CURRENT_PAGE = "home";
if (isset($_GET["p"])) {
  $CURRENT_PAGE = $_GET["p"];
}

/**
 * Build the navigation
 */
foreach (PAGES as $page => $val) {
  if ($page != "&SEP&") {
    $attr = "href=\".?p=$page\"";
    if ($page == $CURRENT_PAGE) $attr = "class=\"current\"";
    echo "<a {$attr}><div>" . $val["name"] . "</div></a>";
  } else {
    echo "<div class=\"nav-sep\"></div>";
  }
}
?>