<?php
/**
 * Define the pages of the app
 */
define("PAGES", ["home" => ["file" => "home.php", "name" => "Home"],
                                      "lfi" => ["file" => "lfi.php", "name" => "LFI / Path traversal"],
                                      "xss1" => ["file" => "xss1.php", "name" => "XSS Type 1"],
                                      "xss2" => ["file" => "xss2.php", "name" => "XSS Type 2"],
                                      "token" => ["file" => "token_manipulation.php", "name" => "Token manipulation"],
                                      "idor" => ["file" => "idor.php", "name" => "IDOR"],
                                      "csrf" => ["file" => "csrf.php", "name" => "CSRF"],
                                      "other" => ["file" => "other.php", "name" => "Ostatní"],
                                      "&SEP&" => [],
                                      "login" => ["file" => "login.php", "name" => "Login"],
                                      "comment" => ["file" => "comment.php", "name" => "Comment"],
                                      "announcements" => ["file" => "announcements.php", "name" => "Announcements"]]);

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