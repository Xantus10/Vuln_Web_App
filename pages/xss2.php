<h2>XSS Type 2</h2>

<p>Na stránce o XSS Type 1 jsme představili samotný koncept XSS (Takže se tam vraťte pakliže tomu nerozumíte). Také jsme probrali co to znamená Type 1 (Reflected XSS). Teď následuje Type 2</p>

<h4>Type 2 - Stored XSS</h4>

<p>U Type 1 XSS jsme mohli vkládat obsah jen dočasně a de facto jen pro jednoho klienta (toho který uskutečnil dotaz). Type 2 XSS je Stored, protože payload je uložen (stored) na serveru. Typickým příkladem stored XSS je tedy jakýkoli text, který v aplikaci přetrvává, např.</p>

<ul>
  <li><p>Komentáře</p></li>
  <li><p>Příspěvky</p></li>
  <li><p>I samotné uživatelské jméno, pakliže se někde zobrazuje</p></li>
</ul>

<p>Stored XSS si cíl nevybírá, spustí se úplně každému, kdo přistoupí na stránku (i samotnému útočníkovi). Někdy se o Type 2 XSS říká, že je to útok hack-and-forget - Ulož payload na stránku a pak už na stránku nikdy nepřistupuj, protože by se ti spustil vlastní škodlivý script.</p>

<h4>Exploitace</h4>

<p>Teď začneme využívat další funkce této aplikace, v navigaci je záložka Login, zde se pouze přihlašte. (Je úplně jedno s jakým uživatelským jménem a přihlašovací logiku zatím netřeba zkoumat)</p>

<p>Po přihlášení máte možnost psát komentáře na stránce Comment. Pro začátek zde můžete vyzkoušet nějaký grafický payload (tagy b, i, h1, apod.). <b>Payload prozatím vkládejte do políčka titulku.</b></p>

<p>Když příspěvek vytvoříte, uvidíte, že je vytvořený na serveru (přetrvává v aplikaci i když ji úplně zavřete a znovu otevřete). Teď můžete zkusit nějaký javascript payload, <b>ale pozor, nedoporučuji to dělat s invazivní funkcí jako alert(), spíše využijte například funkce console.log()!</b> (Výstup z log() je vidět v HTML inspectoru v záložce console)</p>

<h4>Obcházení textarea</h4>

<p>Do titulku nám XSS jde vložit bez problémů. Zkuste teď to samé vložit do obsahu komentáře. Funguje to?</p>

<p>Troufnu si hádat, že ne. Ať už zkoušíte reálný javascript nebo nějaký grafický payload, nic nefunguje. Cokoli co je uvnitř textarea se prostě neinterpretuje.</p>

<img src="img/XSS2payloadhtml.png" alt="Ukazka HTML při escape z textarea">

<p>Ale nezoufejme, my máme úplnou kontrolu nad zdrojovým kódem stránky. Stejně jako můžeme začít nový tag, můžeme taky skončit nějaký předešlý tag (např. i textarea)</p>

<p>Jinými slovy na začátek svého payloadu vložte ukončující tag textarea.</p>

<p class="mono">&lt;/textarea&gt;Zde zbytek</p>
