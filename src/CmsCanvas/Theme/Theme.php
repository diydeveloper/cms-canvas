<?php 

namespace CmsCanvas\Theme;

use View, Config, File, App, Admin;

class Theme {

    /**
     * The theme to be rendered
     * 
     * @var string
     */
    protected $theme;

    /**
     * The theme layout view
     * 
     * @var \Illuminate\View\View
     */
    protected $layout;

    /**
     * Content used in the HTML <title> tags
     * 
     * @var string
     */
    protected $metaTitle = '';

    /**
     * Content used in the HTML <meta name="description"> tags
     * 
     * @var string
     */
    protected $metaDescription = '';

    /**
     * Content used in the HTML <meta name="keywords"> tags
     * 
     * @var string
     */
    protected $metaKeywords = '';

    /**
     * Additinal content for the HTML <head> tags
     * 
     * @var string
     */
    protected $pageHead = '';

    /**
     * Javascripts requested to be included in the theme
     * 
     * @var array
     */
    protected $javascripts = [];

    /**
     * Inline scripts to be included in the theme
     * 
     * @var array
     */
    protected $inlineScripts = [];

    /**
     * Stylesheets requested to be included in the theme
     * 
     * @var array
     */
    protected $stylesheets = [];

    /**
     * Inline CSS to be included in the theme
     * 
     * @var array
     */
    protected $inlineCss = [];

    /**
     * Raw content requested to be included in the theme's footer
     * 
     * @var array
     */
    protected $footerContent = [];

    /**
     * Used to determine if HTML <head> data has already been rendered
     * 
     * @var array
     */
    protected $headersSent = false;

    /**
     * Tracks the order in which javascripts and inline scripts were added for header includes
     * 
     * @var array
     */
    protected $headerJavascriptOrder = [];

    /**
     * Tracks the order in which javascripts and inline scripts were added for footer includes
     * 
     * @var array
     */
    protected $footerJavascriptOrder = [];

    /**
     * Tracks the order in which stylesheets and inline scripts were added
     * 
     * @var array
     */
    protected $stylesheetOrder = [];

    /**
     * Array of cached theme paths
     * 
     * @var array
     */
    protected $themePaths = [];

    /**
     * Hint path delimiter value.
     *
     * @var string
     */
    const HINT_PATH_DELIMITER = '::';

    /**
     * CMSCanvas Theme Namespace
     *
     * @var string
     */
    const CMSCANVAS_THEME_NAMESPACE = 'cmscanvas';

    /**
     * Generates a URL for a theme asset
     *
     * @param string
     * @return string
     */
    public function asset($path = null, $theme = null)
    {
        if ($theme == null) {
            if ($this->theme == null) {
                throw new \Exception("A theme has not been specified or set.");
            }

            $theme = $this->theme;
        }

        return asset($this->getThemeAssetsPath($theme).ltrim($path, '/'));
    }

    /**
     * Returns the default theme that is set in the config
     *
     * @return string
     */
    public function getDefaultTheme()
    {
        return Config::get('cmscanvas::config.theme');
    }

    /**
     * Returns the default layout that is set in the config
     *
     * @return string
     */
    public function getDefaultLayout()
    {
        return Config::get('cmscanvas::config.layout');
    }

    /**
     * Sets the theme to render
     *
     * @param string
     * @return \CmsCanvas\Theme\Theme
     */
    public function setTheme($theme)
    {
        view()->addNamespace('theme', $this->getThemePath($theme).'/views/');

        $this->theme = $theme;

        return $this;
    }

    /**
     * Sets the theme layout to render
     *
     * @param string
     * @return \CmsCanvas\Theme\Theme
     */
    public function setLayout($layout)
    {
        $this->layout = View::make('theme::layouts.'.$layout, ['content' => '']);

        return $this;
    }

    /**
     * Returns the theme layout view
     *
     * @return \Illuminate\View\View
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Specifies the page title used in the metadata output
     *
     * @param string 
     * @return \CmsCanvas\Theme\Theme
     */
    public function setMetaTitle($title)
    {
        if (! empty($title)) {
            $this->metaTitle = $title;
        }

        return $this;
    }

    /**
     * Specifies the page description used in the metadata output
     *
     * @param string 
     * @return \CmsCanvas\Theme\Theme
     */
    public function setMetaDescription($description)
    {
        if (! empty($description)) {
            $this->metaDescription = $description;
        }

        return $this;
    }

    /**
     * Set Keywords
     *
     * Specifies the page keywords used in the metadata output
     *
     * @param string 
     * @return \CmsCanvas\Theme\Theme
     */
    public function setMetaKeywords($keywords)
    {
        if (! empty($keywords)) {
            $this->metaKeywords = $keywords;
        }

        return $this;
    }

    /**
     * Used to include custom JavaScript, CSS, meta information and/or PHP in the <head> block of the template
     *
     * @param string
     * @return \CmsCanvas\Theme\Theme
     */
    function addPageHead($code)
    {
        if (! empty($code)) {
            $this->pageHead = $code;
        }

        return $this;
    }

    /**
     * Used to build an array of external javascripts to include
     *
     * @param mixed
     * @param boolean $footer
     * @return \CmsCanvas\Theme\Theme
     */
    public function addJavascript($javascripts, $footer = false)
    {
        if (! is_array($javascripts)) {
            $javascripts = (array) $javascripts;
        }

        foreach ($javascripts as $javascript) {
            // If HTTP not in javascript uri add prepend url
            $javascript = (strpos($javascript, 'http') === 0 ? $javascript : url($javascript));

            if ( ! in_array($javascript, $this->javascripts)) {
                $this->javascripts[] = $javascript;
                $javascriptKeys = array_keys($this->javascripts);
                $index = end($javascriptKeys);

                // Determine where this script needs to be included
                // and keep track of the order in which javascripts and scripts are added
                if ($footer || $this->headersSent) {
                    $this->footerJavascriptOrder[] = [
                        'array' => 'javascripts',
                        'index' => $index,
                    ];
                } else {
                    $this->headerJavascriptOrder[] = [
                        'array' => 'javascripts',
                        'index' => $index,
                    ];
                }
            }
        }

        return $this;
    }

    /**
     * Add inline script
     *
     * @param mixed $scripts
     * @param boolean $distinct
     * @param boolean $footer
     * @return \CmsCanvas\Theme\Theme
     */
    public function addInlineScript($scripts, $distinct = false, $footer = false)
    {
        if (! is_array($scripts)) {
            $scripts = (array) $scripts;
        }

        foreach ($scripts as $javascript) {
            if ($distinct && in_array($javascript, $this->inlineScripts)) {
                continue;
            }

            $this->inlineScripts[] = $javascript;
            $inlineScriptKeys = array_keys($this->inlineScripts);
            $index = end($inlineScriptKeys);

            // Determine where this script needs to be included
            // and keep track of the order in which javascripts and scripts are added
            if ($footer || $this->headersSent) {
                $this->footerJavascriptOrder[] = [
                    'array' => 'scripts',
                    'index' => $index,
                ];
            } else {
                $this->headerJavascriptOrder[] = [
                    'array' => 'scripts',
                    'index' => $index,
                ];
            }
        }

        return $this;
    }

    /**
     * This function is used to build an array of external stylesheets to include
     *
     * @param mixed $stylesheets
     * @return \CmsCanas\Theme\Theme
     */
    public function addStylesheet($stylesheets)
    {
        if (! is_array($stylesheets)) {
            $stylesheets = (array) $stylesheets;
        }

        foreach ($stylesheets as $stylesheet) {
            $stylesheet = (strpos($stylesheet, 'http') === 0 ? $stylesheet : url($stylesheet));

            if (! in_array($stylesheet, $this->stylesheets)) {
                $this->stylesheets[] = $stylesheet;
                $stylesheetKeys = array_keys($this->stylesheets);
                $index = end($stylesheetKeys);

                // Keep track of the order in which stylesheets and css are added
                $this->stylesheetOrder[] = [
                    'array' => 'stylesheets',
                    'index' => $index,
                ];
            }
        }

        return $this;
    }

    /**
     * Used to build an array of inline CSS to include
     *
     * @param mixed $css
     * @param boolean $distinct
     * @return \CmsCanas\Theme\Theme
     */
    public function addInlineCss($css, $distinct = false)
    {
        if (! is_array($css)) {
            $css = (array) $css;
        }

        foreach ($css as $style) {
            if ($distinct && in_array($style, $this->inlineCss)) {
                continue;
            }

            $this->inlineCss[] = $style;
            $inlineCssKeys = array_keys($this->inlineCss);
            $index = end($inlineCssKeys);

            // Keep track of the order in which stylesheets and css are added
            $this->stylesheetOrder[] = [
                'array' => 'css',
                'index' => $index,
            ];
        }

        return $this;
    }

    /**
     * Used to add predefined sets of javascripts and stylesheets
     * defined in the assets config file.
     *
     * @param mixed
     * @return \CmsCanas\Theme\Theme
     */
    public function addPackage($packages)
    {
        $packageList = Config::get('cmscanvas::assets.packages');

        if (! is_array($packages)) {
            $packages = (array) $packages;
        }

        foreach ($packages as $package) {
            if (isset($packageList[$package])) {
                $package = $packageList[$package];

                if (isset($package['javascript'])) {
                    $this->addJavascript($package['javascript']);
                }

                if (isset($package['stylesheet'])) {
                    $this->addStylesheet($package['stylesheet']);
                }
            }
        }

        return $this;
    }

    /**
     * Commonly used in the <head> HTML tags of a theme file.
     * Outputs title, description, and keyword metadata.
     *
     * @return string
     */
    public function metadata()
    {
        $metadata = '';

        if (! empty($this->metaTitle)) {
            $metadata .= '<title>' . $this->metaTitle . '</title>' . "\r\n";
        }

        if (! empty($this->metaDescription)) {
            $metadata .= '<meta name="description" content="' . $this->metaDescription . '" />' . "\r\n";
        }

        if (! empty($this->metaKeywords)) {
            $metadata .= '<meta name="keywords" content="' . $this->metaKeywords . '" />' . "\r\n";
        }

        $this->headersSent = true;

        return $metadata;
    }

    /**
     * Commonly used in the <head> HTML tags of a theme file.
     * Outputs javascript includes from the javascript array.
     *
     * @param bool $footer
     * @return string
     */
    public function javascripts($footer = false)
    {
        $javascriptIncludes = "";

        if ($footer) {
            $javascriptOrderArray = 'footerJavascriptOrder';
        } else {
            $javascriptOrderArray = 'headerJavascriptOrder';
            $javascriptIncludes = "\n\t<script type=\"text/javascript\">var BASE_HREF=\"" . url('/') . "\"</script>";
        }

        foreach ($this->$javascriptOrderArray as $javascriptOrder) {
            if ($javascriptOrder['array'] == 'javascripts') {
                $javascriptIncludes .=  "\n\t<script type=\"text/javascript\" src=\"" . $this->javascripts[$javascriptOrder['index']] . "\"></script>";
            } elseif ($javascriptOrder['array'] == 'scripts') {
                $script = $this->inlineScripts[$javascriptOrder['index']];

                // Check if script has the script tags included
                if (stripos(trim($script), '<script') === 0) {
                    $javascriptIncludes .=  "\n" . $script;
                } else {
                    $javascriptIncludes .=  "\n\t<script type=\"text/javascript\">" . $script . "</script>";
                }
            }
        }

        if (! $footer) {
            $this->headersSent = true;
        }

        return $javascriptIncludes;
    }

    /**
     * Commonly used in the <head> HTML tags of a theme file
     * Outputs stylesheets includes from the stylesheet array
     *
     * @return string
     */
    public function stylesheets()
    {
        $cssIncludes = '';

        foreach ($this->stylesheetOrder as $cssOrder) {
            if ($cssOrder['array'] == 'stylesheets') {
                $cssIncludes .=  "\n\t<link href=\"" . $this->stylesheets[$cssOrder['index']] . "\" rel=\"stylesheet\" type=\"text/css\" />";
            } elseif ($cssOrder['array'] == 'css') {
                $style = $this->css[$cssOrder['index']];

                // Check if css has the script tags included
                if (stripos(trim($style), '<stle') === 0) {
                    $cssIncludes .=  "\n" . $style;
                } else {
                    $cssIncludes .=  "\n\t<style type=\"text/css\">" . $style . "</style>";
                }
            }
        }

        $this->headersSent = true;

        return $cssIncludes;
    }

    /**
     * Commonly used in the <head> HTML tags immediately before the closing </head> tag
     * Outputs javascript for google analytics
     * Google Analytic's Account ID is set in /application/config/custom_config.php
     *
     * @return string
     */
    public function analytics()
    {
        if (Config::get('cmscanvas::config.ga_tracking_id')) {

            return "<script>
                      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                      ga('create', '".Config::get('cmscanvas::config.ga_tracking_id')."', 'auto');
                      ga('send', 'pageview');

                    </script>";
        }
    }

    /**
     * Commonly used in the <head> HTML tags of a theme file.
     * Outputs additional page <head> code.
     *
     * @return string
     */
    public function pageHead()
    {
        $this->headersSent = true;

        return $this->pageHead;
    }

    /**
     * Commonly used in the <head> HTML tags of a theme file after the opening <head> tag.
     * Combines the outputs of metadata, stylesheets, javascripts, and analytics in one function
     *
     * @return string
     */
    public function head()
    {
        $return = '';
        $return .= $this->metadata();
        $return .= $this->stylesheets();
        $return .= $this->javascripts();
        $return .= $this->pageHead();
        $return .= $this->analytics();

        return $return;
    }

    /**
     * Commonly used in the footer of a theme file immediately before the closing </body> tag
     * Outputs the footer javascripts
     *
     * @return string
     */
    public function footer()
    {
        $return = '';
        foreach ($this->footerContent as $content) {
            $return .= $content;
        }
        $return .= $this->javascripts(true);

        return $return;
    }

    /**
     * Used to add raw content to the footer
     *
     * @param  string $content
     * @return self
     */
    public function addFooterContent($content)
    {
        $this->footerContent[] = $content;

        return $this;
    }

    /**
     * Get Themes
     *
     * Returns all themes located in the themes folder
     *
     * @return array
     */
    public function getThemes()
    {
        $themes = [];

        $cmsCanvasThemes = File::glob(rtrim(Config::get('cmscanvas::config.themes_directory'), '/').'/*', GLOB_ONLYDIR);

        if (is_array($cmsCanvasThemes)) {
            foreach($cmsCanvasThemes as $cmsCanvasTheme) {
                $themeName = basename($cmsCanvasTheme);
                $themes[$themeName] = $this->getFriendlyThemeName($themeName);
            }
        }

        $userThemes = File::glob(rtrim(Config::get('cmscanvas::config.app_themes_directory'), '/').'/*', GLOB_ONLYDIR);

        if (is_array($userThemes)) {
            foreach($userThemes as $userTheme) {
                $themeName = basename($userTheme);
                $themes[$themeName] = $this->getFriendlyThemeName($themeName);
            }
        }

        return $themes;
    }

    /**
     * Returns all of the layouts for the specified theme
     *
     * @return array
     */
    public function getThemeLayouts($theme = null)
    {
        if ($theme == null) {
            if ($this->theme == null) {
                throw new \Exception("A theme has not been specified or set.");
            }

            $theme = $this->theme;
        }

        $layouts = [];

        $themeLayouts = File::files($this->getThemePath($theme).'/views/layouts/');

        foreach ($themeLayouts as $themeLayout) {
            $filename = File::name($themeLayout);
            $filename = str_replace('.blade', '', $filename);
            $layouts[$filename] = $filename;
        }

        return $layouts;
    }

    /**
     * Prepends the admin toolbar to the body of the theme if enabled
     *
     * @param  mixed $resource
     * @return bool
     */
    public function includeAdminToolbar($resource)
    {
        if (!Admin::isAdminToolbarEnabled()) {
            return false;
        }

        $toolbar = view('cmscanvas::admin.adminToolbar')
            ->with(['resource' => $resource]);

        $this->addInlineScript("var ADMIN_URL = '".Admin::url('/')."';");
        $this->addInlineScript("var ADMIN_ASSETS = '".$this->asset('/', 'admin')."';");
        $this->addInlineScript("var CSRF_TOKEN = '".csrf_token()."';");
        $this->addPackage('admin_toolbar');
        $this->addFooterContent($toolbar);

        return true;
    }

    /**
     * Returns the preferred theme path
     *
     * @param string $theme
     * @return string
     */
    public function getThemePath($theme)
    {
        // Check if the theme path has already been cached
        if (isset($this->themePaths[$theme])) {
            return $this->themePaths[$theme];
        }

        $this->themePaths[$theme] = $this->getAppThemePath($theme);

        if (! App::make('files')->isDirectory($this->themePaths[$theme])) {
            $this->themePaths[$theme] = $this->getPackageThemePath($theme);
        }

        return $this->themePaths[$theme];
    }

    /**
     * Returns the theme path in the app folder
     *
     * @param string $theme
     * @return string
     */
    public function getAppThemePath($theme)
    {
        $themePath = Config::get('cmscanvas::config.app_themes_directory'); 

        return rtrim($themePath, '/').'/'.$theme;
    }

    /**
     * Returns the theme path in the package folder
     *
     * @param string $theme
     * @return string
     */
    public function getPackageThemePath($theme)
    {
        $themePath = Config::get('cmscanvas::config.themes_directory'); 

        return rtrim($themePath, '/').'/'.$theme;
    }

    /**
     * Returns the theme name minus the namespace
     *
     * @param string $theme
     * @return string
     */
    public function getThemeAssetsPath($theme)
    {
        $themeAssets = Config::get('cmscanvas::config.theme_assets'); 

        return rtrim($themeAssets, '/').'/'.$theme.'/';
    }

    /**
     * Returns human readable theme name with spaces
     *
     * @param string $theme
     * @return string
     */
    protected function getFriendlyThemeName($theme)
    {
        return ucwords(str_replace(['-', '_'], ' ', $theme));
    }

}
