<?php
  #####################################
  # ShopCMS: Скрипт интернет-магазина
  # Copyright (c) by ADGroup
  # http://shopcms.ru
  #####################################


  // *****************************************************************************
  // Purpose        gets pictures by product
  // Inputs   $productID - product ID
  // Remarks
  // Returns        array of item
  //                                each item consits of
  //                                "photoID"                        - photo ID
  //                                "productID"                        - product ID
  //                                "filename"                        - conventional photo filename
  //                                "thumbnail"                        - thumbnail photo filename
  //                                "enlarged"                        - enlarged photo filename
  //                                "default_picture"        - 1 if default picture, otherwise 0
  function GetPictures($productID)
  {
      $q = db_query("select photoID, productID, filename, thumbnail, enlarged from ".PRODUCT_PICTURES.
          " where productID=".(int)$productID);
      $q2 = db_query("select default_picture from ".PRODUCTS_TABLE." where productID=".(int)$productID);
      $product = db_fetch_row($q2);
      $default_picture = $product[0];
      $res = array();
      while ($row = db_fetch_row($q))
      {
          if ((string )$row["photoID"] == (string )$default_picture) $row["default_picture"] = 1;
          else  $row["default_picture"] = 0;
          $res[] = $row;
      }
      return $res;
  }


  // *****************************************************************************
  // Purpose        deletes three pictures (filename, thumbnail, enlarged) for product
  // Inputs   $photoID - picture ID ( PRODUCT_PICTURES table )
  // Remarks        $photoID identifier is corresponded three pictures ( see PRODUCT_PICTURES
  //                                table in database_structure.xml )
  // Returns        nothing
  function DeleteThreePictures($photoID)
  {
      $q = db_query("select filename, thumbnail, enlarged, productID from ".PRODUCT_PICTURES." where photoID=".(int)$photoID);
      if ($picture = db_fetch_row($q))
      {
          if ($picture["filename"] != "" && $picture["filename"] != null)
              if (file_exists("data/small/".$picture["filename"])) unlink("data/small/".$picture["filename"]);

          if ($picture["thumbnail"] != "" && $picture["thumbnail"] != null)
              if (file_exists("data/medium/".$picture["thumbnail"])) unlink("data/medium/".$picture["thumbnail"]);

          if ($picture["enlarged"] != "" && $picture["enlarged"] != null)
              if (file_exists("data/big/".$picture["enlarged"])) unlink("data/big/".$picture["enlarged"]);

          $q1 = db_query("select default_picture from ".PRODUCTS_TABLE." where productID=".(int)$picture["productID"]);
          if ($product = db_fetch_row($q1))
          {
              if ($product["default_picture"] == $photoID) db_query("update ".PRODUCTS_TABLE." set default_picture=NULL ".
                      " where productID=".(int)$_GET["productID"]);
          }
          db_query("delete from ".PRODUCT_PICTURES." where photoID=".(int)$photoID);
      }
  }

  function DeleteThreePictures2($productID)
  {
      $q = db_query("select filename, thumbnail, enlarged from ".PRODUCT_PICTURES." where productID=".(int)$productID);
      while ($picture = db_fetch_row($q))
      {
          if ($picture["filename"] != "" && $picture["filename"] != null)
              if (file_exists("data/small/".$picture["filename"])) unlink("data/small/".$picture["filename"]);

          if ($picture["thumbnail"] != "" && $picture["thumbnail"] != null)
              if (file_exists("data/medium/".$picture["thumbnail"])) unlink("data/medium/".$picture["thumbnail"]);

          if ($picture["enlarged"] != "" && $picture["enlarged"] != null)
              if (file_exists("data/big/".$picture["enlarged"])) unlink("data/big/".$picture["enlarged"]);

          db_query("delete from ".PRODUCT_PICTURES." where productID=".(int)$productID);
      }
  }

  // *****************************************************************************
  // Purpose        deletes thumbnail picture for product
  // Inputs   $photoID - picture ID ( see PRODUCT_PICTURES table )
  // Remarks        $photoID identifier is corresponded three pictures ( see PRODUCT_PICTURES
  //                                table in database_structure.xml ), but this function delelete only thumbnail
  //                                        picture from server and set thumbnail column value to ''
  // Returns        nothing
  function DeleteThumbnailPicture($photoID)
  {
      $q = db_query("select thumbnail from ".PRODUCT_PICTURES." where photoID=".(int)$photoID);
      if ($thumbnail = db_fetch_row($q))
      {
          if ($thumbnail["thumbnail"] != "" && $thumbnail["thumbnail"] != null)
          {
              if (file_exists("data/medium/".$thumbnail["thumbnail"])) unlink("data/medium/".$thumbnail["thumbnail"]);
          }
          db_query("update ".PRODUCT_PICTURES." set thumbnail=''"." where photoID=".(int)$photoID);
      }
  }


  // *****************************************************************************
  // Purpose        deletes enlarged picture for product
  // Inputs   $photoID - picture ID ( see PRODUCT_PICTURES table )
  // Remarks        $photoID identifier is corresponded three pictures ( see PRODUCT_PICTURES
  //                                table in database_structure.xml ), but this function delelete only enlarged
  //                                        picture from server and set thumbnail column value to ''
  // Returns        nothing
  function DeleteEnlargedPicture($photoID)
  {
      $q = db_query("select enlarged from ".PRODUCT_PICTURES." where photoID=".(int)$photoID);
      if ($enlarged = db_fetch_row($q))
      {
          if ($enlarged["enlarged"] != "" && $enlarged["enlarged"] != null)
          {
              if (file_exists("data/big/".$enlarged["enlarged"])) unlink("data/big/".$enlarged["enlarged"]);
          }
          db_query("update ".PRODUCT_PICTURES." set enlarged=''"." where photoID=".(int)$photoID["enlarged"]);
      }
  }


  // *****************************************************************************
  // Purpose        updates filenames
  // Inputs   $fileNames array of        items
  //                                each item consits of
  //                                        "filename"                - normal picture
  //                                        "thumbnail"                - thumbnail picture
  //                                        "enlarged"                - enlarged picture
  //                                key is picture ID ( see PRODUCT_PICTURES  )
  // Remarks
  //                                if $default_picture == -1 then default picture is not set
  // Returns        nothing
  function UpdatePictures($productID, $fileNames, $default_picture)
  {
      foreach ($fileNames as $key => $value)
      {

          db_query("update ".PRODUCT_PICTURES." set filename='".xEscSQL($value["filename"])."', thumbnail='".xEscSQL($value["thumbnail"])."' ,  enlarged='".xEscSQL($value["enlarged"])."' "."where photoID=".(int)$key);
      }
      if ($default_picture != -1) db_query("update ".PRODUCTS_TABLE." set default_picture = ".xEscSQL($default_picture)." where productID=".(int)$productID);
  }


  function UpdatePicturesUpload($productID, $fileNames, $default_picture)
  {
      foreach ($fileNames as $key => $value)
      {

          $new_filename = Rendernames("ufilenameu_".$key,"data/small/");
          $new_thumbnail = Rendernames("uthumbnailu_".$key,"data/medium/");
          $new_enlarged = Rendernames("uenlargedu_".$key,"data/big/");

          if ($new_filename != "" && $new_filename != null)
          {
              if (CONF_PHOTO_RESIZE) Renderimage($new_filename, CONF_PHOTO_WIDTH1,"data/small/");
              if (CONF_PUT_WATERMARK) Renderwatermark($new_filename,"data/small/");
              $q = db_query("select filename from ".PRODUCT_PICTURES." where photoID=".(int)$key);
              if ($filenamed = db_fetch_row($q))
                  if ($filenamed[0] != "" && $filenamed[0] != null)
                  {
                      if (file_exists("data/small/".$filenamed[0])) unlink("data/small/".$filenamed[0]);
                  }
              db_query("update ".PRODUCT_PICTURES." set filename='".xEscSQL($new_filename)."' where photoID=".(int)$key);
          }
          if ($new_thumbnail != "" && $new_thumbnail != null)
          {
              if (CONF_PHOTO_RESIZE) Renderimage($new_thumbnail, CONF_PHOTO_WIDTH2,"data/medium/");
              if (CONF_PUT_WATERMARK) Renderwatermark($new_thumbnail,"data/medium/");
              $q = db_query("select thumbnail from ".PRODUCT_PICTURES." where photoID=".(int)$key);
              if ($thumbnaild = db_fetch_row($q))
                  if ($thumbnaild[0] != "" && $thumbnaild[0] != null)
                  {
                      if (file_exists("data/medium/".$thumbnaild[0])) unlink("data/medium/".$thumbnaild[0]);
                  }
              db_query("update ".PRODUCT_PICTURES." set thumbnail='".xEscSQL($new_thumbnail)."' where photoID=".(int)$key);
          }
          if ($new_enlarged != "" && $new_enlarged != null)
          {
              if (CONF_PHOTO_RESIZE) Renderimage($new_enlarged, CONF_PHOTO_WIDTH3,"data/big/");
              if (CONF_PUT_WATERMARK) Renderwatermark($new_enlarged,"data/big/");
              $q = db_query("select enlarged from ".PRODUCT_PICTURES." where photoID=".(int)$key);
              if ($enlargedd = db_fetch_row($q))
                  if ($enlargedd[0] != "" && $enlargedd[0] != null)
                  {
                      if (file_exists("data/big/".$enlargedd[0])) unlink("data/big/".$enlargedd[0]);
                  }
              db_query("update ".PRODUCT_PICTURES." set enlarged='".xEscSQL($new_enlarged)."' where photoID=".(int)$key);
          }
      }

      if ($default_picture != -1) db_query("update ".PRODUCTS_TABLE." set default_picture = ".xEscSQL($default_picture)." where productID=".(int)$productID);
  }


  // *****************************************************************************
  // Purpose        adds new picture
  // Inputs        $filename, $thumbnail, $enlarged - keys of item in $_FILES
  //                                corresponded to these file names
  //                        $productID - product ID
  //                        $default_picture - default picture ID
  // Remarks
  //                        if $new_filename == "" then function does not something
  //                        if $default_picture == -1 then default picture is set to new inserted
  //                                        item to PRODUCT_PICTURES
  // Returns        nothing


  function AddNewPictures($productID, $filename, $thumbnail, $enlarged, $default_picture)
  {
      if (isset($_FILES[$filename]) && $_FILES[$filename]["name"] && $_FILES[$filename]["size"] > 0)
      {

          $new_filename = Rendernames($filename,"data/small/");
          $new_thumbnail = Rendernames($thumbnail,"data/medium/");
          $new_enlarged = Rendernames($enlarged,"data/big/");

          if ($new_filename != "")
          {
              db_query("insert into ".PRODUCT_PICTURES."(productID, filename, thumbnail, enlarged)".
                  "  values( ".(int)$productID.", '".xEscSQL($new_filename)."', '".xEscSQL($new_thumbnail).
                  "', '".xEscSQL($new_enlarged)."' ) ");

              if (CONF_PHOTO_RESIZE)
              {

                  if ($new_filename != "") Renderimage($new_filename, CONF_PHOTO_WIDTH1,"data/small/");
                  if ($new_thumbnail != "") Renderimage($new_thumbnail, CONF_PHOTO_WIDTH2,"data/medium/");
                  if ($new_enlarged != "") Renderimage($new_enlarged, CONF_PHOTO_WIDTH3,"data/big/");
              }

              if (CONF_PUT_WATERMARK)
              {

                  if ($new_filename != "") Renderwatermark($new_filename,"data/small/");
                  if ($new_thumbnail != "") Renderwatermark($new_thumbnail,"data/medium/");
                  if ($new_enlarged != "") Renderwatermark($new_enlarged,"data/big/");
              }

              if ($default_picture == -1)
              {
                  $default_pictureID = db_insert_id();
                  db_query("update ".PRODUCTS_TABLE." set default_picture = ".$default_pictureID." where productID=".(int)$productID);
              }
          }
      }
  }

  function Renderimages()
  {

      set_time_limit(0);

      $q = db_query("select filename, thumbnail, enlarged FROM ".PRODUCT_PICTURES);

      while ($row = db_fetch_row($q))
      {
          if (strlen($row["filename"]) > 0 && file_exists("data/small/".$row["filename"])) Renderimage($row["filename"],CONF_PHOTO_WIDTH1,"data/small/");
          if (strlen($row["thumbnail"]) > 0 && file_exists("data/medium/".$row["thumbnail"])) Renderimage($row["thumbnail"],CONF_PHOTO_WIDTH2,"data/medium/");
          if (strlen($row["enlarged"]) > 0 && file_exists("data/big/".$row["enlarged"])) Renderimage($row["enlarged"],CONF_PHOTO_WIDTH3,"data/big/");
      }
  }

  function Renderwatermarks()
  {

      set_time_limit(0);

      $q = db_query("select filename, thumbnail, enlarged FROM ".PRODUCT_PICTURES);

      while ($row = db_fetch_row($q))
      {
          if (strlen($row["filename"]) > 0 && file_exists("data/small/".$row["filename"])) Renderwatermark($row["filename"],"data/small/");
          if (strlen($row["thumbnail"]) > 0 && file_exists("data/medium/".$row["thumbnail"])) Renderwatermark($row["thumbnail"],"data/medium/");
          if (strlen($row["enlarged"]) > 0 && file_exists("data/big/".$row["enlarged"])) Renderwatermark($row["enlarged"],"data/big/");
      }
  }

  // *****************************************************************************
  // Purpose        gets thumbnail file name
  // Inputs        $productID - product ID
  // Remarks
  // Returns        file name, it is not full path
  function GetThumbnail($productID)
  {
      $q = db_query("select default_picture from ".PRODUCTS_TABLE." where productID=".(int)$productID);
      if ($product = db_fetch_row($q))
      {
          $q2 = db_query("select filename from ".PRODUCT_PICTURES." where photoID=".(int)$product["default_picture"]." and productID=".(int)$productID);
          if ($picture = db_fetch_row($q2))
          {
              if (file_exists("data/small/".$picture["filename"]) && strlen($picture["filename"]) > 0)
                      return $picture["filename"];
          }
      }
      return "";
  }


  function GetPictureCount($productID)
  {
      $count_pict = db_query("select COUNT(*) from ".PRODUCT_PICTURES." where productID=".(int)$productID." AND filename!=''");
      $count_pict_row = db_fetch_row($count_pict);
      return $count_pict_row[0];
  }

  function GetThumbnailCount($productID)
  {
      $count_pict = db_query("select COUNT(*) from ".PRODUCT_PICTURES." where productID=".(int)$productID." AND thumbnail!=''");
      $count_pict_row = db_fetch_row($count_pict);
      return $count_pict_row[0];
  }

  function GetEnlargedPictureCount($productID)
  {
      $count_pict = db_query("select COUNT(*) from ".PRODUCT_PICTURES." where productID=".(int)$productID." AND enlarged!=''");
      $count_pict_row = db_fetch_row($count_pict);
      return $count_pict_row[0];
  }

  function Renderimage($tempname, $mode, $folder)
  {
      include_once ('core/asido/class.asido.php');
      asido::driver('gd');

      if ($mode > 0)
      {
          $i = asido::image($folder.$tempname, $folder.$tempname);
          asido::fit($i, $mode, $mode);
          $i->save(ASIDO_OVERWRITE_ENABLED);
      }
  }
  
  function Renderwatermark($tempname, $folder)
  {

      include_once ('core/asido/class.asido.php');
      asido::driver('gd');

      if (CONF_PUT_WATERMARK && file_exists("data/".CONF_WATERMARK_IMAGE))
      {
          $i = asido::image($folder.$tempname, $folder.$tempname);
          asido::watermark($i, "data/".CONF_WATERMARK_IMAGE, ASIDO_WATERMARK_BOTTOM_CENTER, ASIDO_WATERMARK_SCALABLE_ENABLED);
          $i->save(ASIDO_OVERWRITE_ENABLED);
      }
  }

  function Rendernames($tempname, $folder)
  {
      $new_tempname = "";
      if (isset($_FILES[$tempname]) && $_FILES[$tempname]["size"] > 0)
      {
          $picture_name = strtolower(str_replace(" ", "_", $_FILES[$tempname]["name"]));
          $pos = strrpos($picture_name, ".");
          $name = substr($picture_name, 0, $pos);
          $ext = substr($picture_name, $pos + 1);

          if (file_exists($folder.$picture_name))
          {
              $taskDone = false;
              for ($i = 1; (($i < 500) && ($taskDone == false)); $i++)
              {
                  if (!file_exists($folder.$name."_".$i.".".$ext))
                  {
                      if (is_uploaded_file($_FILES[$tempname]['tmp_name']))
                      {
                          if (move_uploaded_file($_FILES[$tempname]['tmp_name'], $folder.$name."_".
                              $i.".".$ext))
                          {
                              SetRightsToUploadedFile($folder.$name."_".$i.".".$ext);
                              $new_tempname = $name."_".$i.".".$ext;
                          }
                      }
                      $taskDone = true;
                  }
              }

          }
          else
          {
              if (is_uploaded_file($_FILES[$tempname]['tmp_name']))
              {
                  if (move_uploaded_file($_FILES[$tempname]['tmp_name'], $folder.$picture_name))
                  {
                      SetRightsToUploadedFile($folder.$picture_name);
                      $new_tempname = $picture_name;
                  }
              }
          }
      }
      return $new_tempname;
  }
?>