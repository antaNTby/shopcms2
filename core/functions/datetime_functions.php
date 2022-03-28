<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################



// *****************************************************************************
// Purpose        gets current date time in database format
// Inputs   nothing
// Remarks
// Returns        date base specific date time
function get_current_time()         // gets current date and time as a string in MySQL format
{
        return strftime("%Y-%m-%d %H:%M:%S", time()+intval(CONF_TIMEZONE)*3600);
}


//converts datetime provided as a string into a standard form (date format is defined in store settings)
function dtConvertToStandartForm( $datetime, $showtime = 0 )
{
        // 2004-12-30 13:25:41
        $array = explode( " ", $datetime );
        $date = $array[0];
        $time = $array[1];

        $dateArray = explode( "-", $date );
        $day        = $dateArray[2];
        $month        = $dateArray[1];
        $year        = $dateArray[0];

        if (!strcmp(_getSettingOptionValue("CONF_DATE_FORMAT"), "MM/DD/YYYY"))
                $date = $month."/".$day."/".$year;
        else
                $date = $day.".".$month.".".$year;

        if ($showtime == 1)
                return $date." ".$time;
        else
                return $date;
}

//converts datetime provided as a string into an array
function dtGetParsedDateTime( $datetime )
{
        // 2004-12-30 13:25:41 - MySQL database datetime format

        $array = explode( " ", $datetime ); //divide date and time
        $date = $array[0];
        $time = $array[1];

        $dateArray = explode( "-", $date );

        return array(
                        "day"                 => (int)$dateArray[2],
                        "month"                => (int)$dateArray[1],
                        "year"                => (int)$dateArray[0]
        );
}

//$dt is a datetime string in MySQL default format (e.g. 2005-12-25 23:59:59)
//this functions converts it to format selected in the administrative mode
function format_datetime($dt)
{
        $dformat = (!strcmp(CONF_DATE_FORMAT,"DD.MM.YYYY")) ? "d.m.Y H:i:s" : "m/d/Y h:i:s A";
        $a = @date($dformat, strtotime($dt));
        return $a;
}

//$dt is a datetime string to MySQL default format (e.g. 2005-12-25)
//this functions converts it to format selected in the administrative mode
function dtDateConvert($dt)
{
        $dformat = (!strcmp(CONF_DATE_FORMAT,"DD.MM.YYYY")) ? "." : "/";
        $array = explode( $dformat, $dt );
        $date = $array[2]."-".$array[1]."-".$array[0];
        return $date;
}

?>