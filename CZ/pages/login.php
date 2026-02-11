<h2>Login</h2>

<?php

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
  echo "<p class=\"red\">Server musí běžet nad HTTPS, vytvořte selfsigned certifikát s</p>";
  echo "<p class=\"mono\">openssl req -x509 -nodes -days 36500 -newkey rsa:2048 -keyout server.key -out server.crt -subj \"/CN=*\"</p>";
}

?>

<form action="" method="post">
  <input type="hidden" name="form" value="login">
  <label for="username">Uživatelské jméno</label>
  <input type="text" name="username" id="username" placeholder="user123" required><br>
  <input type="submit">
</form>

<?php
if (isset($USERNAME)) {
  echo "<p class=\"green\">Přihlášen jako {$USERNAME}</p>";
}
?>
