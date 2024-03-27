<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Services\Shopify\AssetService;
use Modules\Core\Services\Shopify\Client;
use Modules\Core\Services\Shopify\ThemeService;

class ShopifyTemplateService
{
    public const LAYOUT_THEME_LIQUID = "layout/theme.liquid";

    public const STUBS_TEMPLATE = "AzcendSnippet.liquid";

    private $stubsTemplateFolder = __DIR__ . "./../../Shopify/Stubs/";

    private $credential;

    private $theme;

    private $skipToCart = false;

    private ThemeService $themeService;
    private AssetService $assetService;

    public function __construct(string $urlStore, string $token)
    {
        $client = new Client($urlStore, $token);
        $this->themeService = new ThemeService($client);
        $this->assetService = new AssetService($client);
    }

    public function setThemeById($themeId)
    {
        $this->theme = $this->getThemeById($themeId);

        return $this;
    }

    public function setThemeByRole($role)
    {
        $this->theme = $this->getThemeByRole($role);

        if ($this->theme) {
            return true;
        }

        return false;
    }

    public function getAllThemes()
    {
        return $this->themeService->findAll();
    }

    public function getThemeName()
    {
        if ($this->theme) {
            return $this->theme->name;
        }
        return "";
    }

    public function getThemeByRole(string $role)
    {
        $themes = $this->getAllThemes();

        foreach ($themes as $theme) {
            if ($theme->role == $role) {
                return $theme;
            }
        }

        return null;
    }

    public function getThemeById(string $themeId)
    {
        $themes = $this->getAllThemes();

        foreach ($themes as $theme) {
            if ($theme->id == $themeId) {
                return $theme;
            }
        }

        return null;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setSkipToCart(bool $skipToCart): void
    {
        $this->skipToCart = $skipToCart;

        $this->setThemeByRole("main");

        $themeId = $this->theme->id;

        $snippetName = self::STUBS_TEMPLATE;
        $key = "snippets/{$snippetName}";

        $result = $this->assetService->find($themeId, $key);

        $content = !empty($result["asset"]) && !empty($result["asset"]["value"]) ? $result["asset"]["value"] : false;

        if ($content) {
            $value = $skipToCart ? "true" : "false";
            $newContent = preg_replace("/var skipToCart = .+;/", "var skipToCart = {$value};", $content);

            $this->assetService->createOrUpdateAsset($themeId, $key, $newContent);
        }
    }

    public function createSnippet($snippetName, $skipToCart, $domain)
    {
        if (!empty($this->theme)) {
            $themeId = $this->theme->id;
            $snippetContent = file_get_contents("{$this->stubsTemplateFolder}{$snippetName}");

            $snippetContent = str_replace("<DOMAIN>", $domain, $snippetContent);
            $snippetContent = str_replace('"<SKIP_TO_CART>"', $skipToCart ? "true" : "false", $snippetContent);

            $this->assetService->createOrUpdateAsset($themeId, "snippets/{$snippetName}", $snippetContent);
        }
    }

    public function removeSnippet($snippetName)
    {
        if (!empty($this->theme)) {
            $themeId = $this->theme->id;
            $this->assetService->delete($themeId, "snippets/{$snippetName}");
        }
    }

    public function makeTemplateIntegration(ShopifyIntegration $shopifyIntegration, $domain, $theme)
    {
        $this->setThemeByRole("main");

        // insert snippet files
        $this->removeSnippet(self::STUBS_TEMPLATE);
        $this->createSnippet(self::STUBS_TEMPLATE, boolval($shopifyIntegration->skip_to_cart), $domain->name);

        // include snippets into main theme file
        $htmlBody = $this->getTemplateHtml();

        if (empty($htmlBody)) {
            return [
                "failed" => true,
                "message" => "Problema ao refazer integração, template \"theme.liquid\" não encontrado",
            ];
        }

        $htmlBody = $this->removeScript($htmlBody);

        $htmlToPersistence = $htmlBody;
        if (strlen($htmlToPersistence) > 65535) {
            $htmlToPersistence = trim($htmlBody);
        }
        if (strlen($htmlToPersistence) <= 65535) {
            $shopifyIntegration->update([
                "theme_type" => $theme,
                "theme_name" => $this->getThemeName(),
                "theme_file" => $this::LAYOUT_THEME_LIQUID,
                "theme_html" => $htmlToPersistence,
                "layout_theme_html" => $htmlToPersistence,
            ]);
        } else {
            $shopifyIntegration->update([
                "theme_type" => $theme,
                "theme_name" => $this->getThemeName(),
                "theme_file" => $this::LAYOUT_THEME_LIQUID,
            ]);
        }

        $newHtmlBody = $this->insertScript($htmlBody);
        $this->updateTemplateLiquid($newHtmlBody);

        $shopifyIntegration->update([
            "status" => $shopifyIntegration->present()->getStatus("approved"),
        ]);

        return [
            "failed" => false,
            "message" => "Problema ao refazer integração, template \"theme.liquid\" não encontrado",
        ];
    }

    public function updateTemplateLiquid(string $newHtml, string $templateKeyName = self::LAYOUT_THEME_LIQUID)
    {
        if (!empty($this->theme)) {
            $asset = $this->assetService->createOrUpdateAsset($this->theme->id, $templateKeyName, $newHtml);

            if ($asset) {
                return true;
            }
        }

        return false;
    }

    public function insertScript(string $oldHtml)
    {
        $html = $this->removeScript($oldHtml);
        $strPos = strpos($html, "</body>");

        $script = "\n\n  <!-- Não remova. Checkout Azcend. -->";
        $script .= "\n  {% capture azcend_snippet_content %}";
        $script .= "\n    {% include 'AzcendSnippet' %}";
        $script .= "\n  {% endcapture %}";
        $script .= "\n  {% unless azcend_snippet_content contains 'Liquid error' %}";
        $script .= "\n    {% include 'AzcendSnippet' %}";
        $script .= "\n  {% endunless %}";
        $script .= "\n  <!-- Não remova. Checkout Azcend. -->\n\n";

        $newHtml = substr_replace($html, $script, $strPos, 0);

        return $newHtml;
    }

    public function removeScript(string $html)
    {
        $starComment = "\n\n  <!-- Não remova. Checkout Azcend. -->";
        $endComment = "\n  <!-- Não remova. Checkout Azcend. -->\n\n";

        $startScriptPos = strpos($html, $starComment);
        $endScriptPos = strpos($html, $endComment);

        $countEndCharacters = strlen($endComment);

        if ($startScriptPos !== false) {
            //script já existe, remove
            $size = $endScriptPos + $countEndCharacters - $startScriptPos;

            $html = substr_replace($html, "", $startScriptPos, $size);
        }

        return $html;
    }

    public function getTemplateHtml(string $templateKeyName = self::LAYOUT_THEME_LIQUID): ?string
    {
        if (!empty($this->theme)) {
            $templateFiles = $this->assetService->findAll($this->theme->id);
            foreach ($templateFiles as $file) {
                if ($file->key == $templateKeyName) {
                    $htmlCart = $this->assetService->find($this->theme->id, $templateKeyName);

                    return $htmlCart->value;
                }
            }
        }

        return null;
    }

    public function removeIntegrationInAllThemes(): void
    {
        if (!foxutils()->isProduction()) {
            return;
        }

        $themes = $this->getAllThemes();

        foreach ($themes as $theme) {
            $this->setThemeById($theme->id);

            $htmlBody = $this->getTemplateHtml();
            if ($htmlBody !== null) {
                $htmlBody = $this->removeScript($htmlBody ?? "");
                $this->updateTemplateLiquid($htmlBody);
            }
        }
    }
}
