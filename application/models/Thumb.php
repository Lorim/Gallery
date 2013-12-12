<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Thumb
{
    static public function getThumb($sPath)
    {
        $path = dirname(APPLICATION_PATH."/../public" .$sPath);
        $file = basename($sPath);
        if(!file_exists($path."/".$file)) return;
        try {
            if(!file_exists($path."/thumb")) {
                mkdir($path."/thumb");
            }
            if(!file_exists($path. "/thumb/".$file)) {
                $thumb = Application_Thumb_Factory::create($path."/".$file); 
                $thumb->resize(188,280)->save($path. "/thumb/".$file);
            }
        }  catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
            return $sPath;
        }
        return dirname($sPath). "/thumb/".$file;
    }
}