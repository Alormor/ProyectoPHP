<?php
namespace Core;


class Application
{
    public function render(string $pageName, ?array $params = null): void{
        if($params != null){
            foreach ($params as $name => $value){
                $$name = $value;
            }
        }
        $viewPath = dirname(__DIR__);
        require_once $viewPath . DIRECTORY_SEPARATOR . "Views" . DIRECTORY_SEPARATOR . "Layout" . DIRECTORY_SEPARATOR . "header.php";
        require_once $viewPath . DIRECTORY_SEPARATOR . "Views" . DIRECTORY_SEPARATOR . "$pageName.php";
        require_once $viewPath . DIRECTORY_SEPARATOR . "Views" . DIRECTORY_SEPARATOR . "Layout" . DIRECTORY_SEPARATOR . "footer.php";
    }
}
