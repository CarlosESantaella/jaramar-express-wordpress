<?php
/**
 * Comparar configuración de headers
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Comparación de Headers ===\n\n";

// Función para extraer toda la configuración relevante
function extract_settings($elements, $path = '', &$all_settings = []) {
            foreach ($elements as $idx => $element) {
                $current_path = $path ? "{$path} > {$idx}" : $idx;

                if (isset($element['settings'])) {
                    $settings = $element['settings'];

                    // Buscar configuraciones de sticky, colores, efectos
                    $relevant_keys = ['sticky', 'sticky_on', '_header_effect', 'header_transparency',
                                     'background_background', 'background_color', 'background_motion_fx_motion_fx_scrolling',
                                     '_element_id', 'css_classes', '_css_classes'];

                    $relevant_settings = [];
                    foreach ($relevant_keys as $key) {
                        if (isset($settings[$key])) {
                            $relevant_settings[$key] = $settings[$key];
                        }
                    }

                    if (!empty($relevant_settings)) {
                        $all_settings[] = [
                            'path' => $current_path,
                            'id' => $element['id'] ?? 'N/A',
                            'elType' => $element['elType'] ?? 'N/A',
                            'widgetType' => $element['widgetType'] ?? 'N/A',
                            'settings' => $relevant_settings
                        ];
                    }
                }

                if (isset($element['elements']) && is_array($element['elements'])) {
                    extract_settings($element['elements'], $current_path, $all_settings);
                }
            }

            return $all_settings;
}

function analyze_header($header_id, $header_name) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "{$header_name} (ID: {$header_id})\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    $elementor_data = get_post_meta($header_id, '_elementor_data', true);

    if ($elementor_data) {
        $data = json_decode($elementor_data, true);

        $all_settings = [];
        extract_settings($data, '', $all_settings);

        if (count($all_settings) > 0) {
            echo "Elementos con configuraciones relevantes: " . count($all_settings) . "\n\n";

            foreach ($all_settings as $idx => $item) {
                echo ($idx + 1) . ". Element ID: {$item['id']}\n";
                echo "   Type: {$item['elType']}";
                if ($item['widgetType'] !== 'N/A') {
                    echo " / {$item['widgetType']}";
                }
                echo "\n";
                echo "   Path: {$item['path']}\n";
                echo "   Settings:\n";
                foreach ($item['settings'] as $key => $value) {
                    if (is_array($value)) {
                        echo "     - {$key}: " . json_encode($value) . "\n";
                    } else {
                        echo "     - {$key}: {$value}\n";
                    }
                }
                echo "\n";
            }
        } else {
            echo "⚠ No se encontraron configuraciones relevantes\n\n";
        }
    } else {
        echo "✗ No tiene datos de Elementor\n\n";
    }
}

// Comparar ambos headers
analyze_header(1002, "Header ESPAÑOL");
analyze_header(25, "Header INGLÉS");

echo "✓ Comparación completada\n";
?>
