<?php

$UPLOAD_DIR = "pages/uploads";

if (isset($_FILES["file"])) {
  $FILENAME = basename($_FILES["file"]["name"]);

  switch ($_POST["form"]) {
    case "no":
      move_uploaded_file($_FILES["file"]["tmp_name"], "$UPLOAD_DIR/$FILENAME");
      break;
    case "magic":
      $mimeType = mime_content_type($_FILES["file"]["tmp_name"]);
      if ($mimeType != "image/png") {
        echo "<p class=\"red\">That is most definetly not a png!</p>";
      } else {
        move_uploaded_file($_FILES["file"]["tmp_name"], "$UPLOAD_DIR/$FILENAME");
      }
      break;
    case "ext":
      $extension = strtolower(pathinfo($FILENAME, PATHINFO_EXTENSION));
      if ($extension != "png") {
        echo "<p class=\"red\">That extension is not .png!</p>";
      } else {
        move_uploaded_file($_FILES["file"]["tmp_name"], "$UPLOAD_DIR/$FILENAME");
      }
      break;
  }
}

?>



<h2>Vulnerable upload</h2>

<p>Many websites feature some sort of upload functionality. You may be able to include attachments in your message, or you might be able to upload your avatar. However as with any user input, it is important to sanitise the file input as well.</p>

<p>Common ways of sanitisation include</p>

<ul>
  <li><p>Sanitising based on extension - Easy for a hacker to change the extension from .exe to .png</p></li>
  <li><p>Sanitising based on magic bytes - If a file content begins with MZ / ELF, it is an executable</p></li>
  <li><p>Checking whole content - Best solution, check the full file content and verify it</p></li>
</ul>

<b>In our app the uploaded stuff is accessible at: <span class="mono">WEB_ROOT/pages/uploads/...</span>, try it with the file <span class="mono">test.png</span></b>

<h3>Web shells</h3>

<p>But what could a vulnerable file upload really lead to? It depends on the server environment. In the case of PHP it is really bad. Why?</p>

<p>Well, PHP will by default just interpret any file with the .php extension. This means that if we upload a php file and then request it, our code will get interpreted on the server - We have achieved RCE (Remote code execution).</p>

<p>And one common task for our php file on the server might be a simple pipeline</p>

<ol>
  <li><p>Take a value from the request (like from <span class="mono">$_GET</span> query params)</p></li>
  <li><p>Call php <span class="mono">system()</span> function with the query param value</p></li>
  <li><p>Echo back the result</p></li>
  <li><p>Now we have a sort of primitive way to execute commands on the system</p></li>
</ol>

<p>This is called a web shell. The simplest one you can make looks something like this:</p>

<p class="mono">&lt;?php system($_GET["cmd"]) ?&gt;</p>

<p>Though you can make it a bit more cryptic like this:</p>

<p class="mono">&lt;?php $x=$_GET["\x63\x6d\x64"];system(base64_decode($x)) ?&gt;</p>

<p>This one also accepts the <span class="mono">cmd</span> param, but the command should be base64 encoded. (This is great to bypass WAF)</p>

<p>When you request this file and add <span class="mono">?cmd=whoami</span> in the query, the site will echo back the result of whoami command</p>

<h3>Primitive exploit</h3>

<div class="subpage-container">
  <p>Simple file upload</p>
  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="form" value="no">
    <label for="file">Select a file: </label>
    <input type="file" name="file" id="file" required>
    <input type="submit">
  </form>
</div>

<h3>Magic bytes defense</h3>

<p>Fun's over now sadly. Now the file is validated if it really contains a PNG based on its header and its magic bytes.</p>

<p>But we are not done yet! Think! If it's just validating the header, what if you just get a valid PNG header from an image, after that add your PHP web shell code and asve it with a .php extension? Try it!</p>

<p>Note: For editing binary files (like PNGs) I recommend <a href="https://gchq.github.io/CyberChef/" target="_blank">cyberchef</a></p>

<div class="subpage-container">
  <p>Only real png files! (We check content)</p>
  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="form" value="magic">
    <label for="file">Select a file: </label>
    <input type="file" name="file" id="file" required>
    <input type="submit">
  </form>
</div>

<h3>Troubles with extension?</h3>

<p>Now to extension validation. Unfortunatelly with php, this can give us a hard time. That's because php will refuse to interpret any files that are not .php! So what can we do?</p>

<p>Don't worry, there is your ol' reliable LFI to save the day again! The vulnerable function in our LFI is <span class="mono">include_once()</span>, a function used for <b>including php code</b>. And guess what? For some reason include_once doesn't care about file extensions! It just includes the file and interprets any php it found.</p>

<p>The plan is the following:</p>

<ol>
  <li><p>Upload your web shell</p></li>
  <li><p>Got to LFI and include your uploaded file</p></li>
  <li><p>Lastly add the cmd param to the query and test the functionality</p></li>
</ol>

<div class="subpage-container">
  <p>Only .png files</p>
  <form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="form" value="ext">
    <label for="file">Select a file: </label>
    <input type="file" name="file" id="file" required>
    <input type="submit">
  </form>
</div>

<h3>Reality</h3>

<p>In reality you may have to combine these two approaches to get around input validation. But even then, you haven't won.</p>

<p>PHP can have multiple protections in place</p>

<ul>
  <li><p>Disabled functions - Functions like <span class="mono">system</span>, <span class="mono">exec</span>, <span class="mono">popen</span> may be disabled (You can try to exploit around this, but it's hard)</p></li>
  <li><p>Advanced WAF filters - You may have to improve your script to bypass more strict WAF filters (encrypt the commands and responses)</p></li>
  <li><p>AV suspicious file - And finally if you include too much suspicious code, you might get flagged by the server AV</p></li>
</ul>
