<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################




class DWord
{
        var $bitArray;

        function DWord()
        {
                $this->bitArray = array();
                for( $i=1; $i<=32;  $i++)
                        $this->bitArray[$i-1] = 0;
        }

        function _setByte( $byte, $displacement )
        {
                // 00000001 = 1
                $this->bitArray[$displacement + 0] = (($byte&1)   != 0)?1:0;
                // 00000010 = 2
                $this->bitArray[$displacement + 1] = (($byte&2)   != 0)?1:0;
                // 00000100 = 4
                $this->bitArray[$displacement + 2] = (($byte&4)   != 0)?1:0;
                // 00001000 = 8
                $this->bitArray[$displacement + 3] = (($byte&8)   != 0)?1:0;
                // 00010000 = 16
                $this->bitArray[$displacement + 4] = (($byte&16)  != 0)?1:0;
                // 00100000 = 32
                $this->bitArray[$displacement + 5] = (($byte&32)  != 0)?1:0;
                // 01000000 = 64
                $this->bitArray[$displacement + 6] = (($byte&64)  != 0)?1:0;
                // 10000000 = 128
                $this->bitArray[$displacement + 7] = (($byte&128) != 0)?1:0;
        }

        function _getByte( $displacement )
        {
                return $this->bitArray[$displacement + 0]*1  +
                                        $this->bitArray[$displacement + 1]*2 +
                                        $this->bitArray[$displacement + 2]*4 +
                                        $this->bitArray[$displacement + 3]*8 +
                                        $this->bitArray[$displacement + 4]*16 +
                                        $this->bitArray[$displacement + 5]*32 +
                                        $this->bitArray[$displacement + 6]*64 +
                                        $this->bitArray[$displacement + 7]*128;
        }

        function SetValue( $byte1, $byte2, $byte3, $byte4  )
        {
                $this->_setByte( $byte1, 0  );
                $this->_setByte( $byte2, 8  );
                $this->_setByte( $byte3, 16 );
                $this->_setByte( $byte4, 24 );
        }

        function GetValue( &$byte1, &$byte2, &$byte3, &$byte4 )
        {
                $byte1 = $this->_getByte( 0  );
                $byte2 = $this->_getByte( 8  );
                $byte3 = $this->_getByte( 16 );
                $byte4 = $this->_getByte( 24 );
        }

        function GetCount()
        {
                $coeff = 1;
                $res = 0;
                for($i=1; $i<=32; $i++)
                {
                        $res += $this->bitArray[$i-1]*$coeff;
                        $coeff *= 2;
                }
                return $res;
        }

        function SetBit( $bitValue, $bitIndex  )
        {
                $this->bitArray[ $bitIndex ] = $bitValue;
        }

        function GetHTML_Representation()
        {
                $res = "";
                $res .= "<table>";

                // head row
                $res .= "        <tr>";
                for( $i=31; $i>=0; $i-- )
                {
                        $res .= "                <td>";
                        $res .= "                        $i";
                        $res .= "                </td>";
                }
                $res .= "        </tr>";

                // bit values
                $res .= "        <tr>";
                for( $i=31; $i>=0; $i-- )
                {
                        $res .= "                <td>";
                        $res .= "                        ".$this->bitArray[$i];
                        $res .= "                </td>";
                }
                $res .= "        </tr>";
                $res .= "</table>";

                return $res;
        }

        function ShiftToLeft( $countBit )
        {
                $resBitArray = $this->bitArray;

                for( $i=31; $i>=0; $i-- )
                        if ( $i +  $countBit <= 31 )
                                $resBitArray[$i + $countBit] = $resBitArray[$i];

                for( $i=1; $i<=$countBit; $i++ )
                        $resBitArray[$i-1]=0;

                $res = new DWord();
                $res->bitArray = $resBitArray;
                return $res;
        }

        function ShiftToRight( $countBit )
        {
                $resBitArray = $this->bitArray;

                for( $i=0; $i<=31; $i++ )
                        if ( $i -  $countBit >= 0 )
                                $resBitArray[$i - $countBit] = $resBitArray[$i];

                for( $i=31; $i>=31-$countBit+1; $i-- )
                        $resBitArray[$i]=0;

                $res = new DWord();
                $res->bitArray = $resBitArray;
                return $res;
        }

        function BitwiseOR( $dwordObject )
        {
                $res = new DWord();
                for( $i=0; $i<=31; $i++ )
                {
                        if ( $this->bitArray[$i]+$dwordObject->bitArray[$i] != 0 )
                                $res->SetBit( 1, $i );
                        else
                                $res->SetBit( 0, $i );
                }
                return $res;
        }

        function BitwiseAND( $dwordObject )
        {
                $res = new DWord();
                for( $i=0; $i<=31; $i++ )
                        $res->SetBit( $this->bitArray[$i]*$dwordObject->bitArray[$i],
                                                $i );
                return $res;
        }

        function BitwiseXOR( $dwordObject )
        {
                $res = new DWord();
                for( $i=0; $i<=31; $i++ )
                {
                        if ($this->bitArray[$i] == $dwordObject->bitArray[$i])
                                $res->SetBit( 1, $i );
                        else
                                $res->SetBit( 0, $i );
                }
                return $res;
        }

        function Plus( $dwordObject )
        {
                $res = new DWord();
                $cf = 0;
                for( $i=0; $i<=3; $i++ )
                {
                        $byte1 = $this->_getByte( $i*8 );
                        $byte2 = $dwordObject->_getByte( $i*8 );

                        $res->_setByte( $byte1 + $byte2 + $cf, $i*8 );
                        if ( $byte1 + $byte2 + $cf >= 256 )
                                $cf = 1;
                }
                return $res;
        }

}



// *****************************************************************************
// Purpose        encrypts cc_number field ( see ORDERS_TABLE in database_structure.xml )
// Inputs
// Remarks
// Returns
function cryptCCNumberCrypt( $cc_number, $key )
{
        return base64_encode($cc_number);
/*
        $res = "";
        $strlen = strlen( $cc_number );
        for( $i=1; $i<=32-$strlen; $i++ )
                $cc_number .= " ";
        $res .= chr( $strlen );

        $dWordArray = array();
        for( $i=1; $i<=8; $i++ )
        {
                $dWordObject = DWord();
                $dWordObject->SetValue(
                                $cc_number[ ($i-1)*4 + 0 ],
                                $cc_number[ ($i-1)*4 + 1 ],
                                $cc_number[ ($i-1)*4 + 2 ],
                                $cc_number[ ($i-1)*4 + 3 ] );
                $dWordArray[] = $dWordObject;
        }

        $dWordArrayCifered = array();
        for( $i=1; $i<=4; $i++ )
        {
                $ciferedData = _gostCrypt( array( $dWordArray[($i-1)*2], $dWordArray[($i-1)*2 + 1]), $key );
                $dWordArrayCifered[] = $ciferedData[0];
                $dWordArrayCifered[] = $ciferedData[1];
        }

        foreach( $dWordArrayCifered as $dWordCifered )
        {
                $byte1 = 0;
                $byte2 = 0;
                $byte3 = 0;
                $byte4 = 0;
                $dWordCifered->GetValue( &$byte1, &$byte2, &$byte3, &$byte4 );
                $res .= chr($byte1);
                $res .= chr($byte2);
                $res .= chr($byte3);
                $res .= chr($byte4);
        }

        return $res;
*/
}


// *****************************************************************************
// Purpose        decrypts cc_number field ( see ORDERS_TABLE in database_structure.xml )
// Inputs
// Remarks
// Returns
function cryptCCNumberDeCrypt( $cifer, $key )
{
        return base64_decode($cifer);
/*
        $res = "";
        $strlen = (int)($cifer[0]);

        $dWordArray = array();
        for( $i=1; $i<=8; $i++ )
        {
                $dWordObject = DWord();
                $dWordObject->SetValue(
                                $cifer[ ($i-1)*4 + 1 ],
                                $cifer[ ($i-1)*4 + 2 ],
                                $cifer[ ($i-1)*4 + 3 ],
                                $cifer[ ($i-1)*4 + 4 ] );
                $dWordArray[] = $dWordObject;
        }

        $dWordArrayDeCifered = array();
        for( $i=1; $i<=4; $i++ )
        {
                $deCiferedData = _gostDeCrypt( array( $dWordArray[($i-1)*2], $dWordArray[($i-1)*2 + 1]), $key );
                $dWordArrayCifered[] = $deCiferedData[0];
                $dWordArrayCifered[] = $deCiferedData[1];
        }

        foreach( $dWordArrayCifered as $dWordCifered )
        {
                $byte1 = 0;
                $byte2 = 0;
                $byte3 = 0;
                $byte4 = 0;
                $dWordCifered->GetValue( &$byte1, &$byte2, &$byte3, &$byte4 );
                $res .= chr($byte1);
                $res .= chr($byte2);
                $res .= chr($byte3);
                $res .= chr($byte4);
        }

        $temp = $res;
        for( $i=1; $i<=$strlen; $i++ )
                $res .= $temp[$i-1];

        return $res;
*/
}


// *****************************************************************************
// Purpose        encrypts cc_holdername field ( see ORDERS_TABLE in database_structure.xml )
// Inputs
// Remarks
// Returns
function cryptCCHoldernameCrypt( $cc_holdername, $key )
{
        return base64_encode( $cc_holdername );
}


// *****************************************************************************
// Purpose        decrypts cc_holdername field ( see ORDERS_TABLE in database_structure.xml )
// Inputs
// Remarks
// Returns
function cryptCCHoldernameDeCrypt( $cifer, $key )
{
        return base64_decode( $cifer );
}


// *****************************************************************************
// Purpose        encrypts cc_expires field ( see ORDERS_TABLE in database_structure.xml )
// Inputs
// Remarks
// Returns
function cryptCCExpiresCrypt( $cc_expires, $key )
{
        return base64_encode( $cc_expires );
}


// *****************************************************************************
// Purpose        decrypts cc_expires field ( see ORDERS_TABLE in database_structure.xml )
// Inputs
// Remarks
// Returns
function cryptCCExpiresDeCrypt( $cifer, $key )
{
        return base64_decode( $cifer );
}


// *****************************************************************************
// Purpose        encrypts customer ( and admin ) password field
//                                        ( see ORDERS_TABLE in database_structure.xml )
// Inputs
// Remarks
// Returns
function cryptPasswordCrypt( $password, $key )
{
        return base64_encode( $password );
}


// *****************************************************************************
// Purpose        decrypts customer ( and admin ) password field ( see ORDERS_TABLE in database_structure.xml )
// Inputs
// Remarks
// Returns
function cryptPasswordDeCrypt( $cifer, $key )
{
        return base64_decode( $cifer );
}


// *****************************************************************************
// Purpose        encrypts getFileParam
// Inputs
// Remarks        see also get_file.php
// Returns
function cryptFileParamCrypt( $getFileParam, $key )
{
        return base64_encode( $getFileParam );
}


// *****************************************************************************
// Purpose        decrypt getFileParam
// Inputs
// Remarks        see also get_file.php
// Returns
function cryptFileParamDeCrypt( $cifer, $key )
{
        return base64_decode( $cifer );
}


//--------------------------------------
// initialize


// it is single byte values
$bK8 = array( 14,  4, 13,  1,  2, 15, 11,  8,  3, 10,  6, 12,  5,  9,  0,  7 );
$bK7 = array( 15,  1,  8, 14,  6, 11,  3,  4,  9,  7,  2, 13, 12,  0,  5, 10 );
$bK6 = array( 10,  0,  9, 14,  6,  3, 15,  5,  1, 13, 12,  7, 11,  4,  2,  8 );
$bK5 = array(  7, 13, 14,  3,  0,  6,  9, 10,  1,  2,  8,  5, 11, 12,  4, 15 );
$bK4 = array(  2, 12,  4,  1,  7, 10, 11,  6,  8,  5,  3, 15, 13,  0, 14,  9 );
$bK3 = array( 12,  1, 10, 15,  9,  2,  6,  8,  0, 13,  3,  4, 14,  7,  5, 11 );
$bK2 = array(  4, 11,  2, 14, 15,  0,  8, 13,  3, 12,  9,  7,  5, 10,  6,  1 );
$bK1 = array( 13,  2,  8,  4,  6, 15, 11,  1, 10,  9,  3, 14,  5,  0, 12,  7 );

// it is single byte values
$bK87 = array();
$bK65 = array();
$bK43 = array();
$bK21 = array();

for ($i=0; $i<256; $i++)
{
        $bK87[$i] = $bK8[$i >> 4] << 4 | $bK7[$i & 15];
        $bK65[$i] = $bK6[$i >> 4] << 4 | $bK5[$i & 15];
        $bK43[$i] = $bK4[$i >> 4] << 4 | $bK3[$i & 15];
        $bK21[$i] = $bK2[$i >> 4] << 4 | $bK1[$i & 15];
}


function _f( $x )
{
        global $bK87;
        global $bK65;
        global $bK43;
        global $bK21;


        // $bK87[$x>>24 & 255] << 24
        $x1 = $x->ShiftToRight(24);
        $x1 = $x1->BitwiseAND(255);
        $temp = $bK87[ (int)$x1->GetCount() ];
        $x1 = new DWord();
        $x1->SetValue( $temp, 0, 0, 0 );
        $x1->ShiftToLeft( 24 );
        debug( $x1->GetCount() );

        // $bK65[$x>>16 & 255] << 16
        $x2 = $x->ShiftToLeft(16);
        $x2 = $x2->BitwiseAND(255);
        $temp = $bK65[ $x2->GetCount() ];
        $x2 = new DWord();
        $x2->SetValue( $temp, 0, 0, 0 );
        $x2->ShiftToLeft(16);

        // $bK43[$x>> 8 & 255] <<  8
        $x3 = $x->ShiftToRight(8);
        $x3 = $x3->BitwiseAND(255);
        $temp = $bK43[ $x3->GetCount() ];
        $x3 = new DWord();
        $x3->SetValue( $temp, 0, 0, 0 );
        $x3->ShiftToLeft(8);

        // $bK21[$x & 255]
        $x4 = $x->BitwiseAND(255);
        $temp = $bK21[ $x4->GetCount() ];
        $x4 = new DWord();
        $x4->SetValue( $temp, 0, 0, 0 );


        //$x =        $bK87[$x>>24 & 255] << 24 | $bK65[$x>>16 & 255] << 16 |
        //                $bK43[$x>> 8 & 255] <<  8 | $bK21[$x & 255];
        $res = $x1->BitwiseOR( $x2 );
        $res = $res->BitwiseOR( $x3 );
        $res = $res->BitwiseOR( $x4 );

        return $res;
}


// *****************************************************************************
// Purpose        GOST cryptography function
// Inputs           $in                - 2 item of 32 values  ( source data )
//                                $key        - 8 item of 32 values ( key to encrypted )
// Remarks
// Returns        cyfered data
function _gostCrypt( $in, $key )
{
        $n1 = $in[0];
        $n2 = $in[1];

        /* Instead of swapping halves, swap names each round */
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[0])) );
        debug( $n1->GetCount() );
        debug( $key[0]->GetCount() );
        $n2 = _f($n1->Plus($key[0]));
        debug( $n2." = ".$n2->GetCount() );

        debug("=========================== Cifer ============================");
        debug( $n2->GetHTML_Representation() );
        $byte1 = null;
        $byte2 = null;
        $byte3 = null;
        $byte4 = null;
        $n2->GetValue( $byte1, $byte2, $byte3, $byte4 );
        debug( $byte1 );
        debug( $byte2 );
        debug( $byte3 );
        debug( $byte4 );
        debug("==============================================================");



        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[1])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[2])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[3])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[4])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[5])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[6])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[7])) );

        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[0])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[1])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[2])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[3])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[4])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[5])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[6])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[7])) );

        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[0])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[1])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[2])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[3])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[4])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[5])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[6])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[7])) );

        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[7])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[6])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[5])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[4])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[3])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[2])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[1])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[0])) );

        $out = array();
        $out[0] = $n2;
        $out[1] = $n1;

        return $out;
}


function _gostDeCrypt( $out, $key )
{
        $n1 = $in[0];
        $n2 = $in[1];

        /* Instead of swapping halves, swap names each round */
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[0])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[1])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[2])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[3])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[4])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[5])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[6])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[7])) );

        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[7])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[6])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[5])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[4])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[3])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[2])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[1])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[0])) );

        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[7])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[6])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[5])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[4])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[3])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[2])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[1])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[0])) );

        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[7])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[6])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[5])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[4])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[3])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[2])) );
        $n2 = $n2->BitwiseXOR( _f($n1->Plus($key[1])) );
        $n1 = $n1->BitwiseXOR( _f($n2->Plus($key[0])) );

        $out = array();
        $out[0] = $n2;
        $out[1] = $n1;

        return $out;
}

?>