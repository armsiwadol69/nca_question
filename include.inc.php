<?
//===================================
include($DOCUMENT_ROOT."/ncainc/ncacfg.inc.php");
include($cfg_docncainc."ncainc.inc.php");
//===================================
if( !$gb_notlogin && $s_ar_login == "" )
{
	session_destroy();
	//header("location:".$cfg_loginpath);
    echo"<script>top.location='".$cfg_loginpath."';</script>";
}
//===================================
?>
