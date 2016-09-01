<?php
/**
 * View.php
 * Created by Babenoff at 26.08.16 - 21:40
 */

namespace LD2;


use Symfony\Component\DependencyInjection\ContainerBuilder;

class View
{
    const PAGE_NOT_FOUND = "HTTP/1.0 404 Not Found";
    const OK = "HTTP/1.1 200 ok";
    const FORBIDDEN = "HTTP/1.0 403 Forbidden";
    const NO_CACHE = "Cache-Control: no-cache, must-revalidate";
    const DATE_LAST = "Expires: Sat, 26 Jul 1997 05:00:00 GMT";
    const HTML = "Content-Type: text/html; charset=utf-8";
    const XHTML = "Content-type:application/xhtml+xml; charset=utf-8";
    const JSON = "Content-Type: text/json";

    protected $debug;
    /**
     * @var ContainerBuilder
     */
    protected $container;

    public function __construct(array $headers)
    {
        $i = 0;
        do {
            header($i);
                $i++;
        } while ($i < count($headers));
    }

    public function display($page, $data = [], $title = "Лайкдимион 2", $rLink = false){
        $data["title"] = $title;
        $header = file_get_contents(ROOT."/res/templates/header5.html");
        $footer = file_get_contents(ROOT."/res/templates/footer.xhtml");
        if($rLink === true){
            ob_start([$this,"replaceLinks"]);
        }
        ob_start([$this, "replaceGenTime"]);
        ob_start([$this, "replaceVersion"]);
        ob_start([$this, "copyrights"]);
        $tmp = "";


        $tmp .= $header;
        $this->_debug($tmp);
        $tmp .= $page.$footer;
        $tmp = preg_replace_callback("/{{(.*)}}/i", function($matches)use($data){
            if(isset($data[$matches[1]])){
                return $data[$matches[1]];
            }
            return $matches[1];
        }, $tmp);
        $tmp = $this->replaceSqlTime($tmp);
        echo $tmp;
        ob_get_contents();
        if(true === $rLink){
            ob_end_flush();
        }
        ob_end_flush();
        ob_end_flush();
        ob_end_flush();
    }

    protected function _debug(&$tmp){
        if($this->getContainer()->getParameter("debug") === true){
            /** @var Database $pdo */
            $pdo = $this->getContainer()->get("pdo");
            $profilies = $pdo->getProfilies();
            $tmp .= "<ul class='debug'>";
            $time = 0;
            foreach ($profilies as $profile){
                $tmp .="<li>{$profile["Query_ID"]}: {$profile["Query"]} ({$profile["Duration"]})</li>";
                $time+=$profile["Duration"];
            }
            $tmp .= "</ul>";
        }
    }


    /**
     * @return ContainerBuilder
     */
    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    protected function replaceLinks($buffer){
        return preg_replace('/<a(.+?)href="(.+?)"(.*?)>(.+?)<\/a>/mi', '<a$1href="$2&'.substr(md5(random_bytes(8)),0,6).'"$3>$4</a>', $buffer);
    }

    protected function replaceGenTime($buffer){
        $time = microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"];
        return str_replace("%gen_time%", sprintf("ген: %0.4f сек", $time), $buffer);
    }
    protected function replaceSqlTime($buffer){
        /** @var Database $pdo */
        $pdo = $this->getContainer()->get("pdo");
        $time = $pdo->getSqlTime();
        return str_replace("%sql_time%", sprintf("Sql: %0.4f сек", $time), $buffer);
    }
    protected function replaceVersion($buffer){
        return str_replace("%version%", $this->getContainer()->get("composer.json")->version, $buffer);
    }
    protected function copyrights($buffer){
        $authors = $this->getContainer()->get("composer.json")->authors;
        $a = "";
        foreach ($authors as $author){
            if($a == ""){
                $a .= $author->nickname;
            } else {
                $a .= ", ".$author->nickname.">";
            }
        }
        return str_replace("%copyrights%", "&copy; <b>".$a."</b>, 2016", $buffer);
    }
}