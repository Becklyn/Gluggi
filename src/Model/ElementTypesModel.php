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
        "atom" => [
            "isFullPage" => false,
        ],
        "molecule" => [
            "isFullPage" => false,
        ],
        "organism" => [
            "isFullPage" => false,
        ],
        "template" => [
            "isFullPage" => true,
        ],
        "page" => [
            "isFullPage" => true,
        ],
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
        $iterator = new \DirectoryIterator($this->getUserSubDirectory("{$elementType}s"));
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
        return array_keys($this->elementTypes);
    }



    /**
     * Returns the subdirectory in the user's template dir
     *
     * @param string $directoryName
     *
     * @return string the template dir without the trailing slash
     */
    public function getUserSubDirectory ($directoryName)
    {
        return "{$this->baseDir}/layout/views/{$directoryName}";
    }



    /**
     * Returns whether the given element type is full page
     *
     * @param string $key
     *
     * @return bool
     */
    public function isFullPageElementType ($key)
    {
        return array_key_exists($key, $this->elementTypes)
            ? $this->elementTypes[$key]["isFullPage"]
            : null;
    }
}
