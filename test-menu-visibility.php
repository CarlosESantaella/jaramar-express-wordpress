<?php
/**
 * Script para diagnosticar problemas de visibilidad del menú
 */
require_once(__DIR__ . '/wp-load.php');

// Obtener el HTML de la home
ob_start();
include(__DIR__ . '/index.php');
$html = ob_get_clean();

echo "=== Diagnóstico del Menú ===\n\n";

// Verificar si el menú está en el HTML
if (strpos($html, 'hfe-nav-menu') !== false) {
    echo "✓ El widget de menú está presente en el HTML\n";
} else {
    echo "✗ El widget de menú NO está en el HTML\n";
}

// Contar items del menú
preg_match_all('/class="menu-item/', $html, $matches);
echo "✓ Items de menú encontrados: " . count($matches[0]) . "\n";

// Verificar estilos inline que puedan ocultar el menú
if (preg_match('/display:\s*none/i', $html)) {
    echo "⚠ Encontrado 'display: none' en el HTML\n";
}

if (preg_match('/visibility:\s*hidden/i', $html)) {
    echo "⚠ Encontrado 'visibility: hidden' en el HTML\n";
}

if (preg_match('/opacity:\s*0/i', $html)) {
    echo "⚠ Encontrado 'opacity: 0' en el HTML\n";
}

// Verificar color del menú
if (preg_match('/\.hfe-menu-item[^}]*color:\s*([^;]+);/i', $html, $color_match)) {
    echo "Color del menú: " . trim($color_match[1]) . "\n";
} else {
    echo "⚠ No se encontró color definido para .hfe-menu-item\n";
}

echo "\n=== Extrayendo fragmento del menú ===\n";
if (preg_match('/<ul id="menu-1-[^"]+">.*?<\/ul>/s', $html, $menu_match)) {
    $menu_html = substr($menu_match[0], 0, 500);
    echo $menu_html . "...\n";
}

echo "\n✓ Diagnóstico completado\n";
?>
