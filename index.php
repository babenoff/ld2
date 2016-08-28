<?php
if(!isset($_COOKIE[$config["cookie_name"]])){
    header("Location: login.php");
} else {
    require "setup.php";
    require "game/game.php";
}

?>
