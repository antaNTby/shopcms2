<?php

require_once('StandartIncludes.php');//доступ авторизация и прочее
require_once('UnusedImagesCleaner.php');



$step = 0;
if(isset($_REQUEST['step']))$step = $_REQUEST['step'];
try
{
    switch($step)
    {
        case 1:
            UnusedImagesCleaner::CreateTemporaryTables();
            break;
        case 2:
            UnusedImagesCleaner::GetSourceDirectoryFilenames();
            break;
        case 3:
            UnusedImagesCleaner::GetUsedImagesFilenames();
            break;
        case 4:
            UnusedImagesCleaner::GetUnusedImagesFilenames();
            break;
        case 5:
            UnusedImagesCleaner::MooveUnusedImages();
            break;
        case 6:
            UnusedImagesCleaner::DeleteTemporaryTables();
            break;
        case 11:
            echo getcwd();
            break;
        
    }
}
catch(Exception $e)
{
    Echo "Exception!!!<br><pre>";
    var_dump($e);
    echo '</pre>';
}

?>

<ul>
<li><a href="?step=0">Начало</a></li>
<li><a href="?step=1">Создать временные таблицы</a></li>
<li><a href="?step=2">Заполнить таблицу со списком всех файлов</a></li>
<li><a href="?step=3">Заполнить таблицу со списком используемых файлов</a></li>
<li><a href="?step=4">Заполнить таблицу со списком неиспользуемых файлов</a></li>
<li><a href="?step=5">Переместить неиспользуемые файлы</a></li>
<li><a href="?step=6">Удалить временные таблицы</a></li>
<br>
<br>
<li><a href="?step=11">Текущий путь</a></li>

</ul>  
