<?php

class SystemM extends ex_class
{
    public function __construct($method = "")
    {
        $this->Patch = $_SERVER["DOCUMENT_ROOT"] . "/";
        parent::__construct(null);   //на тот случай если мы будем наследовать от класса
        $this->method = $method;
    }
    
    public function InstallModule()
    {
        $dirlist = [ "controllers", "models", "vendors", "tmp" ];
        foreach ($dirlist as $dir)
        {
            if (!file_exists($dir))
            {
                mkdir($dir);
            }
        }
    }
    
    public function UpdateSystem()
    {
        ob_start();
        
        echo "<h1>START INSTALL</h1>\r\n";
        echo "<h1>" . $_SERVER["SERVER_NAME"] . "</h1>\r\n";
        echo "<h1>CREATE DB</h1>\r\n";
        
        $directories = [ 'controller' => 'controllers', 'model' => 'models', 'class' => 'vendors' ];
        
        /* Устанавливаем все БД */
        foreach ($directories as $key => $dir)
        {
            echo "<h2>$dir</h2>\r\n";
            $files1 = scandir($dir);
            foreach ($files1 as $value)
            {
                if (!in_array($value, [ ".", ".." ]))
                {
                    $class = pathinfo($dir . "/" . $value);
                    $class = (str_ireplace("." . $key, "", $class["filename"]));
                    echo "<p>Устанавливаем модуль $class</p>\r\n";
                    if (class_exists($class))
                    {
                        if (method_exists($class, 'CreateDB'))
                        {
                            $newobject = loader($class);
                            $newobject->CreateDB();
                        }
                        echo "<p>Закончили с $class</p>\r\n";
                    }
                    
                }
            }
        }
        
        
        echo "<h1>Install Module</h1>\r\n";
        /* Устанавливаем все преднастройки */
        foreach ($directories as $key => $dir)
        {
            echo "<h2>$dir</h2>\r\n";
            $files1 = scandir($dir);
            foreach ($files1 as $value)
            {
                if (!in_array($value, [ ".", ".." ]))
                {
                    $class = pathinfo($dir . "/" . $value);
                    $class = (str_ireplace(".".$key, "", $class["filename"]));
                    if (class_exists($class))
                    {
                        if (method_exists($class, 'InstallModule'))
                        {
                            echo "<p>Настраиваем модуль $class</p>\r\n";
                            $newobject = loader($class);
                            $newobject->InstallModule();
                        }
                    }
                    
                }
            }
        }
        
        
        echo "<h1>END INSTALL</h1>\r\n";
        
        $content = ob_get_contents();
        ob_end_clean();
        
        return $content;
    }
}