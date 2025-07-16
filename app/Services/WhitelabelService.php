<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class WhitelabelService
{
    protected $currentClient = null;
    protected $config = [];

    public function __construct()
    {
        $this->config = config('whitelabel', []);
        $this->detectClient();
    }

    /**
     * Detect the current client based on configuration
     */
    protected function detectClient()
    {
        $detectionMethod = $this->config['client_detection']['method'] ?? 'env';
        
        switch ($detectionMethod) {
            case 'domain':
                $this->currentClient = $this->detectByDomain();
                break;
            case 'subdomain':
                $this->currentClient = $this->detectBySubdomain();
                break;
            case 'parameter':
                $this->currentClient = $this->detectByParameter();
                break;
            case 'session':
                $this->currentClient = $this->detectBySession();
                break;
            case 'env':
            default:
                $this->currentClient = $this->detectByEnv();
                break;
        }

        // Fallback to default if no client detected
        if (!$this->currentClient || !isset($this->config['clients'][$this->currentClient])) {
            $this->currentClient = $this->config['default_client'] ?? 'payhero';
        }
    }

    /**
     * Detect client by domain
     */
    protected function detectByDomain()
    {
        $host = Request::getHost();
        
        foreach ($this->config['clients'] as $key => $client) {
            if (isset($client['domains']) && in_array($host, $client['domains'])) {
                return $key;
            }
        }
        
        return null;
    }

    /**
     * Detect client by subdomain
     */
    protected function detectBySubdomain()
    {
        $host = Request::getHost();
        $parts = explode('.', $host);
        
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            if (isset($this->config['clients'][$subdomain])) {
                return $subdomain;
            }
        }
        
        return null;
    }

    /**
     * Detect client by parameter
     */
    protected function detectByParameter()
    {
        $paramName = $this->config['client_detection']['parameter_name'] ?? 'client';
        $client = Request::get($paramName);
        
        if ($client && isset($this->config['clients'][$client])) {
            // Store in session for persistence
            Session::put('whitelabel_client', $client);
            return $client;
        }
        
        // Check session
        return Session::get('whitelabel_client');
    }

    /**
     * Detect client by session
     */
    protected function detectBySession()
    {
        return Session::get('whitelabel_client');
    }

    /**
     * Detect client by environment variable
     */
    protected function detectByEnv()
    {
        return env('PROJECT_NAME', $this->config['default_client'] ?? 'payhero');
    }

    /**
     * Get current client key
     */
    public function getCurrentClient()
    {
        return $this->currentClient;
    }

    /**
     * Get current client configuration
     */
    public function getCurrentClientConfig()
    {
        return $this->config['clients'][$this->currentClient] ?? [];
    }

    /**
     * Get a configuration value for the current client
     */
    public function get($key, $default = null)
    {
        $clientConfig = $this->getCurrentClientConfig();
        return data_get($clientConfig, $key, $default);
    }

    /**
     * Get color value
     */
    public function getColor($key, $default = '#000000')
    {
        return $this->get("colors.$key", $default);
    }

    /**
     * Get logo path
     */
    public function getLogo($type = 'main')
    {
        return $this->get("logo.$type", '/images/logo.png');
    }

    /**
     * Get font configuration
     */
    public function getFont($type = 'primary')
    {
        return $this->get("fonts.$type", [
            'family' => 'Arial, sans-serif',
            'weights' => ['regular' => 400]
        ]);
    }

    /**
     * Get typography setting
     */
    public function getTypography($key)
    {
        return $this->get("typography.$key");
    }

    /**
     * Get app name
     */
    public function getAppName()
    {
        return $this->get('app_name', config('app.name', 'Laravel'));
    }

    /**
     * Get footer text
     */
    public function getFooterText()
    {
        return $this->get('footer_text', '© ' . date('Y') . ' All rights reserved');
    }

    /**
     * Get favicon
     */
    public function getFavicon()
    {
        return $this->get('favicon', '/favicon.ico');
    }

    /**
     * Generate CSS variables from configuration
     */
    public function generateCssVariables()
    {
        $colors = $this->get('colors', []);
        $css = '';
        
        foreach ($colors as $key => $value) {
            // Handle CSS variable references
            if (strpos($value, 'var(') === 0) {
                $css .= "--{$key}: {$value};\n";
            } else {
                $css .= "--{$key}: {$value};\n";
            }
        }
        
        return $css;
    }

    /**
     * Generate font CSS
     */
    public function generateFontCss()
    {
        $fonts = $this->get('fonts', []);
        $css = '';
        
        foreach ($fonts as $type => $font) {
            $family = $font['family'] ?? 'Arial, sans-serif';
            $css .= "--font-{$type}: {$family};\n";
            
            if (isset($font['weights'])) {
                foreach ($font['weights'] as $weight => $value) {
                    $css .= "--font-{$type}-{$weight}: {$value};\n";
                }
            }
        }
        
        return $css;
    }

    /**
     * Generate typography CSS
     */
    public function generateTypographyCss()
    {
        $typography = $this->get('typography', []);
        $css = '';
        
        // Base size
        if (isset($typography['base-size'])) {
            $css .= "--font-base-size: {$typography['base-size']};\n";
        }
        
        // Scale ratio
        if (isset($typography['scale-ratio'])) {
            $css .= "--font-scale-ratio: {$typography['scale-ratio']};\n";
        }
        
        // Line heights
        if (isset($typography['line-height'])) {
            foreach ($typography['line-height'] as $key => $value) {
                $css .= "--line-height-{$key}: {$value};\n";
            }
        }
        
        // Letter spacing
        if (isset($typography['letter-spacing'])) {
            foreach ($typography['letter-spacing'] as $key => $value) {
                $css .= "--letter-spacing-{$key}: {$value};\n";
            }
        }
        
        return $css;
    }

    /**
     * Generate complete styles
     */
    public function generateStyles()
    {
        $cacheKey = 'whitelabel_styles_' . $this->currentClient;
        
        return Cache::remember($cacheKey, 3600, function () {
            $cssVariables = $this->generateCssVariables();
            $fontCss = $this->generateFontCss();
            $typographyCss = $this->generateTypographyCss();
            
            return "<style>
:root {
{$cssVariables}
{$fontCss}
{$typographyCss}
}

/* Apply primary font */
body {
    font-family: var(--font-primary);
    font-size: var(--font-base-size);
    line-height: var(--line-height-base);
    color: var(--text);
    background-color: var(--body-bg);
}

/* Headers */
h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-primary);
    font-weight: var(--font-primary-bold);
    line-height: var(--line-height-tight);
    color: var(--text);
}

/* Links */
a {
    color: var(--link);
    text-decoration: none;
}

a:hover {
    color: var(--link-hover);
}

/* Buttons */
.btn-primary {
    background-color: var(--primary);
    border-color: var(--primary);
    color: var(--primary-contrast);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
}

.btn-secondary {
    background-color: var(--secondary);
    border-color: var(--secondary);
    color: var(--secondary-contrast);
}

.btn-secondary:hover {
    background-color: var(--secondary-dark);
    border-color: var(--secondary-dark);
}

/* Cards */
.card {
    background-color: var(--card-bg);
    border-color: var(--border);
}

/* Sidebar */
.site-menubar {
    background-color: var(--sidebar-bg);
}

/* Header */
.site-navbar {
    background-color: var(--header-bg);
}

/* Inputs */
.form-control {
    background-color: var(--input-bg);
    color: var(--input-color);
    border-color: var(--border);
}

.form-control:disabled {
    background-color: var(--input-disabled);
}

/* Alerts */
.alert-success {
    background-color: var(--success-light);
    border-color: var(--success);
    color: var(--success-dark);
}

.alert-danger {
    background-color: var(--danger-light);
    border-color: var(--danger);
    color: var(--danger-dark);
}

.alert-warning {
    background-color: var(--warning-light);
    border-color: var(--warning);
    color: var(--warning-dark);
}

.alert-info {
    background-color: var(--info-light);
    border-color: var(--info);
    color: var(--info-dark);
}
</style>";
        });
    }

    /**
     * Clear cache for current client
     */
    public function clearCache()
    {
        Cache::forget('whitelabel_styles_' . $this->currentClient);
    }

    /**
     * Set client manually (useful for testing)
     */
    public function setClient($client)
    {
        if (isset($this->config['clients'][$client])) {
            $this->currentClient = $client;
            Session::put('whitelabel_client', $client);
            $this->clearCache();
            return true;
        }
        return false;
    }

    /**
     * Validate client configuration
     */
    public function validateClientConfig($client = null)
    {
        $client = $client ?? $this->currentClient;
        $config = $this->config['clients'][$client] ?? [];
        
        $required = ['name', 'colors', 'fonts', 'logo'];
        $missing = [];
        
        foreach ($required as $key) {
            if (!isset($config[$key])) {
                $missing[] = $key;
            }
        }
        
        return [
            'valid' => empty($missing),
            'missing' => $missing,
            'client' => $client,
        ];
    }

    /**
     * Get all available clients
     */
    public function getAvailableClients()
    {
        return array_keys($this->config['clients'] ?? []);
    }

    /**
     * Check if client exists
     */
    public function clientExists($client)
    {
        return isset($this->config['clients'][$client]);
    }
}