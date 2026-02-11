<h2>XSS Type 1</h2>


<figure>
  <img src="img/XSStwitchchat.png" alt="XSS absued in twitch chat">
  <figcaption>Payload in twitch chat is interpreted by chat display application</figcaption>
</figure>

<p>XSS (Cross Site Scripting) is a vulnerability related to putting raw data into the page (messages, posts, comments, etc.). It is once again enabled by the weakness <a target="_blank" href="https://cwe.mitre.org/data/definitions/20.html">CWE-20</a>.</p>

<p>When the web developer lets us write into the page's source code, he can put our input there unsanitised. Then we can put not only text, but also HTML tags like <span class="mono">&lt;b&gt;</span>. These tags then look like a part of the webpage and get interpreted as HTML.</p>

<p>But why stop at graphical knickknacks like <span class="mono">&lt;b&gt;</span>? We can use tags like <span class="mono">&lt;script&gt;</span>. Thanks to that we can put any javascript code that runs in our victim's web browser. As a test, we usually use</p>

<p class="mono">&lt;script&gt;alert('XSS')&lt;/script&gt;</p>

<p>Real attacker could utilize <span class="mono">document.cookies</span> to read your authentication cookies and use <span class="mono">fetch</span> to send them to his server.</p>

<h4>Exploit</h4>

<div class="subpage-container">
  <form action="" method="get">
    <input type="hidden" name="p" value="xss1">
    <input type="text" name="search" placeholder="Text to search">
    <input type="submit">
  </form>
  <?php
    /**
     * Easy logic for Reflected XSS (No input sanitisation)
     */
    if (isset($_GET["search"])) {
      $search = $_GET["search"];
      echo "Search results for \"$search\"";
    }
  ?>
</div>

<p>This window serves for searching within our application (just for illustration purposes), for now it just prints whatever the user searched for. Just try inputing - let's say "pants".</p>

<p>But when we take time to analyse our situation, we find out that the developer just allowed us to write into the page's source code. Oh no! What if he didn't sanitise it!</p>

<p>First try typing in</p>

<p class="mono">&lt;b&gt;Some text&lt;/b&gt;</p>

<p>See? Text got interpreted as HTML tags. Now we can try running some javascript (like our alert function).</p>

<h4>Type 1 - Reflected XSS</h4>

<p><b>That's not all!</b> We did discover a vulnerability, but we can utilize it only in our own browser at the moment. Up until now we spoke about XSS in general, what does the Type 1 mean?</p>

<p>Reflected XSS means that our payload is transferred in query parameters (ours is - parameter search). We can take the whole URL and send it to somebody with the message "Look, do you think this present is good?", and the moment they click the URL, the payload moves from the query parameters into <b>their</b> page and runs in <b>their</b> browser. Try sending the URL after exploitation to a friend of yours (if you are doing this with somebody).</p>

<p>Note: If you noticed characters like %28, %27, %2F, etc. in the URL, those are just URL-Encoded variants of the characters from input (You cannot freely transfer all character inside URL). Try <a target="_blank" href="https://gchq.github.io/CyberChef/">cyberchef</a>! Find URL Decode and and decode the search parameter from the URL.</p>

<p>Type 1 XSS can be targeted for a single person. However it cannot be distributed globally. Let's take a look at Type 2 XSS!</p>
