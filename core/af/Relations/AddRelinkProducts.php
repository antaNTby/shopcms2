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

include_once('AddRelatedProducts.php');

class AddRelinkProducts extends AddRelatedProducts
{                                                                                                     	
    protected $MaxRelatedProdsFromSelfCategoryCount = 1;
    private $ProductsParams = array();
    function __construct() {
        $this->ProductsParams = $this->GetProductsParams();
    }

    private function GetProductsParams()
    {
        $DbQuery = 'SELECT `productID`, `name` as `link`, `maps_sort` FROM `'.PRODUCTS_TABLE.'`';
        $ret = DbHelper::GetTableWithKeyByColumn($DbQuery,'productID');
        return $ret;
    }
    
    protected function AddProductRelationToDB($ProductId,$ReletedId,$SortOrder)
    {
        $ProductParamsRowFrom = $this->ProductsParams[$ReletedId];
        $ProductParamsRowTo =  $this->make_array_prod_name($ProductParamsRowFrom);
        $link = mysql_real_escape_string($ProductParamsRowTo['link']);
        $sim_text = mysql_real_escape_string($ProductParamsRowTo['sim_text']);
        
        $DbQuery = 'INSERT INTO '.$this->RelationTable.' (productID, Owner,sort_order,link,sim_text) VALUES ("'.$ReletedId.'","'.$ProductId.'","'.$SortOrder.'","'.$link.'","'.$sim_text.'")';
        db_query($DbQuery);
    }
    
    private function make_array_prod_name($row)
    {
        $row['n1']=$row['link'];
        $prods_array_links_top_cat=array();
        if ( $row['maps_sort']==3)
        { 
            $prods_array_links_top_cat[$row['productID']]['link']=$row['n1'];
            $prods_array_links_top_cat[$row['productID']]['sim_text']='';
            $prods_array_links_top_cat[$row['productID']]['link2']=str_replace('GPS карта ','Карта ',$row['n1']).' для GPS';
        }
        elseif ($row['maps_sort']==1 && substr($row['n1'],-13)==' 1 см - 250 м')
        { 
            $prods_array_links_top_cat[$row['productID']]['link']=substr($row['n1'],0,-13);
            $prods_array_links_top_cat[$row['productID']]['sim_text']='1 см - 250 м';
        }
        elseif ( $row['maps_sort']==1 && substr($row['n1'],-13)==' 1 см - 500 м')
        {
            $prods_array_links_top_cat[$row['productID']]['link']=str_replace('Спутниковая карта ','Карта ',substr($row['n1'],0,-13)).' со спутника';
            $prods_array_links_top_cat[$row['productID']]['sim_text']='1 см - 500 м';
        }
        elseif ( $row['maps_sort']==1 && substr($row['n1'],-12)==' 1 см - 20 м')
        { 
            $prods_array_links_top_cat[$row['productID']]['link']=str_replace('Спутниковая карта ','Карта ',substr($row['n1'],0,-12)).' со спутника';
            $prods_array_links_top_cat[$row['productID']]['link2']=substr($row['n1'],0,-12);
            $prods_array_links_top_cat[$row['productID']]['sim_text']='1 см - 20 м';}
        elseif ( $row['maps_sort']==2  )
        {
            $pos=0;
            $pos=strpos($row['n1'],' 1 см ');
            if ($pos<2) $pos=strpos($row['n1'],' 1см ');
            $prods_array_links_top_cat[$row['productID']]['sim_text']=substr($row['n1'],$pos);
            $prods_array_links_top_cat[$row['productID']]['link']=trim(str_replace($prods_array_links_top_cat[$row['productID']]['sim_text'],'',$row['n1']));
            $prod_topo_id=$row['productID'];
            //print_r($prods_array_links_top_cat);

        }
        $ret =  $prods_array_links_top_cat[$row['productID']];
        return $ret;
    }
}


?>
