<?php
/**
 * Corregir URLs escapadas de HTTPS a HTTP en JSON
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Corrección de URLs HTTPS a HTTP (JSON escapado) ===\n\n";

global $wpdb;

$old_url = 'https:\/\/jaramar.test';
$new_url = 'http:\/\/jaramar.test';

echo "Buscando: {$old_url}\n";
echo "Reemplazar con: {$new_url}\n\n";

// 1. Actualizar wp_postmeta (donde está _elementor_data)
echo "1. Actualizando wp_postmeta...\n";
$result_postmeta = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s)",
        $old_url,
        $new_url
    )
);
echo "   Filas actualizadas: {$result_postmeta}\n";

// 2. Actualizar wp_posts (por si acaso)
echo "\n2. Actualizando wp_posts...\n";
$result_posts = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
        $old_url,
        $new_url
    )
);
echo "   Filas actualizadas: {$result_posts}\n";

// 3. Actualizar wp_options
echo "\n3. Actualizando wp_options...\n";
$result_options = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, %s, %s)",
        $old_url,
        $new_url
    )
);
echo "   Filas actualizadas: {$result_options}\n";

// 4. También actualizar GUIDs normales
echo "\n4. Actualizando GUIDs...\n";
$result_guid = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->posts} SET guid = REPLACE(guid, %s, %s)",
        'https://jaramar.test',
        'http://jaramar.test'
    )
);
echo "   Filas actualizadas: {$result_guid}\n";

// 5. Mostrar resumen
echo "\n=== Resumen ===\n";
$total = $result_postmeta + $result_posts + $result_options + $result_guid;
echo "Total de registros actualizados: {$total}\n";

// 6. Verificar que se hicieron los cambios
echo "\n=== Verificación ===\n";
$check = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE %s",
        '%' . $wpdb->esc_like($old_url) . '%'
    )
);
echo "URLs con HTTPS (escapadas) restantes en postmeta: {$check}\n";

if ($check == 0) {
    echo "✓ Todas las URLs fueron actualizadas correctamente!\n";
} else {
    echo "⚠ Aún quedan {$check} URLs con HTTPS escapadas\n";
}

echo "\n✓ Corrección completada\n";
?>
