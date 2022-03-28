<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

function newsGetNewsToCustomer()
{
        $q = db_query( "select NID, add_date, title, textToPrePublication from ".NEWS_TABLE." order by add_date DESC LIMIT 0,".CONF_NEWS_COUNT_IN_CUSTOMER_PART);
        $data = array();

        while( $r=db_fetch_row($q) )
        {
               $r["add_date"]=dtConvertToStandartForm($r["add_date"]);
               $data[] = $r;
        }
        return $data;
}

function newsGetPreNewsToCustomer()
{
        $q = db_query( "select NID, add_date, title, textToPrePublication from ".NEWS_TABLE." order by add_date DESC LIMIT 0,".CONF_NEWS_COUNT_IN_NEWS_PAGE);
        $data = array();

        while( $r=db_fetch_row($q) )
        {
               $r["add_date"]=dtConvertToStandartForm($r["add_date"]);
               $data[] = $r;
        }
        return $data;
}


function newsGetFullNewsToCustomer($newsid)
{
        $q = db_query( "select add_date, title, textToPrePublication, textToPublication from ".NEWS_TABLE." where NID=".(int)$newsid);
        if  ( $r = db_fetch_row($q) )
        {
        $r["add_date"]=dtConvertToStandartForm($r["add_date"]);
        $r["NID"] = (int)$newsid;
		}
        return $r;
}

function newsGetNewsToEdit($newsid)
{
        $q = db_query( "select add_date, title, textToPrePublication, textToPublication, textToMail from ".NEWS_TABLE." where NID=".(int)$newsid);
        $r=db_fetch_row($q);
        $r["add_date"]=dtConvertToStandartForm($r["add_date"]);
        return $r;
}

function newsGetAllNews( $callBackParam, &$count_row, $navigatorParams = null )
{
        if ( $navigatorParams != null )
        {
                $offset                        = $navigatorParams["offset"];
                $CountRowOnPage        = $navigatorParams["CountRowOnPage"];
        }
        else
        {
                $offset = 0;
                $CountRowOnPage = 0;
        }

        $q = db_query( "select NID, add_date, title from ".NEWS_TABLE." order by add_stamp DESC" );

        $i = 0;
        $data = array();
        while( $r=db_fetch_row($q) )
        {
                if ( ($i >= $offset && $i < $offset + $CountRowOnPage) ||
                                $navigatorParams == null  )
                {
                   $r["add_date"]=dtConvertToStandartForm($r["add_date"]);
                   $data[] = $r;
                }
                $i++;
        }
        $count_row = $i;
        return $data;
}

function newsAddNews( $add_date, $title, $textToPrePublication, $textToPublication, $textToMail )
{
        $stamp = microtime();
        $stamp = explode(" ", $stamp);
        $stamp = $stamp[1];
        db_query( "insert into ".NEWS_TABLE." ( add_date, title, textToPrePublication, textToPublication, textToMail, add_stamp ) ".
                  " values( '".xEscSQL(dtDateConvert($add_date))."', '".xToText(trim($title))."', '".xEscSQL($textToPrePublication)."', '".xEscSQL($textToPublication)."', '".xEscSQL($textToMail)."', ".$stamp." ) ");
        return db_insert_id();
}

function newsUpdateNews( $add_date, $title, $textToPrePublication, $textToPublication, $textToMail, $id_news )
{
                db_query("update ".NEWS_TABLE.
                 " set     add_date='".xEscSQL(dtDateConvert($add_date))."', ".
                 "         title='".xToText($title)."', ".
                 "         textToPrePublication='".xEscSQL($textToPrePublication)."', ".
                 "         textToPublication='".xEscSQL($textToPublication)."', ".
                 "         textToMail='".xEscSQL($textToMail)."' ".
                 " where NID = ".(int)$id_news);
}

function newsDeleteNews( $newsid )
{
        db_query( "delete from ".NEWS_TABLE." where NID=".(int)$newsid );
}

function newsSendNews($newsid)
{
        $q = db_query( "select add_date, title, textToMail from ".NEWS_TABLE." where NID=".(int)$newsid );
        $news = db_fetch_row( $q );
        $news["add_date"]=dtConvertToStandartForm($news["add_date"]);
        $q = db_query( "select Email from ".MAILING_LIST_TABLE );
        while( $subscriber = db_fetch_row($q) ) xMailTxtHTMLDATA($subscriber["Email"], EMAIL_NEWS_OF." - ".CONF_SHOP_NAME, $news["title"]."<br><br>".$news["textToMail"]);
}

?>