<?php
/**
 * View.php
 * Created by Babenoff at 26.08.16 - 21:40
 */

namespace LD2;


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

    public function __construct(array $headers)
    {
        $i = 0;
        do {
            header($i);
                $i++;
        } while ($i < count($headers));
    }

    public function display($page, $data = [], $title = "Лайкдимион 2"){
        $data["title"] = $title;
        $header = file_get_contents(ROOT."/res/templates/header.xhtml");
        $footer = file_get_contents(ROOT."/res/templates/footer.xhtml");
        ob_start("replaceLinks");
        $tmp = "";
        $tmp .= $header.$page.$footer;
        $tmp = preg_replace_callback("/{{(.*)}}/i", function($matches)use($data){
            if(isset($data[$matches[1]])){
                return $data[$matches[1]];
            }
            return $matches[1];
        }, $tmp);
        $tmp = $this->replaceLinks($tmp);
        echo $tmp;
        ob_get_contents();
        ob_end_flush();
    }

    protected function replaceLinks($buffer){
        return preg_replace('/<a(.+?)href="(.+?)"(.*?)>(.+?)<\/a>/mi', '<a$1href="$2&'.substr(md5(random_bytes(8)),0,6).'"$3>$4</a>', $buffer);
    }

    protected function replaceProcess($link){
        return $link."&".random_int(1,99);
    }
}