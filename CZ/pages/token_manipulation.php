<h2>Token manipulation</h2>

<p>Předtím, než se dostaneme k jádru věci si pojďme připomenout jednu skutečnost. Když se přihlásíte do jakékoli aplikace (i když jste se přihlásili do této aplikace), aplikace si nějak spravuje váš stav. Jak to dělá? Většinou pomocí cookies.</p>

<p>Tak se pojďme podívat do inspectora do záložky Application a zvolme cookies, pakliže jste se v předešlém úkolu přihlásili, měli byste vidět <span class="mono">AUTH</span> cookie i její hodnotu.</p>

<p>Hodnota samotné cookie je většinou takzvaný autentizační <b>Token</b>. No a token manipulation bude o nějaké manipulaci a podvrhování tohoto tokenu. Způsob samozřejmě závisí na typu tokenu. Ten může být např.</p>

<ul>
  <li><p>Basic - V podstatě jen jméno a heslo, které se prostě přenáší (Mohou být zakódovány např. Base64)</p></li>
  <li><p>Session - Tento token je většinou čistě náhodný a má smysl jen pro daný server (Moc se s ním manipulovat nedá)</p></li>
  <li><p>JSON - Tento typ tokenu obsahuje zakódovaná data, pokud není nějak bezpečnostně ošetřen, je primární kandidát na manipulaci</p></li>
</ul>

<h4>Exploitace</h4>

<p>Jako první si pojďme ujasnit, co si od exploitace slibujeme. Odnavigujte opět na stránku komentářů. Po našich hrátkách s Type 2 XSS je zde celkem plno že? Naštěstí nám vývojář dal možnost všechny příspěvky odstranit. Klikněte na tlačítko.</p>

<p>Ajajaj. Musíme být admin, abychom tuto funkci mohli použít. Aplikace bude rozeznávat naší identitu nějak dle našeho tokenu. Pojďme ho tedy prozkoumat.</p>

<p>Zkopírujte si hodnotu své <span class="mono">AUTH</span> cookie. Vypadá zakódovaná base64, tak ji dekódujme přes <a target="_blank" href="https://gchq.github.io/CyberChef/">cyberchefa</a> a uvidíme jestli je naše cookie jen náhodný token nebo jestli v sobě nese nějaké informace.</p>

<p>Po dekódování vidíme JSON typ tokenu s dvěmi hodnotami <span class="mono">username</span> a <span class="mono">role</span>. Vzhledem k tomu že token nepoužívá žádné zjevné ochrany, ho možná můžeme prostě jen změnit, znovu zakódovat, vložit do cookie a možná to bude fungovat, co?</p>

<p>Krok po kroku to pak může vypadat takto</p>

<ol>
  <li><p>Extrahujte cookie z Application záložky inspectoru</p></li>
  <li><p>Dekódujte z base64</p></li>
  <li><p>Vezměte dekódovaný token a pozměňte hodnotu role z user na admin</p></li>
  <li><p>Token znovu zakódujte</p></li>
  <li><p>Token vložte v inspectoru do <span class="mono">AUTH</span> cookie</p></li>
  <li><p>Fungovalo to? Zkuste odebrat všechny komentáře teď</p></li>
</ol>

<h4>JWT tokeny</h4>

<p>V reálu se dnes samotné JSON tokeny nepoužívají, používají se JWT tokeny. Funkci plní stejnou (nesou v sobě autentizační data jako userID, role, apod.), ale jsou k tomu navíc podepsané tajným klíčem serveru (kontrolní součet). Když něco v tokenu změníme, serveru pak nevyjde správný kontrolní součet a pozná, že tento token je zmanipulovaný. Abychom mohli manipulovat token potřebovali bychom tajný klíč serveru.</p>

<p>Prakticky to dělat nebudeme, ale jeden takový způsob je například pomocí LFI. Buďto je totiž klíč na serveru v nějakém souboru (který dokážeme přečíst s LFI) anebo je uložen jako proměnná prostředí (ale i tehdy je uložen na linuxu v souboru <span class="mono">/proc/self/environ</span>). Díky tomuto pak můžeme z jednoduchého "můžu jen číst nějaké soubory" udělat "mám práva admina ve webové aplikaci".</p>
