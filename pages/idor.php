<h2>IDOR</h2>

<p>IDOR (Insecure Direct Object Reference). Nejprve si řekněme, že většina informací se na webovém serveru referencuje pomocí nějakých identifikátorů (většinou číselných). Když tedy chceme informace (např. stav peněz, počet účtů) o účtu uživatele, nebudeme ho referencovat pomocí jeho uživatelského jména, ale pomocí jemu přiděleného čísla.</p>

<p>Jak ale stránka tyto informace pro náš účet získá? Pošle si request. Ten pak může vypadat nějak takto.</p>

<p class="mono">example.com/user?id=35</p>

<p>Teď víme, že naše id je 35 a že se na endpoint /user posílá požadavek, když chceme uživatelská data. Nezajímá vás ale co by se stalo kdybychom zkusili na tento endpoint udělat dotaz s id=34 nebo třeba id=36?</p>

<p>Správně by se nemělo stát nic, protože na tyto informace nemáme právo, ale pokud se vývojář s takovými ochranami neobtěžoval, mohli bychom se dostat k informacím ostatních uživatelů. To je IDOR, použití nezabezpečených (<b>Insecure</b>) přímých odkazů (<b>Direct Reference</b>) na objekty (<b>Object</b>).</p>

<p>Nakonec se hodí říci, že IDOR je umožněn slabinami <a target="_blank" href="https://cwe.mitre.org/data/definitions/340.html">CWE-340</a> a <a target="_blank" href="https://cwe.mitre.org/data/definitions/425.html">CWE-425</a></p>

<h4>Exploitace</h4>

<p>Zneužití této zranitelnosti není nijak těžké, stačí jen změnit nějaké id když ho uvidíte a zkusit své štěstí. V naší aplikaci pro tento účel existuje stránka Announcements.</p>

<p>Tato stránka dnes slouží pouze pro automatizované zprávy pro nově příchozí uživatele. Ale byly doby (hodně dávno), kdy se skrze tyto zprávy posílala zajímavá data. Dokážete nějaká zajímavá data najít? (Třeba zabezpečovací kód ke vstupním dveřím?)</p>
