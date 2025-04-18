<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.2.2-dev
*/namespace
Adminer;const
VERSION="5.2.2-dev";error_reporting(24575);set_error_handler(function($Cc,$Ec){return!!preg_match('~^Undefined (array key|offset|index)~',$Ec);},E_WARNING|E_NOTICE);$Zc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Zc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$hj=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($hj)$$X=$hj;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Fb=adminer()->credentials();$J=Driver::connect($Fb[0],$Fb[1],$Fb[2]);return(is_object($J)?$J:null);}function
idf_unescape($u){if(!preg_match('~^[`\'"[]~',$u))return$u;$Be=substr($u,-1);return
str_replace($Be.$Be,$Be,substr($u,1,-1));}function
q($Q){return
connection()->quote($Q);}function
escape_string($X){return
substr(q($X),1,-1);}function
idx($va,$x,$k=null){return($va&&array_key_exists($x,$va)?$va[$x]:$k);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$Pg,$Zc=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($x,$X)=each($Pg)){foreach($X
as$ue=>$W){unset($Pg[$x][$ue]);if(is_array($W)){$Pg[$x][stripslashes($ue)]=$W;$Pg[]=&$Pg[$x][stripslashes($ue)];}else$Pg[$x][stripslashes($ue)]=($Zc?$W:stripslashes($W));}}}}function
bracket_escape($u,$Ca=false){static$Qi=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($u,($Ca?array_flip($Qi):$Qi));}function
min_version($yj,$Pe="",$g=null){$g=connection($g);$Jh=$g->server_info;if($Pe&&preg_match('~([\d.]+)-MariaDB~',$Jh,$A)){$Jh=$A[1];$yj=$Pe;}return$yj&&version_compare($Jh,$yj)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_bool($ee){$X=ini_get($ee);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($xj,$N,$V,$F){$_SESSION["pwds"][$xj][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$m=0,$tb=null){$tb=connection($tb);$I=$tb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$m]:false);}function
get_vals($H,$d=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$d];}return$J;}function
get_key_vals($H,$g=null,$Mh=true){$g=connection($g);$J=array();$I=$g->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($Mh)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$g=null,$l="<p class='error'>"){$tb=connection($g);$J=array();$I=$tb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$g&&$l&&(defined('Adminer\PAGE_HEADER')||$l=="-- "))echo$l.error()."\n";return$J;}function
unique_array($K,array$w){foreach($w
as$v){if(preg_match("~PRIMARY|UNIQUE~",$v["type"])){$J=array();foreach($v["columns"]as$x){if(!isset($K[$x]))continue
2;$J[$x]=$K[$x];}return$J;}}}function
escape_key($x){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$x,$A))return$A[1].idf_escape(idf_unescape($A[2])).$A[3];return
idf_escape($x);}function
where(array$Z,array$n=array()){$J=array();foreach((array)$Z["where"]as$x=>$X){$x=bracket_escape($x,true);$d=escape_key($x);$m=idx($n,$x,array());$Xc=$m["type"];$J[]=$d.(JUSH=="sql"&&$Xc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Xc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($m,q($X)))));if(JUSH=="sql"&&preg_match('~char|text~',$Xc)&&preg_match("~[^ -@]~",$X))$J[]="$d = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$x)$J[]=escape_key($x)." IS NULL";return
implode(" AND ",$J);}function
where_check($X,array$n=array()){parse_str($X,$Wa);remove_slashes(array(&$Wa));return
where($Wa,$n);}function
where_link($s,$d,$Y,$Nf="="){return"&where%5B$s%5D%5Bcol%5D=".urlencode($d)."&where%5B$s%5D%5Bop%5D=".urlencode(($Y!==null?$Nf:"IS NULL"))."&where%5B$s%5D%5Bval%5D=".urlencode($Y);}function
convert_fields(array$e,array$n,array$M=array()){$J="";foreach($e
as$x=>$X){if($M&&!in_array(idf_escape($x),$M))continue;$wa=convert_field($n[$x]);if($wa)$J
.=", $wa AS ".idf_escape($x);}return$J;}function
cookie($B,$Y,$Ie=2592000){header("Set-Cookie: $B=".urlencode($Y).($Ie?"; expires=".gmdate("D, d M Y H:i:s",time()+$Ie)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($Bb){parse_str($_COOKIE[$Bb],$Nh);return$Nh;}function
get_setting($x,$Bb="adminer_settings"){$Nh=get_settings($Bb);return$Nh[$x];}function
save_settings(array$Nh,$Bb="adminer_settings"){$Y=http_build_query($Nh+get_settings($Bb));cookie($Bb,$Y);$_COOKIE[$Bb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($hd=false){$pj=ini_bool("session.use_cookies");if(!$pj||$hd){session_write_close();if($pj&&@ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($x){return$_SESSION[$x][DRIVER][SERVER][$_GET["username"]];}function
set_session($x,$X){$_SESSION[$x][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($xj,$N,$V,$j=null){$lj=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($j!==null?"db|":"").($xj=='mssql'||$xj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$lj,$A);return"$A[1]?".(sid()?SID."&":"").($xj!="server"||$N!=""?urlencode($xj)."=".urlencode($N)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($j!=""?"&db=".urlencode($j):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($Le,$cf=null){if($cf!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($Le!==null?$Le:$_SERVER["REQUEST_URI"]))][]=$cf;}if($Le!==null){if($Le=="")$Le=".";header("Location: $Le");exit;}}function
query_redirect($H,$Le,$cf,$Yg=true,$Jc=true,$Sc=false,$Di=""){if($Jc){$ci=microtime(true);$Sc=!connection()->query($H);$Di=format_time($ci);}$Wh=($H?adminer()->messageQuery($H,$Di,$Sc):"");if($Sc){adminer()->error
.=error().$Wh.script("messagesPrint();")."<br>";return
false;}if($Yg)redirect($Le,$cf.$Wh);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";";return
connection()->query($H);}function
apply_queries($H,array$T,$Fc='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$Fc($R)))return
false;}return
true;}function
queries_redirect($Le,$cf,$Yg){$Tg=implode("\n",Queries::$queries);$Di=format_time(Queries::$start);return
query_redirect($Tg,$Le,$cf,$Yg,false,!$Yg,$Di);}function
format_time($ci){return
sprintf('%.3f s',max(0,microtime(true)-$ci));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($kg=""){return
substr(preg_replace("~(?<=[?&])($kg".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($x,$Rb=false,$Wb=""){$Yc=$_FILES[$x];if(!$Yc)return
null;foreach($Yc
as$x=>$X)$Yc[$x]=(array)$X;$J='';foreach($Yc["error"]as$x=>$l){if($l)return$l;$B=$Yc["name"][$x];$Li=$Yc["tmp_name"][$x];$yb=file_get_contents($Rb&&preg_match('~\.gz$~',$B)?"compress.zlib://$Li":$Li);if($Rb){$ci=substr($yb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$ci))$yb=iconv("utf-16","utf-8",$yb);elseif($ci=="\xEF\xBB\xBF")$yb=substr($yb,3);}$J
.=$yb;if($Wb)$J
.=(preg_match("($Wb\\s*\$)",$yb)?"":$Wb)."\n\n";}return$J;}function
upload_error($l){$Xe=($l==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($l?'Unable to upload a file.'.($Xe?" ".sprintf('Maximum allowed file size is %sB.',$Xe):""):'File does not exist.');}function
repeat_pattern($wg,$y){return
str_repeat("$wg{0,65535}",$y/65535)."$wg{0,".($y%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",','),preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Tc=false){$J=table_status($R,$Tc);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$p){foreach($p["source"]as$X)$J[$X][]=$p;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$x=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$x];$_POST["fields"][$X]=$_POST["field_vals"][$x];}}foreach((array)$_POST["fields"]as$x=>$X){$B=bracket_escape($x,true);$J[$B]=array("field"=>$B,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($x==driver()->primary),);}return$J;}function
dump_headers($Pd,$mf=false){$J=adminer()->dumpHeaders($Pd,$mf);$gg=$_POST["output"];if($gg!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Pd).".$J".($gg!="file"&&preg_match('~^[0-9a-z]+$~',$gg)?".$gg":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){foreach($K
as$x=>$X){if(preg_match('~["\n,;\t]|^0|\.\d*0$~',$X)||$X==="")$K[$x]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$K)."\r\n";}function
apply_sql_function($r,$d){return($r?($r=="unixepoch"?"DATETIME($d, '$r')":($r=="count distinct"?"COUNT(DISTINCT ":strtoupper("$r("))."$d)"):$d);}function
get_temp_dir(){$J=ini_get("upload_tmp_dir");if(!$J){if(function_exists('sys_get_temp_dir'))$J=sys_get_temp_dir();else{$o=@tempnam("","");if(!$o)return'';$J=dirname($o);unlink($o);}}return$J;}function
file_open_lock($o){if(is_link($o))return;$q=@fopen($o,"c+");if(!$q)return;chmod($o,0660);if(!flock($q,LOCK_EX)){fclose($q);return;}return$q;}function
file_write_unlock($q,$Lb){rewind($q);fwrite($q,$Lb);ftruncate($q,strlen($Lb));file_unlock($q);}function
file_unlock($q){flock($q,LOCK_UN);fclose($q);}function
first(array$va){return
reset($va);}function
password_file($h){$o=get_temp_dir()."/adminer.key";if(!$h&&!file_exists($o))return'';$q=file_open_lock($o);if(!$q)return'';$J=stream_get_contents($q);if(!$J){$J=rand_string();file_write_unlock($q,$J);}else
file_unlock($q);return$J;}function
rand_string(){return
md5(uniqid(strval(mt_rand()),true));}function
select_value($X,$_,array$m,$Ci){if(is_array($X)){$J="";foreach($X
as$ue=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($ue):"")."<td>".select_value($W,$_,$m,$Ci);return"<table>$J</table>";}if(!$_)$_=adminer()->selectLink($X,$m);if($_===null){if(is_mail($X))$_="mailto:$X";if(is_url($X))$_=$X;}$J=adminer()->editVal($X,$m);if($J!==null){if(!is_utf8($J))$J="\0";elseif($Ci!=""&&is_shortable($m))$J=shorten_utf8($J,max(0,+$Ci));else$J=h($J);}return
adminer()->selectVal($J,$_,$m,$X);}function
is_mail($tc){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$fc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$wg="$xa+(\\.$xa+)*@($fc?\\.)+$fc";return
is_string($tc)&&preg_match("(^$wg(,\\s*$wg)*\$)i",$tc);}function
is_url($Q){$fc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($fc?\\.)+$fc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$m){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~',$m["type"]);}function
count_rows($R,array$Z,$oe,array$vd){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($oe&&(JUSH=="sql"||count($vd)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$vd).")$H":"SELECT COUNT(*)".($oe?" FROM (SELECT 1$H GROUP BY ".implode(", ",$vd).") x":$H));}function
slow_query($H){$j=adminer()->database();$Ei=adminer()->queryTimeout();$Rh=driver()->slowQuery($H,$Ei);$g=null;if(!$Rh&&support("kill")){$g=connect();if($g&&($j==""||$g->select_db($j))){$xe=get_val(connection_id(),0,$g);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$xe&token=".get_token()."'); }, 1000 * $Ei);");}}ob_flush();flush();$J=@get_key_vals(($Rh?:$H),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$Wg=rand(1,1e6);return($Wg^$_SESSION["token"]).":$Wg";}function
verify_token(){list($Mi,$Wg)=explode(":",$_POST["token"]);return($Wg^$_SESSION["token"])==$Mi;}function
lzw_decompress($Ia){$bc=256;$Ja=8;$gb=array();$jh=0;$kh=0;for($s=0;$s<strlen($Ia);$s++){$jh=($jh<<8)+ord($Ia[$s]);$kh+=8;if($kh>=$Ja){$kh-=$Ja;$gb[]=$jh>>$kh;$jh&=(1<<$kh)-1;$bc++;if($bc>>$Ja)$Ja++;}}$ac=range("\0","\xFF");$J="";$Gj="";foreach($gb
as$s=>$fb){$sc=$ac[$fb];if(!isset($sc))$sc=$Gj.$Gj[0];$J
.=$sc;if($s)$ac[]=$Gj.$sc[0];$Gj=$sc;}return$J;}function
script($Th,$Pi="\n"){return"<script".nonce().">$Th</script>$Pi";}function
script_src($mj,$Tb=false){return"<script src='".h($mj)."'".nonce().($Tb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace("\0","&#0;",htmlspecialchars($Q,ENT_QUOTES,'utf-8'));}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($B,$Y,$Za,$ze="",$Mf="",$db="",$Ae=""){$J="<input type='checkbox' name='$B' value='".h($Y)."'".($Za?" checked":"").($Ae?" aria-labelledby='$Ae'":"").">".($Mf?script("qsl('input').onclick = function () { $Mf };",""):"");return($ze!=""||$db?"<label".($db?" class='$db'":"").">$J".h($ze)."</label>":$J);}function
optionlist($Rf,$Bh=null,$qj=false){$J="";foreach($Rf
as$ue=>$W){$Sf=array($ue=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($ue).'">';$Sf=$W;}foreach($Sf
as$x=>$X)$J
.='<option'.($qj||is_string($x)?' value="'.h($x).'"':'').($Bh!==null&&($qj||is_string($x)?(string)$x:$X)===$Bh?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($B,array$Rf,$Y="",$Lf="",$Ae=""){static$ze=0;$_e="";if(!$Ae&&substr($Rf[""],0,1)=="("){$ze++;$Ae="label-$ze";$_e="<option value='' id='$Ae'>".h($Rf[""]);unset($Rf[""]);}return"<select name='".h($B)."'".($Ae?" aria-labelledby='$Ae'":"").">".$_e.optionlist($Rf,$Y)."</select>".($Lf?script("qsl('select').onchange = function () { $Lf };",""):"");}function
html_radios($B,array$Rf,$Y="",$Fh=""){$J="";foreach($Rf
as$x=>$X)$J
.="<label><input type='radio' name='".h($B)."' value='".h($x)."'".($x==$Y?" checked":"").">".h($X)."</label>$Fh";return$J;}function
confirm($cf="",$Ch="qsl('input')"){return
script("$Ch.onclick = () => confirm('".($cf?js_escape($cf):'Are you sure?')."');","");}function
print_fieldset($t,$Ge,$Aj=false){echo"<fieldset><legend>","<a href='#fieldset-$t'>$Ge</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$t');",""),"</legend>","<div id='fieldset-$t'".($Aj?"":" class='hidden'").">\n";}function
bold($La,$db=""){return($La?" class='active $db'":($db?" class='$db'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($D,$Ib){return" ".($D==$Ib?$D+1:'<a href="'.h(remove_from_uri("page").($D?"&page=$D".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($D+1)."</a>");}function
hidden_fields(array$Pg,array$Sd=array(),$Hg=''){$J=false;foreach($Pg
as$x=>$X){if(!in_array($x,$Sd)){if(is_array($X))hidden_fields($X,array(),$x);else{$J=true;echo
input_hidden(($Hg?$Hg."[$x]":$x),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
enum_input($U,$ya,array$m,$Y,$wc=null){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$Se);$J=($wc!==null?"<label><input type='$U'$ya value='$wc'".((is_array($Y)?in_array($wc,$Y):$Y===$wc)?" checked":"")."><i>".'empty'."</i></label>":"");foreach($Se[1]as$s=>$X){$X=stripcslashes(str_replace("''","'",$X));$Za=(is_array($Y)?in_array($X,$Y):$Y===$X);$J
.=" <label><input type='$U'$ya value='".h($X)."'".($Za?' checked':'').'>'.h(adminer()->editVal($X,$m)).'</label>';}return$J;}function
input(array$m,$Y,$r,$Ba=false){$B=h(bracket_escape($m["field"]));echo"<td class='function'>";if(is_array($Y)&&!$r){$Y=json_encode($Y,128|64|256);$r="json";}$ih=(JUSH=="mssql"&&$m["auto_increment"]);if($ih&&!$_POST["save"])$r=null;$qd=(isset($_GET["select"])||$ih?array("orig"=>'original'):array())+adminer()->editFunctions($m);$cc=stripos($m["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$ya=" name='fields[$B]'$cc".($Ba?" autofocus":"");$Bc=driver()->enumLength($m);if($Bc){$m["type"]="enum";$m["length"]=$Bc;}echo
driver()->unconvertFunction($m)." ";$R=$_GET["edit"]?:$_GET["select"];if($m["type"]=="enum")echo
h($qd[""])."<td>".adminer()->editInput($R,$m,$ya,$Y);else{$Cd=(in_array($r,$qd)||isset($qd[$r]));echo(count($qd)>1?"<select name='function[$B]'$cc>".optionlist($qd,$r===null||$Cd?$r:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($qd))).'<td>';$ge=adminer()->editInput($R,$m,$ya,$Y);if($ge!="")echo$ge;elseif(preg_match('~bool~',$m["type"]))echo"<input type='hidden'$ya value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$ya value='1'>";elseif($m["type"]=="set"){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$Se);foreach($Se[1]as$s=>$X){$X=stripcslashes(str_replace("''","'",$X));$Za=in_array($X,explode(",",$Y),true);echo" <label><input type='checkbox' name='fields[$B][$s]' value='".h($X)."'".($Za?' checked':'').">".h(adminer()->editVal($X,$m)).'</label>';}}elseif(preg_match('~blob|bytea|raw|file~',$m["type"])&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($r=="json"||preg_match('~^jsonb?$~',$m["type"]))echo"<textarea$ya cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($Ai=preg_match('~text|lob|memo~i',$m["type"]))||preg_match("~\n~",$Y)){if($Ai&&JUSH!="sqlite")$ya
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$ya
.=" cols='30' rows='$L'";}echo"<textarea$ya>".h($Y).'</textarea>';}else{$bj=driver()->types();$Ze=(!preg_match('~int~',$m["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$m["length"],$A)?((preg_match("~binary~",$m["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$m["unsigned"]?1:0)):($bj[$m["type"]]?$bj[$m["type"]]+($m["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$m["type"]))$Ze+=7;echo"<input".((!$Cd||$r==="")&&preg_match('~(?<!o)int(?!er)~',$m["type"])&&!preg_match('~\[\]~',$m["full_type"])?" type='number'":"")." value='".h($Y)."'".($Ze?" data-maxlength='$Ze'":"").(preg_match('~char|binary~',$m["type"])&&$Ze>20?" size='".($Ze>99?60:40)."'":"")."$ya>";}echo
adminer()->editHint($R,$m,$Y);$ad=0;foreach($qd
as$x=>$X){if($x===""||!$X)break;$ad++;}if($ad&&count($qd)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $ad);");}}function
process_input(array$m){if(stripos($m["default"],"GENERATED ALWAYS AS ")===0)return;$u=bracket_escape($m["field"]);$r=idx($_POST["function"],$u);$Y=$_POST["fields"][$u];if($m["type"]=="enum"||driver()->enumLength($m)){if($Y==-1)return
false;if($Y=="")return"NULL";}if($m["auto_increment"]&&$Y=="")return
null;if($r=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?idf_escape($m["field"]):false);if($r=="NULL")return"NULL";if($m["type"]=="set")$Y=implode(",",(array)$Y);if($r=="json"){$r="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}if(preg_match('~blob|bytea|raw|file~',$m["type"])&&ini_bool("file_uploads")){$Yc=get_file("fields-$u");if(!is_string($Yc))return
false;return
driver()->quoteBinary($Yc);}return
adminer()->processInput($m,$Y,$r);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Eh="<ul>\n";foreach(table_status('',true)as$R=>$S){$B=adminer()->tableName($S);if(isset($S["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$Lg="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$B</a>";echo"$Eh<li>".($I?$Lg:"<p class='error'>$Lg: ".error())."\n";$Eh="";}}}echo($Eh?"<p class='message'>".'No tables.':"</ul>")."\n";}function
on_help($mb,$Ph=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $mb, $Ph) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$n,$K,$kj,$l=''){$oi=adminer()->tableName(table_status1($R,true));page_header(($kj?'Edit':'Insert'),$l,array("select"=>array($R,$oi)),$oi);adminer()->editRowPrint($R,$n,$K,$kj);if($K===false){echo"<p class='error'>".'No rows.'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$n)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$Ba=!$_POST;foreach($n
as$B=>$m){echo"<tr><th>".adminer()->fieldName($m);$k=idx($_GET["set"],bracket_escape($B));if($k===null){$k=$m["default"];if($m["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$k,$fh))$k=$fh[1];if(JUSH=="sql"&&preg_match('~binary~',$m["type"]))$k=bin2hex($k);}$Y=($K!==null?($K[$B]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$m["type"])&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$kj&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$k)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$m);$r=($_POST["save"]?idx($_POST["function"],$B,""):($kj&&preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$kj&&$Y==$m["default"]&&preg_match('~^[\w.]+\(~',$Y))$r="SQL";if(preg_match("~time~",$m["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$r="now";}if($m["type"]=="uuid"&&$Y=="uuid()"){$Y="";$r="uuid";}if($Ba!==false)$Ba=($m["auto_increment"]||$r=="now"||$r=="uuid"?null:true);input($m,$Y,$r,$Ba);if($Ba)$Ba=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($n){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($kj?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n",($kj?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'Saving'."…', this); };"):"");}echo($kj?"<input type='submit' name='delete' value='".'Delete'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$y=80,$ii=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$Q,$A);return
h($A[1]).$ii.(isset($A[2])?"":"<i>…</i>");}function
icon($Od,$B,$Nd,$Gi){return"<button type='submit' name='$B' title='".h($Gi)."' class='icon icon-$Od'><span>$Nd</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M��h��g�б���\"P�i��m��cC���d<��f�a��:;NB�q�R;1Lf�9��u7%�d\\;3��A��`%�E�!���e9&���r4�M��A��v2�\r&:�Φs���0��*3�Má���-;�L�C@��i:dt3-8a�I\$ã��e���	��#9lT!Ѻ��>�e��\0�d��d�C��:6\\�c�A��rh��M4�k����Z|�O+��f�9��X�7h\"�Si����|�+9����ƣ���-4W�~�T:�zkH�b{���&�єt��:ܸ.K�v8#\",7!pp2��\0\\�� �\$�r7���ތ#��i\"�a�T�(L�2�#:\0Τ�x���XFǉ�d�&�jv����ڗ��@d�E�����!,9.+�`J�ahbDP<��|\"���Cp�>�ˑ+b2	L���{�F���Q�|����r��Kl�ɏ�_�t=���b�K|�������\r=�R�>����0��(���k�b�JU,PUumI.t�A-K��X�4�z�)MP��k���3e`�N>D#�9\\��(YT��@�h�L��1]ȴ��ͺNKն2\\73i/V��l��Y�җBA�/[J��ĘВ\r;'�2^텪�b�ۣ3�T=0H�8\r�+6��kf�C�]q��W)���²C��2`A���82�!��hm�вGD����2-C���Yc`�<�s��6�2���9�u���y��ҝMgy�=,CZO~^3���0�2�<��k0���wM�{d#`Zۀ����޺�����6�C%���=Rq���_+�-�K>��\n'G���A�\$����^�j><�gf�h�m�b*/\$\$l��ش�g)Aj�� w�#ᣃ�������TN��]�T���%Z���jJ����Cf4���zF�'�*� x�����ho&k\r��,��r�:>s(�LAs����5Ct���n�6��� ll\\9D��\\!��mv�\0�A{9`�.��סS�lg6���!.2��0�P�Ԡ�i\r\$7�w���;G��\$�0�CI�^�J\n�L�Pc��'�*Eh���b��;�p�B��(�x�:D�L�.j�9AC@�I3jf�5sI`X}��Ҕ#���7��T`d���jhH49S\nq��HJQ �H`,F��P�0\\{��m\r�~@20u!	\$�PoQ�4ǚ�\nZL�M��B��k)@�d��SL�pv���y�ЃB��^o���*�R�\"����#��rͥS;\r4�&G�I��T	�r���9=6���Q�T\0�\0��f#��=\$�������H6�P�Y�:�G\$�����0�9:a�3Hz;G�\r!hJ�n�7��	�oY������WLv�ې�i|���%�-��d\$�p�D�R2T\r�paU��n�5r��j�\$�r%D���)\$GڃBu��:��`�(l���SD)I	����9�*ꁗ\rt�2� �zI���g�[X�c,u�\rvJ5?����\"�:�^�.u�J�P�o\$t\$�18��\nnK��T%EZ,6��DH�V�󆪹i�&z��xpdr�x*�}ʐR�25+��ѓ�f�2�w��q�0X1�2dX�ߢ�̏W��ËV8f\"냐q(u��E��G�qM�#а�#K�3WA�v�Y���Þe�K]t��]E��j�=SX���@��Ӈ\r�Ә\$��9��ܬ0�P7\"D.匎<;��Nj�=�����^�mmڒG�68 �C�%v'�y��k/�^�5���3�@�.ڛ��k�a�*�D������:�7��C}��`�`�`)�7���|	�3� i�騽��4�\0.:�Q�L���؜ͨ��f�'�%�ݩM	���Y3�\0�##tP6�(�B�d����o�y�6�|�5��IH7�����z?�(��Ŗ\$�RWT�谦�:��(��`r϶i���s�=D\\��,kr�1��ٓ2�`��A�9��&n��~��Ҭ�6;�vp �M#�]����ɴ���A���iJ��.���t����Ys�OPwĸ�m��Z�����AU�ʷJ�N?����z�3\$P�qs�U9,�����#��5Pnb��uNѐ{N`�饙��۝�i��w\rb�&E�\\tg��b�a�1�+m�pw#�vl��U�����\0�e.��(�wb@����\\�w�(�)��E���;�Z�]/&���|>Q�\"c	<F�\r�7����ϵ\\�'�S�fe�\rR���Vlo/.��\n���F�o� �eh��e��jנ�T�sa4�2�2� �`o�\\�A?��]�IoB[�{7'��Q%�6�s7\$Ó�~%��u)�5i���0�#����\r�f���MX�N\\ɇ\0���,T���ETo\0�{o��R��r�^����C @Z.C,��c�'�J-�BL�\r�P�CD\"�b�^.\"�����h��\0�����\r\0���\n`�	 � ��n�o	����\r��\r��0�`���0��	��\rp� �	0�\n �F@�`� V\0�\n��\r\0�\n�j��\n@�\0�\r��\n��	 �\n@�@�\r\0�& �\n@� �@� ��z��Ƃ*��w�q0g�5�aPxG�����	�	��\n�\n�����\rp���\rp�\r��	��0�\r���`�\r@�@�� ���^\r �\0�\r�d@���3 ��1Q9�AB��<�t�1N?�S���v-�a��p��	p�P�\n����0������\0�@������Ѱ\r�^��\"i@�\n�� �6 �\0�	 p\n��\n��`� �qޒQD�B�M�d9�TUq�1���2�	�\n2�rR#��2+\r�/��#��@��\" �Q\r����\r����@�\n�h\n��〪���\0�`�	��@�!�;�Co�Uҋ2�����e Qk �p ���!P�3с!��r%���p�	 �,��`���\n�`\n@��ff ���`� �\n�@�	��F#�`p��# ���o���\\%Bl��?��M-jP��r��3/�3*Qlp�	p�\r`�=��\n\0_>�1�'�#\0�>\0�\r�������� �\n@��� f�0�'�@Ā�����\0�\rd�Fh�I\$�`���,����C�ȕPݔT�>�7\0]E̣ʉDG���AC�\\BMDԈ�fmd��(\r�OG�F��iDN��ɜn2�4tΔwFt��F����H�CԈu+���\$K�6蔓E��.AKԏL*1JK>�����M��H��\"GN�Pj�E�>� �H&5H��LM#E�P�c��c8��l����,���C�N�Pt�@V t�\n���ݴ�I	kG�H	�)D(�JPl�1jn�lԍ��J�~�*&�n\\���HUfLk�K��r�F���<|HNx\\� Nl�N���I��\0rzMt�U|Z��ĸ����Ԙ�\r�HC�΀B\"�@�b�cn�A����J9Ort�A4�\r��@h��A^`�^�V0�^!L�jU����.�^\r\"���ka���lp��d� ���}\0��N�����IGP���U�YtyMPr��Y��E�ԥx����6`�`jg���1SB�܂�����X�8�V?Id[I��Q.�����`��i��̲vԞ��U)Ԝ���\n2PV�~�� �����\"\"&��r]-� �p*�\0f\"�Kj`�q\nJ�\"q��F�.��\"@r��(�`��3q>\"��f��\r\$ح�� �R1��h&H�`Z�V	u+Moʬ�\n3J�\r Đ�2I� �D'�!S0W0J?y�pjZ.�\n\r ��pw�\"�-+�zr!`�|v2\nl�f(�m�<��=�F\r�Q}��~7��\r��#�o�3��}���x<�~��W���iE�ã��[�8\n�bjj�\r��: ���)v��'{��V�q\no{���)C����߂�i���\r�%�遀���C�(��k�k������4ؐd�� ������jXLN�(A�}xe���|��w���G��xh��X�x�\r��%K���޼oq�x�������8s�4e���xρ��n�*4F�c�8~�h�Ip]��{���%�( �Ӓ��<�V����C�B��{w����O��ƣ��}�Q�8�[���[�{�cT%�&��o�����:�*b�E�`�m�I�YW�k�8��Yo������u�)����Y5o�9��ަ���ى��<�8(�?�\0[s�@*8���}��ߟ9g�\rӟ������\0���\n'w±x)݌٩�����92�Z1��@[I�+��_��5�7=��D�q�z!}�K��Nd��3�\0��q�+��C����Y_�g�8��y����ډ�K�4�{��S�8�2Z��z��X\0Ϩz���S���ڱ��e� �\r�>�:������Ǭ�_�ZÏ��e�������:�����u���{�U�M���a�����B��zɈ���b2YS�WJ(wOwӁwm��تZN�l��˧C��9����8BD��6���Zy�x{���;!�[m�����{}��)��#�4�[���(�b���ɘ���Ն�u��������,O�\"F�7�y?�9���nd�}�����{ݍs�{���e�ʦ>\"�cc����d���cs�{��vdCN�[���GM�C瓭�DE@");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M��h��g���h0�LЁ�d91�S!��	�F�!��v}0��f�a��	G2�Na�'3I��d�K%�	��m(\r&�X��o;NB���y>2S�*�^#��Q���1=���J��W^��L�����o����c!��f��6�m�a���l4&1Lf�9��u7VDc3�n82IΆ��,:5���r�P��1��m�>�5��W/��Fc���Dh2�L�\rN����Wo&�hk��e2ٌ��b12Ƽ~0� ��D}N�\0�f4�M�C�����n=��p��Zش�N�~�;���-C ��%�z�99�P���\"����;�\0f��8�9�p�:�m�8��@\nX�:��r�3#����C�[�Cx�#��I2\\�\"��p����]#��5R�r6�#�L7���!H�\$�\$IRd�'ˉ8]	��x��+�>�C�@-��;���b�<�2Ô�N4,�����-Mr�6Ic�X4�a���5KE�Th@1@���R�K�9\r�(�9�#8�G�CpwID5�2�l\"_'��UB��U�9c@�G=C\n��S��0���j��7PU�����9J]�<׋\nƲσz�?B��2�����4\r/�P\r�M[X��F�_��j���H�bnC&�¡f%@cC^.2�8�׎C�}^��sw�L��/�5O�M��ڳ	*X�?�b�.I�g��&�aq�݊>���FN�-�`�y��4�s���j\\&:�Sa�P;����H�����X��ޯ���d�kt?.���,ZO��@@8�Z3�c�\"��ß\n�=A�H1\\�Z�^/k���΃L�uC\\�c�)0O��M��lpr��7�\r��q�����WRa�������c@���wm�k/�8�*?�̐�4�5�\\m���k��>�d1n��UQ#�����w�憟�Lo&hĪPrnR,5����z�\"\$3��dYH(p\r�ALAC�)pT�Pl�!\"L��8��R�&�\0�����Z౒0P8������J	�`��e�0�	����1��	�D��Js�H���)�k� ��[���C�y�pjx,\rA���m!���<h1�");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':�̢���i1��1��	4������Q6a&��:OAI��e:NF�D|�!���Cy��m2��\"���r<�̱���/C�#����:DbqSe�J�˦Cܺ\n\n��ǱS\rZ��H\$RAܞS+XKvtd�g:��6��EvXŞ�j��mҩej�2�M�����B��&ʮ�L�C�3���Q0�L��-x�\n��D���yNa�Pn:�����s��͐�(�cL��/���(�5{���Qy4��g-�����i4ڃf��(��bU���k��o7�&�ä�*ACb����`.����\r����������\n��Ch�<\r)`�إ`�7�Cʒ���Z���X�<�Q�1X���@�0dp9EQ�f����F�\r��!���(h��)��\np'#Č��H�(i*�r��&<#��7K��~�# ��A:N6�����l�,�\r��JP�3�!@�2>Cr���h�N��]�(a0M3�2��6��U��E2'!<��#3R�<�����X���CH�7�#n�+��a\$!��2��P�0�.�wd�r:Y����E��!]�<��j��@�\\�pl�_\r�Z���ғ�TͩZ�s�3\"�~9���j��P�)Q�YbݕD�Yc��`��z�c��Ѩ��'�#t�BOh�*2��<ŒO�fg-Z����#��8a�^��+r2b��\\��~0�������W����n��p!#�`��Z��6�1�2��@�ky��9\r��B3�pޅ�6��<�!p�G�9�n�o�6s��#F�3���bA��6�9���Z�#��6��%?�s��\"��|؂�)�b�Jc\r����N�s��ih8����ݟ�:�;��H�ތ�u�I5�@�1��A�PaH^\$H�v��@ÛL~���b9�'�����S?P�-���0�C�\nR�m�4���ȓ:���Ը�2��4��h(k\njI��6\"�EY�#��W�r�\r��G8�@t���Xԓ��BS\nc0�k�C I\rʰ<u`A!�)��2��C�\0=��� ���P�1�ӢK!�!��p�Is�,6�d���i1+����k���<��^�	�\n��20�Fԉ_\$�)f\0��C8E^��/3W!א)�u�*���&\$�2�Y\n�]��Ek�DV�\$�J���xTse!�RY� R��`=L���ޫ\nl_.!�V!�\r\nH�k��\$א`{1	|�����i<jRrPTG|��w�4b�\r���4d�,�E��6���<�h[N�q@Oi�>'ѩ\r����;�]#��}�0�ASI�Jd�A/Q����⸵�@t\r�UG��_G�<��<y-I�z򄤝�\"�P��B\0������q`��vA��a̡J�R�ʮ)��JB.�T��L��y����Cpp�\0(7�cYY�a��M��1�em4�c��r��S)o����p�C!I���Sb�0m��(d�EH����߳�X���/���P���y�X��85��\$+�֖���gd�����y��ϝ�J��� �lE��ur�,dCX�}e������m�]��2�̽�(-z����Z��;I��\\�) ,�\n�>�)����\rVS\njx*w`ⴷSFi��d��,���Z�JFM}Њ ��\\Z�P��`�z�Z�E]�d��ɟO�cmԁ]� ������%�\"w4��\n\$��zV�SQD�:�6���G�wM��S0B�-s��)�Z�cǁ2��δA;��n�Wz/A�Zh�G~�c�c%�[�D�&l�FR�77|�I���3��g0�L���a��c�0RJ�2��%���F� S� �L�^� tr���t����ʩ;��.喚Ł�>����[�a�N���^�(!g�@1����N�z�<b�ݖ�����O,��Cu��D�tj޹I;)�݀�\nn�c��Ȃ�W<s�	�\0�hN�P�9��{ue��ut뵕������3��=��g�����J����WQ�0���w9p-���	�������'5��\nO��e)M�)_k�z\0V�����;j�l��\n����x�Pf�-�`C�.@&]#\0ڶp�y͖ƛ�t�d�� ��b}�	G1�m�ru���*�_�xD�3�q��B�sQ��u��s%�\n�5s�ut���{�s�y���N��4�,J{4@��\0��P���^��=��l���`�e~F١h3o�\"��q�R<iUT�[Q��U��M�6�T. ��0'�pe\\�����5����pCe	ٕ�\"*�M	����D���?�h��2���zU�@7�C�4�a��iE!f�\$�B��<�9o*\$��lH�\$ �@����P\rN�Y�n<\$�	�Q�=�F&��*@]\0��� W'd� z\$��j�P[��\$���0#&��_�`+�B)�w�v%	����LcJ��RS��i`�Ů	�F�W	��\nBP\n�\r\0}	瑩0�Z���/`j\$�: �8ie���φx�����a ���Gn�sgO��U%VU��@�N��ϐ�d+�(oJ�@X���zM'F٣�WhV�I^٢�1>�@�\"���� ��Q�R!�\\�`[������.�0fb�F;���Fp�p/t`����(��V���b�Ȳ�(��H�l����ԯ1v�����H��1T�3�q���1�Ѫf�\nT\$���Nq+��`ލv�ǜ�\r�Vm���r���'ϸ��g%�\"L�m����(�(CLz��\"h�X�m=�\\H\n0U�� f&M\$�g\$�U`a\rP�>`�#g��h��`�R4H��'�����GK;\"M�ۨT�h�BE�n\"b>���\r���#�\0�N:�#_	QQ1{	f:B���R�&���)J��Br�+�K.\$�Pq�-r�S%TIT&Q���{#2o(*P��5�`�1H���'	<T�d����s��,N�� ����^\r%�3��\r&��4�B�/\0�kLH\$�4d�>���/�ඵ�H���*���3J�А�<�Hh��p�'��O/&�2I.�x3V.�s5�e3�ێZ�(�9E�g�;R�;�J��Q�@��vgz@������'dZ&�,U���F��b*�D��H! �\r�;%�x'G#��͠w��#�֠�2;#�Bv�X��a�\nb�{4K�G��%���GuE`\\\rB\r\0�-mW\rM\"��#E�cFbF�nz���@4J��[\$��%2V��%�&T�V��d�4hemN�-;Eľ%E�E�r�<\"@�F�P�L �߭�4E����z`�u�7�N�4��\0�F:h�K�h/:�\"�M�Z��\r+P4\r?��S��O;B��0\$FCEp��M\"�%H4D�|��LN�FtE��g���5�=J\r\"��޼5��4�K�P\rbZ�\r\"pEQ'DwK�W0��g'�l\"h�QF�C,�Cc���IH�P�hF]5�& f�T��iSTUS�����[4�[u�Ne�\$o�K��O ��b\" 5�\0�D�)E�%\"�]��/���ЌJ�6U�d��`��a)V-0��DӔbM�)���������`��%�ELt��+��6C7j�d��:�V4ơ3� -�R\rG�IT��#�<4-CgCP{V�\$'����g��R@�'��S=%���F�k:��k��9����e]aO��G9�;��-6��8W��*�x\"U��YlB���������	��\n��p���l����Z�m\0�5����Oq̨��b�W1s@��K�-p���E�Spw\nGWoQ�qG}vp�w}q��q�\\�7�RZ�@��t��t�;pG}w׀/%\"L�E\0t�h�)�\r��J�\\W@�	�|D#S��ƃV��R�z�2���v�����	�}�����(�\0y<�X\r��x���q�<��Isk1S�-Q4Yq8�#��v���d.ֹS;q�!,'(���<.�J7H�\"��.����u�����#�Q�\re�r�Xv[�h\$�{-�Y���JBg��iM8��'�\nƘtDZ~/�b���8��\$��DbR�O�O��`O5S>����[�D�ꔸ����_3X�)��'��Jd\r�X����UD�U�X8�x�-旅�P�N`�	�\n�Z���@Ra48��:���\0�x���N�\\�0%��f��\\��>\"@^\0Zx�Z�\0ZaBr#�X��\r��{��˕�flFb\0[�ވ\0[�6���	��� �=��\n��WB��\$'�kG�(\$y�e9�(8�& h��Rܔ��o�ȼ Ǉ���Y��4��7_��d��9�'���������z\r���  ����v�G��O8���MOh'��X�S0�\0\0�	��9�s?���I�MY�8� 9����HO��,4	��xs��P�*G����c8��Qɠ��wB|�z	@�	���9c�K��QG�bFj�X��oS�\$��dFHĂP�@ѧ<嶴�,�}�m��r��\"�'k�`��c�x��e�C��C��:���:X� �T���^�d�Æqh��s���Lv�Ү0\r,4�\r_v�L�j�jM��b[  ��ls���Z�@�����;f��`2Yc�e�'�Mer��F\$�!��\n��	*0\r�AN�LP��jٓ����;ƣV�Q|(��3����[p��8���|�^\r�Bf/�D���Ҟ B��_�N5M�� \$�\naZЦ���~�Ule�rŧr��Z�aZ�����գs8R�G�Z��w���N�_Ʊ�Yϣ�m����]��;ƚL�����c������Ű��I�Q3��O��|�y*`� �5��4�;&v8�#�R�8+`X�bV�6�ƫi�3F��E���oc82�M�\"����G�Wb\rO�C�Vd�ӭ�w\\�ͯ*cSi�Qү��R`�d7}	���)�ϴ�,�+bd�۹�FN�3��L\\��eRn\$&\\r��+d��]O5kq,&\"D�CU6j�p���\\'�@o�~�5N=�|�&�!��B�w�H�yyz7��(Ǎ���b5(3փ_\0`z�b�Уr��8	�Z�v�8L˓�)��S�M<�*7\$��\rR�b���B%��ƴDs�z�R>[�Q����&Q������'\r�pp�z�/<��}L�#��Ε���Z��\"t��\n��.4�g�P��p�D�n�ʹN��F�d\0`^����\rnȂ׳#_�� w(�2�<7-��X޹\0��s��,^�hC,�!:�\rK��.��Ӣ�Ţ���\\��+v�Z��\0�Q9eʛ˞E�w?>�\$}��D#���c�0MV3�%Y���\r��tj5��7��{ŝ�Lz=�<��8I�M�����G����L�\$��2��{(�pe?u�,R�d*X�4�����\0\"@���}<.@��	��N��\$�XU�js�/��<>\"* �#\$����&CPI	��t������?� ��	�O��\\��_��Q5Y�H@���b��c�h����뱖��O0T�'�8�w�����j+H�v_#�����06�w֎�X��d+�ܓ\\��\n\0	\\�>s��A	PF�d8m'@�\nH�\0�c�OwS�����Y�`�����R��Dna\"��~�?�m���|@6��+�GxV��\0��W�Ӱ�nw���.�؃b��9Í��E�|E���\rЈr�\"��x���-���\rN6�n�\$Ҭ�-B�H�^�)��y&��ךW�ǧ�bv�R�	���N\0��n�	T��`8X��A\r:{O�@\" �!��\$K�qo��jY֪J�����h}d<1I�xd����TT4NeeC0䥿�:D�F�5L�*::H�jZ��F�R�MրnS\n>PO�[�\$V8;#�K\\'�B��R�د��R�_�8�j��*Ej�\\~v���v��p@T�X�\0002dE	�H�V���D�\"Q'EDJB~A��A�Il*'\n�Y��.�+�9��pg���/�\"�1�8�0�IA�FCȨ�V*a��P�d�У5H\"�A��6�s�Y��;訞�/��0��v}y�\r����ץ1�u\"ˋ�m��_�0焄`���\\B1^\nk\r]lh�}]HBW`��0�꨹rFf�)�W,�ҧ]sm9'O�xԽ�,�9J8��?�4�����\"҅�۽�<�-S����M�;�v��6y|�Z����%�a�#8��TC�!�p��\n��CZ(�w��a������?9|��0<BL\r�\n�]�PB0�&�+t�H���օ�Dx^��,�L�}[��B�x}��ru��\0��\0005��S@\"Uؔ@��\0�\$��ސ\"Ҡ��]l/	��I�B4��.�6���d7��\r@=���߬���*G j����f`��:Hn��bĀ71��)C<@A�Y#�����e�o��Y!��I�DM�\nlt����/)�\\43)��2��ɸ�)���f[ ppp1���#��Ð�p\0��œl��^{��A��TH�6�����\n\0P�H�.\r���|�T�FD0��S�y����'1���K���d�����B���C�&�)�W�s Hee+@4� r���ۚ*Lp1<�f�N�Y'�-	XKVa��L���\"���\"�l��q��.YJH�m HV�/�lC�&��H)o�&\\2���%���z\n^Q(6�D� ����Jq���\00a#�6\0vr,�M��&A�������9%Yd��B�h��!W\0�b\r{���@�1��I�22�A��)�H�a@r�0G��7Dd.�LM�<��2���,k/��Me����}Ғ3�=\0�&��B��\nPd.\"��F3X��Sd(*�J6 ���F:��)1�1�?lQ&����h<J͋�f�d�Eպ*�x\n\0��.\"B -�#��Ηt�IΫ���	I8 ��8dh	��x���~��	L!K(�BX��-��h��c/�r��P�I���N�2�|��׶��|\"�M�'��K,\\H��e5*o]4��FP	2��<)�T���o��\n���I�ڢ�!�(���_8Xr�;u�����NJ�����[r��DC:�@�ͳ�l�\0�e\\*x@Aȡ&�(�5��,����#1x� �!T�D���(Q���DJ|D D:\0�A�й� �baE�?rn��Wkx��X=i��,\$3�[�r�9B�Ʊ�d��\0��H��4���<(z���?�sIbJ�g U�\n(}���J\"��A��B�19�~�I�#�\$��%d  e\"�`���t���'O=���@\$��O�\nmT�o+�Z����-�����PF?�_�I�J�X ģ2���-V�;�?2���0�*P3����_T<E�J�\\(�2����)�IQ���鬩���R��L&��!ȯK�iц�t����K�HRl�ȬEs�������D��xǴ�i���!faB���F��e>�V����-Qj�I��7���\"%Rh� g��M������-�b�58R����*��9��ꊰ���9�2Q0���IR[�Z��N\0���20�����\\[@�Q\0��Jx�����EC{���\$lp1=\0�Rо�>E~�������:0���%��R+)\0�	ƑQ�@(\"�_j�T�X\0����\r1�\0P�9#\0����H;B�|���L�Z�����6�/B��\nB�{���|H�,�	*;��(�`�2@6�>�	�?P\0/���\0|\\�eB�`��jq�U/\rc�����҆�6(N\0�/\$�\n8�j*U�\$��y*�=�;���\$�f��8X�BCE��r\"/�����kځ%\\9k���B���0�F��(��'�U���Ʈm�@k�T\0��E��sEhy�e\n�)�)��b7��(W%,�J�r��2D�rhE��\n0Q�3� U�9TPO������8j|�}�R<0���Zl ��T�������*�\$��U\r�\"�.� Ts~�~(�3�a���@��+���l�`:�`�:O�i��BX�?ʄ��7��Lj|�:n�K:ز}�\0��UMc`P%nn\n,�4�Q'%+H.�\"#G��3`�����\n1fg\0�М'�k��qxD<\"��,a|{~���C<S�i�B�\nkN���G�}���k:��������g�)�JD���hÛf�\"�kV~��mM`HO�kD��^�0/tj�l�\r�!�f<��G��T���v�#@�ek@2�w���0�ܭt���į1�u�yvː%8�?1���l��xt��mp��fK3Z�J�=\0@�^p��ۑ����]Ҳ'�t١@C�b��\r[��V��-���o�-��ݠe�}��Y��	-�-m�I\0+��V�D�[B+��(�-�4�>�q��i>=��/0-�cL�pJ b\nd��)�#��G�s����\"�Q�N����`.�ȍ�yȐEtP�q�I]��J8���rWT��I���f�aG�.떄7y��l��A��7'�1�	�S�-�xI��m���L:e�ΉA�W��ζEI��Wz��3W���)*/)C���x*c]�%�}����_��IvͲ�'�\$U��S4k�5WʏJC���7*�b%<WC@�	����c{޴���3)X�&&��eL�I���,N� 2k#p5���f4���Ǻ�z�#��\\����N�b�U��oy���S�4�`q�~1�=�8厉�*�OOJ�C�����'Dd,@kL�������\\�j2ͩ����<�@_q�2�\0�ձ�)`�������s���F\0�����\n���F��<*�x*����`����-��\r���|@����7�H@w����H]��\0�����_w��h0!�s�1Ϗ��Ǭ�hW��.��=W��R*�A_���EDԷ�?1,Ub�9=t�4è��W��^���;����@��(1<D�ÊHx�T()0z�`�_�;��AL��)\n�K[f�H���Wo�@bBK�iM���d+�>�vI�(z:��.݀��9uiѤDY����O`���]I\0��R�Ć,K,���6L��\"\"�1g�(���|T.,�9vb+\rk]u�&�|��b�S��d[�,g��aJ�(C��k��\rF�+	��9��L��))UA�B�U�h�g��c3x�-n9�����x��2��q�ib�rY7�k�y�f�,������)�٪�J:�N�8�Rcly\n��2�W�;�.>�v6Q#A0��{έi��7~@VX���^��11-�+�v|��]Vf���.�{	���\r��;�1lp�/��uF��d�\$PЮ0=@kS�0h��Ɉ@��/*(O�V.��G>�(r��!�6�����Y=XZ@�:�'&0�6kE|���'|H;���N�g�%�W�+��4�;̓��'x|�f�9���(O��d���w%9]��f}��G���s���¾�����XM0����gQ���8̄�+O}�͝0}�9�������Nh�/mgD���s������\n�74勳P~}O)�Ug�9���j�8P��ݸ�(�%����j�7oAB��i)��K��u�� �}s�1�=od�V[Ĵ\n��zl�Mзr:F#{��*#�x��ܰ�<Ds��k/mw :^����1��ύD��2�z*��n��%�����i�Ù *�!8-��tH�'����\r�к�4����8`��\"�����i]�ZZ�>Z\0ަ9����+䟂~��\$ޭ��L�P\\쇁�XA�������i���z�h�\$�SM�T'���1���D��	��5E�\0Ğ\$�ttԮ��:\rMƷS��Ӗ�ls��Af�K�k,N�l�D^zz�dS��/rt�N�>��o%i��\0J�B�po��R����/֘٫x\ny�+��,e4��q5Q'JD�]�B@m����R�Ski~����t0�[ 1�z	���&��^�\nO����V����GV@T*�H9�ωG0\0'�`�Ѱ\r���bQKsLd�*;\n����.ĔUNp�,L�@TR�e��b��F���y�n> IK��rG�	@��?cI�ݓu%G�O�1���C�h�5T�y��I��:\\0��X��>�ʊ�0�޾�QB���EI/-LBT�!b��6���k`jp\0K���>k�d���/���ISk.+*���R�|gR���W\\w���t�.)�^Zc8�Z�~F��Sǵ�S�m̕;b>\0jz=�T'�>��q�y}:�u��&��W�DQ��c-����6<[��e�x�ؠ���[���L�\0wm�l�t�z��<S�&��db�x��oi�gK�\r`�µ�?D5u@b���N��O�𤷤���Y�[�����{�Nr鉞�t���\0��tMs�cBW?�*D�.p���'2��Ge\rp*#�e�����C���\"�QI\n��hi�Q�@���\rl	����_.���t*�^��s�9���Whq���~,��Yθ��dQs¦\r�Bj��D�ǡ��<<T)C�\n�����&�D{\r�l���-R��\r@rk��Ϣ��+Z���P������u8Ȩ����s�و���o�#��g��u\$F�&\n-v\"P����j�nnt�1��V������Awbx߄�D�5��-�0�a�\0\r�/!�I����|/����h��n�Gf-Mdna�^(e�a��¨�Y��Z,�S�E�N��\\�����=�4~Mʹ�\r����Ft�Ŧ�u\"|`��E��R�z��D�`�{��@�k/K�Y����3sJ�䃿5XGͪ�%�9)Q�� �Q���1t�h��!TR���H���Q�\r�C��E�0�#w�G2��/���/��=^ �/Ժ�ΐ����E��\0{+���t�+��q�б��I�t�|����v��q��Ԉƌ&�\r\\�Vߠ=���Eb��nO�rn��X({�ɹuzK��`=:�\n����\0����[�%�:p���q+��R�ldY��\"��[V�u{H-��H�_��8j��V��5����\"\0\"N?E;+�O~�wN�];L�'���SOF����䁻��D�-�!#sN�<��� ¯��mu����G�8���Tn]�����:�zIMn� O�8���z5���o\\5�7�<��Ų#8���?sN�L��	}�x��&4�?�[�z���󳷶����<*W������e}{HZ���,(<o�o�xW�t�2���#�A*�����o\\�R�}xH>NP�|Qɚ|x�'�-� ��2\0��?ƾ2*\r|]t��p�\"�ڲJuuXyb�D\n�Z|�H7�_�W���GuXyH>T\r�G����Ql�������n!�u'�*�C5��>U�2!b	��9Pw��4�����}y�W�|���a\$�g������T�U��&~9(\\*�!b_����w�7\\����]=�\\*���@�#N7ͪ��5QN`@<\0�6!�9��l��\$�wI\$4���2��\$�&���.RZ����Y��uyᤳ�p�&SI��@�EJiL�c���V�1F�1��Z\r\r���h��k���HH��˿�����K���?x��-0\n��d�N3K��C�59)ľ:B#���dN5A1�Ɖ����Od[3ڠ��h�[s~)�9�DN�y����>���X��'Ƚ�ϐH���,��)ڂ�\"�e�0;\0�qeo>��=�|�2�G+B�@z�������@]}��rQ��� k/�|�G�:ѯ�W\0�a4>��^|���g�o�XE�9p���Lrg�A��6��p�e����1�*����7��[�>]�#�?jB�~�/�}�3�:��U\$�?�<��G��a���\n>0#!i�>.{A}'hQ�Lw�~�W_��Th#d��û��d��FQ�����*{��\"�\"�P{���}�4�N���i��\r_����e?l4�2�?\n�F��	��q�U��Ľ�_��`_�����j��{_k_�o�~��c*#�(�/�!Dn�F�`��?@s�B�!�?;�E��������\0k�	�*N��D;���+d\nZZdB��� ��`B5�P\n8�������c#ou��k�ˊM�ݯw�.��F�J���!|�Ĉ2Fc�Y).����XHy�[��~����#/�&����[�����Y@���(|\r\0,O��0Yb��βŬ�\$0���aˑ����� �A\$��0,�@�Ӱ>>9��\\t�i�<�\0�q\0�}@`�\0fVj����dߠ'(����	!_�n��0+c���iig8a]'=-�B!(��8�_���x�j�����)\rH5H�Yn	,f�r��}-d\$��H��2n鴆ܛ�=�-�d���FE-d��a��N_z4@��[�n��\$x!!i0T����u�8�ɸ����\0PZ8Z����c����+Њ�AAF(����`mg*�vS, ǆ��KcA�۬ &��9������c�0w�+�n��=��)\$���Q�~A��a�\0004\0u�{�(��\$���y	!��B�� A<�a��Az ���ZA4\$ZY9.aX\r��d�A�L�v|oOz|�Z�(�e�Z�Ć�");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0�<d4�E'�\n#�\r���.�C!�^t�(��bqH��.���s���2�N�q٤�9��#{�c�����3nӸ2��r�:<�+�9�CȨ���\n<�\r`��/b�\\���!�H�2SڙF#8Ј�I�78�K��*ں�!���鎑��+��:+���&�2|�:��9���:��N���pA/#�� �0D�\\�'�1����2�a@��+J�.�c,�����1��@^.B��ь�`OK=�`B��P�6����>(�eK%! ^!Ϭ�B��HS�s8^9�3�O1��.Xj+���M	#+�F�:�7�S�\$0�V(�FQ�\r!I��*�X�/̊���67=�۪X3݆؇���^��gf#W��g��8ߋ�h�7��E�k\r�ŹG�)��t�We4�V؝����&7�\0R��N!0�1W���y�CP��!��i|�gn��.\r�0�9�Aݸ���۶�^�8v�l\"�b�|�yHY�2�9�0�߅�.��:y���6�:�ؿ�n�\0Q�7��bk�<\0��湸�-�B�{��;�����W����&�/n�w��2A׵�����A�0yu)���kLƹtk�\0�;�d�=%m.��ŏc5�f���*�@4�� ���c�Ƹ܆|�\"맳�h�\\�f�P�N��q����s�f�~P��pHp\n~���>T_��QOQ�\$�V��S�pn1�ʚ��}=���L��Jeuc�����aA|;��ȓN��-��Z�@R��ͳ� �	��.��2�����`RE���^iP1&��ވ(���\$�C�Y�5�؃��axh@��=Ʋ�+>`��ע���\r!�b���r��2p�(=����!�es�X4G�Hhc �M�S.��|YjH��zB�SV��0�j�\nf\r�����D�o��%��\\1���MI`(�:�!�-�3=0������S���gW�e5��z�(h��d�r�ӫ�Ki�@Y.�����\$@�s�ѱEI&��Df�SR}��rڽ?�x\"�@ng����PI\\U��<�5X\"E0��t8��Y�=�`=��>�Q�4B�k���+p`�(8/N�qSK�r����i�O*[J��RJY�&u���7������#�>���Xû�?AP���CD�D���\$�����Y��<���X[�d�d��:��a\$�����Π��W�/ɂ�!+eYIw=9���i�;q\r\n���1��x�0]Q�<�zI9~W��9RD�KI6��L���C�z�\"0NW�WzH4��x�g�ת�x&�F�aӃ��\\�x��=�^ԓ���KH��x��ٓ0�EÝ҂ɚ�X�k,��R���~	��̛�Ny��Sz���6\0D	���؏�hs|.��=I�x}/�uN���'R���n'�|so8r��t����a�\0�5�P�֠dẘ��̕q����5(X�Hp|K�2`�]FU�~!��=� �|�,up�\\���C�o�T�e╙C�}*��f�#�shp��5����mZ�x��fn~v)DH4�e��v��V��by�T��̥,���<�y,̫֞�2���z^����K��2�xo	� ���2� I��a�h�~��c�ej�6��)�]����5�͍dG׊E�t�'N�=V��ɜ@����b^����p:k��1�StTԙ�F�F��`��`��{{���4��7�pcP�ط��V��9�ىLt�	M�����{�C�l��n47s�PL��!�9{l a�������!pG%��)�<��2*�<�9rV���\\����]�W�tn\r<ė�0�vJ�栱I�i��1��Ys{uHհ?�ۖ�����U�o�Aߒr`S��Cc���v�˳J�c�����=��-H/���q'E���w|��N�{\r};��>�x�r���u5�B�*\0����M�����a��\0�{HU����C�W�廳�yB'�<�6�[��s���y����@��{�Q����>?/<�K@� ���B�|aH\"�� R	�@>~@�BhE�L\$�[��Sa \"�Ђ0�Fe`b\0����@�\n`�=���n��.*̔�O��Ϙ���n��<jO�lM�\"mR���/��*�&T肙�T� _E4����8���|R0*�o��Bo>S%\$�� �N�<�|��ξ����y�7\n����޴�,鯢������쐬�Pt��\"l&T�o��E05n����o��r��v�������֣Bp��p��\n���P���.-,��q����3\r/�p��P���b����%m���P2?P����@��0(�/gpz�0�`��gυ��ϑ�\\嬳q��>��p�@\\��u��@���\$Ne�Q������0(A(�mc�L'`Bh\r-�!�b`��k`�������`N�0�	�ЯnN�`��D\0�@~����`K���] �\r�|���ʾ�A#��i�Y�xf�\r�4 ,v�\0ދQ�ɠN��Xo����� q�'��t�r\$��np�6%�%lyMb�ʕ(�S)L')��ޯL�M�I�s {&� KH�@d�l�wf0��x��6��~3�X�h0\"��D�+�A�\$��`b�\$��%2V�L�� Q\"��%���R�FV�Ny+F\n��	 �%fz���+1Z���Mɾ�R�%@ڝ6\"�bN�5.��\0�W���d��4�'l|9.#`��e憀�أj6�Τ�v����vڥ�\rh\r�s7��\"@�\\DŰi8cq8Ğ	�\0ֶbL. �\rdTb@E � \\2`P( B'�����0��/��|�3����&R.Ss+-���cAi4K�}�:�����\0O9,�B��@�CC��A'B@N=�;��7S�<3�DI��MW7��ED�\rŨ�v��@�DȺ�9 �l~\r�d���5�z^�r!�}I������sB��\0eT�K�!�K�UH���/����2�i%<=���^ ��g�8�r7s���%N��E@v�sl5\rp��\$�@����P��\r�\$=�%4��nX\\Xd��z٬~O��x�:��m\"�&��g5Qn�(ൕ5&rs� N\r9���.I�Y63g6�]Qsv�b/O �|��@�y��^ur\"UvI�{V-MVuOD�h`�5�t����\0��T�,	(��q�R�G�.l6[S0@�%���C}T7�85mY��)�8�C��r�;�ئ)�M+�4	� ��4��|�Ϊ1�ZJ`׉5X,�L\0�7T\rx��H��dR*����JЦ\r����52����-Cm1S�R��T`N�e@'Ʀ*�*`�>���\0|��I!�E,�ag�.��cup�Ý9�`B��aa���p�`�m�6��R~�\0���g-cmO��1�\reIN�QN�qo\r��nq����R6�n�Sn�t�w�æ\r�]a���-�a*���\\5Wpv^ OV`AF����3#82p�H'J'nM('M=j�9k�ZbBn�<�@�<� \0�fe�:\0�K(��N���v����-!�1�ލH(�Qg��µ���y�<�� �d�\\�c\\�s,u�˃q0�~�i~���e�Ѷ��*�~��Ƞ�~��M�m���}Wؘ\r�� �@�\"i�\$B���c�g��5b?�6!w��+xl1�`��`��	s�� ��������.�vCnhEd Q��id\"6���`�\"&f�x�(\"�2�Qz��\$�[0%�0lw u��>w�%ر��%�w�Z�\"-���u�%�����Y�g��>x\\�-��פ���-v�\\��x^'M	�P���Y�P����)�8�%�C���@�DF ��\r@�\0�\\�0N��.��S\$��YI��C�I�� �i�>xP͸͒�:ͷ�=��T,�'L��ٞ�q�Q2͌�\r�����d���Δ���@�ђ���9F���`�O��f�O�w�\\h�=�}S�jGGW����A��L��RJ\$JP+�7���L�v,ә(̵���ZP�g����&z+����j���˘7�ͷ��-�vA��w��h �^9�T�OD���Z��C���m����`O��R�yӒ��!�Gvzs��G�\$IhY�����58��xF������Y9����i�ݍ8��U��C��[���e���Zq�uA��1���?���و�9!����:�ړ����b0��{\r�Qh`Md7�{2� ۲8�H`%Ƹ��{-�l�C�Xk�H��Ӟ��|\0�}�X`ShխX�ց��\r���O�y��X��� �:w7�����n�鲌�#�/�:4�(M��;���c�D��z;�Z3��������]��砛�?�.�����\r�o���bO�^`Ϻ�|��������/��X׎�]�|�����^�!%Xٽ��8���\$�;��z�T�xK��-~� 8X)<!��y�x�9�����:�� ىF�������x�z+U຃�����A�E�;��'�%c����Yߪ����w�<{��9���V:��`��ʇ<����G�ء�Y��\0�Z��U�Zq\nmx�)_�}�YǏ�_�z�����y\r�Y��,ۚ3�L��٪�Y��ٸϻ>�M��	�M���	�)��P\0u8 S!Z{Y����9θ���fV3�o�Oϼ�E��`CЭ��࿿XU���}�lw��0�}�����͙�7�Y3ӬӔ��4��G��J��&�ä��ͭ(���-A��V=f|���@E/�%\0r}�ޮnn�\0��Ly�����<+���_��|��#�A��\"C[y��EW���rW��f(\0��Л�>�)�����_�U��,U�\\�#��e���*r�`�N�Y� �*�=a�\\��&�^g4�mü����e#��^�|ނ�QXN����I�>��\0rƉ���4��^Y�V#�)��k�>�׾���Ι�ԚF�W^��%�ݒ\$+�ՍP�kY*u�~��,��M��W͂hhG��K��\\C��7HmZ���S�Z_U��%�\r��b)��gg	q�������@@�����΅t�\rJ���۔��7s������U�K_1��t�j&S�Bi\0��� &\r���`�:�j�F�~=T̪��g�侑��!���^h�^�ו�������/[{�B����(�/�|���g���j/�d\\ޖSɗ�9���G`��u��1�M��?���3}�Q\$q�I�m~����G=��oVz�\0_p���!t�r{��^Z&��	��u�X��1@��G{���Ь�	NI������\$=0�Bu82�S\"�6��Qpj�ov\r<��ɶU�\0.�����E�M\n8�V�oQ\\�`?��L6���=\r�l�����������\"�����B2pu�&\0���5�\r�j��0V�A�����;v\0eH;��ʇTJ��6pH?/\\�H�@!pp�C��+5�\\+a�8;�\r(*��T�Ƣ;�O�|���^Ld�&/��NI�T��|#���G�`j%Ǘ��D����Z�ġ4�ni�i��4��]@t��#5c�ľ��	�Z��R�yR`@ँ\$I{z����胇��4|� ��׉�܀@=hCE��H��, ,Z����i���K��àP�|,g�z*�����E)AjknK\n��C\"J79�}4�f����*�4�65����׏���Q\\���c��M��\r�{��*�1j����lF��m�4��M�*�`�X�G�D�A-qqab���1�9R�H���b��g8�+�l/������ń� (�ʀL\" 8���0(Dc��#ihc���`��8���A1�\\�uK(�4!�������d��3�8����ш�Ʈ4�j�;�#��Ø���s8���5,ucnc�F�N��pPa8�G8�r�K��������ύ�k�i�˕4�A�	�8TҨ�26 ;*i��X����2%M�BJG� &�C*1T\n4	-#�.�%���'z�#�8�A+�@S.0�׀���II`U�Q��U�dd\$)��*]���T����C��9M*��	\$b+��ѽΑ�ydt�\0-��L��8\$��e\$���<Aɍ!�d�\$�p@]�d����&��M+2E��y߈((_|�Md�vU�9!�eD	�(��W=���#���_�'�bN�;��'��\0�O�<�L�iA�� Р�T�����\0�Q�J# }Ba(�/�u�GB���%-)��h��u����~\0�I�U��Pr�+1�������%51��ɒL`ܞE'(�/��Q�Ô��%T�)9�Or��T��],�?�<�a�	����/|�\$O�@Z��I�XN�|�%�,�SK:]ha��%��)k��P\0,���'�0J�:��	��&����V�0����jهJM�*�x���P)���j�K��R ��\\\r�u\r�(ÐW���F: k�����\0��NJ��P!�Q2 'H� *\0�g�T|����~g`D,�Ͼ\0#�	�;(\0�� ��L����f�5�'��`'���&t(��L�g�A���\0��'��ksi���&����d�m���P\"�Ng`O�&����� X@�	��%sh��g_�sb��f�5��M>s3�@T��77�+��nS��dӧ5'�6s\0\\���\0O:��NLS@ P�{;9�Ͷp�F��@�78_�l�9��\n���)�Rg�9�@a�:i�\0�vS�D�g��\0�S����\0�s�M\0B�\0�+O�q�`��>��4	 T9��7=�M�v�=q�'y;�'L�f�F��f�)�ϖwP��T�f�>\0�O�|��?0)O�~�|�����`#N���\0��>�'Ϫ}�ՠ���>�~��e	\0�?�*P��3��\\��@�͌��5\r'�C��P�� O�E\n�MB�#к�T;���=jPރ49����Ez#NƉ٢���FY���\\��\0CA�QJ�TV�������7 \n��v�0@��_��Q�L�RRc!��V�|�z��6��KKю��eS������4��\$�aI��|P��A+��.qKD-�S �EvbCO�>��H����<��\r#��LPܘ�s⥺P֭2�0� =�*��WL��2d�t� \0�!��<	�b�q�\\pa@�Rd o�fKM���p ���\0}����z�\0�����2�Ձ��3\"� �)@\\*g�r�M#!�<��O�XT\"�`\n];S�C���Π��5�B�cP�Ӳ��[���\$4p�&��\"��i�NP�Ӡ�'J�\rE�&8z��p�@>��BR�ݛi\\��uD*vz��Sć\$*��TZ�\nd�6�i��+���J�D�1I�:�=�P��������\"q@|�p��vjoT@SS�ڦ��*���'8\n#����֠+`ɋ�����SC!���:Q��j�|�gXђϰd磬%a�X�^O�uG�e��'��p\0{V��\0�gQWxX�Z��o>���B'�= 'L�)�v��\0�7�1L���k�T�B�R�t�G���,��� �R�MZRWL�U��	��K	�Wngl,T�P�\0�:��`*Y�tS�W\\�`\n��S_�_K��kh&[�5\\��\0��U��� ��\0^\r�rC\\�;�5E���?W�%�:��\n!PZ���BwWW��\0{�7�\$�+�[�1h�B���\0��63.kw,l0��f��:���5c�2X�F�*�j�2���Ðe�ee�L��&��}7��,2��߯�y]D\\��G��.g2�8\nj��][]�M	dc��}{�T锘�+xG�s,�\"���:vQ�\\n�H�7ڟ�yu~ �X=B�*�d�Q�Es�M���u?Z�FH��N�,�D_�R�\0Mh�/�~�y��|C�V���^�5R��2%�Z��V��s*lo{,[�vB��1\0Ƭ���>���/����Z�R�a\n���E� �A��*�a���2�\0a��@z�\\70�a�r���ǩʐvfX�°g8�Z^6g1�N�o��9%��S���t\\�OHH���\r��\n��W^&�A����/��4�{2��<�v�&Xi�_���Ҭ�f��Z��Vз�\\�t�&��0\0�gb�;�J�I�,ˠ\n)��.R�\n�T-yD�Kx¡_�/��x:��H��W5^UQ5���sUZ��IUwfF����R��S����}�Dnz۶F�����\"\0\\�1\nv��W�++g2р���SR�K�C�b�L�%&=7�\n	6��\0r )��\n>g�1���ބ8)Xo����\$U�;��P�V��z��J��}�H;r+ѝdx��\r-��%BA&\\;��'��om|za<n�p =�^=|��8�'@�`6����teD��)}q�_d9\n�	A��#}��_0��m��`�{��\r�rc>�篁~�d߰L�?b%�/�}��\r���>�;\0\\�`ցP\"-!�n!�����7��ϯ�71̼@���M���B��*�A�jn�\"`j���Jҳ���1�E�1m6��/�G�_��b ��s�_z���'~�]L:��\\���cF�a-8()�G̩�a�f,���Fp��E���?�X��)2���\")pi����4�1��������^���p�X5���P���x�܋�w�	��~@���5w ��Ǳ&�J����z�jl�d�	`P�������\"/d{8�����X�Q�d)8��	qx3����ņb{�,1���qM�����>D\\X��3g����g��Ťn�I���b�6xx���3�b�dnU^!�&�|k�	9=Yv���Lq��\"�:��D�+���w���y��#������t.�X|D�xn���<qI�|Hb��ĕ�r-���n���,Є�:a��^�.��ȞF���[_̭����/Oz���8�����\0��;���l�HW���m�O(����W����'@�\$Ȱ�ǒCt	>`tQB��������lm̉8J�O���m%�`�^Kp1��Q����2K�\0�=��y)2׬�4��P��7#ȳ���ċ-TQ�>���.%�\rܥ�Ҩ���k��6@�f@�y��^��;��������HK�>�h)�\"�R2E(̠��-�H�0�e.p��\$��-��7%�)vo�\$/���+.����ce�;�^���ʁd�Wy���v��@q�47Fy;�lY���\n�xJ������l�=��x�&-��]�*d}WA@.OM��;<ک���\09Hl�D+\0�\0�&�UR9���œ��&��&`c�΀C��D��la�}ܩ�=h(�D@ЄG����hP�W�f�@�oB'��@�&H\0���;�;E�N��-��zhoC�9��4mM}l� z&�4v�\r{Ez���4`?\rh{F��n�@ӥB'�қ�d�f�\0�E�B#xZa�v��9=7`����ٗ\0���\r���W����>j�\r�I����f�!�������+S��<e��6���i��f��T�Ԧ?�|��jH�Y��Z5S�9����6�KN�G�}+�x�B�En)Dh舋���n[1���g�ʙ��_�}�v���U��IX\0��:kSA@R+���V��?�b�����B��jB�7�A�X�p6j#Gy��X�\0�R��Xf6��C@r����P���ֹu�d���&��&�@�����z�a&���%�|h��Y�G�/����y(Ŭ��@�|,�d���5۟��0�l�eV�@޷=��}^[+ىW�lR��PN�,ٶ\nK�Q\0�\\0�0��C�|���i�,��0ͳ]|�Og���٩���q�'��{тUD`��eLIP��!W�����tI�_�C�bǄ�Ki�ix����Q;�j�z�)�8 2���b�����\0�FO/%��c��v�L!�S^\0�[�T�!ۚҍ�֚�=h�`�F\0D@�y2E\0�ڌA �՚I�DF��nf�M�t�f�i��Qb�J`�O\n��z�WV�&���(��(�P���YGH��嵮T ��ܡG�Xp�I�-\$懃@=�,��L\\�������o�/�T�����H��w!���XMz���H���:wK�-;<|�{��o\0u�X��d�?\\�f�F�{ˆ�Iyܲ�I/q�ݯ��3�(�S�@\n���̍���w�	)	�m�j����Tij3��X��~��!\$��@�G�(�8o��)�S!o��`{�~��It����<>�8r7\nmÖ|<��?E��7\0�9�W��Vk�8������%�Q�a�s}�����7���61I����M�]�/�0N#o��N�4�����a-�#x��n��N^b�mҶ�uE���P�kT\\�/��Ȍ��z����w���^Br_��-x�E3��N\0+)�����xS{�d�,���%쭲����B�%������ìi�_�pW��H,O��zLo�QQ7�q�<F�Pz�/i/3v�t�p�D�l��ps����ri��k��W p��:�p�����f��}\r8gA�����+:\0lȮt���S]�垩�ν\$/dt���P-�U�،�u�D���>��ܶ����WT�e������\n'�p:e.��?2���GrS?<��v��9�����{�2R������RӯX8%�!lZ���t,�M��Sf�Kw���8_�>jsl�\\��77�g�.�݁��:^e�P���pL�K#@�`�\\GL��5١%��B�@�'zQ�m�����m�\$��3���!�\r�V\n���	���\$�YT���02���w�% ��,�S�]���s��:u�dc\r��3�T�ၻ7e(ؿv��Or/��he.&0����ns#���Y>#)����c_Rш��q���x\n����ZR�jp5��r�n�Q���?��=�i�\0x�*�-����{�.�6�F?#1G|��\0.�x#*�nc>�`8k�%`S�S�A�\r���0��7�\n��m��ڜ[��F��;�F��\$\0T�k�_��_�nv�>8�����2����@�h��W��쩜,m�b*v�\"��0yv��p [�<��#�/�!+{��7�v0�O��_7귻�@��\r5~\\F̰��N�y�`���z�}	�o/��лU�ѻ���8�{�#Zϣ�u��턖���xk��7'��{de��y\\G������ļ 1�  d\$@�9\$M�w6���W_d���@��\\=�)wgsP��=�����=s�^����t��z�g�h�}C����njz'�{Y�wi�_�}��4��\r�3٫�\"g�9�؞��!r��:y����� �%������p�C��e�׿�3�\nwd�����`�pXR`�]L�����e�jDjUtמ��w�>��_��+�X=��^B���\r�K�b�ߞSd�4�6I��P��,^9����áu,}0���x����\\�D�!����:k뗅��>`1�`:��P���_\\To׉\$F�&�T��i0Ⱥ�����_n�J��/[�	~��{���۷p����C>��n��\0��f�@��y����[�?�D���G\0���4Z\0ۍ=�ށ,�!�S���I\"�Ѳ���F]���_%��k߹��_\r����;�1�v?�t�T��\"^��8m��e�^S7!��������@��F	�x�f�Б^#��'��oq�������Ě���w��Տ�E��.��O������Y�����(o�����qN؇���yĤ�p[nw3:�봌�;L�ہ����\0�;*�P6)��*Ȏ������U��д�=�\$��\r��V%\nRRA}���\"�f��P��=�1�4�=&:>�\$�^RMp���\$IL\0�1��𥀔���}���a	H�z��	��	���@�1�V�=&�L	A	@̔�T���<\r�^�j�x.�%�p.�\0Q����9\n�(�.	�@���`(�J\0ʦ�>�����\n�!@2D�֒�\$iE\0f��@�v\n�*`��d�W�s�\\�|�>�H��*��@���Q�j���P�;�|<|!I����0#@�=)�k���X0W��1�ݐZ@�U,D@� (P_@�t��p)A�/�c���0-Am��k��	P2��BQ�5�T1A�%{���=\$�PI=K(�T�V��*@sAҢ�����7@�Y���̕\$\0���`.�9����A\$��Rp{����PV<��(O�#\$ po��*dPNA<`� AG���@�TD\$�I�QBLN�C�=\0������[���<'�A�	��i%���У\$BW�6%S	�\nЙ��L&�@}�p�A	\\����`��#t�ǭ�	�,p&'z[��C�	`5B���\$`�At�BS07A��L��x	�\$�	�� ��bBA	�/�B�U,,0\0ޖt�2@�l\r�RB�i/�����L.r���.�_�X(��\rC\n�/�B�\n��A�!02C5<,ЇB��.ж<x���-�t�]PW�o����N	��ֲ��@��J���F@�.�<��\nC��`����れl����\\\"�� !����D�;`3�^�\n@��'�	C`����(��(���&��\$��\rL:����ð���AjC��<�<�vQ��0��\0��C��XH\0002�^;]	~���ܰ�c�D1��(5(���49�FQ�� �M�E1�@�;�?D\$k��AD�#DsDG�D��I�fD�J�#ıTKQ .���C��H��ĭ<Gq\0�=�!�jC��a�q �5	��L�M�=���ˍ@�O�;��\r@���E\"�iq�gO����49�T��+��D5�?0�������2��AMD�F�7đ�E�7�\\�F�R�dRQ6D��N1�Z�V�r�A+vE\r\\M�D�LV1Y�kO�c�k�YqXE|X@�C�2c�iD�lLQ^śY�'�QoEY,\\1fE�\n��h��]Qd�]W�YE��F)#�h�p��<|*d��2x��7��;��G�^��\"�\$h%Jv\0�X��/�+��w3G��b�3��q�Fq��5D9�Y��\0�.@���69\nt+S�E�*���4\0D����qS����x�8���\r|g@�!_��1�����\$4��w�~���1��n�r�\0*\0�#E���0?�	�'\0d� 	��(Hx�F��\$��\0�H�`(�������x�GD�tH�!�\nɁ ����FJJ|�6�>?)PBQF�q�\$`�\0>/j�T�\r��Z\\s�:	(+ �En��!Fq�^��֬s1n2��dD1��x\0a/�~F�|�xH�>4Z�\\�6�П�_��5z8��y�P?������4 7�\0��\$y�0�Z�>\0��|��Gΐ�s,������^DS|s )��X�5��q1�(LG#�[���'HL�i[��H&H	܁�1 S[&y��K #G�f�K!�[i\0#5�i�+�\$N(��-Jf����&9.E��\$�T���B3!p��\$�0����r���:��e�,���[�0���1Ϻ|�E!���\$,��,�K�@; ��0�l\\�~\0��L1Q��و\0%x@3���� 2G`L[�ܯB��b����2d�L\0��I��������n+r��;�\n:��7H�\ri���\$lO��I4Xd�\r)D1�r�5)��Q�W�J��#��H0Հ<����%hrE�����QH�\$�s#	�ؙi�W�f�gHa8!q̾��D��	^H�T�ق9%̏�\0����H�!Ѝ*JGi�\"�ߌ,M��\$�����M Y\"�II��X;ɟ)(�@�s���P��\0�����/a'PO�����\0!`2���Ʊ��,�O��r��ˀ�-Ĝ�::L���D~-�C�_�F�R]HF 	�x9T��r����Hb*p�[(��2��B\r�T��;-(�GҎ,:�|���\0��f���jD<f��p��@)(Ó�i2�(��O���[�����L������2�\0����'�H�1P��g ĕ���P\nԕ\0�ʵ%k]�\0�(�b �H�(��aHH# I@)�ҙ\0��)��+C�J�\rl�oN\0�̩��������	�n��g,0n��+�H\"�I�7\$���It��\r <�a0H@���+���)�d�!a��T�r��D���rՀ�,p\rD��`MGX���N=5 ZƯ�><�P/�^g`���*+u�i%m%Ӧ�e��'L ��ԚҞ\nK.�Q���),��˫.�!�]K�.ԧ�gK�\"4�\"݈�(���\$y-ø )��W�r��{/�M��K�/����c/£R�,E �Q�A\0����\0�HQ�BL\"Ҥ��2�/\$�%/��3�/0��2<L,��s\r@	-���y�`;��2�\$N|��Y�.�Æo�0Ĝ��c%���k˹+��@�f�ܲ��^�s/��O�̄Bi%M�\0�2�\"W���+A�!\$��)��%��L��2�9/�0�ˠ��t,�33?'�̪y̸C�f�F���؀ħ�\0��T ���6�pȋ3�2������'������Q#����H\n��UL���/���۷\\X	�j���C��M\$��w�T\0�)4�U\nL�k�O{��\"D�!��\n��\0ۂ��h>!?�G5[%��t�_(�kQL�>�ȫI������ə\$,f��i���Ǹ�dڲǹ#��sl���\n�\0I�JK��\r�m%09ͳ��*�!�E9ĭr���*i���̎2S�w\$|�\0�J��a���j�[+���)ρd�K�T�#F8(�a�d���/I.,�MtH�1�P!H)����R�N<c��oJ�Ԩ���8�j~ɡ,{��Z�@\n��?�8q���i9Ď��J�\$�I��8S�\0)GC9�b�\0��鲦�Y%iv�\r��)0���0&	T�����L�G�|��\0�-q2���+d�o���8��KL-D��D�%<��t^s�\nk;ĭ&o\"^8�:̊��P\"�	�;s[3�N�5��,9�5��,��c��r_�̀�Y���c-<�1�\0���Fd���e-,���⨨Z����-�����eH-�̇�=����_-���̉_-r#�<M�-B���͉>@\r��Τd؁���:�5�̚�.�@9ϭ�\r��E,`(81�����^ˊ	P2\"��+q-N�&�D��&�!�9\n�+4���HT\r|����4I�#�Y�Z�;�w.Ě��M��b������K���s��;@�(.�J���a\n\0�ILsb�\$�Ɇ�N\n���so	_3��8�5-t���/-pC���;A�����)�����?�S���+�\$1�4�賑�D�\r]\0002\\\0�����L�)�\$�2\\��2M�2����(��@��G2��ăw>d�M1P�8ms!W(�kQQEH�T�\0�\ny�`������p�a'��G�B��'�s���HHT|u-�L�C�B��K�\$DvŸ��3�˰A=�W�.l;!,��B��P�/����b��j�L/?�����������5T[Qp�d�[��D��MQt?��^��E��QQ[D�?T_QL)-�:Q�EU�!�CF\\��nJ�L��̕��ѐ�ilQ��l����\r�G\$�r��E�Ty��F�T{Q��4gQCG(9xQ�F=���G\\��}Q�H�`7�mĬ���-!���\$���� 8%T�R,R\$lԍ�mC���Xd���F%\$��T�̂��ɋ �g2V�T�!d��4�(kT=��+�4��Z�� %��~��ɔ��T�����+�R�;�80\r�����L^���QZXQ�6��8}��I�9\"dewK8;t��F]-�JO���Ja�Z\$`~��V��\n���-�0��AK�a�p�%X�\0�&��)��8�Z�;��0�A?I���#\0�G�M3� �F���H��\r���M�'HQH��\rx�V�^�3�M�M̃D�o0\$����0�yR�;��0I?!(DRͰ�(	��G�O���3\0\r+��`RL�`��QI�����T�r�Q\0#�k@ �O(�a�C�` mj���������A��\0>\0F%��)]H��\rƶ���] �B�\r�3��f4�&\rR�Tj�\"R�]/d��?�\n`Ïğx\n@ �r?C@(�\n��T4\n@�&\0�@@	�r��\nx���\nd?\n�\0�P�zUO�\0\\� ��(\n��'�`�DHd��b�aRD)a�p1Z�F��'\0�\rO������[�KR�PE@P�TU@�TT}Ag!���@ C���_�v��s����ji�F�?X��\"��?*�0�&�g!�%Pm>����O�?�.��=,,��T�e	�b�\0^�(9�qO�}uJ��ʪKԟSJJP/Ԩ	�J�ET�TeK��T�7�l\0Y�'�MT�P��U)\n�Z�MԖB�\0��x	�Wл�AWkÕeP�]��U0�-*����W��6ը3\\;��F=VY��N��4=�h��V�Z��%V�[`6U�,A!M3�)0�����^`9F(����|�W�x�YU�W�i�<��Wڜ5��NH�^����a���Xa�H\0�?ebc�Z����XC����X���3����dU�V+\rdT-gX�y��V\\�dIVD�i�z�YXSH�V�dA��W�hg���b�ڀ�7�9�+\0��EZx����}jc@V�X�iխ\0[X�P��Z�j���Z��5��Z�:3<Vem���X����[�O�4V�h<U�L[�h�M�\0O���&�Hmu�,j/cr�+(\ru���\\\raf6V�Z����V��@7\0[X�,4��\"����?��U�SZՃL��V`\roU�H�����\nM<r`��G��@/�\r]�K �WlP�%C75�E&-�\r�<�`+s[�j���܏�W����\0�^�k���r��Z]%^�<W�^�{��.�5f�1W���:�W�Z����&9^�wq:W�^R�u�ׁ]�75�Wy_�P�Y׏`Z��5`0[U��o_�Vi���l����q`=V�~���@�?+_�*6X2��Bߌ�C=G��@,��V�5^X�X`Ņ�E\rBx!�2�Y����/^=y6�_�64�`my6Dh�gi��z)5�C\n��J���[b�v\"ؤ���֝_��v.ح\\�[C�\n�N!%5��0e}AM	�(�g�b2d� &9a(\"QF��c��6\$X�0e����\n�j��V��VAI� s�CVE`�>�|k��G���5�tr�d��VL�Z��vNY\"E�r��e��3��\r� #��XU�T�Z]�e\$�^\rs�W�}y?�a`❖V�a��̨�Jr����X�^]��ׅf%��e�wcxCRSX+f�wVi5bU�@2Xx��6;Y�]�hvpي,���T��f��ɝ��]�vl���Gj~�	g�\r�^��^]�!jX�f}~�qY�2\$\0�� ��uٱg͛K���m}�|Y�hyv\0�(}�\0007I��+�o�,�\0���0fU��6�������,	U�6��[h�=Aj�@����=c�q�s��c��쮅5h���\n5d��`�X�5�B���s5��`��kaTVQx�i�P֗���a�Bڣh��օ\$6m|��Y&���}��j�?J~ֿ73Q��Zh�����X�5�eh\r��>�7-ymWа�z�Ѵ�]0i�YOge��W\0I�DU�4EZ���c(�uk\0�VųwD9W��.�k���>\r�.\0�����֢���}`<dG�Z�l�u���,m]��Z	Wm�����6E�Ad�2�����.����Zmŵ��,Am����[�#���	1d=�V�sn�t6=��!���3[�n��SV�e�����Ume���N�^�ֈ�ku�v�Z�n��6�ۍo��`�םó���a-����r��L[couZ��[o��v����\r�@�[x>q6�	��\n�( МUgw�+pe���Yymݿ�	��n���\\U]���\\\\�u���t�%�c5�}fd�Uz9-p�\r��?g}�J�֏B\r�N`܉r5��%��b����V�om���3v�)��սd%��r͵w�*axZ���qV7)U�r����\\�e�Ƣƴ^vj/a>�Gsu_W8\\�[h>�\\�q��f�[mb=�w�'�X�[!W�1FV����[9Wm]�5r���`��r��w�mc��/��u=ϕ�s��:]Gr�e4��YVi�BA���o҇�7Z2A��sK�>����>���)\n�i�#&{Q�`�@�%�)�r���N� ��>��c�V�\"q\n�]vڅn��\ne�@\$�ޛ��hB��\0集�v%� ��>�鸧�@	��]�=3\0*�R-�J�T?�\n@#��>�	�(]���r]�vE�e��w�0�h�xx`)��Qh�)�G��+�1vM�J����	�ƀ�;\\����?�0����@\"ݴ���+�*>��\0�g�(>-E@!��v��wv��(�u@^mx���'vm��]�v�	��^�?\"qWm^�v`,7�ސX�&rJp��^�z���ޥx�������	�!���3{W�ޝv�ۗeݾ�-�W�\0t-��\$��������v�\n ���DI�^�wPi�����W��v��\0(&�\n@,��x]�׽^�|����wؕ7�\0W��׃^W}5�w�&�(�����|�`&]�y���]���ׯ�?��A�xU�C�޽{u�w�^/}E�7��x��w���\nE���{yE�W��yu�\0�?]�']�{��7e��|�j��_�����?}\r�w�S���֞�W��n}�7���y�\0��_������&-����Q}�k7c^1y��I��y�W�`{������t�mTSQX��o��F*�_)Q�g�^�p\\��\0���G8���ڀ&�?���m_���ꀘ�	��`~?�\n�m'�|��ױ��=�bU�H7�1��.'��U`��\r�!��{���'��x&�1�=E�Ta���i�̫�\r�!\0�|]�8�FTU����`�|�o��ޕ�����y\0�T�?����G]v�ס]���\\�&��>뀥v��8F�!�]DW�GcP�	i��O��U�����D^�|]Cj�^(���ݙ}z����p^��7�\nx\$a�?��c��D�i\$�?�����&�\"މ�xm�����l�E� �W�a�}~a��5����Р���?��HC�C�?@�@(a���Xja����ޯv� 8}�{*�8�\n.\r��)��8m��������c�b�f��aՈ���ʀ�}�\$\n��5���r\0\0�2jɬ&z��#��=C���ܭ@����?�	��*���;_�f�*+Z�n\0\$�qQP5\"ݐ@0\\���x@Mر��.8�b�|����{�av\n��b�%/����a�~�8i����>�/�>8�bՇn#Wܦ��P���⧅��8��=|zkjT��*m���� ��e���0�����u�J��x&]�E���)K��\\�_A~�w�c?��*����8ӏ�(�w�Tq������6�3�~6��_`��]��xq`���-�I��&6���و���c�zu帕cw�&-���݆���ݤ������?V3���*�@\n��bɊ�6�v\0���<���S�x�\0V��\n��c���	)��ox6>�j�\r�����c�h\nx���?�z9\0��.��`#��=�	bE}�>�+�@rx���w0��e��0��a��& ׌���C��]ي�x��_|,|\n�bL�EU>&o�2��b�֝N)X	\0�4|�&�kW���bzX�����\nf�vdIw^&�����ĝ����na\0!����D��&���y2�wX	���ҠE�D����u�7�у�+�\n�.x����-C����P0�]��:��s�-|\"��!`tX@'�n?.R`&��?�g�r�ߏ}C��cK��\n� ��^SM뀯w�\\�Ï��\"k�`�y�8 /��? �7����������I�a��\n�\"��`	�*cB��X�c�^��Y@�&{wx	��Ǿ?���>_�·��e�v�[����\\��dƚ>��S��4�\$���]��e���^!�e�X���&�S�_@(d��F]��堭:�*��}�z�i���kX�_�za2��\0��rC�f#v��ᙘ��9��1we�1��o�n���5��d4_f(�^eY�F�c���A��0������X5`Ҟ.��U�n`���j�<�D���%N�⧥�6�#]��Y \$Gb�m���b��b}�f���C��ᅓ0�UT��­x#`��kz�ԧ���m)��;�^m*�_�N� ᳇(	�f-��n#�c���o��\n����)��p��Ǜ�g	�+U���\0�E�p��Nq�&��5�  ��T\ni� ��{���a�v.rJߓ,?FuX��X��zp��(�Rɖ?(�y\"��S�y�gH�\r��?�T����#�S���gs��	��\0i��)�*\0q��o��\0��Vz�\"�A��jX�e�>��(b?b����w0	\0/��w��	�(b�r\$�՜�q��)��rc��\r��~���A�~��a�y5�W�(b�\n�ɟf׌2hsϦ�zh`�L�y�禹���0'/�G`\"'�Z�*=]�}���9�j�Y<��5E@+��L�}����?\0�5\0��&Hy�fo���ؠ�6jI�\0���{X�g%��~9���xg�\$h�Sz�!����P呀.X^z���p��s��'�S�����~Nhz3G��VD &'ڢ�~t[�㊮������z�c�?��-��{���b�?�&��޸�V����&��ɞH��4����OX���S���\$�͌\\�\n �ɕ���	�~� �I��PY�h���q����Vix��EP�1���\"N7�h�vVh��dEx�j��^�֗�;ϥ�C@)dZ����-���/����`�[�a�u��h���z��|^�	�饃P�y++��eF��f'�F.��&z�R�U;+Z�#PY�ݺ�����5���W��|.U�hUw�1�*�Q��*����p�a��C�����H)�'p�j�@>eɚ�#���tw�g#��jW��Մ��	��>����6@\"@<���ʝ���f`d�9��[!\0��C�\r@VO.(b\rl�/! ;@ڃ�-4��I�L�B�TT�:��o\"��aj��\r}&��bT�!R���ٚ��N�R��L���G`��3�j�dYj�^hzr��[J\0IS���>�z�\0۫�]\0�j�?&��ψ `.�l��i����{C:�R��%\0003��j� �W泺ʜ����k3@]��3�*ѥ<����{Vc���M�A[�\0�y@<��?�yC��㚠\n`++O��|��b�X��c^	~Ny)����	���#��F�&����zX�C�&��	k��^QYJcI�^N��뵍N4��`������*}��k�}�:�k�z%�w�k�@H	����Ri�]���w�+,�C@Ø���>�HJ0�>�\0��PUT�� �v���\0?�\nj�FI��.��Hж���{4�ɨ]�\"��U#����)!U)���!d��\0�\"���L���T�J�����@\$��K,Ӵ�%�Q5����r���M��Ӻ����P#��%��R	01����]+�8�4�F���@�`d�ؙ��<S�)C��9����1�l�D�r��#\n+ 4�kK8����K!�{\"��*��>\0번5{%�\$��kA��Ƽ��A�9����T�Ǿ�Tr\0�O�\0as����m���hȕ���/�f��O`�m�	��	�tCz><tD&�\r�ش�M@���R�ur(m )�R.��bQ���j�Z��a)����L�l/۫�%W��F;�8���m�/�}�vGaB|~�m�+'�vm�-�s'��6���P��1��Y�����\0��IB�˱����l�.8o[zR�1��1���r\$�R@\$S���̱��\0�N!F�aX�f�RV�h6�s�\\�P;��[G(Jc1j�>�!�Ѷ\r���nwV���͓���:�\r�L�o��m�黤\n˸��:�=���b�}V��[�	_�`J{�#!�?3'����ߡ����{���:`hrZ77v�r8�紽�AT���x�nJth�����\nh�aV{Q����W�4́�F,D���C��:�i[���h���`��Æ�ݼ�ee���3σ�L��;IH�El ��d���P��/M\$X�����3|�����!Us6���a��C��#	6��ҡ�s�{\"�Z�V���c��!��k�r_U�0V�m��%��<e��z��4�� ������d��%C?F�[+��<�!,﻽ur�[baxg�����%��!��#o�ZX8!F�\$M���:��2ml��OX��;O�����#`mwSS>�lrNk0���@3N��3I�>����n�FD�`_Z&|C���`�Img��V�6e���u>վ���k�j�j��=�A�G�Os��dp��E��,��'�,�t[Q����@�	_�x%[I���C�c3�\n3��6t�!,� ����	mDXr��5���3J}�P\"[���4�CNL+q	�<�\$ZX;����@��I܁��.��\0<��Z�V�r#\r�[	4\n���`F�\0ɬ��q�l\\�� �k��#|Z�%K0K.\nIZ�̠@܄��L��-�Pd�^��1S�������D��H.�iZì}�Z��\r�ͦ�m���ھ��\r�:��k�,s��\0��G�����W\0001\0ȼ���u�ݬ_\\xq��mZ��G�6�\r���?'b2r�?5���G@���o\"\\�r\"o yq�IG!���_ǯ#�k\0�['St�Eγ���OK7%2c�?�n��<�4'\\urf�A\\'H�\n�����WL=/N���u�NmUwv��N�E������y[G�P&��2�^���G?M�*f��M8�)�s����c���Y�uvk[E�:���\0ǿ���H9�x�d����`0��,hCB����)���{�J|��2[��Җ,K�s	�{A1���u�ar��>��+�Vaz#�͈W����ʭ�74`�)��5+�00\$,�sE��8;\"�ta	�W�[�����͛�p��\nF������d���[`G0&���� ��s�2\\ſ��\\�s	\\���͒t/5��va�����N�\"V	�g`�+bMpG�<1����(<�����D��sʟ=��sϴ),vN����\0�@�\\���=�ٹ��xa�v��<��O�@���u:��o�ϗA ��f��6��s[>}J��\\5���֌�\r�9�&G͜�Ҋ���=G�˘W��5�y�;\\Ū˧H<�Ӿ7\0SGs-͟H�&��H�!��y�j�{����\"B;�lk5a8T������W��Z�L�.��5��t�`�L�W���M\"�t��M���������IGM����f�7t��Ţ�u�9c�'\nW�*�6���&گa��\0�/!˓��\\����>������������ YC�ᆚ?O.�l�@O=*���@��W�����j@`���Ċ|��\r�p9��b�N4�W�9�@��M�P�����Q|@�Q�O>]R�K��+��W�g[�����\\|�s�b�H�t�\\�K�r���;�y�t`/^]N]t�&�����g�W\$��f�_qOP�9�W\\`�]C��A?F�C�A�pj v%B�b���Ӿ���# q�4�Y5�P�4���Cк1T��oq�tAG�\\�&/k2}�<�L�A6�5\"Wg,�#�g ���Z]�1�¤�I�ń��GS\nHk\\��]�g=��(=C�/ւ�R�]9ک��m24������r�\\�ւ�����������?k�w������t�gP�u���2u�H�݉	�͓=��?�X��2��gkQ�����l5�֙ew8l_ܧn���Mr�v�w9���4�O�ws>�a%D�]�IE�e}lt�({�]�__Oc��vH�C�9)�m�u�M}=�\0�@07B	_�S�\\�2Bݽ���U� ���e]nB��M-v��\r�v\0��pMɕ̤��u!@w��H&���Ӳ�t�֖��n��u�\"_�@��)N���ڿoN�W��s\n��Q��N�lw\"�|��������AjF/-���w��4q}��e�ox���\"9=���z3_P��K��:�fN��h��Qs��_X�Sj���S����5�%��v���A������FB�;R��pGG�\00063���@61������{%�+LS����?+����f��Iu7���t��at�\r7����0�i����Wi��?�3��@�n��R�@��m��f������lg!��A�F&v�-������^\$�Y���aj��(��_���o���?��ە���rK�dC���3w�I̤���5�\0q۰�����;wj����[�����a��596��#�L�kvo]��d~a�\\���������'V�u�4�@��`��{�G-�_���j�eI:�����Q���ze{y�,i:�v���z�}`��I�%����7'���]]����y�_gI;��R	v��ϡ�ބ��x\rc@sk����&+�O���n%赵r�r�#t�'��϶4��WV���U��=�066�]觥}kS9w���zb�9>�Y\n�� �zb��M�Uze�Y2���\\���;�?���x4M��Ӆz��?>.�鏨>�q\"U`�Ӊܘ7;2��*t���aa n�o�%?�Q�����Vz�ܧ�K�Ϋ러>��5�A�q�'*������>�>F�ơF�Kс\"��m�/?<��k�)�ޞt�Z|�~ɬf~��M�%�������=\r��t3<g\\�yl|����l�Q`X�Y\\�[�m�'�x���\$©��\\sCㄺ8�D�t\\���\$��ͯ��p�w��g�^�%���a�W����/�w�>b���7�;�`[�^�J	��}s{�����]�o�>G\\��=��������{�\$��v��'A�\\���g��������{�O`�[��Ks�W���k�5�����*����ϯ�{��.�\0je���\0000����\\mzp	gA������Eٟ�����@�V�N����S�Ͽ��O�����,h�D����*S�z���=���w��+v���+|A�����P6*��vgBfŰ����J��WQ�g²��{�(O��bu1��J�\$���!�����^�!��g�CT��\\� Ԁʱ6�x{�Q<��:~G?��{7��\r�KuM�	X\\��])w���}+)�}SI��[�;����J稒?H��Կ�_I���G����B�vGtM��3|��C\0�p�gOR��-�H �@�q;^����O�IPʉ��\r��f�w3���ﶟ{!���C����׵ℏ�茜Huڐ�T�8{V�v��OHb1G�k��\n��C�8K�f��'ߪp��+׾R�����>Xt��L��2mU!���?K�6O�2~1���ۣ�))g2a�%�O\\�wZeȿZ��ei_�u?����ׂ\n��W6}�YYڲ���P�_~=��7\"�+���F���g\\��'��b�&h�Z�����IϜ���֯����\n�D�_~�ey�;\0B��E���OU0\$Sz��&�*oU�FMS�rRt2�r/�2�e���޵,T��Me��1�a�eE��T&.P�%&��W%ɡ�u���F���Sz��RT.�̚P��6�����Q~�g�`�������P�X�\$��9��D%�V\$j��MTEY��C���Yի�BP����Wc �W���%'���IU������=�?�C�G2��f���]63]���[�j�U�y����\0�Z�ÿ����s�s�l2��M�²�XX��t\0e���9�VD�Yfp�F��[�\0=_�ѓ��A�@���\\�\0��	�'V�F*(���[�@\"Z���]���]��Rc��u|g���-�SEL��e� �&���<�h�\n�P\0f�t���0\"J�Q�?�D�W��TSO�@���q�k�G2��)?�0)g\0�`5�*���\0�T�_�\0�(�L�*����G\np�z;�J���gh���&Z\\,�ʖ�B���,4�M�y3��,�M�\\J\n\0Oi��o����\0�@0|�\n�����Uv���yo%^�Q�)~~�� ި҈C\n�g�E��v���Y�td��������r��j���uF�516�ɵ�dEO���@�d�E����6��`c�h\n��2,�W��sk��y9��`A��b2�``�i�S\"8�;�`���U�\n`*̩��\0E\0�U��Rp�)��b��=�ҧ� (�	�Aa~�!��Z� ,8 �0�h��A�T��`���ih��(\"��ظA#^:ʸ�( ,]`���aU����%`U?�&\0V�͚F�hl�X��_\0�5��;�l%^X���er�ٓBcz��1�\0W��6X)��Y�AS��ӥ��h K���A����t�v0C`�Abj���0V���oeLí��%O�GٯAN�Eh��.⠒4�a��,�PV����^���/�f�*`���d��ň�H&�cXs�8�����x}��<Y�@�a�N���Zؔ1�b�ā���7Pi`�*z�Y��|X,�j�\\\0+�)��X-�F؈1���?37�4�H���zb@�q�4QP`�޲)b�����Pt ��სM�+�4�f�1,^<���⵬�Z 14c����^FX�S�u\0��A�3-�-�i��h��]�;F��d6@�f�-S�'EE�\r�ɱ�ah�ђ�\\e���߲�+0M�V	\rDɿ��e�v�88,#�ު#_3�!�6�/�k��d\"8C����m���p����^=7M{b֕+R�F����0f4��8FP�����V��a��}�Z�j���qf�1�1V�0��j4gg6�@z��-�E�A�T��?�0�#A�����U��� ��m��^L���;%L<�\nG\nRPGF��%S�umd�嵪=v��,A�9�W���	�C�t���GH����st�@~[��5),�F8�c�sؼ\"m>�Lj9�qm��#�lr���m#�[��|�Ŷ��6ˉ�M�llf�(�7#�ͣ�6�Ipѵ�ĭ��M;xH�0v�=�ύ���l�#}��y��A����F���+�������6a:\n�U�� �ɉ�:�Zl��|\$i��H�l��MK�����6�~L7�2�V��Kۇ������0���̤C�ղ�tP�D�Q���v�q\"��c5o&ᇆ�m���J�1�Ϋ8��3L���\r��G�)�11��FHd0��G�O�ܤk+��b�C������S��П\r@;x�g��\n{T��T�%]ؠp�J�Ԗ��(��.�)���f����wT\r 8�G��gܛ7����a2\\RCb��D��������6�O\\�盲m���rvp��{�6�0Ɠ�9��^-B���΋t�7z��+�������%�;lHq�C��ɭ8�W��F�7=��������-��54r��3��)��.G:܅9b9#A(�%�vh���h�4�	\0gK5�c��gע�=�H��oE�mڣo���	6t<���!�\0s\0���vP�86X������n[C�\r������э�(R�Y��ʴ�P���m���ݠM����RnZ���?�F��[���������aj*����}@-�R�\r�~�=�?P����\\������)���PD�\\UtBEy1	ŕ��K��h��ȅ�&��>����¼B؆��\"�)�^Td�lC����jD/N�;�\nD+竆�����\n������\"(!HYt�1�D�c�\"+:�`3EH���\")�O��2#E��KO��΅�ߙM|��k�7�n�i\nC~ec��:�oNT��F����D��@���:\$�����>D������<H�eq#P�D@?�8�c��e���&�Z�15	bp�i�9\"PD�9��7x*6\\@k�8��8�W�J\0�j�=t&�TA�Ca������%���ˑ/Έ��\0�\r\n@p�mn{�<9ꅩ	&k�&�I)�g�0]L��4���R\"h�gsx��Aț�7ҢD�*���?'�I�\"Z�RZ~��&I�7S�y��D�,_'�s�J��֦-~��2��lN�I�/�d�@tKpBdD��n]\"�\0d��%�X��5�3���\r�<QG�1BU��o��A�Pآ���w�Ʌ�q�cȨ���\"�7.s�:(�R�;�J�@�yT���`k��ʢ�����8#�ȧ����<�\\��]^\"Jx�p�.w��tLH��]_k�M�J*`�ȫ/eމ8���\n�-�`�`�=����)�UH� KS����y�T��\n�M*C�R0�+����T�����z<��QZ��K��v+є��Z��6(w�kd+��4	c�ѿ��0Sᘲ����l���#j�T�'��)�-Y��ͽ�YX���ߗ>�t�n:᧪+_�#�1��5B,Xȯ�b��CG��'�X:��AT�qG`\n��sn�Il�-��>����]V������n�et�p�Ak&A��a+ڧ�J��L��%l_!�ڥ��V�(=��TM�ب������n:�N�ĘkIA���\0��bbؗ�o�Ԫ:�l�F/ʽT�Cwx7�ut���\r^��s���-[����A��.mj=XT�����R�\n���0Y�� \"O�A��bLd�ABx𸕫?�#SO���!�t����S�ȅ�4�	� `�F_��K~��n�x���\0��:�yN�\r%�RgÉ��&D����y��Ʊw���Gae�C��D/Fa���h*���\0rG�e}���f�� m���At�I��g���R/Fv�Z�\\PLH�@~c:W-7ÿ�mK���&�8-,h� ��f��-?{I8r�')���r�M�2Jp\r`��_z���j8ԪT�F�{�\r ��\$V�A\n�=�?[\r�JH��mG���L湝���5~\r�2��s���d�2u7)�s7�s��%���-�\"������Ғ;h��	P���N�B\$���1&�b�z|��BxI�+�tL�Zs:�z+�{�{n�^�\0h%�ۯW~\nz@�\"�w��7p\"g;�3^���sz��-0,�Y�|��oA�3�H�.{#xF^mt�N#�ȗiݞʶYs�n'{�w=�Ƃ�Ų,��mS�����H;(��@=W�i=޲<����{�'ӑ�\"��x`����|(�1΄�<0��a�c�ȩ�#���u��ū��[�����q{P��qx�i�;�3H\r��������?\0��[�t�>Qf^��M��q���텕��pn6��/k�F�.\\��%zt�\$6�݇�Q�U�1lR��^lju;*dpF����� 4B8���	^��z}��\n��5��(���{��ǩ,�����2�g\n��ګ�G}�3Ӱ����=�\n4��@R6��>Ǭu̡V=��(���`��zx�8*Q�)Oj#���	M%�k����:�ZA�D|��1�b+��\\Uu\0������\0q\r��h(��t����V?C��\\�^�I����������@e�:0!k�3.є��[������L�J�8�n���S��`F�m3��FH�ac�:�{���;���n�6\"��!c�)��A*<�|��;�����\0�a}�޹��Kx��]�Ō��15(܃��Oj�� ��Df����!��ЏvB%(���>,\$���B�\0##]	HT���d��&\nXM@��2t�\\��E��!����^8ħѶ�7�#����a��S	0�7E�XB��.��\0M��H�F/ZO`;ʝT��?A�Gtv��(���r6ǚu���8�(�Oj>��5 r<�����ӂH@Qy\"���FgyIO�����6nEa���3\\�k�\\��Zi�����#x�<X��w��b�C9�p3#ʻ�7'�|b�!wZ���Ҫ7���Ik�&����/�J��U*gUb2ISZ`\0		.*i0���C��{,��K?�H�����4�tIUC#E�Z�2^�edk	�T�#iUJ�{�7�I!jC���S�'�V�<�v?�~���⫡�ڤ��ŀ���G���i0��x%؇��K�`_hr�g��D!nH��\$���EW��>Ip�P�?��ː@� L�WBEU�I5�buZ(U�RO��+%VO\$�\"Ha�ej¤�����4�`a`s\0��PW�\$�bP6I*�S\\�+&���5tr0)#F�A�&[*�mW�y\$���ے���Ka���XQ�c�&\"\$Y��-�d�?CVB��Id�BRa�䌈v��}&Ki�`��_?gG#=�D�g��W�A*\0������}��I�0�'<�ɉ�6�s����bAq�l�'�R\0'11,�z�5UO����.j�V�B��a	�Ԃ�OĚ;fJ`a`�3h���y�&�C씀�5\0'\0���������ή�_:��}��Ro������A��������^�'Q~\\�i<,hױ��]��R���Ӕ�d��@K4Fh�:\n/M����x4��W�ׂ|�}�;�K싙��Yg�%��2����@)3fb��P�2FeEٿ��g��IvK5�R���a�μ�3�|�h ��T�Q�&?����1Jg����=�����0�g�U��#?�|����*��x�Q3�mpf���b4M�>�ւpWI�4*���CA���0��(����,��,�/\$˔�P���F�eX�4b4M|�l�����??cF�sG�m�%W���R�*Kf��'\$ׇ�i�ɉ�\\ (�W�/	��j	��!�F����\n+L�1��A(�͡ц5vf�`8z9A�̈́�YH�yi�Ӥ�J�\"��	Q!��Y��TD��r�0@?t���Bvm=	��h��S�T@36m,\r�������@���l����+8L�##�W\n8	[XxR��A����H�l�����`Jې�V �DR����2�+,:�n!ף�k�'ۓ���P��E9r�%��4�������EuL�� 4�7e�\\���H�u5�9{؉ֈtG�{�ٰ(�IY���Ha98�w�e:��TZ3zS�t���K�r�_-STW��b{A�;CI�7��711�D����r���	��>��59�����cD��w�fFLH�Ӄ	�<�\$N8�I��b��	���l~V&YLcpH�S3)@�2���A).R㊾�yH����gګ&�\$�\\���[�[��N��6���6>��H��!��KN�P3�\r�䟥>7���A��d�:�&�Z�%7k�(�װM��˚[trp.I��Q��������Y�(�_�&5#�d�)��)m����H,���r?dlH�(�r]��X`�!@B�\$\\|���Ke��\0�\0I���h�Lk��xh&����9�1\0p\0��U�r�@�8\0j\0���9{�\0�7!\0�fĽ� \r@\0006��\0��������5��/�_@�����3��0�I�R����	�/�`��}`��K�6\0���� ����\0q/����|�\r�\0000��0J^� ���L ��0�_\0i���%��\0g/�_��SaS��\n��T���9��&�4��\0���I~��\0�,��/����&!L5��\0�b��)��\r���L�\0���i~�%������p���_��1Fc4�i���fKޘ�\0�b��)��f\0000\0b\0�c���2�&*���0���ً3�\0005�0�aP	�2�fL&\$1h��I����\0005�0~`4�Y{�\r�QK�o/�c��9�S&�F��0�_��@`\r <\0c��\0�a\$Ǔ�S-�L|��0�e�	�_Kޘ�0~`��)�3fV�}\0b\0�c����S&K�r��2f_�©|2�Lu��1`d�ٓ�0�L<��/�_l�~�@L����c��ɐ`\rfh�b\0j�a�Y��o\0004�3_�ę�3\r@�4�=0jbL����5fJ\0004��36^����@�g�&�90Za�ɹ���X�5��/�h(i�s%@LŘ/�g\\�i�s\rf\$M(��4Nd\\�I\r�&p�Q��\0�`��I��\"f�KޘU0�hĿ��D�\0004�q0\"bҹ}s	&�\0002\0i/za��)��%&\nL��3^jt��aS&�̙�5*cT�顓\r@����4\0�f�ɐSV�5�>��\0�jT�	�:�jL\0g4jf�Y��&�L?�5�a,�	3!�9L��3fa�Ѡ3?��K�0_D�T��f����2kܿI�3A%�K0���9|�	f�L��y0:j��9��:&�L�\0c3�ll�i��g�8M ��5�c��y��.�̝��2fg�Y�S\"f{�\0��3�ht�@7�y͢��0njT�)���rL�L�l(�k	��\0�\$�q��5�f�)�%�ͼ�5�X\\�i�sS&�M4��6b�飳rf�͆�A1\"i�й��[f0L�<���I�sX&�0��/�k�ɉ��~�L͋�10�c8����������2J`D�Y��AJ��\0��1�iԿ�3+f�M%��1�n\\���Sf�M��+4�j|�i���L�5jbd�I�Sfn�S5�0�<��Y���Q%�W6�i��3v�L��8Jh��)�sGH�N\0��0Bi���P�'L(��16m��Ub�ͅLq��0�j��9�@f5MQ��2Bd�Y�s��tLH�q5�l<�	��P��K�g0nr��i@&���Zk�s\\Ɣ���E�J��3�r�Ʃ�R��F�r��5�p�Ʃ�S\0%��f�w3�cl�9�ӄ&kL��=1�a��)��t�=N��M3`ѩ�gΓ��9\nd�y��A���{��0pL�Y��(��NQ�M7�p��9��l�\0�ʛ2\"k���3:&�N:�o4zv��937�4Mq�%:Nhtۃ_�\n��́��2Js��I��&�M��5\nl,�9��'BM;��8Nw|�y�3�&L���2fs\\�9�-f�k�0�a��	����e�m�W/�nt���3�f�N���3�l\\�)�s�'/6���/�r�y�S	�.ο�-7ot���g'�8�;5X��)�S�5Β��0�eDӉ�svg��\n��0Z`��9���g�M��	0�j4�)�s*�=N\r�s2vw��ѓN��%�=;nj��9��5�2���7ud�٭���L��=\"o��9�s�&D=��/�c��y�����:33fd�ݹ�Ӽ�O\n��7{T�i�S�IM#��6�`��YƳ+��O#��9ȩ��	�f&�M�\0�k��Y{�<��MM�%5jx,��8Ӂ�A�d��<�d�����H��O���<�z|�	��	'�M��9�m�Y�3e&?O�}3�g��S&��3��/�o����\$�����>�d�I�k�e;�+>ReqS�� �\$N��<Fp��Y�2��O�X`�x,�����']�\0��26k��������ϋ>�:�p���3�f�Nƚ�7�i���r��M���2bf��	�3H�hL��/�t��|���l�̚�:h\$�Y�=�N\n�C=>q�����&0��L���a��	���V�!�]3�k���SR(�^��2.n��:sx�@M�1@>lt�Y�s�f̀��6���ɨ\"%�L��3vaU���LH�G4�pt�	��&zM�I@�x��Y�s��rϩ��1Bn4�ɕg}Mٙ�4.}�٦�Q��K���=6z\$�I�3�f�G�Gkə���fxMt��0ց��Y��[&\rϸ�!0>m��f-M\"�2�h���S�&��Ι+9N{m9�2�&�OJ��>����鲓�'ROb�E<�j������fN�g<h4�������С7=s��3��J̒��2����ɱ��9L�'?r��ǩ��v�����@vg|����+�9�s��Afzl�	��&�Lj��::��Zөh\n@�F� �^T��k����]��ՑEP�K�ٜ��e���N#\n�]@Y30�cnM٢��`���h�2)hf�9�=F��I���^����XP?�i�Ģ8�N\rK��xY�3���a���(I�Q��3na��Q���7,B�)+�N����&���bBQ� �ȸ�����\r^�ɞN#�ORj�-\n������( ���j�ɚ\n��w�V��QaDi���8��,��'<� +A�3K�貇�_`�KA�%l�(��L����N;�'(�(��v^�R�������%F��+Ū�\0@#(��w`)�~U�l(��K�9��ȁ��da�y_�u�kq2��لI��*M1��0�F��I�_�	�ҧx0�c�}3_�FĭL\r�+�H�ѱ\0��	�B��J�p�m���MT��Tr!��\0�G&E�[�֠���m!���0����Rq��,P���NH��/��D�Q�^x6�E���Y\n�Ԛ%V&��Xp5��ϔ�#v�Bڙ\$\\\0P&�C]� �#2���G���4\n9K���;j�,��:@�|Y��S`O�|��Pk@إ2��8��\\��4�|�^Q#e&�\$�2�0״���>ʉ�3.�w��x3\0P�-����JJ����H��iv�� ���65lZ4�)�C0&Y.�!���IBe9�KFE��(�Q'IM���-�Y��Qq�ѱ�0���}�D��\0����\nF��eY]�c���K3%��i/Rq`�F�+\n12�9��u��NxL� x?dݩ8��d�'&�F�=iC��d�����Q�X��h���b�\\�d�M��d���?��t<p.�L�;�j�ɖ��5#d�m��/f6H5�E����(�D)����F��ʐ�������0�cA��d(ΩF�,VR,�:�7\n�%�5�\n�w\0�ʚ��ϝ}(��l��VVED�c���gWƨqTp�Տs(+Ph�ѕ��@����̾K�� (j��@t��Qf��I�N��5!���@�^пf��v��iwAV^��E���鴀h��	c�К!>� ��p^����?Zb�kiuAN�H�m��F�@��D_hÞ�8fw��i2���L�x9���ŗ�QY�^Ʋ%\"B������\0V�Mv�V�Ř!��c` �U3� ��ɪ��`�M2����@����SXbM���9˺XZ/��,�\n��4ZO�{)+R����a�CH2tŚ���gH��Sƾ�� ����K�kHb̫���Re����5ZOlhW�Ʀ,���}u�l��`�x��N�M0�0^il�Z��LZ���s��i�4;aN�����O��)�S`��N)}�8f�Բ?Ӟ��K�\n�3x0Y	�2�k�N��1f�+��Ss��Θ���pԤ`�1g�LY7���6�œf����a8�D�o����'Kɯ*{l�U����O�E;z-�i+���J\n�#�}t�)�A�hzOJ�c(4��i�S�co�v�1Z~����F�v��%�c�鐇��\0��%@�f0S�*�)�T��7�5������kL\re3�s�鞲t�H�b�,�e�oj�)���f�U=x6��i��Od^�Ҡ}CJkL����-b�Mf�S\"�k����_�5֚�DF4��)�����O��D�F[�S�_�֛�):n����Y��O��G�!�g��ӄ��Oq��,�L��ű��U�U92���Ӕ�O������^�ӟ��N��=ARui�S(�R�]B��T�)�Tg�)C�H�X��)d���P(]�9@i��e�O�U<:��(j+A���Q*�UEz{��S΢R��M=�,\\)�B����/D̹�lԸ`YO��t!ztT�h�S�3O�v�!�gZ4S.�������~M)�Tτ9D\"��L(C�\0(�/G^b��@jcUi�1��AH�Mf+��mT�Þ�E2��4�i�A�9LȮ\r3J=TF���%&�S���B��5=jp/d��P�>�C����*SV�{S�����i��j!��T�5 x 5j�TKb�T^�}Lz@p@i�TT]�QZ\rEꈴ������G���*�5��U-aQ���F�q5*:0\nh=Q¨�G&z�9�z��&��\"		8r,9��07�sN^�\rG��������R����>�AT�1O���PZ��E�F�t��U���NZ��AjPU�EU\"�-Jz��*`�Ԭb��~�eU:[�=�T������}�\0�|���aOzsR2i����2f^z�ʬ`\0�\\�w��\0�&��6�|��Z7/t�漂��R�̭���1��VZ��7j�k�*�\0TFܻ��va�;�дբ�-�I;*)�k��jg���{Q��'�\0���|H���\n�4�)�/L�oV抳�	\0�~Qx���S&:D�jI�R�`n|��A��^jU���(\$u]ʴyٳ�S\0�V6�)�Bp�`��꫼�d�;V7�~j��q����Mm�Tn鈳y��X����3���\0��sM'�Z�Jf0��q=���e�'fgXq��W)B�i)t2_����y=0	*��jUȧ�����bz��}���/d^�N�cf�u|I�4(QXʍ�-��Mv+�Q\0�Xn�udz����!\0CF�O���O�}�j�T�&�Px�>�~�g+)\0Y�5YF��>²�+*\0M��Yb�8�?���(Vh�h�����)Ք ~���Mְ��RU��H3�`%Lγ�̴F�@/\n��J�O��5�+EV�������0��r�I���_�ZM�#9�lkLV�g'K�K:Ҵ�X�ѱ�(��EMZ�L�)tѱ�P�F��3B�l�k\$V�&oV���kf\0���n��kZ���Y6-Tu ��cGm�\rlr�l�Y�3�hT���V0���:�Qe�K2�mP�!UB@#�ʪR6�U`�7Y��\0������o����'=[�Q�?���+���V���,\n���{����G���8�����V�k��Q�+3�>�Y��\0�W��el*�@\n��H'�}�� �bu���3yf'Y.�C1�B�ę]��\0��+1�K��/��G[)��s���`�Э5Z���^�E`��5�YHU��\n8A�k�2�\0�[�!��,�	��6�>}q�=��j��Kh@��S�#��EQ����Q2�K��U֙b��&V��#p+�\0³�k�L����uڍ6�+����iz�_�`MD{//�8�Y8\n���*�т��)��C(�U�	��^���x�lխk�ד��^V�l��L��\0T��^v��vMh�ߨ�^q�%\"*K\0��\0We^�S�**L+��w��ko^n��`��x+�5�i_[)��I�^����η�ֽ�f+�����I�xa=|��@VK\0�\\\$�u��V�d��Ud�C���%Rn&�L��#\$�'��=Rqb�K2���iFu�I�.ʀ�Fy���N5.��҃c<�I�'R}4��	���&�!��0���=Y��@�eI�}�v��U�+�VK��ε�*�l )�V�`V���y3�Su����6N���PV�A�AX��ϛ{���G0?��^��|&�7XD��f;`y�*��5*���]!�\0Bi��*�K�d͹��Z���*�)_aU���Bg��Xd�[�,�͡��K\0�2��т�\0���♒0?��NI�%��\rՙ��X��[�>���\n���X!��V����4k�Y�\0G	����b�a��W&�a��Շ&I�ujɳ2`xM6��:֫�?׸d�N�R�Sz�b�a����Ζ-�*Xv�X`���&��e�\r��\$�bt�ZY�,�<ص���m�� \n��SEf����aZ��+�jJa��5S��dhK�@�\n\\�)he_٪�T�ȕ��01�I���*�q p��f�����80@3ʡG�Ԓ̈D�jQt;a�I*��� �S3�0�6%�a���\$���b�2�I	��!n�>Ϙ���Șn[\"�l��V�dP,[#�}�Y J\0��t�\$�.,��%�4Œ�#!���\09�ed~�|��M�];:31���!��Yl��8��0��-�K'�\r,���d�&���'�Q�YQ�Id��8{)�O���1��d��p[&�W�Y\\Ien�y�-Ff�Yk��e�c\r�,�V,�����d�1��k.BЬ�YB��e���{/\0�!���eN�Ք�0�_�j�h��eV�\r�/3���Y]�+8̭�{1vQ�����7dRh]�dv`���p��4.�U�,���P�Uebh]�4vd��Y��?djh]��5�`��c�wf��՚y��g���B*{e�͸c���q,�٫�f��Ř9��a���F�f��m����k���ҲOe�p-��9 �ٺ��eNp-��<�P�ٚ��dR�m��:6z'\"Y��g��m��>v{&T����e�_\r��=vQ�\0�в�hȵ�;@6~����g��m���V���YB��gR���\$��-\n�F��gr�ݡ�;�x�yY��hJ̵�K,VZ&a��3h��U��9���-�1h���ػE�mY��qh��5��F�1��ϴvtf�=�C���ZG�ohF���H��-(\0_��h�Ҭ�KH6��(�洸~tE��6��-\nZc�h����-�-*ZK�5:\"�Ŧ��s�--�U�izӵ��<f��Z_��i��]��L��l��h��hr�4��Ms�8ډ��i��L��Qv��=�˲wjT��+`�Z_�9j��[)x�NZ��Wjr�m�KS��mSڢ���%�kV���ڳ�9j��m�;N6��W���Sj�����VV��>�C��i*Յ�;4v�m'�'��j��%��X���bZ�XCi��{R��-k�H����n]��W֓mo���k6Յ�+\\�'�#��kR��k\\mwZ��\0�LӉ�t1-gZ��k�iĽ��;�XLa��k^k��{\\\r�-�Z�\rl6�d�;b��1[w;ذ9���gQ�#�lrj5�\\V��[ ��j�hu�ZV�-xZ��Kl��ů�d�z��Z�*{lr_���d�����8�Wk�h���^V��G[8�ikּ�[c�H����a1��]�i6��sMp��m�d��iv��Z���lra]�i֯�5�`��mzڥ�Ʉ��-�[��m���	�v�%��t��k^_e�Kl����[t�[m��m�y���-�ھ5�lrbm��n��&\$ۈ��k�_���o��-���n\"�*��V�@�[#XYd.��KYj���[�&N��M�y�V�mʁ\$�b�	%��s��m��O=nzݵ��f��ۜ�uk��=��x\0n��[���n�ԕ�9{����ۚ��kr����s��-�[���oN�5�I�����[ڷ[k��TӋvV�[�o��]�Kgv��[�So_m�ɂ������j^ޭ�i���-����o��̽٠��-���[0*��[t�������9l��m��v��n�m�m�`}Aڙ��m\"����_�\0�\\��n��,[�n��3Z��Sp��}�I���-�[ᶙp��M�kj\r�\0��-1��,��u�֮\\\n��m~�\r��7m��e��p�d͹�mwm��o��qa���x��m�͡�9m�����V�-���q�����V����d�8J�%�sS4-��?��7.�Ʋd�Fn\0��,tf�)ћr�Fn9,,:3qb�Ϳ�y�!�����n���{��m�܎wjf����yV�nA�ɹ,��e������Eܒ��rnޥ�S?�.=\\���r~`���~�f\\��r�٥�/�5\$��g{��Qڂ5^�3hT�A�\n`�pU��T^��Q��'*�U\0�Oª�RBqL\"��/5b�UҨ+C���Z4/wc�����QxE��b4_s�H�-�E�RQ��fQQ�\rS��U��m�l��Q⪍(�OB�9�ժ�e�{�Z_Wi�%R��d뚰�*����T^����e�ՕG�L���=��w�]���j��jM�JJ S�~��un�\\���07a�L��e8j�k�iIA_��M.��I�l�o鑓й�U��R���RX�Uͺ��r�8k��ߩ�����M��+��9no�*�-uj�M�˜�q)��L���\"\n�����S.x��\ru��;)P�V.��P�ou���j[�>)��v��I���ˠ宁/׺t����vWB+�S����B������*OS���)���xTk��]Q�;M�	�k��ѫI���Y@�u�Dk�Z\\S��GRQ��?�&T=�%U��gR§s\r*�wn �]�a�t����l�Z�&su�U̫����%\\Ϩ�Tr\r��:��mn��U�uuZOeD��9.o]c�wn���f%7:�[�Ku��}֢���T������ʜ�_.�S���U2�5G�[0p�DR�^;v�������>ݓ�}R~N��U'��A�T�R~���U�AۻN\0�	6(�j(�B\$�-J&�l������(�5t��eu��̞�l�8e��6�5|�t�k�.ө�J6�))QW~lB�7G�\n���=,�l@�\0�R�S������\n�+K\"�3�H����nL�Q.�K�s[���yr�7[A�֤]mK+ɻ�e��Q\"��%{E1�wK`Ӂ�pr|�!�;V��@��;bN-:b���7��!ނ|�Hl��{�Ω�/C6eN��0s�g(�P���R�!DU�H�W��f^�4	����A<w�\n#�Hx�-���|ף��C�\r��{���(�Ĩu=�8A������������A;���D^[xr�ټp�q�B�=O���A0��`�1�]DZ��_���R��7u���6�C��aJ���r�z�(��\\���́�Oت|̈�:����R��^m�����@\0=5�l!zQ�|���	�B�W���#d��I�����h����(��\rGE�+�|@U���71�!�;R�9QC�47Y��z_#��y��`\"k�7Ȕ;�au���ݤ���ҳ^�j��C�s�Y��]f6artْ�h���ض�p�y��{Z�A��T��t�f����X��Y=���	)�ۦȠ\r���7yR��c����_[W,	r�]�W@nˇ>_a�#|y��&��ۡ��_�DI�� ?Λ\0��*u6��V���H߀�����V���c�:�YL\rj���.��d��h\nB����U�.o�ߙ�bY����!�ܘ}�G���0'�w��/Uſ,U}nK��� ttGȿ!~�g@�Ҏ/�߱\\40��K�[��<o�D��L��+���^��/33��+7����nz�f�����J�%�ҿ�\"��[� s/��rp,վe���R�q���g�������B����8*8G�f���ۇ��r��8���%K_�����\0��7�o/��^l]�\0�����\$���y����1M�o�7�Rwz�##i�ݍ�[L�cGz�V���la �L�@��H	\\HP��dC���\$cc������.xh���fɜ�#9�z��4]5\0�\0�Ŋ�idi�Q�O�0`Q�������.8�y�2�gW��g�3�Z�Ϳ����\r�ܶ�\"���ki�Z��A.+�'���f<O7Dئ���6O�1�X`Ho����n�\"ڨ��ߨ�`�S��`(���=޵�\rv�>�V�J�\0n�OU���(`λ��j����\\h�G��JB�J�=�|ݰQ��{� �A��J���Q/~�T&j�^nE�|z�C�6�/�Q�6�݉���w�p[߁����@a\r�Gd6��H����\0000��σ�ޘ\$��x8�L`�7k��ݨ!K�׽�U���[��\"g ��P=�wr�����d�@@��\0G�IQN�7.,ba~~lK��!	���E)Mz�JB�XFA�X��ZOA!���B �p��xi����F���i�Q�g�J�����cT/��+r�US��Fs\$�	�&�=�h��V�XXI�ޫ�� �J���Ճ[p�6T-q4�;��EPU�\0Q�\0����P\"	����b{g )�\ni�ah4�L�\n�1l+��@9�i\0NU��ȄNk\r`O�KJ�g��.�c�?h�j�	���\rR�T_����_t,5 ֥W�ob�>U����I�w;5���բs���a�����Ɛ");}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo"�PNG\r\n\n\0\0\0\rIHDR\0\0\09\0\0\09\0\0\0~6��\0\0\0000PLTE\0\0\0���+NvYt�s���������������su�IJ����/.�������C��\0\0\0tRNS\0@��f\0\0\0	pHYs\0\0\0\0\0��\0\0�IDAT8�Ք�N�@��E��l϶��p6�G.\$=���>��	w5r}�z7�>��P�#\$��K�j�7��ݶ����?4m�����t&�~�3!0�0��^��Af0�\"��,��*��4���o�E���X(*Y��	6	�PcOW���܊m��r�0�~/��L�\rXj#�m���j�C�]G�m�\0�}���ߑu�A9�X�\n��8�V�Y�+�D#�iq�nKQ8J�1Q6��Y0�`��P�bQ�\\h�~>�:pSɀ������GE�Q=�I�{�*�3�2�7�\ne�L�B�~�/R(\$�)�� ��HQn�i�6J�	<��-.�w�ɪj�Vm���m�?S�H��v����Ʃ��\0��^�q��)���]��U�92�,;�Ǎ�'p���!X˃����L�D.�tæ��/w����R��	w�d��r2�Ƥ�4[=�E5�S+�c\0\0\0\0IEND�B`�";}exit;}if($_GET["script"]=="version"){$o=get_temp_dir()."/adminer.version";@unlink($o);$q=file_open_lock($o);if($q)file_write_unlock($q,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));@ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",HTTPS,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$Zc);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",'15');function
lang($u,$_f=null){$ua=func_get_args();$ua[0]=$u;return
call_user_func_array('Adminer\lang_format',$ua);}function
lang_format($Ri,$_f=null){if(is_array($Ri)){$Cg=($_f==1?0:1);$Ri=$Ri[$Cg];}$Ri=str_replace("'",'’',$Ri);$ua=func_get_args();array_shift($ua);$ld=str_replace("%d","%s",$Ri);if($ld!=$Ri)$ua[0]=format_number($_f);return
vsprintf($ld,$ua);}define('Adminer\LANG','en');abstract
class
SqlDb{static$instance;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($N,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Nb);abstract
function
query($H,$cj=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($mc,$V,$F,array$Rf=array()){$Rf[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$Rf[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($mc,$V,$F,$Rf);}catch(\Exception$Hc){return$Hc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$cj=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='Unknown error.';return
false;}$this->store_result($I);return$I;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($kf){$J=$this->fetch($kf);return($J?array_map(array($this,'unresource'),$J):$J);}private
function
unresource($X){return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$U=$K->pdo_type;$K->type=($U==\PDO::PARAM_INT?0:15);$K->charsetnr=($U==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($C){for($s=0;$s<$C;$s++)$this->fetch();}}}function
add_driver($t,$B){SqlDriver::$drivers[$t]=$B;}function
get_driver($t){return
SqlDriver::$drivers[$t];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;protected$conn;protected$types=array();var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();static
function
connect($N,$V,$F){$f=new
Db;return($f->attach($N,$V,$F)?:$f);}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$m){}function
unconvertFunction(array$m){}function
select($R,array$M,array$Z,array$vd,array$Tf=array(),$z=1,$D=0,$Lg=false){$oe=(count($vd)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$vd,$Tf,$z,$D);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$z&&$vd&&$oe&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($vd&&$oe?"\nGROUP BY ".implode(", ",$vd):"").($Tf?"\nORDER BY ".implode(", ",$Tf):""),$z,($D?$z*$D:0),"\n");$ci=microtime(true);$J=$this->conn->query($H);if($Lg)echo
adminer()->selectQuery($H,$ci,!$J);return$J;}function
delete($R,$Ug,$z=0){$H="FROM ".table($R);return
queries("DELETE".($z?limit1($R,$H,$Ug):" $H$Ug"));}function
update($R,array$O,$Ug,$z=0,$Fh="\n"){$vj=array();foreach($O
as$x=>$X)$vj[]="$x = $X";$H=table($R)." SET$Fh".implode(",$Fh",$vj);return
queries("UPDATE".($z?limit1($R,$H,$Ug,$Fh):" $H$Ug"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$G){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$Ei){}function
convertSearch($u,array$X,array$m){return$u;}function
convertOperator($Nf){return$Nf;}function
value($X,array$m){return(method_exists($this->conn,'value')?$this->conn->value($X,$m):$X);}function
quoteBinary($th){return
q($th);}function
warnings(){}function
tableHelp($B,$re=false){}function
inheritsFrom($R){return
array();}function
inheritedTables($R){return
array();}function
partitionsInfo($R){return
array();}function
hasCStyleEscapes(){return
false;}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R)."
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'",$this->conn);}function
allFields(){$J=array();if(DB!=""){foreach(get_rows("SELECT TABLE_NAME AS tab, COLUMN_NAME AS field, IS_NULLABLE AS nullable, DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length".(JUSH=='sql'?", COLUMN_KEY = 'PRI' AS `primary`":"")."
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY TABLE_NAME, ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}}return$J;}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach($o,$V,$F){$this->link=new
\SQLite3($o);$yj=$this->link->version();$this->server_info=$yj["versionString"];return'';}function
query($H,$cj=false){$I=@$this->link->query($H);$this->error="";if(!$I){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($I->numColumns())return
new
Result($I);$this->affected_rows=$this->link->changes();return
true;}function
quote($Q){return(is_utf8($Q)?"'".$this->link->escapeString($Q)."'":"x'".first(unpack('H*',$Q))."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$d=$this->offset++;$U=$this->result->columnType($d);return(object)array("name"=>$this->result->columnName($d),"type"=>($U==SQLITE3_TEXT?15:0),"charsetnr"=>($U==SQLITE3_BLOB?63:0),);}function
__destruct(){$this->result->finalize();}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach($o,$V,$F){$this->dsn(DRIVER.":$o","","");$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{function
attach($o,$V,$F){parent::attach($o,$V,$F);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($o){if(is_readable($o)&&$this->query("ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$o)?$o:dirname($_SERVER["SCRIPT_FILENAME"])."/$o")." AS a"))return!self::attach($o,'','');return
false;}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($N,$V,$F){if($F!="")return'Database does not support password.';return
parent::connect(":memory:","","");}function
__construct(Db$f){parent::__construct($f);if(min_version(3.31,0,$f))$this->generated=array("STORED","VIRTUAL");}function
structuredTypes(){return
array_keys($this->types[0]);}function
insertUpdate($R,array$L,array$G){$vj=array();foreach($L
as$O)$vj[]="(".implode(", ",$O).")";return
queries("REPLACE INTO ".table($R)." (".implode(", ",array_keys(reset($L))).") VALUES\n".implode(",\n",$vj));}function
tableHelp($B,$re=false){if($B=="sqlite_sequence")return"fileformat2.html#seqtab";if($B=="sqlite_master")return"fileformat2.html#$B";}function
checkConstraints($R){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R),0,$this->conn),$Se);return
array_combine($Se[2],$Se[2]);}function
allFields(){$J=array();foreach(tables_list()as$R=>$U){foreach(fields($R)as$m)$J[$R][]=$m;}return$J;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($gd){return
array();}function
limit($H,$Z,$z,$C=0,$Fh=" "){return" $H$Z".($z?$Fh."LIMIT $z".($C?" OFFSET $C":""):"");}function
limit1($R,$H,$Z,$Fh="\n"){return(preg_match('~^INTO~',$H)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($H,$Z,1,0,$Fh):" $H WHERE rowid = (SELECT rowid FROM ".table($R).$Z.$Fh."LIMIT 1)");}function
db_collation($j,$jb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name");}function
count_tables($i){return
array();}function
table_status($B=""){$J=array();foreach(get_rows("SELECT name AS Name, type AS Engine, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') ".($B!=""?"AND name = ".q($B):"ORDER BY name"))as$K){$K["Rows"]=get_val("SELECT COUNT(*) FROM ".idf_escape($K["Name"]));$J[$K["Name"]]=$K;}foreach(get_rows("SELECT * FROM sqlite_sequence".($B!=""?" WHERE name = ".q($B):""),null,"")as$K)$J[$K["name"]]["Auto_increment"]=$K["seq"];return$J;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
fields($R){$J=array();$G="";foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($R).")")as$K){$B=$K["name"];$U=strtolower($K["type"]);$k=$K["dflt_value"];$J[$B]=array("field"=>$B,"type"=>(preg_match('~int~i',$U)?"integer":(preg_match('~char|clob|text~i',$U)?"text":(preg_match('~blob~i',$U)?"blob":(preg_match('~real|floa|doub~i',$U)?"real":"numeric")))),"full_type"=>$U,"default"=>(preg_match("~^'(.*)'$~",$k,$A)?str_replace("''","'",$A[1]):($k=="NULL"?null:$k)),"null"=>!$K["notnull"],"privileges"=>array("select"=>1,"insert"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$K["pk"],);if($K["pk"]){if($G!="")$J[$G]["auto_increment"]=false;elseif(preg_match('~^integer$~i',$U))$J[$B]["auto_increment"]=true;$G=$B;}}$Wh=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R));$u='(("[^"]*+")+|[a-z0-9_]+)';preg_match_all('~'.$u.'\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i',$Wh,$Se,PREG_SET_ORDER);foreach($Se
as$A){$B=str_replace('""','"',preg_replace('~^"|"$~','',$A[1]));if($J[$B])$J[$B]["collation"]=trim($A[3],"'");}preg_match_all('~'.$u.'\s.*GENERATED ALWAYS AS \((.+)\) (STORED|VIRTUAL)~i',$Wh,$Se,PREG_SET_ORDER);foreach($Se
as$A){$B=str_replace('""','"',preg_replace('~^"|"$~','',$A[1]));$J[$B]["default"]=$A[3];$J[$B]["generated"]=strtoupper($A[4]);}return$J;}function
indexes($R,$g=null){$g=connection($g);$J=array();$Wh=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R),0,$g);if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Wh,$A)){$J[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$A[1],$Se,PREG_SET_ORDER);foreach($Se
as$A){$J[""]["columns"][]=idf_unescape($A[2]).$A[4];$J[""]["descs"][]=(preg_match('~DESC~i',$A[5])?'1':null);}}if(!$J){foreach(fields($R)as$B=>$m){if($m["primary"])$J[""]=array("type"=>"PRIMARY","columns"=>array($B),"lengths"=>array(),"descs"=>array(null));}}$ai=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($R),$g);foreach(get_rows("PRAGMA index_list(".table($R).")",$g)as$K){$B=$K["name"];$v=array("type"=>($K["unique"]?"UNIQUE":"INDEX"));$v["lengths"]=array();$v["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($B).")",$g)as$sh){$v["columns"][]=$sh["name"];$v["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($B).' ON '.idf_escape($R),'~').' \((.*)\)$~i',$ai[$B],$fh)){preg_match_all('/("[^"]*+")+( DESC)?/',$fh[2],$Se);foreach($Se[2]as$x=>$X){if($X)$v["descs"][$x]='1';}}if(!$J[""]||$v["type"]!="UNIQUE"||$v["columns"]!=$J[""]["columns"]||$v["descs"]!=$J[""]["descs"]||!preg_match("~^sqlite_~",$B))$J[$B]=$v;}return$J;}function
foreign_keys($R){$J=array();foreach(get_rows("PRAGMA foreign_key_list(".table($R).")")as$K){$p=&$J[$K["id"]];if(!$p)$p=$K;$p["source"][]=$K["from"];$p["target"][]=$K["to"];}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($B))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($j){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($B){$Pc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Pc)\$~",$B)){connection()->error=sprintf('Please use one of the extensions %s.',str_replace("|",", ",$Pc));return
false;}return
true;}function
create_database($j,$c){if(file_exists($j)){connection()->error='File exists.';return
false;}if(!check_sqlite_name($j))return
false;try{$_=new
Db();$_->attach($j,'','');}catch(\Exception$Hc){connection()->error=$Hc->getMessage();return
false;}$_->query('PRAGMA encoding = "UTF-8"');$_->query('CREATE TABLE adminer (i)');$_->query('DROP TABLE adminer');return
true;}function
drop_databases($i){connection()->attach(":memory:",'','');foreach($i
as$j){if(!@unlink($j)){connection()->error='File exists.';return
false;}}return
true;}function
rename_database($B,$c){if(!check_sqlite_name($B))return
false;connection()->attach(":memory:",'','');connection()->error='File exists.';return@rename(DB,$B);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($R,$B,$n,$id,$ob,$xc,$c,$_a,$E){$oj=($R==""||$id);foreach($n
as$m){if($m[0]!=""||!$m[1]||$m[2]){$oj=true;break;}}$b=array();$eg=array();foreach($n
as$m){if($m[1]){$b[]=($oj?$m[1]:"ADD ".implode($m[1]));if($m[0]!="")$eg[$m[0]]=$m[1][0];}}if(!$oj){foreach($b
as$X){if(!queries("ALTER TABLE ".table($R)." $X"))return
false;}if($R!=$B&&!queries("ALTER TABLE ".table($R)." RENAME TO ".table($B)))return
false;}elseif(!recreate_table($R,$B,$b,$eg,$id,$_a))return
false;if($_a){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $_a WHERE name = ".q($B));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($B).", $_a)");queries("COMMIT");}return
true;}function
recreate_table($R,$B,array$n,array$eg,array$id,$_a="",$w=array(),$ic="",$ja=""){if($R!=""){if(!$n){foreach(fields($R)as$x=>$m){if($w)$m["auto_increment"]=0;$n[]=process_field($m,$m);$eg[$x]=idf_escape($x);}}$Kg=false;foreach($n
as$m){if($m[6])$Kg=true;}$kc=array();foreach($w
as$x=>$X){if($X[2]=="DROP"){$kc[$X[1]]=true;unset($w[$x]);}}foreach(indexes($R)as$ve=>$v){$e=array();foreach($v["columns"]as$x=>$d){if(!$eg[$d])continue
2;$e[]=$eg[$d].($v["descs"][$x]?" DESC":"");}if(!$kc[$ve]){if($v["type"]!="PRIMARY"||!$Kg)$w[]=array($v["type"],$ve,$e);}}foreach($w
as$x=>$X){if($X[0]=="PRIMARY"){unset($w[$x]);$id[]="  PRIMARY KEY (".implode(", ",$X[2]).")";}}foreach(foreign_keys($R)as$ve=>$p){foreach($p["source"]as$x=>$d){if(!$eg[$d])continue
2;$p["source"][$x]=idf_unescape($eg[$d]);}if(!isset($id[" $ve"]))$id[]=" ".format_foreign_key($p);}queries("BEGIN");}$Ua=array();foreach($n
as$m){if(preg_match('~GENERATED~',$m[3]))unset($eg[array_search($m[0],$eg)]);$Ua[]="  ".implode($m);}$Ua=array_merge($Ua,array_filter($id));foreach(driver()->checkConstraints($R)as$Wa){if($Wa!=$ic)$Ua[]="  CHECK ($Wa)";}if($ja)$Ua[]="  CHECK ($ja)";$zi=($R==$B?"adminer_$B":$B);if(!queries("CREATE TABLE ".table($zi)." (\n".implode(",\n",$Ua)."\n)"))return
false;if($R!=""){if($eg&&!queries("INSERT INTO ".table($zi)." (".implode(", ",$eg).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($eg)))." FROM ".table($R)))return
false;$Yi=array();foreach(triggers($R)as$Wi=>$Fi){$Vi=trigger($Wi,$R);$Yi[]="CREATE TRIGGER ".idf_escape($Wi)." ".implode(" ",$Fi)." ON ".table($B)."\n$Vi[Statement]";}$_a=$_a?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($R));if(!queries("DROP TABLE ".table($R))||($R==$B&&!queries("ALTER TABLE ".table($zi)." RENAME TO ".table($B)))||!alter_indexes($B,$w))return
false;if($_a)queries("UPDATE sqlite_sequence SET seq = $_a WHERE name = ".q($B));foreach($Yi
as$Vi){if(!queries($Vi))return
false;}queries("COMMIT");}return
true;}function
index_sql($R,$U,$B,$e){return"CREATE $U ".($U!="INDEX"?"INDEX ":"").idf_escape($B!=""?$B:uniqid($R."_"))." ON ".table($R)." $e";}function
alter_indexes($R,$b){foreach($b
as$G){if($G[0]=="PRIMARY")return
recreate_table($R,$R,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$X){if(!queries($X[2]=="DROP"?"DROP INDEX ".idf_escape($X[1]):index_sql($R,$X[0],$X[1],"(".implode(", ",$X[2]).")")))return
false;}return
true;}function
truncate_tables($T){return
apply_queries("DELETE FROM",$T);}function
drop_views($_j){return
apply_queries("DROP VIEW",$_j);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
move_tables($T,$_j,$xi){return
false;}function
trigger($B,$R){if($B=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$u='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$Xi=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$u\\s*(".implode("|",$Xi["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($u))?\\s+ON\\s*$u\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($B)),$A);$Bf=$A[3];return
array("Timing"=>strtoupper($A[1]),"Event"=>strtoupper($A[2]).($Bf?" OF":""),"Of"=>idf_unescape($Bf),"Trigger"=>$B,"Statement"=>$A[4],);}function
triggers($R){$J=array();$Xi=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R))as$K){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$Xi["Timing"]).')\s*(.*?)\s+ON\b~i',$K["sql"],$A);$J[$K["name"]]=array($A[1],$A[2]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
begin(){return
queries("BEGIN");}function
last_id($I){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain($f,$H){return$f->query("EXPLAIN QUERY PLAN $H");}function
found_rows($S,$Z){}function
types(){return
array();}function
create_sql($R,$_a,$gi){$J=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($R));foreach(indexes($R)as$B=>$v){if($B=='')continue;$J
.=";\n\n".index_sql($R,$v['type'],$B,"(".implode(", ",array_map('Adminer\idf_escape',$v['columns'])).")");}return$J;}function
truncate_sql($R){return"DELETE FROM ".table($R);}function
use_sql($Nb){}function
trigger_sql($R){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R)));}function
show_variables(){$J=array();foreach(get_rows("PRAGMA pragma_list")as$K){$B=$K["name"];if($B!="pragma_list"&&$B!="compile_options"){$J[$B]=array($B,'');foreach(get_rows("PRAGMA $B")as$K)$J[$B][1].=implode(", ",$K)."\n";}}return$J;}function
show_status(){$J=array();foreach(get_vals("PRAGMA compile_options")as$Qf)$J[]=explode("=",$Qf,2)+array('','');return$J;}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Uc){return
preg_match('~^(check|columns|database|drop_col|dump|indexes|descidx|move_col|sql|status|table|trigger|variables|view|view_trigger)$~',$Uc);}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($Cc,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=preg_replace('~^[^:]*: ~','',$l);$this->error=$l;}function
attach($N,$V,$F){$j=adminer()->database();set_error_handler(array($this,'_error'));$this->string="host='".str_replace(":","' port='",addcslashes($N,"'\\"))."' user='".addcslashes($V,"'\\")."' password='".addcslashes($F,"'\\")."'";$bi=adminer()->connectSsl();if(isset($bi["mode"]))$this->string
.=" sslmode='".$bi["mode"]."'";$this->link=@pg_connect("$this->string dbname='".($j!=""?addcslashes($j,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$j!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($Q){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$Q):"'".pg_escape_string($this->link,$Q)."'");}function
value($X,array$m){return($m["type"]=="bytea"&&$X!==null?pg_unescape_bytea($X):$X);}function
select_db($Nb){if($Nb==adminer()->database())return$this->database;$J=@pg_connect("$this->string dbname='".addcslashes($Nb,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($J)$this->link=$J;return$J;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($H,$cj=false){$I=@pg_query($this->link,$H);$this->error="";if(!$I){$this->error=pg_last_error($this->link);$J=false;}elseif(!pg_num_fields($I)){$this->affected_rows=pg_affected_rows($I);$J=true;}else$J=new
Result($I);if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$J;}function
warnings(){return
h(pg_last_notice($this->link));}function
copyFrom($R,array$L){$this->error='';set_error_handler(function($Cc,$l){$this->error=(ini_bool('html_errors')?html_entity_decode($l):$l);return
true;});$J=pg_copy_from($this->link,$R,$L);restore_error_handler();return$J;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=pg_num_rows($I);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$J=new
\stdClass;$J->orgtable=pg_field_table($this->result,$d);$J->name=pg_field_name($this->result,$d);$J->type=pg_field_type($this->result,$d);$J->charsetnr=($J->type=="bytea"?63:0);return$J;}function
__destruct(){pg_free_result($this->result);}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach($N,$V,$F){$j=adminer()->database();$mc="pgsql:host='".str_replace(":","' port='",addcslashes($N,"'\\"))."' client_encoding=utf8 dbname='".($j!=""?addcslashes($j,"'\\"):"postgres")."'";$bi=adminer()->connectSsl();if(isset($bi["mode"]))$mc
.=" sslmode='".$bi["mode"]."'";return$this->dsn($mc,$V,$F);}function
select_db($Nb){return(adminer()->database()==$Nb);}function
query($H,$cj=false){$J=parent::query($H,$cj);if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$J;}function
warnings(){}function
copyFrom($R,array$L){$J=$this->pdo->pgsqlCopyFromArray($R,$L);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$J;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($H){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$H),$A)){$L=explode("\n",$A[2]);$this->affected_rows=count($L);return$this->copyFrom($A[1],$L);}return
parent::multi_query($H);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";var$operators=array("=","<",">","<=",">=","!=","~","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";static
function
connect($N,$V,$F){$f=parent::connect($N,$V,$F);if(is_string($f))return$f;$yj=get_val("SELECT version()",0,$f);$f->flavor=(preg_match('~CockroachDB~',$yj)?'cockroach':'');$f->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$yj);if(min_version(9,0,$f))$f->query("SET application_name = 'Adminer'");if($f->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),'Date and time'=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),'Strings'=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),'Binary'=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),'Network'=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),'Geometry'=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types['Strings']["json"]=4294967295;if(min_version(9.4,0,$f))$this->types['Strings']["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f))$this->generated=array("STORED");$this->partitionBy=array("RANGE","LIST");if(!$f->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$m){$zc=$this->types['User types'][$m["type"]];return($zc?type_values($zc):"");}function
setUserTypes($bj){$this->types['User types']=array_flip($bj);}function
insertReturning($R){$_a=array_filter(fields($R),function($m){return$m['auto_increment'];});return(count($_a)==1?" RETURNING ".idf_escape(key($_a)):"");}function
insertUpdate($R,array$L,array$G){foreach($L
as$O){$kj=array();$Z=array();foreach($O
as$x=>$X){$kj[]="$x = $X";if(isset($G[idf_unescape($x)]))$Z[]="$x = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$kj)." WHERE ".implode(" AND ",$Z))&&connection()->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES (".implode(", ",$O).")")))return
false;}return
true;}function
slowQuery($H,$Ei){$this->conn->query("SET statement_timeout = ".(1000*$Ei));$this->conn->timeout=1000*$Ei;return$H;}function
convertSearch($u,array$X,array$m){$Bi="char|text";if(strpos($X["op"],"LIKE")===false)$Bi
.="|date|time(stamp)?|boolean|uuid|inet|cidr|macaddr|".number_type();return(preg_match("~$Bi~",$m["type"])?$u:"CAST($u AS text)");}function
quoteBinary($th){return"'\\x".bin2hex($th)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($B,$re=false){$Ke=array("information_schema"=>"infoschema","pg_catalog"=>($re?"view":"catalog"),);$_=$Ke[$_GET["ns"]];if($_)return"$_-".str_replace("_","-",$B).".html";}function
inheritsFrom($R){return
get_vals("SELECT relname FROM pg_class JOIN pg_inherits ON inhparent = oid WHERE inhrelid = ".$this->tableOid($R)." ORDER BY 1");}function
inheritedTables($R){return
get_vals("SELECT relname FROM pg_inherits JOIN pg_class ON inhrelid = oid WHERE inhparent = ".$this->tableOid($R)." ORDER BY 1");}function
partitionsInfo($R){$K=connection()->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".driver()->tableOid($R))->fetch_assoc();if($K){$ya=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $K[partrelid] AND attnum IN (".str_replace(" ",", ",$K["partattrs"]).")");$Oa=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Oa[$K["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$ya)),);}return
array();}function
tableOid($R){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($R)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
supportsIndex(array$S){return$S["Engine"]!="view";}function
hasCStyleEscapes(){static$Qa;if($Qa===null)$Qa=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$Qa;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($gd){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($H,$Z,$z,$C=0,$Fh=" "){return" $H$Z".($z?$Fh."LIMIT $z".($C?" OFFSET $C":""):"");}function
limit1($R,$H,$Z,$Fh="\n"){return(preg_match('~^INTO~',$H)?limit($H,$Z,1,0,$Fh):" $H".(is_view(table_status1($R))?$Z:$Fh."WHERE ctid = (SELECT ctid FROM ".table($R).$Z.$Fh."LIMIT 1)"));}function
db_collation($j,$jb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($j));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$H="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$H
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$H
.="
ORDER BY 1";return
get_key_vals($H);}function
count_tables($i){$J=array();foreach($i
as$j){if(connection()->select_db($j))$J[$j]=count(tables_list());}return$J;}function
table_status($B=""){static$Ed;if($Ed===null)$Ed=get_val("SELECT 'pg_table_size'::regproc");$J=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($Ed?",
	pg_table_size(oid) AS \"Data_length\",
	pg_indexes_size(oid) AS \"Index_length\"":"").",
	obj_description(oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples as \"Rows\",
	inhparent AS inherited,
	current_schema() AS nspname
FROM pg_class
LEFT JOIN pg_inherits ON inhrelid = oid
WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($B!=""?"AND relname = ".q($B):"ORDER BY relname"))as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return
in_array($S["Engine"],array("view","materialized view"));}function
fk_support($S){return
true;}function
fields($R){$J=array();$ra=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz',);foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
WHERE a.attrelid = ".driver()->tableOid($R)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$K){preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$K["full_type"],$A);list(,$U,$y,$K["length"],$ka,$va)=$A;$K["length"].=$va;$Ya=$U.$ka;if(isset($ra[$Ya])){$K["type"]=$ra[$Ya];$K["full_type"]=$K["type"].$y.$va;}else{$K["type"]=$U;$K["full_type"]=$K["type"].$y.$ka.$va;}if(in_array($K['attidentity'],array('a','d')))$K['default']='GENERATED '.($K['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$K["generated"]=($K["attgenerated"]=="s"?"STORED":"");$K["null"]=!$K["attnotnull"];$K["auto_increment"]=$K['attidentity']||preg_match('~^nextval\(~i',$K["default"])||preg_match('~^unique_rowid\(~',$K["default"]);$K["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(preg_match('~(.+)::[^,)]+(.*)~',$K["default"],$A))$K["default"]=($A[1]=="NULL"?null:idf_unescape($A[1]).$A[2]);$J[$K["field"]]=$K;}return$J;}function
indexes($R,$g=null){$g=connection($g);$J=array();$pi=driver()->tableOid($R);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $pi AND attnum > 0",$g);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, (indpred IS NOT NULL)::int as indispartial
FROM pg_index
JOIN pg_class ON indexrelid = oid
WHERE indrelid = $pi
ORDER BY indisprimary DESC, indisunique DESC",$g)as$K){$gh=$K["relname"];$J[$gh]["type"]=($K["indispartial"]?"INDEX":($K["indisprimary"]?"PRIMARY":($K["indisunique"]?"UNIQUE":"INDEX")));$J[$gh]["columns"]=array();$J[$gh]["descs"]=array();if($K["indkey"]){foreach(explode(" ",$K["indkey"])as$Zd)$J[$gh]["columns"][]=$e[$Zd];foreach(explode(" ",$K["indoption"])as$ae)$J[$gh]["descs"][]=(intval($ae)&1?'1':null);}$J[$gh]["lengths"]=array();}return$J;}function
foreign_keys($R){$J=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($R)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$K){if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$K['definition'],$A)){$K['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$A[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$A[2],$Qe)){$K['ns']=idf_unescape($Qe[2]);$K['table']=idf_unescape($Qe[4]);}$K['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$A[3])));$K['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$A[4],$Qe)?$Qe[1]:'NO ACTION');$K['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$A[4],$Qe)?$Qe[1]:'NO ACTION');$J[$K['conname']]=$K;}}return$J;}function
view($B){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($B).")")));}function
collations(){return
array();}function
information_schema($j){return
get_schema()=="information_schema";}function
error(){$J=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$J,$A))$J=$A[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($A[3]).'})(.*)~','\1<b>\2</b>',$A[2]).$A[4];return
nl_br($J);}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).($c?" ENCODING ".idf_escape($c):""));}function
drop_databases($i){connection()->close();return
apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');}function
rename_database($B,$c){connection()->close();return
queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($B));}function
auto_increment(){return"";}function
alter_table($R,$B,$n,$id,$ob,$xc,$c,$_a,$E){$b=array();$Tg=array();if($R!=""&&$R!=$B)$Tg[]="ALTER TABLE ".table($R)." RENAME TO ".table($B);$Gh="";foreach($n
as$m){$d=idf_escape($m[0]);$X=$m[1];if(!$X)$b[]="DROP $d";else{$uj=$X[5];unset($X[5]);if($m[0]==""){if(isset($X[6]))$X[1]=($X[1]==" bigint"?" big":($X[1]==" smallint"?" small":" "))."serial";$b[]=($R!=""?"ADD ":"  ").implode($X);if(isset($X[6]))$b[]=($R!=""?"ADD":" ")." PRIMARY KEY ($X[0])";}else{if($d!=$X[0])$Tg[]="ALTER TABLE ".table($B)." RENAME $d TO $X[0]";$b[]="ALTER $d TYPE$X[1]";$Hh=$R."_".idf_unescape($X[0])."_seq";$b[]="ALTER $d ".($X[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) STORED~','EXPRESSION\1',$X[3]):(isset($X[6])?"SET DEFAULT nextval(".q($Hh).")":"DROP DEFAULT"));if(isset($X[6]))$Gh="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($Hh)." OWNED BY ".idf_escape($R).".$X[0]";$b[]="ALTER $d ".($X[2]==" NULL"?"DROP NOT":"SET").$X[2];}if($m[0]!=""||$uj!="")$Tg[]="COMMENT ON COLUMN ".table($B).".$X[0] IS ".($uj!=""?substr($uj,9):"''");}}$b=array_merge($b,$id);if($R==""){$P="";if($E){$eb=(connection()->flavor=='cockroach');$P=" PARTITION BY $E[partition_by]($E[partition])";if($E["partition_by"]=='HASH'){$sg=+$E["partitions"];for($s=0;$s<$sg;$s++)$Tg[]="CREATE TABLE ".idf_escape($B."_$s")." PARTITION OF ".idf_escape($B)." FOR VALUES WITH (MODULUS $sg, REMAINDER $s)";}else{$Jg="MINVALUE";foreach($E["partition_names"]as$s=>$X){$Y=$E["partition_values"][$s];$og=" VALUES ".($E["partition_by"]=='LIST'?"IN ($Y)":"FROM ($Jg) TO ($Y)");if($eb)$P
.=($s?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$X)?$X:idf_escape($X))."$og";else$Tg[]="CREATE TABLE ".idf_escape($B."_$X")." PARTITION OF ".idf_escape($B)." FOR$og";$Jg=$Y;}$P
.=($eb?"\n)":"");}}array_unshift($Tg,"CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)$P");}elseif($b)array_unshift($Tg,"ALTER TABLE ".table($R)."\n".implode(",\n",$b));if($Gh)array_unshift($Tg,$Gh);if($ob!==null)$Tg[]="COMMENT ON TABLE ".table($B)." IS ".q($ob);foreach($Tg
as$H){if(!queries($H))return
false;}return
true;}function
alter_indexes($R,$b){$h=array();$hc=array();$Tg=array();foreach($b
as$X){if($X[0]!="INDEX")$h[]=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");elseif($X[2]=="DROP")$hc[]=idf_escape($X[1]);else$Tg[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R)." (".implode(", ",$X[2]).")";}if($h)array_unshift($Tg,"ALTER TABLE ".table($R).implode(",",$h));if($hc)array_unshift($Tg,"DROP INDEX ".implode(", ",$hc));foreach($Tg
as$H){if(!queries($H))return
false;}return
true;}function
truncate_tables($T){return
queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$T)));}function
drop_views($_j){return
drop_tables($_j);}function
drop_tables($T){foreach($T
as$R){$P=table_status1($R);if(!queries("DROP ".strtoupper($P["Engine"])." ".table($R)))return
false;}return
true;}function
move_tables($T,$_j,$xi){foreach(array_merge($T,$_j)as$R){$P=table_status1($R);if(!queries("ALTER ".strtoupper($P["Engine"])." ".table($R)." SET SCHEMA ".idf_escape($xi)))return
false;}return
true;}function
trigger($B,$R){if($B=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($R)." AND trigger_name = ".q($B);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$K)$e[]=$K["event_object_column"];$J=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$K){if($e&&$K["Event"]=="UPDATE")$K["Event"].=" OF";$K["Of"]=implode(", ",$e);if($J)$K["Event"].=" OR $J[Event]";$J=$K;}return$J;}function
triggers($R){$J=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($R))as$K){$Vi=trigger($K["trigger_name"],$R);$J[$Vi["Trigger"]]=array($Vi["Timing"],$Vi["Event"]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF"),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($B,$U){$L=get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = '.q($B));$J=idx($L,0,array());$J["returns"]=array("type"=>$J["type_udt_name"]);$J["fields"]=get_rows('SELECT parameter_name AS field, data_type AS type, character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = '.q($B).'
ORDER BY ordinal_position');return$J;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()
ORDER BY SPECIFIC_NAME');}function
routine_languages(){return
get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language");}function
routine_id($B,$K){$J=array();foreach($K["fields"]as$m){$y=$m["length"];$J[]=$m["type"].($y?"($y)":"");}return
idf_escape($B)."(".implode(", ",$J).")";}function
last_id($I){$K=(is_object($I)?$I->fetch_row():array());return($K?$K[0]:0);}function
explain($f,$H){return$f->query("EXPLAIN $H");}function
found_rows($S,$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($S["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$fh))return$fh[1];}function
types(){return
get_key_vals("SELECT oid, typname
FROM pg_type
WHERE typnamespace = ".driver()->nsOid."
AND typtype IN ('b','d','e')
AND typelem = 0");}function
type_values($t){$Bc=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $t ORDER BY enumsortorder");return($Bc?"'".implode("', '",array_map('addslashes',$Bc))."'":"");}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return
get_val("SELECT current_schema()");}function
set_schema($vh,$g=null){if(!$g)$g=connection();$J=$g->query("SET search_path TO ".idf_escape($vh));driver()->setUserTypes(types());return$J;}function
foreign_keys_sql($R){$J="";$P=table_status1($R);$ed=foreign_keys($R);ksort($ed);foreach($ed
as$dd=>$cd)$J
.="ALTER TABLE ONLY ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." ADD CONSTRAINT ".idf_escape($dd)." $cd[definition] ".($cd['deferrable']?'DEFERRABLE':'NOT DEFERRABLE').";\n";return($J?"$J\n":$J);}function
create_sql($R,$_a,$gi){$lh=array();$Ih=array();$P=table_status1($R);if(is_view($P)){$zj=view($R);return
rtrim("CREATE VIEW ".idf_escape($R)." AS $zj[select]",";");}$n=fields($R);if(count($P)<2||empty($n))return
false;$J="CREATE TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." (\n    ";foreach($n
as$m){$ng=idf_escape($m['field']).' '.$m['full_type'].default_value($m).($m['null']?"":" NOT NULL");$lh[]=$ng;if(preg_match('~nextval\(\'([^\']+)\'\)~',$m['default'],$Se)){$Hh=$Se[1];$Vh=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($Hh)):"SELECT * FROM $Hh"),null,"-- "));$Ih[]=($gi=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $Hh;\n":"")."CREATE SEQUENCE $Hh INCREMENT $Vh[increment_by] MINVALUE $Vh[min_value] MAXVALUE $Vh[max_value]".($_a&&$Vh['last_value']?" START ".($Vh["last_value"]+1):"")." CACHE $Vh[cache_value];";}}if(!empty($Ih))$J=implode("\n\n",$Ih)."\n\n$J";$G="";foreach(indexes($R)as$Xd=>$v){if($v['type']=='PRIMARY'){$G=$Xd;$lh[]="CONSTRAINT ".idf_escape($Xd)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$v['columns'])).")";}}foreach(driver()->checkConstraints($R)as$ub=>$wb)$lh[]="CONSTRAINT ".idf_escape($ub)." CHECK $wb";$J
.=implode(",\n    ",$lh)."\n)";$og=driver()->partitionsInfo($P['Name']);if($og)$J
.="\nPARTITION BY $og[partition_by]($og[partition])";$J
.="\nWITH (oids = ".($P['Oid']?'true':'false').");";if($P['Comment'])$J
.="\n\nCOMMENT ON TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." IS ".q($P['Comment']).";";foreach($n
as$Wc=>$m){if($m['comment'])$J
.="\n\nCOMMENT ON COLUMN ".idf_escape($P['nspname']).".".idf_escape($P['Name']).".".idf_escape($Wc)." IS ".q($m['comment']).";";}foreach(get_rows("SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = ".q($R).($G?" AND indexname != ".q($G):""),null,"-- ")as$K)$J
.="\n\n$K[indexdef];";return
rtrim($J,';');}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
trigger_sql($R){$P=table_status1($R);$J="";foreach(triggers($R)as$Ui=>$Ti){$Vi=trigger($Ui,$P['Name']);$J
.="\nCREATE TRIGGER ".idf_escape($Vi['Trigger'])." $Vi[Timing] $Vi[Event] ON ".idf_escape($P["nspname"]).".".idf_escape($P['Name'])." $Vi[Type] $Vi[Statement];;\n";}return$J;}function
use_sql($Nb){return"\connect ".idf_escape($Nb);}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Uc){return
preg_match('~^(check|database|table|columns|sql|indexes|descidx|comment|view|'.(min_version(9.3)?'materializedview|':'').'scheme|'.(min_version(11)?'procedure|':'').'routine|sequence|trigger|type|variables|drop_col'.(connection()->flavor=='cockroach'?'':'|processlist').'|kill|dump)$~',$Uc);}function
kill_process($X){return
queries("SELECT pg_terminate_backend(".number($X).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("oracle","Oracle (beta)");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";var$_current_db;private$link;function
_error($Cc,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=preg_replace('~^[^:]*: ~','',$l);$this->error=$l;}function
attach($N,$V,$F){$this->link=@oci_new_connect($V,$F,$N,"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$l=oci_error();return$l["message"];}function
quote($Q){return"'".str_replace("'","''",$Q)."'";}function
select_db($Nb){$this->_current_db=$Nb;return
true;}function
query($H,$cj=false){$I=oci_parse($this->link,$H);$this->error="";if(!$I){$l=oci_error($this->link);$this->errno=$l["code"];$this->error=$l["message"];return
false;}set_error_handler(array($this,'_error'));$J=@oci_execute($I);restore_error_handler();if($J){if(oci_num_fields($I))return
new
Result($I);$this->affected_rows=oci_num_rows($I);oci_free_statement($I);}return$J;}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($I){$this->result=$I;}private
function
convert($K){foreach((array)$K
as$x=>$X){if(is_a($X,'OCILob')||is_a($X,'OCI-Lob'))$K[$x]=$X->load();}return$K;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$J=new
\stdClass;$J->name=oci_field_name($this->result,$d);$J->type=oci_field_type($this->result,$d);$J->charsetnr=(preg_match("~raw|blob|bfile~",$J->type)?63:0);return$J;}function
__destruct(){oci_free_statement($this->result);}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";var$_current_db;function
attach($N,$V,$F){return$this->dsn("oci:dbname=//$N;charset=AL32UTF8",$V,$F);}function
select_db($Nb){$this->_current_db=$Nb;return
true;}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),'Date and time'=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),'Strings'=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),'Binary'=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),);}function
begin(){return
true;}function
insertUpdate($R,array$L,array$G){foreach($L
as$O){$kj=array();$Z=array();foreach($O
as$x=>$X){$kj[]="$x = $X";if(isset($G[idf_unescape($x)]))$Z[]="$x = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$kj)." WHERE ".implode(" AND ",$Z))&&connection()->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES (".implode(", ",$O).")")))return
false;}return
true;}function
hasCStyleEscapes(){return
true;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($gd){return
get_vals("SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1");}function
limit($H,$Z,$z,$C=0,$Fh=" "){return($C?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $H$Z) t WHERE rownum <= ".($z+$C).") WHERE rnum > $C":($z?" * FROM (SELECT $H$Z) WHERE rownum <= ".($z+$C):" $H$Z"));}function
limit1($R,$H,$Z,$Fh="\n"){return" $H$Z";}function
db_collation($j,$jb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
get_current_db(){$j=connection()->_current_db?:DB;unset(connection()->_current_db);return$j;}function
where_owner($Hg,$hg="owner"){if(!$_GET["ns"])return'';return"$Hg$hg = sys_context('USERENV', 'CURRENT_SCHEMA')";}function
views_table($e){$hg=where_owner('');return"(SELECT $e FROM all_views WHERE ".($hg?:"rownum < 0").")";}function
tables_list(){$zj=views_table("view_name");$hg=where_owner(" AND ");return
get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."$hg
UNION SELECT view_name, 'view' FROM $zj
ORDER BY 1");}function
count_tables($i){$J=array();foreach($i
as$j)$J[$j]=get_val("SELECT COUNT(*) FROM all_tables WHERE tablespace_name = ".q($j));return$J;}function
table_status($B=""){$J=array();$zh=q($B);$j=get_current_db();$zj=views_table("view_name");$hg=where_owner(" AND ");foreach(get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = '.q($j).$hg.($B!=""?" AND table_name = $zh":"")."
UNION SELECT view_name, 'view', 0, 0 FROM $zj".($B!=""?" WHERE view_name = $zh":"")."
ORDER BY 1")as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return
true;}function
fields($R){$J=array();$hg=where_owner(" AND ");foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($R)."$hg ORDER BY column_id")as$K){$U=$K["DATA_TYPE"];$y="$K[DATA_PRECISION],$K[DATA_SCALE]";if($y==",")$y=$K["CHAR_COL_DECL_LENGTH"];$J[$K["COLUMN_NAME"]]=array("field"=>$K["COLUMN_NAME"],"full_type"=>$U.($y?"($y)":""),"type"=>strtolower($U),"length"=>$y,"default"=>$K["DATA_DEFAULT"],"null"=>($K["NULLABLE"]=="Y"),"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),);}return$J;}function
indexes($R,$g=null){$J=array();$hg=where_owner(" AND ","aic.table_owner");foreach(get_rows("SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($R)."$hg
ORDER BY ac.constraint_type, aic.column_position",$g)as$K){$Xd=$K["INDEX_NAME"];$lb=$K["DATA_DEFAULT"];$lb=($lb?trim($lb,'"'):$K["COLUMN_NAME"]);$J[$Xd]["type"]=($K["CONSTRAINT_TYPE"]=="P"?"PRIMARY":($K["CONSTRAINT_TYPE"]=="U"?"UNIQUE":"INDEX"));$J[$Xd]["columns"][]=$lb;$J[$Xd]["lengths"][]=($K["CHAR_LENGTH"]&&$K["CHAR_LENGTH"]!=$K["COLUMN_LENGTH"]?$K["CHAR_LENGTH"]:null);$J[$Xd]["descs"][]=($K["DESCEND"]&&$K["DESCEND"]=="DESC"?'1':null);}return$J;}function
view($B){$zj=views_table("view_name, text");$L=get_rows('SELECT text "select" FROM '.$zj.' WHERE view_name = '.q($B));return
reset($L);}function
collations(){return
array();}function
information_schema($j){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){return
h(connection()->error);}function
explain($f,$H){$f->query("EXPLAIN PLAN FOR $H");return$f->query("SELECT * FROM plan_table");}function
found_rows($S,$Z){}function
auto_increment(){return"";}function
alter_table($R,$B,$n,$id,$ob,$xc,$c,$_a,$E){$b=$hc=array();$ag=($R?fields($R):array());foreach($n
as$m){$X=$m[1];if($X&&$m[0]!=""&&idf_escape($m[0])!=$X[0])queries("ALTER TABLE ".table($R)." RENAME COLUMN ".idf_escape($m[0])." TO $X[0]");$Zf=$ag[$m[0]];if($X&&$Zf){$Df=process_field($Zf,$Zf);if($X[2]==$Df[2])$X[2]="";}if($X)$b[]=($R!=""?($m[0]!=""?"MODIFY (":"ADD ("):"  ").implode($X).($R!=""?")":"");else$hc[]=idf_escape($m[0]);}if($R=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)");return(!$b||queries("ALTER TABLE ".table($R)."\n".implode("\n",$b)))&&(!$hc||queries("ALTER TABLE ".table($R)." DROP (".implode(", ",$hc).")"))&&($R==$B||queries("ALTER TABLE ".table($R)." RENAME TO ".table($B)));}function
alter_indexes($R,$b){$hc=array();$Tg=array();foreach($b
as$X){if($X[0]!="INDEX"){$X[2]=preg_replace('~ DESC$~','',$X[2]);$h=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");array_unshift($Tg,"ALTER TABLE ".table($R).$h);}elseif($X[2]=="DROP")$hc[]=idf_escape($X[1]);else$Tg[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R)." (".implode(", ",$X[2]).")";}if($hc)array_unshift($Tg,"DROP INDEX ".implode(", ",$hc));foreach($Tg
as$H){if(!queries($H))return
false;}return
true;}function
foreign_keys($R){$J=array();$H="SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = ".q($R);foreach(get_rows($H)as$K)$J[$K['NAME']]=array("db"=>$K['DEST_DB'],"table"=>$K['DEST_TABLE'],"source"=>array($K['SRC_COLUMN']),"target"=>array($K['DEST_COLUMN']),"on_delete"=>$K['ON_DELETE'],"on_update"=>null,);return$J;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($_j){return
apply_queries("DROP VIEW",$_j);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
last_id($I){return
0;}function
schemas(){$J=get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");return($J?:get_vals("SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = ".q(DB)." ORDER BY 1"));}function
get_schema(){return
get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");}function
set_schema($xh,$g=null){if(!$g)$g=connection();return$g->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($xh));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$J=array();$L=get_rows('SELECT * FROM v$instance');foreach(reset($L)as$x=>$X)$J[]=array($x,$X);return$J;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Uc){return
preg_match('~^(columns|database|drop_col|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~',$Uc);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$l){$this->errno=$l["code"];$this->error
.="$l[message]\n";}$this->error=rtrim($this->error);}function
attach($N,$V,$F){$vb=array("UID"=>$V,"PWD"=>$F,"CharacterSet"=>"UTF-8");$bi=adminer()->connectSsl();if(isset($bi["Encrypt"]))$vb["Encrypt"]=$bi["Encrypt"];if(isset($bi["TrustServerCertificate"]))$vb["TrustServerCertificate"]=$bi["TrustServerCertificate"];$j=adminer()->database();if($j!="")$vb["Database"]=$j;$this->link=@sqlsrv_connect(preg_replace('~:~',',',$N),$vb);if($this->link){$be=sqlsrv_server_info($this->link);$this->server_info=$be['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($Q){$dj=strlen($Q)!=strlen(utf8_decode($Q));return($dj?"N":"")."'".str_replace("'","''",$Q)."'";}function
select_db($Nb){return$this->query(use_sql($Nb));}function
query($H,$cj=false){$I=sqlsrv_query($this->link,$H);$this->error="";if(!$I){$this->get_error();return
false;}return$this->store_result($I);}function
multi_query($H){$this->result=sqlsrv_query($this->link,$H);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($I=null){if(!$I)$I=$this->result;if(!$I)return
false;if(sqlsrv_field_metadata($I))return
new
Result($I);$this->affected_rows=sqlsrv_rows_affected($I);return
true;}function
next_result(){return$this->result?!!sqlsrv_next_result($this->result):false;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($I){$this->result=$I;}private
function
convert($K){foreach((array)$K
as$x=>$X){if(is_a($X,'DateTime'))$K[$x]=$X->format("Y-m-d H:i:s");}return$K;}function
fetch_assoc(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC));}function
fetch_row(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC));}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$m=$this->fields[$this->offset++];$J=new
\stdClass;$J->name=$m["Name"];$J->type=($m["Type"]==1?254:15);$J->charsetnr=0;return$J;}function
seek($C){for($s=0;$s<$C;$s++)sqlsrv_fetch($this->result);}function
__destruct(){sqlsrv_free_stmt($this->result);}}function
last_id($I){return
get_val("SELECT SCOPE_IDENTITY()");}function
explain($f,$H){$f->query("SET SHOWPLAN_ALL ON");$J=$f->query($H);$f->query("SET SHOWPLAN_ALL OFF");return$J;}}else{abstract
class
MssqlDb
extends
PdoDb{function
select_db($Nb){return$this->query(use_sql($Nb));}function
lastInsertId(){return$this->pdo->lastInsertId();}}function
last_id($I){return
connection()->lastInsertId();}function
explain($f,$H){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach($N,$V,$F){return$this->dsn("sqlsrv:Server=".str_replace(":",",",$N),$V,$F);}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach($N,$V,$F){return$this->dsn("dblib:charset=utf8;host=".str_replace(":",";unix_socket=",preg_replace('~:(\d)~',';port=\1',$N)),$V,$F);}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";static
function
connect($N,$V,$F){if($N=="")$N="localhost:1433";return
parent::connect($N,$V,$F);}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20),'Date and time'=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10),'Strings'=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823),'Binary'=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),);}function
insertUpdate($R,array$L,array$G){$n=fields($R);$kj=array();$Z=array();$O=reset($L);$e="c".implode(", c",range(1,count($O)));$Pa=0;$he=array();foreach($O
as$x=>$X){$Pa++;$B=idf_unescape($x);if(!$n[$B]["auto_increment"])$he[$x]="c$Pa";if(isset($G[$B]))$Z[]="$x = c$Pa";else$kj[]="$x = c$Pa";}$vj=array();foreach($L
as$O)$vj[]="(".implode(", ",$O).")";if($Z){$Qd=queries("SET IDENTITY_INSERT ".table($R)." ON");$J=queries("MERGE ".table($R)." USING (VALUES\n\t".implode(",\n\t",$vj)."\n) AS source ($e) ON ".implode(" AND ",$Z).($kj?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$kj):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($Qd?$O:$he)).") VALUES (".($Qd?$e:implode(", ",$he)).");");if($Qd)queries("SET IDENTITY_INSERT ".table($R)." OFF");}else$J=queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES\n".implode(",\n",$vj));return$J;}function
begin(){return
queries("BEGIN TRANSACTION");}function
tableHelp($B,$re=false){$Ke=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$_=$Ke[get_schema()];if($_)return"relational-databases/system-$_".preg_replace('~_~','-',strtolower($B))."-transact-sql";}}function
idf_escape($u){return"[".str_replace("]","]]",$u)."]";}function
table($u){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($u);}function
get_databases($gd){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($H,$Z,$z,$C=0,$Fh=" "){return($z?" TOP (".($z+$C).")":"")." $H$Z";}function
limit1($R,$H,$Z,$Fh="\n"){return
limit($H,$Z,1,0,$Fh);}function
db_collation($j,$jb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($j));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables($i){$J=array();foreach($i
as$j){connection()->select_db($j);$J[$j]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$J;}function
table_status($B=""){$J=array();foreach(get_rows("SELECT ao.name AS Name, ao.type_desc AS Engine, (SELECT value FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($B!=""?"AND name = ".q($B):"ORDER BY name"))as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return$S["Engine"]=="VIEW";}function
fk_support($S){return
true;}function
fields($R){$qb=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($R).", 'column', NULL)");$J=array();$ni=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($R));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name, t.name type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($ni))as$K){$U=$K["type"];$y=(preg_match("~char|binary~",$U)?intval($K["max_length"])/($U[0]=='n'?2:1):($U=="decimal"?"$K[precision],$K[scale]":""));$J[$K["name"]]=array("field"=>$K["name"],"full_type"=>$U.($y?"($y)":""),"type"=>$U,"length"=>$y,"default"=>(preg_match("~^\('(.*)'\)$~",$K["default"],$A)?str_replace("''","'",$A[1]):$K["default"]),"default_constraint"=>$K["default_constraint"],"null"=>$K["is_nullable"],"auto_increment"=>$K["is_identity"],"collation"=>$K["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$K["is_primary_key"],"comment"=>$qb[$K["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($ni))as$K){$J[$K["name"]]["generated"]=($K["is_persisted"]?"PERSISTED":"VIRTUAL");$J[$K["name"]]["default"]=$K["definition"];}return$J;}function
indexes($R,$g=null){$J=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($R),$g)as$K){$B=$K["name"];$J[$B]["type"]=($K["is_primary_key"]?"PRIMARY":($K["is_unique"]?"UNIQUE":"INDEX"));$J[$B]["lengths"]=array();$J[$B]["columns"][$K["key_ordinal"]]=$K["column_name"];$J[$B]["descs"][$K["key_ordinal"]]=($K["is_descending_key"]?'1':null);}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($B))));}function
collations(){$J=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$c)$J[preg_replace('~_.*~','',$c)][]=$c;return$J;}function
information_schema($j){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).(preg_match('~^[a-z0-9_]+$~i',$c)?" COLLATE $c":""));}function
drop_databases($i){return
queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$i)));}function
rename_database($B,$c){if(preg_match('~^[a-z0-9_]+$~i',$c))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $c");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($B));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($R,$B,$n,$id,$ob,$xc,$c,$_a,$E){$b=array();$qb=array();$ag=fields($R);foreach($n
as$m){$d=idf_escape($m[0]);$X=$m[1];if(!$X)$b["DROP"][]=" COLUMN $d";else{$X[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$X[1]);$qb[$m[0]]=$X[5];unset($X[5]);if(preg_match('~ AS ~',$X[3]))unset($X[1],$X[2]);if($m[0]=="")$b["ADD"][]="\n  ".implode("",$X).($R==""?substr($id[$X[0]],16+strlen($X[0])):"");else{$k=$X[3];unset($X[3]);unset($X[6]);if($d!=$X[0])queries("EXEC sp_rename ".q(table($R).".$d").", ".q(idf_unescape($X[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$X)][]="";$Zf=$ag[$m[0]];if(default_value($Zf)!=$k){if($Zf["default"]!==null)$b["DROP"][]=" ".idf_escape($Zf["default_constraint"]);if($k)$b["ADD"][]="\n $k FOR $d";}}}}if($R=="")return
queries("CREATE TABLE ".table($B)." (".implode(",",(array)$b["ADD"])."\n)");if($R!=$B)queries("EXEC sp_rename ".q(table($R)).", ".q($B));if($id)$b[""]=$id;foreach($b
as$x=>$X){if(!queries("ALTER TABLE ".table($B)." $x".implode(",",$X)))return
false;}foreach($qb
as$x=>$X){$ob=substr($X,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($B).", @level2type = N'Column', @level2name = ".q($x));queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $ob,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($B).",
@level2type = N'Column',
@level2name = ".q($x));}return
true;}function
alter_indexes($R,$b){$v=array();$hc=array();foreach($b
as$X){if($X[2]=="DROP"){if($X[0]=="PRIMARY")$hc[]=idf_escape($X[1]);else$v[]=idf_escape($X[1])." ON ".table($R);}elseif(!queries(($X[0]!="PRIMARY"?"CREATE $X[0] ".($X[0]!="INDEX"?"INDEX ":"").idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R):"ALTER TABLE ".table($R)." ADD PRIMARY KEY")." (".implode(", ",$X[2]).")"))return
false;}return(!$v||queries("DROP INDEX ".implode(", ",$v)))&&(!$hc||queries("ALTER TABLE ".table($R)." DROP ".implode(", ",$hc)));}function
found_rows($S,$Z){}function
foreign_keys($R){$J=array();$Kf=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($R).", @fktable_owner = ".q(get_schema()))as$K){$p=&$J[$K["FK_NAME"]];$p["db"]=$K["PKTABLE_QUALIFIER"];$p["ns"]=$K["PKTABLE_OWNER"];$p["table"]=$K["PKTABLE_NAME"];$p["on_update"]=$Kf[$K["UPDATE_RULE"]];$p["on_delete"]=$Kf[$K["DELETE_RULE"]];$p["source"][]=$K["FKCOLUMN_NAME"];$p["target"][]=$K["PKCOLUMN_NAME"];}return$J;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($_j){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$_j)));}function
drop_tables($T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables($T,$_j,$xi){return
apply_queries("ALTER SCHEMA ".idf_escape($xi)." TRANSFER",array_merge($T,$_j));}function
trigger($B,$R){if($B=="")return
array();$L=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($B));$J=reset($L);if($J)$J["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$J["text"]);return$J;}function
triggers($R){$J=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($R))as$K)$J[$K["name"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($vh){$_GET["ns"]=$vh;return
true;}function
create_sql($R,$_a,$gi){if(is_view(table_status1($R))){$zj=view($R);return"CREATE VIEW ".table($R)." AS $zj[select]";}$n=array();$G=false;foreach(fields($R)as$B=>$m){$X=process_field($m,$m);if($X[6])$G=true;$n[]=implode("",$X);}foreach(indexes($R)as$B=>$v){if(!$G||$v["type"]!="PRIMARY"){$e=array();foreach($v["columns"]as$x=>$X)$e[]=idf_escape($X).($v["descs"][$x]?" DESC":"");$B=idf_escape($B);$n[]=($v["type"]=="INDEX"?"INDEX $B":"CONSTRAINT $B ".($v["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($R)as$B=>$Wa)$n[]="CONSTRAINT ".idf_escape($B)." CHECK ($Wa)";return"CREATE TABLE ".table($R)." (\n\t".implode(",\n\t",$n)."\n)";}function
foreign_keys_sql($R){$n=array();foreach(foreign_keys($R)as$id)$n[]=ltrim(format_foreign_key($id));return($n?"ALTER TABLE ".table($R)." ADD\n\t".implode(",\n\t",$n).";\n\n":"");}function
truncate_sql($R){return"TRUNCATE TABLE ".table($R);}function
use_sql($Nb){return"USE ".idf_escape($Nb);}function
trigger_sql($R){$J="";foreach(triggers($R)as$B=>$Vi)$J
.=create_trigger(" ON ".table($R),trigger($B,$R)).";";return$J;}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Uc){return
preg_match('~^(check|comment|columns|database|drop_col|dump|indexes|descidx|scheme|sql|table|trigger|view|view_trigger)$~',$Uc);}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.2.2-dev")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($h=false){return
password_file($h);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($gd=true){return
get_databases($gd);}function
pluginsLinks(){}function
operators(){return
driver()->operators;}function
schemas(){return
schemas();}function
queryTimeout(){return
2;}function
headers(){}function
csp(array$Gb){return$Gb;}function
head($Kb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$kf){$o="adminer$kf.css";if(file_exists($o))$J[]="$o?v=".crc32(file_get_contents($o));}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.'System'.'<td>',html_select("auth[driver]",SqlDriver::$drivers,DRIVER,"loginDriver(this);")),adminer()->loginFormField('server','<tr><th>'.'Server'.'<td>','<input name="auth[server]" value="'.h(SERVER).'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'),adminer()->loginFormField('username','<tr><th>'.'Username'.'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'.script("const authDriver = qs('#username').form['auth[driver]']; authDriver && authDriver.onchange();")),adminer()->loginFormField('password','<tr><th>'.'Password'.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.'Database'.'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
loginFormField($B,$Gd,$Y){return$Gd.$Y."\n";}function
login($Me,$F){if($F=="")return
sprintf('Adminer does not support accessing a database without a password, <a href="https://www.adminer.org/en/password/"%s>more information</a>.',target_blank());return
true;}function
tableName(array$mi){return
h($mi["Name"]);}function
fieldName(array$m,$Tf=0){$U=$m["full_type"];$ob=$m["comment"];return'<span title="'.h($U.($ob!=""?($U?": ":"").$ob:'')).'">'.h($m["field"]).'</span>';}function
selectLinks(array$mi,$O=""){$B=$mi["Name"];echo'<p class="links">';$Ke=array("select"=>'Select data');if(support("table")||support("indexes"))$Ke["table"]='Show structure';$re=false;if(support("table")){$re=is_view($mi);if($re)$Ke["view"]='Alter view';else$Ke["create"]='Alter table';}if($O!==null)$Ke["edit"]='New item';foreach($Ke
as$x=>$X)echo" <a href='".h(ME)."$x=".urlencode($B).($x=="edit"?$O:"")."'".bold(isset($_GET[$x])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($B,$re)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$li){return
array();}function
backwardKeysPrint(array$Da,array$K){}function
selectQuery($H,$ci,$Sc=false){$J="</p>\n";if(!$Sc&&($Cj=driver()->warnings())){$t="warnings";$J=", <a href='#$t'>".'Warnings'."</a>".script("qsl('a').onclick = partial(toggle, '$t');","")."$J<div id='$t' class='hidden'>\n$Cj</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($ci).")</span>".(support("sql")?" <a href='".h(ME)."sql=".urlencode($H)."'>".'Edit'."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$jd){return$L;}function
selectLink($X,array$m){}function
selectVal($X,$_,array$m,$dg){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$m["type"])&&!preg_match("~var~",$m["type"])?"<code>$X</code>":(preg_match('~json~',$m["type"])?"<code class='jush-js'>$X</code>":$X)));if(preg_match('~blob|bytea|raw|file~',$m["type"])&&!is_utf8($X))$J="<i>".lang_format(array('%d byte','%d bytes'),strlen($dg))."</i>";return($_?"<a href='".h($_)."'".(is_url($_)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$m){return$X;}function
config(){return
array();}function
tableStructurePrint(array$n,$mi=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".'Column'."<td>".'Type'.(support("comment")?"<td>".'Comment':"")."</thead>\n";$fi=driver()->structuredTypes();foreach($n
as$m){echo"<tr><th>".h($m["field"]);$U=h($m["full_type"]);$c=h($m["collation"]);echo"<td><span title='$c'>".(in_array($U,(array)$fi['User types'])?"<a href='".h(ME.'type='.urlencode($U))."'>$U</a>":$U.($c&&isset($mi["Collation"])&&$c!=$mi["Collation"]?" $c":""))."</span>",($m["null"]?" <i>NULL</i>":""),($m["auto_increment"]?" <i>".'Auto Increment'."</i>":"");$k=h($m["default"]);echo(isset($m["default"])?" <span title='".'Default value'."'>[<b>".($m["generated"]?"<code class='jush-".JUSH."'>$k</code>":$k)."</b>]</span>":""),(support("comment")?"<td>".h($m["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$w){echo"<table>\n";foreach($w
as$B=>$v){ksort($v["columns"]);$Lg=array();foreach($v["columns"]as$x=>$X)$Lg[]="<i>".h($X)."</i>".($v["lengths"][$x]?"(".$v["lengths"][$x].")":"").($v["descs"][$x]?" DESC":"");echo"<tr title='".h($B)."'><th>$v[type]<td>".implode(", ",$Lg)."\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$e){print_fieldset("select",'Select',$M);$s=0;$M[""]=array();foreach($M
as$x=>$X){$X=idx($_GET["columns"],$x,array());$d=select_input(" name='columns[$s][col]'",$e,$X["col"],($x!==""?"selectFieldChange":"selectAddRow"));echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$s][fun]",array(-1=>"")+array_filter(array('Functions'=>driver()->functions,'Aggregation'=>driver()->grouping)),$X["fun"]).on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'",1).script("qsl('select').onchange = function () { helpClose();".($x!==""?"":" qsl('select, input', this.parentNode).onchange();")." };","")."($d)":$d)."</div>\n";$s++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$w){print_fieldset("search",'Search',$Z);foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$v["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$s]' value='".h(idx($_GET["fulltext"],$s))."'>",script("qsl('input').oninput = selectFieldChange;",""),checkbox("boolean[$s]",1,isset($_GET["boolean"][$s]),"BOOL"),"</div>\n";}$Ta="this.parentNode.firstChild.onchange();";foreach(array_merge((array)$_GET["where"],array(array()))as$s=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],adminer()->operators())))echo"<div>".select_input(" name='where[$s][col]'",$e,$X["col"],($X?"selectFieldChange":"selectAddRow"),"(".'anywhere'.")"),html_select("where[$s][op]",adminer()->operators(),$X["op"],$Ta),"<input type='search' name='where[$s][val]' value='".h($X["val"])."'>",script("mixin(qsl('input'), {oninput: function () { $Ta }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});",""),"</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$Tf,array$e,array$w){print_fieldset("sort",'Sort',$Tf);$s=0;foreach((array)$_GET["order"]as$x=>$X){if($X!=""){echo"<div>".select_input(" name='order[$s]'",$e,$X,"selectFieldChange"),checkbox("desc[$s]",1,isset($_GET["desc"][$x]),'descending')."</div>\n";$s++;}}echo"<div>".select_input(" name='order[$s]'",$e,"","selectAddRow"),checkbox("desc[$s]",1,false,'descending')."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($z){echo"<fieldset><legend>".'Limit'."</legend><div>","<input type='number' name='limit' class='size' value='".intval($z)."'>",script("qsl('input').oninput = selectFieldChange;",""),"</div></fieldset>\n";}function
selectLengthPrint($Ci){if($Ci!==null)echo"<fieldset><legend>".'Text length'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Ci)."'>","</div></fieldset>\n";}function
selectActionPrint(array$w){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>","<script".nonce().">\n","const indexColumns = ";$e=array();foreach($w
as$v){$Jb=reset($v["columns"]);if($v["type"]!="FULLTEXT"&&$Jb)$e[$Jb]=1;}$e[""]=1;foreach($e
as$x=>$X)json_row($x);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$uc,array$e){}function
selectColumnsProcess(array$e,array$w){$M=array();$vd=array();foreach((array)$_GET["columns"]as$x=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$x]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$vd[]=$M[$x];}}return
array($M,$vd);}function
selectSearchProcess(array$n,array$w){$J=array();foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$s)!="")$J[]="MATCH (".implode(", ",array_map('Adminer\idf_escape',$v["columns"])).") AGAINST (".q($_GET["fulltext"][$s]).(isset($_GET["boolean"][$s])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$x=>$X){$hb=$X["col"];if("$hb$X[val]"!=""&&in_array($X["op"],adminer()->operators())){$sb=array();foreach(($hb!=""?array($hb=>$n[$hb]):$n)as$B=>$m){$Hg="";$rb=" $X[op]";if(preg_match('~IN$~',$X["op"])){$Ud=process_length($X["val"]);$rb
.=" ".($Ud!=""?$Ud:"(NULL)");}elseif($X["op"]=="SQL")$rb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$A))$rb=" $A[1] ".adminer()->processInput($m,"%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$Hg="$X[op](".q($X["val"]).", ";$rb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$rb
.=" ".adminer()->processInput($m,$X["val"]);if($hb!=""||(isset($m["privileges"]["where"])&&(preg_match('~^[-\d.'.(preg_match('~IN$~',$X["op"])?',':'').']+$~',$X["val"])||!preg_match('~'.number_type().'|bit~',$m["type"]))&&(!preg_match("~[\x80-\xFF]~",$X["val"])||preg_match('~char|text|enum|set~',$m["type"]))&&(!preg_match('~date|timestamp~',$m["type"])||preg_match('~^\d+-\d+-\d+~',$X["val"]))))$sb[]=$Hg.driver()->convertSearch(idf_escape($B),$X,$m).$rb;}$J[]=(count($sb)==1?$sb[0]:($sb?"(".implode(" OR ",$sb).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$n,array$w){$J=array();foreach((array)$_GET["order"]as$x=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$x])?" DESC":"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$jd){return
false;}function
selectQueryBuild(array$M,array$Z,array$vd,array$Tf,$z,$D){return"";}function
messageQuery($H,$Di,$Sc=false){restart_session();$Id=&get_session("queries");if(!idx($Id,$_GET["db"]))$Id[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$Id[$_GET["db"]][]=array($H,time(),$Di);$Yh="sql-".count($Id[$_GET["db"]]);$J="<a href='#$Yh' class='toggle'>".'SQL command'."</a>\n";if(!$Sc&&($Cj=driver()->warnings())){$t="warnings-".count($Id[$_GET["db"]]);$J="<a href='#$t' class='toggle'>".'Warnings'."</a>, $J<div id='$t' class='hidden'>\n$Cj</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Yh' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1000)."</code></pre>".($Di?" <span class='time'>($Di)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($Id[$_GET["db"]])-1)).'">'.'Edit'.'</a>':'').'</div>';}function
editRowPrint($R,array$n,$K,$kj){}function
editFunctions(array$m){$J=($m["null"]?"NULL/":"");$kj=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$x=>$qd){if(!$x||(!isset($_GET["call"])&&$kj)){foreach($qd
as$wg=>$X){if(!$wg||preg_match("~$wg~",$m["type"]))$J
.="/$X";}}if($x&&$qd&&!preg_match('~set|blob|bytea|raw|file|bool~',$m["type"]))$J
.="/SQL";}if($m["auto_increment"]&&!$kj)$J='Auto Increment';return
explode("/",$J);}function
editInput($R,array$m,$ya,$Y){if($m["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$ya value='-1' checked><i>".'original'."</i></label> ":"").($m["null"]?"<label><input type='radio'$ya value=''".($Y!==null||isset($_GET["select"])?"":" checked")."><i>NULL</i></label> ":"").enum_input("radio",$ya,$m,$Y,$Y===0?0:null);return"";}function
editHint($R,array$m,$Y){return"";}function
processInput(array$m,$Y,$r=""){if($r=="SQL")return$Y;$B=$m["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$r))$J="$r()";elseif(preg_match('~^current_(date|timestamp)$~',$r))$J=$r;elseif(preg_match('~^([+-]|\|\|)$~',$r))$J=idf_escape($B)." $r $J";elseif(preg_match('~^[+-] interval$~',$r))$J=idf_escape($B)." $r ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$r))$J="$r(".idf_escape($B).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$r))$J="$r($J)";return
unconvert_field($m,$J);}function
dumpOutput(){$J=array('text'=>'open','file'=>'save');if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($j){}function
dumpTable($R,$gi,$re=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($gi)dump_csv(array_keys(fields($R)));}else{if($re==2){$n=array();foreach(fields($R)as$B=>$m)$n[]=idf_escape($B)." $m[full_type]";$h="CREATE TABLE ".table($R)." (".implode(", ",$n).")";}else$h=create_sql($R,$_POST["auto_increment"],$gi);set_utf8mb4($h);if($gi&&$h){if($gi=="DROP+CREATE"||$re==1)echo"DROP ".($re==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($re==1)$h=remove_definer($h);echo"$h;\n\n";}}}function
dumpData($R,$gi,$H){if($gi){$Ue=(JUSH=="sqlite"?0:1048576);$n=array();$Rd=false;if($_POST["format"]=="sql"){if($gi=="TRUNCATE+INSERT")echo
truncate_sql($R).";\n";$n=fields($R);if(JUSH=="mssql"){foreach($n
as$m){if($m["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Rd=true;break;}}}}$I=connection()->query($H,1);if($I){$he="";$Na="";$we=array();$rd=array();$ii="";$Vc=($R!=''?'fetch_assoc':'fetch_row');$Cb=0;while($K=$I->$Vc()){if(!$we){$vj=array();foreach($K
as$X){$m=$I->fetch_field();if(idx($n[$m->name],'generated')){$rd[$m->name]=true;continue;}$we[]=$m->name;$x=idf_escape($m->name);$vj[]="$x = VALUES($x)";}$ii=($gi=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$vj):"").";\n";}if($_POST["format"]!="sql"){if($gi=="table"){dump_csv($we);$gi="INSERT";}dump_csv($K);}else{if(!$he)$he="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$we)).") VALUES";foreach($K
as$x=>$X){if($rd[$x]){unset($K[$x]);continue;}$m=$n[$x];$K[$x]=($X!==null?unconvert_field($m,preg_match(number_type(),$m["type"])&&!preg_match('~\[~',$m["full_type"])&&is_numeric($X)?$X:q(($X===false?0:$X))):"NULL");}$th=($Ue?"\n":" ")."(".implode(",\t",$K).")";if(!$Na)$Na=$he.$th;elseif(JUSH=='mssql'?$Cb%1000!=0:strlen($Na)+4+strlen($th)+strlen($ii)<$Ue)$Na
.=",$th";else{echo$Na.$ii;$Na=$he.$th;}}$Cb++;}if($Na)echo$Na.$ii;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($Rd)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($Pd){return
friendly_url($Pd!=""?$Pd:(SERVER!=""?SERVER:"localhost"));}function
dumpHeaders($Pd,$mf=false){$gg=$_POST["output"];$Nc=(preg_match('~sql~',$_POST["format"])?"sql":($mf?"tar":"csv"));header("Content-Type: ".($gg=="gz"?"application/x-gzip":($Nc=="tar"?"application/x-tar":($Nc=="sql"||$gg!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($gg=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$Nc;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.'Alter database'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'Alter schema':'Create schema')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'Privileges'."</a>\n":"");return
true;}function
navigation($jf){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$uf=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$uf)<0?h($uf):"")."</a>","</span></h1>\n";if($jf=="auth"){$gg="";foreach((array)$_SESSION["pwds"]as$xj=>$Kh){foreach($Kh
as$N=>$sj){$B=h(get_setting("vendor-$xj-$N")?:get_driver($xj));foreach($sj
as$V=>$F){if($F!==null){$Qb=$_SESSION["db"][$xj][$N][$V];foreach(($Qb?array_keys($Qb):array(""))as$j)$gg
.="<li><a href='".h(auth_url($xj,$N,$V,$j))."'>($B) ".h($V.($N!=""?"@".adminer()->serverName($N):"").($j!=""?" - $j":""))."</a>\n";}}}}if($gg)echo"<ul id='logins'>\n$gg</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");}else{$T=array();if($_GET["ns"]!==""&&!$jf&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($jf);$ia=array();if(DB==""||!$jf){if(support("sql")){$ia[]="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".'SQL command'."</a>";$ia[]="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".'Import'."</a>";}$ia[]="<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'Export'."</a>";}$Vd=$_GET["ns"]!==""&&!$jf&&DB!="";if($Vd)$ia[]='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'Create table'."</a>";echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($Vd){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".'No tables.'."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=5.2.2-dev",true);if(support("sql")){echo"<script".nonce().">\n";if($T){$Ke=array();foreach($T
as$R=>$U)$Ke[]=preg_quote($R,'/');echo"var jushLinks = { ".JUSH.": [ '".js_escape(ME).(support("table")?"table=":"select=")."\$&', /\\b(".implode("|",$Ke).")\\b/g ] };\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(isset($_GET["sql"])||isset($_GET["trigger"])||isset($_GET["check"])){$ti=array_fill_keys(array_keys($T),array());foreach(driver()->allFields()as$R=>$n){foreach($n
as$m)$ti[$R][]=$m["field"];}echo"addEventListener('DOMContentLoaded', () => { autocompleter = jush.autocompleteSql('".idf_escape("")."', ".json_encode($ti)."); });\n";}}echo"</script>\n";}echo
script("syntaxHighlighting('".preg_replace('~^(\d\.?\d).*~s','\1',connection()->server_info)."', '".connection()->flavor."');");}function
databasesPrint($jf){$i=adminer()->databases();if(DB&&$i&&!in_array(DB,$i))array_unshift($i,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Ob=script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");echo"<label title='".'Database'."'>".'DB'.": ".($i?html_select("db",array(""=>"")+$i,DB).$Ob:"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".'Use'."'".($i?" class='hidden'":"").">\n";if(support("scheme")){if($jf!="db"&&DB!=""&&connection()->select_db(DB)){echo"<br><label>".'Schema'.": ".html_select("ns",array(""=>"")+adminer()->schemas(),$_GET["ns"])."$Ob</label>";if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
tablesPrint(array$T){echo"<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($T
as$R=>$P){$R="$R";$B=adminer()->tableName($P);if($B!=""&&!$P["inherited"])echo'<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select")." title='".'Select data'."'>".'select'."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($P)?"view":"structure"))." title='".'Show structure'."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$error='';private$hooks=array();function
__construct($Ag){if($Ag===null){$Ag=array();$Ha="adminer-plugins";if(is_dir($Ha)){foreach(glob("$Ha/*.php")as$o)$Wd=include_once"./$o";}$Hd=" href='https://www.adminer.org/plugins/#use'".target_blank();if(file_exists("$Ha.php")){$Wd=include_once"./$Ha.php";if(is_array($Wd)){foreach($Wd
as$_g)$Ag[get_class($_g)]=$_g;}else$this->error
.=sprintf('%s must <a%s>return an array</a>.',"<b>$Ha.php</b>",$Hd)."<br>";}foreach(get_declared_classes()as$db){if(!$Ag[$db]&&preg_match('~^Adminer\w~i',$db)){$dh=new
\ReflectionClass($db);$xb=$dh->getConstructor();if($xb&&$xb->getNumberOfRequiredParameters())$this->error
.=sprintf('<a%s>Configure</a> %s in %s.',$Hd,"<b>$db</b>","<b>$Ha.php</b>")."<br>";else$Ag[$db]=new$db;}}}$this->plugins=$Ag;$la=new
Adminer;$Ag[]=$la;$dh=new
\ReflectionObject($la);foreach($dh->getMethods()as$hf){foreach($Ag
as$_g){$B=$hf->getName();if(method_exists($_g,$B))$this->hooks[$B][]=$_g;}}}function
__call($B,array$lg){$ua=array();foreach($lg
as$x=>$X)$ua[]=&$lg[$x];$J=null;foreach($this->hooks[$B]as$_g){$Y=call_user_func_array(array($_g,$B),$ua);if($Y!==null){if(!self::$append[$B])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($u,$_f=null){$ua=func_get_args();$ua[0]=idx($this->translations[LANG],$u)?:$u;return
call_user_func_array('Adminer\lang_format',$ua);}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($N,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);list($Ld,$Bg)=explode(":",$N,2);$bi=adminer()->connectSsl();if($bi)$this->ssl_set($bi['key'],$bi['cert'],$bi['ca'],'','');$J=@$this->real_connect(($N!=""?$Ld:ini_get("mysqli.default_host")),($N.$V!=""?$V:ini_get("mysqli.default_user")),($N.$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,(is_numeric($Bg)?intval($Bg):ini_get("mysqli.default_port")),(is_numeric($Bg)?$Bg:null),($bi?($bi['verify']!==false?2048:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,false);return($J?'':$this->error);}function
set_charset($Va){if(parent::set_charset($Va))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Va");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($N,$V,$F){if(ini_bool("mysql.allow_local_infile"))return
sprintf('Disable %s or enable %s or %s extensions.',"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),("$N$V"!=""?$V:ini_get("mysql.default_user")),("$N$V$F"!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Va){if(function_exists('mysql_set_charset')){if(mysql_set_charset($Va,$this->link))return
true;mysql_set_charset('utf8',$this->link);}return$this->query("SET NAMES $Va");}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Nb){return
mysql_select_db($Nb,$this->link);}function
query($H,$cj=false){$I=@($cj?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($I===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($I);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=mysql_num_rows($I);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$J=mysql_fetch_field($this->result,$this->offset++);$J->orgtable=$J->table;$J->charsetnr=($J->blob?63:0);return$J;}function
__destruct(){mysql_free_result($this->result);}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach($N,$V,$F){$Rf=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);$bi=adminer()->connectSsl();if($bi){if($bi['key'])$Rf[\PDO::MYSQL_ATTR_SSL_KEY]=$bi['key'];if($bi['cert'])$Rf[\PDO::MYSQL_ATTR_SSL_CERT]=$bi['cert'];if($bi['ca'])$Rf[\PDO::MYSQL_ATTR_SSL_CA]=$bi['ca'];if(isset($bi['verify']))$Rf[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$bi['verify'];}return$this->dsn("mysql:charset=utf8;host=".str_replace(":",";unix_socket=",preg_replace('~:(\d)~',';port=\1',$N)),$V,$F,$Rf);}function
set_charset($Va){return$this->query("SET NAMES $Va");}function
select_db($Nb){return$this->query("USE ".idf_escape($Nb));}function
query($H,$cj=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$cj);return
parent::query($H,$cj);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($N,$V,$F){$f=parent::connect($N,$V,$F);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($th=iconv("windows-1250","utf-8",$f))>strlen($f))$f=$th;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'Date and time'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'Strings'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'Lists'=>array("enum"=>65535,"set"=>64),'Binary'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'Geometry'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types['Strings']["json"]=4294967295;if(min_version('',10.7,$f)){$this->types['Strings']["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version(9,'',$f)){$this->types['Numbers']["vector"]=16383;$this->insertFunctions['vector']='string_to_vector';}if(min_version(5.1,'',$f))$this->partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$m){return(preg_match("~binary~",$m["type"])?"<code class='jush-sql'>UNHEX</code>":($m["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):(preg_match("~geometry|point|linestring|polygon~",$m["type"])?"<code class='jush-sql'>GeomFromText</code>":"")));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$G){$e=array_keys(reset($L));$Hg="INSERT INTO ".table($R)." (".implode(", ",$e).") VALUES\n";$vj=array();foreach($e
as$x)$vj[$x]="$x = VALUES($x)";$ii="\nON DUPLICATE KEY UPDATE ".implode(", ",$vj);$vj=array();$y=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($vj&&(strlen($Hg)+$y+strlen($Y)+strlen($ii)>1e6)){if(!queries($Hg.implode(",\n",$vj).$ii))return
false;$vj=array();$y=0;}$vj[]=$Y;$y+=strlen($Y)+2;}return
queries($Hg.implode(",\n",$vj).$ii);}function
slowQuery($H,$Ei){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Ei FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($Ei*1000).") */ $A[2]";}}function
convertSearch($u,array$X,array$m){return(preg_match('~char|text|enum|set~',$m["type"])&&!preg_match("~^utf8~",$m["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($u USING ".charset($this->conn).")":$u);}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($B,$re=false){$Oe=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower("information-schema-".($Oe?"$B-table/":str_replace("_","-",$B)."-table.html"));if(DB=="mysql")return($Oe?"mysql$B-table/":"system-schema.html");}function
partitionsInfo($R){$od="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=connection()->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $od ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$I->fetch_row();$sg=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $od AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($sg);$J["partition_values"]=array_values($sg);return$J;}function
hasCStyleEscapes(){static$Qa;if($Qa===null){$Zh=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Qa=(strpos($Zh,'NO_BACKSLASH_ESCAPES')===false);}return$Qa;}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}}function
idf_escape($u){return"`".str_replace("`","``",$u)."`";}function
table($u){return
idf_escape($u);}function
get_databases($gd){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$J=($gd?slow_query($H):get_vals($H));restart_session();set_session("dbs",$J);stop_session();}return$J;}function
limit($H,$Z,$z,$C=0,$Fh=" "){return" $H$Z".($z?$Fh."LIMIT $z".($C?" OFFSET $C":""):"");}function
limit1($R,$H,$Z,$Fh="\n"){return
limit($H,$Z,1,0,$Fh);}function
db_collation($j,array$jb){$J=null;$h=get_val("SHOW CREATE DATABASE ".idf_escape($j),1);if(preg_match('~ COLLATE ([^ ]+)~',$h,$A))$J=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$h,$A))$J=$jb[$A[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$i){$J=array();foreach($i
as$j)$J[$j]=count(get_vals("SHOW TABLES IN ".idf_escape($j)));return$J;}function
table_status($B="",$Tc=false){$J=array();foreach(get_rows($Tc?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($B!=""?"AND TABLE_NAME = ".q($B):"ORDER BY Name"):"SHOW TABLE STATUS".($B!=""?" LIKE ".q(addcslashes($B,"%_\\")):""))as$K){if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($B!="")$K["Name"]=$B;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
fields($R){$Oe=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$m=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$sd=$K["GENERATION_EXPRESSION"];$Qc=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Qc,$rd);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$U,$Re);$k=$K["COLUMN_DEFAULT"];if($k!=""){$qe=preg_match('~text|json~',$Re[1]);if(!$Oe&&$qe)$k=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($k));if($Oe||$qe){$k=($k=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($A){return
stripslashes(str_replace("''","'",$A[1]));},$k));}if(!$Oe&&preg_match('~binary~',$Re[1])&&preg_match('~^0x(\w*)$~',$k,$A))$k=pack("H*",$A[1]);}$J[$m]=array("field"=>$m,"full_type"=>$U,"type"=>$Re[1],"length"=>$Re[2],"unsigned"=>ltrim($Re[3].$Re[4]),"default"=>($rd?($Oe?$sd:stripslashes($sd)):$k),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($Qc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Qc,$A)?$A[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($rd[1]=="PERSISTENT"?"STORED":$rd[1]),);}return$J;}function
indexes($R,$g=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$g)as$K){$B=$K["Key_name"];$J[$B]["type"]=($B=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?($K["Index_type"]=="SPATIAL"?"SPATIAL":"INDEX"):"UNIQUE")));$J[$B]["columns"][]=$K["Column_name"];$J[$B]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$B]["descs"][]=null;}return$J;}function
foreign_keys($R){static$wg='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Db=get_val("SHOW CREATE TABLE ".table($R),1);if($Db){preg_match_all("~CONSTRAINT ($wg) FOREIGN KEY ?\\(((?:$wg,? ?)+)\\) REFERENCES ($wg)(?:\\.($wg))? \\(((?:$wg,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Db,$Se,PREG_SET_ORDER);foreach($Se
as$A){preg_match_all("~$wg~",$A[2],$Th);preg_match_all("~$wg~",$A[5],$xi);$J[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$Th[0]),"target"=>array_map('Adminer\idf_unescape',$xi[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($B),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$x=>$X)sort($J[$x]);return$J;}function
information_schema($j){return($j=="information_schema")||(min_version(5.5)&&$j=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).($c?" COLLATE ".q($c):""));}function
drop_databases(array$i){$J=apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($B,$c){$J=false;if(create_database($B,$c)){$T=array();$_j=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$_j[]=$R;else$T[]=$R;}$J=(!$T&&!$_j)||move_tables($T,$_j,$B);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Aa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$v){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$v["columns"],true)){$Aa="";break;}if($v["type"]=="PRIMARY")$Aa=" UNIQUE";}}return" AUTO_INCREMENT$Aa";}function
alter_table($R,$B,array$n,array$id,$ob,$xc,$c,$_a,$E){$b=array();foreach($n
as$m){if($m[1]){$k=$m[1][3];if(preg_match('~ GENERATED~',$k)){$m[1][3]=(connection()->flavor=='maria'?"":$m[1][2]);$m[1][2]=$k;}$b[]=($R!=""?($m[0]!=""?"CHANGE ".idf_escape($m[0]):"ADD"):" ")." ".implode($m[1]).($R!=""?$m[2]:"");}else$b[]="DROP ".idf_escape($m[0]);}$b=array_merge($b,$id);$P=($ob!==null?" COMMENT=".q($ob):"").($xc?" ENGINE=".q($xc):"").($c?" COLLATE ".q($c):"").($_a!=""?" AUTO_INCREMENT=$_a":"");if($E){$sg=array();if($E["partition_by"]=='RANGE'||$E["partition_by"]=='LIST'){foreach($E["partition_names"]as$x=>$X){$Y=$E["partition_values"][$x];$sg[]="\n  PARTITION ".idf_escape($X)." VALUES ".($E["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $E[partition_by]($E[partition])";if($sg)$P
.=" (".implode(",",$sg)."\n)";elseif($E["partitions"])$P
.=" PARTITIONS ".(+$E["partitions"]);}elseif($E===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)$P");if($R!=$B)$b[]="RENAME TO ".table($B);if($P)$b[]=ltrim($P);return($b?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$b)):true);}function
alter_indexes($R,$b){$Ua=array();foreach($b
as$X)$Ua[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Ua));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$_j){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$_j)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$_j,$xi){$hh=array();foreach($T
as$R)$hh[]=table($R)." TO ".idf_escape($xi).".".table($R);if(!$hh||queries("RENAME TABLE ".implode(", ",$hh))){$Vb=array();foreach($_j
as$R)$Vb[table($R)]=view($R);connection()->select_db($xi);$j=idf_escape(DB);foreach($Vb
as$B=>$zj){if(!queries("CREATE VIEW $B AS ".str_replace(" $j."," ",$zj["select"]))||!queries("DROP VIEW $j.$B"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$_j,$xi){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$B=($xi==DB?table("copy_$R"):idf_escape($xi).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $B"))||!queries("CREATE TABLE $B LIKE ".table($R))||!queries("INSERT INTO $B SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$Vi=$K["Trigger"];if(!queries("CREATE TRIGGER ".($xi==DB?idf_escape("copy_$Vi"):idf_escape($xi).".".idf_escape($Vi))." $K[Timing] $K[Event] ON $B FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($_j
as$R){$B=($xi==DB?table("copy_$R"):idf_escape($xi).".".table($R));$zj=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $B"))||!queries("CREATE VIEW $B AS $zj[select]"))return
false;}return
true;}function
trigger($B,$R){if($B=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($B));return
reset($L);}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K)$J[$K["Trigger"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($B,$U){$ra=array("bool","boolean","integer","double precision","real","dec","numeric","fixed","national char","national varchar");$Uh="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";$zc=driver()->enumLength;$aj="((".implode("|",array_merge(array_keys(driver()->types()),$ra)).")\\b(?:\\s*\\(((?:[^'\")]|$zc)++)\\))?"."\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";$wg="$Uh*(".($U=="FUNCTION"?"":driver()->inout).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$aj";$h=get_val("SHOW CREATE $U ".idf_escape($B),2);preg_match("~\\(((?:$wg\\s*,?)*)\\)\\s*".($U=="FUNCTION"?"RETURNS\\s+$aj\\s+":"")."(.*)~is",$h,$A);$n=array();preg_match_all("~$wg\\s*,?~is",$A[1],$Se,PREG_SET_ORDER);foreach($Se
as$kg)$n[]=array("field"=>str_replace("``","`",$kg[2]).$kg[3],"type"=>strtolower($kg[5]),"length"=>preg_replace_callback("~$zc~s",'Adminer\normalize_enum',$kg[6]),"unsigned"=>strtolower(preg_replace('~\s+~',' ',trim("$kg[8] $kg[7]"))),"null"=>true,"full_type"=>$kg[4],"inout"=>strtoupper($kg[1]),"collation"=>strtolower($kg[9]),);return
array("fields"=>$n,"comment"=>get_val("SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ".q($B)),)+($U!="FUNCTION"?array("definition"=>$A[11]):array("returns"=>array("type"=>$A[12],"length"=>$A[13],"unsigned"=>$A[15],"collation"=>$A[16]),"definition"=>$A[17],"language"=>"SQL",));}function
routines(){return
get_rows("SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($B,array$K){return
idf_escape($B);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$H){return$f->query("EXPLAIN ".(min_version(5.1)&&!min_version(5.7)?"PARTITIONS ":"").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$_a,$gi){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$_a)$J=preg_replace('~ AUTO_INCREMENT=\d+~','',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Nb){return"USE ".idf_escape($Nb);}function
trigger_sql($R){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$K)$J
.="\nCREATE TRIGGER ".idf_escape($K["Trigger"])." $K[Timing] $K[Event] ON ".table($K["Table"])." FOR EACH ROW\n$K[Statement];;\n";return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$m){if(preg_match("~binary~",$m["type"]))return"HEX(".idf_escape($m["field"]).")";if($m["type"]=="bit")return"BIN(".idf_escape($m["field"])." + 0)";if(preg_match("~geometry|point|linestring|polygon~",$m["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($m["field"]).")";}function
unconvert_field(array$m,$J){if(preg_match("~binary~",$m["type"]))$J="UNHEX($J)";if($m["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if(preg_match("~geometry|point|linestring|polygon~",$m["type"])){$Hg=(min_version(8)?"ST_":"");$J=$Hg."GeomFromText($J, $Hg"."SRID($m[field]))";}return$J;}function
support($Uc){return!preg_match("~scheme|sequence|type|view_trigger|materializedview".(min_version(8)?"":"|descidx".(min_version(5.1)?"":"|event")).(min_version('8.0.16','10.2.1')?"":"|check")."~",$Uc);}function
kill_process($X){return
queries("KILL ".number($X));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types(){return
array();}function
type_values($t){return"";}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($vh,$g=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',$_GET[DRIVER]);define('Adminer\DB',$_GET["db"]);define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));function
page_header($Gi,$l="",$Ma=array(),$Hi=""){page_headers();if(is_ajax()&&$l){page_messages($l);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Ii=$Gi.($Hi!=""?": $Hi":"");$Ji=strip_tags($Ii.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Ji,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=5.2.2-dev"),'">
';$Hb=adminer()->css();$Dd=false;$Bd=false;foreach($Hb
as$mj){if(strpos($mj,"adminer.css")!==false){$Dd=true;$o=preg_replace('~\?.*~','',$mj);if(!preg_match('~:~',$mj)&&is_readable($o)&&preg_match('~prefers-color-scheme:\s*dark~',file_get_contents($o)))$Bd=true;}if(strpos($mj,"adminer-dark.css")!==false)$Bd=true;}$Kb=($Dd?($Bd?null:false):($Bd?:null));$af=" media='(prefers-color-scheme: dark)'";if($Kb!==false)echo"<link rel='stylesheet'".($Kb?"":$af)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=5.2.2-dev")."'>\n";echo"<meta name='color-scheme' content='".($Kb===null?"light dark":($Kb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=5.2.2-dev");if(adminer()->head($Kb))echo"<link rel='icon' href='data:image/gif;base64,R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.2.2-dev")."'>\n";foreach($Hb
as$X)echo"<link rel='stylesheet'".(preg_match('~-dark\.~',$X)&&!$Kb?$af:"")." href='".h($X)."'>\n";echo"\n<body class='".'ltr'." nojs";adminer()->bodyClass();echo"'>\n";$o=get_temp_dir()."/adminer.version";if(!$_COOKIE["adminer_version"]&&function_exists('openssl_verify')&&file_exists($o)&&filemtime($o)+86400>time()){$yj=unserialize(file_get_contents($o));$Rg="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";if(openssl_verify($yj["version"],base64_decode($yj["signature"]),$Rg)==1)$_COOKIE["adminer_version"]=$yj["version"];}echo
script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick".(isset($_COOKIE["adminer_version"])?"":", onload: partial(verifyVersion, '".VERSION."', '".js_escape(ME)."', '".get_token()."')")."});
document.body.classList.replace('nojs', 'js');
const offlineMessage = '".js_escape('You are offline.')."';
const thousandsSeparator = '".js_escape(',')."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ma!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> » ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:'Server');if($Ma===false)echo"$N\n";else{echo"<a href='".h($_)."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ma)))echo'<a href="'.h($_."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> » ';if(is_array($Ma)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ma
as$x=>$X){$Xb=(is_array($X)?$X[1]:h($X));if($Xb!="")echo"<a href='".h(ME."$x=").urlencode(is_array($X)?$X[0]:$X)."'>$Xb</a> » ";}}echo"$Gi\n";}}echo"<h2>$Ii</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($l);$i=&get_session("dbs");if(DB!=""&&$i&&!in_array(DB,$i,true))$i=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Gb){$Fd=array();foreach($Gb
as$x=>$X)$Fd[]="$x $X";header("Content-Security-Policy: ".implode("; ",$Fd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self'","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$wf;if(!$wf)$wf=base64_encode(rand_string());return$wf;}function
page_messages($l){$lj=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$gf=idx($_SESSION["messages"],$lj);if($gf){echo"<div class='message'>".implode("</div>\n<div class='message'>",$gf)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$lj]);}if($l)echo"<div class='error'>$l</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($jf=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($jf);echo"</div>\n";if($jf!="auth")echo'<form action="" method="post">
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="Logout" id="logout">
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($of){while($of>=2147483648)$of-=4294967296;while($of<=-2147483649)$of+=4294967296;return(int)$of;}function
long2str(array$W,$Bj){$th='';foreach($W
as$X)$th
.=pack('V',$X);if($Bj)return
substr($th,0,end($W));return$th;}function
str2long($th,$Bj){$W=array_values(unpack('V*',str_pad($th,4*ceil(strlen($th)/4),"\0")));if($Bj)$W[]=strlen($th);return$W;}function
xxtea_mx($Ij,$Hj,$ji,$ue){return
int32((($Ij>>5&0x7FFFFFF)^$Hj<<2)+(($Hj>>3&0x1FFFFFFF)^$Ij<<4))^int32(($ji^$Hj)+($ue^$Ij));}function
encrypt_string($ei,$x){if($ei=="")return"";$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($ei,true);$of=count($W)-1;$Ij=$W[$of];$Hj=$W[0];$Sg=floor(6+52/($of+1));$ji=0;while($Sg-->0){$ji=int32($ji+0x9E3779B9);$oc=$ji>>2&3;for($ig=0;$ig<$of;$ig++){$Hj=$W[$ig+1];$nf=xxtea_mx($Ij,$Hj,$ji,$x[$ig&3^$oc]);$Ij=int32($W[$ig]+$nf);$W[$ig]=$Ij;}$Hj=$W[0];$nf=xxtea_mx($Ij,$Hj,$ji,$x[$ig&3^$oc]);$Ij=int32($W[$of]+$nf);$W[$of]=$Ij;}return
long2str($W,false);}function
decrypt_string($ei,$x){if($ei=="")return"";if(!$x)return
false;$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($ei,false);$of=count($W)-1;$Ij=$W[$of];$Hj=$W[0];$Sg=floor(6+52/($of+1));$ji=int32($Sg*0x9E3779B9);while($ji){$oc=$ji>>2&3;for($ig=$of;$ig>0;$ig--){$Ij=$W[$ig-1];$nf=xxtea_mx($Ij,$Hj,$ji,$x[$ig&3^$oc]);$Hj=int32($W[$ig]-$nf);$W[$ig]=$Hj;}$Ij=$W[$of];$nf=xxtea_mx($Ij,$Hj,$ji,$x[$ig&3^$oc]);$Hj=int32($W[0]-$nf);$W[0]=$Hj;$ji=int32($ji-0x9E3779B9);}return
long2str($W,true);}$yg=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($x)=explode(":",$X);$yg[$x]=$X;}}function
add_invalid_login(){$Fa=get_temp_dir()."/adminer.invalid";foreach(glob("$Fa*")?:array($Fa)as$o){$q=file_open_lock($o);if($q)break;}if(!$q)$q=file_open_lock("$Fa-".rand_string());if(!$q)return;$me=unserialize(stream_get_contents($q));$Di=time();if($me){foreach($me
as$ne=>$X){if($X[0]<$Di)unset($me[$ne]);}}$le=&$me[adminer()->bruteForceKey()];if(!$le)$le=array($Di+30*60,0);$le[1]++;file_write_unlock($q,serialize($me));}function
check_invalid_login(array&$yg){$me=array();foreach(glob(get_temp_dir()."/adminer.invalid*")as$o){$q=file_open_lock($o);if($q){$me=unserialize(stream_get_contents($q));file_unlock($q);break;}}$le=idx($me,adminer()->bruteForceKey(),array());$vf=($le[1]>29?$le[0]-time():0);if($vf>0)auth_error(lang_format(array('Too many unsuccessful logins, try again in %d minute.','Too many unsuccessful logins, try again in %d minutes.'),ceil($vf/60)),$yg);}$za=$_POST["auth"];if($za){session_regenerate_id();$xj=$za["driver"];$N=$za["server"];$V=$za["username"];$F=(string)$za["password"];$j=$za["db"];set_password($xj,$N,$V,$F);$_SESSION["db"][$xj][$N][$V][$j]=true;if($za["permanent"]){$x=implode("-",array_map('base64_encode',array($xj,$N,$V,$j)));$Mg=adminer()->permanentLogin(true);$yg[$x]="$x:".base64_encode($Mg?encrypt_string($F,$Mg):"");cookie("adminer_permanent",implode(" ",$yg));}if(count($_POST)==1||DRIVER!=$xj||SERVER!=$N||$_GET["username"]!==$V||DB!=$j)redirect(auth_url($xj,$N,$V,$j));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$x)set_session($x,null);unset_permanent($yg);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.'.' '.'Thanks for using Adminer, consider <a href="https://www.adminer.org/en/donation/">donating</a>.');}elseif($yg&&!$_SESSION["pwds"]){session_regenerate_id();$Mg=adminer()->permanentLogin();foreach($yg
as$x=>$X){list(,$cb)=explode(":",$X);list($xj,$N,$V,$j)=array_map('base64_decode',explode("-",$x));set_password($xj,$N,$V,decrypt_string(base64_decode($cb),$Mg));$_SESSION["db"][$xj][$N][$V][$j]=true;}}function
unset_permanent(array&$yg){foreach($yg
as$x=>$X){list($xj,$N,$V,$j)=array_map('base64_decode',explode("-",$x));if($xj==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$j==DB)unset($yg[$x]);}cookie("adminer_permanent",implode(" ",$yg));}function
auth_error($l,array&$yg){$Lh=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Lh]||$_GET[$Lh])&&!$_SESSION["token"])$l='Session expired, please login again.';else{restart_session();add_invalid_login();$F=get_password();if($F!==null){if($F===false)$l
.=($l?'<br>':'').sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> %s method to make it permanent.',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($yg);}}if(!$_COOKIE[$Lh]&&$_GET[$Lh]&&ini_bool("session.use_only_cookies"))$l='Session support must be enabled.';$lg=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$lg["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header('Login',$l,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".'The action will be performed after successful login with the same credentials.'."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($yg);page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){list($Ld,$Bg)=explode(":",SERVER,2);if(preg_match('~^\s*([-+]?\d+)~',$Bg,$A)&&($A[1]<1024||$A[1]>65535))auth_error('Connecting to privileged ports is not allowed.',$yg);check_invalid_login($yg);$Fb=adminer()->credentials();$f=Driver::connect($Fb[0],$Fb[1],$Fb[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Me=null;if(!is_object($f)||($Me=adminer()->login($_GET["username"],get_password()))!==true){$l=(is_string($f)?nl_br(h($f)):(is_string($Me)?$Me:'Invalid credentials.')).(preg_match('~^ | $~',get_password())?'<br>'.'There is a space in the input password which might be the cause.':'');auth_error($l,$yg);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header('Logout','Invalid CSRF token. Send the form again.');page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($za&&$_POST["token"])$_POST["token"]=get_token();$l='';if($_POST){if(!verify_token()){$ee="max_input_vars";$Ye=ini_get($ee);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$x){$X=ini_get($x);if($X&&(!$Ye||$X<$Ye)){$ee=$x;$Ye=$X;}}}$l=(!$_POST["token"]&&$Ye?sprintf('Maximum number of allowed fields exceeded. Please increase %s.',"'$ee'"):'Invalid CSRF token. Send the form again.'.' '.'If you did not send this request from Adminer then close this page.');}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$l=sprintf('Too big POST data. Reduce the data or increase the %s configuration directive.',"'post_max_size'");if(isset($_GET["sql"]))$l
.=' '.'You can upload a big SQL file via FTP and import it from server.';}function
print_select_result($I,$g=null,array$Xf=array(),$z=0){$Ke=array();$w=array();$e=array();$Ka=array();$bj=array();$J=array();for($s=0;(!$z||$s<$z)&&($K=$I->fetch_row());$s++){if(!$s){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($se=0;$se<count($K);$se++){$m=$I->fetch_field();$B=$m->name;$Wf=(isset($m->orgtable)?$m->orgtable:"");$Vf=(isset($m->orgname)?$m->orgname:$B);if($Xf&&JUSH=="sql")$Ke[$se]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($Wf!=""){if(isset($m->table))$J[$m->table]=$Wf;if(!isset($w[$Wf])){$w[$Wf]=array();foreach(indexes($Wf,$g)as$v){if($v["type"]=="PRIMARY"){$w[$Wf]=array_flip($v["columns"]);break;}}$e[$Wf]=$w[$Wf];}if(isset($e[$Wf][$Vf])){unset($e[$Wf][$Vf]);$w[$Wf][$Vf]=$se;$Ke[$se]=$Wf;}}if($m->charsetnr==63)$Ka[$se]=true;$bj[$se]=$m->type;echo"<th".($Wf!=""||$m->name!=$Vf?" title='".h(($Wf!=""?"$Wf.":"").$Vf)."'":"").">".h($B).($Xf?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($B),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"</thead>\n";}echo"<tr>";foreach($K
as$x=>$X){$_="";if(isset($Ke[$x])&&!$e[$Ke[$x]]){if($Xf&&JUSH=="sql"){$R=$K[array_search("table=",$Ke)];$_=ME.$Ke[$x].urlencode($Xf[$R]!=""?$Xf[$R]:$R);}else{$_=ME."edit=".urlencode($Ke[$x]);foreach($w[$Ke[$x]]as$hb=>$se)$_
.="&where".urlencode("[".bracket_escape($hb)."]")."=".urlencode($K[$se]);}}elseif(is_url($X))$_=$X;if($X===null)$X="<i>NULL</i>";elseif($Ka[$x]&&!is_utf8($X))$X="<i>".lang_format(array('%d byte','%d bytes'),strlen($X))."</i>";else{$X=h($X);if($bj[$x]==254)$X="<code>$X</code>";}if($_)$X="<a href='".h($_)."'".(is_url($_)?target_blank():'').">$X</a>";echo"<td".($bj[$x]<=9||$bj[$x]==246?" class='number'":"").">$X";}}echo($s?"</table>\n</div>":"<p class='message'>".'No rows.')."\n";return$J;}function
referencable_primary($Dh){$J=array();foreach(table_status('',true)as$oi=>$R){if($oi!=$Dh&&fk_support($R)){foreach(fields($oi)as$m){if($m["primary"]){if($J[$oi]){unset($J[$oi]);break;}$J[$oi]=$m;}}}}return$J;}function
textarea($B,$Y,$L=10,$kb=80){echo"<textarea name='".h($B)."' rows='$L' cols='$kb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($ya,array$Rf,$Y="",$Lf="",$zg=""){$wi=($Rf?"select":"input");return"<$wi$ya".($Rf?"><option value=''>$zg".optionlist($Rf,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$zg'>").($Lf?script("qsl('$wi').onchange = $Lf;",""):"");}function
json_row($x,$X=null){static$ad=true;if($ad)echo"{";if($x!=""){echo($ad?"":",")."\n\t\"".addcslashes($x,"\r\n\t\"\\/").'": '.($X!==null?'"'.addcslashes($X,"\r\n\"\\/").'"':'null');$ad=false;}else{echo"\n}\n";$ad=true;}}function
edit_type($x,array$m,array$jb,array$kd=array(),array$Rc=array()){$U=$m["type"];echo"<td><select name='".h($x)."[type]' class='type' aria-labelledby='label-type'>";if($U&&!array_key_exists($U,driver()->types())&&!isset($kd[$U])&&!in_array($U,$Rc))$Rc[]=$U;$fi=driver()->structuredTypes();if($kd)$fi['Foreign keys']=$kd;echo
optionlist(array_merge($Rc,$fi),$U),"</select><td>","<input name='".h($x)."[length]' value='".h($m["length"])."' size='3'".(!$m["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($jb?"<input list='collations' name='".h($x)."[collation]'".(preg_match('~(char|text|enum|set)$~',$U)?"":" class='hidden'")." value='".h($m["collation"])."' placeholder='(".'collation'.")'>":''),(driver()->unsigned?"<select name='".h($x)."[unsigned]'".(!$U||preg_match(number_type(),$U)?"":" class='hidden'").'><option>'.optionlist(driver()->unsigned,$m["unsigned"]).'</select>':''),(isset($m['on_update'])?"<select name='".h($x)."[on_update]'".(preg_match('~timestamp|datetime~',$U)?"":" class='hidden'").'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"CURRENT_TIMESTAMP":$m["on_update"])).'</select>':''),($kd?"<select name='".h($x)."[on_delete]'".(preg_match("~`~",$U)?"":" class='hidden'")."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",driver()->onActions),$m["on_delete"])."</select> ":" ");}function
process_length($y){$Ac=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Ac(?:\\s*,\\s*$Ac)*+\\s*\\)?\\s*\$~",$y)&&preg_match_all("~$Ac~",$y,$Se)?"(".implode(",",$Se[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$y)));}function
process_type(array$m,$ib="COLLATE"){return" $m[type]".process_length($m["length"]).(preg_match(number_type(),$m["type"])&&in_array($m["unsigned"],driver()->unsigned)?" $m[unsigned]":"").(preg_match('~char|text|enum|set~',$m["type"])&&$m["collation"]?" $ib ".(JUSH=="mssql"?$m["collation"]:q($m["collation"])):"");}function
process_field(array$m,array$Zi){if($m["on_update"])$m["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$m["on_update"]);return
array(idf_escape(trim($m["field"])),process_type($Zi),($m["null"]?" NULL":" NOT NULL"),default_value($m),(preg_match('~timestamp|datetime~',$m["type"])&&$m["on_update"]?" ON UPDATE $m[on_update]":""),(support("comment")&&$m["comment"]!=""?" COMMENT ".q($m["comment"]):""),($m["auto_increment"]?auto_increment():null),);}function
default_value(array$m){$k=$m["default"];$rd=$m["generated"];return($k===null?"":(in_array($rd,driver()->generated)?(JUSH=="mssql"?" AS ($k)".($rd=="VIRTUAL"?"":" $rd")."":" GENERATED ALWAYS AS ($k) $rd"):" DEFAULT ".(!preg_match('~^GENERATED ~i',$k)&&(preg_match('~char|binary|text|json|enum|set~',$m["type"])||preg_match('~^(?![a-z])~i',$k))?(JUSH=="sql"&&preg_match('~text|json~',$m["type"])?"(".q($k).")":q($k)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($k)":$k)))));}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$x=>$X){if(preg_match("~$x|$X~",$U))return" class='$x'";}}function
edit_fields(array$n,array$jb,$U="TABLE",array$kd=array()){$n=array_values($n);$Sb=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$pb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?'Column name':'Parameter name'),"<td id='label-type'>".'Type'."<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".'Length',"<td>".'Options';if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".'Auto Increment'."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",)),"<td id='label-default'$Sb>".'Default value',(support("comment")?"<td id='label-comment'$pb>".'Comment':"");echo"<td>".icon("plus","add[".(support("move_col")?0:count($n))."]","+",'Add next'),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");foreach($n
as$s=>$m){$s++;$Yf=$m[($_POST?"orig":"field")];$dc=(isset($_POST["add"][$s-1])||(isset($m["field"])&&!idx($_POST["drop_col"],$s)))&&(support("drop_col")||$Yf=="");echo"<tr".($dc?"":" style='display: none;'").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$s][inout]",explode("|",driver()->inout),$m["inout"]):"")."<th>";if($dc)echo"<input name='fields[$s][field]' value='".h($m["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'>";echo
input_hidden("fields[$s][orig]",$Yf);edit_type("fields[$s]",$m,$jb,$kd);if($U=="TABLE")echo"<td>".checkbox("fields[$s][null]",1,$m["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$s'".($m["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Sb>".(driver()->generated?html_select("fields[$s][generated]",array_merge(array("","DEFAULT"),driver()->generated),$m["generated"])." ":checkbox("fields[$s][generated]",1,$m["generated"],"","","","label-default")),"<input name='fields[$s][default]' value='".h($m["default"])."' aria-labelledby='label-default'>",(support("comment")?"<td$pb><input name='fields[$s][comment]' value='".h($m["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");echo"<td>",(support("move_col")?icon("plus","add[$s]","+",'Add next')." ".icon("up","up[$s]","↑",'Move up')." ".icon("down","down[$s]","↓",'Move down')." ":""),($Yf==""||support("drop_col")?icon("cross","drop_col[$s]","x",'Remove'):"");}}function
process_fields(array&$n){$C=0;if($_POST["up"]){$Be=0;foreach($n
as$x=>$m){if(key($_POST["up"])==$x){unset($n[$x]);array_splice($n,$Be,0,array($m));break;}if(isset($m["field"]))$Be=$C;$C++;}}elseif($_POST["down"]){$md=false;foreach($n
as$x=>$m){if(isset($m["field"])&&$md){unset($n[key($_POST["down"])]);array_splice($n,$C,0,array($md));break;}if(key($_POST["down"])==$x)$md=$m;$C++;}}elseif($_POST["add"]){$n=array_values($n);array_splice($n,key($_POST["add"]),0,array(array()));}elseif(!$_POST["drop_col"])return
false;return
true;}function
normalize_enum(array$A){$X=$A[0];return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($X[0].$X[0],$X[0],substr($X,1,-1))),'\\'))."'";}function
grant($td,array$Og,$e,$If){if(!$Og)return
true;if($Og==array("ALL PRIVILEGES","GRANT OPTION"))return($td=="GRANT"?queries("$td ALL PRIVILEGES$If WITH GRANT OPTION"):queries("$td ALL PRIVILEGES$If")&&queries("$td GRANT OPTION$If"));return
queries("$td ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$e, ",$Og).$e).$If);}function
drop_create($hc,$h,$jc,$_i,$lc,$Le,$ff,$df,$ef,$Ff,$sf){if($_POST["drop"])query_redirect($hc,$Le,$ff);elseif($Ff=="")query_redirect($h,$Le,$ef);elseif($Ff!=$sf){$Eb=queries($h);queries_redirect($Le,$df,$Eb&&queries($hc));if($Eb)queries($jc);}else
queries_redirect($Le,$df,queries($_i)&&queries($lc)&&queries($hc)&&queries($h));}function
create_trigger($If,array$K){$Fi=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$If.$Fi:$Fi.$If).rtrim(" $K[Type]\n$K[Statement]",";").";";}function
create_routine($ph,array$K){$O=array();$n=(array)$K["fields"];ksort($n);foreach($n
as$m){if($m["field"]!="")$O[]=(preg_match("~^(".driver()->inout.")\$~",$m["inout"])?"$m[inout] ":"").idf_escape($m["field"]).process_type($m,"CHARACTER SET");}$Ub=rtrim($K["definition"],";");return"CREATE $ph ".idf_escape(trim($K["name"]))." (".implode(", ",$O).")".($ph=="FUNCTION"?" RETURNS".process_type($K["returns"],"CHARACTER SET"):"").($K["language"]?" LANGUAGE $K[language]":"").(JUSH=="pgsql"?" AS ".q($Ub):"\n$Ub;");}function
remove_definer($H){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$H);}function
format_foreign_key(array$p){$j=$p["db"];$xf=$p["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$p["source"])).") REFERENCES ".($j!=""&&$j!=$_GET["db"]?idf_escape($j).".":"").($xf!=""&&$xf!=$_GET["ns"]?idf_escape($xf).".":"").idf_escape($p["table"])." (".implode(", ",array_map('Adminer\idf_escape',$p["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$p["on_delete"])?" ON DELETE $p[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$p["on_update"])?" ON UPDATE $p[on_update]":"");}function
tar_file($o,$Ki){$J=pack("a100a8a8a8a12a12",$o,644,0,0,decoct($Ki->size),decoct(time()));$bb=8*32;for($s=0;$s<strlen($J);$s++)$bb+=ord($J[$s]);$J
.=sprintf("%06o",$bb)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$Ki->send();echo
str_repeat("\0",511-($Ki->size+511)%512);}function
ini_bytes($ee){$X=ini_get($ee);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
doc_link(array$vg,$Ai="<sup>?</sup>"){$Jh=connection()->server_info;$yj=preg_replace('~^(\d\.?\d).*~s','\1',$Jh);$nj=array('sql'=>"https://dev.mysql.com/doc/refman/$yj/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$yj)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s','\1\2',$Jh)."&id=",);if(connection()->flavor=='maria'){$nj['sql']="https://mariadb.com/kb/en/";$vg['sql']=(isset($vg['mariadb'])?$vg['mariadb']:str_replace(".html","/",$vg['sql']));}return($vg[JUSH]?"<a href='".h($nj[JUSH].$vg[JUSH].(JUSH=='mssql'?"?view=sql-server-ver$yj":""))."'".target_blank().">$Ai</a>":"");}function
db_size($j){if(!connection()->select_db($j))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($h){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$h)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header('Database'.": ".h(DB),'Invalid database.',true);}else{if($_POST["db"]&&!$l)queries_redirect(substr(ME,0,-1),'Databases have been dropped.',drop_databases($_POST["db"]));page_header('Select database',$l,false);echo"<p class='links'>\n";foreach(array('database'=>'Create database','privileges'=>'Privileges','processlist'=>'Process list','variables'=>'Variables','status'=>'Status',)as$x=>$X){if(support($x))echo"<a href='".h(ME)."$x='>$X</a>\n";}echo"<p>".sprintf('%s version: %s through PHP extension %s',get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".sprintf('Logged as: %s',"<b>".h(logged_user())."</b>")."\n";$i=adminer()->databases();if($i){$xh=support("scheme");$jb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),"<thead><tr>".(support("database")?"<td>":"")."<th>".'Database'.(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".'Refresh'."</a>":"")."<td>".'Collation'."<td>".'Tables'."<td>".'Size'." - <a href='".h(ME)."dbsize=1'>".'Compute'."</a>".script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');","")."</thead>\n";$i=($_GET["dbsize"]?count_tables($i):array_flip($i));foreach($i
as$j=>$T){$oh=h(ME)."db=".urlencode($j);$t=h("Db-".$j);echo"<tr>".(support("database")?"<td>".checkbox("db[]",$j,in_array($j,(array)$_POST["db"]),"","","",$t):""),"<th><a href='$oh' id='$t'>".h($j)."</a>";$c=h(db_collation($j,$jb));echo"<td>".(support("database")?"<a href='$oh".($xh?"&amp;ns=":"")."&amp;database=' title='".'Alter database'."'>$c</a>":$c),"<td align='right'><a href='$oh&amp;schema=' id='tables-".h($j)."' title='".'Database schema'."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($j)."'>".($_GET["dbsize"]?db_size($j):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>\n".input_hidden("all").script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".'Drop'."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}if(!empty(adminer()->plugins)){echo"<div class='plugins'>\n","<h3>".'Loaded plugins'."</h3>\n<ul>\n";foreach(adminer()->plugins
as$_g){$Yb=(method_exists($_g,'description')?$_g->description():"");if(!$Yb){$dh=new
\ReflectionObject($_g);if(preg_match('~^/[\s*]+(.+)~',$dh->getDocComment(),$A))$Yb=$A[1];}$yh=(method_exists($_g,'screenshot')?$_g->screenshot():"");echo"<li><b>".get_class($_g)."</b>".h($Yb?": $Yb":"").($yh?" (<a href='".h($yh)."'".target_blank().">".'screenshot'."</a>)":"")."\n";}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~ns=[^&]*&~','',ME)."ns=".get_schema());if(!set_schema($_GET["ns"])){header("HTTP/1.1 404 Not Found");page_header('Schema'.": ".h($_GET["ns"]),'Invalid schema.',true);page_footer("ns");exit;}}}class
TmpFile{private$handler;var$size;function
__construct(){$this->handler=tmpfile();}function
write($zb){$this->size+=strlen($zb);fwrite($this->handler,$zb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$n=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$n)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$n[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$n=fields($a);if(!$n)$l=error()?:'No tables.';$S=table_status1($a);$B=adminer()->tableName($S);page_header(($n&&is_view($S)?$S['Engine']=='materialized view'?'Materialized view':'View':'Table').": ".($B!=""?$B:h($a)),$l);$nh=array();foreach($n
as$x=>$m)$nh+=$m["privileges"];adminer()->selectLinks($S,(isset($nh["insert"])||!support("table")?"":null));$ob=$S["Comment"];if($ob!="")echo"<p class='nowrap'>".'Comment'.": ".h($ob)."\n";function
tables_links($T){echo"<ul>\n";foreach($T
as$R)echo"<li><a href='".h(ME."table=".urlencode($R))."'>".h($R)."</a>";echo"</ul>\n";}$de=driver()->inheritsFrom($a);if($de){echo"<h3>".'Inherits from'."</h3>\n";tables_links($de);}elseif($n)adminer()->tableStructurePrint($n,$S);if(support("indexes")&&driver()->supportsIndex($S)){echo"<h3 id='indexes'>".'Indexes'."</h3>\n";$w=indexes($a);if($w)adminer()->tableIndexesPrint($w);echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.'Alter indexes'."</a>\n";}if(!is_view($S)){if(fk_support($S)){echo"<h3 id='foreign-keys'>".'Foreign keys'."</h3>\n";$kd=foreign_keys($a);if($kd){echo"<table>\n","<thead><tr><th>".'Source'."<td>".'Target'."<td>".'ON DELETE'."<td>".'ON UPDATE'."<td></thead>\n";foreach($kd
as$B=>$p){echo"<tr title='".h($B)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$p["source"]))."</i>";$_=($p["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($p["db"]),ME):($p["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($p["ns"]),ME):ME));echo"<td><a href='".h($_."table=".urlencode($p["table"]))."'>".($p["db"]!=""&&$p["db"]!=DB?"<b>".h($p["db"])."</b>.":"").($p["ns"]!=""&&$p["ns"]!=$_GET["ns"]?"<b>".h($p["ns"])."</b>.":"").h($p["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$p["target"]))."</i>)","<td>".h($p["on_delete"]),"<td>".h($p["on_update"]),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($B)).'">'.'Alter'.'</a>',"\n";}echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.'Add foreign key'."</a>\n";}if(support("check")){echo"<h3 id='checks'>".'Checks'."</h3>\n";$Xa=driver()->checkConstraints($a);if($Xa){echo"<table>\n";foreach($Xa
as$x=>$X)echo"<tr title='".h($x)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($x))."'>".'Alter'."</a>","\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.'Create check'."</a>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<h3 id='triggers'>".'Triggers'."</h3>\n";$Yi=triggers($a);if($Yi){echo"<table>\n";foreach($Yi
as$x=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($x)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($x))."'>".'Alter'."</a>\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.'Add trigger'."</a>\n";}$ce=driver()->inheritedTables($a);if($ce){echo"<h3 id='partitions'>".'Partitions'."</h3>\n";$og=driver()->partitionsInfo($a);if($og)echo"<p><code class='jush-".JUSH."'>BY ".h("$og[partition_by]($og[partition])")."</code>\n";tables_links($ce);}}elseif(isset($_GET["schema"])){page_header('Database schema',"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));$qi=array();$ri=array();$ca=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ca,$Se,PREG_SET_ORDER);foreach($Se
as$s=>$A){$qi[$A[1]]=array($A[2],$A[3]);$ri[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$Ni=0;$Ga=-1;$vh=array();$ch=array();$Fe=array();$sa=driver()->allFields();foreach(table_status('',true)as$R=>$S){if(is_view($S))continue;$Cg=0;$vh[$R]["fields"]=array();foreach($sa[$R]as$m){$Cg+=1.25;$m["pos"]=$Cg;$vh[$R]["fields"][$m["field"]]=$m;}$vh[$R]["pos"]=($qi[$R]?:array($Ni,0));foreach(adminer()->foreignKeys($R)as$X){if(!$X["db"]){$De=$Ga;if(idx($qi[$R],1)||idx($qi[$X["table"]],1))$De=min(idx($qi[$R],1,0),idx($qi[$X["table"]],1,0))-1;else$Ga-=.1;while($Fe[(string)$De])$De-=.0001;$vh[$R]["references"][$X["table"]][(string)$De]=array($X["source"],$X["target"]);$ch[$X["table"]][$R][(string)$De]=$X["target"];$Fe[(string)$De]=true;}}$Ni=max($Ni,$vh[$R]["pos"][0]+2.5+$Cg);}echo'<div id="schema" style="height: ',$Ni,'em;">
<script',nonce(),'>
qs(\'#schema\').onselectstart = () => false;
const tablePos = {',implode(",",$ri)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$Ni,';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'',js_escape(DB),'\');
</script>
';foreach($vh
as$B=>$R){echo"<div class='table' style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em;'>",'<a href="'.h(ME).'table='.urlencode($B).'"><b>'.h($B)."</b></a>",script("qsl('div').onmousedown = schemaMousedown;");foreach($R["fields"]as$m){$X='<span'.type_class($m["type"]).' title="'.h($m["type"].($m["length"]?"($m[length])":"").($m["null"]?" NULL":'')).'">'.h($m["field"]).'</span>';echo"<br>".($m["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$yi=>$eh){foreach($eh
as$De=>$Zg){$Ee=$De-idx($qi[$B],1);$s=0;foreach($Zg[0]as$Th)echo"\n<div class='references' title='".h($yi)."' id='refs$De-".($s++)."' style='left: $Ee"."em; top: ".$R["fields"][$Th]["pos"]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: ".(-$Ee)."em;'></div></div>";}}foreach((array)$ch[$B]as$yi=>$eh){foreach($eh
as$De=>$e){$Ee=$De-idx($qi[$B],1);$s=0;foreach($e
as$xi)echo"\n<div class='references arrow' title='".h($yi)."' id='refd$De-".($s++)."' style='left: $Ee"."em; top: ".$R["fields"][$xi]["pos"]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Ee)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($vh
as$B=>$R){foreach((array)$R["references"]as$yi=>$eh){foreach($eh
as$De=>$Zg){$if=$Ni;$We=-10;foreach($Zg[0]as$x=>$Th){$Dg=$R["pos"][0]+$R["fields"][$Th]["pos"];$Eg=$vh[$yi]["pos"][0]+$vh[$yi]["fields"][$Zg[1][$x]]["pos"];$if=min($if,$Dg,$Eg);$We=max($We,$Dg,$Eg);}echo"<div class='references' id='refl$De' style='left: $De"."em; top: $if"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($We-$if)."em;'></div></div>\n";}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".urlencode($ca)),'" id="schema-link">Permanent link</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$l){save_settings(array_intersect_key($_POST,array_flip(array("output","format","db_style","types","routines","events","table_style","auto_increment","triggers","data_style"))),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Nc=dump_headers((count($T)==1?key($T):DB),(DB==""||count($T)>1));$pe=preg_match('~sql~',$_POST["format"]);if($pe){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$gi=$_POST["db_style"];$i=array(DB);if(DB==""){$i=$_POST["databases"];if(is_string($i))$i=explode("\n",rtrim(str_replace("\r","",$i),"\n"));}foreach((array)$i
as$j){adminer()->dumpDatabase($j);if(connection()->select_db($j)){if($pe&&preg_match('~CREATE~',$gi)&&($h=get_val("SHOW CREATE DATABASE ".idf_escape($j),1))){set_utf8mb4($h);if($gi=="DROP+CREATE")echo"DROP DATABASE IF EXISTS ".idf_escape($j).";\n";echo"$h;\n";}if($pe){if($gi)echo
use_sql($j).";\n\n";$fg="";if($_POST["types"]){foreach(types()as$t=>$U){$Bc=type_values($t);if($Bc)$fg
.=($gi!='DROP+CREATE'?"DROP TYPE IF EXISTS ".idf_escape($U).";;\n":"")."CREATE TYPE ".idf_escape($U)." AS ENUM ($Bc);\n\n";else$fg
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$B=$K["ROUTINE_NAME"];$ph=$K["ROUTINE_TYPE"];$h=create_routine($ph,array("name"=>$B)+routine($K["SPECIFIC_NAME"],$ph));set_utf8mb4($h);$fg
.=($gi!='DROP+CREATE'?"DROP $ph IF EXISTS ".idf_escape($B).";;\n":"")."$h;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$h=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($h);$fg
.=($gi!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$h;;\n\n";}}echo($fg&&JUSH=='sql'?"DELIMITER ;;\n\n$fg"."DELIMITER ;\n\n":$fg);}if($_POST["table_style"]||$_POST["data_style"]){$_j=array();foreach(table_status('',true)as$B=>$S){$R=(DB==""||in_array($B,(array)$_POST["tables"]));$Lb=(DB==""||in_array($B,(array)$_POST["data"]));if($R||$Lb){$Ki=null;if($Nc=="tar"){$Ki=new
TmpFile;ob_start(array($Ki,'write'),1e5);}adminer()->dumpTable($B,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$_j[]=$B;elseif($Lb){$n=fields($B);adminer()->dumpData($B,$_POST["data_style"],"SELECT *".convert_fields($n,$n)." FROM ".table($B));}if($pe&&$_POST["triggers"]&&$R&&($Yi=trigger_sql($B)))echo"\nDELIMITER ;;\n$Yi\nDELIMITER ;\n";if($Nc=="tar"){ob_end_flush();tar_file((DB!=""?"":"$j/")."$B.csv",$Ki);}elseif($pe)echo"\n";}}if(function_exists('Adminer\foreign_keys_sql')){foreach(table_status('',true)as$B=>$S){$R=(DB==""||in_array($B,(array)$_POST["tables"]));if($R&&!is_view($S))echo
foreign_keys_sql($B);}}foreach($_j
as$zj)adminer()->dumpTable($zj,$_POST["table_style"],1);if($Nc=="tar")echo
pack("x512");}}}adminer()->dumpFooter();exit;}page_header('Export',$l,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Pb=array('','USE','DROP+CREATE','CREATE');$si=array('','DROP+CREATE','CREATE');$Mb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Mb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");if(!isset($K["events"])){$K["routines"]=$K["events"]=($_GET["dump"]=="");$K["triggers"]=$K["table_style"];}echo"<tr><th>".'Output'."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".'Format'."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".'Database'."<td>".html_select('db_style',$Pb,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],'User types'):"").(support("routine")?checkbox("routines",1,$K["routines"],'Routines'):"").(support("event")?checkbox("events",1,$K["events"],'Events'):"")),"<tr><th>".'Tables'."<td>".html_select('table_style',$si,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],'Auto Increment').(support("trigger")?checkbox("triggers",1,$K["triggers"],'Triggers'):""),"<tr><th>".'Data'."<td>".html_select('data_style',$Mb,$K["data_style"]),'</table>
<p><input type="submit" value="Export">
',input_token(),'
<table>
',script("qsl('table').onclick = dumpClick;");$Ig=array();if(DB!=""){$Za=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Za>".'Tables'."</label>".script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);",""),"<th style='text-align: right;'><label class='block'>".'Data'."<input type='checkbox' id='check-data'$Za></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);",""),"</thead>\n";$_j="";$ui=tables_list();foreach($ui
as$B=>$U){$Hg=preg_replace('~_.*~','',$B);$Za=($a==""||$a==(substr($a,-1)=="%"?"$Hg%":$B));$Lg="<tr><td>".checkbox("tables[]",$B,$Za,$B,"","block");if($U!==null&&!preg_match('~table~i',$U))$_j
.="$Lg\n";else
echo"$Lg<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$Za)."</label>\n";$Ig[$Hg]++;}echo$_j;if($ui)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-databases'".($a==""?" checked":"").">".'Database'."</label>",script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);",""),"</thead>\n";$i=adminer()->databases();if($i){foreach($i
as$j){if(!information_schema($j)){$Hg=preg_replace('~_.*~','',$j);echo"<tr><td>".checkbox("databases[]",$j,$a==""||$a=="$Hg%",$j,"","block")."\n";$Ig[$Hg]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$ad=true;foreach($Ig
as$x=>$X){if($x!=""&&$X>1){echo($ad?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$x%")."'>".h($x)."</a>";$ad=false;}}}elseif(isset($_GET["privileges"])){page_header('Privileges');echo'<p class="links"><a href="'.h(ME).'user=">'.'Create user'."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$td=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($td?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".'Username'."<th>".'Server'."<th></thead>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"])."<td>".h($K["Host"]).'<td><a href="'.h(ME.'user='.urlencode($K["User"]).'&host='.urlencode($K["Host"])).'">'.'Edit'."</a>\n";if(!$td||DB!="")echo"<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".'Edit'."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$l&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();exit;}restart_session();$Jd=&get_session("queries");$Id=&$Jd[DB];if(!$l&&$_POST["clear"]){$Id=array();redirect(remove_from_uri("history"));}stop_session();page_header((isset($_GET["import"])?'Import':'SQL command'),$l);$Je='--'.(JUSH=='sql'?' ':'');if(!$l&&$_POST){$q=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$Xh=adminer()->importServerPath();$q=@fopen((file_exists($Xh)?$Xh:"compress.zlib://$Xh.gz"),"rb");$H=($q?fread($q,1e6):false);}else$H=get_file("sql_file",true,";");if(is_string($H)){if(function_exists('memory_get_usage')&&($bf=ini_bytes("memory_limit"))!="-1")@ini_set("memory_limit",max($bf,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$Sg=$H.(preg_match("~;[ \t\r\n]*\$~",$H)?"":";");if(!$Id||first(end($Id))!=$Sg){restart_session();$Id[]=array($Sg,time());set_session("queries",$Jd);stop_session();}}$Uh="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|$Je)[^\n]*\n?|--\r?\n)";$Wb=";";$C=0;$wc=true;$g=connect();if($g&&DB!=""){$g->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$g);}$nb=0;$Dc=array();$mg='[\'"'.(JUSH=="sql"?'`#':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Je.'|$'.(JUSH=="pgsql"?'|\$[^$]*\$':'');$Oi=microtime(true);$ma=get_settings("adminer_import");$nc=adminer()->dumpFormat();unset($nc["sql"]);while($H!=""){if(!$C&&preg_match("~^$Uh*+DELIMITER\\s+(\\S+)~i",$H,$A)){$Wb=preg_quote($A[1]);$H=substr($H,strlen($A[0]));}elseif(!$C&&JUSH=='pgsql'&&preg_match("~^($Uh*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$A)){$Wb="\n\\\\\\.\r?\n";$C=strlen($A[0]);}else{preg_match("($Wb\\s*|$mg)",$H,$A,PREG_OFFSET_CAPTURE,$C);list($md,$Cg)=$A[0];if(!$md&&$q&&!feof($q))$H
.=fread($q,1e5);else{if(!$md&&rtrim($H)=="")break;$C=$Cg+strlen($md);if($md&&!preg_match("(^$Wb)",$md)){$Ra=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($Cg>0&&strtolower($H[$Cg-1])=="e"));$wg=($md=='/*'?'\*/':($md=='['?']':(preg_match("~^$Je|^#~",$md)?"\n":preg_quote($md).($Ra?'|\\\\.':''))));while(preg_match("($wg|\$)s",$H,$A,PREG_OFFSET_CAPTURE,$C)){$th=$A[0][0];if(!$th&&$q&&!feof($q))$H
.=fread($q,1e5);else{$C=$A[0][1]+strlen($th);if(!$th||$th[0]!="\\")break;}}}else{$wc=false;$Sg=substr($H,0,$Cg+($Wb[0]=="\n"?3:0));$nb++;$Lg="<pre id='sql-$nb'><code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Sg)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Uh*+ATTACH\\b~i",$Sg,$A)){echo$Lg,"<p class='error'>".'ATTACH queries are not supported.'."\n";$Dc[]=" <a href='#sql-$nb'>$nb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Lg;ob_flush();flush();}$ci=microtime(true);if(connection()->multi_query($Sg)&&$g&&preg_match("~^$Uh*+USE\\b~i",$Sg))$g->query($Sg);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Lg:""),"<p class='error'>".'Error in query'.(connection()->errno?" (".connection()->errno.")":"").": ".error()."\n";$Dc[]=" <a href='#sql-$nb'>$nb</a>";if($_POST["error_stops"])break
2;}else{$Di=" <span class='time'>(".format_time($ci).")</span>".(strlen($Sg)<1000?" <a href='".h(ME)."sql=".urlencode(trim($Sg))."'>".'Edit'."</a>":"");$oa=connection()->affected_rows;$Cj=($_POST["only_errors"]?"":driver()->warnings());$Dj="warnings-$nb";if($Cj)$Di
.=", <a href='#$Dj'>".'Warnings'."</a>".script("qsl('a').onclick = partial(toggle, '$Dj');","");$Lc=null;$Xf=null;$Mc="explain-$nb";if(is_object($I)){$z=$_POST["limit"];$Xf=print_select_result($I,$g,array(),$z);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$zf=$I->num_rows;echo"<p class='sql-footer'>".($zf?($z&&$zf>$z?sprintf('%d / ',$z):"").lang_format(array('%d row','%d rows'),$zf):""),$Di;if($g&&preg_match("~^($Uh|\\()*+SELECT\\b~i",$Sg)&&($Lc=explain($g,$Sg)))echo", <a href='#$Mc'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$Mc');","");$t="export-$nb";echo", <a href='#$t'>".'Export'."</a>".script("qsl('a').onclick = partial(toggle, '$t');","")."<span id='$t' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ma["output"])." ".html_select("format",$nc,$ma["format"]).input_hidden("query",$Sg)."<input type='submit' name='export' value='".'Export'."'>".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Uh*+(CREATE|DROP|ALTER)$Uh++(DATABASE|SCHEMA)\\b~i",$Sg)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang_format(array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),$oa)."$Di\n";}echo($Cj?"<div id='$Dj' class='hidden'>\n$Cj</div>\n":"");if($Lc){echo"<div id='$Mc' class='hidden explain'>\n";print_select_result($Lc,$g,$Xf);echo"</div>\n";}}$ci=microtime(true);}while(connection()->next_result());}$H=substr($H,$C);$C=0;}}}}if($wc)echo"<p class='message'>".'No commands to execute.'."\n";elseif($_POST["only_errors"])echo"<p class='message'>".lang_format(array('%d query executed OK.','%d queries executed OK.'),$nb-count($Dc))," <span class='time'>(".format_time($Oi).")</span>\n";elseif($Dc&&$nb>1)echo"<p class='error'>".'Error in query'.": ".implode("",$Dc)."\n";}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';$Jc="<input type='submit' value='".'Execute'."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Sg=$_GET["sql"];if($_POST)$Sg=$_POST["query"];elseif($_GET["history"]=="all")$Sg=$Id;elseif($_GET["history"]!="")$Sg=idx($Id[$_GET["history"]],0);echo"<p>";textarea("query",$Sg,20);echo
script(($_POST?"":"qs('textarea').focus();\n")."qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '".js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history"))."');"),"<p>";adminer()->sqlPrintAfter();echo"$Jc\n",'Limit rows'.": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{echo"<fieldset><legend>".'File upload'."</legend><div>";$zd=(extension_loaded("zlib")?"[.gz]":"");echo(ini_bool("file_uploads")?"SQL$zd (&lt; ".ini_get("upload_max_filesize")."B): <input type='file' name='sql_file[]' multiple>\n$Jc":'File uploads are disabled.'),"</div></fieldset>\n";$Td=adminer()->importServerPath();if($Td)echo"<fieldset><legend>".'From server'."</legend><div>",sprintf('Webserver file %s',"<code>".h($Td)."$zd</code>"),' <input type="submit" name="webfile" value="'.'Run file'.'">',"</div></fieldset>\n";echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),'Stop on error')."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),'Show only errors')."\n",input_token();if(!isset($_GET["import"])&&$Id){print_fieldset("history",'History',$_GET["history"]!="");for($X=end($Id);$X;$X=prev($Id)){$x=key($Id);list($Sg,$Di,$rc)=$X;echo'<a href="'.h(ME."sql=&history=$x").'">'.'Edit'."</a>"." <span class='time' title='".@date('Y-m-d',$Di)."'>".@date("H:i:s",$Di)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace("~^(#|$Je).*~m",'',$Sg)))),80,"</code>").($rc?" <span class='time'>($rc)</span>":"")."<br>\n";}echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'Edit all'."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$n=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$n):""):where($_GET,$n));$kj=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($n
as$B=>$m){if(!isset($m["privileges"][$kj?"update":"insert"])||adminer()->fieldName($m)==""||$m["generated"])unset($n[$B]);}if($_POST&&!$l&&!isset($_GET["select"])){$Le=$_POST["referer"];if($_POST["insert"])$Le=($kj?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$Le))$Le=ME."select=".urlencode($a);$w=indexes($a);$fj=unique_array($_GET["where"],$w);$Vg="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($Le,'Item has been deleted.',driver()->delete($a,$Vg,$fj?0:1));else{$O=array();foreach($n
as$B=>$m){$X=process_input($m);if($X!==false&&$X!==null)$O[idf_escape($B)]=$X;}if($kj){if(!$O)redirect($Le);queries_redirect($Le,'Item has been updated.',driver()->update($a,$O,$Vg,$fj?0:1));if(is_ajax()){page_headers();page_messages($l);exit;}}else{$I=driver()->insert($a,$O);$Ce=($I?last_id($I):0);queries_redirect($Le,sprintf('Item%s has been inserted.',($Ce?" $Ce":"")),$I);}}}$K=null;if($_POST["save"])$K=(array)$_POST["fields"];elseif($Z){$M=array();foreach($n
as$B=>$m){if(isset($m["privileges"]["select"])){$wa=($_POST["clone"]&&$m["auto_increment"]?"''":convert_field($m));$M[]=($wa?"$wa AS ":"").idf_escape($B);}}$K=array();if(!support("table"))$M=array("*");if($M){$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));if(!$I)$l=error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!support("table")&&!$n){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$x=>$X){if(!$Z)$K[$x]=null;$n[$x]=array("field"=>$x,"null"=>($x!=driver()->primary),"auto_increment"=>($x==driver()->primary));}}}edit_form($a,$n,$K,$kj,$l);}elseif(isset($_GET["create"])){$a=$_GET["create"];$qg=driver()->partitionBy;$tg=driver()->partitionsInfo($a);$bh=referencable_primary($a);$kd=array();foreach($bh
as$oi=>$m)$kd[str_replace("`","``",$oi)."`".str_replace("`","``",$m["field"])]=$oi;$ag=array();$S=array();if($a!=""){$ag=fields($a);$S=table_status1($a);if(count($S)<2)$l='No tables.';}$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$l){if($_POST["drop"])queries_redirect(substr(ME,0,-1),'Table has been dropped.',drop_tables(array($a)));else{$n=array();$sa=array();$oj=false;$id=array();$Zf=reset($ag);$qa=" FIRST";foreach($K["fields"]as$x=>$m){$p=$kd[$m["type"]];$Zi=($p!==null?$bh[$p]:$m);if($m["field"]!=""){if(!$m["generated"])$m["default"]=null;$Qg=process_field($m,$Zi);$sa[]=array($m["orig"],$Qg,$qa);if(!$Zf||$Qg!==process_field($Zf,$Zf)){$n[]=array($m["orig"],$Qg,$qa);if($m["orig"]!=""||$qa)$oj=true;}if($p!==null)$id[idf_escape($m["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$kd[$m["type"]],'source'=>array($m["field"]),'target'=>array($Zi["field"]),'on_delete'=>$m["on_delete"],));$qa=" AFTER ".idf_escape($m["field"]);}elseif($m["orig"]!=""){$oj=true;$n[]=array($m["orig"]);}if($m["orig"]!=""){$Zf=next($ag);if(!$Zf)$qa="";}}$E=array();if(in_array($K["partition_by"],$qg)){foreach($K
as$x=>$X){if(preg_match('~^partition~',$x))$E[$x]=$X;}foreach($E["partition_names"]as$x=>$B){if($B==""){unset($E["partition_names"][$x]);unset($E["partition_values"][$x]);}}$E["partition_names"]=array_values($E["partition_names"]);$E["partition_values"]=array_values($E["partition_values"]);if($E==$tg)$E=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$E=null;$cf='Table has been altered.';if($a==""){cookie("adminer_engine",$K["Engine"]);$cf='Table has been created.';}$B=trim($K["name"]);queries_redirect(ME.(support("table")?"table=":"select=").urlencode($B),$cf,alter_table($a,$B,(JUSH=="sqlite"&&($oj||$id)?$sa:$n),$id,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$E));}}page_header(($a!=""?'Alter table':'Create table'),$l,array("table"=>$a),h($a));if(!$_POST){$bj=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($bj["int"])?"int":(isset($bj["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($ag
as$m){$m["generated"]=$m["generated"]?:(isset($m["default"])?"DEFAULT":"");$K["fields"][]=$m;}if($qg){$K+=$tg;$K["partition_names"][]="";$K["partition_values"][]="";}}}$jb=collations();if(is_array(reset($jb)))$jb=call_user_func_array('array_merge',array_values($jb));$yc=driver()->engines();foreach($yc
as$xc){if(!strcasecmp($xc,$K["Engine"])){$K["Engine"]=$xc;break;}}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo'Table name'.": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",($yc?html_select("Engine",array(""=>"(".'engine'.")")+$yc,$K["Engine"]).on_help("event.target.value",1).script("qsl('select').onchange = helpClose;")."\n":"");if($jb)echo"<datalist id='collations'>".optionlist($jb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".'collation'.")'>\n");echo"<input type='submit' value='".'Save'."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$jb,"TABLE",$kd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",'Auto Increment'.": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),'Default values',"columnShow(this.checked, 5)","jsonly");$qb=($_POST?$_POST["comments"]:get_setting("comments"));echo(support("comment")?checkbox("comments",1,$qb,'Comment',"editingCommentsClick(this, true);","jsonly").' '.(preg_match('~\n~',$K["Comment"])?"<textarea name='Comment' rows='2' cols='20'".($qb?"":" class='hidden'").">".h($K["Comment"])."</textarea>":'<input name="Comment" value="'.h($K["Comment"]).'" data-maxlength="'.(min_version(5.5)?2048:60).'"'.($qb?"":" class='hidden'").'>'):''),'<p>
<input type="submit" value="Save">
';}echo'
';if($a!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$a));if($qg&&(JUSH=='sql'||$a=="")){$rg=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",'Partition by',$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$qg),$K["partition_by"]).on_help("event.target.value.replace(/./, 'PARTITION BY \$&')",1).script("qsl('select').onchange = partitionByChange;"),"(<input name='partition' value='".h($K["partition"])."'>)\n",'Partitions'.": <input type='number' name='partitions' class='size".($rg||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($rg?"":" class='hidden'").">\n","<thead><tr><th>".'Partition name'."<th>".'Values'."</thead>\n";foreach($K["partition_names"]as$x=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($x==count($K["partition_names"])-1?script("qsl('input').oninput = partitionNameChange;"):''),'<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$x)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Yd=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Yd[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Yd[]="SPATIAL";$w=indexes($a);$G=array();if(JUSH=="mongo"){$G=$w["_id_"];unset($Yd[0]);unset($w["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$l&&!$_POST["add"]&&!$_POST["drop_col"]){$b=array();foreach($K["indexes"]as$v){$B=$v["name"];if(in_array($v["type"],$Yd)){$e=array();$He=array();$Zb=array();$O=array();ksort($v["columns"]);foreach($v["columns"]as$x=>$d){if($d!=""){$y=idx($v["lengths"],$x);$Xb=idx($v["descs"],$x);$O[]=idf_escape($d).($y?"(".(+$y).")":"").($Xb?" DESC":"");$e[]=$d;$He[]=($y?:null);$Zb[]=$Xb;}}$Kc=$w[$B];if($Kc){ksort($Kc["columns"]);ksort($Kc["lengths"]);ksort($Kc["descs"]);if($v["type"]==$Kc["type"]&&array_values($Kc["columns"])===$e&&(!$Kc["lengths"]||array_values($Kc["lengths"])===$He)&&array_values($Kc["descs"])===$Zb){unset($w[$B]);continue;}}if($e)$b[]=array($v["type"],$B,$O);}}foreach($w
as$B=>$Kc)$b[]=array($Kc["type"],$B,"DROP");if(!$b)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),'Indexes have been altered.',alter_indexes($a,$b));}page_header('Indexes',$l,array("table"=>$a),h($a));$n=array_keys(fields($a));if($_POST["add"]){foreach($K["indexes"]as$x=>$v){if($v["columns"][count($v["columns"])]!="")$K["indexes"][$x]["columns"][]="";}$v=end($K["indexes"]);if($v["type"]||array_filter($v["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($w
as$x=>$v){$w[$x]["name"]=$x;$w[$x]["columns"][]="";}$w[]=array("columns"=>array(1=>""));$K["indexes"]=$w;}$He=(JUSH=="sql"||JUSH=="mssql");$Oh=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">Index Type
<th><input type="submit" class="wayoff">','Columns'.($He?"<span class='idxopts".($Oh?"":" hidden")."'> (".'length'.")</span>":"");if($He||support("descidx"))echo
checkbox("options",1,$Oh,'Options',"indexOptionsShow(this.checked)","jsonly")."\n";echo'<th id="label-name">Name
<th><noscript>',icon("plus","add[0]","+",'Add next'),'</noscript>
</thead>
';if($G){echo"<tr><td>PRIMARY<td>";foreach($G["columns"]as$x=>$d)echo
select_input(" disabled",$n,$d),"<label><input disabled type='checkbox'>".'descending'."</label> ";echo"<td><td>\n";}$se=1;foreach($K["indexes"]as$v){if(!$_POST["drop_col"]||$se!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$se][type]",array(-1=>"")+$Yd,$v["type"],($se==count($K["indexes"])?"indexesAddRow.call(this);":""),"label-type"),"<td>";ksort($v["columns"]);$s=1;foreach($v["columns"]as$x=>$d){echo"<span>".select_input(" name='indexes[$se][columns][$s]' title='".'Column'."'",($n?array_combine($n,$n):$n),$d,"partial(".($s==count($v["columns"])?"indexesAddColumn":"indexesChangeColumn").", '".js_escape(JUSH=="sql"?"":$_GET["indexes"]."_")."')"),"<span class='idxopts".($Oh?"":" hidden")."'>",($He?"<input type='number' name='indexes[$se][lengths][$s]' class='size' value='".h(idx($v["lengths"],$x))."' title='".'Length'."'>":""),(support("descidx")?checkbox("indexes[$se][descs][$s]",1,idx($v["descs"],$x),'descending'):""),"</span> </span>";$s++;}echo"<td><input name='indexes[$se][name]' value='".h($v["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n","<td>".icon("cross","drop_col[$se]","x",'Remove').script("qsl('button').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");}$se++;}echo'</table>
</div>
<p>
<input type="submit" value="Save">
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$l&&!$_POST["add"]){$B=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'Database has been dropped.',drop_databases(array(DB)));}elseif(DB!==$B){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".urlencode($B),'Database has been renamed.',rename_database($B,$K["collation"]));}else{$i=explode("\n",str_replace("\r","",$B));$hi=true;$Be="";foreach($i
as$j){if(count($i)==1||$j!=""){if(!create_database($j,$K["collation"]))$hi=false;$Be=$j;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".urlencode($Be),'Database has been created.',$hi);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),'Database has been altered.');}}page_header(DB!=""?'Alter database':'Create database',$l,array(),h(DB));$jb=collations();$B=DB;if($_POST)$B=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$jb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$td){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$td,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n".($jb?html_select("collation",array(""=>"(".'collation'.")")+$jb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)):""),'<input type="submit" value="Save">
';if(DB!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',DB))."\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",'Add next')."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$K=$_POST;if($_POST&&!$l){$_=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$_,'Schema has been dropped.');else{$B=trim($K["name"]);$_
.=urlencode($B);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($B),$_,'Schema has been created.');elseif($_GET["ns"]!=$B)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($B),$_,'Schema has been altered.');else
redirect($_);}}page_header($_GET["ns"]!=""?'Alter schema':'Create schema',$l);if(!$K)$K["name"]=$_GET["ns"];echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="Save">
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$_GET["ns"]))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ba=($_GET["name"]?:$_GET["call"]);page_header('Call'.": ".h($ba),$l);$ph=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Ud=array();$fg=array();foreach($ph["fields"]as$s=>$m){if(substr($m["inout"],-3)=="OUT")$fg[$s]="@".idf_escape($m["field"])." AS ".idf_escape($m["field"]);if(!$m["inout"]||substr($m["inout"],0,2)=="IN")$Ud[]=$s;}if(!$l&&$_POST){$Sa=array();foreach($ph["fields"]as$x=>$m){$X="";if(in_array($x,$Ud)){$X=process_input($m);if($X===false)$X="''";if(isset($fg[$x]))connection()->query("SET @".idf_escape($m["field"])." = $X");}$Sa[]=(isset($fg[$x])?"@".idf_escape($m["field"]):$X);}$H=(isset($_GET["callf"])?"SELECT":"CALL")." ".table($ba)."(".implode(", ",$Sa).")";$ci=microtime(true);$I=connection()->multi_query($H);$oa=connection()->affected_rows;echo
adminer()->selectQuery($H,$ci,!$I);if(!$I)echo"<p class='error'>".error()."\n";else{$g=connect();if($g)$g->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$g);else
echo"<p class='message'>".lang_format(array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),$oa)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($fg)print_select_result(connection()->query("SELECT ".implode(", ",$fg)));}}echo'
<form action="" method="post">
';if($Ud){echo"<table class='layout'>\n";foreach($Ud
as$x){$m=$ph["fields"][$x];$B=$m["field"];echo"<tr><th>".adminer()->fieldName($m);$Y=idx($_POST["fields"],$B);if($Y!=""){if($m["type"]=="set")$Y=implode(",",$Y);}input($m,$Y,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="Call">
',input_token(),'</form>

<pre>
';function
pre_tr($th){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($th))));}$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';echo
preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($A){$bd=pre_tr($A[2]);return"<table>\n".($A[1]?"<thead>$bd</thead>\n":$bd).pre_tr($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($ph['comment']))));echo'</pre>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$K=$_POST;if($_POST&&!$l&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$xi=array();foreach($K["source"]as$x=>$X)$xi[$x]=$K["target"][$x];$K["target"]=$xi;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $B"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$b="ALTER TABLE ".table($a);$I=($B==""||queries("$b DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B)));if(!$K["drop"])$I=queries("$b ADD".format_foreign_key($K));}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'Foreign key has been dropped.':($B!=""?'Foreign key has been altered.':'Foreign key has been created.')),$I);if(!$K["drop"])$l='Source and target columns must have the same data type, there must be an index on the target columns and referenced data must exist.';}page_header('Foreign key',$l,array("table"=>$a),h($a));if($_POST){ksort($K["source"]);if($_POST["add"])$K["source"][]="";elseif($_POST["change"]||$_POST["change-js"])$K["target"]=array();}elseif($B!=""){$kd=foreign_keys($a);$K=$kd[$B];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$Th=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$bg=get_schema();set_schema($K["ns"]);}$ah=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$xi=array_keys(fields(in_array($K["table"],$ah)?$K["table"]:reset($ah)));$Lf="this.form['change-js'].value = '1'; this.form.submit();";echo"<p><label>".'Target table'.": ".html_select("table",$ah,$K["table"],$Lf)."</label>\n";if(support("scheme")){$wh=array_filter(adminer()->schemas(),function($vh){return!preg_match('~^information_schema$~i',$vh);});echo"<label>".'Schema'.": ".html_select("ns",$wh,$K["ns"]!=""?$K["ns"]:$_GET["ns"],$Lf)."</label>";if($K["ns"]!="")set_schema($bg);}elseif(JUSH!="sqlite"){$Qb=array();foreach(adminer()->databases()as$j){if(!information_schema($j))$Qb[]=$j;}echo"<label>".'DB'.": ".html_select("db",$Qb,$K["db"]!=""?$K["db"]:$_GET["db"],$Lf)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type="submit" name="change" value="Change"></noscript>
<table>
<thead><tr><th id="label-source">Source<th id="label-target">Target</thead>
';$se=0;foreach($K["source"]as$x=>$X){echo"<tr>","<td>".html_select("source[".(+$x)."]",array(-1=>"")+$Th,$X,($se==count($K["source"])-1?"foreignAddRow.call(this);":""),"label-source"),"<td>".html_select("target[".(+$x)."]",$xi,idx($K["target"],$x),"","label-target");$se++;}echo'</table>
<p>
<label>ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-REFERENCES",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"SQLRF01111",)),'<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add column"></noscript>
';if($B!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$cg="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$cg=strtoupper($P["Engine"]);}if($_POST&&!$l){$B=trim($K["name"]);$wa=" AS\n$K[select]";$Le=ME."table=".urlencode($B);$cf='View has been altered.';$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$U=="VIEW"&&$cg=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$wa,$Le,$cf);else{$zi=$B."_adminer_".uniqid();drop_create("DROP $cg ".table($a),"CREATE $U ".table($B).$wa,"DROP $U ".table($B),"CREATE $U ".table($zi).$wa,"DROP $U ".table($zi),($_POST["drop"]?substr(ME,0,-1):$Le),'View has been dropped.',$cf,'View has been created.',$a,$B);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($cg!="VIEW");if(!$l)$l=error();}page_header(($a!=""?'Alter view':'Create view'),$l,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],'Materialized view'):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type="submit" value="Save">
';if($a!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$a));echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$ke=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$di=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$l){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'Event has been dropped.');elseif(in_array($K["INTERVAL_FIELD"],$ke)&&isset($di[$K["STATUS"]])){$uh="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'Event has been altered.':'Event has been created.'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$uh.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$uh)."\n".$di[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'Alter event'.": ".h($aa):'Create event'),$l);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>Name<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">Start<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">End<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>Every<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$ke,$K["INTERVAL_FIELD"]),'<tr><th>Status<td>',html_select("STATUS",$di,$K["STATUS"]),'<tr><th>Comment<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",'On completion preserve'),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="Save">
';if($aa!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$aa));echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ba=($_GET["name"]?:$_GET["procedure"]);$ph=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$l){$Yf=routine($_GET["procedure"],$ph);$zi="$K[name]_adminer_".uniqid();foreach($K["fields"]as$x=>$m){if($m["field"]=="")unset($K["fields"][$x]);}drop_create("DROP $ph ".routine_id($ba,$Yf),create_routine($ph,$K),"DROP $ph ".routine_id($K["name"],$K),create_routine($ph,array("name"=>$zi)+$K),"DROP $ph ".routine_id($zi,$K),substr(ME,0,-1),'Routine has been dropped.','Routine has been altered.','Routine has been created.',$ba,$K["name"]);}page_header(($ba!=""?(isset($_GET["function"])?'Alter function':'Alter procedure').": ".h($ba):(isset($_GET["function"])?'Create function':'Create procedure')),$l);if(!$_POST){if($ba=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$ph);$K["name"]=$ba;}}$jb=get_vals("SHOW CHARACTER SET");sort($jb);$qh=routine_languages();echo($jb?"<datalist id='collations'>".optionlist($jb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($qh?"<label>".'Language'.": ".html_select("language",$qh,$K["language"])."</label>\n":""),'<input type="submit" value="Save">
<div class="scrollable">
<table class="nowrap">
';edit_fields($K["fields"],$jb,$ph);if(isset($_GET["function"])){echo"<tr><td>".'Return type';edit_type("returns",$K["returns"],$jb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"]);echo'<p>
<input type="submit" value="Save">
';if($ba!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$ba));echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$da=$_GET["sequence"];$K=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);$B=trim($K["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($da),$_,'Sequence has been dropped.');elseif($da=="")query_redirect("CREATE SEQUENCE ".idf_escape($B),$_,'Sequence has been created.');elseif($da!=$B)query_redirect("ALTER SEQUENCE ".idf_escape($da)." RENAME TO ".idf_escape($B),$_,'Sequence has been altered.');else
redirect($_);}page_header($da!=""?'Alter sequence'.": ".h($da):'Create sequence',$l);if(!$K)$K["name"]=$da;echo'
<form action="" method="post">
<p><input name="name" value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="Save">
';if($da!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$da))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){$ea=$_GET["type"];$K=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);if($_POST["drop"])query_redirect("DROP TYPE ".idf_escape($ea),$_,'Type has been dropped.');else
query_redirect("CREATE TYPE ".idf_escape(trim($K["name"]))." $K[as]",$_,'Type has been created.');}page_header($ea!=""?'Alter type'.": ".h($ea):'Create type',$l);if(!$K)$K["as"]="AS ";echo'
<form action="" method="post">
<p>
';if($ea!=""){$bj=driver()->types();$Bc=type_values($bj[$ea]);if($Bc)echo"<code class='jush-".JUSH."'>ENUM (".h($Bc).")</code>\n<p>";echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$ea))."\n";}else{echo'Name'.": <input name='name' value='".h($K['name'])."' autocapitalize='off'>\n",doc_link(array('pgsql'=>"datatype-enum.html",),"?");textarea("as",$K["as"]);echo"<p><input type='submit' value='".'Save'."'>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B=$_GET["name"];$K=$_POST;if($K&&!$l){if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(),"",array(),"$B",($K["drop"]?"":$K["clause"]));else{$I=($B==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($B)));if(!$K["drop"])$I=queries("ALTER TABLE ".table($a)." ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"")." CHECK ($K[clause])");}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'Check has been dropped.':($B!=""?'Check has been altered.':'Check has been created.')),$I);}page_header(($B!=""?'Alter check'.": ".h($B):'Create check'),$l,array("table"=>$a));if(!$K){$ab=driver()->checkConstraints($a);$K=array("name"=>$B,"clause"=>$ab[$B]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo'Name'.': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type="submit" value="Save">
';if($B!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$Xi=trigger_options();$K=(array)trigger($B,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$l&&in_array($_POST["Timing"],$Xi["Timing"])&&in_array($_POST["Event"],$Xi["Event"])&&in_array($_POST["Type"],$Xi["Type"])){$If=" ON ".table($a);$hc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$If:"");$Le=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($hc,$Le,'Trigger has been dropped.');else{if($B!="")queries($hc);queries_redirect($Le,($B!=""?'Trigger has been altered.':'Trigger has been created.'),queries(create_trigger($If,$_POST)));if($B!="")queries(create_trigger($If,$K+array("Type"=>reset($Xi["Type"]))));}}$K=$_POST;}page_header(($B!=""?'Alter trigger'.": ".h($B):'Create trigger'),$l,array("table"=>$a));echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>Time<td>',html_select("Timing",$Xi["Timing"],$K["Timing"],"triggerChange(/^".preg_quote($a,"/")."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>Event<td>',html_select("Event",$Xi["Event"],$K["Event"],"this.form['Timing'].onchange();"),(in_array("UPDATE OF",$Xi["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>Type<td>',html_select("Type",$Xi["Type"],$K["Type"]),'</table>
<p>Name: <input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type="submit" value="Save">
';if($B!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){$fa=$_GET["user"];$Og=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$_b)$Og[$_b][$K["Privilege"]]=$K["Comment"];}$Og["Server Admin"]+=$Og["File access on server"];$Og["Databases"]["Create routine"]=$Og["Procedures"]["Create routine"];unset($Og["Procedures"]["Create routine"]);$Og["Columns"]=array();foreach(array("Select","Insert","Update","References")as$X)$Og["Columns"][$X]=$Og["Tables"][$X];unset($Og["Server Admin"]["Usage"]);foreach($Og["Tables"]as$x=>$X)unset($Og["Databases"][$x]);$rf=array();if($_POST){foreach($_POST["objects"]as$x=>$X)$rf[$X]=(array)$rf[$X]+idx($_POST["grants"],$x,array());}$ud=array();$Gf="";if(isset($_GET["host"])&&($I=connection()->query("SHOW GRANTS FOR ".q($fa)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$Se,PREG_SET_ORDER)){foreach($Se
as$X){if($X[1]!="USAGE")$ud["$A[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$ud["$A[2]$X[2]"]["GRANT OPTION"]=true;}}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$K[0],$A))$Gf=$A[1];}}if($_POST&&!$l){$Hf=(isset($_GET["host"])?q($fa)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Hf",ME."privileges=",'User has been dropped.');else{$tf=q($_POST["user"])."@".q($_POST["host"]);$ug=$_POST["pass"];if($ug!=''&&!$_POST["hashed"]&&!min_version(8)){$ug=get_val("SELECT PASSWORD(".q($ug).")");$l=!$ug;}$Eb=false;if(!$l){if($Hf!=$tf){$Eb=queries((min_version(5)?"CREATE USER":"GRANT USAGE ON *.* TO")." $tf IDENTIFIED BY ".(min_version(8)?"":"PASSWORD ").q($ug));$l=!$Eb;}elseif($ug!=$Gf)queries("SET PASSWORD FOR $tf = ".q($ug));}if(!$l){$mh=array();foreach($rf
as$Af=>$td){if(isset($_GET["grant"]))$td=array_filter($td);$td=array_keys($td);if(isset($_GET["grant"]))$mh=array_diff(array_keys(array_filter($rf[$Af],'strlen')),$td);elseif($Hf==$tf){$Ef=array_keys((array)$ud[$Af]);$mh=array_diff($Ef,$td);$td=array_diff($td,$Ef);unset($ud[$Af]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$Af,$A)&&(!grant("REVOKE",$mh,$A[2]," ON $A[1] FROM $tf")||!grant("GRANT",$td,$A[2]," ON $A[1] TO $tf"))){$l=true;break;}}}if(!$l&&isset($_GET["host"])){if($Hf!=$tf)queries("DROP USER $Hf");elseif(!isset($_GET["grant"])){foreach($ud
as$Af=>$mh){if(preg_match('~^(.+)(\(.*\))?$~U',$Af,$A))grant("REVOKE",array_keys($mh),$A[2]," ON $A[1] FROM $tf");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?'User has been altered.':'User has been created.'),!$l);if($Eb)connection()->query("DROP USER $tf");}}page_header((isset($_GET["host"])?'Username'.": ".h("$fa@$_GET[host]"):'Create user'),$l,array("privileges"=>array('','Privileges')));$K=$_POST;if($K)$ud=$rf;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$K["pass"]=$Gf;if($Gf!="")$K["hashed"]=true;$ud[(DB==""||$ud?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>Server<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>Username<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>Password<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8)?"":checkbox("hashed",1,$K["hashed"],'Hashed',"typePassword(this.form['pass'], this.checked);")),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".'Privileges'.doc_link(array('sql'=>"grant.html#priv_level"));$s=0;foreach($ud
as$Af=>$td){echo'<th>'.($Af!="*.*"?"<input name='objects[$s]' value='".h($Af)."' size='10' autocapitalize='off'>":input_hidden("objects[$s]","*.*")."*.*");$s++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>'Server',"Databases"=>'Database',"Tables"=>'Table',"Columns"=>'Column',"Procedures"=>'Routine',)as$_b=>$Xb){foreach((array)$Og[$_b]as$Ng=>$ob){echo"<tr><td".($Xb?">$Xb<td":" colspan='2'").' lang="en" title="'.h($ob).'">'.h($Ng);$s=0;foreach($ud
as$Af=>$td){$B="'grants[$s][".h(strtoupper($Ng))."]'";$Y=$td[strtoupper($Ng)];if($_b=="Server Admin"&&$Af!=(isset($ud["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$B><option><option value='1'".($Y?" selected":"").">".'Grant'."<option value='0'".($Y=="0"?" selected":"").">".'Revoke'."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$B value='1'".($Y?" checked":"").($Ng=="All privileges"?" id='grants-$s-all'>":">".($Ng=="Grant option"?"":script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$s-all'); };"))),"</label>";$s++;}}}echo"</table>\n",'<p>
<input type="submit" value="Save">
';if(isset($_GET["host"]))echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',"$fa@$_GET[host]"));echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$l){$ye=0;foreach((array)$_POST["kill"]as$X){if(kill_process($X))$ye++;}queries_redirect(ME."processlist=",lang_format(array('%d process has been killed.','%d processes have been killed.'),$ye),$ye||!$_POST["kill"]);}}page_header('Process list',$l);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");$s=-1;foreach(process_list()as$s=>$K){if(!$s){echo"<thead><tr lang='en'>".(support("kill")?"<th>":"");foreach($K
as$x=>$X)echo"<th>$x".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($x),'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"REFRN30223",));echo"</thead>\n";}echo"<tr>".(support("kill")?"<td>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$x=>$X)echo"<td>".((JUSH=="sql"&&$x=="Info"&&preg_match("~Query|Killed~",$K["Command"])&&$X!="")||(JUSH=="pgsql"&&$x=="current_query"&&$X!="<IDLE>")||(JUSH=="oracle"&&$x=="sql_text"&&$X!="")?"<code class='jush-".JUSH."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($K["db"]!=""?"db=".urlencode($K["db"])."&":"")."sql=".urlencode($X)).'">'.'Clone'.'</a>':h($X));echo"\n";}echo'</table>
</div>
<p>
';if(support("kill"))echo($s+1)."/".sprintf('%d in total',max_connections()),"<p><input type='submit' value='".'Kill'."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$w=indexes($a);$n=fields($a);$kd=column_foreign_keys($a);$Cf=$S["Oid"];$na=get_settings("adminer_import");$nh=array();$e=array();$_h=array();$Uf=array();$Ci="";foreach($n
as$x=>$m){$B=adminer()->fieldName($m);$pf=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($m["privileges"]["select"])&&$B!=""){$e[$x]=$pf;if(is_shortable($m))$Ci=adminer()->selectLengthProcess();}if(isset($m["privileges"]["where"])&&$B!="")$_h[$x]=$pf;if(isset($m["privileges"]["order"])&&$B!="")$Uf[$x]=$pf;$nh+=$m["privileges"];}list($M,$vd)=adminer()->selectColumnsProcess($e,$w);$M=array_unique($M);$vd=array_unique($vd);$oe=count($vd)<count($M);$Z=adminer()->selectSearchProcess($n,$w);$Tf=adminer()->selectOrderProcess($n,$w);$z=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$gj=>$K){$wa=convert_field($n[key($K)]);$M=array($wa?:idf_escape(key($K)));$Z[]=where_check($gj,$n);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$G=$ij=array();foreach($w
as$v){if($v["type"]=="PRIMARY"){$G=array_flip($v["columns"]);$ij=($M?$G:array());foreach($ij
as$x=>$X){if(in_array(idf_escape($x),$M))unset($ij[$x]);}break;}}if($Cf&&!$G){$G=$ij=array($Cf=>0);$w[]=array("type"=>"PRIMARY","columns"=>array($Cf));}if($_POST&&!$l){$Fj=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ab=array();foreach($_POST["check"]as$Wa)$ab[]=where_check($Wa,$n);$Fj[]="((".implode(") OR (",$ab)."))";}$Fj=($Fj?"\nWHERE ".implode(" AND ",$Fj):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$od=($M?implode(", ",$M):"*").convert_fields($e,$n,$M)."\nFROM ".table($a);$xd=($vd&&$oe?"\nGROUP BY ".implode(", ",$vd):"").($Tf?"\nORDER BY ".implode(", ",$Tf):"");$H="SELECT $od$Fj$xd";if(is_array($_POST["check"])&&!$G){$ej=array();foreach($_POST["check"]as$X)$ej[]="(SELECT".limit($od,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n).$xd,1).")";$H=implode(" UNION ALL ",$ej);}adminer()->dumpData($a,"table",$H);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$kd)){if($_POST["save"]||$_POST["delete"]){$I=true;$oa=0;$O=array();if(!$_POST["delete"]){foreach($_POST["fields"]as$B=>$X){$X=process_input($n[$B]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($B)]=($X!==false?$X:idf_escape($B));}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($G&&is_array($_POST["check"]))||$oe){$I=($_POST["delete"]?driver()->delete($a,$Fj):($_POST["clone"]?queries("INSERT $H$Fj".driver()->insertReturning($a)):driver()->update($a,$O,$Fj)));$oa=connection()->affected_rows;if(is_object($I))$oa+=$I->num_rows;}else{foreach((array)$_POST["check"]as$X){$Ej="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n);$I=($_POST["delete"]?driver()->delete($a,$Ej,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$Ej)):driver()->update($a,$O,$Ej,1)));if(!$I)break;$oa+=connection()->affected_rows;}}}$cf=lang_format(array('%d item has been affected.','%d items have been affected.'),$oa);if($_POST["clone"]&&$I&&$oa==1){$Ce=last_id($I);if($Ce)$cf=sprintf('Item%s has been inserted.'," $Ce");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$cf,$I);if(!$_POST["delete"]){$Fg=(array)$_POST["fields"];edit_form($a,array_intersect_key($n,$Fg),$Fg,!$_POST["clone"],$l);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$l='Ctrl+click on a value to modify it.';else{$I=true;$oa=0;foreach($_POST["val"]as$gj=>$K){$O=array();foreach($K
as$x=>$X){$x=bracket_escape($x,true);$O[idf_escape($x)]=(preg_match('~char|text~',$n[$x]["type"])||$X!=""?adminer()->processInput($n[$x],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($gj,$n),($oe||$G?0:1)," ");if(!$I)break;$oa+=connection()->affected_rows;}queries_redirect(remove_from_uri(),lang_format(array('%d item has been affected.','%d items have been affected.'),$oa),$I);}}elseif(!is_string($Yc=get_file("csv_file",true)))$l=upload_error($Yc);elseif(!preg_match('~~u',$Yc))$l='File must be in UTF-8 encoding.';else{save_settings(array("output"=>$na["output"],"format"=>$_POST["separator"]),"adminer_import");$I=true;$kb=array_keys($n);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Yc,$Se);$oa=count($Se[0]);driver()->begin();$Fh=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$L=array();foreach($Se[0]as$x=>$X){preg_match_all("~((?>\"[^\"]*\")+|[^$Fh]*)$Fh~",$X.$Fh,$Te);if(!$x&&!array_diff($Te[1],$kb)){$kb=$Te[1];$oa--;}else{$O=array();foreach($Te[1]as$s=>$hb)$O[idf_escape($kb[$s])]=($hb==""&&$n[$kb[$s]]["null"]?"NULL":q(preg_match('~^".*"$~s',$hb)?str_replace('""','"',substr($hb,1,-1)):$hb));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$G));if($I)driver()->commit();queries_redirect(remove_from_uri("page"),lang_format(array('%d row has been imported.','%d rows have been imported.'),$oa),$I);driver()->rollback();}}}$oi=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header('Select'.": $oi",$l);$O=null;if(isset($nh["insert"])||!support("table")){$lg=array();foreach((array)$_GET["where"]as$X){if(isset($kd[$X["col"]])&&count($kd[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$X["val"])))))$lg["set"."[".bracket_escape($X["col"])."]"]=$X["val"];}$O=$lg?"&".http_build_query($lg):"";}adminer()->selectLinks($S,$O);if(!$e&&support("table"))echo"<p class='error'>".'Unable to select the table'.($n?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$e);adminer()->selectSearchPrint($Z,$_h,$w);adminer()->selectOrderPrint($Tf,$Uf,$w);adminer()->selectLimitPrint($z);adminer()->selectLengthPrint($Ci);adminer()->selectActionPrint($w);echo"</form>\n";$D=$_GET["page"];$nd=null;if($D=="last"){$nd=get_val(count_rows($a,$Z,$oe,$vd));$D=floor(max(0,intval($nd)-1)/$z);}$Ah=$M;$wd=$vd;if(!$Ah){$Ah[]="*";$Ab=convert_fields($e,$n,$M);if($Ab)$Ah[]=substr($Ab,2);}foreach($M
as$x=>$X){$m=$n[idf_unescape($X)];if($m&&($wa=convert_field($m)))$Ah[$x]="$wa AS $X";}if(!$oe&&$ij){foreach($ij
as$x=>$X){$Ah[]=idf_escape($x);if($wd)$wd[]=idf_escape($x);}}$I=driver()->select($a,$Ah,$Z,$wd,$Tf,$z,$D,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$D)$I->seek($z*$D);$vc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($D&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$z&&$vd&&$oe&&JUSH=="sql")$nd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'No rows.'."\n";else{$Ea=adminer()->backwardKeys($a,$oi);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$vd&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'Modify'."</a>");$qf=array();$qd=array();reset($M);$Xg=1;foreach($L[0]as$x=>$X){if(!isset($ij[$x])){$X=idx($_GET["columns"],key($M))?:array();$m=$n[$M?($X?$X["col"]:current($M)):$x];$B=($m?adminer()->fieldName($m,$Xg):($X["fun"]?"*":h($x)));if($B!=""){$Xg++;$qf[$x]=$B;$d=idf_escape($x);$Md=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($x);$Xb="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($x))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$pd=apply_sql_function($X["fun"],$B);$Sh=isset($m["privileges"]["order"])||$pd;echo($Sh?'<a href="'.h($Md.($Tf[0]==$d||$Tf[0]==$x||(!$Tf&&$oe&&$vd[0]==$d)?$Xb:'')).'">'."$pd</a>":$pd),"<span class='column hidden'>";if($Sh)echo"<a href='".h($Md.$Xb)."' title='".'descending'."' class='text'> ↓</a>";if(!$X["fun"]&&isset($m["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.'Search'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($x)."');");echo"</span>";}$qd[$x]=$X["fun"];next($M);}}$He=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$x=>$X)$He[$x]=max($He[$x],min(40,strlen(utf8_decode($X))));}}echo($Ea?"<th>".'Relations':"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$kd)as$of=>$K){$fj=unique_array($L[$of],$w);if(!$fj){$fj=array();foreach($L[$of]as$x=>$X){if(!preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~',$x))$fj[$x]=$X;}}$gj="";foreach($fj
as$x=>$X){$m=(array)$n[$x];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$m["type"])&&strlen($X)>64){$x=(strpos($x,'(')?$x:idf_escape($x));$x="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$m["collation"])?$x:"CONVERT($x USING ".charset(connection()).")").")";$X=md5($X);}$gj
.="&".($X!==null?urlencode("where[".bracket_escape($x)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($x));}echo"<tr>".(!$vd&&$M?"":"<td>".checkbox("check[]",substr($gj,1),in_array(substr($gj,1),(array)$_POST["check"])).($oe||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$gj)."' class='edit'>".'edit'."</a>"));foreach($K
as$x=>$X){if(isset($qf[$x])){$m=(array)$n[$x];$X=driver()->value($X,$m);if($X!=""&&(!isset($vc[$x])||$vc[$x]!=""))$vc[$x]=(is_mail($X)?$qf[$x]:"");$_="";if(preg_match('~blob|bytea|raw|file~',$m["type"])&&$X!="")$_=ME.'download='.urlencode($a).'&field='.urlencode($x).$gj;if(!$_&&$X!==null){foreach((array)$kd[$x]as$p){if(count($kd[$x])==1||end($p["source"])==$x){$_="";foreach($p["source"]as$s=>$Th)$_
.=where_link($s,$p["target"][$s],$L[$of][$Th]);$_=($p["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($p["db"]),ME):ME).'select='.urlencode($p["table"]).$_;if($p["ns"])$_=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($p["ns"]),$_);if(count($p["source"])==1)break;}}}if($x=="COUNT(*)"){$_=ME."select=".urlencode($a);$s=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$fj))$_
.=where_link($s++,$W["col"],$W["val"],$W["op"]);}foreach($fj
as$ue=>$W)$_
.=where_link($s++,$ue,$W);}$Nd=select_value($X,$_,$m,$Ci);$t=h("val[$gj][".bracket_escape($x)."]");$Gg=idx(idx($_POST["val"],$gj),bracket_escape($x));$qc=!is_array($K[$x])&&is_utf8($Nd)&&$L[$of][$x]==$K[$x]&&!$qd[$x]&&!$m["generated"];$Ai=preg_match('~text|json|lob~',$m["type"]);echo"<td id='$t'".(preg_match(number_type(),$m["type"])&&($X===null||is_numeric(strip_tags($Nd)))?" class='number'":"");if(($_GET["modify"]&&$qc&&$X!==null)||$Gg!==null){$_d=h($Gg!==null?$Gg:$K[$x]);echo">".($Ai?"<textarea name='$t' cols='30' rows='".(substr_count($K[$x],"\n")+1)."'>$_d</textarea>":"<input name='$t' value='$_d' size='$He[$x]'>");}else{$Ne=strpos($Nd,"<i>…</i>");echo" data-text='".($Ne?2:($Ai?1:0))."'".($qc?"":" data-warning='".h('Use edit link to modify this value.')."'").">$Nd";}}}if($Ea)echo"<td>";adminer()->backwardKeysPrint($Ea,$L[$of]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$D){$Ic=true;if($_GET["page"]!="last"){if(!$z||(count($L)<$z&&($L||!$D)))$nd=($D?$D*$z:0)+count($L);elseif(JUSH!="sql"||!$oe){$nd=($oe?false:found_rows($S,$Z));if(intval($nd)<max(1e4,2*($D+1)*$z))$nd=first(slow_query(count_rows($a,$Z,$oe,$vd)));else$Ic=false;}}$jg=($z&&($nd===false||$nd>$z||$D));if($jg)echo(($nd===false?count($L)+1:$nd-$D*$z)>$z?'<p><a href="'.h(remove_from_uri("page")."&page=".($D+1)).'" class="loadmore">'.'Load more data'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $z, '".'Loading'."…');",""):''),"\n";echo"<div class='footer'><div>\n";if($jg){$Ve=($nd===false?$D+(count($L)>=$z?2:1):floor(($nd-1)/$z));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".'Page'."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'Page'."', '".($D+1)."')); return false; };"),pagination(0,$D).($D>5?" …":"");for($s=max(1,$D-4);$s<min($Ve,$D+5);$s++)echo
pagination($s,$D);if($Ve>0)echo($D+5<$Ve?" …":""),($Ic&&$nd!==false?pagination($Ve,$D):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Ve'>".'last'."</a>");}else
echo"<legend>".'Page'."</legend>",pagination(0,$D).($D>1?" …":""),($D?pagination($D,$D):""),($Ve>$D?pagination($D+1,$D).($Ve>$D+1?" …":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$ec=($Ic?"":"~ ").$nd;$Mf="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$ec' : checked); selectCount('selected2', this.checked || !checked ? '$ec' : checked);";echo
checkbox("all",1,0,($nd!==false?($Ic?"":"~ ").lang_format(array('%d row','%d rows'),$nd):""),$Mf)."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>Modify</legend><div>
<input type="submit" value="Save"',($_GET["modify"]?'':' title="'.'Ctrl+click on a value to modify it.'.'"'),'>
</div></fieldset>
<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="Edit">
<input type="submit" name="clone" value="Clone">
<input type="submit" name="delete" value="Delete">',confirm(),'</div></fieldset>
';$ld=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($ld['sql']);break;}}if($ld){print_fieldset("export",'Export'." <span id='selected2'></span>");$gg=adminer()->dumpOutput();echo($gg?html_select("output",$gg,$na["output"])." ":""),html_select("format",$ld,$na["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($vc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import'>".'Import'."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ","<input type='file' name='csv_file'> ",html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$na["format"])," <input type='submit' name='import' value='".'Import'."'>","</span>";echo
input_token(),"</form>\n",(!$vd&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?'Status':'Variables');$wj=($P?show_status():show_variables());if(!$wj)echo"<p class='message'>".'No rows.'."\n";else{echo"<table>\n";foreach($wj
as$K){echo"<tr>";$x=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($x)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$ki=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$S){json_row("Comment-$B",h($S["Comment"]));if(!is_view($S)){foreach(array("Engine","Collation")as$x)json_row("$x-$B",h($S[$x]));foreach($ki+array("Auto_increment"=>0,"Rows"=>0)as$x=>$X){if($S[$x]!=""){$X=format_number($S[$x]);if($X>=0)json_row("$x-$B",($x=="Rows"&&$X&&$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")?"~ $X":$X));if(isset($ki[$x]))$ki[$x]+=($S["Engine"]!="InnoDB"||$x!="Data_free"?$S[$x]:0);}elseif(array_key_exists($x,$S))json_row("$x-$B","?");}}}foreach($ki
as$x=>$X)json_row("sum-$x",format_number($X));json_row("");}elseif($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));else{foreach(count_tables(adminer()->databases())as$j=>$X){json_row("tables-$j",$X);json_row("size-$j",db_size($j));}json_row("");}exit;}else{$vi=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($vi&&!$l&&!$_POST["search"]){$I=true;$cf="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$cf='Tables have been truncated.';}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$cf='Tables have been moved.';}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$cf='Tables have been copied.';}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$cf='Tables have been dropped.';}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$cf
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?"":" ANALYZE"),$_POST["tables"]));$cf='Tables have been optimized.';}elseif(!$_POST["tables"])$cf='No tables.';elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$cf
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(substr(ME,0,-1),$cf,$I);}page_header(($_GET["ns"]==""?'Database'.": ".h(DB):'Schema'.": ".h($_GET["ns"])),$l,true);if(adminer()->homepage()){if($_GET["ns"]!==""){echo"<h3 id='tables-views'>".'Tables and views'."</h3>\n";$ui=tables_list();if(!$ui)echo"<p class='message'>".'No tables.'."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".'Search data in tables'." <span id='selected2'></span></legend><div>","<input type='search' name='query' value='".h($_POST["query"])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');","")," <input type='submit' name='search' value='".'Search'."'>\n","</div></fieldset>\n";if($_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=driver()->convertOperator("LIKE %%");search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",""),'<th>'.'Table','<td>'.'Rows'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'REFRN20286')),'<td>'.'Engine'.doc_link(array('sql'=>'storage-engines.html')),'<td>'.'Collation'.doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')),'<td>'.'Data Length'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'REFRN20286')),'<td>'.'Index Length'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),'<td>'.'Data Free'.doc_link(array('sql'=>'show-table-status.html')),'<td>'.'Auto Increment'.doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),(support("comment")?'<td>'.'Comment'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE')):''),"</thead>\n";$T=0;foreach($ui
as$B=>$U){$zj=($U!==null&&!preg_match('~table|sequence~i',$U));$t=h("Table-".$B);echo'<tr><td>'.checkbox(($zj?"views[]":"tables[]"),$B,in_array("$B",$vi,true),"","","",$t),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".urlencode($B)."' title='".'Show structure'."' id='$t'>".h($B).'</a>':h($B));if($zj)echo'<td colspan="6"><a href="'.h(ME)."view=".urlencode($B).'" title="'.'Alter view'.'">'.(preg_match('~materialized~i',$U)?'Materialized view':'View').'</a>','<td align="right"><a href="'.h(ME)."select=".urlencode($B).'" title="'.'Select data'.'">?</a>';else{foreach(array("Rows"=>array("select",'Select data'),"Engine"=>array(),"Collation"=>array(),"Data_length"=>array("create",'Alter table'),"Index_length"=>array("indexes",'Alter indexes'),"Data_free"=>array("edit",'New item'),"Auto_increment"=>array("auto_increment=1&create",'Alter table'),)as$x=>$_){$t=" id='$x-".h($B)."'";echo($_?"<td align='right'>".(support("table")||$x=="Rows"||(support("indexes")&&$x!="Data_length")?"<a href='".h(ME."$_[0]=").urlencode($B)."'$t title='$_[1]'>?</a>":"<span$t>?</span>"):"<td id='$x-".h($B)."'>");}$T++;}echo(support("comment")?"<td id='Comment-".h($B)."'>":""),"\n";}echo"<tr><td><th>".sprintf('%d in total',count($ui)),"<td><td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),"<td>".h(db_collation(DB,collations()));foreach(array("Data_length","Index_length","Data_free")as$x)echo"<td align='right' id='sum-$x'>";echo"\n","</table>\n","</div>\n";if(!information_schema(DB)){echo"<div class='footer'><div>\n";$tj="<input type='submit' value='".'Vacuum'."'> ".on_help("'VACUUM'");$Pf="<input type='submit' name='optimize' value='".'Optimize'."'> ".on_help(JUSH=="sql"?"'OPTIMIZE TABLE'":"'VACUUM OPTIMIZE'");echo"<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>".(JUSH=="sqlite"?$tj."<input type='submit' name='check' value='".'Check'."'> ".on_help("'PRAGMA integrity_check'"):(JUSH=="pgsql"?$tj.$Pf:(JUSH=="sql"?"<input type='submit' value='".'Analyze'."'> ".on_help("'ANALYZE TABLE'").$Pf."<input type='submit' name='check' value='".'Check'."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".'Repair'."'> ".on_help("'REPAIR TABLE'"):"")))."<input type='submit' name='truncate' value='".'Truncate'."'> ".on_help(JUSH=="sqlite"?"'DELETE'":"'TRUNCATE".(JUSH=="pgsql"?"'":" TABLE'")).confirm()."<input type='submit' name='drop' value='".'Drop'."'>".on_help("'DROP TABLE'").confirm()."\n";$i=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($i)!=1&&JUSH!="sqlite"){$j=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo"<p><label>".'Move to other database'.": ",($i?html_select("target",$i,$j):'<input name="target" value="'.h($j).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".'Move'."'>",(support("copy")?" <input type='submit' name='copy' value='".'Copy'."'> ".checkbox("overwrite",1,$_POST["overwrite"],'overwrite'):""),"\n";}echo"<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support("table")?" selectCount('selected2', formChecked(this, /^tables\[/) || $T);":"")." }"),input_token(),"</div></fieldset>\n","</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo"<p class='links'><a href='".h(ME)."create='>".'Create table'."</a>\n",(support("view")?"<a href='".h(ME)."view='>".'Create view'."</a>\n":"");if(support("routine")){echo"<h3 id='routines'>".'Routines'."</h3>\n";$rh=routines();if($rh){echo"<table class='odds'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Type'.'<td>'.'Return type'."<td></thead>\n";foreach($rh
as$K){$B=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".urlencode($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($K["SPECIFIC_NAME"]).$B).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($K["SPECIFIC_NAME"]).$B).'">'.'Alter'."</a>";}echo"</table>\n";}echo'<p class="links">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a>':'').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n";}if(support("sequence")){echo"<h3 id='sequences'>".'Sequences'."</h3>\n";$Ih=get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name");if($Ih){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."</thead>\n";foreach($Ih
as$X)echo"<tr><th><a href='".h(ME)."sequence=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."sequence='>".'Create sequence'."</a>\n";}if(support("type")){echo"<h3 id='user-types'>".'User types'."</h3>\n";$rj=types();if($rj){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."</thead>\n";foreach($rj
as$X)echo"<tr><th><a href='".h(ME)."type=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."type='>".'Create type'."</a>\n";}if(support("event")){echo"<h3 id='events'>".'Events'."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".'Name'."<td>".'Schedule'."<td>".'Start'."<td>".'End'."<td></thead>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?'At given time'."<td>".$K["Execute at"]:'Every'." ".$K["Interval value"]." ".$K["Interval field"]."<td>$K[Starts]"),"<td>$K[Ends]",'<td><a href="'.h(ME).'event='.urlencode($K["Name"]).'">'.'Alter'.'</a>';echo"</table>\n";$Gc=get_val("SELECT @@event_scheduler");if($Gc&&$Gc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Gc)."\n";}echo'<p class="links"><a href="'.h(ME).'event=">'.'Create event'."</a>\n";}if($ui)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}}}page_footer();