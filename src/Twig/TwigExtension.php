<?php

namespace BecklynLayout\Twig;

use BecklynLayout\Entity\Element;
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
     * Renders a overview of the given elements
     *
     * @param string[] $list
     * @param array    $options
     *
     * @return
     */
    public function elementsOverview (array $list, array $options = [])
    {
        $options = array_replace(
            [
                "includeNavigation" => true
            ],
            $options
        );

        // this is a flag which tells the component that it is rendered in a template list
        $options["inElementList"] = true;

        $elements = array_map(
            function ($reference)
            {
                return new Element($reference);
            },
            $list
        );


        return $this->application["twig"]->render("@core/elements_overview.twig", [
            "elements" => $elements,
            "options" => $options
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions ()
    {
        return [
            new \Twig_SimpleFunction("asset",            [$this, "asset"]),
            new \Twig_SimpleFunction("content",          [$this, "content"]),
            new \Twig_SimpleFunction("coreAsset",        [$this, "coreAsset"]),
            new \Twig_SimpleFunction("elementsOverview", [$this, "elementsOverview"], ["is_safe" => ["html"]]),
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
