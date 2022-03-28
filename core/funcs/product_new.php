<?php

if ((!isset($productID)) || ($productID < 0)) return;

function php2js($a=false)
{
    if (is_null($a) || is_resource($a)) {
        return 'null';
    }
    if ($a === false) {
        return 'false';
    }
    if ($a === true) {
        return 'true';
    }

    if (is_scalar($a)) {
        if (is_float($a)) {
            //Always use "." for floats.
            $a = str_replace(',', '.', strval($a));
        }

        // All scalars are converted to strings to avoid indeterminism.
        // PHP's "1" and 1 are equal for all PHP operators, but
        // JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
        // we should get the same result in the JS frontend (string).
        // Character replacements for JSON.
        static $jsonReplaces = array(
            array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
            array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')
        );

        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
    }

    $isList = true;

    for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
        if (key($a) !== $i) {
            $isList = false;
            break;
        }
    }

    $result = array();

    if ($isList) {
        foreach ($a as $v) {
            $result[] = php2js($v);
        }

        return '[ ' . join(', ', $result) . ' ]';
    } else {
        foreach ($a as $k => $v) {
            $result[] = php2js($k) . ': ' . php2js($v);
        }

        return '{ ' . join(', ', $result) . ' }';
    }
}

//все товары без ограничений начинай с текущей категории до корня
function GetCategoryParent($categoryID)
{
    $sql_query = 'SELECT parent FROM ' . CATEGORIES_TABLE . ' WHERE categoryID=' . $categoryID;
    $q = mysql_query($sql_query);
    $row = mysql_fetch_assoc($q);
    $Parent = $row['parent'];
    return $Parent;
}

function GetRecommendsAllLevels($categoryID, $productID)
{
    $Products = array();
    $CurrentProducts = GetProductsWithImagesByCategoryId($categoryID, $productID);
    $Products = array_merge($Products, $CurrentProducts);
    $ParentId = GetCategoryParent($categoryID);
    while ($ParentId != 1) {
        $categoryID = $ParentId;
        $CurrentProducts = GetProductsWithImagesByCategoryId($categoryID);
        $Products = array_merge($Products, $CurrentProducts);
        $ParentId = GetCategoryParent($categoryID);
    }
    return $Products;
}

function GetProductsWithImagesByCategoryId($CategoryId, $ExcludeProductId = false)
{
    $exclude = ($ExcludeProductId) ? " AND ( p.productID != $ExcludeProductId )" : '';

    //Товары этой категории
    $sql_query = "SELECT REPLACE( tp.name,'{city}',p.tpl_city ) as name, UID,
		IF(p.tplID>0,tp.Price,p.Price) Price,
		IF(p.tplID>0,tp.folder_pictures, p.folder_pictures) as folder_pictures, p.pictures, pp.enlarged FROM " . PRODUCTS_TABLE . " p
		LEFT JOIN " . PRODUCTS_TPL_TABLE . ' tp USING(tplID)
		LEFT JOIN ' . PRODUCT_PICTURES . ' pp ON (p.default_picture = pp.photoID)
		WHERE categoryID=' . $CategoryId . $exclude . ' ORDER BY IF(p.tplID>0,tp.sort_order,p.sort_order), name';

    $sql = mysql_query($sql_query);
    $Products = array();
    while ($row = mysql_fetch_assoc($sql)) {
        $row['pictures'] = explode(',', $row['pictures']);
        if ($row['folder_pictures'])
            $img = "data/pictures_tpl/" . $row['folder_pictures'] . "/125/" . array_shift($row['pictures']);
        else
            $img = 'data/pictures/osn/' . $row['enlarged'];

        $Product = array(
            'name' => $row['name'],
            'UID' => $row['UID'],
            'Price' => show_price($row['Price']),
            'img' => $img
        );
        $Products[] = $Product;
    }
    return $Products;
}

//все товары из этой же категории
function GetRecommends1($productID, $categoryID)
{
    $sql_query = "SELECT REPLACE( tp.name,'{city}',p.tpl_city ) as name, tp.Price as tplPrice, p.Price, tp.folder_pictures, p.pictures, pp.enlarged, p.UID
		FROM " . PRODUCTS_TABLE . ' p
		LEFT JOIN ' . PRODUCTS_TPL_TABLE . ' tp USING(tplID)
		LEFT JOIN ' . PRODUCT_PICTURES . ' pp ON (p.default_picture = pp.photoID)
		WHERE p.categoryID = ' . $categoryID . ' AND p.productID != ' . $productID . '
		ORDER BY viewed_times DESC
		';
    $q = mysql_query($sql_query);

    $recomeds1 = array();

    while ($row = mysql_fetch_assoc($q)) {
        $recomed1 = array();
        if ($row['folder_pictures']) {
            $arr = explode(",", $row['pictures']);
            $img = "data/pictures_tpl/" . $row['folder_pictures'] . "/125/" . array_shift($arr);
        } else
            $img = 'data/pictures/osn/' . $row['enlarged'];

        $recomed1['name'] = $row['name'];
        $recomed1['Price'] = ($row['tplPrice'] > 0) ? show_price($row['tplPrice']) : show_price($row['Price']);
        $recomed1['img'] = $img;
        $recomed1['UID'] = $row['UID'];
        $recomeds1[] = $recomed1;
    }
    return $recomeds1;
}


//5 из родительской категории
function GetRecommends2($productID, $categoryID)
{
    $q = mysql_query("SELECT parent FROM " . CATEGORIES_TABLE . " WHERE categoryID = $categoryID");
    $parent_id = mysql_result($q, 0);

    //один productID - может быть в разных категориях???
    $sql_query = "SELECT REPLACE( tp.name,'{city}',p.tpl_city ) as name, tp.Price as tplPrice, p.Price, tp.folder_pictures, p.pictures, p.UID, pp.enlarged
		FROM " . PRODUCTS_TABLE . ' p
		LEFT JOIN ' . PRODUCT_PICTURES . ' pp USING (productID)
		LEFT JOIN ' . PRODUCTS_TPL_TABLE . ' tp USING(tplID)
		LEFT JOIN ' . CATEGORIES_TABLE . ' c USING (categoryID)
		WHERE categoryID = ' . $parent_id . '
		AND p.default_picture = pp.photoID
		ORDER BY p.viewed_times DESC
		';

    $q = mysql_query($sql_query);

    $recomeds2_index = 0;
    $recomeds2 = array();
    while (($row = mysql_fetch_assoc($q)) && ($recomeds2_index < 5)) {
        if ($row['folder_pictures']) {
            $arr = explode(",", $row['pictures']);
            $img = "data/pictures_tpl/" . $row['folder_pictures'] . "/125/" . array_shift($arr);
        } else
            $img = 'data/pictures/osn/' . $row['enlarged'];

        $recomed2['name'] = $row['name'];
        $recomed2['Price'] = ($row['tplPrice'] > 0) ? show_price($row['tplPrice']) : show_price($row['Price']);
        $recomed2['img'] = $img;
        $recomed2['UID'] = $row['UID'];
        $recomeds2[] = $recomed2;

        $recomeds2_index++;
    }
    return $recomeds2;
}

//не знаю что это такое :)
function GetAllProductPictures($a)
{
    $path_to_tpl_pictures = "data/pictures_tpl/";
    $path_to_pictures = "data/pictures/";
    $productID = $a['productID'];

    $all_product_pictures = array();
    if ($a['folder_pictures']) {
        foreach ($a['pictures'] as $key => $value) {
            //Must be data/pictures_tpl
            $tmp = $path_to_tpl_pictures . $a['folder_pictures'] . "/80/" . $value;
            $tmp2 = $path_to_tpl_pictures . $a['folder_pictures'] . "/" . $value;
            if (file_exists($tmp))
                $all_product_pictures[$key] = array($tmp, $tmp2);
        }
    } else {
        if (isset($_GET["picture_id"]))
            $pictures = db_query("select photoID, filename, thumbnail, enlarged from " . PRODUCT_PICTURES . " where photoID!=" . (int)$_GET["picture_id"] . " AND productID=" . $productID);
        elseif ($a["default_picture"])
            $pictures = db_query("select photoID, filename, thumbnail, enlarged,typ from " . PRODUCT_PICTURES . " where photoID!=" . $a["default_picture"] . " AND productID=" . $productID);
        else
            $pictures = db_query("select photoID, filename, thumbnail, enlarged,typ from " . PRODUCT_PICTURES . " where productID=" . (string)$productID);

        while ($picture = db_fetch_row($pictures)) {
            if ($picture['filename'] && $picture['enlarged']) {
                $tmp = $path_to_pictures . "lmenu/" . $picture['enlarged'];
                $tmp2 = $path_to_pictures . $picture['enlarged'];
                if (file_exists($tmp) && file_exists($tmp2)) {
                    $all_product_pictures[] = array($tmp, $tmp2);
                }
            }
        }
    }
    return $all_product_pictures;
}


function GetSubProduct($a, $subID)
{
    $q = mysql_query("SELECT description, seo_gen, Price, name FROM avl_products_subtpl WHERE tplID = " . (int)$subID);
    $t = mysql_query("SELECT Price FROM avl_products_tpl WHERE tplID = " . (int)$a['tplID']);
    //Перенаправление на страницу товара в случае не верного указания подтовара
    if (mysql_num_rows($q) <= 0) {
        header("HTTP/1.1 301 Moved Permanently");
        header('Location:' . $urlProduct);
        die;
    }

    $text = mysql_result($q, 0);
    $arrWords = array(
        '{city}' => $a['tpl_city'],
        '{val_1}' => $a['tpl_val_1'],
        '{val_2}' => htmlspecialchars_decode($a['tpl_val_2']),
        '{productID}' => $productID
    );

    $text = strtr($text, $arrWords);
    $seo_gen = mysql_result($q, 0, 1);
    $price = mysql_result($t, 0);
    $subProductName = mysql_result($q, 0, 3);
    $subProductName = strtr($subProductName, $arrWords);
    $a["PriceWithUnit"] = show_price($price);

    $a['name'] = $subProductName;
    if (!empty($text) && $seo_gen == 1) {
        preg_match_all('/\[(.*)\]/U', $text, $pattern);
        $indexes = unserialize($a['text_index']);
        foreach ($indexes as $k => $v) {
            $str = $pattern[1][$k];
            $part = explode('|', $str);
            $to_replace['[' . $str . ']'] = $part[$v];
        }

        $to_show = strtr($text, $to_replace);
        $description = $to_show;
    } else {
        $description = $text;
    }
}

function GetRelated2($productID)
{
    $related2 = array();
    $q = db_query("SELECT p.productID as productID, rp.sort_order, link,sim_text, UID FROM " . RELATED_PRODUCTS_TABLE . "_v2 as rp JOIN " . PRODUCTS_TABLE . " as p USING(productID) WHERE Owner=" . $productID . " order by rp.sort_order");
    while ($row = db_fetch_row($q)) $related2[] = $row;
    return $related2;
}

$product = GetProduct($productID);
if ((!$product) || ($product["enabled"] == 0)) {
    Redirect("index.php");//why not root '/'?
    //exit(); - in redirect
}


$dontshowcategory = 1;

$imgTextWarning = CONF_IMG_WARNING;

$smarty->assign("imgTextWarning", $imgTextWarning);
$smarty->assign("main_content_template", "product_detailed.tpl.html");

//$cat_info = db_fetch_row(db_query("SELECT typ FROM ".CATEGORIES_TABLE." WHERE categoryID=".(int)$a['categoryID']));
//$a["typ"]=$cat_info['typ'];
$a = $product;
$a["maps_sort"] = (int)$a["maps_sort"];
$a["PriceWithUnit"] = show_price($a["Price"]);
$a["list_priceWithUnit"] = show_price($a["list_price"]);
$a["PriceWithOutUnit"] = show_priceWithOutUnit($a["Price"]);

if (((float)$a["shipping_freight"]) > 0) $a["shipping_freightUC"] = show_price($a["shipping_freight"]);

if (isset($_GET["picture_id"])) //hz
{
    $picture = db_query("select filename, thumbnail, enlarged from " . PRODUCT_PICTURES . " where photoID=" . (int)$_GET["picture_id"]);
    $picture_row = db_fetch_row($picture);
} elseif (!is_null($a["default_picture"])) {
    $picture = db_query("select filename, thumbnail, enlarged from " . PRODUCT_PICTURES . " where photoID=" . $a["default_picture"] . ' LIMIT 1');
    $picture_row = db_fetch_row($picture);
} else {
    $picture = db_query("select filename, thumbnail, enlarged, photoID from " . PRODUCT_PICTURES . " where productID='" . $productID . "'");
    if ($picture_row = db_fetch_row($picture)) $a["default_picture"] = $picture_row["photoID"];
    else $picture_row = null;
}

if ($a['folder_pictures']) {
    $a["pictures"] = explode(",", $a["pictures"]);
    //$a["thumbnail"]  = "";
    $big_picture = array_shift($a["pictures"]);
    $a["big_picture"] = "data/pictures_tpl/" . $a["folder_pictures"] . "/380/" . $big_picture;
    $a["full_picture"] = "data/pictures_tpl/" . $a["folder_pictures"] . "/" . $big_picture;
} elseif ($picture_row) {
    $a["picture"] = $picture_row['filename'];
    $a["thumbnail"] = $picture_row['thumbnail'];
    $a["big_picture"] = "data/pictures/main_pic/" . $picture_row[2];
    $a["full_picture"] = "data/pictures/" . $picture_row[2];
}

if (!isset($categoryID)) $categoryID = $a["categoryID"];
$smarty->assign("categoryID", $categoryID);

//get selected category info - Need fix by template value ???  - not used on product page
/*$q = db_query("SELECT categoryID, parent, name, description, picture, sort_order FROM ".CATEGORIES_TABLE." WHERE categoryID=".$categoryID) or die (db_error());
$row = db_fetch_row($q);
$smarty->assign("selected_category", $row);
*/
//calculate a path to the category
$smarty->assign("product_category_path", catCalculatePathToCategory($categoryID));


//extra parameters
$extra = GetExtraParametrs($productID);
$smarty->assign("product_extra", $extra);

$smarty->assign("productslinkscat", getcontentprod($productID));

//update several product fields
if (isset($a["picture"]))
    if (!file_exists("" . $a["picture"])) $a["picture"] = 0;

if (isset($a["thumbnail"]))
    if (!file_exists("" . $a["thumbnail"])) $a["thumbnail"] = 0;

$all_product_pictures = GetAllProductPictures($a);
$smarty->assign("all_product_pictures", $all_product_pictures);


//eproduct
if (strlen($a["eproduct_filename"]) > 0 && file_exists("files/" . $a["eproduct_filename"])) {
    $size = filesize("files/" . $a["eproduct_filename"]);
    if ($size > 1000) $size = round($size / 1000);
    $a["eproduct_filesize"] = $size . " Kb";
} else {
    $a["eproduct_filename"] = "";
}

//initialize product "request information" form in case it has not been already submitted
if (!isset($_POST["request_information"])) {
    if (!isset($_SESSION["log"])) {
        $customer_name = "";
        $customer_email = "";
    } else {
        $custinfo = regGetCustomerInfo2($_SESSION["log"]);
        $customer_name = $custinfo["first_name"] . " " . $custinfo["last_name"];
        $customer_email = $custinfo["Email"];
    }

    $message_text = "";
}
$smarty->hassign("customer_name", $customer_name);
$smarty->hassign("customer_email", $customer_email);
$smarty->hassign("message_text", $message_text);

//$smarty->assign("product_related", $related);

$related2 = GetRelated2($productID);//from RELATED_PRODUCTS_TABLE_v2
$smarty->assign("product_related2", $related2);

if (isset($_GET["sent"])) $smarty->assign("sent", 1);

//$smarty->assign("product_info", $a);

//reviews number
$q = db_query("SELECT count(*) FROM " . DISCUSSIONS_TABLE . " WHERE productID=" . $productID) or die (db_error());
$k = db_fetch_row($q);
$k = $k[0];
$smarty->assign("product_reviews_count", $k);

//все из этой же категории
//$recomeds1 = GetRecommends1($productID,$categoryID);
$recomeds1 = GetRecommendsAllLevels($categoryID, $productID);
$smarty->assign("recomeds1", $recomeds1);

$recomeds2 = GetRecommends2($productID, $categoryID);
$smarty->assign("recomeds2", $recomeds2);

//Вставка в шаблон товара параметров {city}, {val_1} и др

if ($subID == "0") {
    $q = mysql_query("SELECT description, seo_gen, Price, sub_products FROM avl_products_tpl WHERE tplID = " . (int)$a['tplID']);
    $q = mysql_fetch_assoc($q);
    $text = $a['description'];
    $subPr = $q['sub_products'];

    $subProds = unserialize($subPr);
    if ($subProds) {
        $arrWords = array(
            '{city}' => $a['tpl_city'],
            '{val_1}' => $a['tpl_val_1'],
            '{val_2}' => htmlspecialchars_decode($a['tpl_val_2']),
            '{productID}' => $productID
        );//papush

        //$SubProdsCount = count($subProds);
        //get column sub_val
        $SubVals = array();
        foreach ($subProds as $subProd) {
            $sub_val = $subProd['sub_val'];
            $SubVals['{' . $subProd['sub_val'] . '}'] = $subProd['sub_sel'];
        }

        $sql_query = 'SELECT tplID, sub_title FROM ' . PRODUCTS_SUBTPL_TABLE . ' WHERE tplID IN (' . join(',', $SubVals) . ')';
        $result = mysql_query($sql_query);
        $rows = array();
        $Urls = array();
        while ($row = mysql_fetch_assoc($result)) $Urls[$row['tplID']] = $row['sub_title'];

        foreach ($SubVals as &$SubVal) {
            $SubVal = $a['UID'] . $Urls[$SubVal] . '.html';
        }

        $arrWords = array_merge($arrWords, $SubVals);
        $text = strtr($text, $arrWords);
    }
    $seo_gen = $q['seo_gen'];
    //$price = $q['Price'];
    //$a["PriceWithUnit"] = show_price( $price );
    if (!empty($text) && $seo_gen == 1) {
        preg_match_all('/\[(.*)\]/U', $text, $pattern);
        $indexes = unserialize($a['text_index']);
        foreach ($indexes as $k => $v) {
            $str = $pattern[1][$k];
            $part = explode('|', $str);
            $to_replace['[' . $str . ']'] = $part[$v];
        }

        $to_show = strtr($text, $to_replace);
        $description = $to_show;
    } else {
        $description = $text;
        $a['description'] = $text;
    }

} else {
    //GetSubProduct($a, $subID);
    $q = mysql_query("SELECT description, seo_gen, Price, name FROM avl_products_subtpl WHERE tplID = " . (int)$subID);
    $t = mysql_query("SELECT Price FROM avl_products_tpl WHERE tplID = " . (int)$a['tplID']);
    //Перенаправление на страницу товара в случае не верного указания подтовара
    if (mysql_num_rows($q) <= 0) {
        header("HTTP/1.1 301 Moved Permanently");
        header('Location:' . $urlProduct);
        die;
    }

    $text = mysql_result($q, 0);
    $arrWords = array(
        '{city}' => $a['tpl_city'],
        '{val_1}' => $a['tpl_val_1'],
        '{val_2}' => htmlspecialchars_decode($a['tpl_val_2']),
        '{productID}' => $productID,
        '{year}' => date('Y'),
    );

    $text = strtr($text, $arrWords);
    $seo_gen = mysql_result($q, 0, 1);
    //$price = mysql_result($t, 0);
    $subProductName = mysql_result($q, 0, 3);
    $subProductName = strtr($subProductName, $arrWords);
    //$a["PriceWithUnit"] = show_price( $price );

    $a['name'] = $subProductName;
    if (!empty($text) && $seo_gen == 1) {
        preg_match_all('/\[(.*)\]/U', $text, $pattern);
        $indexes = unserialize($a['text_index']);
        foreach ($indexes as $k => $v) {
            $str = $pattern[1][$k];
            $part = explode('|', $str);
            $to_replace['[' . $str . ']'] = $part[$v];
        }

        $to_show = strtr($text, $to_replace);
        $description = $to_show;
    } else {
        $description = $text;
    }
    if ($description != '') $a['description'] = $description;
}


include('core/config/w1.inc.php');

$smarty->assign("w1_success_url", $w1_success_url);
$smarty->assign("w1_fail_url", $w1_fail_url);
$smarty->assign("w1_currency_id", $w1_currency_id);

$price = $a['Price'];

/*if($price > 0)
$price = $price * $multi_koef;
else
$price = $a['Price'];
*/

$robo_display_price = show_price($price);
$sMerchantLogin = $w1_login;
$nOutSum = show_priceWithOutUnit($price);
$Desc = $a['name'];

$smarty->assign("robo_display_price", $robo_display_price);
$smarty->assign("sMerchantLogin", $sMerchantLogin);
$smarty->assign("nOutSum", $nOutSum);
$smarty->assign("Desc", $Desc);

//Robokassa
include('core/config/robokassa.inc.php');
include('core/classes/class.robokassa.php');
$a['BuyButton'] = Robokassa::GetPaymentButton($nOutSum, $a['name'], 1, 1);
//echo '<!-- '.var_dump($a['name']).' -->';
$productJson = array(
    'id'=>$productID,
    'name'=> $a['name'],
    'price'=> $nOutSum
);
$productJson = php2js($productJson);//json_encode_win1251
$smarty->assign("productJson", $productJson);

//Название подтовара
$smarty->assign("product_info", $a);
if (isset($_GET['buy'])) {
    $smarty->assign("buy_change", "1");
}

if ($subID > "0") {
    $smarty->assign("sub_product_name", $subProductName);
}

if (!isset($_GET["vote"])) IncrementProductViewedTimes($productID);

if (isset($_GET['cart'])) $smarty->assign("cart", 1);
?>