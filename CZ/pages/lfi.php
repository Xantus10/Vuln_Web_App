<h2>LFI / Path traversal</h2>

<p>LFI (Local File Inclusion) a Path traversal jsou dvě, spolu často související zranitelnosti. Nejprve si je pojďme představit každou zvlášť.</p>

<div class="subpage-container">
  <?php
  /**
   * If there isn't a subpage specified, specify it as lfi.php
   */
  if (!isset($_GET["sub"])) {
    $query = $_GET;
    $query["sub"] = "lfi.php";
    $queryString = http_build_query($query);
    $path = strtok($_SERVER['REQUEST_URI'],"?");

    header("Location: {$path}?{$queryString}");
    exit;
  }
  // Include the subpage
  include_once("subpage/" . $_GET["sub"]);
  ?>
</div>

<img src="img/pathtrav.png" alt="Popis path traversal skákání">

<h4>Exploitace</h4>

<p>Teď tedy na praktické zneužití. Pokud jste se po (nebo při) pročtení textů o LFI a Path traversalu podívali do URL a všimli si přesně popisované situace, můžete si udělat malé bezvýznamné plus. V URL se totiž mimo jiné nachází parametr sub. Tento parametr ovlivňuje zobrazenou podstránku (soubor lfi.php nebo pathtrav.php). Co když zkusíte svoje štěstí, zda vstup není zranitelný?</p>

<ul>
  <li><p>Poznámka 1: Dejte dostatek "../" systém vám neukousne hlavu když jich bude moc, ale nebude to fungovat když jich bude málo</p></li>
  <li><p>Poznámka 2: Na linuxu můžete zkusit soubor /etc/passwd, na windows zase Windows/win.ini</p></li>
</ul>

<h4>Bonus</h4>

<p>Teď pár zajímavostí:</p>

<p>Když vložíte na stránku špatný vstup (neexistující soubor), vypíše se vám kus kódu aplikace a dokonce i absolutní cesta k souboru. Díky tomuto můžeme identifikovat OS (Linux/Windows), použitý webový server (Apache/Nginx/...) a kontext naší zranitelné funkce (možná uvidíme jaké ochrany funkce zkouší). Toto je samozřejmě zase špatně a je to slabina <a target="_blank" href="https://cwe.mitre.org/data/definitions/209.html">CWE-209</a>. Zkuste takový error vyvolat a vypozorujte z něj, co se dá.</p>

<p>Teď slovíčko k ochranám. Nejjednodušší je prostě validovat daný vstup jak nejlépe to jde. Ovšem ani tehdy nemusí být vše stoprocentní. Například řekněme, že máme ochranu, která nám maže řetězce "../".</p>

<p class="mono">/www/pages/../../../../etc/passwd</p>

<p>Po odstranění</p>

<p class="mono">/www/pages/etc/passwd</p>

<p>Právě jsme přišli o náš path traversal že? Ne tak úplně, útočník totiž může udělat například následující.</p>

<p class="mono">/www/pages/....//....//....//....//etc/passwd</p>

<p>Jak bude mazání "../" vypadat teď?</p>

<p class="mono">/www/pages/..<span class="red">../</span>/..<span class="red">../</span>/..<span class="red">../</span>/..<span class="red">../</span>/etc/passwd</p>

<p>Po vymazání naše cesta tedy vypadá takto.</p>

<p class="mono">/www/pages/../../../../etc/passwd</p>

<p>Což je vstup, který uskuteční path traversal.</p>
