<h2>CSRF</h2>

<p>Cross Site Request Forgery will make it so that an action on one site can lead to you performing a different action on a completely different site. How? Let's see.</p>

<p>We will be using a banking app for this example. When you log in through the web portal, an auth cookie for the domain <span class="mono">banka.cz</span> will be set (The banka.cz is the bank's domain). When you want to transfer money, you will click on the page and your browser will send request to <span class="mono">banka.cz/transfer</span>. The browser will look if it has any cookies saved for this domain and it finds one (your auth cookie). And so it sends it with the request. Thanks to this the bank's server can identify you.</p>

<p>And now CSRF. WE've got a page with a picture of a kitty with the title "Click me!". In reality, clicking the image will send a request to <span class="mono">banka.cz/transfer</span>. But it's fine right? You are at a different page and you haven't provided your credentials here after all. But the browser simply sees "oh a request is being made, do I have any cookies for this domain?", and if it does, it will send them (it doesn't matter that you are on the wrong page).</p>

<img src="img/csrf.png" alt="CSRF attack schematic">

<p>In other words, through a single click (although JUST LOADING the page would work as well) you could be performing actions in other web applications if you are logged into them. Of course only if the page isn't using a CSRF protection.</p>

<h4>Exploitation preparation</h4>

<p>Even though I would like it if all the functionality could be done with this app alone, as we've mentioned you need two websites for CSRF. One vulnerable and an attacker one. We are missing the attacker application, so I prepared a minimalistic website, all you have to do is run it somewhere (one instance is enough).</p>

<ol>
  <li><p>Download python</p></li>
  <li><p>Install additional packages using <span class="mono">pip install flask pyopenssl</span></p></li>
  <li><p>Download the application <a target="_blank" href="zip.php">HERE</a> and unzip it</p></li>
  <li><p>In ip.txt, write the ip of this application (including a base path like this: <span class="mono">1.2.3.4/vuln</span>)</p></li>
  <li><p>Run <span class="mono">run.py</span></p></li>
</ol>

<h4>Exploitation</h4>

<p>Exploitation is done nearly automatically here (most of it is handled on the attacker's website). Even then, we can learn a lot.</p>

<p>If you aren't logged in, then log in. Then navigate to the attacker's website (maybe you just came by it, mayber somebody created a comment "look at these kittens" and provided the link). On the webpage open HTML inspector and open the Network tab (we'll come back to it later). After that, click the kitten image (once is enough, but click how many times you want).</p>

<p>Now switch to this application to the Comment page and be amazed.</p>

<h4>Attacker's webpage analysis</h4>

<p>Switch back to the attacker's page, in the Network tab you'll see your sent requests. If you click one and go to the <span class="mono">Request Headers</span> section, you will see a <span class="mono">Cookie</span> header. What's its value?</p>

<p>Now view the page's source code and focus one the kitten part. You will see a whole form hiding behind the image.</p>

<h4>Bonus iframe</h4>

<p>When you send a form, it will redirect you to the target site. Why didn't it happen here? The trick is in the <span class="mono">target</span> attribute of the form. This controls where the form will display its result. Default value is current window, but we display the form's result in an <span class="mono">iframe</span> tag on our page. It, however, has <span class="mono">display: none</span>.</p>

<h4>Protections</h4>

<p>There is a great number of protections against CSRF (I recommend you go back to <a href=".?p=home">homepage</a> and take a look at the OWASP cheatsheets project), we can mention the most basic one.</p>

<p>That is the <span class="mono">SameSite</span> attribute for cookies. This attribute controls the rules for when cookies are sent by the browser.</p>

<table>
  <tr>
    <th>SameSite</th>
    <th>What it do?</th>
  </tr>
  <tr>
    <td>None</td>
    <td>Control nothing, nowadays only allowed in combination with the <a target="_blank" href="https://owasp.org/www-community/controls/SecureCookieAttribute">Secure</a> attribute</td>
  </tr>
  <tr>
    <td>Lax</td>
    <td>Sent the cookie only in GET requests from other pages (No other method will work), blocks most CSRF</td>
  </tr>
  <tr>
    <td>Strict</td>
    <td>Blocks even GET requests from other pages; <b>CAUTION!</b> Page is just the two top domains (app.example.com and bank.example.com are the <b>same</b> page)</td>
  </tr>
</table>
