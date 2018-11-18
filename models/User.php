<?php 
    class User {
        public $Id;
        public $Name;
        public $LastName;
        public $Nickname;
        public $Email;
        public $Password;
        public $imageURL;
        public $imageBackgroundURL;
        public $Provider;//google.com,twitter.com,facebook.com
        public $Phone;
        public function __construct($params) {
            $this->Id = (array_key_exists('Id',$params)) ? $params['Id'] : -1;
            $this->Name = (array_key_exists('Name',$params)) ? $params['Name'] : "";
            $this->LastName = (array_key_exists('LastName',$params)) ? $params['LastName'] : "";
            $this->Nickname = (array_key_exists('Nickname',$params)) ? $params['Nickname'] : "";
            $this->Email = (array_key_exists('Email',$params)) ? $params['Email'] : "";
            $this->Password = (array_key_exists('Password',$params)) ? $params['Password'] : "";
            $this->imageURL = (array_key_exists('imageURL',$params)) ? $params['imageURL'] : "";
            $this->imageBackgroundURL = (array_key_exists('imageBackgroundURL',$params)) ? $params['imageBackgroundURL'] : "";
            $this->Provider = (array_key_exists('Provider',$params)) ? $params['Provider'] : "";
            $this->Phone = (array_key_exists('Phone',$params)) ? $params['Phone'] : "";
        }

        public function toJSON(){
            return get_object_vars($this);
        }
        
    };
?>