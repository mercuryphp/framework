<?php

namespace System\Web\Security;

use System\Std\Date;

class MembershipUser {
    
    protected $user_id;
    protected $username;
    protected $email;
    protected $first_name;
    protected $middle_name;
    protected $last_name;
    protected $password_question;
    protected $created_date;
    protected $last_login_date;
    protected $last_password_change_date;
    protected $last_locked_date;
    protected $last_active_date;
    protected $failed_password_count;

    public function getUserId(){
        return $this->user_id;
    }
    
    public function setUsername($username){
        $this->username = $username;
    }
    
    public function getUsername(){
        return $this->username;
    }
    
    public function setEmail($email){
        $this->email = $email;
    }
    
    public function getEmail(){
        return $this->email;
    }
    
    public function setFirstName($firstName){
        $this->first_name = $firstName;
    }
    
    public function getFirstName(){
        return $this->first_name;
    }
    
    public function setMiddleName($middleName){
        $this->middle_name = $middleName;
    }
    
    public function getMiddleName(){
        return $this->middle_name;
    }
    
    public function setLastName($lastName){
        $this->last_name = $lastName;
    }
    
    public function getLastName(){
        return $this->last_name;
    }
    
    public function setPasswordQuestion($passwordQuestion){
        $this->password_question = $passwordQuestion;
    }
    
    public function getCreatedDate(){
        return Date::parse($this->created_date);
    }
    
    public function setLastLoginDate($lastLoginDate){
        return $this->last_login_date = $lastLoginDate;
    }
    
    public function getLastLoginDate(){
        return Date::parse($this->last_login_date);
    }
    
    public function setLastPasswordChangeDate(){
        return Date::parse($this->last_password_change_date);
    }
    
    public function getLastPasswordChangeDate(){
        return Date::parse($this->last_password_change_date);
    }
    
    public function setLastLockedDate($lastLockedDate){
        $this->last_locked_date = $lastLockedDate;
    }
    
    public function getLastLockedDate(){
        return Date::parse($this->last_locked_date);
    }
    
    public function setLastActiveDate($lastActiveDate){
        $this->last_active_date = $lastActiveDate;
    }
    
    public function getLastActiveDate(){
        return Date::parse($this->last_active_date);
    }
}