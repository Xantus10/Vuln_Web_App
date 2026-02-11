<h2>CSRF</h2>

<p>Cross Site Request Forgery je útok, kdy akcí na jedné stránce můžete nevědomky udělat jinou akci na úplně jiné stránce. Jak? Vysvětlíme.</p>

<p>Budeme používat příklad banky. Když se do webové aplikace banky přihlásíte, nastaví se vám autentizační cookie pro doménu <span class="mono">banka.cz</span>. Když pak chcete převést peníze, kliknete na stránce a pošlete request na <span class="mono">banka.cz/prevod</span>. Prohlížeč se podívá, jestli pro tuto doménu má uložené nějaké cookies a zjistí, že ano (vaši auth cookie), a tak ji pošle taky. Díky tomu banka ví, že to vy chcete poslat peníze.</p>

<p>A teď CSRF. Máme naši stránku, na které je obrázek kočičky s nápisem klikni mě. Ve skutečnosti kliknutí na tento obrázek pošle request na <span class="mono">banka.cz/prevod</span>. Ale to je v pohodě ne? Přeci jsme na jiné stránce a zde jsme se do banky nehlásili. Ale prohlížeč prostě uvidí "aha jde nějaký request, mám k této doméně nějaké cookies?", a pokud ano, tak je prostě pošle (nezáleží na tom, že nejsme na správné stránce).</p>

<img src="img/csrf.png" alt="CSRF útok graficky">

<p>Jinými slovy jedním kliknutím (i když by stačilo JEN stránku NAČÍST) můžete nevědomky dělat akce v jiných webových aplikací, do kterých jste přihlášeni. Samozřejmě pokud stránka nevyužívá nějaké ochrany proti CSRF.</p>

<h4>Příprava na exploitaci</h4>

<p>I když bych rád, aby se veškerá funkcionalita této aplikace vešla prostě sem, tak jak bylo řečeno, na CSRF jsou potřeba stránky dvě. Jedna zranitelná a jedna útočníkova. Vzhledem k tomu že nemáme útočníkovu aplikaci jsem připravil minimalistickou verzi, kterou je třeba stáhnout a spustit (stačí na nějakém jednom zařízení).</p>

<ol>
  <li><p>Stáhněte si python</p></li>
  <li><p>Doinstalujte balíčky s <span class="mono">pip install flask pyopenssl</span></p></li>
  <li><p>Stáhněte aplikaci <a target="_blank" href="zip.php">ZDE</a> a rozzipujte ji</p></li>
  <li><p>Do ip.txt napište ip adresu na které běží tato aplikace (případně i path jako např. <span class="mono">1.2.3.4/vuln</span>)</p></li>
  <li><p>Spusťte <span class="mono">run.py</span></p></li>
</ol>

<h4>Exploitace</h4>

<p>Exploitace se zde děje prakticky automaticky (většinu zajišťuje stránka útočníka). My se i tak můžeme hodně přiučit.</p>

<p>Pokud nejste přihlášeni, tak se přihlašte. Pak odnavigujte na útočníkovu stránku (možná jste na ni prostě narazili, možná někdo udělal komentář s titulkem "Podívejte se na tyhle koťátka" a dal tam link). Na stránce si nejprve otevřete inspector záložku Network (později se k ní vrátíme). Pak klikněte na obrázek koťátka (stačí jednou, ale můžete kolikrát chcete).</p>

<p>Teď se podívejte v této aplikaci do sekce Comment a žasněte.</p>

<h4>Analýza útočníkovy stránky</h4>

<p>Když se podíváte zpět na útočníkovu stránku, uvidíte v Network, že jste posílali requesty. Když si na nějaký kliknete a sjedete do sekce <span class="mono">Request Headers</span>, uvidíte tam header <span class="mono">Cookie</span>. Jakou má hodnotu?</p>

<p>Teď se podívejte do zdrojového kódu stránky a zaměřte se na část s koťátkem. Uvidíte, že se za koťátkem skrývá celý formulář.</p>

<h4>Bonus iframe</h4>

<p>Když za normálních okolností odešlete formulář, měl by vás přesměrovat na cílovou stránku. U nás se to ale neděje. Proč? Trik je v atributu formuláře <span class="mono">target</span>. Ten ovlivňuje, kde se odpověď na formulář zobrazí. Standartně je to aktuální okno, ale my jsme formulář odkázali na <span class="mono">iframe</span> tag na naší stránce, který má <span class="mono">display: none</span>.</p>

<h4>Ochrany</h4>

<p>Ochran proti CSRF je vícero (doporučuji se vrátit na <a href=".?p=home">domovskou stránku</a> a podívat se na projekt OWASP cheatsheets), nicméně se zde můžeme zmínit o dnes nejběžnější variantě.</p>

<p>Tou je atribut <span class="mono">SameSite</span> pro cookies. Ten totiž ovlivňuje, kdy jsou cookies poslány.</p>

<table>
  <tr>
    <th>SameSite</th>
    <th>Co dělá</th>
  </tr>
  <tr>
    <td>None</td>
    <td>Nic nehlídá, dnes je povolen už jen v kombinaci s atributem <a target="_blank" href="https://owasp.org/www-community/controls/SecureCookieAttribute">Secure</a></td>
  </tr>
  <tr>
    <td>Lax</td>
    <td>Hlídá, aby byla cookie zaslána jen při GET requestech z jiných stránek (Nebude fungovat jakákoli jiná metoda), blokuje většinu CSRF</td>
  </tr>
  <tr>
    <td>Strict</td>
    <td>Blokuje i GET requesty z jiných stránek; <b>POZOR!</b> Stránka jsou jen dvě top level domény (app.example.com a bank.example.com jsou <b>stejná</b> stránka)</td>
  </tr>
</table>
