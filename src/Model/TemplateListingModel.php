<?php


namespace BecklynLayout\Model;

use Silex\Application;

/**
 * Generic model which handles the template listing
 */
class TemplateListingModel
{
    /**
     * The dir containing the templates
     *
     * @var string
     */
    private $templatesDir;


    /**
     * @var string
     */
    private $twigNamespace;


    /**
     * @param string $templatesDir
     * @param string $twigNamespace
     */
    public function __construct ($templatesDir, $twigNamespace)
    {
        $this->templatesDir  = $templatesDir;
        $this->twigNamespace = $twigNamespace;
    }


    /**
     * Returns a list of all previews
     *
     * @return array
     */
    public function getAllTemplates ()
    {
        $iterator = new \DirectoryIterator($this->templatesDir);
        $previews = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file)
        {
            if ($file->isDir() || ("twig" !== $file->getExtension()))
            {
                continue;
            }

            $key            = $file->getBasename(".{$file->getExtension()}");
            $previews[$key] = [
                "key"       => $key,
                "fileName"  => $file->getBasename(),
                "title"     => ucwords(str_replace(["_", "-"], " ", $key)),
                "reference" => "@{$this->twigNamespace}/{$file->getBasename()}"
            ];
        }

        return $previews;
    }


    /**
     * Returns the preview by key
     *
     * @param string $key
     *
     * @return null|array
     */
    public function getTemplateDetails ($key)
    {
        $previews = $this->getAllTemplates();

        return array_key_exists($key, $previews)
            ? $previews[$key]
            : null;
    }


    /**
     * Returns a list of all template references, ordered alphabetically by file name
     *
     * @return string[]
     */
    public function getAllTemplateReferences ()
    {
        $templates = array_map(
            function ($templateInfo)
            {
                return $templateInfo["reference"];
            },
            $this->getAllTemplates()
        );

        natcasesort($templates);
        return $templates;
    }
}
