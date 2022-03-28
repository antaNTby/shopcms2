<?php

require_once "SitemapIndexNode.php";

class SitemapGenerator
{
    protected $FilenamesExtention = 'xml';
    protected $SitemapIndexFilename = 'sitemap_index_v';
    protected $SitemapPartFilename = 'sitemap_v';
    protected $WorkDirectory;
    protected $WebDirectory;

    protected $TotalNodes=0;
    protected $TotalSizeInBytes=0;

    protected $SitemapIndexWriter;
    protected $SitemapWriter;

    protected $Domain;

    public function __construct($WorkDirectory,$Domain,$WebDirectory)
    {
        $this->WorkDirectory = $WorkDirectory;
        $this->WebDirectory = $WebDirectory;
        $this->Domain = $Domain;

        $SitemapIndexFilenameWithExtention = $WorkDirectory.$this->SitemapIndexFilename.'.'.$this->FilenamesExtention;
        $SitemapIndexWriter = new SitemapIndexWriter($SitemapIndexFilenameWithExtention,$Domain,$WebDirectory);
        $this->SitemapIndexWriter = $SitemapIndexWriter;
        $this->UpdateSitemapWriter();
    }

    public function push($Node)
    {
        if(!$this->SitemapWriter->TryWriteNode($Node))
        {
            $this->UpdateSitemapWriter();
            $this->SitemapWriter->WriteNode($Node);
        }
    }

    protected function UpdateSitemapWriter()
    {
        if($this->SitemapWriter) $this->SitemapWriter->WriteEnd();
        $SitemapFilename = $this->GetSitemapFilename();
        $RealSitemapFilenameWithPath = $this->WorkDirectory.$SitemapFilename;

        $this->SitemapWriter = new SitemapWriter($RealSitemapFilenameWithPath,$this->Domain,$this->WebDirectory);

        $SitemapIndexNode = new SitemapIndexNode();
        $SitemapIndexNode->loc = $SitemapFilename;
        $SitemapIndexNode->lastmod = gmdate("Y-m-d\TH:i:s");
        $this->SitemapIndexWriter->WriteNode($SitemapIndexNode);
        ++$this->TotalNodes;
    }

    protected function GetSitemapFilename()
    {
        $SitemapFilename = $this->SitemapPartFilename.str_pad($this->TotalNodes,6,'0',STR_PAD_LEFT).'.xml';
        return $SitemapFilename;
    }

    public function __destruct()
    {
        $this->SitemapWriter->WriteEnd();
        $this->SitemapIndexWriter->WriteEnd();
    }
}
