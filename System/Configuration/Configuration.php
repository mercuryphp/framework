<?php

namespace System\Configuration;

class Configuration extends ConfigurationReader {
    
    protected $sessionSection;
    protected $namespaces;
    protected $connectionStrings;
    protected $appSettings;

    public function init(){
        $this->sessionSection = new SessionSection($this->getItem('session'));
        $this->namespaces = new NamespaceSection($this->getItem('namespaces'));
        $this->connectionStrings = new ConnectionStringSection($this->getItem('connectionStrings'));
        $this->appSettings = new AppSettingSection($this->getItem('appSettings'));
    }
    
    public function getSession(){
        return $this->sessionSection;
    }
    
    public function getNamespaces(){
        return $this->namespaces;
    }
    
    public function getConnectionStrings(){
        return $this->connectionStrings;
    }
    
    public function getAppSettings(){
        return $this->appSettings;
    }
}

?>
