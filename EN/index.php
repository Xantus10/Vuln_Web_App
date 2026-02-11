<?php
/**
 * Path to comments file
 * @var string
 */
$COMMENTS_PATH = "pages/data/comments.json";

/**
 * Path to messages file
 * @var string
 */
$ANNOUNCEMENTS_PATH = "pages/data/announcements.json";

/**
 * Username (if user is logged in)
 * @var string
 */
$USERNAME = null;

/**
 * Role of the user (if user is logged in)
 * @var string
 */
$ROLE = null;

/**
 * Check for authentication cookie
 */
if (isset($_COOKIE["AUTH"])) {
  $dec = base64_decode($_COOKIE["AUTH"]);
  $data = json_decode($dec, true);
  if (json_last_error() == JSON_ERROR_NONE) {
    $USERNAME = $data["username"] ?? null;
    $ROLE = $data["role"] ?? null;
  }
}

/**
 * Handle when form was sent from any site
 */
if (isset($_POST["form"])) {
  $pth = $_SERVER['REQUEST_URI'];
  $form = $_POST["form"];
  /**
   * Login form handling logic
   */
  if ($form == "login") {
    if (isset($_POST["username"])) {
      $username = $_POST["username"];
      $data = ["username" => $username, "role" => "user"];
      $json = json_encode($data);
      $b64 = base64_encode($json);
      setrawcookie("AUTH", $b64, ["secure" => true, 'samesite' => "None"]);
      /**
       * NOTE: On https support
       * This app MUST go over HTTPS for CSRF
       * 
       */
    }
  /**
   * Comment form handling logic
   */
  } elseif ($form == "comment") {
    if (isset($_POST["title"]) && isset($_POST["cont"]) && isset($USERNAME)) {
      // Load the variables
      $title = $_POST["title"];
      $cont = $_POST["cont"];
      $new = ["title"=> $title,"cont"=> $cont, "username" => $USERNAME];

      // Read the current file
      $len = filesize($COMMENTS_PATH);
      $cont = "";
      if ($len > 0) {
        $file = fopen($COMMENTS_PATH,"r");
        $cont = fread($file, $len);
        fclose($file);
      }

      $data = [];
      if (strlen($cont) != 0) {
        $data = json_decode($cont, true);
      }

      // Append to JSON
      $data[] = $new;

      // Write to file
      $file = fopen($COMMENTS_PATH,"w");
      fwrite($file, json_encode($data));
    }
  /**
   * Erase comments form logic
   */
  } elseif ($form == "erase") {
    // Check for admin role
    if ($ROLE == "admin") {
      if (file_exists($COMMENTS_PATH)) {
        unlink($COMMENTS_PATH);
      }

      touch($COMMENTS_PATH);

      if (isset($_GET["aderr"])) {
        $pth = substr($pth, 0, -8);
      }
    } else {
      $pth .= "&aderr=1";
    }
  }
  // After POST make browser issue a GET
  header("Location: {$pth}");
}

?>


<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Aplikace pro showcase a paktickou exploitaci určitých webových útoků">
  <meta name="author" content="Jroslav Žaba">
  <link rel="stylesheet" href="index.css">
  <title>Vuln | Webové útoky</title>
</head>
<body>
  <div class="cont">
    <header class="header">
      <?php
        include_once("components/header.php");
      ?>
    </header>

    <main class="main">
      <nav class="nav">
        <?php
          include_once("components/nav.php");
        ?>
      </nav>
      <article class="article">
        <?php
          include_once("pages/" . PAGES[$CURRENT_PAGE]["file"]);
        ?>
      </article>
    </main>
    
    <footer class="footer">
      <?php
        include_once("components/footer.php");
      ?>
    </footer>
  </div>
</body>
</html>