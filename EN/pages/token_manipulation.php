<h2>Token manipulation</h2>

<p>Before we start examining this, let's remind ourselves of how things work. When you log into any app (also applies to this app), the application maintains your state. How? In most cases it uses cookies.</p>

<p>Open HTML inspector, navigate to the Application windowand pick Cookies, if you logged in in the previous task, you should see the <span class="mono">AUTH</span> cookie and its value.</p>

<p>The cookie value is called and authentication <b>Token</b>. And token manipulation will be about manipulating this token. How do we do it depends on the type of the token of course. Here are some token types</p>

<ul>
  <li><p>Basic - Just the username and password, the app verifies it on every request (They can be encoded using something like Base64)</p></li>
  <li><p>Session - This token is oftentimes just random value and has meaning only to the server (It cannot be manipulated much)</p></li>
  <li><p>JSON - This token contains some encoded data, if not secured, it is a primary candidate for manipulation</p></li>
</ul>

<h4>Exploitation</h4>

<p>First let's set our sights on the objective. Navigate to the Comment page. After playing around with Type 2 XSS, it became quite full huh? God bless the developer for giving us an option to delete all comments. Click the button.</p>

<p>Darn it. We have to be an admin to use it. Application will resolve our role based on our auth token (somehow). Let's explore it.</p>

<p>Copy the value of your <span class="mono">AUTH</span> cookie. It looks like it's been encoded with base64, so let's decode it with <a target="_blank" href="https://gchq.github.io/CyberChef/">cyberchef</a> and we'll see if it's just a random token or if it holds some data.</p>

<p>After decoding we can see a JSON type token with two values <span class="mono">username</span> and <span class="mono">role</span>. It doesn't seem to be secured in any way, so maybe all we have to do is change it, encode it, put it into the cookie and maybe it will work, hmm?</p>

<p>Step by step, it could look like this</p>

<ol>
  <li><p>Extract the cookie from the Application window of HTML inspector</p></li>
  <li><p>Decode from base64</p></li>
  <li><p>Take the decoded token and change role user to admin</p></li>
  <li><p>Encode the token</p></li>
  <li><p>Put the token back into the <span class="mono">AUTH</span> cookie</p></li>
  <li><p>Did it work? Try removing all comments now</p></li>
</ol>

<h4>JWT tokens</h4>

<p>In reality, raw JSON tokens aren't really used, JWT tokens are. Their functionality remains the same (they carry auth data like userID, role, etc.), but they are also signed using the server's secret key (checksum). If we change the token, the server doesn't get the right checksum and it recognizes the token has been manipulated with. To manipulate the token, we'd need the server's secret key.</p>

<p>We will not do this practically, but one of our options is LFI. The key is usually stored in a file (which we can read with LFI) or it is stored as an environment variable (but even then on linux, it is stored in <span class="mono">/proc/self/environ</span>). Thanks to this, we can go from "I can just read some files" to "I have admin privileges in the app".</p>
