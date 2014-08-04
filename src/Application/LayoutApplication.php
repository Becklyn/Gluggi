<?php


namespace BecklynLayout\Application;

use BecklynLayout\Controller\PreviewController;
use BecklynLayout\Model\TemplateListingModel;
use BecklynLayout\Twig\TwigExtension;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Twig_Environment;

/**
 * The main application
 */
class LayoutApplication extends Application
{
    /**
     * {@inheritdoc}
     */
    public function __construct ($webDir, array $values = array())
    {
        parent::__construct($values);

        $this->bootstrap(dirname($webDir));
    }


    /**
     * Bootstraps the complete application
     *
     * @param string $baseDir the path to the web dir
     */
    private function bootstrap ($baseDir)
    {
        $this->registerProviders();
        $this->registerCoreTwigNamespace();
        $this->registerModelsAndTwigLayoutNamespaces($baseDir);
        $this->registerControllers();
        $this->registerTwigExtensions();
        $this->defineCoreRouting();
    }


    /**
     * Registers all used service providers
     */
    private function registerProviders ()
    {
        $this->register(new TwigServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new ServiceControllerServiceProvider());
    }


    /**
     * Registers the @core twig namespace
     */
    private function registerCoreTwigNamespace ()
    {
        $libDir = dirname(dirname(__DIR__)) . "/resources";

        // register core
        $this["twig.loader.filesystem"]->addPath("{$libDir}/views", "core");
    }


    /**
     * Registers all models and all twig layout namespaces
     *
     * @param string $baseDir
     */
    private function registerModelsAndTwigLayoutNamespaces ($baseDir)
    {
        $parts = [
            "component" => "{$baseDir}/layout/views/components",
            "layout"    => "{$baseDir}/layout/views/layouts",
            "page"      => "{$baseDir}/layout/views/pages",
            "preview"   => "{$baseDir}/layout/views"
        ];

        foreach ($parts as $partNamespace => $partBaseUrl)
        {
            // model
            $this["model.layout.{$partNamespace}"] = $this->share(
                function () use ($partBaseUrl, $partNamespace)
                {
                    return new TemplateListingModel($partBaseUrl, $partNamespace);
                }
            );

            // twig template namespace
            $this["twig.loader.filesystem"]->addPath($partBaseUrl, $partNamespace);
        }
    }


    /**
     * Registers all controllers
     */
    private function registerControllers ()
    {
        $this["controller.layout.preview"] = $this->share(function ()
            {
                return new PreviewController($this["model.layout.preview"], $this["twig"]);
            }
        );
    }


    /**
     * Registers all used twig extensions
     */
    private function registerTwigExtensions ()
    {
        $this['twig'] = $this->share($this->extend('twig',
                function (Twig_Environment $twig, Application $app)
                {
                    $twig->addExtension(new TwigExtension($app));

                    return $twig;
                }
            )
        );
    }


    /**
     * Defines the routes of the core app
     */
    private function defineCoreRouting ()
    {
        $this->get("/",                  "controller.layout.preview:indexAction");
        $this->get("/preview/{preview}", "controller.layout.preview:previewAction")->bind("layout_preview");
    }
}
