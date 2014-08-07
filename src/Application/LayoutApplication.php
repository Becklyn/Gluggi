<?php


namespace BecklynLayout\Application;

use BecklynLayout\Controller\CoreController;
use BecklynLayout\Model\TemplateListingModel;
use BecklynLayout\Twig\TwigExtension;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig_Environment;

/**
 * The main application
 */
class LayoutApplication extends Application
{
    /**
     * Bootstraps the complete application
     *
     * @param string $webDir the path to the web dir
     * @param array $config the config
     */
    public function bootstrap ($webDir, array $config = [])
    {
        $baseDir = dirname($webDir);
        $config  = $this->resolveConfig($config);

        $this->registerProviders();
        $this->registerCoreTwigNamespace();
        $this->registerModelsAndTwigLayoutNamespaces($baseDir);
        $this->registerControllers();
        $this->registerTwigExtensions($config);
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
        $this["controller.core"] = $this->share(function ()
            {
                return new CoreController($this["model.layout.preview"], $this["model.layout.page"], $this["twig"]);
            }
        );
    }


    /**
     * Registers all used twig extensions
     *
     * @param array $config
     */
    private function registerTwigExtensions (array $config)
    {
        $this['twig'] = $this->share($this->extend('twig',
                function (Twig_Environment $twig, Application $app) use ($config)
                {
                    // add custom extension
                    $twig->addExtension(new TwigExtension($app));

                    // add global gluggi variable
                    $twig->addGlobal("gluggi", [
                        "config" => $config
                    ]);

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
        $this->get("/",                  "controller.core:indexAction");
        $this->get("/preview/{preview}", "controller.core:previewAction")->bind("layout_preview");
        $this->get("/page/{page}",       "controller.core:pageAction")->bind("layout_page");
    }


    /**
     * Returns the resolved config
     *
     * @param array $config
     *
     * @return array
     */
    private function resolveConfig (array $config)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefaults([
            "title" => "Gluggi"
        ]);

        return $optionsResolver->resolve($config);
    }
}
