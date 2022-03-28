<?php
require_once "SitemapNode.php";

class SitemapCreatorForVectorMaps
{
    protected $WorkDirectory;
    protected $Domain;
    protected $WebDirectory;
    protected $SitemapGenerator;

    public function CreateSitemap($WorkDirectory,$Domain,$WebDirectory)
    {
        $this->WorkDirectory = $WorkDirectory;
        $this->Domain = $Domain;
        $this->WebDirectory = $WebDirectory;

        $this->SitemapGenerator = new SitemapGenerator($WorkDirectory,$Domain,$WebDirectory);

        $this->CreateSitemapRecursive();
    }

    protected function CreateSitemapRecursive()
    {
        $SitemapGenerator = $this->SitemapGenerator;

        //получаем список товаров в категории
        $ProductList = $this->GetProductList();
        foreach($ProductList as $Product)
        {
            $SitePage = $this->ProductToSitePage($Product);
            $SitemapGenerator->push($SitePage);
        }
    }

    protected function GetProductList()
    {
        $SqlQuery = "SELECT date_modified, UID from ".PRODUCTS_TABLE." p WHERE (tplID IN (28,29,30,31))";
        $ret = DbHelperWithFree::GetTable($SqlQuery);
        return $ret;
    }

    protected function ProductToSitePage($DbValue)
    {
        $DefaultPriority='0.7';
        $DefaultChangefreq='weekly';

        $ret = new SitemapNode();
        $ret->loc = htmlspecialchars($DbValue['UID']).'.html';
        $ret->lastmod=str_replace(" ", "T", $DbValue['date_modified'])."+00:00";;
        $ret->changefreq = $DefaultChangefreq;
        $ret->priority = $DefaultPriority;
        return $ret;
    }
}
