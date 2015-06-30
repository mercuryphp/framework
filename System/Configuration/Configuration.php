<?php

namespace System\Configuration;

class Configuration extends ConfigurationReader {
    
    protected $environment;
    protected $session;
    protected $namespaces;
    protected $connectionStrings;
    protected $appSettings;

    public function init(){
        $this->environment = new EnvironmentSection($this->getItem('environment'));
        $this->session = new SessionSection($this->getItem('session'));
        $this->namespaces = new NamespaceSection($this->getItem('namespaces'));
        $this->connectionStrings = new ConnectionStringSection($this->getItem('connectionStrings'));
        $this->appSettings = new AppSettingSection($this->getItem('appSettings'));
    }
    
    public function getEnvironment(){
        return $this->environment;
    }
    
    public function getSession(){
        return $this->session;
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
