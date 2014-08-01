<?php


namespace BecklynLayout\Controller;

use BecklynLayout\Model\LayoutPreview;
use Symfony\Component\HttpFoundation\Response;

class PreviewController
{
    /**
     * @var LayoutPreview
     */
    private $previewModel;

    private $twig;


    public function __construct (LayoutPreview $previewModel, \Twig_Environment $twig)
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
            "previews" => $this->previewModel->getAllPreviews()
        ]);
    }


    public function previewAction ($preview)
    {
        $previewData = $this->previewModel->getPreview($preview);

        if (null === $previewData)
        {
            return new Response("Preview not found.", 404);
        }

        return $this->twig->render("@preview/{$previewData['fileName']}");
    }
}
