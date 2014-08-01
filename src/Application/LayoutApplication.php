<?php


namespace BecklynLayout\Application;

use BecklynLayout\Controller\PreviewController;
use BecklynLayout\Model\LayoutPreview;
use BecklynLayout\Twig\TwigExtension;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Twig_Environment;

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
        // register providers
        $this->register(new TwigServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new ServiceControllerServiceProvider());

        // register template paths
        $libDir = dirname(dirname(__DIR__)) . "/resources";
        $this["twig.loader.filesystem"]->addPath($libDir . "/views",                    "core");
        $this["twig.loader.filesystem"]->addPath($baseDir . "/layout/views/components", "component");
        $this["twig.loader.filesystem"]->addPath($baseDir . "/layout/views/layouts",    "layout");
        $this["twig.loader.filesystem"]->addPath($baseDir . "/layout/views/pages",      "page");
        $this["twig.loader.filesystem"]->addPath($baseDir . "/layout/views",            "preview");

        // register model
        $this["model.layout.preview"] = $this->share(function () use ($baseDir)
        {
            return new LayoutPreview($baseDir);
        });

        // register controllers
        $this["controller.preview"] = $this->share(function ()
        {
            return new PreviewController($this["model.layout.preview"], $this["twig"]);
        });

        // add twig extensions
        $this['twig'] = $this->share($this->extend('twig',
            function(Twig_Environment $twig, Application $app)
            {
                $twig->addExtension(new TwigExtension($app));
                return $twig;
            }
        ));


        // define routing
        $this->get("/", "controller.preview:indexAction");
        $this->get("/preview/{preview}", "controller.preview:previewAction")->bind("layout_preview");
    }
}
