<?php

class AddRelatedProducts
{
    const MaxRelatedProdsCount = 5; //всего связанных товаров
    protected $MaxRelatedProdsFromSelfCategoryCount = 3; //товаров из той же категории
    
    private $ProductsCategoriesIds=array();//таблица:PriductId,CategoryId
    private $ProductsCount=0;//кол-во строк в таблице $ProductsAndCategoriesIds
    private $CategoriesWithFirstProductsIds=array();//[CategoryId]=>array(ProductId_1,...,ProductId_N)
    protected $RelationTable = RELATED_PRODUCTS_TABLE;
    
    public function UpdateRelatedProducts()
    {
        //таблица из id товара и id категори
        $this->ProductsCategoriesIds = $ProductsCategoriesIds = $this->GetProductsCategoriesIds();
        $this->ProductsCount = count($ProductsCategoriesIds);
        
        //получаем список категорий и их первые N товаров
        $this->CategoriesWithFirstProductsIds = $CategoriesWithFirstProductsIds = $this->GetCategoriesWithFirstProductsIds();
        
        $this->ClearAllRelatedProducts();//удаляем все существующие связи
        
        //$ExcludeIds=array();//исключаемые из автогенерируемых
        foreach($ProductsCategoriesIds as $ProductId=>$CategoryId)
        {
            $CategoryProductIds = $CategoriesWithFirstProductsIds[$CategoryId];//первые N+1 товара из категории
            $ExcludeIds = $CategoryProductIds;//исключаемые из случайных
            $Key = array_search($ProductId,$CategoryProductIds);unset($CategoryProductIds[$Key]);//удаляем текущий $ProductId 
        
            $RandomCount = self::MaxRelatedProdsCount - count($CategoryProductIds);//сколько нужно случайных
            $RandomProductIds = $this->GetRandomProductsCategoriesIds($RandomCount,$ExcludeIds);//случайные
            $AllReletedIds = array_merge($CategoryProductIds,$RandomProductIds);//объеденяем
            $this->AddProductRelationsToDB($ProductId,$AllReletedIds);//добавляем в базу
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
