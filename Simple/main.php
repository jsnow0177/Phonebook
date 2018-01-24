<?php
namespace Simple;

use Simple\Psr4\ClassLoader;

// Подключаем класс автозагрузчика классов
include_once((dirname(__FILE__) . '/src/Psr4/ClassLoader.php'));

// Настраиваем автозагрузку
$classLoader = new ClassLoader();
$classLoader->Append('Simple', dirname(__FILE__) . '/src');
$classLoader->Enable(); // Включаем автозагрузчик