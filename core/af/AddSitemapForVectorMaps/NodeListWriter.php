<?php
abstract class NodeListWriter
{
    protected $StartContent;
    protected $NodeContent;
    protected $EndContent;

    protected $File;

    protected $TotalNodes=0;
    protected $TotalSizeInBytes=0;
    protected $Domain;
    protected $WebDirectory;

    public function __construct($filename,$Domain,$WebDirectory)
    {
        $this->Domain = $Domain;
        $this->WebDirectory = $WebDirectory;

        $File = fopen($filename,'w');
        if($File==false) throw new Exception();
        $this->File = $File;

        $this->StartContentLength = strlen($this->StartContent);
        $this->EndContentLength = strlen($this->EndContent);
        $this->WriteStart();
    }

    protected function WriteStart()
    {
        $this->WriteStr($this->StartContent);
    }

    abstract public function WriteNode(SitemapNodeBase $Node);

    protected function WriteNodeStr($NodeStr)
    {
        $this->WriteStr($NodeStr);
        ++$this->TotalNodes;
    }

    protected function WriteStr($NodeStr)
    {
        $NodeStr.="\n";
        $NodeLength = strlen($NodeStr);
        $r=fputs($this->File,$NodeStr);
        if(!$r) throw new Exception();
        $this->TotalSizeInBytes += $NodeLength;
    }

    public function WriteEnd()
    {
        $r = fputs($this->File,$this->EndContent);
        if(!$r) throw new Exception();
    }
}
