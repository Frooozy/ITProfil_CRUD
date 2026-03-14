<?php
session_start(); // Nutné pro přenos hlášek přes $_SESSION [cite: 43, 50, 67]

$file = 'profile.json';

// --- NAČTENÍ DAT ---
$data = ['interests' => [], 'projects' => [], 'skills' => []];
if (file_exists($file)) {
    $json_data = file_get_contents($file); // [cite: 53]
    $data = array_merge($data, json_decode($json_data, true) ?? []); // [cite: 55]
}


?>