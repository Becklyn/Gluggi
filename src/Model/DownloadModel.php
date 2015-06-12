<?php

namespace BecklynLayout\Model;

use BecklynLayout\Entity\Download;


/**
 *
 */
class DownloadModel
{
    /**
     * @var string
     */
    private $downloadDir;


    /**
     * Relative to web/
     */
    const RELATIVE_DOWNLOAD_DIR = "downloads";



    /**
     * @param $baseDir
     */
    public function __construct ($baseDir)
    {
        $this->downloadDir = rtrim($baseDir, "/") . "/web/" . self::RELATIVE_DOWNLOAD_DIR;
    }



    /**
     * Returns a list of all downloads
     *
     * @return Download[]
     */
    public function getAllDownloads ()
    {
        try
        {
            $directoryIterator = new \DirectoryIterator($this->downloadDir);
            $downloads         = [];

            foreach ($directoryIterator as $file)
            {
                if (!$file->isFile())
                {
                    continue;
                }

                // skip hidden files
                if ("." === $file->getBasename()[0])
                {
                    continue;
                }

                $downloads[$file->getFilename()] = new Download($file, self::RELATIVE_DOWNLOAD_DIR);
            }

            ksort($downloads);
            return $downloads;
        }
        catch (\UnexpectedValueException $e)
        {
            return [];
        }
    }
}
