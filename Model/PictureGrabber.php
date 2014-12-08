<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 08/12/2014
 * Time: 12:25
 */

namespace Rudak\PictureGrabber\Model;


class PictureGrabber
{

    private $fileName;
    private $fileName_length;
    private $dir;
    private $url;
    private $prefix;

    public function __construct($url, $dir, $prefix = null, $fileName_length = 5)
    {
        $this->url             = $url;
        $this->dir             = '../../../../../../../web/' . $dir;
        $this->prefix          = $prefix;
        $this->fileName_length = $fileName_length;
    }

    public function getImage()
    {
        $this->setFileName();
        $path = $this->getAbsoluteFilePath();

        if ($this->checkDir(dirname($path))) {
            if ($this->createFile($path)) {
                $ch = curl_init($this->url);
                $fp = fopen($path, 'wb+');

                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);

                #TODO : verifier poids image recue

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    private function checkDir($dir)
    {
        if (!file_exists($dir)) {
            return false;
        }
        if (!is_writable($dir)) {
            return false;
        }
        return true;
    }

    private function createFile($path)
    {
        try {
            $cf = fopen($path, 'w+');
            if (!$cf) {
                return false;
            }
            return 'fichier créé';
        } catch (\Exception $e) {
            return false;
        }
    }

    private function setFileName()
    {
        $str            = 'azertyuiopqsdfghjklmwxcvbn0123456789';
        $shuffle        = str_shuffle($str);
        $this->fileName = $this->prefix . substr($shuffle, 0, $this->fileName_length) . '.jpg';
    }

    private function getAbsoluteFilePath()
    {
        return $this->getAbsoluteDirPath() . '/' . $this->fileName;
    }

    private function getAbsoluteDirPath()
    {
        return __DIR__ . $this->dir;
    }
} 