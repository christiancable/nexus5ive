<?php

namespace App\Nexus2\Helpers;

class Key
{
    /**
     * GetKeyForFile
     *
     * @param mixed  $file     - the filename we want the key for
     * @param string $location - the filename of the current menu or article
     *
     * @return string the key for $file as an absolute path without dots
     */
    public static function getKeyForFile($file, $location, $bbsroot = '')
    {
        // is this an absolute path?
        $isAbsolute = false;
        if (in_array(substr($file, 0, 1), ['/','\\'])) {
            $key = $file;
            $isAbsolute = true;
        } else {
            $key = dirname($location) . DIRECTORY_SEPARATOR . ($file);
        }

        // normalise the directory_separator
        $key = strtr(
            $key,
            ['/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR]
        );

        $bbsroot = strtr(
            $bbsroot,
            ['/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR]
        );

        // deal with dots if we have them
        $keyChunks = [];
        $chunks = array_filter(explode(DIRECTORY_SEPARATOR, $key), 'strlen');
        foreach ($chunks as $chunk) {
            if ('..' === $chunk) {
                array_pop($keyChunks);
                continue;
            }
            $keyChunks[] = $chunk;
        }
        $key = implode(DIRECTORY_SEPARATOR, $keyChunks);
        if ($isAbsolute) {
            $key = DIRECTORY_SEPARATOR . $key;
        }

        // if we have a bbsroot then append that to absolute keys
        if (('' != $bbsroot) && ($isAbsolute)) {
            $key = $bbsroot . DIRECTORY_SEPARATOR . ltrim($key, DIRECTORY_SEPARATOR);
        }
        return $key;
    }
}
