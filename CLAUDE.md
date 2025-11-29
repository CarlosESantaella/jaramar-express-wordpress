# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress corporate website for Jaramar Express built on Laragon (Windows development environment). The site uses Elementor page builder with the Hello Elementor theme and includes multilingual support via Polylang.

## Development Environment

- **Platform**: Laragon on Windows (MSYS_NT-10.0-26200)
- **PHP Version**: 8.3.14
- **MySQL Version**: 8.0.30
- **WordPress**: Standard installation with custom theme modifications
- **Database**:
  - Name: `jaramar`
  - User: `root`
  - Password: (empty for local development)
  - Host: `localhost`

## Project Structure

```
wp-content/
├── themes/
│   ├── hello-elementor/        # Active theme (modified Hello Elementor)
│   │   ├── assets/
│   │   │   ├── js/main.js      # Custom JavaScript (navbar, file upload, form handlers)
│   │   │   └── fonts/          # Gotham font family
│   │   └── functions.php       # Theme setup and configurations
│   ├── recursos/               # Project documentation and brand assets
│   │   ├── Identidad de marca/ # Brand identity, logos, typography
│   │   ├── Contenido textual.docx
│   │   └── Lista de tareas.xlsx
│   └── [default themes]
├── plugins/
│   ├── elementor/              # Primary page builder
│   ├── polylang/               # Multilingual support
│   ├── wordpress-seo/          # Yoast SEO
│   ├── contact-form-7/         # Contact forms
│   ├── header-footer-elementor/
│   ├── litespeed-cache/        # Caching (enabled: WP_CACHE)
│   └── ewww-image-optimizer/   # Image optimization
└── uploads/                    # Media files

wp-config.php                   # Database and WordPress configuration
.htaccess                       # URL rewriting rules
```

## Custom Development

### Theme Customization (Hello Elementor)

The active theme is Hello Elementor with custom modifications:

**Custom JavaScript** (`wp-content/themes/hello-elementor/assets/js/main.js`):
- Dynamic header height adjustment on scroll
- File upload UI for Contact Form 7
- Form submission feedback with SVG icons
- Navbar sticky behavior

**Custom Fonts**:
- Gotham font family (Brand typography) located in `assets/fonts/`

**Key Theme Features**:
- Elementor integration for drag-and-drop page building
- WooCommerce support enabled
- Custom header and footer menus
- Responsive and accessibility-ready

### Important Configuration

**wp-config.php settings**:
- `WP_DEBUG` = true (development mode)
- `WP_DEBUG_LOG` = true (logging enabled)
- `DISALLOW_FILE_EDIT` = true (security: disable file editor)
- `DISALLOW_FILE_MODS` = true (security: disable plugin/theme installation from admin)
- `WP_CACHE` = true (LiteSpeed Cache enabled)

## Common Development Tasks

### Working with the Theme

**To modify theme styles:**
```bash
# Edit the main theme stylesheet
Edit wp-content/themes/hello-elementor/style.css
```

**To modify custom JavaScript:**
```bash
# Edit custom JavaScript
Edit wp-content/themes/hello-elementor/assets/js/main.js
```

**To add new theme functionality:**
```bash
# Edit theme functions
Edit wp-content/themes/hello-elementor/functions.php
```

### Database Operations

**Import database:**
```bash
# Using MySQL command line
mysql -u root jaramar < u977340242_jaramar.sql
```

**Export database:**
```bash
# Using mysqldump
mysqldump -u root jaramar > backup.sql
```

### Elementor Development

- Pages are built using Elementor's visual editor (accessed via WordPress admin)
- Template parts are stored in the database, not in theme files
- Header/footer templates use "Header Footer Elementor" plugin
- Custom CSS can be added through Elementor's Custom CSS feature or theme customizer

### Multilingual (Polylang)

- Content translations managed through WordPress admin
- Language-specific URLs configured via Polylang settings
- Strings registered for translation in `polylang/settings/`

### Performance Optimization

**LiteSpeed Cache** is active:
- Cache configuration in WordPress admin
- `.htaccess` rules managed by LiteSpeed Cache plugin

**Image Optimization**:
- EWWW Image Optimizer handles automatic image compression
- Configured in WordPress admin

## Git Workflow

**.gitignore** excludes:
- `wp-config.php` (contains local database credentials)
- `jaramar-express-wordpress.zip` (large backup file)

**Current branch**: `develop`

## WordPress Admin Access

- Local URL: `http://jaramar/wp-admin`
- Debug log location: `wp-content/debug.log` (when WP_DEBUG_LOG is enabled)

## Architecture Notes

### Page Building Flow
1. **Elementor Templates**: Pages use Elementor templates stored in database
2. **Theme Integration**: Hello Elementor provides minimal styling, Elementor handles design
3. **Custom Code**: Additional functionality in `main.js` for UI interactions
4. **Forms**: Contact Form 7 with custom JavaScript enhancement for file uploads

### Plugin Dependencies
- Elementor requires Hello Elementor theme (or compatible theme)
- Header Footer Elementor extends Elementor for header/footer templates
- Polylang integrates with Yoast SEO for multilingual SEO

### Performance Stack
1. **Caching**: LiteSpeed Cache (server-level + plugin)
2. **Images**: EWWW Image Optimizer (compression)
3. **Code**: Minified assets via Elementor

## Brand Assets

Located in `wp-content/themes/recursos/Identidad de marca/`:
- Official brand manual PDF
- Logo variations (SVG, RGB formats)
- Gotham typography family
- Color specifications and usage guidelines

## Security Considerations

- File editing disabled in admin (`DISALLOW_FILE_EDIT`)
- Plugin/theme modifications disabled (`DISALLOW_FILE_MODS`)
- Debug mode enabled for development (disable in production)
- Authentication keys should be regenerated for production deployment

## Deployment Notes

Before deploying to production:
1. Set `WP_DEBUG` and `WP_DEBUG_LOG` to `false` in `wp-config.php`
2. Update database credentials in `wp-config.php`
3. Regenerate authentication salts using WordPress.org secret-key service
4. Update site URL in WordPress settings or database
5. Clear and reconfigure LiteSpeed Cache
6. Update `.htaccess` if domain structure changes
7. Test all Polylang language URLs
