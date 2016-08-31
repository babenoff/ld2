<?php
/**
 * login.php
 * Created by Babenoff at 26.08.16 - 19:31
 */
require "setup.php";

setcookie($config["cookie_name"], "", time() - 3600);
$page = "";
if (isset($_POST["username"])) {

} else {
    if(!isset($_GET["action"])){
        $_GET["action"] = "login";
    }
    if(file_exists(ROOT."/site/".$_GET["action"].".php")){
        require ROOT."/site/".$_GET["action"].".php";
    } else {
        require ROOT."/errors/404.php";
    }
}