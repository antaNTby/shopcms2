<?php
 include_once('StandartIncludes.php');
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/tr/html4/loose.dtd'>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1251">
    <link rel="stylesheet" href="/design/admin/images/style.css" type="text/css">
    <link rel="icon" href="/design/admin/images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/design/admin/images/favicon.ico" type="image/x-icon">
    <title>���������� �������������� ������� � ���������������� �����</title>
</head>
<body>
<form action="" method="post">
    <h1><a href="./Relations.php">���������� �������������� ������� � ���������������� �����</a></h1>
    �������� �����:<br />
    <!--
	    <label><input type="checkbox" name="recommendation" checked="checked">�������������� ������</label><br />
	    <label><input type="checkbox" name="relink" checked="checked">��������������� ����</label><br />
    	<label><input type="checkbox" name="relink4r" checked="checked">���������� ��������� ��� ��� �� �������</label><br />
    -->
    <label><input type="checkbox" name="relink5" checked="checked">���������� ���� �����</label><br />
    <input type="submit" value="��������">
</form>
<br />    
<textarea style="width:100%;height:25em;" >
<?php

include_once('DbHelper.php');
include_once('AddRelatedProducts.php');
include_once('AddRelinkProducts.php');
include_once('AddRelinkProducts4r.php');
include_once('AddRelinkProducts5.php');

if(isset($_POST['all'])||isset($_POST['recommendation'])||isset($_POST['relink']) || isset($_POST['relink4r'])  || isset($_POST['relink5']))
{
    //if(isset($_POST['all'])) RelationUpdater::UpdateAll();
    //else
    {
        if(isset($_POST['recommendation'])) RelationUpdater::UpdateRecommendation();
        if(isset($_POST['relink']))  RelationUpdater::UpdateRelink();
        if(isset($_POST['relink4r']))  RelationUpdater::UpdateRelink4r();
        if(isset($_POST['relink5']))  RelationUpdater::UpdateRelink5();
    }
}

class RelationUpdater
{
    static function UpdateAll()
    {
        self::UpdateRecommendation();
        self::UpdateRelink();
    }
    
    static function UpdateRecommendation()
    {
        echo "�������������� ������:\n";
        $obj = new AddRelatedProducts();
        self::Update($obj);
    }
    
    static function UpdateRelink()
    {
        echo "��������������� ����:\n";
        $obj = new AddRelinkProducts();
        self::Update($obj);
    }

    static function UpdateRelink4r()
    {
        echo "��������������� ����:\n";
        $obj = new AddRelinkProducts4r();
        self::Update($obj);
    }
    
    static function UpdateRelink5()
    {
        echo "��������������� ����:\n";
        $obj = new AddRelinkProducts5();
        self::Update($obj);
    }

    private static function Update($Obj)
    {
        try
        {
            echo "- ����������...\n";flush();
            $Obj->UpdateRelatedProducts();
        }
        catch(Exception $e)
        {
            echo "-������ ��� ����������:\n";
            echo $e->getMessage();
        }
        echo "- ���������\n\n";
    }
}

?>
</textarea>
</body>

