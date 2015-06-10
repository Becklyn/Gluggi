<?php

namespace BecklynLayout\Entity;

/**
 * An element (atom, molecule, etc...)
 */
class Element
{
    //region Fields
    /**
     * @var string
     */
    private $key;


    /**
     * @var string
     */
    private $title;


    /**
     * @var string
     */
    private $reference;


    /**
     * @var string
     */
    private $elementType;


    /**
     * @var bool
     */
    private $hidden;
    //endregion



    /**
     * @param string $reference
     */
    public function __construct ($reference)
    {
        if (1 === preg_match("/^@(?P<elementType>[^\\/]+?)\\/(?P<key>[^\\/]+?)\\.twig$/", $reference, $matches))
        {
            $this->key = $matches["key"];
            $this->elementType = $matches["elementType"];
            $this->title = ucwords(str_replace(["_", "-"], " ", $this->key));
            $this->reference = $reference;
            $this->hidden = 0 === strpos($this->key, "_");
        }
        else
        {
            throw new \InvalidArgumentException("Could not determine element from reference '{$reference}'");
        }
    }



    //region Accessors
    /**
     * @return string
     */
    public function getKey ()
    {
        return $this->key;
    }



    /**
     * @return string
     */
    public function getTitle ()
    {
        return $this->title;
    }



    /**
     * @return string
     */
    public function getReference ()
    {
        return $this->reference;
    }



    /**
     * @return string
     */
    public function getElementType ()
    {
        return $this->elementType;
    }



    /**
     * @return boolean
     */
    public function isHidden ()
    {
        return $this->hidden;
    }



    /**
     * Returns the id of the element
     *
     * @return mixed
     */
    public function getId ()
    {
        $replace = [
            "@"     => "",
            "/"     => "-",
            ".twig" => ""
        ];

        return str_replace(array_keys($replace), array_values($replace), $this->reference);
    }



    /**
     * Returns the full title
     *
     * @return string
     */
    public function getFullTitle ()
    {
        return ucfirst($this->elementType) . ": {$this->title}";
    }
    //endregion
}
