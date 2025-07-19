<?php

class Zip extends ZipArchive {
    public function add($path, $relative = null)
    {
        if (!file_exists($path)) {
            return false;
        }
        
        $file = new SplFileInfo($path);
        $path = $file->getPathname();
        $pathRelative = $path;

        if ($relative) {
            $pathRelative = substr($path, strlen((string) $relative));
        }
    
        if ($file->isFile()) {
            $this->addFile($path, $pathRelative);
        }
        
        if ($file->isDir()) {
            $this->addEmptyDir($pathRelative);
        }
    }
}

