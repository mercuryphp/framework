<?php

namespace System\Configuration;

class Configuration {
    
    protected $reader;
    protected $environment;
    protected $session;
    protected $namespaces;
    protected $formsAuthentication;
    protected $connectionStrings;
    protected $appSettings;
    
    public function __construct(Readers\Reader $reader){
        $this->reader = $reader;
        $this->environment = new EnvironmentSection($reader->get('environment'));
        $this->session = new SessionSection($reader->get('session'));
        $this->namespaces = new NamespaceSection($reader->get('namespaces'));
        $this->formsAuthentication = new FormsAuthenticationSection($reader->get('formsAuthentication'));
        $this->connectionStrings = new ConnectionStringSection($reader->get('connectionStrings'));
        $this->appSettings = new AppSettingSection($reader->get('appSettings'));
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
    
    public function get($key){
        return $this->reader->get($key);
    }
}