<h2>XSS Type 1</h2>


<figure>
  <img src="img/XSStwitchchat.png" alt="XSS zneužito v twitch chatu">
  <figcaption>Payload v twitch chatu je interpretován aplikací pro zobrazování chatu</figcaption>
</figure>

<p>XSS (Cross Site Scripting) je zranitelnost souvisejicí s vkládáním uživatelského vstupu do obsahu stránky (zprávy, příspěvky, komentáře, apod.). Opět se kloubí se slabinou <a target="_blank" href="https://cwe.mitre.org/data/definitions/20.html">CWE-20</a>.</p>

<p>Když nás vývojář webu nechá volně psát do zdrojového kódu stránky, může se stát, že náš vstup do zdrojového kódu vloží neošetřený. Pak můžeme zkusit vkládat nejen text, ale i HTML tagy jako například <span class="mono">&lt;b&gt;</span>. Tyto tagy se pak tváří jako součást stránky a jsou interpretovány.</p>

<p>Proč se ale držet u grafických srand jako <span class="mono">&lt;b&gt;</span>? My můžeme zvolit například tag <span class="mono">&lt;script&gt;</span>. Díky tomu můžeme do stránky vkládat libovolný javascript kód který se provede v prohlížeči oběti. Jako test se běžně používá text</p>

<p class="mono">&lt;script&gt;alert('XSS')&lt;/script&gt;</p>

<p>Reálný útočník by ale mohl pomocí <span class="mono">document.cookies</span> přečíst autentizační cookies a pomocí <span class="mono">fetch</span> si je odeslat na svůj server.</p>

<h4>Exploit</h4>

<div class="subpage-container">
  <form action="" method="get">
    <input type="hidden" name="p" value="xss1">
    <input type="text" name="search" placeholder="Text to search">
    <input type="submit">
  </form>
  <?php
    /**
     * Easy logic for Reflected XSS (No input sanitisation)
     */
    if (isset($_GET["search"])) {
      $search = $_GET["search"];
      echo "Výsledky hledání pro \"$search\"";
    }
  ?>
</div>

<p>Toto okénko slouží pro hledání v rámci naší aplikace (jen ilustračně), prozatím prostě vypíše, co uživatel hledá. Jen zkuste zadat např. "kalhoty".</p>

<p>Když ale naši situaci trochu zanalyzujeme, zjistíme že nám právě vývojář dovolil zapsat do zdrojového kódu stránky. No to by bylo, aby to náhodou neošetřil!</p>

<p>Zkuste nejprve zadat</p>

<p class="mono">&lt;b&gt;Nejaky text&lt;/b&gt;</p>

<p>Vidíte? Text se interpretoval jako HTML tagy. Teď můžeme zkusit spustit libovolný javascript (ukázkově třeba právě funkci alert).</p>

<h4>Type 1 - Reflected XSS</h4>

<p><b>To ale není vše!</b> Sice jsme přišli na zranitelnost, ale momentálně ji dokážeme využít jen v našem vlastním prohlížeči. Doteď jsme se bavili jen o XSS obecně, co to znamené ten Type 1?</p>

<p>Reflected XSS je o tom, že náš payload je přenášen prostřednictvím query parametrů (což je pro náš případ pravda - parametr search). My můžeme vzít celé URL i s nějakým škodlivým payloadem, poslat ho někomu se zprávou "Podívej se, myslíš že by byl tenhle dárek pro tebe?", a v moment kdy odkaz rozkliknou se payload z parametru přesune na stránku a spustí u <b>nich</b> v prohlížeči. Zkuste schválně URL po exploitaci poslat spolusedícímu/kamarádovi přes MS Teams, ať ho otevře.</p>

<p>Poznámka: Pokud jste si v url všimli podivných znaků jako %28, %27, %2F, apod. tyto jsou jen URL-Encoded varianty z inputu (V url nejdou volně přenášet všechny znaky). Zkuste si v <a target="_blank" href="https://gchq.github.io/CyberChef/">cyberchefu</a> najít URL Decode a dekódujte parametr search z URL.</p>

<p>Type 1 XSS lze tedy mířit cíleně na konkrétní 1 osobu. Na druhou stranu ho nelze šířit globálně. Pojďme se tedy podívat na Type 2 XSS</p>
