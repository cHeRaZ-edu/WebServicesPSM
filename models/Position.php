<?php

class Position {
    public $Lat;
    public $Lng;

    public function __construct($params) {
        $this->Lat = (array_key_exists('Lat',$params)) ? $params['Lat'] : 25.65042;
        $this->Lng = (array_key_exists('Lng',$params)) ? $params['Lng'] : -100.1996316;
    }

    public function toJSON(){
        return get_object_vars($this);
    }

};

?>