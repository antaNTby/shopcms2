<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

$NodeID = 0;

class xmlNodeX{

        var $ParentNode;
        var $ChildNodes         = array();
        var $Attributes         = array();
        var $Data;
        var $Name;
        var $ID;
        var $ParserResource;
        var $parsingNode;

        function xmlNodeX($_Name = '', $_Attributes = array(), $_Data = '' ){

                $this->Name                 = $_Name;
                $this->Attributes         = $_Attributes;
                $this->Data                 = $_Data;
        }

        function getName(){

                return $this->Name;
        }

        function getAttributes(){

                return $this->Attributes;
        }

        function getAttribute($_Name){

                if(isset($this->Attributes[$_Name]))return $this->Attributes[$_Name];
                else return null;
        }

        function getData(){

                return $this->Data;
        }

        function &getChildNodes(){

                return $this->ChildNodes;
        }

        function &createChildNode($_Name, $_Attributes = array(), $_Data = ''){

                global $NodeID;

                $_ChildNode = &new xmlNodeX($_Name, $_Attributes, $_Data);

                $_ChildNode->setParentNode($this);
                $_ChildNode->ID = ++$NodeID;

                $this->addChildNode($_ChildNode);
                return $_ChildNode;
        }

        function addChildNode(&$_ChildNode){

                $this->ChildNodes[] = &$_ChildNode;
        }

        function setParentNode(&$_ParentNode){


                $this->ParentNode = &$_ParentNode;
        }

        function &getParentNode(){

                return $this->ParentNode;
        }

        function getNodeXML($_Level = -1){

                $_Level++;
                $_attrs = array();
                foreach ( $this->Attributes as $_Key=>$_Val ){

                        $_attrs[] = "{$_Key}=\"{$_Val}\"";
                }

                $_ChildrenXMLs = array();

                $_ChildNodesNum = count($this->ChildNodes);
                for ( $i=0; $i<$_ChildNodesNum; $i++ )
                        $_ChildrenXMLs[] = $this->ChildNodes[$i]->getNodeXML($_Level);

                return "<{$this->Name}".(count($_attrs)?" ".implode(" ", $_attrs):'').">".($this->Data?"<![CDATA[".($this->Data)."]]>":"").
                        (count($_ChildrenXMLs)?implode("",$_ChildrenXMLs):'').
                        "</{$this->Name}>";
        }

        function _replaceSpecialChars($_Data){

                $_Data = str_replace('&','&amp;', $_Data);
                return str_replace(array('<','>'), array('&lt;','&gt;'), $_Data);
        }

        function renderTreeFromInner($_Inner){

                $this->ParserResource = xml_parser_create ();
                xml_parser_set_option($this->ParserResource, XML_OPTION_CASE_FOLDING, false);
                xml_set_object($this->ParserResource, $this);
                xml_set_element_handler($this->ParserResource, "_tagOpen", "_tagClosed");

                xml_set_character_data_handler($this->ParserResource, "_tagData");

                $_Inner = xml_parse($this->ParserResource,$_Inner );
                if(!$_Inner) {
                        die(sprintf("XML error: %s at line %d",
                                xml_error_string(xml_get_error_code($this->ParserResource)),
                                xml_get_current_line_number($this->ParserResource)));
                }

                xml_parser_free($this->ParserResource);
        }

        function _tagOpen($parser, $name, $attrs){

                if(!isset($this->parsingNode)){

                        $this->parsingNode = &$this;
                        $this->Name = $name;
                        $this->Attributes = $attrs;
                }else {

                        $_tParent = &$this->parsingNode;
                        $this->parsingNode = &$_tParent->createChildNode($name, $attrs);
                }
        }

        function _tagData($parser, $tagData){

                if(trim($tagData)||$this->parsingNode->Data){

                        $this->parsingNode->Data .= $tagData;
                }
        }

        function _tagClosed($parser, $name){

                if(!$this->parsingNode->getParentNode())
                        unset($this->parsingNode);
                else
                        $this->parsingNode = &$this->parsingNode->getParentNode();
        }

        function getChildrenByName($_Name){

                $_TC = count($this->ChildNodes)-1;
                $Nodes = array();
                for ( ; $_TC>=0; $_TC--){

                        if ($this->ChildNodes[$_TC]->getName() == $_Name){

                                $Nodes[] = &$this->ChildNodes[$_TC];
                        }
                }

                return $Nodes;
        }

        /**
         * Now only /xxx/xxxx/xxxxx
         *
         * @param unknown_type $_xPath
         */
        function xPath($_xPath){

                $TagNames = explode('/', $_xPath);
                $_TagName = '';
                $Nodes = array();

                while (count($TagNames)){

                        $_TagName = array_shift($TagNames);
                        if(!$_TagName)continue;

                        @list($chTagName) = $TagNames;

                        if(!count($TagNames) && $_TagName==$this->getName()){
//                                print '--------';
                                return array(&$this);
                        }

                        $ChildNodes = $this->getChildrenByName($chTagName);

//                        print_r($ChildNodes);
                        $_TC = count($ChildNodes)-1;
                        for(; $_TC>=0; $_TC--){

//                                print_r($TagNames);
//                                print_r($ChildNodes[$_TC]->xPath('/'.$_TagName.'/'.implode('/', $TagNames)));
                                $Nodes = array_merge($Nodes, $ChildNodes[$_TC]->xPath('/'.implode('/', $TagNames)));
                        }
                        break;
                }

                return $Nodes;
        }
}
?>