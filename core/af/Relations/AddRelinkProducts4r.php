<?php
/*
if (  !defined('RELATED_PRODUCTS_TABLE_V2')  ) 
{
        define('RELATED_PRODUCTS_TABLE_V2', 'avl_related_items_v2');
} */

if (  !defined('RELATED_PRODUCTS_TABLE_V2')  ) 
{
        define('RELATED_PRODUCTS_TABLE_V2', RELATED_PRODUCTS_TABLE.'_v2');
}

class AddRelinkProducts4r
{                          
    const MaxRelatedProdsCount = 5; //����� ��������� �������
    private $ProductsCategoriesIds=array();//�������:PriductId,CategoryId - ��� ������ �� �� ����� ������ �������� ���������
	private $ProductsCount=0;//���-�� ����� � ������� $ProductsParams
    protected $RelationTable = RELATED_PRODUCTS_TABLE_V2;
    private $ProductsParams = array();

    function __construct() {
    }
                                                                           	
    private function GetProductsParams()
    {
        $DbQuery = "SELECT `productID`, 
        IF(p.tplID>0,REPLACE(t.name,'{city}',p.tpl_city),p.name) as `link`
        FROM `".PRODUCTS_TABLE.'` p JOIN '.PRODUCTS_TPL_TABLE.' t USING(tplID) LIMIT 100';
        $ret = DbHelper::GetTableWithKeyByColumn($DbQuery,'productID');
        return $ret;
    }

    private function GetProductsIds()
    {
        $DbQuery = "SELECT `productID`
        FROM `".PRODUCTS_TABLE.'`';
        $ret = DbHelper::GetTableWithKeyByColumn($DbQuery,'productID');
        return $ret;
    }

    private function GetProductParam($productID)
    {
        $DbQuery = "SELECT `productID`, 
        IF(p.tplID>0,REPLACE(t.name,'{city}',p.tpl_city),p.name) as `link`
        FROM `".PRODUCTS_TABLE.'` p JOIN '.PRODUCTS_TPL_TABLE.' t USING(tplID) WHERE p.productID='.$productID;
        $ret = mysql_fetch_array(mysql_query($DbQuery));
        return $ret;
    }

    public function UpdateRelatedProducts()
    {
		//��������� ��� ������ (�������� ���������)
        //$this->ProductsCategoriesIds = $ProductsCategoriesIds = $this->GetProductsCategoriesIds();
        //$this->ProductsCount = count($ProductsCategoriesIds);

		//�������� ������ ������� ��� ������� ���������� �������� ����� ���������
		$WorkProducts = $this->GetWorkProducts();
		$this->ProductsCount = $this->GetProductsCount();
		
		foreach($WorkProducts as $WorkProduct)
		{
			$RandomCount = self::MaxRelatedProdsCount - $WorkProduct['cnt'];//������� ����� ���������
			//��������� ��� �� ���� �� ���������
			$ExcludeIds = $this->GetCategoryProductsIds($WorkProduct['categoryID']);
			$RandomProductIds = $this->GetRandomProductsCategoriesIds($RandomCount,$ExcludeIds);//���������
            $this->AddProductRelationsToDB($WorkProduct['Owner'],$RandomProductIds);//��������� � ����
		}
    }
    
    private function GetProductsCount()
    {
		$sql_query = 'SELECT COUNT(*) FROM '.PRODUCTS_TABLE;
		$ret = DbHelper::GetScalar($sql_query);
		return $ret;
    }

    private function GetProductsCategoriesIds()
    {
        $DbQuery = 'SELECT `productID`, `categoryID` FROM `'.PRODUCTS_TABLE.'`';
        $ret = DbHelper::GetArray($DbQuery,'productID','categoryID');
        return $ret;
    }

    private function GetWorkProducts()
    {
        $DbQuery = 'SELECT `Owner`, `categoryID`, `cnt` FROM `tmp_relink_r4_all`';
        $ret = DbHelper::GetTable($DbQuery);
        return $ret;
    }
    
    private function GetCategoryProductsIds($categoryID)
    {
        $DbQuery = 'SELECT `productID` FROM `'.PRODUCTS_TABLE.'` WHERE categoryID='.$categoryID;
        $ret = DbHelper::GetColumn($DbQuery,'productID');
        return $ret;
    }
    
    private function GetProductInfoByIndex($index)
    {
		$sql_query =  "SELECT `ind`.`productID`, 
        IF(p.tplID>0,REPLACE(t.name,'{city}',p.tpl_city),p.name) as `link`
        FROM `tmp_relink_products` ind JOIN ".PRODUCTS_TABLE.' p USING(productID)
        JOIN '.PRODUCTS_TPL_TABLE.' t USING(tplID)
        WHERE ind.index='.$index;
        $ret = mysql_fetch_array(mysql_query($sql_query));
        return $ret;
    }
    
    private function GetRandomProductsCategoriesIds($Count,$ExcludeIds)
    {
         $ret = array();
         $ProductsCount = $this->ProductsCount;//��������� �������
         for($i=0;$i<$Count;)
         {                                         
             $index = mt_rand(1,$ProductsCount);
             $RandProductInfo = $this->GetProductInfoByIndex($index);
             $RandProductId = $RandProductInfo['productID'];
             
             if(!in_array($RandProductId,$ExcludeIds))
             {
                $ret[$RandProductId] = $RandProductInfo;
                $ExcludeIds[] = $RandProductId;
                $i++;  
             } 
         }
         return $ret;
    }

    private function AddProductRelationsToDB($ProductId,$ReletedIds)
    {
         $SortOrder=0;
         foreach($ReletedIds as $ReletedId)
         {
             $this->AddProductRelationToDB($ProductId,$ReletedId,$SortOrder);
             $SortOrder++;
         }
    }

    protected function AddProductRelationToDB($ProductId,$ReletedId,$SortOrder)
    {
        //$ProductParamsRowFrom = $this->GetProductParam($ReletedId);
        $ProductParamsRowFrom = $ReletedId;
        $ProductParamsRowTo =  $this->make_array_prod_name($ProductParamsRowFrom);
        $link = mysql_real_escape_string($ProductParamsRowTo['name']);
        $sim_text = mysql_real_escape_string($ProductParamsRowTo['sim_text']);
        
        $DbQuery = 'INSERT INTO '.$this->RelationTable.' (productID, Owner,sort_order,link,sim_text) VALUES ("'.$ReletedId['productID'].'","'.$ProductId.'","'.$SortOrder.'","'.$link.'","'.$sim_text.'")';
        db_query($DbQuery);
    }
    
    
    private function make_array_prod_name($row)
    {
        $row['n1']=$row['link'];
        $link = $row['link'];
        
        $res=array();
        $MapGpsStr = 'GPS ����� ';
        $MapTopoStr = '��������������� ����� ';
        $MapSpuSrt = '����������� ����� ';
		$MapKadStr = '����������� ����� ';
		        
        if( startsWith($link,$MapGpsStr))//GPS �����
        { 
            $row['link2']=str_replace($MapGpsStr,'����� ',$row['n1']).' ��� GPS';
            $row['sim_text']='';
            $row['maps_sort']=3;
        }
        elseif(startsWith($link,$MapTopoStr))//��������������� ����� 
        { 
        	$sim_pos = strpos($link,'1 ��');
            $row['link']=trim(substr($row['n1'],0,$sim_pos));
            $row['sim_text']=substr($row['n1'],$sim_pos);
            $row['maps_sort']=2;
        }
        elseif (startsWith($link,$MapSpuSrt))//����������� ����� 
        {
        	$sim_pos = strpos($link,'1 ��');
            $row['sim_text']=substr($row['n1'],$sim_pos);
            $row['link']=trim(substr($row['n1'],0,$sim_pos));
            $row['link2']=str_replace($MapSpuSrt,'����� ',$row['link'].' �� ��������');
            $row['maps_sort']=1;
        }
        elseif ( startsWith($link,$MapKadStr)  )//�����������
        {
			$row['maps_sort']=4;
			$row['sim_text']='';
        }
        else throw new Exception();
        
        //- ��� 50% ����������� ���� ��� ���� "����������� ����� {city} 1 �� - 125 �" 
        //�������� �� "����� {city} �� �������� 1 �� - 125 �";
        if($row['maps_sort']==1)
        {
			$row['name']= mt_rand(0,1) ? $row['link2']:$row['link'];
        }
        //- ��� 25% GPS ���� ��� "GPS ����� {city}" �������� �� "����� {city} ��� GPS";
        elseif($row['maps_sort']==3) 
        {
			$row['name'] = mt_rand(0,4) ? $row['link'] : $row['link2'];
        }
        else $row['name'] = $row['link'];
        
        //- 15% ���� ������� � �������� �������� ������� - �������� �������� ����������� ��������������� � ������;
        if(($row['maps_sort']==1) || ($row['maps_sort']==2))
        {
			if(mt_rand(0,99)<15)
			{
				$row['name'].=' '.$row['sim_text'];
				$row['sim_text']='';
			}
        }
        
        return $row;
    }
}


?>
