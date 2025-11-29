<?php
/**
 * Buscar widget de navegación en todos los posts
 */
require_once(__DIR__ . '/wp-load.php');

global $wpdb;

echo "=== Buscando widget de navegación en todos los posts ===\n\n";

// Buscar en todos los posts que tengan _elementor_data
$posts = $wpdb->get_results(
    "SELECT post_id, LEFT(meta_value, 200) as preview
     FROM {$wpdb->postmeta}
     WHERE meta_key = '_elementor_data'
     AND meta_value LIKE '%navigation-menu%'
     LIMIT 10"
);

if ($posts) {
    echo "Encontrados " . count($posts) . " posts con widget de navegación:\n\n";

    foreach ($posts as $post) {
        $post_data = get_post($post->post_id);
        $template_type = get_post_meta($post->post_id, 'ehf_template_type', true);

        echo "Post ID: {$post->post_id}\n";
        echo "  Título: {$post_data->post_title}\n";
        echo "  Tipo: {$post_data->post_type}\n";
        echo "  Status: {$post_data->post_status}\n";
        echo "  Template: " . ($template_type ?: 'N/A') . "\n";
        echo "  ---\n";
    }
} else {
    echo "⚠ No se encontraron posts con widget de navegación\n";
}

echo "\n✓ Búsqueda completada\n";
?>
