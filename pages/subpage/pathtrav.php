<h3>Path traversal</h3>

<p>Path traversal souvisí s faktem, že každá webová aplikace běží v nějaké složce (případně jejích podsložkách).</p>
<ul>
  <li><p>Na Linuxu např. <span class="mono">/www</span></p></li>
  <li><p>Na Windows např. <span class="mono">C:\xampp\htdocs</span></p></li>
</ul>
<p>Pakliže máme k dispozici LFI, můžeme zkusit, zda nelze uniknout z naší složky za pomocí <span class="mono">../</span></p>
<p>V podstatě tyto znaky prohlásíme za část cesty a OS serveru je bude interpretovat jako pohyb o složku výše.</p>

<p class="mono">example.com/?page=../../../../etc/passwd</p>

<p>Na serveru:</p>

<p class="mono">/www/pages/ + ../../../../etc/passwd</p>
<p class="mono">/www/pages/../../../../etc/passwd</p>
<p class="mono">/etc/passwd</p>

<p>Úspěšně jsme unikli z adresáře /www/pages a můžeme se přesunout na jakoukoli cestu na systému</p>

<a href=".?p=lfi&sub=lfi.php">Přejít na LFI</a>
