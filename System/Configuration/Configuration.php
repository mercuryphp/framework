<?php

namespace System\Configuration;

class Configuration {
    
    protected $environment;
    protected $session;
    protected $namespaces;
    protected $formsAuthentication;
    protected $connectionStrings;
    protected $appSettings;
    
    public function __construct(Readers\Reader $reader){
        $this->environment = new EnvironmentSection($reader->getItem('environment'));
        $this->session = new SessionSection($reader->getItem('session'));
        $this->namespaces = new NamespaceSection($reader->getItem('namespaces'));
        $this->formsAuthentication = new FormsAuthenticationSection($reader->getItem('formsAuthentication'));
        $this->connectionStrings = new ConnectionStringSection($reader->getItem('connectionStrings'));
        $this->appSettings = new AppSettingSection($reader->getItem('appSettings'));
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
    
    public function getFormsAuthentication(){
        return $this->formsAuthentication;
    }
    
    public function getConnectionStrings(){
        return $this->connectionStrings;
    }
    
    public function getAppSettings(){
        return $this->appSettings;
    }
}

?>
