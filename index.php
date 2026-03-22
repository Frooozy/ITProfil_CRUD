<?php
session_start(); // Spustí session – umožňuje ukládat zprávy mezi načteními stránky

$file = 'profile.json'; // Název souboru, kde jsou uložená data (JSON)

// --- NAČTENÍ DAT ---

// Vytvoří výchozí strukturu dat (pokud soubor ještě neexistuje)
$data = ['interests' => [], 'projects' => [], 'skills' => []];

// Kontrola, jestli soubor existuje
if (file_exists($file)) {

    $json_data = file_get_contents($file); // Načte obsah JSON souboru do proměnné

    // Převede JSON na PHP pole a spojí ho s výchozími daty
    $data = array_merge($data, json_decode($json_data, true) ?? []);
}

// Kontrola, jestli byl odeslán formulář metodou POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? ''; // Zjistí, jakou akci chceme provést (add, delete, edit)

    $category = $_POST['category'] ?? ''; // Kategorie dat (interests, projects nebo skills)

    // Kontrola, jestli daná kategorie existuje v poli
    if (isset($data[$category])) {

        // 1️⃣ PŘIDÁNÍ POLOŽKY
        if ($action === 'add') {

            $value = trim($_POST['value'] ?? ''); // Načte hodnotu z formuláře a odstraní mezery

            // Kontrola, jestli pole není prázdné
            if (empty($value)) {

                $_SESSION['msg'] = "Pole nesmí být prázdné."; // Uloží chybovou zprávu

            } else {

                // Převede všechny položky na malá písmena (pro kontrolu duplicity)
                $lower_items = array_map('mb_strtolower', $data[$category]);

                // Kontrola, jestli už položka existuje
                if (in_array(mb_strtolower($value), $lower_items)) {

                    $_SESSION['msg'] = "Tato položka už existuje."; // Zpráva o duplicitě

                } else {

                    $data[$category][] = $value; // Přidá novou položku do pole

                    $_SESSION['msg'] = "Položka byla úspěšně přidána."; // Potvrzení přidání
                }
            }
        }

        // 2️⃣ MAZÁNÍ POLOŽKY
        if ($action === 'delete') {

            $index = $_POST['index'] ?? null; // Získá index položky v poli

            // Kontrola, jestli položka existuje
            if (isset($data[$category][$index])) {

                unset($data[$category][$index]); // Odstraní položku z pole

                $data[$category] = array_values($data[$category]); // Přeindexuje pole

                $_SESSION['msg'] = "Položka byla odstraněna."; // Zpráva o odstranění
            }
        }

        // 3️⃣ EDITACE POLOŽKY
        if ($action === 'edit') {

            $index = $_POST['index'] ?? null; // Index upravované položky

            $new_value = trim($_POST['updated_value'] ?? ''); // Nová hodnota z formuláře

            // Kontrola prázdného pole
            if (empty($new_value)) {

                $_SESSION['msg'] = "Pole nesmí být prázdné.";

            } else {

                // Vytvoří kopii pole pro kontrolu duplicity
                $temp = $data[$category];

                unset($temp[$index]); // Odebere aktuální položku z kontroly

                // Kontrola duplicity
                if (in_array(mb_strtolower($new_value), array_map('mb_strtolower', $temp))) {

                    $_SESSION['msg'] = "Tato položka už existuje.";

                } else {

                    $data[$category][$index] = $new_value; // Aktualizuje hodnotu

                    $_SESSION['msg'] = "Položka byla upravena."; // Zpráva o úpravě
                }
            }
        }

        // Uloží změněná data zpět do JSON souboru
        file_put_contents(
            $file,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    // PRG Pattern – přesměruje stránku po odeslání formuláře
    // zabrání opětovnému odeslání formuláře při refresh
    header("Location: index.php");

    exit; // Ukončí skript
}

// Načte zprávu ze session pro zobrazení na stránce
$alert = $_SESSION['msg'] ?? null;

// Po načtení zprávy ji odstraní ze session
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>IT Profil 5.0</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>

    <h1>Můj IT Profil</h1>

    <?php if ($alert): ?>
        <div class="alert"><?php echo htmlspecialchars($alert); ?></div> <?php endif; ?>

    <div class="dashboard">
        <?php 
        $titles = ['interests' => 'Zájmy', 'projects' => 'Projekty', 'skills' => 'Dovednosti'];
        foreach ($titles as $cat => $label): 
        ?>
            <section>
                <h2><?php echo $label; ?></h2>
                
                <form method="post">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="category" value="<?php echo $cat; ?>">
                    <input type="text" name="value" placeholder="Přidat..." required>
                    <button type="submit">Přidat</button>
                </form>

                <ul>
                    <?php foreach ($data[$cat] as $idx => $item): ?>
                        <li>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] === "$cat-$idx"): ?>
                                <form method="post" class="edit-form">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="category" value="<?php echo $cat; ?>">
                                    <input type="hidden" name="index" value="<?php echo $idx; ?>">
                                    <input type="text" name="updated_value" value="<?php echo htmlspecialchars($item); ?>">
                                    <button type="submit">Uložit</button>
                                </form>
                            <?php else: ?>
                                <span><?php echo htmlspecialchars($item); ?></span> <div class="actions">
                                    <a href="?edit=<?php echo "$cat-$idx"; ?>">Upravit</a> <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="category" value="<?php echo $cat; ?>">
                                        <input type="hidden" name="index" value="<?php echo $idx; ?>">
                                        <button type="submit" class="btn-delete">Smazat</button> </form>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>
    </div>

</body>
</html>