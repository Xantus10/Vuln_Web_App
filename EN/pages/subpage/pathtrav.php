<h3>Path traversal</h3>

<p>Path traversal is related to the fact that a web application runs in some directory (and subdirectories).</p>
<ul>
  <li><p>On Linux often <span class="mono">/www</span></p></li>
  <li><p>On Windows XAMPP, it can be <span class="mono">C:\xampp\htdocs</span></p></li>
</ul>
<p>If we can perform LFI, we can try to escape from our directory using <span class="mono">../</span></p>
<p>We simply pretend that these characters are a part of a legitimate path and the OS will interpret them as moving one direcotry up.</p>

<p class="mono">example.com/?page=../../../../etc/passwd</p>

<p>On server:</p>

<p class="mono">/www/pages/ + ../../../../etc/passwd</p>
<p class="mono">/www/pages/../../../../etc/passwd</p>
<p class="mono">/etc/passwd</p>

<p>We successfully escaped from the /www/pages directory and we can traverse to any path on the system</p>

<a href=".?p=lfi&sub=lfi.php">Go to LFI</a>
