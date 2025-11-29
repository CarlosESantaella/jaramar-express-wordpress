<?php
/**
 * Sincronizar configuración del header español al inglés
 */
require_once(__DIR__ . '/wp-load.php');

echo "=== Sincronización de Headers ===\n\n";

// Obtener datos del header español (que funciona bien)
$spanish_header_data = get_post_meta(1002, '_elementor_data', true);

if (!$spanish_header_data) {
    die("✗ No se pudo obtener datos del header español\n");
}

echo "✓ Datos del header español obtenidos\n";

// Decodificar
$spanish_data = json_decode($spanish_header_data, true);

// Obtener datos del header inglés
$english_header_data = get_post_meta(25, '_elementor_data', true);
$english_data = json_decode($english_header_data, true);

echo "✓ Datos del header inglés obtenidos\n\n";

// Función para encontrar el widget de menú y mantenerlo
function find_menu_widget($elements) {
    foreach ($elements as &$element) {
        if (isset($element['widgetType']) && $element['widgetType'] === 'navigation-menu') {
            return $element;
        }

        if (isset($element['elements']) && is_array($element['elements'])) {
            $found = find_menu_widget($element['elements']);
            if ($found !== null) {
                return $found;
            }
        }
    }
    return null;
}

// Encontrar el widget de menú en inglés
$english_menu = find_menu_widget($english_data);

if ($english_menu) {
    echo "✓ Widget de menú en inglés encontrado (ID: {$english_menu['id']})\n";
    echo "  Menu ID actual: " . ($english_menu['settings']['menu'] ?? 'N/A') . "\n\n";
} else {
    echo "⚠ No se encontró widget de menú en inglés\n\n";
}

// Función para reemplazar el widget de menú en el header español con el del inglés
function replace_menu_widget(&$elements, $new_menu_widget) {
    foreach ($elements as &$element) {
        if (isset($element['widgetType']) && $element['widgetType'] === 'navigation-menu') {
            // Mantener solo el menu ID del widget inglés, pero usar las demás configuraciones del español
            if ($new_menu_widget && isset($new_menu_widget['settings']['menu'])) {
                $element['settings']['menu'] = $new_menu_widget['settings']['menu'];
            }
            echo "  ✓ Widget de menú actualizado con menu ID: {$element['settings']['menu']}\n";
            return true;
        }

        if (isset($element['elements']) && is_array($element['elements'])) {
            if (replace_menu_widget($element['elements'], $new_menu_widget)) {
                return true;
            }
        }
    }
    return false;
}

echo "Copiando estructura del header español al inglés...\n";

// Copiar la estructura española y actualizar el menú
$new_english_data = $spanish_data;
replace_menu_widget($new_english_data, $english_menu);

// Convertir a JSON
$new_english_json = json_encode($new_english_data);

// Guardar en el header inglés
update_post_meta(25, '_elementor_data', wp_slash($new_english_json));

echo "\n✓ Header inglés actualizado con la estructura del español\n";

// Eliminar CSS para regenerar
delete_post_meta(25, '_elementor_css');

echo "✓ CSS eliminado, se regenerará automáticamente\n";

// Regenerar CSS
if (class_exists('\Elementor\Plugin')) {
    $css_file = new \Elementor\Core\Files\CSS\Post(25);
    $css_file->update();
    echo "✓ CSS regenerado\n";
}

// Limpiar cache
wp_cache_flush();
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "✓ Cache limpiado\n";
}

echo "\n✓ Sincronización completada!\n";
echo "\nAhora ambos headers deberían tener:\n";
echo "- Barra superior con fondo azul secundario\n";
echo "- Efecto sticky al hacer scroll\n";
echo "- Logo que se reduce al hacer scroll\n";
echo "- Menú en el idioma correcto (español/inglés)\n";
?>
