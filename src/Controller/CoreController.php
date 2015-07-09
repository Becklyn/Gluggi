<?php


namespace BecklynLayout\Controller;

use BecklynLayout\Entity\Element;
use BecklynLayout\Model\DownloadModel;
use BecklynLayout\Model\ElementTypesModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CoreController
{
    /**
     * @var ElementTypesModel
     */
    private $elementTypesModel;


    /**
     * @var DownloadModel
     */
    private $downloadModel;


    /**
     * @var \Twig_Environment
     */
    private $twig;



    /**
     * @param ElementTypesModel $elementTypesModel
     * @param DownloadModel     $downloadModel
     * @param \Twig_Environment $twig
     */
    public function __construct (ElementTypesModel $elementTypesModel, DownloadModel $downloadModel, \Twig_Environment $twig)
    {
        $this->elementTypesModel = $elementTypesModel;
        $this->downloadModel     = $downloadModel;
        $this->twig              = $twig;
    }


    /**
     * Handles the index action
     *
     * @return string
     */
    public function indexAction ()
    {
        $layoutGroups = array_map(
            function ($elementType)
            {
                return [
                    "title"       => ucfirst($elementType) . "s",
                    "elementType" => $elementType,
                    "elements"    => $this->elementTypesModel->getListedElements($elementType),
                ];
            },
            $this->elementTypesModel->getAllElementTypes()
        );


        return $this->twig->render("@core/index.twig", [
            "layoutGroups" => $layoutGroups,
            "downloads"    => $this->downloadModel->getAllDownloads(),
        ]);
    }



    /**
     * Displays a preview file
     *
     * @param string $elementType
     * @param string $key
     *
     * @return string|Response
     */
    public function showElementAction ($elementType, $key)
    {
        try
        {
            $element = $this->elementTypesModel->getElement($key, $elementType);

            if ((null === $element) || $element->isHidden())
            {
                throw new NotFoundHttpException("Element '{$key}' of type '{$elementType}' not found.");
            }

            $templateSuffix = $this->elementTypesModel->isFullPageElementType($elementType) ? "_fullpage" : "";

            return $this->twig->render("@core/show_element{$templateSuffix}.twig", [
                "element" => $element
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            throw new NotFoundHttpException("Unknown element type '{$elementType}'.", $e);
        }
    }



    /**
     * Returns a list of all elements of the given type
     *
     * @param $elementType
     *
     * @return string
     */
    public function elementsOverviewAction ($elementType)
    {
        try
        {
            $elementReferences = array_map(
                function (Element $element)
                {
                    return $element->getReference();
                },
                $this->elementTypesModel->getListedElements($elementType)
            );

            return $this->twig->render("@core/elements_overview_page.twig", [
                "elementReferences" => $elementReferences
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            throw new NotFoundHttpException("Unknown element type '{$elementType}'.", $e);
        }
    }
}
