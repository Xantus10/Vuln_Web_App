<h2>Your messages</h2>

<?php
/**
 * User must be logged in
 */
if (!isset($USERNAME)) {
  echo "<p class=\"red\">You have to log in first!<p>";
  exit;
}

/**
 * Indexes of the messages to display to the normal user
 * @var array
 */
$MSG_IDS = [11, 12];

/**
 * Including file must provide this variable
 */
if (!isset($ANNOUNCEMENTS_PATH)) {
  echo "ANNOUNCEMENTS_PATH variable not set!";
  exit;
}
// Read the announcements
$len = filesize($ANNOUNCEMENTS_PATH);
if ($len == 0) {
  exit;
}
$file = fopen($ANNOUNCEMENTS_PATH,"r");
$cont = fread($file, $len);
fclose($file);

$data = json_decode($cont, true);
?>

<div class="subpage-container">
  <?php
    // Show the message titles
    foreach ($MSG_IDS as $ix) {
      $title = $data[$ix]["title"];
      echo "<a href=\".?p=announcements&msgid={$ix}\">{$title}</a><br>";
    }
  ?>
</div>

<hr>

<div class="subpage-container">
  <?php
    // Show the selected message content
    if (isset($_GET["msgid"])) {
      $msgid = intval($_GET["msgid"]);
      if ($msgid < 0 || $msgid >= count($data)) {
        echo "<p class=\"red\">There is no such message!</p>";
        exit;
      }
      $title = $data[$msgid]["title"];
      $content = $data[$msgid]["content"];
      echo "<h4>{$title}</h4>";
      echo "<p>{$content}</p>";
    } else {
      echo "<p class=\"red\">First select a message</p>";
    }
  ?>
</div>
