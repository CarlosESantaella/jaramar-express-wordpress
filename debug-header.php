<?php
/**
 * Debug del header de Elementor
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Debug del Header de Elementor ===\n\n";

// Obtener datos del header (post ID 25)
$header_data = get_post_meta(25, '_elementor_data', true);

if ($header_data) {
    $data = json_decode($header_data, true);

    echo "✓ Header encontrado (Post ID: 25)\n";
    echo "Total de elementos: " . count($data) . "\n\n";

    // Buscar el widget de navegación
    function find_navigation_widget($elements, $depth = 0) {
        foreach ($elements as $element) {
            if (isset($element['widgetType']) && $element['widgetType'] === 'navigation-menu') {
                echo str_repeat("  ", $depth) . "✓ Widget de Navegación encontrado!\n";
                echo str_repeat("  ", $depth) . "  ID: " . ($element['id'] ?? 'N/A') . "\n";

                if (isset($element['settings'])) {
                    echo str_repeat("  ", $depth) . "  Configuración:\n";
                    echo str_repeat("  ", $depth) . "    - Layout: " . ($element['settings']['layout'] ?? 'N/A') . "\n";
                    echo str_repeat("  ", $depth) . "    - Menu: " . ($element['settings']['menu'] ?? 'N/A') . "\n";
                    echo str_repeat("  ", $depth) . "    - Breakpoint: " . ($element['settings']['breakpoint'] ?? 'N/A') . "\n";

                    // Verificar si hay estilos que oculten el menú
                    if (isset($element['settings']['_element_id'])) {
                        echo str_repeat("  ", $depth) . "    - Element ID: " . $element['settings']['_element_id'] . "\n";
                    }
                }
                return true;
            }

            if (isset($element['elements']) && is_array($element['elements'])) {
                if (find_navigation_widget($element['elements'], $depth + 1)) {
                    return true;
                }
            }
        }
        return false;
    }

    if (!find_navigation_widget($data)) {
        echo "⚠ No se encontró widget de navegación en el header\n";
    }
} else {
    echo "✗ No se pudo obtener datos del header\n";
}

// Verificar asignación del header
echo "\n=== Verificación de Asignación ===\n";
$hfe_settings = get_option('hfe_templates');
if ($hfe_settings) {
    echo "Configuración Header Footer Elementor:\n";
    print_r($hfe_settings);
} else {
    echo "⚠ No hay configuración de Header Footer Elementor\n";
}

// Verificar que el header se esté cargando
echo "\n=== Verificación de Carga ===\n";
echo "Template type: " . get_post_meta(25, 'ehf_template_type', true) . "\n";

echo "\n✓ Debug completado\n";
?>
