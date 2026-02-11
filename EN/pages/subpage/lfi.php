<h3>LFI</h3>

<p>LFI alone simply means we include (<b>inclusion</b>) some (<b>local file</b>). This situation happens when two conditions meet.</p>

<ol>
  <li><p>The page dynamically loads another file (subpage)</p></li>
  <li><p>The subpage input is not sanitised (<a target="_blank" href="https://cwe.mitre.org/data/definitions/20.html">CWE-20</a>)</p></li>
</ol>

<p>In practice it might look like this:</p>

<p class="mono">example.com/?page=index.php</p>
<p class="mono">example.com/index.php?avatarimage=default.png</p>

<a href=".?p=lfi&sub=pathtrav.php">Go to Path traversal</a>
