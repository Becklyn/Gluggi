<?php


namespace BecklynLayout\Composer;

use Symfony\Component\Filesystem\Filesystem;
use Composer\Script\Event;

/**
 *
 */
class ScriptHandler
{
    /**
     * Installs the assets as part of the composer installation/update process
     */
    public static function installAssets (Event $event)
    {
        $webDir     = "web/";
        $currentDir = getcwd();
        $io         = $event->getIO();

        $io->write("");
        $io->write("Gluggi: Install core assets");
        $io->write("~~~~~~~~~~~~~~~~~~~~~~~~~~~");

        if (!is_dir($webDir))
        {
            $io->write("The web-dir ({$webDir}) was not found in {$currentDir}, can not dump assets.");
            $io->write("");
            return;
        }

        $webAssetsDir = "{$webDir}core/";
        $fileSystem   = new Filesystem();

        if (file_exists($webAssetsDir))
        {
            $fileSystem->remove($webAssetsDir);
            $io->write("Existing core assets dir removed.");
        }

        $assetsDir = dirname(dirname(__DIR__)) . "/resources/public";
        $fileSystem->mirror($assetsDir, $webAssetsDir);
        $io->write("Core assets successfully installed.");
        $io->write("");
    }
}
