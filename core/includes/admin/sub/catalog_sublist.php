<?
//babenkoma
//���� �� ������� ��� ����������, ��
if (isset($_POST['sufix'])){
    mysql_query("DELETE FROM `avl_routes` WHERE `target` LIKE '%subID=".$_POST['sufix']."'");
//�����, ���� �� �������� ������ ������ ���������� ����� ���� 
} else {
    //if (isset($_POST['edit'])) {$parTplID = "WHERE parentTplID=".(int)$_POST['edit'];} else $parTplID = '';
    $sql = mysql_query("SELECT tplID, tpl_name FROM avl_products_subtpl ");//.$parTplID);
    if (mysql_num_rows($sql) >= 1) {
        $msg = 'subList = [';
        $row = mysql_fetch_assoc($sql);
        $msg .= '["'.strval($row["tplID"]).'", "'.$row["tpl_name"].'"]';
        while ($row = mysql_fetch_assoc($sql)){
            $msg .= ', ["'.strval($row["tplID"]).'", "'.$row["tpl_name"].'"]';
        }
        $msg .= '];';
    } else {
        $msg=0;
    }
    header('Content-Type: text/plain; charset=windows-1251');
    echo  $msg;
}
die;

//babenkoma\
?>