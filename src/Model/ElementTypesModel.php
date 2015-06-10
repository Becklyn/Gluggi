<?php

namespace BecklynLayout\Model;

use BecklynLayout\Entity\Element;
use Silex\Application;


/**
 * Generic model which handles the template listing
 */
class ElementTypesModel
{
    /**
     * The available layout types
     *
     * @var string[]
     */
    private $elementTypes = [
        "atom",
        "molecule",
        "organism",
        "template",
        "page",
    ];


    /**
     * The base dir containing the templates
     *
     * @var string
     */
    private $baseDir;



    /**
     * @param string $baseDir
     */
    public function __construct ($baseDir)
    {
        $this->baseDir = rtrim($baseDir, "/");
    }



    /**
     * Returns a list of all elements
     *
     * @param string $elementType
     *
     * @return Element[]
     */
    public function getAllElements ($elementType)
    {
        $iterator = new \DirectoryIterator($this->getTemplateDirOfElementType($elementType));
        $allElements = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file)
        {
            if ($file->isDir() || ("twig" !== $file->getExtension()))
            {
                continue;
            }

            $reference = "@{$elementType}/{$file->getBasename()}";
            $element = new Element($reference);
            $allElements[$element->getKey()] = $element;
        }

        return $allElements;
    }



    /**
     * Returns the listed elements
     *
     * @param string $elementType
     *
     * @return Element[]
     */
    public function getListedElements ($elementType)
    {
        return array_filter(
            $this->getAllElements($elementType),
            function (Element $element)
            {
                return !$element->isHidden();
            }
        );
    }



    /**
     * Returns the preview by key
     *
     * @param string $key
     * @param string $elementType
     *
     * @return Element|null
     */
    public function getElement ($key, $elementType)
    {
        $elements = $this->getAllElements($elementType);

        return array_key_exists($key, $elements)
            ? $elements[$key]
            : null;
    }



    /**
     * Returns a list of all element types
     *
     * @return string[]
     */
    public function getAllElementTypes ()
    {
        return $this->elementTypes;
    }



    /**
     * Returns the template dir of an element type
     *
     * @param string $elementType
     *
     * @return string the template dir without the trailing slash
     */
    public function getTemplateDirOfElementType ($elementType)
    {
        if (!in_array($elementType, $this->elementTypes, true))
        {
            throw new \InvalidArgumentException("Unknown element type: '{$elementType}'");
        }

        return "{$this->baseDir}/layout/views/{$elementType}s";
    }
}
