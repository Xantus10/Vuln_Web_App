<?php
$SESSIONFILE = 'session.txt';

$AUTH_SESSION_USERNAME = null;


if (file_exists($SESSIONFILE)) {
  $sessionData = null;
  $content = file_get_contents($SESSIONFILE);
  
  // Decode JSON as associative array
  $sessionData = json_decode($content, true);

  if (json_last_error() !== JSON_ERROR_NONE) {
      $sessionData = null; // invalid JSON
  }

  if ($sessionData != null && isset($_COOKIE["AUTH_SESSION"])) {
    $cookie = $_COOKIE["AUTH_SESSION"];
    foreach ($sessionData as $sess) {
      if ($cookie === $sess["ssid"]) {
        $AUTH_SESSION_USERNAME = $sess["username"];
        break;
      }
    }
    $hash = md5("admin");
    $date = date("d-m-Y");
    $random = 4;

    $result = "$hash-$date-$random";

    if ($cookie == $result) {
      $AUTH_SESSION_USERNAME = "admin (Congratulations!)";
    }
  }
}


if (isset($_POST["form"])) {
  $pth = $_SERVER['REQUEST_URI'];
  $form = $_POST["form"];
  if ($form == "session_auth") {
    if (isset($_POST['username'])) {
      $username = $_POST['username'];

      if ($username == "admin") {
        return header("Location: {$pth}&sserr=1");
      } 

      $hash = md5($username);
      $date = date("d-m-Y");
      $random = rand(0, 9);

      $result = "$hash-$date-$random";

      setcookie("AUTH_SESSION", $result);
      $sessions = [];

      // 2. Load existing sessions if file exists
      if (file_exists($SESSIONFILE)) {
          $content = file_get_contents($SESSIONFILE);
          $decoded = json_decode($content, true);

          if (is_array($decoded)) {
              $sessions = $decoded;
          }
      }

      // Append new session
      $sessions[] = [
          "ssid" => $result,
          "username" => $username
      ];

      // Save back to file
      file_put_contents($SESSIONFILE, json_encode($sessions, JSON_PRETTY_PRINT));
    }
  }

  return header("Location: {$pth}");
}
?>


<h2>Authentication bypass</h2>

<p>In the Vuln app, you have manipulated a simple primitive login token. Now we are going to investigate <b>all the others</b> (well almost).</p>

<h3>Session cookie</h3>

<p>Session authentication works by assigning you some sort of token and then the server holds some data associated to the token. Typically it looks like this:</p>

<ul>
  <li><p class="mono">aff31...</p> => name: Admin, role: admin</li>
  <li><p class="mono">44e5a...</p> => name: Tom, role: user</li>
</ul>

<p>Since the data is on the server side, we cannot manipulate it. Can we however manipulate the token (called ssid or sessionid)?</p>

<p>With a good ssid, the answer is no. A good ssid should fulfill the following:</p>

<ol>
  <li><p>The token, or its part must not be predictable</p></li>
  <li><p>The token must have sufficient entropy (randomness, nowadays you can see anything between 16 and 32 bytes)</p></li>
  <li><p>The token must not be recurring (Same or similar token for same or similar user)</p></li>
</ol>

<p>What if it doesn't fulfill these? Well this example will hopefully showcase that.</p>

<p>Below is a login form, you will notice that you can log in as any user you want, except admin. And that's your exercise, have the page echo back "Logged in as admin".</p>

<p>Here is some source code from the app:</p>

<p class="mono">$username = $_POST['username'];</p>
<br>
<p class="mono">if ($username == "admin") return header("Location: {$pth}&sserr=1");</p>
<br>
<p class="mono">$hash = md5($username);</p>
<p class="mono">$date = date("d-m-Y");</p>
<p class="mono">$random = rand(0, 9);</p>
<br>
<p class="mono">$result = "$hash-$date-$random";</p>
<br>
<p class="mono">setcookie("AUTH_SESSION", $result);</p>

<p>Now, knowing that the admin has logged in just today, can you manufacture a valid SSID for him?</p>

<div class="subpage-container">
  <p>Login</p>
  <form action="" method="post">
    <input type="hidden" name="form" value="session_auth">
    <label for="username">Username: </label>
    <input type="text" name="username" id="username" placeholder="User123" required><br>
    <input type="submit">
    <?php
      if (isset($AUTH_SESSION_USERNAME)) {
        echo "<p class=\"green\">Logged in as {$AUTH_SESSION_USERNAME}</p>";
      }
      if (isset($_GET["sserr"])) {
        echo "<p class=\"green\">You cannot log in as admin</p>";
      }
    ?>
  </form>
</div>

<h3>JWT tokens</h3>

<p>Json Web Tokens are tokens which hold some information. The token in the original Vuln app was basically a JWT, but <b>it wasn't signed</b>.</p>

<p>The signature is some control value. To compute this value, you need some secret key that the server uses. The exact signature depends on the used algorithm. These are the main ones:</p>

<ul>
  <li><p>None - Don't check the signature (ignore it)</p></li>
  <li><p>HS - HMAC, compute hash with the secret key, only the server with the key can verify it</p></li>
  <li><p>RS - RSA signature, uses private key for signing, anyone can verify the token with public key</p></li>
</ul>

<p>Here is an example JWT</p>

<p class="mono"><span class="red">eyJhbGciOiJSUzI1NiJ9</span>.<span class="green">eyJ1c2VybmFtZSI6ImFkbWluIn0</span>.i4XqU8HqqEFU26kDAL2zTGXXOlsfm9iizkuYn3G1WbjDMCARQJxQxveCQ2FB7a5-tqEo1emYt8J0zStzSbQhPwHgl7Hq729S-3mCQcIta1jdpeXj1SCXZoZU-R1QJeKHXpdCE1E6ifzPo2G4LSYQL3qyuwbyKEUqoDliNgPWtpo</p>

<p>It is divided into 3 parts separated by '.', each part is encoded with base64</p>

<ul>
  <li><p>Red - This part is the header, try decoding the base64, it is a JSON featuring info about the token (like the algorithm used)</p></li>
  <li><p>Green - This is the data part, the base64 is once again hiding a JSON</p></li>
  <li><p>White - This is the signature</p></li>
</ul>

<p>Now let's try various ways of breaking it.</p>


