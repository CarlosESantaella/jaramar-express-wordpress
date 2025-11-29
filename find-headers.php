<?php
/**
 * Buscar headers de manera más completa
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Búsqueda de Headers ===\n\n";

global $wpdb;

// Buscar todos los posts con tipo elementor_library
$posts = $wpdb->get_results(
    "SELECT ID, post_title, post_status, post_type
     FROM {$wpdb->posts}
     WHERE post_type = 'elementor_library'
     AND post_status = 'publish'
     ORDER BY ID ASC"
);

echo "Posts de Elementor Library encontrados: " . count($posts) . "\n\n";

foreach ($posts as $post) {
    $template_type = get_post_meta($post->ID, '_elementor_template_type', true);
    $ehf_type = get_post_meta($post->ID, 'ehf_template_type', true);

    if ($template_type === 'header' || $ehf_type === 'header') {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID: {$post->ID}\n";
        echo "Título: {$post->post_title}\n";
        echo "Status: {$post->post_status}\n";
        echo "Template Type: {$template_type}\n";
        echo "EHF Type: {$ehf_type}\n";

        // Verificar idioma
        $terms = wp_get_object_terms($post->ID, 'language');
        if (!empty($terms) && !is_wp_error($terms)) {
            echo "Idioma: {$terms[0]->slug} ({$terms[0]->name})\n";
        } else {
            echo "Idioma: N/A\n";
        }

        echo "\n";
    }
}

echo "\n=== Posts ID 25 y 1002 (headers conocidos) ===\n";
foreach ([25, 1002] as $id) {
    $post = get_post($id);
    if ($post) {
        echo "\nID: {$id}\n";
        echo "Título: {$post->post_title}\n";
        echo "Tipo: {$post->post_type}\n";
        echo "Status: {$post->post_status}\n";

        $template_type = get_post_meta($id, '_elementor_template_type', true);
        $ehf_type = get_post_meta($id, 'ehf_template_type', true);
        echo "Template Type: {$template_type}\n";
        echo "EHF Type: {$ehf_type}\n";

        $terms = wp_get_object_terms($id, 'language');
        if (!empty($terms) && !is_wp_error($terms)) {
            echo "Idioma: {$terms[0]->slug} ({$terms[0]->name})\n";
        } else {
            echo "Idioma: N/A\n";
        }
    }
}

echo "\n✓ Búsqueda completada\n";
?>
