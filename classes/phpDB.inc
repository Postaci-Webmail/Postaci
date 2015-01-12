<?
/*
   File name         : phpDB.inc
   Version           : 1.1.0
   Author            : Joe Thong
   Purpose           : phpDB General Module. A PHP database wrapper for various database servers.  Database results
                       are flushed automatically by phpDB.  Supported database servers are MySQL, MSQL, PostgreSQL,
                       Microsoft SQL Server and Sybase.
   Last modified     : 03 Sep 2000

   Copyright (c) Joe Thong Chean Fonk.
   All rights reserved.
*/

  if (!defined("_PHPDB_GENERAL_LAYER")) {
    define("_PHPDB_GENERAL_LAYER", 1 );
  }

  $databaseType = $database_type;
  $phpDBRootPath = '.';

  function useDB($dbType = "") {
    global $phpDBRootPath,$postaci_directory;
    switch (strtolower($dbType)) {
      case "mysql":
      case "msql":
      case "postgresql":
      case "mssql":
      case "sybase":
        include("$postaci_directory" . "classes". "/phpDB-" . "$dbType" . ".inc");
        break;
      case "":
        die("$text72");
        return false;
      default:
        die("$text73");
        return false;
      }
      return true;
   }

   useDB($databaseType);

?>
