<h2>Další útoky</h2>

<p>Na této stránce jsme probrali spousty a spousty útoků. Mnohé tu ale pořád chybí. Není to ,protože by se na ně zapomnělo, ale protože jsou tyto útoky až moc nebezpečné (ano, právě tyto útoky v reálu hledáte nejraději). Většinou to znamená, že útok vede k RCE (Remote Code Execution), neboli spuštění útočníkova kódu na remote zařízení.</p>

<h2>SQL injection</h2>

<p>Injection typy útoků jsou útoky, kdy nám vývojář dovolil psát někam do kódu, ale neošetřil nám vstup (XSS je injection útok). SQL je jazyk pro interakci s databází. SQL injection se stane když nás vývojář nechá psát do dotazu do databáze a my tak do jisté míry můžeme s dotazem manipulovat. Co můžeme dělat?</p>

<ul>
  <li><p>Zjistit strukturu databáze (jaké má databáze, tabulky, sloupce)</p></li>
  <li><p>Dostat libovolná data z databáze (např. uživatelská jména a hesla)</p></li>
  <li><p>Při dobrých podmínkách data modifikovat, či mazat</p></li>
  <li><p>A finálně při miskonfiguraci může SQLi vést k RCE</p></li>
</ul>

<p>Prakticky to pak může vypadat např. takto</p>

<p class="mono">SELECT * FROM users WHERE username='<span class="red">admin</span>' AND password='admin'</p>

<p>Zde je příklad uživatele, co zadal uživatelské jméno admin a heslo také admin. Tento dotaz vrátí odpověď jen tehdy, když existuje uživatel s daným jménem a heslem (tím pádem tímto dotazem realizujeme ověřování hesla). Povšiměte si také, že jsou naše řetězce ohraničeny apostrofy.</p>

<p class="mono">SELECT * FROM users WHERE username='<span class="red">admin' Tady muzu psat </span>' AND password='admin'</p>

<p>Tady jsme pomocí vloženého a neošetřeného apostrofu unikli z řetězce a zbytek naší zprávy je teď interpretován jako SQL.</p>

<p class="mono">SELECT * FROM users WHERE username='<span class="red">admin' -- </span>' AND password='admin'</p>

<p>A my jsme se rozhodli pro nejprimitivnější variantu SQLi. Znaky <span class="mono red">--</span> totiž odkomentují zbytek příkazu SQL. Jinými slovy se autentizujeme jako uživatel admin, ale bez kontroly hesla (protože jsme ji odkomentovali).</p>

<h2>File upload</h2>

<p>Často se v aplikaci setkáte s možností nahrávání vlastních souborů, ovšem to může být v určitých ohledech velký problém. Hlavně je to problém pro systémy využívající PHP nebo ASP .NET</p>

<p>Jak se dá nahrání souboru zneužít? Nahrajeme takzvaný <b>Web Shell</b>. V podstatě krátký skript, který při zavolání udělá následující:</p>

<ol>
  <li><p>Vezmi příkaz z URL paramterů</p></li>
  <li><p>Příkaz vykonej</p></li>
  <li><p>Výstup příkazu zobraz na stránce</p></li>
</ol>

<p>Primitivní PHP Web shell vypadá např. takto:</p>

<p class="mono">&lt;?php system($_GET["cmd"]) ?&gt;</p>

<p>Pokud náš cílový webserver PHP soubory umí interpretovat a my si vyžádáme náš shell s parametrem <span class="mono">cmd=whoami</span>. Na výstup dostaneme uživatelské jméno, pod kterým běží aplikace. Dosáhli jsme RCE.</p>


