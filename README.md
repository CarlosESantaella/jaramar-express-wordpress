# Jaramar Express - WordPress Website

Sitio web corporativo para Jaramar Express desarrollado con WordPress, Elementor y soporte multilingüe (Español/Inglés).

## 📋 Tabla de Contenidos

- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación Local](#instalación-local)
- [Configuración](#configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Plugins Requeridos](#plugins-requeridos)
- [Solución de Problemas](#solución-de-problemas)
- [Comandos Útiles](#comandos-útiles)
- [Credenciales](#credenciales)

---

## 🖥️ Requisitos del Sistema

### Windows (Laragon - Recomendado)
- **Sistema Operativo:** Windows 10/11
- **Laragon:** 6.0 o superior
- **PHP:** 8.3.14 o superior
- **MySQL:** 8.0.30 o superior
- **Apache:** 2.4.54 o superior
- **Espacio en Disco:** Mínimo 2 GB

### Linux/Mac (Alternativas)
- **XAMPP** 8.2+ o **MAMP** 6.8+
- **PHP:** 8.1+ (con extensiones: mysqli, gd, curl, mbstring, xml, zip)
- **MySQL/MariaDB:** 8.0+
- **Apache/Nginx**

---

## 🚀 Instalación Local

### Opción 1: Instalación con Laragon (Windows)

#### 1. Descargar e Instalar Laragon

```bash
# Descargar desde: https://laragon.org/download/
# Instalar Laragon Full (incluye PHP, MySQL, Apache)
```

#### 2. Clonar el Repositorio

```bash
# Navegar a la carpeta www de Laragon
cd C:\laragon\www

# Clonar el proyecto
git clone <url-del-repositorio> jaramar

# O descomprimir el ZIP del proyecto
# unzip jaramar.zip
```

#### 3. Crear Base de Datos

**Opción A: Desde Laragon**
1. Abrir Laragon
2. Click derecho → MySQL → Crear Base de Datos
3. Nombre: `jaramar`

**Opción B: Desde línea de comandos**
```bash
# Abrir terminal en la carpeta del proyecto
cd C:\laragon\www\jaramar

# Conectar a MySQL
mysql -u root -p

# Crear base de datos
CREATE DATABASE jaramar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### 4. Importar Base de Datos

```bash
# Si tienes un backup SQL
mysql -u root jaramar < backup.sql

# O usando el backup incluido
mysql -u root jaramar < u977340242_jaramar.sql
```

#### 5. Configurar wp-config.php

Copiar el archivo de ejemplo:

```bash
cp wp-config-sample.php wp-config.php
```

Editar `wp-config.php` con las siguientes configuraciones:

```php
// Base de datos
define('DB_NAME', 'jaramar');
define('DB_USER', 'root');
define('DB_PASSWORD', '');  // Vacío en Laragon por defecto
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// URL del sitio (importante)
define('WP_HOME', 'http://jaramar.test');
define('WP_SITEURL', 'http://jaramar.test');

// Modo debug (desarrollo)
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Supresión de warnings de PHP 8.3
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// Security
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);

// Cache (LiteSpeed)
define('WP_CACHE', true);
```

**Importante:** Generar nuevas claves de seguridad en:
https://api.wordpress.org/secret-key/1.1/salt/

#### 6. Configurar Virtual Host

Laragon crea automáticamente el virtual host. Verificar que:

```
URL del sitio: http://jaramar.test
```

Si necesitas cambiarlo:
1. Laragon → Preferencias → General → Auto Virtual Hosts
2. Patrón: `{name}.test`

#### 7. Instalar WP-CLI (Opcional pero recomendado)

```bash
# Descargar WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

# Verificar que funciona
php wp-cli.phar --info
```

---

### Opción 2: Instalación con XAMPP/MAMP (Linux/Mac)

#### 1. Instalar XAMPP/MAMP

```bash
# Linux - Descargar desde:
https://www.apachefriends.org/download.html

# Mac - Descargar MAMP desde:
https://www.mamp.info/en/downloads/
```

#### 2. Configurar el Proyecto

```bash
# Linux (XAMPP)
cd /opt/lampp/htdocs
git clone <url-del-repositorio> jaramar

# Mac (MAMP)
cd /Applications/MAMP/htdocs
git clone <url-del-repositorio> jaramar
```

#### 3. Crear Base de Datos

Acceder a phpMyAdmin:
- XAMPP: http://localhost/phpmyadmin
- MAMP: http://localhost:8888/phpMyAdmin

```sql
CREATE DATABASE jaramar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 4. Importar y Configurar

Seguir los pasos 4-6 de la instalación con Laragon, ajustando rutas según el sistema.

---

## ⚙️ Configuración Post-Instalación

### 1. Actualizar URLs en Base de Datos

Si la URL antigua es diferente a `http://jaramar.test`, ejecutar:

```bash
php fix-urls.php
```

O manualmente con WP-CLI:

```bash
php wp-cli.phar search-replace 'https://old-url.com' 'http://jaramar.test' --all-tables
```

### 2. Activar Plugins Requeridos

```bash
# Activar todos los plugins esenciales
php wp-cli.phar plugin activate contact-form-7
php wp-cli.phar plugin activate elementor
php wp-cli.phar plugin activate header-footer-elementor
php wp-cli.phar plugin activate essential-addons-for-elementor-lite
php wp-cli.phar plugin activate sticky-header-effects-for-elementor
php wp-cli.phar plugin activate polylang
php wp-cli.phar plugin activate wordpress-seo
php wp-cli.phar plugin activate litespeed-cache
php wp-cli.phar plugin activate ewww-image-optimizer
```

### 3. Regenerar CSS de Elementor

```bash
# Regenerar todos los archivos CSS
php force-regenerate-css.php

# O usar WP-CLI
php wp-cli.phar elementor flush-css
```

### 4. Limpiar Cache

```bash
# WordPress cache
php wp-cli.phar cache flush

# Elementor cache
php wp-cli.phar elementor flush-css
```

### 5. Verificar Permisos (Linux/Mac)

```bash
# Dar permisos correctos
sudo chown -R www-data:www-data /path/to/jaramar
sudo chmod -R 755 /path/to/jaramar
sudo chmod -R 775 wp-content/uploads
```

---

## 📁 Estructura del Proyecto

```
jaramar/
├── wp-admin/                    # WordPress admin
├── wp-content/
│   ├── themes/
│   │   └── hello-elementor/    # Tema activo (modificado)
│   │       ├── assets/
│   │       │   ├── js/
│   │       │   │   └── main.js # JavaScript personalizado
│   │       │   └── fonts/      # Fuente Gotham
│   │       ├── functions.php   # Funciones del tema
│   │       └── style.css       # Estilos del tema
│   ├── plugins/
│   │   ├── elementor/          # Constructor de páginas
│   │   ├── polylang/           # Multiidioma
│   │   ├── contact-form-7/     # Formularios
│   │   ├── essential-addons-for-elementor-lite/
│   │   ├── header-footer-elementor/
│   │   ├── sticky-header-effects-for-elementor/
│   │   ├── litespeed-cache/
│   │   ├── wordpress-seo/
│   │   └── ewww-image-optimizer/
│   └── uploads/                # Archivos multimedia
├── wp-config.php               # Configuración de WordPress
├── .htaccess                   # Reglas de Apache
├── README.md                   # Este archivo
├── CLAUDE.md                   # Documentación del proyecto
├── wp-cli.phar                 # WP-CLI (opcional)
├── fix-urls.php                # Script para actualizar URLs
├── force-regenerate-css.php    # Script para regenerar CSS
└── sync-headers.php            # Script para sincronizar headers
```

---

## 🔌 Plugins Requeridos

| Plugin | Versión | Estado | Descripción |
|--------|---------|--------|-------------|
| **Elementor** | 3.17.3 | ✅ Activo | Constructor visual de páginas |
| **Essential Addons for Elementor Lite** | 5.8.18 | ✅ Activo | Widgets adicionales para Elementor |
| **Header Footer Elementor** | 1.6.17 | ✅ Activo | Headers y footers personalizados |
| **Sticky Header Effects** | 1.6.10 | ✅ Activo | Efecto sticky en el header |
| **Polylang** | 3.5.2 | ✅ Activo | Soporte multiidioma (ES/EN) |
| **Contact Form 7** | 5.8.3 | ✅ Activo | Formularios de contacto |
| **Yoast SEO** | 21.5 | ✅ Activo | Optimización SEO |
| **LiteSpeed Cache** | 6.5.4 | ✅ Activo | Sistema de caché |
| **EWWW Image Optimizer** | 7.2.1 | ✅ Activo | Optimización de imágenes |

---

## 🎨 Configuración del Tema

### Colores Globales de Jaramar

Los colores de marca están configurados en `functions.php`:

```php
--e-global-color-primary: #FF5100    // Naranja Jaramar
--e-global-color-secondary: #001B71   // Azul Jaramar
--e-global-color-text: #323232        // Gris oscuro
--e-global-color-accent: #FFFFFF      // Blanco
```

### Fuente Personalizada

**Fuente:** Gotham (Light, Medium, Bold)
**Ubicación:** `wp-content/themes/hello-elementor/assets/fonts/`

```
GothamLight.ttf   (300)
GothamMedium.ttf  (400, 500)
GothamBold.ttf    (700)
```

---

## 🌍 Configuración Multiidioma

El sitio está configurado con **Polylang** para soportar:
- 🇪🇸 Español (idioma principal)
- 🇬🇧 Inglés

### Estructura de Páginas por Idioma

| Página | Español (ID) | Inglés (ID) |
|--------|--------------|-------------|
| **Home** | 1038 | 13 |
| **About us** | 977 | 15 |
| **Services: Maritime** | 980 | 17 |
| **Services: Ground** | 982 | 19 |
| **Services: Customs** | 1136 | 1131 |
| **Work with us** | 984 | 21 |
| **Contact** | 975 | 23 |
| **Header** | 1002 | 25 |
| **Footer** | 1000 | 30 |

---

## 🔧 Solución de Problemas

### Problema 1: Textos no aparecen en las páginas

**Causa:** Plugin "Essential Addons for Elementor Lite" inactivo

**Solución:**
```bash
php wp-cli.phar plugin activate essential-addons-for-elementor-lite
php force-regenerate-css.php
```

### Problema 2: Videos e imágenes no se ven

**Causa:** URLs con HTTPS en lugar de HTTP

**Solución:**
```bash
php fix-https-urls-escaped.php
php wp-cli.phar elementor flush-css
```

### Problema 3: Header en inglés no es sticky

**Causa:** Configuraciones diferentes entre headers

**Solución:**
```bash
php sync-headers.php
php wp-cli.phar elementor flush-css
```

### Problema 4: Colores incorrectos (no aparece naranja)

**Causa:** CSS de Elementor no regenerado

**Solución:**
```bash
php force-regenerate-css.php
php wp-cli.phar cache flush
```

### Problema 5: Error 403 en archivos multimedia

**Causa:** URLs apuntando a dominio antiguo

**Solución:**
```bash
php fix-urls.php
```

### Problema 6: Error "Cannot redeclare function"

**Causa:** Múltiples ejecuciones de scripts PHP

**Solución:**
```bash
# Limpiar cache de PHP
php wp-cli.phar cache flush
# Reiniciar servidor web en Laragon
```

### Problema 7: Página en blanco / Error crítico

**Causa:** Plugins incompatibles o faltantes

**Solución:**
```bash
# Verificar log de errores
cat wp-content/debug.log

# Desactivar todos los plugins
php wp-cli.phar plugin deactivate --all

# Activar uno por uno
php wp-cli.phar plugin activate elementor
# ... etc
```

---

## 💻 Comandos Útiles

### WordPress CLI (WP-CLI)

```bash
# Ver información del sitio
php wp-cli.phar core version
php wp-cli.phar plugin list
php wp-cli.phar theme list

# Gestión de usuarios
php wp-cli.phar user list
php wp-cli.phar user create username email@example.com --role=administrator

# Regenerar thumbnails
php wp-cli.phar media regenerate --yes

# Buscar y reemplazar en BD
php wp-cli.phar search-replace 'old-text' 'new-text'

# Exportar/Importar BD
php wp-cli.phar db export backup.sql
php wp-cli.phar db import backup.sql

# Actualizar permalinks
php wp-cli.phar rewrite flush
```

### Elementor

```bash
# Limpiar cache de Elementor
php wp-cli.phar elementor flush-css

# Regenerar CSS
php force-regenerate-css.php

# Ver versión
php wp-cli.phar elementor version
```

### Base de Datos

```bash
# Backup de base de datos
mysqldump -u root jaramar > backup_$(date +%Y%m%d).sql

# Restaurar backup
mysql -u root jaramar < backup_20250128.sql

# Optimizar tablas
php wp-cli.phar db optimize
```

### Git

```bash
# Ver estado
git status

# Crear commit
git add .
git commit -m "Descripción de cambios"

# Crear nueva rama
git checkout -b feature/nueva-funcionalidad

# Ver cambios
git log --oneline -10
git diff
```

---

## 🔐 Credenciales

### Desarrollo Local

**WordPress Admin:**
- URL: `http://jaramar.test/wp-admin`
- Usuario: `admin_jaramar`
- Password: `Jaramar2024!`

**Base de Datos (Laragon):**
- Host: `localhost`
- Usuario: `root`
- Password: *(vacío)*
- Base de datos: `jaramar`

**phpMyAdmin:**
- URL: `http://localhost/phpmyadmin`

### Producción

⚠️ **IMPORTANTE:** Antes de desplegar a producción:

1. Cambiar todas las contraseñas
2. Generar nuevas claves de seguridad en `wp-config.php`
3. Configurar `WP_DEBUG` a `false`
4. Actualizar URLs:
   ```bash
   php wp-cli.phar search-replace 'http://jaramar.test' 'https://jaramar.com' --all-tables
   ```
5. Configurar SSL/HTTPS
6. Configurar copias de seguridad automáticas
7. Activar firewall y seguridad adicional

---

## 📝 Notas Adicionales

### Desarrollo

- El proyecto usa PHP 8.3.14, compatible con 8.1+
- Se recomienda usar PHP 8.2+ para mejor rendimiento
- Elementor requiere al menos 128MB de memoria PHP
- El modo debug está activado en desarrollo

### Archivos Ignorados (.gitignore)

```
wp-config.php
wp-content/uploads/
wp-content/cache/
*.log
.DS_Store
node_modules/
```

### Documentación del Proyecto

Ver `CLAUDE.md` para información detallada sobre:
- Estructura del proyecto
- Configuraciones específicas
- Assets de marca
- Mejores prácticas

---

## 🆘 Soporte

### Recursos

- **WordPress:** https://wordpress.org/support/
- **Elementor:** https://elementor.com/help/
- **WP-CLI:** https://wp-cli.org/
- **Laragon:** https://laragon.org/docs/

### Contacto

Para problemas específicos del proyecto, consultar:
1. Este README
2. `CLAUDE.md` en la raíz del proyecto
3. Logs en `wp-content/debug.log`

---

## 📄 Licencia

Este proyecto es propiedad de Jaramar Express. Todos los derechos reservados.

---

**Última actualización:** Noviembre 2025
**Versión de WordPress:** 6.4+
**Versión de PHP:** 8.3.14
**Versión de Elementor:** 3.17.3
