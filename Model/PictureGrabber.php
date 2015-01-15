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
    private $http;
    private $content_length;
    private $error;

    public function __construct($url, $dir, $prefix = null, $fileName_length = 5)
    {
        $this->url             = $url;
        //$this->dir             = '../../../../../../../web/' . $dir;
        $this->dir             = $dir;
        $this->prefix          = $prefix;
        $this->fileName_length = ($fileName_length<=36)?$fileName_length:36;
    }

    public function getImage($filename = null)
    {
        if(null === $filename){
            $this->setFileName();
        } else {
            $this->fileName = $filename;
        }
        
        $path = $this->getAbsoluteFilePath();

        if ($this->checkDir($this->dir)) {
            if ($this->createFile($path)) {
                $this->http = null;
                while ($this->http != 200) {
                    $ch = curl_init($this->url);
                    $fp = fopen($path, 'wb+');

                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    if (false === curl_exec($ch)) {
                        $this->error = curl_error($ch);
                    }

                    $this->http           = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $this->content_length = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

                    curl_close($ch);
                    fclose($fp);
                }
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
            $this->error = 'Le dossier '.$dir.' n\'existe pas';
            return false;
        }
        if (!is_writable($dir)) {
            $this->error = 'Le dossier '.$dir.' n\'est pas ecrivable^^';
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
            $this->error = 'Impossible de creer ' . $path;
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
        return $this->getAbsoluteDirPath() .DIRECTORY_SEPARATOR. $this->fileName;
    }

    private function getAbsoluteDirPath()
    {
        return realpath($this->dir);
    }

    /**
     * @return mixed
     */
    public function getHttp()
    {
        return $this->http;
    }

    /**
     * @return mixed
     */
    public function getContentLength()
    {
        return $this->content_length;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
} 