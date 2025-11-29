<?php
/**
 * Verificar configuración de headers
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Verificación de Headers ===\n\n";

global $wpdb;

// Buscar todos los headers
$headers = $wpdb->get_results(
    "SELECT p.ID, p.post_title, p.post_status, pm.meta_value as template_type
     FROM {$wpdb->posts} p
     LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'ehf_template_type'
     WHERE pm.meta_value = 'header'
     ORDER BY p.ID ASC"
);

echo "Headers encontrados: " . count($headers) . "\n\n";

foreach ($headers as $header) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: {$header->ID}\n";
    echo "Título: {$header->post_title}\n";
    echo "Status: {$header->post_status}\n";

    // Verificar idioma (Polylang)
    $lang = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT t.slug
             FROM {$wpdb->term_relationships} tr
             INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
             INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
             WHERE tr.object_id = %d AND tt.taxonomy = 'language'",
            $header->ID
        )
    );
    echo "Idioma: " . ($lang ?: 'N/A') . "\n";

    // Verificar configuración de sticky
    $elementor_data = get_post_meta($header->ID, '_elementor_data', true);

    if ($elementor_data) {
        $data = json_decode($elementor_data, true);

        // Buscar configuración sticky
        function find_sticky_settings($elements, $depth = 0) {
            $sticky_found = [];

            foreach ($elements as $element) {
                if (isset($element['settings'])) {
                    $settings = $element['settings'];

                    // Buscar configuración de sticky
                    if (isset($settings['sticky']) || isset($settings['sticky_on']) ||
                        isset($settings['_header_effect']) || isset($settings['header_transparency'])) {
                        $sticky_found[] = [
                            'id' => $element['id'] ?? 'N/A',
                            'elType' => $element['elType'] ?? 'N/A',
                            'sticky' => $settings['sticky'] ?? 'N/A',
                            'sticky_on' => $settings['sticky_on'] ?? 'N/A',
                            'header_effect' => $settings['_header_effect'] ?? 'N/A',
                            'transparency' => $settings['header_transparency'] ?? 'N/A',
                            'background' => $settings['background_background'] ?? 'N/A',
                            'background_color' => $settings['background_color'] ?? 'N/A',
                        ];
                    }
                }

                if (isset($element['elements']) && is_array($element['elements'])) {
                    $child_sticky = find_sticky_settings($element['elements'], $depth + 1);
                    $sticky_found = array_merge($sticky_found, $child_sticky);
                }
            }

            return $sticky_found;
        }

        $sticky_settings = find_sticky_settings($data);

        if (count($sticky_settings) > 0) {
            echo "Configuraciones sticky encontradas: " . count($sticky_settings) . "\n";
            foreach ($sticky_settings as $idx => $setting) {
                echo "  " . ($idx + 1) . ". Element ID: {$setting['id']}\n";
                echo "     Type: {$setting['elType']}\n";
                echo "     Sticky: {$setting['sticky']}\n";
                echo "     Sticky On: " . json_encode($setting['sticky_on']) . "\n";
                echo "     Header Effect: {$setting['header_effect']}\n";
                echo "     Transparency: {$setting['transparency']}\n";
                echo "     Background: {$setting['background']}\n";
                echo "     Background Color: {$setting['background_color']}\n";
            }
        } else {
            echo "⚠ No se encontraron configuraciones sticky\n";
        }
    } else {
        echo "✗ No tiene datos de Elementor\n";
    }

    echo "\n";
}

// Verificar configuración de Header Footer Elementor
echo "\n=== Configuración Header Footer Elementor ===\n";
$hfe_templates = get_option('hfe_templates');
if ($hfe_templates) {
    echo "Configuración actual:\n";
    print_r($hfe_templates);
} else {
    echo "⚠ No hay configuración\n";
}

echo "\n✓ Verificación completada\n";
?>
