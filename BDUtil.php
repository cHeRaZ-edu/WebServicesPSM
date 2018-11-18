<?php

include_once('models/User.php');
include_once('models/Grua.php');
include_once('models/Calificacion.php');
include_once('models/Position.php');

class ManagerMySQL {
    private $mysqli;
    private $host;
    private $username;# name user mysql
    private $password;
    private $database;# name database to connect

    public function __construct() {
        $this->host = "localhost";
        $this->username = "root";
        $this->password = "";
        $this->database = "db_psm";
    }

    function connect() {   
        $this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->mysqli->connect_errno) {
            echo "Problema con la conexion a la base de datos: " . $this->mysqli->error;
        }	
    }

    function disconnect() {
        if(!$this->mysqli)
            $this->mysqli->close();
    }

    /**
     * Register user in database
     * @param user modelo usuario
     */
    function signup($user) {
        $arrayResult = array('exception' => '', 'Id' => -1);
        $query = "INSERT INTO user(name,last_name,nickname,email,password,provider) VALUES ('$user->Name', '$user->LastName', '$user->Nickname', '$user->Email','$user->Password', '$user->Provider')";
        $result = $this->mysqli->query($query);
        if (!$result) {
           $arrayResult['exception'] = 'Error en metodo getUserMessage se enctro un error en el query';
           return $arrayResult; 
       }
       $arrayResult['Id'] = $this->mysqli->insert_id;//Last id
       return $arrayResult;
    }
    function login($nickname,$password) {
        $temp = array('temp'=>'');
        $user = new User($temp);
        $query = "SELECT * FROM user as u WHERE u.nickname = '$nickname' and u.password = '$password';";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $user;
        while ($row = $result->fetch_assoc()) {
            $user->Id = $row['id'];
            $user->Name = $row['name'];
            $user->LastName = $row['last_name'];
            $user->Nickname = $row['nickname'];
            $user->Email = $row['email'];
            $user->Password = $row['password'];
            $user->imageURL = $row['imageURL'];
            $user->imageBackgroundURL = $row['imageBackgroundURL'];
            $user->Provider = $row['provider'];
            $user->Phone = $row['phone'];
        }
        $result->free();
        return $user; 
    }
    function loginWith($name,$provider) {
        $temp = array('temp'=>'');
        $user = new User($temp);
        $query = "SELECT * FROM user as u WHERE u.name = '$name' and u.provider = '$provider';";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $user;
        while ($row = $result->fetch_assoc()) {
            $user->Id = $row['id'];
            $user->Name = $row['name'];
            $user->LastName = $row['last_name'];
            $user->Nickname = $row['nickname'];
            $user->Email = $row['email'];
            $user->Password = $row['password'];
            $user->imageURL = $row['imageURL'];
            $user->Provider = $row['provider'];
        }
        $result->free();
        return $user; 
    }
    function update_user($user) {
        $query = "UPDATE user as u SET
        u.name = '$user->Name',
        u.last_name = '$user->LastName',
        u.phone = '$user->Phone'
        WHERE id = $user->Id";
        $this->mysqli->query($query);
    }
    function existsUser($user) {
        $isUser = 0;
        $query = "SELECT if(exists(SELECT * FROM user as u WHERE u.nickname = '$user->Nickname'),1,0) as `existsUser`;";
        $result = $this->mysqli->query($query);
        if (!$result) {
           $arrayResult['exception'] = 'Error en metodo getUserMessage se enctro un error en el query';
           return $arrayResult; 
        }
        while ($row = $result->fetch_assoc()) {
            $isUser = $row['existsUser'];
        }
        $result->free();
        return $isUser;   
    }
    function getPathPerfilURL($user) {
        $arrayResult = array('exception' => '', 'path_temp' => '');
        $query = "SELECT IFNULL(u.imageURL,'') as `URL` FROM user as u WHERE u.id = $user->Id;";
        $result = $this->mysqli->query($query);
        if (!$result) {
           $arrayResult['exception'] = 'Error en metodo getUserMessage se enctro un error en el query';
           return $arrayResult; 
        }
        while ($row = $result->fetch_assoc()) {
            $arrayResult['path_temp'] = $row['URL'];
        }
        $result->free();
        return $arrayResult;
    }
    function getPathPerfilBackgroundURL($user) {
        $arrayResult = array('exception' => '', 'path_temp' => '');
        $query = "SELECT IFNULL(u.imageBackgroundURL,'') as `URL` FROM user as u WHERE u.id = $user->Id;";
        $result = $this->mysqli->query($query);
        if (!$result) {
           $arrayResult['exception'] = 'Error en metodo getUserMessage se enctro un error en el query';
           return $arrayResult; 
        }
        while ($row = $result->fetch_assoc()) {
            $arrayResult['path_temp'] = $row['URL'];
        }
        $result->free();
        return $arrayResult;
    }
    function uploadPerfilImg($user) {
        $arrayResult = array('exception' => '');
        $query = "UPDATE user SET
                imageURL = '$user->imageURL'
                WHERE id = $user->Id";
       $this->mysqli->query($query);
    }
    function uploadPerfilBackgroundImg($user) {
        $query = "UPDATE user SET
                imageBackgroundURL = '$user->imageBackgroundURL'
                WHERE id = $user->Id";
        $this->mysqli->query($query);
    }
    function getPathGruaBackgroundURL($grua) {
        $arrayResult = array('exception' => '', 'path_temp' => '');
        $query = "SELECT IFNULL(g.imageURL,'') as `URL` FROM grua as g WHERE g.id = $grua->Id;";
        $result = $this->mysqli->query($query);
        if (!$result) {
           $arrayResult['exception'] = 'Error en metodo getUserMessage se enctro un error en el query';
           return $arrayResult; 
        }
        while ($row = $result->fetch_assoc()) {
            $arrayResult['path_temp'] = $row['URL'];
        }
        $result->free();
        return $arrayResult;
    }
    function uploadGruaBackgroundImg($grua) {
        $query = "UPDATE grua as g SET
        g.imageURL = '$grua->imageURL'
        WHERE id = $grua->Id";
        $this->mysqli->query($query);
    }
    function insert_grua($id, $grua) {
        $arrayResult = array('exception' => '', 'Id' => -1);
        $query = "INSERT INTO grua(name,description,idUser) VALUES ('$grua->Name', '$grua->Description', '$id')";
        $result = $this->mysqli->query($query);
        if (!$result) {
           $arrayResult['exception'] = 'Error en metodo getUserMessage se enctro un error en el query';
           return $arrayResult; 
       }
       $arrayResult['Id'] = $this->mysqli->insert_id;//Last id
       return $arrayResult;
    }
    function update_grua($grua) {
        $query = "UPDATE grua as g SET
        g.name = '$grua->Name',
        g.description = '$grua->Description'
        WHERE id = $grua->Id";
        $this->mysqli->query($query);
    }
    function find_grua($idUser) {
        $temp = array('temp'=>'');
        $grua = new Grua($temp);
        if($idUser == -1)
            return $grua;
        $query = "SELECT*FROM grua as g WHERE g.idUser = $idUser;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $grua;
        while ($row = $result->fetch_assoc()) {
            $grua->Id = $row['id'];
            $grua->Name = $row['name'];
            $grua->Description = $row['description'];
            $grua->imageURL = $row['imageURL'];
        }
        $result->free();
        return $grua;
    }
    function get_all_gruas() {
        $gruas = array();
        $i = 0;

        $query = "SELECT*FROM grua;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $gruas;
        while ($row = $result->fetch_assoc()) {
            $grua = new Grua(array());
            $grua->Id = $row['id'];
            $grua->Name = $row['name'];
            $grua->Description = $row['description'];
            $grua->imageURL = $row['imageURL'];
            $key = "00".$i;
            $temp = array($key=>$grua->toJSON());
            $gruas = array_merge($gruas,$temp);
            $i++;
        }
        $result->free();
        return $gruas;
    }

    function findUserGrua($idGrua) {
        $user = new User(array());
        $query = "SELECT u.* FROM grua as g
        inner join user as u
        on u.id = g.idUser and g.id = $idGrua;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $gruas;
        while ($row = $result->fetch_assoc()) {
            $user->Id = $row['id'];
            $user->Name = $row['name'];
            $user->LastName = $row['last_name'];
            $user->Nickname = $row['nickname'];
            $user->Email = $row['email'];
            $user->imageURL = $row['imageURL'];
            $user->Provider = $row['provider'];
        }
        $result->free();
        return $user;
    }

    function calificar_grua($califiacion) {

        $query = "INSERT INTO calificacion(idUser,idGrua,vote_5,vote_4,vote_3,vote_2,vote_1,message,imageURL)
        VALUES($califiacion->idUser,$califiacion->idGrua,
                $califiacion->vote_5,$califiacion->vote_4,$califiacion->vote_3,$califiacion->vote_2,$califiacion->vote_1,
                '$califiacion->message','$califiacion->imageURL')
                ON DUPLICATE KEY UPDATE
                vote_5 = $califiacion->vote_5,
                vote_4 = $califiacion->vote_4,
                vote_3 = $califiacion->vote_3,
                vote_2 = $califiacion->vote_2,
                vote_1 = $califiacion->vote_1,
                message = '$califiacion->message',
                imageURL = '$califiacion->imageURL';

        ";
        $this->mysqli->query($query);

    }

    function get_all_calificacion($idGrua) {
        $calificaciones = array();
        $i = 0;

        $query = "SELECT*FROM calificacion WHERE idGrua = $idGrua;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $gruas;
        while ($row = $result->fetch_assoc()) {
            $c = new Calificacion(array());
            $c->idUser = $row['idUser'];
            $c->idGrua = $row['idGrua'];
            $c->vote_5 = $row['vote_5'];
            $c->vote_4 = $row['vote_4'];
            $c->vote_3 = $row['vote_3'];
            $c->vote_2 = $row['vote_2'];
            $c->vote_1 = $row['vote_1'];
            $c->message = $row['message'];
            $c->imageURL = $row['imageURL'];
            $key = "00".$i;
            $temp = array($key=>$c->toJSON());
            $calificaciones = array_merge($calificaciones,$temp);
            $i++;
        }
        $result->free();
        return $calificaciones;
    }

    function get_Calificacion_Grua($idGrua) {
        $c = new Calificacion(array());
        $query = "SELECT*FROM calificacion WHERE idGrua = $idGrua;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $c;
        while ($row = $result->fetch_assoc()) {
            $c->vote_5 += $row['vote_5'];
            $c->vote_4 += $row['vote_4'];
            $c->vote_3 += $row['vote_3'];
            $c->vote_2 += $row['vote_2'];
            $c->vote_1 += $row['vote_1'];
        }
        $result->free();
        return $c;
    }

    function update_GeoLocalizacion($idUser, $json_position) {
        $query = "UPDATE user as u SET
        u.LatLngJSON = '$json_position'
        WHERE id = $idUser";
        $this->mysqli->query($query);
    }

    function find_mode_user($idUser) {
        $mode = 0;
        $invisible = 0;
        $query = "SELECT*FROM user as u WHERE u.id = $idUser;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return null;
        while ($row = $result->fetch_assoc()) {
            $mode = $row['mode'];
            $invisible = $row['invisible'];
        }
        $result->free();

        return array('invisible'=>$invisible,'mode'=>$mode);
    }

    function updateModeUser($idUser, $invisible, $mode) {
        $query = "UPDATE user as u SET
        u.invisible = $invisible,
        u.mode = $mode
        WHERE id = $idUser";
        $this->mysqli->query($query);
    }

    function get_all_marker() {
        $markers = array();
        $i = 0;
        $query = "SELECT*FROM user as u WHERE u.invisible = 0;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return null;
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $name = $row['name'];
            $nickname = $row['nickname'];
            $LatLngJSON = $row['LatLngJSON'];
            $mode = $row['mode'];

            $temp = 
            array(
                'id'=>$id,
                'name'=>$name,
                'nickname'=>$nickname,
                'jsonLatLng'=>$LatLngJSON,
                'mode'=>$mode);
            $key = "00".$i;
            $temp = array($key=>$temp);
            $markers = array_merge($markers,$temp);
            $i++;
        }
        $result->free();
        return $markers;
    }

    function SaveMessage($message) {
        $id = -1;
        $query = "INSERT INTO message_chat(idUserSend,idUserRecive,message,imageURL,time_send,response) VALUES ('$message->idUserSend', '$message->idUserRecive', '$message->message', '$message->imageURL','$message->time_send', 0)";
        $result = $this->mysqli->query($query);
        if (!$result) 
           return $id; 
        $id = $this->mysqli->insert_id;//Last id

        return $id;
    }

    function SaveImageMessage($message) {
        $query = "UPDATE message_chat SET
        imageURL = '$message->imageURL'
        WHERE id = $message->Id;";
        $this->mysqli->query($query);
    }

    function get_all_messsage_chat($idUserSend, $idUserReceive) {
        $messages = array();
        $i = 0;

        $query = "SELECT * FROM message_chat as m
        WHERE m.idUserSend = $idUserSend AND m.idUserRecive = $idUserReceive OR m.idUserSend = $idUserReceive AND m.idUserRecive = $idUserSend
        ORDER BY `id` ASC;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $messages;
        while ($row = $result->fetch_assoc()) {
            $m = new MessageChat(array());
            $m->Id = $row['id'];
            $m->idUserSend = $row['idUserSend'];
            $m->idUserRecive = $row['idUserRecive'];
            $m->message = $row['message'];
            $m->imageURL = $row['imageURL'];
            $m->time_send = $row['time_send'];
            $m->response = $row['response'];

            $key = "00".$i;
            $temp = array($key=>$m->toJSON());
            $messages = array_merge($messages,$temp);
            $i++;
        }
        $result->free();
        return $messages;
    }

    function get_last_messages($idUserSend, $idUserReceive, $lastId) {
        $messages = array();
        $i = 0;
        $query = "SELECT * FROM message_chat as m
        WHERE m.id > $lastId AND (m.idUserSend = $idUserSend AND m.idUserRecive = $idUserReceive OR m.idUserSend = $idUserSend AND m.idUserRecive = $idUserReceive)
        ORDER BY `id` ASC;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $messages;
        while ($row = $result->fetch_assoc()) {
            $m = new MessageChat(array());
            $m->Id = $row['id'];
            $m->idUserSend = $row['idUserSend'];
            $m->idUserRecive = $row['idUserRecive'];
            $m->message = $row['message'];
            $m->imageURL = $row['imageURL'];
            $m->time_send = $row['time_send'];
            $m->response = $row['response'];

            $key = "00".$i;
            $temp = array($key=>$m->toJSON());
            $messages = array_merge($messages,$temp);
            $i++;
        }
        $result->free();
        return $messages;

    }

    function get_user_send_messages($idUser) {
        $users = array();
        $i = 0;
        $query = "SELECT u.* FROM message_chat as m
        inner join user as u
        on u.id = m.idUserSend
        WHERE m.idUserRecive = $idUser
        GROUP BY m.idUserSend;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $users;
        while ($row = $result->fetch_assoc()) {
            $u = new User(array());
            $u->Id = $row['id'];
            $u->Name = $row['name'];
            $u->LastName = $row['last_name'];
            $u->Nickname = $row['nickname'];
            $u->imageURL = $row['imageURL'];

            $key = "00".$i;
            $temp = array($key=>$u->toJSON());
            $users = array_merge($users,$temp);
            $i++;
        }
        $result->free();
        return $users;

    }
    function get_notifiy_user($idUser) {
        $list_id = array();
        $users = array();
        $i = 0;
        $query = "SELECT u.id,u.name,u.nickname,u.imageURL FROM message_chat as m
        inner join user as u
        on u.id = m.idUserSend
        WHERE m.idUserRecive = $idUser AND m.response = 0
        GROUP BY u.id;";
        $result = $this->mysqli->query($query);
        if(!$result)
            return $users;
        while ($row = $result->fetch_assoc()) {
            $u = new User(array());
            $u->Id = $row['id'];
            $u->Name = $row['name'];
            $u->Nickname = $row['nickname'];
            $u->imageURL = $row['imageURL'];

            $key = "00".$i;
            $temp = array($key=>$u->toJSON());
            $users = array_merge($users,$temp);
            $list_id[] = $u->Id;
            $i++;
        }
        $result->free();

        foreach($list_id as $id) {
            $query = "UPDATE message_chat as m SET
            m.response = 1
            WHERE m.idUserRecive = $idUser AND m.idUserSend = $id;";
            $this->mysqli->query($query);
        }

        return $users;
    }

}


?>