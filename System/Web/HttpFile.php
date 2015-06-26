<?php

namespace System\Web;

final class HttpFile{

    private $fileName;
    private $tmpFileName;
    private $contentType;
    private $size;
    
    public function setFileName($fileName){
        $this->fileName = $fileName;
    }
    
    public function getFileName(){
        return $this->fileName;
    }
    
    public function setTmpFileName($tmpFileName){
        $this->tmpFileName = $tmpFileName;
    }
    
    public function getTmpFileName(){
        return $this->tmpFileName;
    }
    
    public function setContentType($contentType){
        $this->contentType = $contentType;
    }
    
    public function getContentType(){
        return $this->contentType;
    }
    
    public function setSize($size){
        $this->size = $size;
    }
    
    public function getSize(){
        return $this->size;
    }
    
    public function save($destination){
        return move_uploaded_file($this->tmpFileName, $destination);
    }
}

?>