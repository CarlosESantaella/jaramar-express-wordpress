<?php
/**
 * Actualizar configuración del widget de menú en el header
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Actualización del Widget de Menú ===\n\n";

// Obtener datos del header
$elementor_data = get_post_meta(25, '_elementor_data', true);
if ($elementor_data) {
    $data = json_decode($elementor_data, true);

    // Función para actualizar el menú en el widget
    function update_menu_widget(&$elements) {
        $updated = false;
        foreach ($elements as &$element) {
            if (isset($element['widgetType']) && $element['widgetType'] === 'navigation-menu') {
                echo "✓ Widget de navegación encontrado\n";
                echo "  Menu actual: " . ($element['settings']['menu'] ?? 'N/A') . "\n";

                // Actualizar a usar el menú correcto (ID 2 = "main menu")
                $element['settings']['menu'] = '2';
                $element['settings']['layout'] = 'horizontal';

                echo "  Menu actualizado a: 2 (main menu)\n";
                echo "  Layout actualizado a: horizontal\n";

                $updated = true;
            }

            if (isset($element['elements']) && is_array($element['elements'])) {
                if (update_menu_widget($element['elements'])) {
                    $updated = true;
                }
            }
        }
        return $updated;
    }

    if (update_menu_widget($data)) {
        // Guardar los datos actualizados
        $updated_json = json_encode($data);
        update_post_meta(25, '_elementor_data', wp_slash($updated_json));

        // Eliminar CSS para regenerar
        delete_post_meta(25, '_elementor_css');

        // Regenerar CSS
        if (class_exists('\Elementor\Plugin')) {
            $css_file = new \Elementor\Core\Files\CSS\Post(25);
            $css_file->update();
            echo "\n✓ CSS regenerado\n";
        }

        // Limpiar cache
        wp_cache_flush();
        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }

        echo "\n✓ Widget de menú actualizado correctamente!\n";
        echo "El menú debería aparecer ahora.\n";
    } else {
        echo "⚠ No se encontró widget de navegación para actualizar\n";
    }
} else {
    echo "✗ No se pudieron obtener datos del header\n";
}
?>
