<?php


namespace BecklynLayout\Controller;

use BecklynLayout\Model\TemplateListingModel;
use Symfony\Component\HttpFoundation\Response;

class CoreController
{
    /**
     * @var TemplateListingModel
     */
    private $previewModel;


    /**
     * @var TemplateListingModel
     */
    private $pageModel;


    /**
     * @var \Twig_Environment
     */
    private $twig;


    public function __construct (TemplateListingModel $previewModel, TemplateListingModel $pageModel, \Twig_Environment $twig)
    {
        $this->previewModel = $previewModel;
        $this->pageModel    = $pageModel;
        $this->twig         = $twig;
    }


    /**
     * Handles the index action
     *
     * @return string
     */
    public function indexAction ()
    {
        return $this->twig->render("@core/index.twig", [
            "previews" => $this->previewModel->getAllTemplates(),
            "pages"    => $this->pageModel->getAllTemplates()
        ]);
    }


    /**
     * Displays a preview file
     *
     * @param string $preview
     *
     * @return string|Response
     */
    public function previewAction ($preview)
    {
        $previewData = $this->previewModel->getTemplateDetails($preview);

        if (null === $previewData)
        {
            return new Response("Preview not found.", 404);
        }

        return $this->twig->render($previewData['reference']);
    }


    /**
     * Displays a page
     *
     * @param string $page
     *
     * @return string|Response
     */
    public function pageAction ($page)
    {
        $previewData = $this->pageModel->getTemplateDetails($page);

        if (null === $previewData)
        {
            return new Response("Preview not found.", 404);
        }

        return $this->twig->render($previewData['reference']);
    }
}
