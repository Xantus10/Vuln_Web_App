<h2>Other attacks</h2>

<p>On this web, we have gone over many different attacks. However, we are still missing a few. This isn't because I forgot about them, but because these attacks are too dangerous (yes, it is these attacks that you would like to find in reality). The attack usually leads to RCE (Remote Code Execution), enabling you to run your commands on the remote server.</p>

<h2>SQL injection</h2>

<p>Injection type are attacks, where the developer permitted us to write somewhere in the source code, but he didn't sanitise our input (XSS is an injection attack). SQL is language for interacting with a database. SQL injection will happen when the developer lets us write in the database query and enables us to manipulate the query to a certain extent. What can we do with this?</p>

<ul>
  <li><p>Find the database structure (the databases, tables, columns)</p></li>
  <li><p>Read data from the database (like usernames and passwords)</p></li>
  <li><p>If we are lucky, we may be able to manipulate or delete data</p></li>
  <li><p>And finally, when poorly configured, SQLi can lead to RCE</p></li>
</ul>

<p>Practically, it may look like this</p>

<p class="mono">SELECT * FROM users WHERE username='<span class="red">admin</span>' AND password='admin'</p>

<p>Here the user entered his credentials into the login page, he entered username admin and password also admin. This query will return a successful result only if a user with this username AND this password exists (therefore we can perform password validation with this query). Also take note of the fact that our strings are surrounded by apostrophes.</p>

<p class="mono">SELECT * FROM users WHERE username='<span class="red">admin' Here I can write </span>' AND password='admin'</p>

<p>Here, we entered an unsanitised apostrophe and the rest of our message will get interpreted as SQL.</p>

<p class="mono">SELECT * FROM users WHERE username='<span class="red">admin' -- </span>' AND password='admin'</p>

<p>And we decided for the most elementary variant of SQLi. The <span class="mono red">--</span> symbol will comment out the rest of the SQL query. In other words we get authenticated as admin, but we skip password check (because we commented it out).</p>

<h2>File upload</h2>

<p>Oftentimes, application may allow you to upload files, this can however become a serious issue. Especially for systems using PHP or ASP .NET</p>

<p>How can a file upload be exploited? We upload a so called <b>Web Shell</b>. In essence a short script which performs the following:</p>

<ol>
  <li><p>Take command from URL parameters</p></li>
  <li><p>Execute the command</p></li>
  <li><p>Echo the output of the command onto the webpage</p></li>
</ol>

<p>A primitive PHP shell can look like this:</p>

<p class="mono">&lt;?php system($_GET["cmd"]) ?&gt;</p>

<p>If our target server supports interpreting PHP files and we request our webshell file with the parameter <span class="mono">cmd=whoami</span>. We recieve the username under which the web application runs on the output. We have reached RCE.</p>


