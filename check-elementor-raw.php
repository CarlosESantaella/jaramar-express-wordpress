<?php
require_once(__DIR__ . '/wp-load.php');

$data = get_post_meta(13, '_elementor_data', true);

// Buscar https://jaramar.test
if (strpos($data, 'https:\/\/jaramar.test') !== false) {
    echo "✓ Encontrado: https:\/\/jaramar.test (escapado)\n";
} else {
    echo "✗ NO encontrado: https:\/\/jaramar.test (escapado)\n";
}

if (strpos($data, 'https://jaramar.test') !== false) {
    echo "✓ Encontrado: https://jaramar.test (sin escapar)\n";
} else {
    echo "✗ NO encontrado: https://jaramar.test (sin escapar)\n";
}

// Mostrar primeros 1000 caracteres
echo "\nPrimeros 1000 caracteres:\n";
echo substr($data, 0, 1000) . "\n";
?>
