<?php

require_once('StandartIncludes.php');//������ ����������� � ������
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
<li><a href="?step=0">������</a></li>
<li><a href="?step=1">������� ��������� �������</a></li>
<li><a href="?step=2">��������� ������� �� ������� ���� ������</a></li>
<li><a href="?step=3">��������� ������� �� ������� ������������ ������</a></li>
<li><a href="?step=4">��������� ������� �� ������� �������������� ������</a></li>
<li><a href="?step=5">����������� �������������� �����</a></li>
<li><a href="?step=6">������� ��������� �������</a></li>
<br>
<br>
<li><a href="?step=11">������� ����</a></li>

</ul>  
