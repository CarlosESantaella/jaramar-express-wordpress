<?php
/**
 * Corregir URLs de HTTPS a HTTP
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Corrección de URLs HTTPS a HTTP ===\n\n";

global $wpdb;

$old_url = 'https://jaramar.test';
$new_url = 'http://jaramar.test';

echo "Buscando: {$old_url}\n";
echo "Reemplazar con: {$new_url}\n\n";

// 1. Actualizar wp_posts
echo "1. Actualizando wp_posts...\n";
$result_posts_content = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
        $old_url,
        $new_url
    )
);
echo "   Filas actualizadas en post_content: {$result_posts_content}\n";

$result_posts_guid = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->posts} SET guid = REPLACE(guid, %s, %s)",
        $old_url,
        $new_url
    )
);
echo "   Filas actualizadas en guid: {$result_posts_guid}\n";

// 2. Actualizar wp_postmeta
echo "\n2. Actualizando wp_postmeta...\n";
$result_postmeta = $wpdb->query(
    $wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s)",
        $old_url,
        $new_url
    )
);
echo "   Filas actualizadas: {$result_postmeta}\n";

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

// 4. Mostrar resumen
echo "\n=== Resumen ===\n";
$total = $result_posts_content + $result_posts_guid + $result_postmeta + $result_options;
echo "Total de registros actualizados: {$total}\n";

// 5. Verificar que se hicieron los cambios
echo "\n=== Verificación ===\n";
$check = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_value LIKE %s",
        '%' . $wpdb->esc_like($old_url) . '%'
    )
);
echo "URLs con HTTPS restantes en postmeta: {$check}\n";

if ($check == 0) {
    echo "✓ Todas las URLs fueron actualizadas correctamente!\n";
} else {
    echo "⚠ Aún quedan {$check} URLs con HTTPS\n";
}

echo "\n✓ Corrección completada\n";
?>
