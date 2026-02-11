<h2>XSS Type 2</h2>

<p>On the XSS Type 1 page, we introduced the concept of XSS (So return there if you don't get it). We also discussed what the Type 1 means (Reflected XSS). Next up, Type 2</p>

<h4>Type 2 - Stored XSS</h4>

<p>With Type 1 XSS, we could add content only temporarily and only for our client (or the one that made the request). Type 2 XSS is Stored, because the payload is stored on the server. An example of stored XSS is therefore any content that persists in the application, i.e.</p>

<ul>
  <li><p>Comments</p></li>
  <li><p>Posts</p></li>
  <li><p>Even the username, if it's displayed somewhere</p></li>
</ul>

<p>Stored XSS doesn't pick its target, it will attack anyone who loads the page (even attacker himself). Sometimes Type 2 XSS is called hack-and-forget - Plant the payload in the app and then never access it again, because you would run your own malicious script.</p>

<h4>Exploitation</h4>

<p>Now let's start making use of other features of this app, navigate to the Login page and just log in. (The username doesn't matter + no need to investigate the login logic)</p>

<p>After login, you can create comments on the Comment page. For starters try some graphical payload (tags b, i, h1, etc.). <b>Type the payload into the Title field.</b></p>

<p>After you create the comment, you will see it persists on the server (it will stay there even if you completely close and reopen your browser). Now, you may try some javascript payload, <b>careful though! I don't recommend using an invasive function like alert(), rather use function console.log()!</b> (Output of log() is visible in HTML inspectior in the Console window)</p>

<h4>Textarea bypass</h4>

<p>We can inject XSS into title with zero issue. Now try putting it into the content of the comment. Did it work?</p>

<p>I think not. Whether you try javascript or some graphical payload, nothing works. Anything inside textarea just doesn't get interpreted.</p>

<img src="img/XSS2payloadhtml.png" alt="Textarea HTML showcase">

<p>But don't worry! We have complete control over the page's sorce code. We can start a new tag, who is to say we cannot end a different one? (like our textarea)</p>

<p>In other words, start your payload with the closing tag of textarea.</p>

<p class="mono">&lt;/textarea&gt;Rest here</p>
