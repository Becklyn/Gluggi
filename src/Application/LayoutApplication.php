<?php


namespace BecklynLayout\Application;

use BecklynLayout\Controller\CoreController;
use BecklynLayout\Model\DownloadModel;
use BecklynLayout\Model\ElementTypesModel;
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
        $this["gluggi.config"] = $this->resolveConfig($config);

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
        $this["twig.loader.filesystem"]->addPath(__DIR__ . "/../../resources/views", "core");
    }


    /**
     * Registers all models and all twig layout namespaces
     *
     * @param string $baseDir
     */
    private function registerModelsAndTwigLayoutNamespaces ($baseDir)
    {
        $elementTypesModel = new ElementTypesModel($baseDir);

        // model
        $this["model.element_types"] = $elementTypesModel;
        $this["model.download"]      = new DownloadModel($baseDir);

        // twig template namespaces
        foreach ($elementTypesModel->getAllElementTypes() as $elementType)
        {
            $this["twig.loader.filesystem"]->addPath($elementTypesModel->getUserSubDirectory("{$elementType}s"), $elementType);
        }


        $this["twig.loader.filesystem"]->addPath($elementTypesModel->getUserSubDirectory("_base"), "base");
    }


    /**
     * Registers all controllers
     */
    private function registerControllers ()
    {
        $this["controller.core"] = $this->share(function ()
            {
                return new CoreController($this["model.element_types"], $this["model.download"], $this["twig"]);
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
                    // add custom extension
                    $twig->addExtension(new TwigExtension($app));

                    // add global gluggi variable
                    $twig->addGlobal("gluggi", [
                        "config" => $this["gluggi.config"]
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
        $this->get("/",                    "controller.core:indexAction")->bind("index");
        $this->get("/all/{elementType}",   "controller.core:elementsOverviewAction")->bind("elements_overview");
        $this->get("/{elementType}/{key}", "controller.core:showElementAction")->bind("element");
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
            "title" => "Gluggi",
            "base_template" => "@core/base.twig",
        ]);

        return $optionsResolver->resolve($config);
    }
}
