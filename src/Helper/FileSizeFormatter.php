<?php

namespace BecklynLayout\Helper;

/**
 *
 */
class FileSizeFormatter
{
    /**
     * @param float $bytes
     *
     * @return string
     */
    public function formatFileSize ($bytes)
    {
        $sz = 'BKMGTP';
        $factor = (int) floor((strlen($bytes) - 1) / 3);
        return number_format($bytes / pow(1024, $factor), 2, ",", ".") . @$sz[$factor];
    }
}
