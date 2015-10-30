<?php

namespace System\Web\Security;

use System\Web\Session\FileSystem;

class SessionAuthenticationHandler extends AuthenticationHandler {
    
    public function setHttpContext(\System\Web\HttpContext $httpContext){
        $this->httpContext = $httpContext;
    }
    
    public function setTicket(AuthenticationTicket $ticket){
        $session = new FileSystem($this->httpContext->getRequest(), $this->httpContext->getResponse());
        $session->setName($this->cookieName);
        $session->set('ticket', $ticket);
        $session->write();
    }
    
    public function authenticate(){
        $session = new FileSystem($this->httpContext->getRequest(), $this->httpContext->getResponse());
        $session->setName($this->cookieName);
        $session->open();
        
        $identity = new UserIdentity('Anonymous');
        $ticket = $session->get('ticket');
        
        if($ticket){
            if($ticket && ((\System\Std\Date::now()->getTimestamp() < $ticket->getExpire()) || $ticket->getExpire()==0)){
                $identity = new UserIdentity($ticket->getName(), $ticket->getUserData(), true);
            }
        }

        $this->httpContext->getRequest()->setUser($identity);
    }
}