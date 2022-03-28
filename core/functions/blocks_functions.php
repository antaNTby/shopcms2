<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

function GetAllBlocksAttributes() {
    $q = db_query("select bid, title, bposition, active, which, sort, html, url, admin from ".BLOCKS_TABLE." ORDER BY sort ASC");
    $data = array( );    
	while ( $row = db_fetch_row($q)) {
        if ( $row["html"] == 1 ) {
            if ( file_exists("core/tpl/user/".CONF_DEFAULT_TEMPLATE."/blocks/".$row["url"])) $data[] = $row;
        }
        else $data[] = $row;
    }
    return $data;
}

function Powerblocks($switches, $b_id) {
    db_query("update ".BLOCKS_TABLE." set active=".( int ) $switches." where bid=".( int ) $b_id);
}

function SortBlocks() {
    $data = ScanPostVariableWithId(array( "sort" ));
    foreach ( $data as $key => $val ) {
        if ( isset ( $val["sort"] )) {
            db_query("UPDATE ".BLOCKS_TABLE." SET sort=".( int ) $val["sort"]." WHERE bid=".( int ) $key);
        }
    }
}

function blockspgGetblocksPage($page_ID) {
    $q = db_query("select title, content, bposition, active, which, html, url, admin, pages, dpages, categories, products, about from ".BLOCKS_TABLE." where bid=".( int ) $page_ID);
    if ( $row = db_fetch_row($q)) {
        $row["bid"] = ( int ) $page_ID;
        $row["pages"] = unserialize($row["pages"]);
        $row["dpages"] = unserialize($row["dpages"]);
        $row["categories"] = unserialize($row["categories"]);
		$row["products"] = unserialize($row["products"]);
    }
    return $row;
}

function blockspgUpdateblocksPage($page_ID, $page_name, $page_text, $which, $bposition, $active, $admin, $s, $d, $c, $p) {
    $rs = isset ( $s ) ? serialize($s) : serialize(array());
    $rd = isset ( $d ) ? serialize($d) : serialize(array());
    $rc = isset ( $c ) ? serialize($c) : serialize(array());
	$rpt = explode("\n",chop($p));
    $rp = array();
    for ($i=0; $i<count($rpt); $i++) if($tmp=(int) rtrim($rpt[$i]) > 0 && rtrim($rpt[$i]) !== "") $rp[] = (int) rtrim($rpt[$i]);
    $rp = serialize($rp);
    db_query("update ".BLOCKS_TABLE." set 
	title='".xToText($page_name)."', "." 
	content='".xEscSQL($page_text)."', "." 
	bposition=".( int ) $bposition.", "." 
	active=".( int ) $active.", "." 
	which=".( int ) $which.", "." 
	admin=".( int ) $admin.", "." 
	pages='".xEscSQL($rs)."', "." 
	dpages='".xEscSQL($rd)."', "." 
	categories='".xEscSQL($rc)."', "." 
	products='".xEscSQL($rp)."' "." 
	where bid=".( int ) $page_ID);
}

function blockspgAddblocksPage($page_name, $page_text, $which, $bposition, $active, $admin, $s, $d, $c, $p) {
    $rs = isset ( $s ) ? serialize($s) : serialize(array());
    $rd = isset ( $d ) ? serialize($d) : serialize(array());
    $rc = isset ( $c ) ? serialize($c) : serialize(array());
	$rpt = explode("\n",chop($p));
    $rp = array();
    for ($i=0; $i<count($rpt); $i++) if($tmp=(int) rtrim($rpt[$i]) > 0 && rtrim($rpt[$i]) !== "") $rp[] = (int) rtrim($rpt[$i]);
    $rp = serialize($rp);
    db_query("insert into ".BLOCKS_TABLE." ( title, content, bposition, active, which, admin, pages, dpages, categories, products )  "." values( '".xToText($page_name)."', '".xEscSQL($page_text)."', ".( int ) $bposition.", ".( int ) $active.", ".( int ) $which.", ".( int ) $admin.", '".xEscSQL($rs)."',
                '".xEscSQL($rd)."', '".xEscSQL($rc)."', '".xEscSQL($rp)."') ");
}

function blockspgAddblocksPageFile($page_name, $page_file, $which, $bposition, $active, $admin, $s, $d, $c, $p) {
    $rs = isset ( $s ) ? serialize($s) : serialize(array());
    $rd = isset ( $d ) ? serialize($d) : serialize(array());
    $rc = isset ( $c ) ? serialize($c) : serialize(array());
	$rpt = explode("\n",chop($p));
    $rp = array();
    for ($i=0; $i<count($rpt); $i++) if($tmp=(int) rtrim($rpt[$i]) > 0 && rtrim($rpt[$i]) !== "") $rp[] = (int) rtrim($rpt[$i]);
    $rp = serialize($rp);
    db_query("insert into ".BLOCKS_TABLE." ( title, bposition, active, which, html, url, admin, pages, dpages, categories, products )  "." values( '".xToText($page_name)."', ".( int ) $bposition.", ".( int ) $active.", ".( int ) $which.", '1', '".$page_file."', ".( int ) $admin.", '".xEscSQL($rs)."',
                '".xEscSQL($rd)."', '".xEscSQL($rc)."', '".xEscSQL($rp)."') ");
}

function blockspgDeleteblocks($page_ID) {
    db_query("delete from ".BLOCKS_TABLE." where bid=".( int ) $page_ID);
}

?>