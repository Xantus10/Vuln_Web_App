<h3>LFI</h3>

<p>Samotné LFI znamená prostě že do stránky vložíme (<b>inclusion</b>) nějaký soubor, co je lokálně na serveru (<b>local file</b>). Tato situace nastává přitom, když se sejdou dvě podmínky.</p>

<ol>
  <li><p>Stránka dynamicky načítá nějaký jiný soubor (podstránku)</p></li>
  <li><p>Vstup pro podstránku není ošetřen (<a target="_blank" href="https://cwe.mitre.org/data/definitions/20.html">CWE-20</a>)</p></li>
</ol>

<p>V realitě to pak vypadá např takto:</p>

<p class="mono">example.com/?page=index.php</p>
<p class="mono">example.com/index.php?avatarimage=default.png</p>

<a href=".?p=lfi&sub=pathtrav.php">Přejít na Path traversal</a>
