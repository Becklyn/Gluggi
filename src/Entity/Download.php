<?php

namespace BecklynLayout\Entity;

use BecklynLayout\Helper\FileSizeFormatter;


/**
 *
 */
class Download
{
    /**
     * @var int
     */
    private $size;


    /**
     * @var string
     */
    private $fileName;


    /**
     * @var \DateTime
     */
    private $lastModified;


    /**
     * @var string
     */
    private $url;



    /**
     * @param \SplFileInfo $fileInfo
     * @param string       $downloadDirUrl
     */
    public function __construct (\SplFileInfo $fileInfo, $downloadDirUrl)
    {
        $this->size         = $fileInfo->getSize();
        $this->fileName     = $fileInfo->getBasename();
        $this->lastModified = \DateTime::createFromFormat("U", $fileInfo->getMTime());
        $this->url         = "{$downloadDirUrl}/" . rawurlencode($this->fileName);
    }



    /**
     * @return string
     */
    public function getFileName ()
    {
        return $this->fileName;
    }



    /**
     * @return string
     */
    public function getSize ()
    {
        $fileSizeHelper = new FileSizeFormatter();
        return $fileSizeHelper->formatFileSize($this->size);
    }



    /**
     * @return \DateTime
     */
    public function getLastModified ()
    {
        return $this->lastModified;
    }



    /**
     * @return string
     */
    public function getUrl ()
    {
        return $this->url;
    }
}
