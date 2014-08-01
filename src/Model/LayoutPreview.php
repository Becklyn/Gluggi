<?php


namespace BecklynLayout\Model;


use Silex\Application;
use Symfony\Component\HttpKernel\KernelInterface;

class LayoutPreview
{
    /**
     * Returns a list of all previews
     *
     * @return array
     */
    public function getAllPreviews ()
    {
        $iterator = new \DirectoryIterator(BASE_DIR . "/layout/views");
        $previews = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file)
        {
            if ($file->isDir() || ("twig" !== $file->getExtension()))
            {
                continue;
            }

            $key = $file->getBasename(".{$file->getExtension()}");
            $previews[$key] = [
                "key"      => $key,
                "fileName" => $file->getBasename(),
                "title"    => ucwords(str_replace(["_", "-"], " ", $key))
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
    public function getPreview ($key)
    {
        $previews = $this->getAllPreviews();

        return array_key_exists($key, $previews)
            ? $previews[$key]
            : null;
    }
}
