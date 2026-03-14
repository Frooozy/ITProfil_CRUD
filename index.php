<?php
session_start(); // Nutné pro přenos hlášek přes $_SESSION [cite: 43, 50, 67]

$file = 'profile.json';

// --- NAČTENÍ DAT ---
$data = ['interests' => [], 'projects' => [], 'skills' => []];
if (file_exists($file)) {
    $json_data = file_get_contents($file); // [cite: 53]
    $data = array_merge($data, json_decode($json_data, true) ?? []); // [cite: 55]
}
    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $category = $_POST['category'] ?? ''; // Určuje, zda měníme zájmy, projekty nebo dovednosti
    
    if (isset($data[$category])) {
        // 1. PŘIDÁNÍ [cite: 4, 14]
        if ($action === 'add') {
            $value = trim($_POST['value'] ?? ''); // [cite: 60]
            if (empty($value)) {
                $_SESSION['msg'] = "Pole nesmí být prázdné."; // [cite: 17, 40]
            } else {
                $lower_items = array_map('mb_strtolower', $data[$category]);
                if (in_array(mb_strtolower($value), $lower_items)) { // [cite: 18, 19, 63]
                    $_SESSION['msg'] = "Tato položka už existuje."; // [cite: 37]
                } else {
                    $data[$category][] = $value;
                    $_SESSION['msg'] = "Položka byla úspěšně přidána."; // [cite: 36]
                }
            }
        }

        // 2. MAZÁNÍ [cite: 6, 20]
        if ($action === 'delete') {
            $index = $_POST['index'] ?? null;
            if (isset($data[$category][$index])) {
                unset($data[$category][$index]); // [cite: 23, 64]
                $data[$category] = array_values($data[$category]); // Reindexace [cite: 65]
                $_SESSION['msg'] = "Položka byla odstraněna."; // [cite: 38]
            }
        }

        // 3. EDITACE [cite: 5, 25]
        if ($action === 'edit') {
            $index = $_POST['index'] ?? null;
            $new_value = trim($_POST['updated_value'] ?? '');
            if (empty($new_value)) {
                $_SESSION['msg'] = "Pole nesmí být prázdné."; // [cite: 31, 40]
            } else {
                // Kontrola duplicity (mimo editovaný prvek)
                $temp = $data[$category];
                unset($temp[$index]);
                if (in_array(mb_strtolower($new_value), array_map('mb_strtolower', $temp))) {
                    $_SESSION['msg'] = "Tato položka už existuje."; // [cite: 32]
                } else {
                    $data[$category][$index] = $new_value;
                    $_SESSION['msg'] = "Položka byla upravena."; // [cite: 39]
                }
            }
        }

        // Uložení změn do souboru [cite: 24, 54, 56]
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    // PRG Pattern - Redirect [cite: 8, 47, 49, 70]
    header("Location: index.php");
    exit;
}   
?>