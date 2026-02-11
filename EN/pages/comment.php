<h2>Comment section</h2>


<?php
/**
 * User must be logged in
 */
if (!isset($USERNAME)) {
  echo "<p class=\"red\">Musíte se nejprve přihlásit!<p>";
  exit;
}
?>

<form action="" method="post">
  <input type="hidden" name="form" value="comment">
  <label for="title">Title of the comment</label><br>
  <input type="text" name="title" id="title" required><br>
  <label for="cont">Content of the comment</label><br>
  <textarea name="cont" id="cont" cols="40" rows="6" required></textarea><br>
  <input type="submit">
</form>

<hr>

<form action="" method="post">
  <input type="hidden" name="form" value="erase">
  <input type="submit" value="Delete all comments">
</form>

<?php
/**
 * After unsuccessful attempt at erasing comments, show
 */
if (isset($_GET["aderr"])) {
  echo "<p class=\"red\">You have to be admin to perform this action!</p>";
}
?>

<?php
/**
 * Including file must provide this variable
 */
if (!isset($COMMENTS_PATH)) {
  echo "COMMENTS_PATH variable not set!";
  exit;
}

// Read the file
$len = filesize($COMMENTS_PATH);
if ($len == 0) {
  exit;
}
$file = fopen($COMMENTS_PATH,"r");
$cont = fread($file, $len);
fclose($file);

$data = json_decode($cont, true);

/**
 * Display the comments
 */
foreach ($data as $val) {
  $tit = $val["title"];
  $con = $val["cont"];
  $usr = $val["username"];
  echo "<h4>{$usr} - {$tit}</h4>";
  echo "<textarea cols=\"40\" rows=\"6\" readonly>{$con}</textarea><br><br>";
}

?>
