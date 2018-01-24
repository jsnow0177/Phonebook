<?php
namespace JSnow\Web\Controllers;

class WebInterface
{

    public function Phonebook()
    {
        if(file_exists(ROOT_PATH . '/st/index.html')){
            header("Content-Type: text/html; charset=utf-8");
            include((ROOT_PATH . '/st/index.html'));
            die();
        }else{
            die("Не удалось найти файл веб-интерфейса!");
        }
    }

}