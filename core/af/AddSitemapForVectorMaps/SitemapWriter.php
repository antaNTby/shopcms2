<?php

class SitemapWriter extends NodeListWriter
{
    protected $StartContent='<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    protected $StartContentLength;

    protected $NodeContent;
    protected $EndContent='</urlset>';
    protected $EndContentLength;

    protected $MaxTotalNodes=50000;
    protected $MaxTotalSizeInBytes=10485760;//10Mb

    public function TryWriteNode(SitemapNode $SitemapNode)
    {
        if(($this->TotalNodes+1) > $this->MaxTotalNodes) return false;

        $NodeStr = <<<ND
<url><loc>{$this->Domain}{$SitemapNode->loc}</loc><lastmod>{$SitemapNode->lastmod}</lastmod><changefreq>{$SitemapNode->changefreq}</changefreq><priority>{$SitemapNode->priority}</priority></url>
ND;
        $NodeLength = strlen($NodeStr);
        if(($this->TotalSizeInBytes + $NodeLength + $this->EndContentLength)> $this->MaxTotalSizeInBytes) return false;

        $this->WriteNodeStr($NodeStr);
        return true;
    }

    public function WriteNode(SitemapNodeBase $Node)
    {
        if(!$this->TryWriteNode($Node)) throw new Exception();
    }
}
