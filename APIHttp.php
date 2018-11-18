<?php
include_once('BDUtil.php');
include_once('models/User.php');
include_once('models/Grua.php');
include_once('models/Calificacion.php');
include_once('models/Position.php');
include_once('models/MessageChat.php');



$action = (isset($_POST['action'])) ? $_POST['action'] : "";

switch($action) {

    case 'signup':
        signup();
        break;
    case 'login':
        login();
        break;
    case 'login_with':
        loginWith();
        break;
    case 'upload_image':
        uploadImageService();
        break;
    case 'update_user':
        update_user();
        break;
    case 'update_grua':
        updateGrua();
        break;
    case 'get_all_gruas':
        get_all_gruas();
        break;
    case 'find_user_grua':
        Find_User();
        break;
    case 'calificacion_grua':
        Calificar_Grua();
        break;
    case 'get_all_calificacion':
        getAllCalificacion();
        break;
    case 'result_calificacion':
        getCalificacionGrua();
        break;
    case 'update_geo':
        updateGeoLocalizacion();
        break;
    case 'updateModeUser':
        updateMode_User();
        break;
    case 'get_all_markes':
        get_all_user_marker();
        break;
    case 'send_message_user':
        SendMessageUser();
        break;
    case 'get_all_message_user':
        Get_all_message();
        break;
    case 'get_last_message':
        get_last_messages_user();
        break;
    case 'get_users_send_messages':
        Get_users_send_messages();
        break;
    case 'notify_message':
        notify_messages();
        break;
    default:
        $response =  array('code_status' => 200,'message_server'=>'Your are not selected action option ');
        echo json_encode($response);
    break;
}

function signup() {
    $response = array('code_status' => 200,'message_server'=>'');
    $userJson = (isset($_POST['userJson'])) ? $_POST['userJson'] : null;
    if(!$userJson){
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }

    $array_user = json_decode($userJson, true);
    $manager_mysql = new ManagerMySQL();
    try {
        //Register
        $user = new User($array_user);
        $manager_mysql->connect();
        if($manager_mysql->existsUser($user)==1) {
            $manager_mysql->disconnect();
            $response['code_status'] = 200;
            $response['message_server']  = "El usuario ya existe, elija otro nombre de usuario";
            $aux = array('Id'=>-1);
            $response = array_merge($response, $aux);
            echo json_encode($response);
            return;
        }
        $result = $manager_mysql->signup($user); //Get id and description exepction
        $manager_mysql->disconnect();

        //prepare json
        $response['code_status'] = 200;
        $response['message_server']  = "OK";
        $response = array_merge($response,$result);

        echo json_encode($response);//send json
        
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

    
}

function uploadImageService() {
    $response = array('code_status' => 200,'message_server'=>'','imageURL'=>'');
    $dataJson = (isset($_POST['dataJson'])) ? $_POST['dataJson'] : null;
    $option = (isset($_POST['option'])) ? $_POST['option'] : null;
    if(!$dataJson){
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }

    $array_data = json_decode($dataJson, true);
    $manager_mysql = new ManagerMySQL();
    try {
        
        $manager_mysql->connect();
        
        //Check if exists URL image
        if($option == 'image_perfil') {
            $user = new User($array_data);
            $result_array = $manager_mysql->getPathPerfilURL($user);
             //... replace image
            $result_array = uploadImage($result_array,$user);
            $user->imageURL = $result_array['URL'];
            $manager_mysql->uploadPerfilImg($user);
            $response['imageURL'] = $user->imageURL;
        } else if ($option == 'image_background') {
            $user = new User($array_data);
            $result_array = $manager_mysql->getPathPerfilBackgroundURL($user);
            $result_array = uploadImage($result_array,$user);
            $user->imageBackgroundURL = $result_array['URL'];
            $manager_mysql->uploadPerfilBackgroundImg($user);
            $response['imageURL'] = $user->imageURL;
        } else if($option == 'image_background_grua') {
            $grua = new Grua($array_data);
            $result_array = $manager_mysql->getPathGruaBackgroundURL($grua);
            $result_array = uploadGruaImage($result_array, $grua);
            $grua->imageURL = $result_array['URL'];
            $manager_mysql->uploadGruaBackgroundImg($grua);
            $response['imageURL'] = $grua->imageURL;
        } else if($option == 'message_image') {
            $message = new MessageChat($array_data);
            $result_array = uploadMessageImage();
            $message->imageURL = $result_array['URL'];
            $message->Id = $manager_mysql->SaveMessage($message);
            $response['imageURL'] = $message->toJSON();
        }
        
        $manager_mysql->disconnect();

        

        echo json_encode($response);//response
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

}

function login() {
    $response = array('code_status' => 200,'message_server'=>'', 'json_user'=>'', 'json_grua'=>'');
    $nickname = (isset($_POST['nickname'])) ? $_POST['nickname'] : null;
    $password = (isset($_POST['password'])) ? $_POST['password'] : null;

    if(!$nickname || !$password) {
        $response['code_status'] = 200;
        $response['message_server'] = "No se encontro ningun nickname o password";
        echo json_encode($response);
        return;
    }
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();

        $user = $manager_mysql->login($nickname,$password);
        $grua = $manager_mysql->find_grua($user->Id); 
        $manager_mysql->disconnect();
        if($user->Id == -1) {
            $response['message_server'] = "Usuario o contraseña no son correctos";
        }
        $response['json_user'] = $user->toJSON();
        $response['json_grua'] = $grua->toJSON();
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

}

function loginWith() {
    $response = array('code_status' => 200,'message_server'=>'', 'json_user'=>'', 'json_grua'=>'');
    $name = (isset($_POST['name'])) ? $_POST['name'] : null;
    $provider = (isset($_POST['provider'])) ? $_POST['provider'] : null;

    if(!$name || !$provider) {
        $response['code_status'] = 200;
        $response['message_server'] = "No se encontro ningun nickname o password";
        echo json_encode($response);
        return;
    }

    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();

        $user = $manager_mysql->loginWith($name,$provider);
        $grua = $manager_mysql->find_grua($user->Id);
        $manager_mysql->disconnect();

        if($user->Id == -1) {
            $response['message_server'] = "Usuario o contraseña no son correctos";
        }
        $response['json_user'] = $user->toJSON();
        $response['json_grua'] = $grua->toJSON();
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

}

function update_user() {
    $response = array('code_status' => 200,'message_server'=>'','imageURL'=>'');
    $userJson = (isset($_POST['userJson'])) ? $_POST['userJson'] : null;

    if(!$userJson){
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }

    $array_user = json_decode($userJson, true);
    $manager_mysql = new ManagerMySQL();

    try {
        //Update
        $user = new User($array_user);
        $manager_mysql->connect();
        $manager_mysql->update_user($user);
        $manager_mysql->disconnect();

        $response['message_server'] = "Datos actualizados";
        echo json_encode($response);

    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }



}

function uploadFile($path) {
    $array_result = array('exepction' => '', 'URL'=> '');
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        $uploads_dir = './' . $path . '/';
        $tmp_name = $_FILES['file']['tmp_name'];
        $pic_name = $_FILES['file']['name'];
        move_uploaded_file($tmp_name, $uploads_dir.$pic_name);
        $array_result['URL'] = $path . '/' . $pic_name;
        }
    else
        $array_result['exepction'] = "File not uploaded successfully.";
   return $array_result;

}

function uploadImage($params, $user) {
    $path = $params['path_temp'];
    if(strcmp($path, '') != 0) {
        if(file_exists(dirname(__FILE__) . "/" . $path)) {
            unlink(dirname(__FILE__) . "/" . $path);
        }
    }
    $path = "users/" . $user->Nickname . "/img";
    if(!file_exists(dirname(__FILE__) . "/" . $path))
        mkdir(dirname(__FILE__) . "/" . $path, 0777, true);
    return uploadFile($path);
}

function uploadGruaImage($params, $grua) {
    $path = $params['path_temp'];
    if(strcmp($path, '') != 0) {
        if(file_exists(dirname(__FILE__) . "/" . $path)) {
            unlink(dirname(__FILE__) . "/" . $path);
        }
    }
    $path = "gruas/" . $grua->Name . "/img";
    if(!file_exists(dirname(__FILE__) . "/" . $path))
        mkdir(dirname(__FILE__) . "/" . $path, 0777, true);
    return uploadFile($path);
}

function uploadMessageImage() {
    $path = "temp";
    if(!file_exists(dirname(__FILE__) . "/" . $path))
        mkdir(dirname(__FILE__) . "/" . $path, 0777, true);
    return uploadFile($path);
}

function updateGrua() {
    $response = array('code_status' => 200,'message_server'=>'','Id'=>-1);
    $gruaJson = (isset($_POST['gruaJson'])) ? $_POST['gruaJson'] : null;
    $idUser = (isset($_POST['idUser'])) ? $_POST['idUser'] : null;

    if(!$gruaJson || !$idUser){
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }
    $array_grua = json_decode($gruaJson, true);
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();

        $grua = new Grua($array_grua);
        if($grua->Id == -1) {
           $arrayResult = $manager_mysql->insert_grua($idUser, $grua);
           $response['Id'] = $arrayResult['Id'];
        } else {
            $manager_mysql->update_grua($grua);
            $response['Id'] = $grua->Id;
        }
        $manager_mysql->disconnect();

        echo json_encode($response);

    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}
function get_all_gruas(){
    $response = array('code_status' => 200,'message_server'=>'', 'json_gruas'=>'');
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $gruas = $manager_mysql->get_all_gruas();
        $manager_mysql->disconnect();
        $response['json_gruas']  = $gruas;
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}
function Find_User() {
    $idGrua = (isset($_POST['idGrua'])) ? $_POST['idGrua'] : -1;
    $response = array('code_status' => 200,'message_server'=>'', 'json_user'=>'');
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $user = $manager_mysql->findUserGrua($idGrua);
        $manager_mysql->disconnect();
        $response['json_user'] = $user->toJSON();
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

}

function Calificar_Grua() {
    $json_calificacion = (isset($_POST['calificacionJson'])) ? $_POST['calificacionJson'] : null;
    $response = array('code_status' => 200,'message_server'=>'');

    if(!$json_calificacion){
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }
    $array_calificacion = json_decode($json_calificacion, true);
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $califiacion = new Calificacion($array_calificacion);
        $manager_mysql->calificar_grua($califiacion);
        $manager_mysql->disconnect();
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

}

function getAllCalificacion() {
    $idGrua = (isset($_POST['idGrua'])) ? $_POST['idGrua'] : -1;
    $response = array('code_status' => 200,'message_server'=>'', 'json_calificacion'=>'');

    if($idGrua == -1) {
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }

    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $calificaciones = $manager_mysql->get_all_calificacion($idGrua);
        $manager_mysql->disconnect();
        $response['json_calificacion']  = $calificaciones;
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}

function getCalificacionGrua() {
    $idGrua = (isset($_POST['idGrua'])) ? $_POST['idGrua'] : -1;
    $response = array('code_status' => 200,'message_server'=>'', 'json_calificacion'=>'');

    if($idGrua == -1) {
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }

    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $c = $manager_mysql->get_Calificacion_Grua($idGrua);
        $manager_mysql->disconnect();
        if($c!=null)
            $response['json_calificacion']  = $c->toJSON();
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}

function updateGeoLocalizacion() {
    $idUser = (isset($_POST['idUser'])) ? $_POST['idUser'] : -1;
    $json_position = (isset($_POST['positionJson'])) ? $_POST['positionJson'] : null;
    $response = array('code_status' => 200,'message_server'=>'','json_mode'=>'');

    if($idUser == -1 || $json_position==null) {
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $manager_mysql->update_GeoLocalizacion($idUser,$json_position);
        $array_result = $manager_mysql->find_mode_user($idUser);
        $manager_mysql->disconnect();

        if($array_result!=null) {
            $response['json_mode'] = json_encode($array_result);
        }

        echo json_encode($response);

    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}

function updateMode_User() {
    $idUser = (isset($_POST['idUser'])) ? $_POST['idUser'] : -1;
    $invisible = (isset($_POST['invisible'])) ? $_POST['invisible'] : 0;
    $mode = (isset($_POST['mode'])) ? $_POST['mode'] : 0;
    $response = array('code_status' => 200,'message_server'=>'');

    if($idUser == -1) {
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $manager_mysql->updateModeUser($idUser,$invisible,$mode);
        $manager_mysql->disconnect();
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

}

function get_all_user_marker() {
    $response = array('code_status' => 200,'message_server'=>'','json_marker'=>'');
    
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $resut_array = $manager_mysql->get_all_marker();
        $manager_mysql->disconnect();
        $response['json_marker'] = $resut_array;
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}

function SendMessageUser() {
    $message_json =  (isset($_POST['messageJson'])) ? $_POST['messageJson'] : null;
    $response = array('code_status' => 200,'message_server'=>'','idMessage'=>-1);

    if(!$message_json){
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }

    $array_message = json_decode($message_json, true);
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $message = new MessageChat($array_message);
        $id = $manager_mysql->SaveMessage($message);
        $manager_mysql->disconnect();
        $response['idMessage'] = $id;
        echo json_encode($response); 

    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}

function Get_all_message() {
    $idUserSend = (isset($_POST['idUserSend'])) ? $_POST['idUserSend'] : -1;
    $idUserReceive = (isset($_POST['idUserReceive'])) ? $_POST['idUserReceive'] : -1;
    $response = array('code_status' => 200,'message_server'=>'','json_message'=>'');

    if($idUserSend == -1 || $idUserReceive == -1) {
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        //Obtener todos los mensajes de tal chat
        $array_result = $manager_mysql->get_all_messsage_chat($idUserSend,$idUserReceive);
        $manager_mysql->disconnect();
        $response['json_message'] = $array_result;
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}

function get_last_messages_user() {
    $idUserSend = (isset($_POST['idUserSend'])) ? $_POST['idUserSend'] : -1;
    $idUserReceive = (isset($_POST['idUserReceive'])) ? $_POST['idUserReceive'] : -1;
    $idLastMessage = (isset($_POST['idLastMessage'])) ? $_POST['idLastMessage'] : -1;
    $response = array('code_status' => 200,'message_server'=>'','json_message'=>'');

    if($idUserSend == -1 || $idUserReceive == -1 || $idLastMessage == -1 ) {
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        //Obtener todos los mensajes de tal chat
        $array_result = $manager_mysql->get_last_messages($idUserSend,$idUserReceive,$idLastMessage);
        $manager_mysql->disconnect();
        $response['json_message'] = $array_result;
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }
}

function Get_users_send_messages() {
    $idUser = (isset($_POST['idUser'])) ? $_POST['idUser'] : -1;
    $response = array('code_status' => 200,'message_server'=>'','json_user'=>'');
    
    if($idUser == -1) {
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        //Obtener todos los mensajes de tal chat
        $array_result = $manager_mysql->get_user_send_messages($idUser);
        $manager_mysql->disconnect();
        $response['json_user'] = $array_result;
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

}

function notify_messages() {
    $idUser = (isset($_POST['idUser'])) ? $_POST['idUser'] : -1;
    $response = array('code_status' => 200,'message_server'=>'','json_user'=>'');

    if($idUser == -1) {
        $response['code_status'] = 500;
        $response['message_server']  = "No se encontro el json";
        echo json_encode($response);
        return;
    }
    $manager_mysql = new ManagerMySQL();
    try {
        $manager_mysql->connect();
        $array_result = $manager_mysql->get_notifiy_user($idUser);
        $manager_mysql->disconnect();
        $response['json_user'] = $array_result;
        echo json_encode($response);
    } catch(Exception $ex) {
        $manager_mysql->disconnect();
    }

}

?>