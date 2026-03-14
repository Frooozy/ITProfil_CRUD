CRUD + RPG pattern

1. Práce se soubory a JSON
file_get_contents($file): Slouží k načtení celého obsahu souboru (v našem případě profile.json) do jednoho textového řetězce.

file_put_contents($file, $data): Zapíše data do souboru. Používáme ji k uložení aktualizovaného seznamu zájmů zpět do JSONu.

json_decode($json, true): Převádí textový řetězec ve formátu JSON na PHP pole, se kterým můžeme v kódu dále pracovat. Parametr true zajistí, že se vytvoří asociativní pole.

json_encode($data): Převádí PHP pole zpět na textový formát JSON, aby bylo možné ho uložit do souboru.

2. Řízení toku a PRG pattern
session_start(): Aktivuje práci se "sessions" (relacemi). To nám umožňuje uchovat zprávy (např. o úspěšném uložení) i po přesměrování stránky.

header("Location: index.php"): Odešle prohlížeči instrukci k přesměrování. Toto je základ PRG patternu, který zabrání opětovnému odeslání formuláře při obnově stránky (F5).

exit / die(): Okamžitě ukončí vykonávání skriptu. Voláme ho hned po header, aby PHP nepokračovalo v generování zbytku stránky, když už uživatele přesměrováváme jinam.

3. Bezpečnost a manipulace s daty
htmlspecialchars($string): Klíčová funkce pro bezpečnost. Převádí speciální znaky (jako < nebo >) na HTML entity. Tím brání útoku typu XSS (vložení škodlivého skriptu uživatelem).

trim($string): Odstraní bílé znaky (mezery, entery) ze začátku a konce textu. Používáme k validaci, aby uživatel neuložil zájem tvořený jen mezerami.

mb_strtolower($string): Převede text na malá písmena (s podporou češtiny). Používáme ji pro kontrolu duplicity, aby "Sport" a "sport" byly brány jako stejná položka.

array_map(): Umožňuje aplikovat funkci (např. strtolower) na všechny prvky v poli najednou.

4. Ostatní
unset($_SESSION['msg']): Smaže konkrétní proměnnou. Používáme ji k tomu, aby se potvrzovací hláška zobrazila jen jednou a po refreshnutí stránky zmizela.

isset(): Ověřuje, zda proměnná existuje a není prázdná. Často používáno u kontroly, zda byl odeslán konkrétní klíč v poli $_POST.