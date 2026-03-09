<h2>LFI</h2>

<p>LFI was discussed in the standard Vuln web app. However, it is such a useful attack, that we will include the vulnerability here. Below is just a standard LFI, no strings attached.</p>

<p>And why is it here? We will utilize it to execute various other attacks! So look forward to that!</p>

<div class="subpage-container">
  <p>Fancy something from the uploads folder, folks?</p>
  <form action="" method="get">
    <input type="hidden" name="p" value="lfi">
    <label for="file">Filename: </label>
    <input type="text" name="file" id="file" placeholder="image.png" required><br>
    <input type="submit">
  </form>
</div>

<?php
  if (isset($_GET["file"])) {
    echo "<div class=\"subpage-container\">";
    include_once("uploads/{$_GET["file"]}");
    echo "</div>";
  }
?>

<h3>AAAnd that's not all! - Bypassing defenses</h3>

<p>I have also implemented several versions of this lfi with various protections in place. Let's bypass them all!</p>

<h3>Example from previous app.</h3>

<p>The web developer denied us the option to use <span class="mono">../</span> with the following code.</p>

<p class="mono">include_once(str_replace("../", "", $_GET["prot1"]));</p>

<p>Do you still remember how to bypass this?</p>

<p>Hint: That particular string is being replaced <b>just once</b>.</p>

<div class="subpage-container">
  <p>First protection bypass</p>
  <form action="" method="get">
    <input type="hidden" name="p" value="lfi">
    <label for="prot1">Filename: </label>
    <input type="text" name="prot1" id="prot1" placeholder="image.png" required><br>
    <input type="submit">
  </form>
</div>

<?php
  if (isset($_GET["prot1"])) {
    echo "<div class=\"subpage-container\">";
    include_once(str_replace("../", "", $_GET["prot1"]));
    echo "</div>";
  }
?>

<h3>Another one</h3>

<p>And less work for me! You see, you didn't have to do all that to bypass that vulnerability. Look back at the source code. It literally just includes <b>exactly what you pass</b>. Now try to exploit it again!</p>

<p>Hint: Who said it needs to be a relative path? I feel more <b>absolute</b> today.</p>

<h3>Frontend defenses</h3>

<p>We haven't paid it much attention in the Vuln app, but webs are divided into frontend and backend.</p>

<ul>
  <li><p><b>Frontend</b> is the application in your browser (uses HTML+CSS+JS)</p></li>
  <li><p><b>Backend</b> is the application on the server (in our case PHP)</p></li>
</ul>

<p>We have two applications, which means we can validate in two different places. The question is where?</p>

<p>Well, the input below is sanitised on the frontend, but not on the backend. Let's see if you can break it.</p>

<div class="subpage-container">
  <p>Frontend protection bypass</p>
  <form action="" method="get">
    <input type="hidden" name="p" value="lfi">
    <label for="prot2">Filename: </label>
    <input type="text" name="prot2" id="prot2" placeholder="image.png" required pattern="^[a-zA-Z0-9\._\-]+$"><br>
    <input type="submit">
  </form>
</div>

<?php
  if (isset($_GET["prot2"])) {
    echo "<div class=\"subpage-container\">";
    include_once($_GET["prot2"]);
    echo "</div>";
  }
?>

<p>Have you tried it? There are actually two solutions (at the very least).</p>

<p>First option is to go into the HTML inspector and delete the "pattern" attribute of the input. This might not always work as sometimes Javascript is used for validation. Then it might be a bit harder.</p>

<p>The second option bypasses this entirely. We do not necessarily need the frontend to make requests. Just send some valid request and manually change the query parameter in the URL.</p>

<p><b>Moral of the story</b>: Frontend validation is just so that the standard user doesn't make a mistake by accident. It provides no security, therefore always validate on backend.</p>
