<?php
  
set_include_path("../../af/Common/RelationTemplateLink");
require_once('DbHelper.php');

class UnusedImagesCleaner
{
    /*private static $SourceDirectory = '/home/sahagps/www/pictures';
    private static $DestDirectory = '/home/sahagps/www/pictures/fordel';
    private static $AllFileTable = 'tmpToolUnusedImagesAllFiles';
    private static $UsedFileTable = 'tmpToolUnusedImagesUsedFiles';
    private static $UnusedFileTable = 'tmpToolUnusedImagesUnUsedFiles';
    private static $TmpDirectory = '/home/sahagps/www/cache';
    private static $UnusedListFilename = 'UnusedList.txt';
    private static $SubDirs = array('blok2','cat','lmenu','main_pic','osn');*/
    
    private static $SourceDirectory = '/home/mapsshop/domains/mapsshop.ru/public_html/pictures';
    private static $DestDirectory = '/home/mapsshop/domains/mapsshop.ru/public_html/pictures/fordel';
    private static $TmpDirectory = '/home/mapsshop/domains/mapsshop.ru/public_html/cache';

    private static $AllFileTable = 'tmpToolUnusedImagesAllFiles';
    private static $UsedFileTable = 'tmpToolUnusedImagesUsedFiles';
    private static $UnusedFileTable = 'tmpToolUnusedImagesUnUsedFiles';
    private static $UnusedListFilename = 'UnusedList.txt';
    private static $SubDirs = array('blok2','cat','lmenu','main_pic','osn');

    //Step1
    static function CreateTemporaryTables()
    {
        $AllFileTable = self::$AllFileTable;
        $UsedFileTable = self::$UsedFileTable;
        $UnusedFileTable = self::$UnusedFileTable;
        
       $CreateAllFilesTableQuery = 
       //Имена всех файлов из pictures + размер
"CREATE TABLE `$AllFileTable`(  
  `filename` VARCHAR(255) NOT NULL,
  `size` INT(11),
  PRIMARY KEY (`filename`)
) CHARSET=cp1251 COLLATE=cp1251_general_ci";

       $CreateUsedFilesTableQuery = 
//имена используемых файлов
"CREATE TABLE `$UsedFileTable`(  
  `filename` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`filename`)
)";

       $CreateUnusedFilesTableQuery = 
//неиспользуемые файлы
"CREATE TABLE `$UnusedFileTable`(  
  `filename` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`filename`)
)";
       $res = mysql_query($CreateAllFilesTableQuery);
       if (!$res) { throw new Exception(mysql_error());}

        $res = mysql_query($CreateUsedFilesTableQuery);
       if (!$res) { throw new Exception(mysql_error());}
        $res = mysql_query($CreateUnusedFilesTableQuery);
       if (!$res) { throw new Exception(mysql_error());}
    }
    
    //Step2
    static function GetSourceDirectoryFilenames()
    {
        //1. Создаем текстовый файл с именами и размерами файлов
        //2. Импотртируем в таблицу
        $SourceDirectory = self::$SourceDirectory;
        $TmpDirectory = self::$TmpDirectory;
        $FileList = $TmpDirectory.'/'.'FileList.txt';
        self::CreateDirectoryFileList($SourceDirectory,$FileList);
        self::LoadFileListToDb($FileList);
        //$DbQuery = 
    }
    
    //Step3
    static function GetUsedImagesFilenames()
    {
        $UsedFileTable = self::$UsedFileTable;
        
        $DbQuery = "INSERT INTO $UsedFileTable (filename) 
SELECT filename FROM avl_product_pictures
UNION
SELECT thumbnail FROM avl_product_pictures
UNION
SELECT enlarged FROM avl_product_pictures";
        $res = mysql_query($DbQuery);
        if (!$res) { throw new Exception(mysql_error());}    
    }
    
    //Step4
    static function GetUnusedImagesFilenames()
    {
         $UnusedFileTable = self::$UnusedFileTable;
         $UsedFileTable = self::$UsedFileTable;
         $AllFileTable = self::$AllFileTable;
         $TmpDirectory = self::$TmpDirectory;
         $OutFilename = $TmpDirectory.'/'.self::$UnusedListFilename;
        $DbQuery = "INSERT INTO $UnusedFileTable (filename)
SELECT filename FROM $AllFileTable WHERE filename NOT IN (SELECT filename FROM $UsedFileTable)";
        
        /*$DbQuery = "SELECT filename FROM tmpToolUnusedImagesAllFiles 
        WHERE filename NOT IN (SELECT filename FROM tmpToolUnusedImagesUsedFiles) INTO OUTFILE '$OutFilename'";*/
        $res = mysql_query($DbQuery);
        if (!$res) { throw new Exception(mysql_error());}        
    }
    
    //Step5
    static function MooveUnusedImages()
    {
        $SourceDirectory = self::$SourceDirectory;
        $DestDirectory = self::$DestDirectory;
        $SubDirs = self::$SubDirs;
        $UnusedListFilename = self::$UnusedListFilename;
        $TmpDirectory = self::$TmpDirectory;
        
        $ListFilename = $TmpDirectory.'/'.$UnusedListFilename;
        $FileListHandle = fopen($ListFilename,'rb');
        if(!$FileListHandle) throw new Exception('Cant Create Filelist at '.$ListFilename);
        while(($CurrentSrt=fgets($FileListHandle))!==false)
        {
            //$pices = explode("\t",$CurrentSrt);
            //$CurrentFilename = $pices[0];
            $CurrentFilename = trim($CurrentSrt);
            $SourceFile = $SourceDirectory.'/'.$CurrentFilename;
            $DestFile = $DestDirectory.'/'.$CurrentFilename;
            rename($SourceFile,$DestFile);
            
            foreach($SubDirs as $SubDir)
            {
                $SourceFile = $SourceDirectory.'/'.$SubDir.'/'.$CurrentFilename;
                if(file_exists($SourceFile)){
                    $DestFile = $DestDirectory.'/'.$SubDir.'/'.$CurrentFilename;
                    rename($SourceFile,$DestFile);    
                }
            }
        }
    }
    
    private static function MoveFile($SourceFile,$DestFile)
    {
        $res = copy($SourceFile,$DestFile);
        if(!$res) throw new Exception('Cant copy file from '.$SourceFile.' to '.$DestFile);
        $res = unlink($SourceFile);
        if(!$res) throw new Exception('Cant delete file '.$SourceFile);
    }
    
    //Step6
    static function DeleteTemporaryTables()
    {
        $AllFileTable = self::$AllFileTable;
        $UsedFileTable = self::$UsedFileTable;
        $UnusedFileTable = self::$UnusedFileTable;
        $DbQuery = "DROP TABLE IF EXISTS $AllFileTable, $UsedFileTable, $UnusedFileTable";
        $res = mysql_query($DbQuery);
        if (!$res) { throw new Exception(mysql_error());}
    }
    
    private static function CreateDirectoryFileList($SourceDirectory,$ListFilename)
    {
        $FileListHandle = fopen($ListFilename,'wb');
        if(!$FileListHandle) throw new Exception('Cant Create Filelist at '.$ListFilename);
        
        $DirectoryHandle = opendir($SourceDirectory);
        if(!$DirectoryHandle) throw new Exception('Cant open directory at '.$SourceDirectory);
        
        readdir($DirectoryHandle);//.
        readdir($DirectoryHandle);//..
        while (false !== ($CurrentFilename = readdir($DirectoryHandle))) {
            $CurrentFilenameWithPath = $SourceDirectory.'/'.$CurrentFilename; 
            if(is_file($CurrentFilenameWithPath))
            {
                $CurrentFileSize = filesize($CurrentFilenameWithPath);
                $CurrentString = $CurrentFilename."\t".$CurrentFileSize."\n";
                $res=fwrite($FileListHandle,$CurrentString);
                if(FALSE===$res) throw new Exception("Cant write string ('$CurrentString') to ListFilename ($ListFilename)");
            }
        }
    }
    
    private static function LoadFileListToDb($SourceFilename)
    {
        $AllFileTable = self::$AllFileTable;
        $DbQuery = "LOAD DATA INFILE '$SourceFilename' INTO TABLE `$AllFileTable`";
        $res = mysql_query($DbQuery);
        if (!$res) { throw new Exception(mysql_error());}
    }
}

function _format_bytes($a_bytes)
 {
     if ($a_bytes < 1024) {
         return $a_bytes .' B';
     } elseif ($a_bytes < 1048576) {
         return round($a_bytes / 1024, 2) .' KiB';
     } elseif ($a_bytes < 1073741824) {
         return round($a_bytes / 1048576, 2) . ' MiB';
     } elseif ($a_bytes < 1099511627776) {
         return round($a_bytes / 1073741824, 2) . ' GiB';
     } elseif ($a_bytes < 1125899906842624) {
         return round($a_bytes / 1099511627776, 2) .' TiB';
     } elseif ($a_bytes < 1152921504606846976) {
         return round($a_bytes / 1125899906842624, 2) .' PiB';
     } elseif ($a_bytes < 1180591620717411303424) {
         return round($a_bytes / 1152921504606846976, 2) .' EiB';
     } elseif ($a_bytes < 1208925819614629174706176) {
         return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
     } else {
         return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
     }
 }
  
?>
