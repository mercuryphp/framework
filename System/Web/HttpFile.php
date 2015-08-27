<?php

namespace System\Web;

final class HttpFile {

    private $fileName;
    private $tmpFileName;
    private $contentType;
    private $size;
    
    /**
     * Initializes a new instance of the HttpFile class and encapsulates 
     * information about an individual file that has been uploaded by a client.
     * 
     * @method  __construct
     * @param   string $fileName
     * @param   string $tmpName
     * @param   string $contentType
     * @param   string $size
     */
    public function __construct($fileName, $tmpName, $contentType, $size){
        $this->fileName = $fileName;
        $this->tmpFileName = $tmpName;
        $this->contentType = $contentType;
        $this->size = $size;
    }
    
    /**
     * Gets the name of the file being uploaded.
     * 
     * @method  getFileName
     * @return  string
     */
    public function getFileName(){
        return $this->fileName;
    }

    /**
     * Gets the temporary name of the file being uploaded.
     * 
     * @method  getTmpFileName
     * @return  string
     */
    public function getTmpFileName(){
        return $this->tmpFileName;
    }

    /**
     * Gets the content type of the file being uploaded.
     * 
     * @method  getContentType
     * @return  string
     */
    public function getContentType(){
        return $this->contentType;
    }

    /**
     * Gets the size of the file being uploaded.
     * 
     * @method  getSize
     * @return  int
     */
    public function getSize(){
        return (int)$this->size;
    }
    
    /**
     * Gets the file extension of the file being uploaded.
     * 
     * @method  getFileExtension
     * @return  string
     */
    public function getFileExtension(){
        return substr($this->fileName, strripos($this->fileName, '.')+1);
    }
    
    /**
     * Saves the file being uploaded.
     * 
     * @method  save
     * @param   string $destination
     */
    public function save($destination){
        return move_uploaded_file($this->tmpFileName, $destination);
    }
}