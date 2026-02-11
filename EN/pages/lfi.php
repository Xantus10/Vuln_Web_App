<h2>LFI / Path traversal</h2>

<p>LFI (Local File Inclusion) and Path traversal are two connected vulnerabilities. First let's intoduce them separately.</p>

<div class="subpage-container">
  <?php
  /**
   * If there isn't a subpage specified, specify it as lfi.php
   */
  if (!isset($_GET["sub"])) {
    $query = $_GET;
    $query["sub"] = "lfi.php";
    $queryString = http_build_query($query);
    $path = strtok($_SERVER['REQUEST_URI'],"?");

    header("Location: {$path}?{$queryString}");
    exit;
  }
  // Include the subpage
  include_once("subpage/" . $_GET["sub"]);
  ?>
</div>

<img src="img/pathtrav.png" alt="Path traversal illustration">

<h4>Exploitation</h4>

<p>Now to practical exploitation. If while (or after) you read through the texts about LFI and Path traversal looked into the URL and saw the described situation there, you can give yourself a pat on the head. In URL, we can see the sub parameter. This parameter controls the displayed subpage (the files lfi.php and pathtrav.php). How about you try your luck? Maybe the parameter is vulnerable?</p>

<ul>
  <li><p>Note 1: Make sure to add enough "../" the system will survive if you enter too many, but it will not work if you enter too few</p></li>
  <li><p>Note 2: On linux, you can try the file /etc/passwd, on windows try Windows/win.ini</p></li>
</ul>

<h4>Bonus</h4>

<p>Now a few bonus facts:</p>

<p>If you enter a bad input into the sub parameter (nonexistent file), It will output a piece of the applications code and the absolute path to your file. Thanks to this we can identify the OS (Linux/Windows), used web server (Apache/Nginx/...) and the context of our vulnerable function (maybe we can see some protections the function tries). This is bad of course and it is the weakness <a target="_blank" href="https://cwe.mitre.org/data/definitions/209.html">CWE-209</a>. Try to cause such an error and see what you can find out from it.</p>

<p>Now a word about protections. The easiest option is to sanitise the input. However even then it may not be 100%. Let's say we implemented a protection which erases the "../" strings.</p>

<p class="mono">/www/pages/../../../../etc/passwd</p>

<p>After erasing</p>

<p class="mono">/www/pages/etc/passwd</p>

<p>We just lost our path traversal right? Not really, The attacker can try the following.</p>

<p class="mono">/www/pages/....//....//....//....//etc/passwd</p>

<p>How will erasing the "../" behave now?</p>

<p class="mono">/www/pages/..<span class="red">../</span>/..<span class="red">../</span>/..<span class="red">../</span>/..<span class="red">../</span>/etc/passwd</p>

<p>After erasing, the path will look like this.</p>

<p class="mono">/www/pages/../../../../etc/passwd</p>

<p>And this input will perform path traversal.</p>
