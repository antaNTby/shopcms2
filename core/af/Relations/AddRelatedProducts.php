<?php

class AddRelatedProducts
{
    const MaxRelatedProdsCount = 5; //����� ��������� �������
    protected $MaxRelatedProdsFromSelfCategoryCount = 3; //������� �� ��� �� ���������
    
    private $ProductsCategoriesIds=array();//�������:PriductId,CategoryId
    private $ProductsCount=0;//���-�� ����� � ������� $ProductsAndCategoriesIds
    private $CategoriesWithFirstProductsIds=array();//[CategoryId]=>array(ProductId_1,...,ProductId_N)
    protected $RelationTable = RELATED_PRODUCTS_TABLE;
    
    public function UpdateRelatedProducts()
    {
        //������� �� id ������ � id ��������
        $this->ProductsCategoriesIds = $ProductsCategoriesIds = $this->GetProductsCategoriesIds();
        $this->ProductsCount = count($ProductsCategoriesIds);
        
        //�������� ������ ��������� � �� ������ N �������
        $this->CategoriesWithFirstProductsIds = $CategoriesWithFirstProductsIds = $this->GetCategoriesWithFirstProductsIds();
        
        $this->ClearAllRelatedProducts();//������� ��� ������������ �����
        
        //$ExcludeIds=array();//����������� �� ����������������
        foreach($ProductsCategoriesIds as $ProductId=>$CategoryId)
        {
            $CategoryProductIds = $CategoriesWithFirstProductsIds[$CategoryId];//������ N+1 ������ �� ���������
            $ExcludeIds = $CategoryProductIds;//����������� �� ���������
            $Key = array_search($ProductId,$CategoryProductIds);unset($CategoryProductIds[$Key]);//������� ������� $ProductId 
        
            $RandomCount = self::MaxRelatedProdsCount - count($CategoryProductIds);//������� ����� ���������
            $RandomProductIds = $this->GetRandomProductsCategoriesIds($RandomCount,$ExcludeIds);//���������
            $AllReletedIds = array_merge($CategoryProductIds,$RandomProductIds);//����������
            $this->AddProductRelationsToDB($ProductId,$AllReletedIds);//��������� � ����
        }
    }
    
    private function GetProductsCategoriesIds()
    {
        $DbQuery = 'SELECT `productID`, `categoryID` FROM `'.PRODUCTS_TABLE.'`';
        $ret = DbHelper::GetArray($DbQuery,'productID','categoryID');
        return $ret;
    }
    
    private function GetCategoriesWithFirstProductsIds()
    {
        $DbSubQuery = 'SELECT `t1`.`categoryID`, `t2`.`productId` FROM `'.CATEGORIES_TABLE.
        '` t1 JOIN `'.PRODUCTS_TABLE.'` t2 ON `t1`.`categoryID` = `t2`.`categoryID` ORDER BY `t1`.`categoryID`, `t2`.`sort_order`';
        
        $MaxProductsCount = $this->MaxRelatedProdsFromSelfCategoryCount + 1;
        $DbQuery = 'SET @n='.$MaxProductsCount.', @i=0, @p=0';
        db_query($DbQuery);
        $DbQuery = 'SELECT * FROM ('.$DbSubQuery.') t WHERE IF (@p=categoryID, @i:=@i+1,(@i:=0) OR (@p:=categoryID)) AND @i<@n';

        $ProductsIds = DbHelper::GetColumnGroupBy($DbQuery,'categoryID','productId');
        return $ProductsIds;
    }
    
    private function ClearAllRelatedProducts()
    {
        $DbQuery = 'TRUNCATE TABLE '.$this->RelationTable;
        $DbRes = db_query($DbQuery);
    }
    
    private function GetRandomProductsCategoriesIds($Count,$ExcludeIds)
    {
         $ret = array();
         $ProductsCategoriesIds = $this->ProductsCategoriesIds;
         for($i=0;$i<$Count;)
         {                                         
             $RandProductId = array_rand($ProductsCategoriesIds);
             if(!in_array($RandProductId,$ExcludeIds))
             {
                $ret[] = $RandProductId;
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
        $DbQuery = 'INSERT INTO '.$this->RelationTable.'  (productID, Owner,sort_order) VALUES ('.$ReletedId.','.$ProductId.','.$SortOrder.')';
        db_query($DbQuery);
    }
}



?>
