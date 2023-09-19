<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\ShopifyIntegration;
use Slince\Shopify\Client;
use Slince\Shopify\Manager\Asset\Asset;
use Slince\Shopify\Manager\Theme\Theme;
use Slince\Shopify\PublicAppCredential;

class ShopifyTemplateService
{
    public const LAYOUT_THEME_LIQUID = "layout/theme.liquid";

    public const STUBS_TEMPLATE = "AzcendSnippet.liquid";

    private $stubsTemplateFolder = __DIR__ . "./../../Shopify/Stubs/";

    private $cacheDir;

    private $credential;

    private $client;

    private $theme;

    private $skipToCart = false;

    public function __construct(string $urlStore, string $token, $getThemes = true)
    {
        if (!$this->cacheDir) {
            $cache = "/var/tmp";
            //$cache = storage_path();
        } else {
            $cache = $this->cacheDir;
        }

        $this->credential = new PublicAppCredential($token);
        $this->client = new Client($urlStore, $this->credential, [
            "meta_cache_dir" => $cache, // Metadata cache dir, required
        ]);

        if ($getThemes) {
            sleep(1);
            $this->getAllThemes();
        }
    }

    /**
     * @param string $cacheDir
     * @return $this
     */
    public function cacheDir(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;

        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $themeId
     * @return $this
     */
    public function setThemeById($themeId)
    {
        $this->theme = $this->getThemeById($themeId);

        return $this;
    }

    /**
     * @param $role
     * @return bool
     */
    public function setThemeByRole($role)
    {
        $this->theme = $this->getThemeByRole($role);

        if ($this->theme) {
            return true;
        }

        return false;
    }

    /**
     * @return \Slince\Shopify\Theme\Theme[]
     */
    public function getAllThemes()
    {
        return $this->client->getThemeManager()->findAll([]);
    }

    /**
     * @param string $role
     * @return mixed
     */
    public function getThemeIdByRole(string $role)
    {
        $theme = $this->getThemeByRole($role);

        return $theme->id;
    }

    /**
     * @return mixed
     */
    public function getThemeName()
    {
        if ($this->theme) {
            return $this->theme->getName();
        }
        return ""; //throwl
    }

    /**
     * @param string $role
     * @return \Slince\Shopify\Theme\Theme
     */
    public function getThemeByRole(string $role)
    {
        $themes = $this->getAllThemes();

        foreach ($themes as $theme) {
            if ($theme->getRole() == $role) {
                return $theme;
            }
        }

        return null;
    }

    /**
     * @param string $themeId
     * @return Theme|null
     */
    public function getThemeById(string $themeId)
    {
        $themes = $this->getAllThemes();

        foreach ($themes as $theme) {
            if ($theme->getId() == $themeId) {
                return $theme;
            }
        }

        return null; //throwl
    }

    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param bool $skipToCart
     */
    public function setSkipToCart(bool $skipToCart): void
    {
        $this->skipToCart = $skipToCart;

        $this->setThemeByRole("main");

        $themeId = $this->theme->getId();
        $resource = "themes/{$themeId}/assets";

        $snippetName = self::STUBS_TEMPLATE;
        $key = "snippets/{$snippetName}";

        $result = $this->client->get($resource, [
            "asset" => [
                "key" => $key,
            ]
        ]);

        $content = !empty($result['asset']) && !empty($result['asset']['value']) ? $result['asset']['value'] : false;

        if ($content) {
            $value  =  $skipToCart ? "true" : "false";
            $newContent = preg_replace('/var skipToCart = .+;/', "var skipToCart = {$value};", $content);

            $this->client->put($resource, [
                "asset" => [
                    "key" => $key,
                    "value" => $newContent
                ]
            ]);
        }
    }

    public function createSnippet($snippetName, $skipToCart, $domain)
    {
        if (!empty($this->theme)) {
            $themeId = $this->theme->getId();
            $snippetContent = file_get_contents("{$this->stubsTemplateFolder}{$snippetName}");

            $snippetContent = str_replace("<DOMAIN>", $domain, $snippetContent);
            $snippetContent = str_replace("'<SKIP_TO_CART>'", $skipToCart ? 'true' : 'false', $snippetContent);

            $resource = "themes/{$themeId}/assets";
            $this->client->put($resource, [
                "asset" => [
                    "key" => "snippets/{$snippetName}",
                    "value" => $snippetContent
                ]
            ]);
        }
    }

    public function removeSnippet($snippetName)
    {
        if (!empty($this->theme)) {
            $themeId = $this->theme->getId();
            $resource = "themes/{$themeId}/assets";
            $this->client->delete($resource, [
                "asset" => [
                    "key" => "snippets/{$snippetName}"
                ]
            ]);
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
            $asset = $this->client->getAssetManager()->update($this->theme->getId(), [
                "key" => $templateKeyName,
                "value" => $newHtml,
            ]);

            if ($asset) {
                return true;
            }
        }

        return false; //throwl
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

    /**
     * @return Asset[]|null
     */
    public function getAllTemplates()
    {
        if (!empty($this->theme->id)) {
            return $this->client->getAssetManager()->findAll($this->theme);
        }

        return null; //throwl
    }

    /**
     * @param string $templateKeyName
     * @return string|null
     */
    public function getTemplateHtml(string $templateKeyName = self::LAYOUT_THEME_LIQUID): ?string
    {
        if (!empty($this->theme)) {
            $templateFiles = $this->client->getAssetManager()->findAll($this->theme->getId());
            foreach ($templateFiles as $file) {
                if ($file->getKey() == $templateKeyName) {
                    $htmlCart = $this->client->getAssetManager()->find($this->theme->getId(), $templateKeyName);

                    return $htmlCart->getValue();
                }
            }
        }

        return null; //throwl
    }

    public function removeIntegrationInAllThemes(): void
    {
        if (!foxutils()->isProduction()) {
            return;
        }

        $themes = $this->getAllThemes();

        /** @var Theme $theme */
        foreach ($themes as $theme) {
            $this->setThemeById($theme->getId());

            $htmlBody = $this->getTemplateHtml();
            $htmlBody = $this->removeScript($htmlBody);

            $this->updateTemplateLiquid($htmlBody);
        }
    }
}
