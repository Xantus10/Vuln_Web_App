<h2>Tools</h2>

<p>If you are going to be an ethical hacker, you will need tools to help you hack. If you are going to be a defender, you will need to know which tools you will be attacked with and their capabilities. Either way, you should familiarize yourself with some web hacking weapons.</p>

<h3>Kali</h3>

<p>First of all, I need to mention that you should use linux. Most cybersecurity tools are made for linux. Additionally with a distribution like <a href="https://www.kali.org/" target="_blank">Kali Linux</a> a lot of tools come pre-installed and you can install other tools much more easily than on Windows.</p>

<h3>Gobuster</h3>

<p><a href="https://www.kali.org/tools/gobuster/" target="_blank">Gobuster</a> is a web directory enumeration tool. Essentially websites can have many hidden directories that the average user is not aware of (for example an uploads direcotry). Or there may be some publicly accessible interesting files (like configuration or backup files).</p>

<p>When you pass a URL and a wordlist to gobuster, it will make a request to all the possible paths specified in the wordlist and display the results. Now you have a clear view of most of the accessible paths on a website and you can choose your next course of action.</p>

<h4>Usage</h4>

<p>First to install the tool, you just need to do <span class="mono">sudo apt install gobuster</span> on kali. I also recommend installing <span class="mono">Dirbuster</span>, because it comes with the beforementioned wordlists.</p>

<p>Gobuster has many availible commands, but the two you will use the most are <span class="mono">dir</span> and <span class="mono">fuzz</span>.</p>

<p class="mono">gobuster dir -u https://example.com -w /path/to/wordlist</p>

<p>This command will launch gobuster against the example.com domain and will use the wordlist specified in -w (on kali wordlists are in <span class="mono">/usr/share/wordlists</span>)</p>

<p class="mono">gobuster fuzz-u https://example.com/?msgid=FUZZ -w /path/to/wordlist</p>

<p>The fuzz functionality replaces every FUZZ with a word from the wordlist. The FUZZ can be in URL, request body, headers. Now for usage: Remember IDOR? In the original Vuln app, you manually changed the <span class="mono">msgid</span> until you found the one you wanted. With FUZZ, you can make automate it. The wordlist you would use would be a list of numbers 0..1000 and gobuster would try them all.</p>

<h3>Hydra</h3>

<p><a href="https://www.kali.org/tools/hydra/" target="_blank">Hydra</a> is a login brute force tool. It supports a variety of protocols (ftp, shh) and various HTTP brute forces.</p>
