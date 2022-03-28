<?php
if (CONF_BACKEND_SAFEMODE && $_POST['fACTION']) //this action is forbidden when SAFE MODE is ON
{
        Redirect( $_POST['fREDIRECT']?set_query('&safemode=yes', $_POST['fREDIRECT']):set_query('&safemode=yes') );
}

$msg = '';


switch ($_POST['fACTION']){
        case 'NEW_LINK_CATEGORY':
                $_ncID = le_addCategory($_POST['LINK_CATEGORY']);
                if($_ncID){

                        $_POST['fREDIRECT'] = set_query('categoryID='.$_ncID, $_POST['fREDIRECT']);
                        $msg = 'ok';
                }
                else{ $error_message = STRING_ERROR_LE_LINK_CATEGORY_EXISTS;}
        break;
        case 'SAVE_LINK_CATEGORY':
                if(le_saveCategory($_POST['LINK_CATEGORY']))$msg = 'ok';
                else $error_message = STRING_ERROR_LE_LINK_CATEGORY_EXISTS;
        break;
        case 'DELETE_LINK_CATEGORY':

                if(!isset($_POST['LINK_CATEGORY']['le_cID'])) break;
                le_deleteCategory((int)$_POST['LINK_CATEGORY']['le_cID']);
                $_links = le_getLinks(
                        0,
                        le_getLinksNumber('le_lCategoryID = '.(int)$_POST['LINK_CATEGORY']['le_cID']),
                        (int)$_POST['LINK_CATEGORY']['le_cID'],
                        'le_lID',
                        'le_lVerified ASC, le_lURL ASC');
                foreach ($_links as $__link)
                        le_SaveLink(array(
                                'le_lID'                         => (int)$__link['le_lID'],
                                'le_lCategoryID'         => 0
                                ));
                $_POST['fREDIRECT'] = set_query('categoryID=', $_POST['fREDIRECT']);
                $msg = 'ok';
        break;
        case 'NEW_LINK':
                if(!strlen(str_replace('http://','',$_POST['LINK']['le_lURL']))){

                        $error_message = STRING_ERROR_LE_ENTER_LINK;
                        $show_new_link = true;
                        break;
                }
                $_POST['LINK']['le_lURL'] = xEscSQL($_POST['LINK']['le_lURL']);
                if(!strlen($_POST['LINK']['le_lText'])){

                        $error_message = STRING_ERROR_LE_ENTER_TEXT;
                        $show_new_link = true;
                        break;
                }
                if(strlen($_POST['LINK']['le_lDesk'])){

                         $_POST['LINK']['le_lDesk'] = xToText($_POST['LINK']['le_lDesk']);
                }
                $_POST['LINK']['le_lText'] = xToText($_POST['LINK']['le_lText']);
                if(strpos($_POST['LINK']['le_lURL'],'http://'))
                        $_POST['LINK']['le_lURL'] = 'http://'.$_POST['LINK']['le_lURL'];
                $_POST['LINK']['le_lVerified'] = date("Y-m-d H:i:s");
                if(!le_addLink($_POST['LINK'])){

                        $show_new_link = true;
                        $error_message = STRING_ERROR_LE_LINK_EXISTS;
                        break;
                }
                $msg = 'ok';
        break;
        case 'MOVE_LINKS':
                if(isset($_POST['LINKS_IDS']))
                foreach($_POST['LINKS_IDS'] as $_linkID){

                        le_SaveLink(array('le_lID'=>$_linkID,'le_lCategoryID'=>$_POST['new_le_lCategoryID']));
                }
                $msg = 'ok';
                break;
        case 'SAVE_LINKS':
                if(isset($_POST['LINKS_IDS']))
                foreach($_POST['LINKS_IDS'] as $_linkID){

                if(strlen(str_replace('http://','',$_POST['LINK'][$_linkID]['le_lURL']))){

                        $_POST['LINK'][$_linkID]['le_lURL'] = xEscSQL($_POST['LINK'][$_linkID]['le_lURL']);
                }

                if(strlen($_POST['LINK'][$_linkID]['le_lText'])){

                        $_POST['LINK'][$_linkID]['le_lText'] = xToText($_POST['LINK'][$_linkID]['le_lText']);
                }
                if(strlen($_POST['LINK'][$_linkID]['le_lDesk'])){

                        $_POST['LINK'][$_linkID]['le_lDesk'] = xToText($_POST['LINK'][$_linkID]['le_lDesk']);
                }


                        $_POST['LINK'][$_linkID]['le_lID'] = $_linkID;
                        if(!le_SaveLink($_POST['LINK'][$_linkID]))$error_message = STRING_ERROR_LE_LINK_EXISTS;
                }
                if(!isset($error_message))$msg = 'ok';
                break;
        case 'VERIFY_LINKS':
                if(isset($_POST['LINKS_IDS']))
                foreach($_POST['LINKS_IDS'] as $_linkID){
                        le_SaveLink(array('le_lID'=>$_linkID,'le_lVerified'=>date("Y-m-d H:i:s")));
                }
                $msg = 'ok';
                break;
        case 'UNVERIFY_LINKS':
                if(isset($_POST['LINKS_IDS']))
                foreach($_POST['LINKS_IDS'] as $_linkID){
                        le_SaveLink(array('le_lID'=>$_linkID,'le_lVerified'=>'NULL'));
                }
                $msg = 'ok';
                break;
        case 'DELETE_LINKS':
                if(isset($_POST['LINKS_IDS']))
                foreach($_POST['LINKS_IDS'] as $_le_lID)
                        le_DeleteLink($_le_lID);
                $msg = 'ok';
                break;
}
if($_POST['fREDIRECT']&&$msg=='ok')Redirect(set_query('action='.$msg, $_POST['fREDIRECT']));

?>
