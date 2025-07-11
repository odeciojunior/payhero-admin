<?php

namespace App\Http\Controllers;

use App\Facades\Whitelabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WhitelabelController extends Controller
{
    /**
     * Generate dynamic CSS file
     */
    public function css(Request $request)
    {
        $client = Whitelabel::getCurrentClient();
        $cacheKey = "whitelabel_css_{$client}";
        
        $css = Cache::remember($cacheKey, 3600, function () {
            return $this->generateCss();
        });
        
        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=3600');
    }
    
    /**
     * Generate complete CSS
     */
    private function generateCss()
    {
        $cssVariables = Whitelabel::generateCssVariables();
        $fontCss = Whitelabel::generateFontCss();
        $typographyCss = Whitelabel::generateTypographyCss();
        
        return <<<CSS
/* Whitelabel Dynamic CSS */
/* Generated for client: {$this->getCurrentClientName()} */

:root {
{$cssVariables}
{$fontCss}
{$typographyCss}
}

/* Typography */
body {
    font-family: var(--font-primary);
    font-size: var(--font-base-size);
    line-height: var(--line-height-base);
    color: var(--text);
    background-color: var(--body-bg);
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-primary);
    font-weight: var(--font-primary-bold, 700);
    line-height: var(--line-height-tight);
    color: var(--text);
}

/* Links */
a {
    color: var(--link);
    text-decoration: none;
    transition: color 0.2s ease;
}

a:hover {
    color: var(--link-hover);
}

/* Buttons */
.btn {
    font-family: var(--font-primary);
    font-weight: var(--font-primary-medium, 500);
    transition: all 0.2s ease;
}

.btn-primary {
    background-color: var(--primary);
    border-color: var(--primary);
    color: var(--primary-contrast);
}

.btn-primary:hover,
.btn-primary:focus {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
    color: var(--primary-contrast);
}

.btn-primary:active {
    background-color: var(--primary-dark) !important;
    border-color: var(--primary-dark) !important;
}

.btn-secondary {
    background-color: var(--secondary);
    border-color: var(--secondary);
    color: var(--secondary-contrast);
}

.btn-secondary:hover,
.btn-secondary:focus {
    background-color: var(--secondary-dark);
    border-color: var(--secondary-dark);
    color: var(--secondary-contrast);
}

.btn-accent {
    background-color: var(--accent);
    border-color: var(--accent);
    color: var(--accent-contrast);
}

.btn-accent:hover,
.btn-accent:focus {
    background-color: var(--accent-dark);
    border-color: var(--accent-dark);
    color: var(--accent-contrast);
}

/* Cards */
.card {
    background-color: var(--card-bg);
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px var(--shadow);
}

.card-header {
    background-color: var(--card-bg);
    border-bottom: 1px solid var(--border);
}

/* Forms */
.form-control {
    background-color: var(--input-bg);
    color: var(--input-color);
    border-color: var(--border);
    font-family: var(--font-primary);
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem var(--focus-ring);
}

.form-control:disabled {
    background-color: var(--input-disabled);
    opacity: 0.6;
}

/* Navigation */
.site-menubar {
    background-color: var(--sidebar-bg);
}

.site-navbar {
    background-color: var(--header-bg);
    box-shadow: 0 2px 4px var(--shadow);
}

.navbar-brand {
    color: var(--text);
}

/* Tables */
.table {
    color: var(--text);
}

.table thead th {
    background-color: var(--gray-100);
    border-bottom: 2px solid var(--border);
    color: var(--text);
    font-weight: var(--font-primary-semibold, 600);
}

.table tbody tr {
    border-bottom: 1px solid var(--border);
}

.table tbody tr:hover {
    background-color: var(--gray-100);
}

/* Alerts */
.alert {
    border-radius: 4px;
    font-weight: var(--font-primary-medium, 500);
}

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

/* Badges */
.badge-primary {
    background-color: var(--primary);
    color: var(--primary-contrast);
}

.badge-secondary {
    background-color: var(--secondary);
    color: var(--secondary-contrast);
}

.badge-success {
    background-color: var(--success);
    color: white;
}

.badge-danger {
    background-color: var(--danger);
    color: white;
}

.badge-warning {
    background-color: var(--warning);
    color: var(--gray-900);
}

.badge-info {
    background-color: var(--info);
    color: white;
}

/* Text colors */
.text-primary {
    color: var(--primary) !important;
}

.text-secondary {
    color: var(--secondary) !important;
}

.text-success {
    color: var(--success) !important;
}

.text-danger {
    color: var(--danger) !important;
}

.text-warning {
    color: var(--warning) !important;
}

.text-info {
    color: var(--info) !important;
}

.text-muted {
    color: var(--text-muted) !important;
}

/* Background colors */
.bg-primary {
    background-color: var(--primary) !important;
    color: var(--primary-contrast);
}

.bg-secondary {
    background-color: var(--secondary) !important;
    color: var(--secondary-contrast);
}

.bg-success {
    background-color: var(--success) !important;
    color: white;
}

.bg-danger {
    background-color: var(--danger) !important;
    color: white;
}

.bg-warning {
    background-color: var(--warning) !important;
    color: var(--gray-900);
}

.bg-info {
    background-color: var(--info) !important;
    color: white;
}

/* Borders */
.border {
    border-color: var(--border) !important;
}

.border-primary {
    border-color: var(--primary) !important;
}

.border-secondary {
    border-color: var(--secondary) !important;
}

/* Modals */
.modal-content {
    background-color: var(--card-bg);
    border-color: var(--border);
}

.modal-header {
    border-bottom-color: var(--border);
}

.modal-footer {
    border-top-color: var(--border);
}

.modal-backdrop {
    background-color: var(--backdrop);
}

/* Tooltips */
.tooltip-inner {
    background-color: var(--tooltip-bg);
    color: white;
}

/* Selection */
::selection {
    background-color: var(--selection-bg);
    color: var(--primary-contrast);
}

::-moz-selection {
    background-color: var(--selection-bg);
    color: var(--primary-contrast);
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--gray-100);
}

::-webkit-scrollbar-thumb {
    background: var(--gray-400);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--gray-500);
}

/* Loading states */
.placeholder-loading {
    background-color: var(--gray-200);
}

/* Custom scrollbar for Firefox */
* {
    scrollbar-width: thin;
    scrollbar-color: var(--gray-400) var(--gray-100);
}
CSS;
    }
    
    /**
     * Get current client name
     */
    private function getCurrentClientName()
    {
        return Whitelabel::get('name', 'Default');
    }
    
    /**
     * Clear CSS cache
     */
    public function clearCache(Request $request)
    {
        $client = Whitelabel::getCurrentClient();
        Cache::forget("whitelabel_css_{$client}");
        Whitelabel::clearCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Whitelabel cache cleared successfully'
        ]);
    }
}