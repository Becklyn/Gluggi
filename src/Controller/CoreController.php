<?php


namespace BecklynLayout\Controller;

use BecklynLayout\Entity\Element;
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
     * @var \Twig_Environment
     */
    private $twig;



    /**
     * @param ElementTypesModel $elementTypesModel
     * @param \Twig_Environment $twig
     */
    public function __construct (ElementTypesModel $elementTypesModel, \Twig_Environment $twig)
    {
        $this->elementTypesModel = $elementTypesModel;
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

            return $this->twig->render("@core/show_element.twig", [
                "element" => $element
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            throw new NotFoundHttpException("Unknown element type '{$elementType}'.", $e);
        }
    }



    /**
     * Returns a list of all atoms
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
