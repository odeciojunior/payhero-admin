# Whitelabel Theme System

This Laravel application includes a comprehensive whitelabel theming system that allows you to customize the appearance for different clients/brands.

## Features

- Dynamic client detection based on environment variables, domain, subdomain, URL parameters, or session
- CSS variables for easy theming
- Dynamic logo and favicon switching
- Font and typography customization
- Blade directives for easy template integration
- Caching for performance optimization

## Configuration

The whitelabel configuration is stored in `config/whitelabel.php`. Here's the structure:

```php
return [
    'default_client' => 'payhero',
    'client_detection' => [
        'method' => 'env', // Options: env, domain, subdomain, parameter, session
        'parameter_name' => 'client',
    ],
    'clients' => [
        'payhero' => [
            'name' => 'PayHero',
            'domains' => ['payhero.io', 'app.payhero.io'],
            'colors' => [...],
            'fonts' => [...],
            'typography' => [...],
            'logo' => [...],
            'favicon' => '/images/clients/payhero/favicon.ico',
            'app_name' => 'PayHero Manager',
            'footer_text' => 'PayHero Technology © 2025',
        ],
    ],
];
```

## Usage

### Blade Directives

```blade
{{-- Get any configuration value --}}
@whitelabel('app_name')

{{-- Get color value --}}
@whitelabelColor('primary')

{{-- Get logo path --}}
@whitelabelLogo('main')
@whitelabelLogo('login')
@whitelabelLogo('icon')

{{-- Get font configuration --}}
@whitelabelFont('primary')

{{-- Get current client --}}
@whitelabelClient

{{-- Inject all CSS variables --}}
@whitelabelStyles
```

### Helper Functions

```php
// Get whitelabel instance or value
whitelabel('app_name');

// Get color
whitelabel_color('primary');

// Get logo
whitelabel_logo('main');

// Get font
whitelabel_font('primary');

// Get app name
whitelabel_app_name();

// Get footer text
whitelabel_footer_text();

// Get favicon
whitelabel_favicon();

// Get current client
whitelabel_client();

// Check if current client
is_whitelabel_client('payhero');
```

### Facade Usage

```php
use App\Facades\Whitelabel;

// Get current client
$client = Whitelabel::getCurrentClient();

// Get configuration value
$appName = Whitelabel::get('app_name');

// Get color
$primaryColor = Whitelabel::getColor('primary');

// Clear cache
Whitelabel::clearCache();

// Set client manually (useful for testing)
Whitelabel::setClient('velana');
```

## Adding a New Client

1. Add the client configuration to `config/whitelabel.php`:

```php
'new_client' => [
    'name' => 'New Client',
    'domains' => ['newclient.com'],
    'colors' => [
        'primary' => '#123456',
        'secondary' => '#654321',
        // ... add all required colors
    ],
    'fonts' => [
        'primary' => [
            'family' => '"CustomFont", Arial, sans-serif',
            'weights' => [
                'regular' => 400,
                'bold' => 700,
            ]
        ]
    ],
    'logo' => [
        'main' => '/images/clients/new_client/logo.png',
        'icon' => '/images/clients/new_client/icon.png',
        'login' => '/images/clients/new_client/login-logo.png',
    ],
    'favicon' => '/images/clients/new_client/favicon.ico',
    'app_name' => 'New Client App',
    'footer_text' => 'New Client © 2025',
],
```

2. Create the logo directory:

```bash
mkdir -p public/images/clients/new_client
```

3. Add logo files:
   - `logo.png` - Main logo
   - `icon.png` - Icon/small logo
   - `login-logo.png` - Login page logo
   - `favicon.ico` - Favicon

## Client Detection Methods

### Environment Variable (default)
Set `PROJECT_NAME` in your `.env` file:
```
PROJECT_NAME=payhero
```

### Domain-based
The system will match the current domain against the `domains` array in each client configuration.

### Subdomain-based
Uses the first part of the domain as the client key (e.g., `velana.example.com` → `velana`).

### URL Parameter
Pass `?client=velana` in the URL. The client will be stored in the session.

### Session-based
The client is stored in the session and persists across requests.

## CSS Variables

The system generates CSS variables for all color values:

```css
:root {
    --primary: #6F42C1;
    --primary-light: #9A75D6;
    --primary-dark: #563098;
    --secondary: #FD7E14;
    /* ... etc ... */
}
```

These can be used in your CSS files:

```css
.button {
    background-color: var(--primary);
    color: var(--primary-contrast);
}

.button:hover {
    background-color: var(--primary-dark);
}
```

## Routes

- `/whitelabel/css` - Dynamically generated CSS file
- `/whitelabel/clear-cache` - Clear the whitelabel cache (requires authentication)

## Caching

The CSS and styles are cached for 1 hour. To clear the cache:

```php
// Via code
Whitelabel::clearCache();

// Via HTTP (requires authentication)
POST /whitelabel/clear-cache
```

## Middleware

The `WhitelabelMiddleware` is automatically applied to all web routes. It:
- Detects the current client
- Shares whitelabel data with all views
- Handles client switching via URL parameters

## Testing Different Clients

1. **Environment Variable**: Change `PROJECT_NAME` in `.env`
2. **URL Parameter**: Add `?client=velana` to any URL
3. **Programmatically**: Use `Whitelabel::setClient('velana')`

## Troubleshooting

1. **CSS not updating**: Clear the cache using `/whitelabel/clear-cache`
2. **Client not detected**: Check the detection method in config
3. **Missing images**: Ensure logo files exist in the correct directory
4. **Fonts not loading**: Check font URLs and CORS settings

## Example Color Scheme

### PayHero (Purple/Orange)
- Primary: Purple (#6F42C1)
- Secondary: Orange (#FD7E14)
- Accent: Pink (#D63384)

### Velana (Emerald/Aqua)
- Primary: Emerald (#046d3d)
- Secondary: Aqua (#019fb4)
- Accent: Mid-tone Emerald (#4f9170)