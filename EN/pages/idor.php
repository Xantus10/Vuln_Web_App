<h2>IDOR</h2>

<p>IDOR (Insecure Direct Object Reference). First let's say that most data on the server is referenced via some unique identifier (oftentimes numbers). When we want to know some information (ie. the account balance, the number of accounts) about a user, we will not reference him with his username, but with his "id number".</p>

<p>But how will the page get this information? It will send a request. This may look like this.</p>

<p class="mono">example.com/user?id=35</p>

<p>Now we know that our id is 35 and that a request to /user will return our user data. But what if we tried sending this request with id=34 or id=36?</p>

<p>If everything is done correctly, nothing should happen, but if the developer didn't bother with such protections, we could end up accessing other user's information. This is IDOR, using (<b>Insecure</b>) (<b>Direct References</b>) to (<b>Objects</b>).</p>

<p>Lastly we may mention that IDOR is enabled by weaknesses <a target="_blank" href="https://cwe.mitre.org/data/definitions/340.html">CWE-340</a> and <a target="_blank" href="https://cwe.mitre.org/data/definitions/425.html">CWE-425</a></p>

<h4>Exploitation</h4>

<p>The exploitation of this vulnerability is not difficult, just change some id somewhere and try your luck. In our application, you can try the Announcements page.</p>

<p>This page now displayes only welcome messages for new users. However there used to be a time (long ago) when interesting data was being displayed on this page. Can you find some interesting data? (Like the main door security code?)</p>
