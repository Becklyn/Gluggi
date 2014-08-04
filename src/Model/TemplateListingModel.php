<?php


namespace BecklynLayout\Model;


use Silex\Application;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateListingModel
{


    /**
     * The base dir of the application
     *
     * @var string
     */
    private $baseDir;


    /**
     * @var string
     */
    private $twigNamespace;


    /**
     * @param string $baseDir
     * @param string $twigNamespace
     */
    public function __construct ($baseDir, $twigNamespace)
    {
        $this->baseDir       = $baseDir;
        $this->twigNamespace = $twigNamespace;
    }


    /**
     * Returns a list of all previews
     *
     * @return array
     */
    public function getAllTemplates ()
    {
        if (!is_dir("{$this->baseDir}/layout/views"))
        {
            return [];
        }

        $iterator = new \DirectoryIterator("{$this->baseDir}/layout/views");
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
     * Returns a list of all template references
     *
     * @return string[]
     */
    public function getAllTemplateReferences ()
    {
        return array_map(
            function ($templateInfo)
            {
                return $templateInfo["reference"];
            },
            $this->getAllTemplates()
        );
    }
}
