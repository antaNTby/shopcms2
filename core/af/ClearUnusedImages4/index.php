<?php

require_once('StandartIncludes.php');//������ ����������� � ������
require_once('UnusedImagesCleaner2.php');


/*
1. �������� ������ ������� ��� �������� � ����� �������
2. ��� ����������� ������ ������� ��������� ������ ������������ ��� ��������
3. ��������� ������ ���� ��������� ��������
4. �� ������ ������� ������ ������������ - ������� ������ ��������������
3. ��������� ��� �������������� �������� � ���������� � ����� ����� ��� ��������
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

