<?php


namespace BecklynLayout\Controller;

use BecklynLayout\Model\TemplateListingModel;
use Symfony\Component\HttpFoundation\Response;

class PreviewController
{
    /**
     * @var TemplateListingModel
     */
    private $previewModel;

    private $twig;


    public function __construct (TemplateListingModel $previewModel, \Twig_Environment $twig)
    {
        $this->previewModel = $previewModel;
        $this->twig = $twig;
    }


    /**
     * Handles the index action
     *
     * @return string
     */
    public function indexAction ()
    {
        return $this->twig->render("@core/index.twig", [
            "previews" => $this->previewModel->getAllTemplates()
        ]);
    }


    public function previewAction ($preview)
    {
        $previewData = $this->previewModel->getTemplateDetails($preview);

        if (null === $previewData)
        {
            return new Response("Preview not found.", 404);
        }

        return $this->twig->render("@preview/{$previewData['fileName']}");
    }
}
