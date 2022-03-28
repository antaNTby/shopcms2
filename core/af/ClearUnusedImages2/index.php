<?php

require_once('StandartIncludes.php');//доступ авторизация и прочее
require_once('UnusedImagesCleaner2.php');


/*
1. Получить список товаров без картинок в новой таблице
2. Для полученного списка товаров составить список используемых ими картинок
3. Составить список всех имеющихся картинок
4. Из списка вычесть список используемых - получим список неиспользуемых
3. Перенести все неиспользуемые картинки с подпапками в новую папку для удаления
*/

$step = 0;
if(isset($_REQUEST['step']))$step = $_REQUEST['step'];
try
{
    switch($step)
    {
        case 1:
            UnusedImagesCleaner2::CreateTemporaryTables();
            break;
        case 2:
            UnusedImagesCleaner2::GetSourceDirectoryFilenames();
            break;
        case 3:
            UnusedImagesCleaner2::GetUsedImagesFilenames();
            break;
        case 4:
            UnusedImagesCleaner2::GetUnusedImagesFilenames();
            break;
        case 5:
            UnusedImagesCleaner2::MooveUnusedImages();
            break;
        case 6:
            UnusedImagesCleaner2::DeleteTemporaryTables();
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

