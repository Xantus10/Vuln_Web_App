<?php

$UPLOAD_DIR = "uploads";

$FILENAME = basename($_FILES["file"]);

move_uploaded_file($_FILES["file"]["tmp_name"], "$UPLOAD_DIR/$FILENAME");

?>



<h2>Vulnerable upload</h2>

<p>Many websites feature some sort of upload functionality. You may be able to include attachments in your message, or you might be able to upload your avatar. However as with any user input, it is important to sanitise the file input as well.</p>

<p>Common ways of sanitisation include</p>

<ul>
  <li><p>Sanitising based on extension - Easy for a hacker to change the extension from .exe to .png</p></li>
  <li><p>Sanitising based on magic bytes - If a file content begins with MZ / ELF, it is an executable</p></li>
  <li><p>Checking whole content - Best solution, check the full file content and verify it</p></li>
</ul>

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

<p>When you request this file and add <span class="mono">?cmd=whoami</span> in the query, the site will echo back the result of whoami command</p>

<h3>Primitive exploit</h3>

<div class="subpage-container">
  <p>Simple file upload</p>
  <form action="" method="post" enctype="multipart/form-data">
    <label for="file">Select a file: </label>
    <input type="file" name="file" id="file" required>
    <input type="submit">
  </form>
</div>
