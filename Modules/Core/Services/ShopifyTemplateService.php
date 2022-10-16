<?php

namespace Modules\Core\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Modules\Core\Entities\ShopifyIntegration;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use PHPHtmlParser\Exceptions\UnknownChildTypeException;
use PHPHtmlParser\Selector\Parser;
use PHPHtmlParser\Selector\Selector;
use Slince\Shopify\Client;
use Slince\Shopify\Manager\Asset\Asset;
use Slince\Shopify\Manager\Theme\Theme;
use Slince\Shopify\PublicAppCredential;

class ShopifyTemplateService
{
    public const LAYOUT_THEME_LIQUID = 'layout/theme.liquid';

    public const SECTION_UTM = 'utm';

    public const SECTION_CART = 'cart';

    public const SECTION_SKIP_TO_CART = 'skip to cart';

    public const STUBS_TEMPLATE_UTM = 'CloudfoxUtmScriptSnnipet.liquid';

    public const STUBS_TEMPLATE_CART = 'CloudfoxCartScriptSnippet.liquid';

    public const STUBS_TEMPLATE_SKIP_TO_CART = 'CloudfoxSkipToCartScriptSnippet.liquid';

    private $stubsTemplateFolder = __DIR__ . './../../Shopify/Stubs/';

    private $cacheDir;

    private $credential;

    private $client;

    private $theme;

    private $skipToCart = false;

    public function __construct(string $urlStore, string $token, $getThemes = true)
    {
        if (!$this->cacheDir) {
            $cache = '/var/tmp';
            //$cache = storage_path();
        } else {
            $cache = $this->cacheDir;
        }

        $this->credential = new PublicAppCredential($token);
        $this->client = new Client(
            $urlStore,
            $this->credential,
            [
                'meta_cache_dir' => $cache // Metadata cache dir, required
            ]
        );

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
        return ''; //throwl
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
    }

    public function makeTemplateIntegration(ShopifyIntegration $shopifyIntegration, $domain, $theme)
    {
        $this->removeIntegrationInAllThemes();

        $this->setThemeByRole('main');

        $htmlBody = $this->getTemplateHtml();


        if (empty($htmlBody)) {
            return [
                'failed' => true,
                'message' => 'Problema ao refazer integração, template \'theme.liquid\' não encontrado'
            ];
        }

        $shopifyIntegration->update(
            [
                'theme_type' => $theme,
                'theme_name' => $this->getThemeName(),
                'theme_file' => $this::LAYOUT_THEME_LIQUID,
                'theme_html' => $htmlBody,
            ]
        );

        $shopifyIntegration->update(
            [
                'layout_theme_html' => $htmlBody,
            ]
        );

        $newHtmlBody = $this->insertScript(
            $htmlBody,
            self::SECTION_UTM,
            $domain->name,
            self::STUBS_TEMPLATE_UTM
        );
        $newHtmlBody = $this->insertScript(
            $newHtmlBody,
            self::SECTION_CART,
            $domain->name,
            self::STUBS_TEMPLATE_CART
        );

        if($shopifyIntegration->skip_to_cart) {
            $newHtmlBody = $this->insertScript(
                $newHtmlBody,
                self::SECTION_SKIP_TO_CART,
                $domain->name,
                self::STUBS_TEMPLATE_SKIP_TO_CART
            );
        }

        $this->updateTemplateLiquid($newHtmlBody);

        $shopifyIntegration->update(
            [
                'status' => $shopifyIntegration->present()->getStatus('approved'),
            ]
        );

        return [
            'failed' => false,
            'message' => 'Problema ao refazer integração, template \'theme.liquid\' não encontrado'
        ];
    }

    public function updateTemplateLiquid(string $newHtml, string $templateKeyName = self::LAYOUT_THEME_LIQUID)
    {
        if (!empty($this->theme)) {
            $asset = $this->client->getAssetManager()->update(
                $this->theme->getId(),
                [
                    "key" => $templateKeyName,
                    "value" => $newHtml,
                ]
            );

            if ($asset) {
                return true;
            }
        }

        return false; //throwl
    }


    public function insertScript(string $oldHtml, string $scriptName, string $domain, string $stubLiquidSnippet)
    {
        $html = $this->removeScript($oldHtml, $scriptName);

        $strPos = strpos($html, '</body>');

        $scriptFox = file_get_contents("{$this->stubsTemplateFolder}{$stubLiquidSnippet}");
        $scriptFox = $this->changeDomainSnippets($scriptFox, $domain);

        $newHtml = substr_replace($html, $scriptFox, $strPos, 0);

        return $newHtml;
    }

    public function removeScript(string $html, string $scriptName)
    {
        $starComment = "<!-- start cloudfox {$scriptName} script -->";
        $endComment = "<!-- end cloudfox {$scriptName} script -->";

        $startScriptPos = strpos($html, $starComment);
        $endScriptPos = strpos($html, $endComment);

        $countEndCharacters = strlen($endComment);

        if ($startScriptPos !== false) {
            //script já existe, remove
            $size = ($endScriptPos + $countEndCharacters) - $startScriptPos;

            $html = substr_replace($html, '', $startScriptPos, $size);
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
                    $htmlCart = $this->client->getAssetManager()
                        ->find($this->theme->getId(), $templateKeyName);

                    return $htmlCart->getValue();
                }
            }
        }

        return null; //throwl
    }

    public function removeIntegrationInAllThemes(): void
    {
        /*if(!foxutils()->isProduction()){
            return;
        }*/

        $themes = $this->getAllThemes();

        /** @var Theme $theme */
        foreach ($themes as $theme) {
            $this->setThemeById($theme->getId());

            $htmlBody = $this->getTemplateHtml();
            $htmlBody = $this->removeScript($htmlBody, self::SECTION_UTM);
            $htmlBody = $this->removeScript($htmlBody, self::SECTION_CART);
            $htmlBody = $this->removeScript($htmlBody, self::SECTION_SKIP_TO_CART);

            $this->updateTemplateLiquid($htmlBody);
        }
    }

    private function changeDomainSnippets($text, $domain, $oldDomain = 'localhost:8081')
    {
        return str_replace($oldDomain,$domain,$text);
    }
}
