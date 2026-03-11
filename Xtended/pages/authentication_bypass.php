<?php

### Sessions

$SESSIONFILE = 'session.txt';

$AUTH_SESSION_USERNAME = null;

$AUTH_SESSION_COOKIENAME = "AUTH_SESSION";

if (file_exists($SESSIONFILE)) {
  $sessionData = null;
  $content = file_get_contents($SESSIONFILE);
  
  // Decode JSON as associative array
  $sessionData = json_decode($content, true);

  if (json_last_error() !== JSON_ERROR_NONE) {
      $sessionData = null; // invalid JSON
  }

  if ($sessionData != null && isset($_COOKIE[$AUTH_SESSION_COOKIENAME])) {
    $cookie = $_COOKIE[$AUTH_SESSION_COOKIENAME];
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

### JWT 1

include_once("../components/jwt.php");

$AUTH_JWT_1 = null;
$AUTH_JWT_1_ROLE = null;

$AUTH_JWT_1_COOKIENAME = "AUTH_JWT_1";

$AUTH_JWT_1_KEY = "SuperSecretJWTHS256Key***Yea?";

if (isset($_COOKIE[$AUTH_JWT_1_COOKIENAME])) {
  $token = $_COOKIE[$AUTH_JWT_1_COOKIENAME];
  if (!JWT::verify($token, $AUTH_JWT_1_KEY)) {
    $AUTH_JWT_1 = false;
  } else {
    $data = JWT::decode($token);
    $AUTH_JWT_1 = $data['payload']['username'];
    $AUTH_JWT_1_ROLE = $data['payload']['role'];
  }
}

### JWT 2

$AUTH_JWT_2 = null;
$AUTH_JWT_2_ROLE = null;

$AUTH_JWT_2_COOKIENAME = "AUTH_JWT_2";

$AUTH_JWT_2_PRIV_KEY = file_get_contents("keys/private.pem");
$AUTH_JWT_2_PUB_KEY = file_get_contents("keys/public.pem");

if (isset($_COOKIE[$AUTH_JWT_2_COOKIENAME])) {
  $token = $_COOKIE[$AUTH_JWT_2_COOKIENAME];
  if (!JWT::verify($token, $AUTH_JWT_2_PUB_KEY)) {
    $AUTH_JWT_2 = false;
  } else {
    $data = JWT::decode($token);
    $AUTH_JWT_2 = $data['payload']['username'];
    $AUTH_JWT_2_ROLE = $data['payload']['role'];
  }
}

### JWT 3

$AUTH_JWT_3 = null;
$AUTH_JWT_3_ROLE = null;

$AUTH_JWT_3_COOKIENAME = "AUTH_JWT_3";

$AUTH_JWT_3_KEY = file_get_contents("keys/jwt3.txt");

if (isset($_COOKIE[$AUTH_JWT_3_COOKIENAME])) {
  $token = $_COOKIE[$AUTH_JWT_3_COOKIENAME];
  if (!JWT::verify($token, $AUTH_JWT_3_KEY)) {
    $AUTH_JWT_3 = false;
  } else {
    $data = JWT::decode($token);
    $AUTH_JWT_3 = $data['payload']['username'];
    $AUTH_JWT_3_ROLE = $data['payload']['role'];
  }
}

### Form submission

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

      setcookie($AUTH_SESSION_COOKIENAME, $result);
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
  } elseif ($form == "jwt_auth_1") {
    $username = $_POST['username'];

    $token = JWT::sign(["username" => $username, "role" => "user"], $AUTH_JWT_1_KEY, "HS256");

    setcookie($AUTH_JWT_1_COOKIENAME, $token);
  } elseif ($form == "jwt_auth_2") {
    $username = $_POST['username'];

    $token = JWT::sign(["username" => $username, "role" => "user"], $AUTH_JWT_2_PRIV_KEY, "RS256");

    setcookie($AUTH_JWT_2_COOKIENAME, $token);
  } elseif ($form == "jwt_auth_3") {
    $username = $_POST['username'];

    $token = JWT::sign(["username" => $username, "role" => "user"], $AUTH_JWT_3_KEY, "HS256");

    setcookie($AUTH_JWT_3_COOKIENAME, $token);
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
<p class="mono">setcookie($AUTH_SESSION_COOKIENAME, $result);</p>

<p>Now, knowing that the admin has logged in just today, can you manufacture a valid SSID for him?</p>

<div class="subpage-container">
  <p>Login session</p>
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
        echo "<p class=\"red\">You cannot log in as admin</p>";
      }
    ?>
  </form>
</div>

<h3>JWT tokens</h3>

<p>Json Web Tokens are tokens which hold some information. The token in the original Vuln app was basically a JWT, but <b>it wasn't signed</b>.</p>

<p>The signature is some control value. To compute this value, you need some secret key that the server uses. The exact signature depends on the used algorithm. These are the main ones:</p>

<ul>
  <li><p>none - Don't check the signature (ignore it)</p></li>
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

<b>For easy work with JWT tokens, I recommend you find a website like <a href="https://jwt.rocks/" target="_blank">this one</a>.</b>

<p>Now let's try various ways of breaking it.</p>

<h3>"alg": "none"</h3>

<p>You surely noticed something strange in those algorithm options, that being the <span class="mono">'none'</span> option. Your first task is simple:</p>

<ol>
  <li><p>Get the JWT token form your cookie</p></li>
  <li><p>Change its <span class="mono">'alg'</span> value to <span class="mono">'none'</span></p></li>
  <li><p>Change your role to admin</p></li>
</ol>

<div class="subpage-container">
  <p>Login JWT 1</p>
  <form action="" method="post">
    <input type="hidden" name="form" value="jwt_auth_1">
    <label for="username">Username: </label>
    <input type="text" name="username" id="username" placeholder="User123" required><br>
    <input type="submit">
    <?php
      if (isset($AUTH_JWT_1)) {
        if (!$AUTH_JWT_1) {
          echo "<p class=\"red\">Your JWT token is invalid!</p>";
        } else {
          echo "<p class=\"green\">Logged in as {$AUTH_JWT_1}</p>";
          if (isset($AUTH_JWT_1_ROLE)) {
            echo "<p class=\"green\">Your role is $AUTH_JWT_1_ROLE</p>";
          }
        }
      }
    ?>
  </form>
</div>

<h3>JWT confusion attack</h3>

<p>And we don't have to change the topic from the algorithms yet. There is still one more trick up my sleeve. The JWT confusion attack!</p>

<p>Picture this, you use the RS256 alg for JWT, therefore you keep your PRIVATE KEY on your server and you use it for signing. You post your PUBLIC KEY, well ... publicly, and you + anyone else can use it for verification. This is the important bit, you use the PUBLIC KEY for <b>verification</b>.</p>

<p>Then what if you change your alg to HS256? Your HMAC will get computed using the verification key = PUBLIC KEY. Now if only you could obtain this PUBLIC KEY, then you could sign your own JWT with HS256 and have the server verify it like that!</p>

<p>In summary:</p>

<ol>
  <li><p>Verify if the JWT uses RS256 alg</p></li>
  <li><p>Check if PUBLIC KEY is accessible</p></li>
  <li><p>Sign your own token with HS256 and use the PUBLIC KEY as key</p></li>
  <li><p>Use your crafted token and try your luck</p></li>
</ol>

<p>Your task is once again to have the app recognize your role as admin.</p>

<p>Note: you could just do the 'none' attack again, but what would you learn from it?</p>

<?php
echo "<p class=\"mono\">{$AUTH_JWT_2_PUB_KEY}</p>";
?>

<div class="subpage-container">
  <p>Login JWT 2</p>
  <form action="" method="post">
    <input type="hidden" name="form" value="jwt_auth_2">
    <label for="username">Username: </label>
    <input type="text" name="username" id="username" placeholder="User123" required><br>
    <input type="submit">
    <?php
      if (isset($AUTH_JWT_2)) {
        if (!$AUTH_JWT_2) {
          echo "<p class=\"red\">Your JWT token is invalid!</p>";
        } else {
          echo "<p class=\"green\">Logged in as {$AUTH_JWT_2}</p>";
          if (isset($AUTH_JWT_2_ROLE)) {
            echo "<p class=\"green\">Your role is $AUTH_JWT_2_ROLE</p>";
          }
        }
      }
    ?>
  </form>
</div>

<h3>Using LFI</h3>

<p>Finally, as you can probably guess, the whole security of JWT depends on the <b>secret</b> key. But guess what? This secret key has to be stored somewhere. And if it happens to be in a file, then you could read it with something like LFI!</p>

<p>That is why LFI is such a dangerous vulnerability. Not because the attacker can read <span class="mono">/etc/passwd</span> and find out there is a user named bob, but because it can lead to leakage of other secret keys / data. Therefore things can escalate from "I can only read some files with LFI" to "I can craft JWT and get web admin privileges".</p>

<p>So, knowing that the directory sturcture looks something like this:</p>

<ul>
  <li><p>lfi.php - The source script</p></li>
  <li><p>uploads - Folder for uploaded materials</p></li>
  <li><p>keys</p>
    <ul>
      <li><p>jwt3.txt - I wonder what could be hidden here?</p></li>
    </ul>
  </li>
</ul>

<p>Can you get the key with LFI and then sign your own JWT token?</p>

<div class="subpage-container">
  <p>Login JWT 3</p>
  <form action="" method="post">
    <input type="hidden" name="form" value="jwt_auth_3">
    <label for="username">Username: </label>
    <input type="text" name="username" id="username" placeholder="User123" required><br>
    <input type="submit">
    <?php
      if (isset($AUTH_JWT_3)) {
        if (!$AUTH_JWT_3) {
          echo "<p class=\"red\">Your JWT token is invalid!</p>";
        } else {
          echo "<p class=\"green\">Logged in as {$AUTH_JWT_3}</p>";
          if (isset($AUTH_JWT_3_ROLE)) {
            echo "<p class=\"green\">Your role is $AUTH_JWT_3_ROLE</p>";
          }
        }
      }
    ?>
  </form>
</div>
