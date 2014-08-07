<?php

namespace BecklynLayout\Twig;

use Silex\Application;

/**
 * The twig extensions for the core
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * @var Application
     */
    private $application;


    /**
     * @param Application $application
     */
    public function __construct (Application $application)
    {
        $this->application = $application;
    }


    /**
     * Generates a path to a layout asset
     *
     * @param string $asset
     *
     * @return string
     */
    public function asset ($asset)
    {
        return $this->application["request"]->getBasePath() . "/assets/" . ltrim($asset, "/");
    }


    /**
     * Generates a path to a content asset
     *
     * @param string $asset
     *
     * @return string
     */
    public function content ($asset)
    {
        return $this->application["request"]->getBasePath() . "/content/" . ltrim($asset, "/");
    }


    /**
     * Generates a path to a core asset
     *
     * @param string $asset
     *
     * @return string
     */
    public function coreAsset ($asset)
    {
        return $this->application["request"]->getBasePath() . "/core/" . ltrim($asset, "/");
    }


    /**
     * Renders a template list
     *
     * @param string[] $list
     * @param array    $options
     *
     * @return
     */
    public function templateList (array $list, array $options = [])
    {
        $options = array_replace([
            "fullScreen" => true
        ], $options);

        return $this->application["twig"]->render("@core/templateList.twig", [
            "templates" => $list,
            "options" => $options
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions ()
    {
        return [
            new \Twig_SimpleFunction("asset",         [$this, "asset"]),
            new \Twig_SimpleFunction("content",       [$this, "content"]),
            new \Twig_SimpleFunction("coreAsset",     [$this, "coreAsset"]),
            new \Twig_SimpleFunction("templateList",  [$this, "templateList"], ["is_safe" => ["html"]]),
            new \Twig_SimpleFunction("allComponents", [$this->application["model.layout.component"], "getAllTemplateReferences"]),
            new \Twig_SimpleFunction("allLayouts",    [$this->application["model.layout.layout"], "getAllTemplateReferences"]),
            new \Twig_SimpleFunction("allPages",      [$this->application["model.layout.page"], "getAllTemplateReferences"]),
        ];
    }


    /**
     * Returns an anchor-compatible id
     *
     * @param string $template
     *
     * @return string
     */
    public function filterAnchor ($template)
    {
        $replace = [
            "@"     => "",
            "/"     => "-",
            ".twig" => ""
        ];

        return str_replace(array_keys($replace), array_values($replace), $template);
    }


    /**
     * Returns a title for this component
     *
     * @param string $template
     *
     * @return string
     */
    public function filterTitle ($template)
    {
        if (1 === preg_match("~^@(?P<type>.*?)\\/(?P<name>.*?)\\.twig$~", $template, $matches))
        {
            return ucwords($matches["type"]) . ": " . ucwords(str_replace(["_", "-"], " ", $matches["name"]));
        }

        return $template;
    }


    /**
     * {@inheritdoc}
     */
    public function getFilters ()
    {
        return [
            new \Twig_SimpleFilter("preview_anchor", [$this, "filterAnchor"]),
            new \Twig_SimpleFilter("preview_title",  [$this, "filterTitle"]),
        ];
    }



    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName ()
    {
        return __CLASS__;
    }
}
