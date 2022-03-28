<?php
require_once "NodeListWriter.php";

class SitemapIndexWriter extends NodeListWriter
{
    protected $StartContent='<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    protected $NodeContent;
    protected $EndContent='</sitemapindex>';

    public function WriteNode(SitemapNodeBase $Node)
    {
        $SitemapIndexNode = $Node;
        $NodeStr =<<<NDS
<sitemap><loc>{$this->Domain}{$this->WebDirectory}{$SitemapIndexNode->loc}</loc><lastmod>{$SitemapIndexNode->lastmod}</lastmod></sitemap>
NDS;
        $this->WriteNodeStr($NodeStr);
    }
}
