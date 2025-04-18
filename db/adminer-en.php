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
as$B=>$m){echo"<tr><th>".adminer()->fieldName($m);$k=idx($_GET["set"],bracket_escape($B));if($k===null){$k=$m["default"];if($m["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$k,$fh))$k=$fh[1];if(JUSH=="sql"&&preg_match('~binary~',$m["type"]))$k=bin2hex($k);}$Y=($K!==null?($K[$B]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$m["type"])&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$kj&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$k)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$m);$r=($_POST["save"]?idx($_POST["function"],$B,""):($kj&&preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$kj&&$Y==$m["default"]&&preg_match('~^[\w.]+\(~',$Y))$r="SQL";if(preg_match("~time~",$m["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$r="now";}if($m["type"]=="uuid"&&$Y=="uuid()"){$Y="";$r="uuid";}if($Ba!==false)$Ba=($m["auto_increment"]||$r=="now"||$r=="uuid"?null:true);input($m,$Y,$r,$Ba);if($Ba)$Ba=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($n){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($kj?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n",($kj?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'Saving'."â€¦', this); };"):"");}echo($kj?"<input type='submit' name='delete' value='".'Delete'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$y=80,$ii=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$Q,$A);return
h($A[1]).$ii.(isset($A[2])?"":"<i>â€¦</i>");}function
icon($Od,$B,$Nd,$Gi){return"<button type='submit' name='$B' title='".h($Gi)."' class='icon icon-$Od'><span>$Nd</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M‡±h´ÄgÌĞ±ÜÍŒ\"PÑiÒm„™cC³éˆŞd<Ìfóa¼ä:;NBˆqœR;1Lf³9ÈŞu7%©d\\;3ÍÇAĞä`%ŒEÃ!¨€¬e9&ã°‚r4˜M‚ÂA”Øv2\r&:©Î¦sœê“0ìÛ*3šMÃ¡…ºä-;šL‡C@èÌi:dt3-8a‘I\$Ã£°êe§Œ	Ë#9lT!Ñº…>˜e“\0ÊdÄÉdõC±ç:6\\c£A¾ÀrhÚM4ëk·ÔâÎZ|O+—“f9ÉÆXå±7h\"ì–Si¶¨ú¼|Ç+9èáÅÆ£©ÌĞ-4W~T:¹zkHá b{ Ğí&“Ñ”t†ª:Ü¸.K v8#\",7!pp2¡\0\\Á¼ ˜\$Îr7ƒ ŞŒ#€ği\"ôaÌT (Lî2#:\0Î¤ËxØ½ÌXFÇ‰‚dš&Îjv• ¨°Ú—£@düE¯ÛúÿÀ!,9.+˜`JáahbDP<Á°|\"Âèò¢¨Cpì>ÿË‘+b2	Lˆ¡é{óF’ˆÏQ´|™¦©ºr„ÁKl’ÉÔ_Æt=ÉÏğbÀK|©ÍŒ®ºª\r=ÓR¬>“ è0±£(ö¼¯kèb‰JU,PUumI.tèA-K”üXı4²z)MPĞçk’ºÙ3e`¾N>D#Â9\\ƒ(YTÛã@÷hL“Å1]È´ƒÍºNKÕ¶2\\73i/V¤¯lÛÅYÁÒ—BAœ/[JÖİÄ˜Ğ’\r;'²2^í…ªÔb¹Û£3éT=0Hö8\rí+6¹kf×CÏ]qÕW)‹ë˜ÆÂ²C•ÿ2`A–°¾82Á!À¾hmÇĞ²GD£‹ú¼2-C ö‹Yc`Â<¾s È6ù2µŠ¶9ˆu”æøyÌÒMgyí=,CZO~^3¥Òî0Ó2‹<¡Ğk0£…wM®{d#`ZÛ€ú‹ŒãŞº±ëù‚æ6¯C%»¼ª=Rq•Øğ¼_+ìµ-ÛK>ôÒ\n'GÀˆòA¡\$íú¡¡^j><ögf¾hømb*/\$\$lµÀØ´µg)Aj×ó wò#á£ƒƒ‡ÁÔÕõ…TNÕ]„Tÿ¾ã%ZÿùjJ–¦ªCf4ÆòázFà'ˆ*’ xª¹ª¬Öho&k\räÅ,­árÔ:>s(úLAsë€®¸5Ct‚¥©n†6¤ãË ll\\9DåÜ\\!‚mv‡\0ÊA{9`ˆ.¸â ×¡S’lg6¡àÛ!.2Á˜0‡PØÔ Ñi\r\$7‡w ¿â;G°Æ\$œ0âCIÃ^J\nçL·Pc¼Š'š*Eh°—äb‰”;µpŞBÃÛ(‰x‰:DÈLË.j±9AC@ I3jf’5sI`X}ÆÜÒ”#‚ºà7ŠT`d”¤jhH49S\nq”HJQ ØH`,F‡†PÊ0\\{ƒ¨m\rÌ~@20u!	\$†PoQ¥4ÇšÎ\nZLæM‚°BêÂk)@¶d³éSLÈpv’ Ïy²ĞƒBß“^oà„ä›*¾R™\"ÒıÓâ#€ºrÍ¥S;\r4ï&GùI©æT	°r’àê9=6’¡ÈQ’T\0“\0Äïf#¤ù=\$ÕŸ‘¢ŸÒH6†PæYƒ:®G\$œš‘ İ0è9:a”3Hz;GÃ\r!hJ±nĞ7‚	ıoYÿú¨¨¥ôWLvÌÛ“i|ŠùÄ%‡-â¤d\$‰pŒDä‘R2T\rÕpaU§øn—5rª„jÁ\$ƒr%D†©Ç)\$GÚƒBuÎà:²ø`¥(l€ àSD)I	ÀÖÁØ9ç*ê—\rtĞ2¡ ÆzI™§›g[Xûc,u\rvJ5?§ªÒÃ\"Š:„^‘.uàJùP“o\$t\$ƒ18„Ò\nnKÄT%EZ,6íDH„Vôó†ª¹i«&z¶«xpdrx*ƒ}ÊR25+Š’Ñ“fì2‡w»qş0X1Ï2dX‹ß¢èÌW©æÃ‹V8f\"ëƒq(uğ…E™ G“qM#Ğ°ñ#K•3WAŞv†YôÌç‰ÃeK]t¾˜]EÂëj=SXî™„@€ÏÓ‡\rŒÓ˜\$åÖ9ÂäÜ¬0ØP7\"D.åŒ<;ª‡Njæ=õºüÀå^èmmÚ’G¤68 ÆC’%v'ªyßÆk/‚^˜5šì3ä@Í.Ú›„kŠa’*—DºßÑ³‘:Ñ7ÿ•C}ıÄ`ø`í`)İ7Îõç­|	3Ğ iÕé¨½Âï4•\0.:®QßLƒçäØœÍ¨»Üfâ'™%äİ©M	¥¡ÙY3“\0Ç##tP6(‹BÏd§ ©Èoƒy¯6­|à5ş¸IH7 —âöæ„z?ù(ÑÚÅ–\$«RWT¼è°¦“:ˆû(ºá`rÏ¶i… ‚s‚=D\\ƒ˜,kr1ôÆÙ“2Ø`êñAä9˜¢&nÁ~ÍÌÒ¬£6;«vp ÓM#È]«¿ïàÉ´ÃÙØA˜’ÅiJ‚Œ.şÜá»tî»çÄîYsíOPwÄ¸¦mâæZ“‡İÿÀAUãÊ·JğN?Š€z»3\$PôqsÛU9,©ÖÊóó’#©µ5Pnb¦ãuNÑ{N`êé¥™šÛ°i—w\rbº&Eµ\\tg÷ğb¾a1‡+mÈpw#ÂvlÇıUÔö¡Ò\0ïe.(’wb@ïéş´\\°w…(…)´èEŸ¼¢;äZé]/&‘ËÃ|>Q¶\"c	<Fœ\rÑ7‚ÜÉåÏµ\\“'ÊSfe÷\rRŒ–ğŸVlo/.’’\nÖàFì±oè ·ehıÔeñÃj× ÈTÙsa4³2à2† Ö`oÎ\\¿A?ö]ğIoB[±{7' İQ%¨6ıs7\$Ã“ò‰‹~%ƒ»u)Õ5i ÷ÿ0¡#†¥£†\rÆfäËMX˜N\\É‡\0­¤ï,TüíôETo\0{oÄÂRˆ˜r‰^…§äîC @Z.C,şÂcû'°J-êBL \r€P«CD\"êb ^.\"ÚóàÊÀh€¤\0Øà€Ø\r\0Šà‚\n`‚	 Š š n o	€‚à”\r ´\r ¢0À` „0£¢	Á\rpš „	0À\n ’F@’`à V\0 \n€¢\r\0¤\n‚jÀÌ\n@ \0Ü\r Š\nÀ	 Ş\n@Ê@à\r\0ğ& ë\n@Ì @Æ àz­†Æ‚*Š©wĞq0g£5ğaPxGÂÕ…	•	\n¥\n­µ½\rpÅÍ\rpÓ\rÁ	Ğß0æ\rëğó`¢\r@ğ@†º ì‰à°é^\r ğ\0î\r d@î€ğ3 ­Ñ1Q9ÑAB„¨<®tÈ1N?ÑSÃá°v-‘ağƒp‹ğ“	pÛP§\n°¯°¿0Ç°Ï°Õ•\0ƒ@ÑĞíõ‘©Ñ°\r ^Àä\"i@\nÀ Œ6 ˜\0ê	 p\n€ò\n€š`Œ ˆqŞ’QDí¦BäM°d9ñTUq÷1û Ã2’	ò\n2²rR#°Ğ2+\r’/€Õ#‘¡@’ñ\" ÖQ\rÀÜ€˜\ràŒÀÈ@\n h\n€ã€ªíÀ†\0Ì`¨	ÀÆ@±!ñ;ñCoæUÒ‹2‘õò›ñe Qk ±p •àŸ!P±3Ñ!àƒr%ÒÁÛpÀ	 Í,Ğğ`ìğî\n `\n@êàff ° ª`Æ ’\nà¦@´	€âF#È`p€í# ‚äÿoÂÿ \\%Bl»Ï?çŞM-jPñór–¤3/Ó3*QlpÀ	pŸ\r`“=€Î\n\0_>±1’'ñ‘#\0ß>\0¡\rà”€Àäà˜„ ¢\n@â€ fÊ0Á'±@Ä€ŞÄÀÊÅ\0è\rd€FhµI\$ó`Ø”è,Üò¤¸é‰çCÍÈ•Pİ”T”>Ê7\0]EÌ£Ê‰DG¼©ÍAC´\\BMDÔˆò¥fmd—è(\rôOG FçÆiDN†ïÉœn2é4tÎ”wFt”íFŠí®ŞÙHšCÔˆu+°èÏ\$K¬6è”“Eä¤.AKÔL*1JK>º©èÈñMÔÁH¨ø\"GN„PjÄE´>ì ëH&5HÔ÷LM#EÍP‘c†¶c8‰æl €£¢,ÿ¢µCâ¦N€PtÇ@V tü\nşÔÖİ´õI	kGÎH	¤)D(™JPl„1jnğlÔ¬ÜJí~Ø*&ğn\\ÙÕ†HUfLk¯KôrFºìÇ<|HNx\\ NlêNäòôI¢Ö\0rzMtŠU|ZêšôÄ¸¦õºåÔ˜è\r“HCşÎ€B\"æ@ób¦cnœA —ÆJ9Ort´A4¸\rªÙ@hòÀA^`Û^¥V0Õ^!LòjUşğÛ.µ^\r\"±¬ì©kaõŒìlp‘‰d‘ Öş©}\0î¬ÖNï•àÔş•IGPÕöËUûYtyMPrä‹YÔÒEÚÔ¥xÄÔÄé6`×`jg´Şµ1SB²Ü‚°èòíÕXÖ8–V?Id[IµßQ.ŒåÖíİ`•ñi¬î‰Ì²vÔÉÂU)ÔœÎàÓ\n2PVœ~ãÌ ‰¢ˆŠ¦\"\"&§µr]-¢ áp*¨\0f\"†Kj`Ïq\nJã\"q¥F¬.öú\"@r«Â(³`òä3q>\"‚ôf‚Ø\r\$Ø­£ˆ ¢R1Ìªh&Hú`Z¬V	u+MoÊ¬¬\n3Jê\r Ä×2Iü ©D'×!S0W0J?yûpjZ.·\n\r ÷“pw–\"—-+ãzr!`Å|v2\nl¢f(¤m†<ƒÌ=âF\r©Q}ƒÏ~7æÌ\r·à#å½oÎ3÷ï}·ìØx<ø~×ıW¬øiEÃ£€à[‚8\n bjjë\r‚˜: ïƒØ¶)vÖê'{·ÕVçq\no{·±…)Cƒ˜‹àß‚˜i†Àê\rø%·é€ààÊC˜(œØkkôø‰‚‹ô4Ød”¾ ¿†øŠ‚jXLN÷(A—}xe‡ø‰|ø±wø´ÁG†€xhäí„Xx¦\r˜Ô%K¾ö…Ş¼oqƒx¸ˆ•˜»Š8s4e‡¸xÏ¤ínÓ*4Føc8~ŠhIp]Šâ{…åÎ%ù( ·Ó’øğ<åV÷à£ø†C¹B–Ø{wØıˆ¹O”€Æ£øù}¹Q‰8•[”×ñ•[Œ{“cT%ù&´Êo–·Á—¹:š*béE”`é™m–IŠYW™kš8›•Yo”™§šù—u¸)–¹™—Y5o™9—ãŞ¦÷’Ù‰‘¹<¦8(ù?œ\0[s×@*8·˜·}¹ßŸ9g›\rÓŸ¹—–¹”â\0Œ˜\n'wÂ±x)İŒÙ©šµšº—92·Z1ùï@[Iº+¢÷_“š5‚7=‚šD§qz!}ºK¤ùNdå£Ú3‚\0æã†qº+—C¡Øú¿Y_g‡8Øúy¸½‰Ú‰¨Kâ4Ú{‡ÙS¨8–2Z—zX\0Ï¨z©‡úS§ØªÚ±¹e« ¾\rª>¾:£§ù­‘ÚÇ¬´_¬ZÃ£’e»¬úµ¨®:ç¨ø¿œ•u„÷­{ÈUM‹—Úašƒ°ØíB«zÉˆû‰ãb2YSWJ(wOwÓwm› ØªZN÷l¶åË§CÌæ9å§í´€Ğæ8BDÊ¤6Œ©£Zy±x{ˆèæ;!©[mƒš¯¬Û{}»¸)¯¸#Î4¶[®´Å(½bˆ½ É˜¸«úÕ†›u¨û­«™«¹Ê,O¥\"Fª7y?»9£¼ÙndÑ}»±¹™{İs½{¹Š e´Ê¦>\"Öcc§‡¬d¤ŞÒcs{şÌvdCN½[Àû¹GM¿Cç“­ÉDE@");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M‡±h´ÄgÆÈh0ÁLĞàd91¢S!¤Û	Fƒ!°äv}0Ìfóa¼å	G2ÎNa'3IĞÊd•K%Ó	”Òm(\r&ãXèĞo;NBÉÄêy>2Sª*¶^#ŒÆQŒœĞ1=˜¥ÆJ¶W^„L£‘¡Ëo¡ˆÌÆc!Äf³­6»mâ¾a˜¯³l4&1Lf³9ÈŞu7VDc3Øn82IÎ†°Ê,:5ŒÊØr·P«ö1 Äm¡>5™W/™Fc©‡Dh2˜L‚\rN¯“ËİWo&Ähk€Şe2ÙŒĞÀb12Æ¼~0Ğ ”ãD}N¶\0úf4™M†C™”é­×ìn=´ˆpã©ZØ´²NÓ~Í;îŠÑ-C òæ%êz¢99PéÁã¤\"­Â‹²ß;‰\0fñª8à9¡pÔ:mÜ8ã„â@\nXà:rØ3#¨Øû­CÀ[¹Cxî#€ğI2\\š\"¡Àp“ÆÑÀ]#¨Ó5R¬r6Œ#ÃL7ğğß!Hƒ\$\$IRdÛ'Ë‰8]	§ƒxÜé+ò”¦>ÅC˜@-ã¨Û;ÃîÜbï<ä©2Ã”ğN4,„Œ«ã”ã-Mr¥6IcšX4¹aÊ†Ã5KETh@1@íÍR®K“9\r£(æ9Œ#8ËG¡CpwID5Ì2Øl\"_'ÓÊUBŒÌU¡9c@ÃG=C\nèÛSÈ0§Õàj®×7PUàÈ†£Û9J]®<×‹\nÆ²Ïƒzû?B÷Ô2—ÍÒÜ4\r/˜P\rÁM[X¡‚F‘_ìÿjŒ¬›HÓbnC&ŸÂ¡f%@cC^.2ã8¨×CÑ}^˜sw½LğÂ/ø5OÙM‘ä¸Ú³	*Xî?ŠbÍ.IgÊÔ&óaq„İŠ>‡çšFNå½-’`æy¬ä4¥s»áÓj\\&:ˆSaåP;ô¼†²H‘ëû”®XŒÎŞ¯Œéd¡kt?.´õ±,ZOÁ·@@8Z3©cŸ\"ÑèÃŸ\nØ=AšH1\\œZÏ^/kêÿÅÎƒLíuC\\ñc)0OüÃMÍïlpr†—7ä\rƒ‡q˜†Á¶ÙWRaÆŒ¡¥Øöc@Áwm’k/Û8£*?ÇÌè4ª5æ\\mŸ§¡kàù>d1nğëUQ#£§Üø¾wçæ†Ÿ«Lo&hÄªPrnR,5„Ÿ‡ôzƒ\"\$3»”dYH(p\rÂALACš)pTõPl²!\"L€´8ÀÂRà´&…\0µ“‡îZà±’0P8×ÆûÜã‡ÉJ	‡`Â¨e†0	®€Úœ1ûŠ	®D‘ÄJs°H‚³ˆ)™kÆ ¡[ÅóÔCÈy‚pjx,\rA‘…m!‡Ùœ<h1äœ");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':œÌ¢™Ğäi1ã³1Ôİ	4›ÍÀ£‰ÌQ6a&ó°Ç:OAIìäe:NFáD|İ!‘Ÿ†CyŒêm2ËÅ\"ã‰ÔÊr<”Ì±˜ÙÊ/C#‚‘Ùö:DbqSe‰JË¦CÜº\n\n¡œÇ±S\rZ“H\$RAÜS+XKvtdÜg:£í6Ÿ‰EvXÅ³j‘ÉmÒ©ej×2šM§©äúB«Ç&Ê®‹L§C°3„åQ0ÕLÆé-xè\nÓìD‘ÈÂyNaäPn:ç›¼äèsœÍƒ( cLÅÜ/õ£(Æ5{ŞôQy4œøg-–‚ı¢êi4ÚƒfĞÎ(ÕëbUıÏk·îo7Ü&ãºÃ¤ô*ACb’¾¢Ø`.‡­ŠÛ\rÎĞÜü»ÏÄú¼Í\n ©ChÒ<\r)`èØ¥`æ7¥CÊ’ŒÈâZùµãXÊ<QÅ1X÷¼‰@·0dp9EQüf¾°ÓFØ\r‰ä!ƒæ‹(hô£)‰Ã\np'#ÄŒ¤£HÌ(i*†r¸æ&<#¢æ7KÈÈ~Œ# È‡A:N6ã°Ê‹©lÕ,§\r”ôJPÎ3£!@Ò2>Cr¾¡¬h°N„á]¦(a0M3Í2”×6…ÔUæ„ãE2'!<·Â#3R<ğÛãXÒæÔCHÎ7ƒ#nä+±€a\$!èÜ2àPˆ0¤.°wd¡r:Yö¨éE²æ…!]„<¹šjâ¥ó@ß\\×pl§_\rÁZ¸€Ò“¬TÍ©ZÉsò3\"²~9À©³jã‰PØ)Q“Ybİ•DëYc¿`ˆzácµÑ¨ÌÛ'ë#t“BOh¢*2ÿ…<Å’Oêfg-Z£œˆÕ# è8aĞ^ú+r2b‰ø\\á~0©áş“¥ùàW©¸ÁŞnœÙp!#•`åëZö¸6¶12×Ã@é²kyÈÆ9\rìäB3çƒpŞ…î6°è<£!pïG¯9àn‘o›6s¿ğ#FØ3íÙàbA¨Ê6ñ9¦ıÀZ£#ÂŞ6ûÊ%?‡s¨È\"ÏÉ|Ø‚§)şbœJc\r»Œ½NŞsÉÛih8Ï‡¹æİŸè:Š;èúHåŞŒõu‹I5û@è1îªAèPaH^\$H×vãÖ@Ã›L~—¨ùb9'§ø¿±S?PĞ-¯˜ò˜0Cğ\nRòmÌ4‡ŞÓÈ“:ÀõÜÔ¸ï2òÌ4œµh(k\njIŠÈ6\"˜EYˆ#¹W’rª\r‘G8£@tĞáXÔ“âÌBS\nc0Ék‚C I\rÊ°<u`A!ó)ĞÔ2”ÖC¢\0=‡¾ æáäPˆ1‘Ó¢K!¹!†åŸpÄIsÑ,6âdÃéÉi1+°ÈâÔk‰€ê<•¸^	á\nÉ20´FÔ‰_\$ë)f\0 ¤C8E^¬Ä/3W!×)Œu™*äÔè&\$ê”2Y\n©]’„EkñDV¨\$ïJ²’‡xTse!RY» R™ƒ`=Lò¸ãàŞ«\nl_.!²V!Â\r\nHĞk²\$×`{1	|± °i<jRrPTG|‚w©4b´\r‰¡Ç4d¤,§E¡È6©äÏ<Ãh[N†q@Oi×>'Ñ©\rŠ¥ó—;¦]#“æ}Ğ0»ASIšJdÑA/QÁ´â¸µÂ@t\r¥UG‚Ä_G<éÍ<y-IÉzò„¤Ğ\" PÂàB\0ıíÀÈÁœq`‘ïvAƒˆaÌ¡Jå RäÊ®)Œ…JB.¦TÜñL¡îy¢÷ Cpp\0(7†cYY•a¨M€é1•em4Óc¢¸r£«S)oñÍà‚pæC!I†¼¾SÂœb0mìñ(d“EHœøš¸ß³„X‹ª£/¬•™P©èøyÆXé85ÈÒ\$+—Ö–»²gdè€öÎÎyİÜÏ³J×Øë ¢lE“¢urÌ,dCX}e¬ìÅ¥õ«mƒ]ˆĞ2 Ì½È(-z¦‚Zåú;Iöî¼\\Š) ,\n¤>ò)·¤æ\rVS\njx*w`â´·SFiÌÓd¯¼,»áĞZÂJFM}ĞŠ À†\\Z¾Pìİ`¹zØZûE]íd¤”ÉŸOëcmÔ]À ¬Á™•‚ƒ%ş\"w4Œ¥\n\$øÉzV¢SQDÛ:İ6«äG‹wMÔîS0B‰-sÆê)ã¾Zí¤cÇ2†˜Î´A;æ¥n©Wz/AÃZh G~cœc%Ë[ÉD£&lFRæ˜77|ªI„¢3¹íg0ÖLƒˆa½äcÃ0RJ‘2ÏÑ%“³ÃFáº SÃ ©L½^‘ trÚîÙtñÃ›¡Ê©;”Ç.å–šÅ”>ù€Ãá[®a‡N»¤Ï^Ã(!g—@1ğğó¢üN·zÔ<béİ–ŒäÛÑõO,ÛóCîuº¸D×tjŞ¹I;)®İ€é\nnäcºáÈ‚íˆW<sµ	Å\0÷hN¼PÓ9ÎØ{ue…¤utëµ•öè°ºó§½ 3ò‡î=ƒg¥ëº¸ÎÓJìÍºòWQ‡0ø•Øw9p-…Àº	ı§”øËğÙ'5»´\nOÛ÷e)MÈ)_kàz\0V´ÖÚúŞ;jîlîÎ\nÀ¦êçxÕPf-ä`CË.@&]#\0Ú¶pğyÍ–Æ›ŒtËdú¶ Ãó¼b}	G1·mßru™ßÀ*ñ_ÀxD²3Çq¼„BÓsQæ÷u€ús%ê\nª5s§ut½„Â{sòy¥€øNŸ¯4¥,J{4@®ş\0»’PÄÊÃ^ºš=“¯l„“²`èe~FÙ¡h3oé\"¤”q·R<iUT°[QàôUˆÇM6üT. ºê0'pe\\¼½ôŞ5ßÖÌ”pCe	Ù•Ô\"* M	”¨¦–D™ş±?ûhüØ2¡ĞãzU@7°CÓ4ıaµ²iE!fË\$üB¤…<œ9o*\$¯ælH™\$ Å@ààÊæ€P\rNÀYn<\$²	ÀQ…=F&¥ *@]\0ÊÏË W'dÖ z\$æĞjĞP[¢ö\$òä¯Ğ0#& _Ì`+†B)„wŒv%	âÔ›LcJ„€RSÀÂi`ÌÅ®	F€W	êË\nBP\nç\r\0}	ï¦®0²Zğ¸‚ò/`j\$«: §8ieüÀØÏ†xâ¹Â±îa ¬GnøsgO¢äU%VU°†@‚NÀ¤Ïúd+®(oJï†@XÆèàzM'FÙ£àWhV®I^Ù¢™1>İ@Ğ\"î¨¤‰ ÈQñR!‘\\¢`[¥¤«¨‰.Ø0fb†F;ëÂ‡çFpÏp/t`Â ô®(§ÀVé¸ø b“È²‰(€ˆHˆl‚œÁÎÔ¯1v­Ş‘€ğHĞï1Tï3ñ“q›àÉ1¦ÑªfË\nT\$°éàNq+Ëí`ŞvÖÇœï\rüVmûÇr°¨Ø'Ï¸±ñg%«\"Lˆm¼…‘(’(CLzˆ\"hâXØm= \\H\n0U‡‚ f&M\$¤g\$ñU`a\rPş>`Ë#gªhôî`†R4H€Ñ'ç©­³²GK;\"M¶Û¨TŒhµBEn\"b> Ú\rÀš©#›\0æ•N:í#_	QQ1{	f:BËÂáRª&àÜã)JµÄBr¹+ÂK.\$ĞPqõ-r®S%TIT&Qö·Ò{#2o(*P¯â5ï`„1H…®¢'	<Tğd±÷ª¾sÀì,NÚÊ ÒÉÔì^\r%ƒ3îĞ\r&à“4Bì/\0ĞkLH\$³4dÓ>ŠàÒ/³à¶µ€Hö€·* ºù3JÇĞ¥<†Hh©pú'‚çO/&ï2I.îx3V.¢s5Óe3íªÛZÛ(õ9E”g§;R—;±J½‘QÃ@ªÓvgz@¶“‚Şó†'dZ&Â,Uã²ßò¦F æb*²D‹òH! ä\r’;%‡x'G#°šÍ w‰Á#°Ö È2;#òBvÀXÉâ”aí\nb”{4K€G¦ß%°†ÒGuE`\\\rB\r\0¨-mW\rM\"¶#EôcFbFÕnzÓóÿ@4JÈÒ[\$Êë%2V”‹%ô&TÔV›ˆdÕ4hemN¯-;EÄ¾%E¥E´r <\"@»FÔPÂ€·L Üß­Ü4EÉğ°ÒÄz`ĞuŒ7éNŠ4¯Ë\0°F:hÎKœh/:\"™MÊZÔö\r+P4\r?¤™Sø™O;B©0\$FCEp‚ÇM\"%H4D´|€LN†FtEÑşgŠş°5å=J\r\"›Ş¼5³õ4à¾KñP\rbZà¨\r\"pEQ'DwKõW0î’g'…l\"hQFïC,ùCcŒ®òIHÒP hF]5µ& fŸTæÌiSTUS¨ÿîÉ[4™[uºNe–\$oüKìÜO àÿb\" 5ï\0›DÅ)EÒ%\"±]Âî/­âÈĞŒJ­6UÂdÿ‡`õña)V-0—DÓ”bMÍ)­šŠïÔ¯ØıÄ`Šæ%ñELtˆ˜+ìÛ6C7jëdµ¤:´V4Æ¡3î -ßR\rGòIT®…#¥<4-CgCP{V…\$'ëˆÓ÷gàûR@ä'Ğ²S=%À½óFñk: ¢k‘Ø9®²¤óe]aO¼ÒG9˜;îù-6Ûâ8WÀ¨*øx\"U‹®YlBïîöò¯ğÖ´°·	§ı\n‚îp®ğÉlšÉìÒZ–m\0ñ5¢òä®ğOqÌ¨ÌÍbÊW1s@ĞùKéº-pîûÆE¦Spw\nGWoQÓqG}vp‹w}q€ñqÓ\\Æ7ÆRZ÷@Ìì¡t‡ıtÆ;pG}w×€/%\"LE\0tÀhâ)§\r€àJÚ\\W@à	ç|D#S³¸ÆƒVÏâR±z‰2Ïõövµú©–‘	ã}¨’‡¢¯(¸\0y<¤X\r×İx±°‹q·<µœIsk1Sñ-Q4Yq8î#Şîv—îĞd.Ö¹S;qË!,'(òƒä<.è±J7Hç\"’š.³·¨ñuŒ°‡ü€#ÊQ\reƒrÀXv[¬h\$â{-éY °ûJBgé‰iM8¸”'Â\nÆ˜tDZ~/‹b‹ÖÕ8¸\$¸¸DbROÂOÆû`O5S>¸ö˜Î[ DÇê”¸¥ä€_3Xø)©À'éÄJd\rX»©¸UDìU X8ò•x¯-æ—…àPÌN` 	à¦\nŠZà‹”@Ra48§Ì:ø©\0éŠx°†ÖN§\\ê0%ãŒ·f“˜\\ ğ>\"@^\0ZxàZŸ\0ZaBr#åXÇğ\r•¨{•àË•¹flFb\0[–Şˆ\0[—6›˜	˜¢° ©=’â\n ¦WBøÆ\$'©kG´(\$yÌe9Ò(8Ù& h®îRÜ”ÙæoØÈ¼ Ç‡øƒ†Y£–4Øô7_’­dùã9'ı‘¢ú Üúï²ûz\r™ÙÖ  Ÿåğşv›G€èO8èØìMOh'æèXöS0³\0\0Ê	¸ı9s?‡öI¹MY¢8Ø 9ğ˜üä£HO“—,4	•xs‘‚P¤*G‡¢çc8·ªQÉ ø˜wB|Àz	@¦	à£9cÉK¤¤QGÄbFjÀXú’oSª\$ˆdFHÄ‚PÃ@Ñ§<å¶´Å,‚}ï®m£–rœÿ\"Å'k‹`Œ¡cà¡x‹¦e»C¨ÑCìì:¼ŞØ:XÌ ¹TŞÂÂ^´dÆÃ†qh¤ÎsÃ¹×LvÊÒ®0\r,4µ\r_vÔLòj¥jMáb[  ğƒlsÀŞ•Z°@øºäÁ¶;f”í`2Ycëeº'ƒMerÊÛF\$È!êê\n ¤	*0\rºAN»LP¥äjÙ“»»¿¼;Æ£VÓQ|(ğ‰3’†ÄÊ[p‰˜8óú¼|Ô^\räBf/DÆØÕÒ Bğ€_¶N5Mô© \$¼\naZĞ¦¶È€~ÀUlï¥eõrÅ§rÒ™Z®aZ³•¹ãøÕ£s8RÀGŒZŒ w®¢ªNœ_Æ±«YÏ£òm­‰âªÀ]’¦;ÆšLÚÿ‚º¶cø™€û°Å°ÆÚIÀQ3¹”Oã‡Ç|’y*`  ê5ÉÚ4ğ;&v8‘#¯Rô8+`XÍbVğ6¸Æ«i•3Fõ×EĞô„Øoc82ÛM­\"¶˜¹©G¦Wb\rOĞC¿VdèÓ­¤w\\äÍ¯*cSiÀQÒ¯“ã³R`úd7}	‚ºš)¢Ï´·,+bd§Û¹½FN£3¾¹L\\ãşeRn\$&\\rôê+dæÕ]O5kq,&\"DCU6j§pçÇÉ\\'‚@oµ~è5N=¨|”&è´!ÏÕBØwˆHÚyyz7Ï·(Çøâ½b5(3Öƒ_\0`zĞb®Ğ£r½‚8	ğ¢ZàvÈ8LË“·)²SİM<²*7\$›º\rRŒb·–âB%ıàÆ´Ds€zÏR>[‚Q½ŒĞ&Q«¨À¯¡Ì'\r‡ppÌz·/<‹‡}L¢#°Î•ÂĞâZ¹ã²\"tÆï\n„.4Şgæ«Pºp®Dìnà¥Ê¹NÈâFàd\0`^—åä\rnÈ‚×³#_âÄ w(ü2÷<7-ªXŞ¹\0··s¬ø,^¹hC,å!:×\rK„Ó.äİÓ¢¯Å¢ï¹ÔØ\\„ò+v˜Zàê\0§Q9eÊ›ËEöw?>°\$}£·D#ªğã cÓ0MV3½%Y»ÛÀ\rûÄtj5ÔÅ7¼ü{ÅšLz=­<ƒë8IøMõ°•õâGØÑÎŞLÅ\$’á2‰€{(ÿpe?uİ,Rïd*Xº4é®ı¿‡Í\0\"@Šˆš}<.@õ’	€ŞN²²\$î«XUjsİ/üî<>\"* è#\$Ôş÷Õ&CPI	ÿèt¿áùü¦î?è †´	ğOËÇ\\ Ì_èÎQ5YH@‹ŠÙbâÑcÑhî·ùæë±––…O0T©' 8¡wü»­öj+H€v_#º„íïì06ÈwÖœX†à»d+£Ü“\\Àå–\n\0	\\ğŸŸ>sî…ÓšA	PFöd8m'@š\nH´\0¬cèOwSßØ’—Yá`²ˆˆ¨¢R×ıDna\" ì™~Â?Ámğ†|@6ä½+ìGxV’ä\0°‰WƒÓ°’nw”„‘.¡Øƒb«Ÿ9Ã¸ˆEÈ|E·ÃÂ\rĞˆr¬\"Ğøx„‘¸-¸êŠâš\rN6n·\$Ò¬ı-BíHæ^Ó)â¥y&ãã×šW–Ç§àbv…Rì	¸¥³N\0°Ànâ	T„–`8X¬ğA\r:{Oş@\" Œ!Á¤\$KÂäqoĞËjYÖªJ´şÂíÜh}d<1IÇxdŠÊÎTT4NeeC0ä¥¿‡:D›FÚ5LŞ*::H”jZå—­FõRªMÖ€nS\n>POó[Œ\$V8;#‰K\\'ùBÖè»R®Ø¯°›RÑ_8Ájé*Ej \\~vÆÂĞvÄÛp@T€X‹\0002dE	…Hí‡Vğñ×D”\"Q'EDJB~A´ƒA¤Il*'\n¶Yå.è›+©9¾ñpg†ƒÒ/\"¸1—8Ä0„IAÊFCÈ¨ŠV*a™èPÀdÖĞ£5H\" AØå6İs¬YİØ;è¨È/¨¸0ãv}y˜\rÍƒâÎ×¥1…u\"Ë‹Šmãñ_º0ç„„`ß¯¿\\B1^\nk\r]lhø}]HBW`±—0½ê¨¹rFf€)”W,ÕÒ§]sm9'O¢xÔ½Í,ê9J8§£? 4ÉÉï¡\"Ò…èÛ½Ì<Ñ-S¨ÉÃşMÃ;ĞvÌñ6y|„ZòÁ‹¨%àa•#8¢ˆTC‘!pºË\nØïCZ(ï½wéØa– ·ˆÁ?9|€ó0<BL\r‰\nˆ]ÀPB0¤&‘+tÄHƒñÖ…àDx^÷î³,Lğ}[¦ÄBñx}½ĞruĞË\0¾€\0005‹åS@\"UØ”@Ü°\0€\$äÁŞ\"Ò ŸÄ]l/	ùíIâB4¯™.Â6 Â…ˆd7ˆ\r@=‘ªß¬¢ÕÛ*G jŒ¬Šüf`»:Hnì‘ÔbÄ€71Çê)C<@AÍY#°¦¡ëÑe’oâÖY!ÅÊI’DM¼\nlt¨“€/)˜\\43)®Ù2ï­É¸Ó)ÁŒ²f[ ppp1€µ©#“‰Ã¶p\0Ä§Å“l›À^{€„Aœ¤THå6ÖÊ«è\n\0PâH€.\r›’|ÀTFD0ŠS€y”ğÀÏ'1Ö´¤K’² dØµ±¯ÄBş”™Cç&Å)şW€s Hee+@4– r·“áÛš*Lp1<üf‚N–Y'­-	XKVa¦–L­¥ö\"›€Œ\"ìl•£q…É.YJHàm HV/lCŞ&àÀH)oÁ&\\2Äœ­%âáéz\n^Q(6ì˜D€ ÈûJq°–á«\00a#Ë6\0vr,»MÌú&A„Ôòìœ»‰9%YdBêhÀÖ!W\0êb\r{˜”Æ@Ç1¹‹I¬22AÚÚ)™H¾a@r’0GÉÜ7Dd.LM˜<˜ã2ĞÈË,k/™Meª¹œ}Ò’3ä=\0Ğ&É‹B‰ø\nPd.\"ÈñF3XÈSd(*¨J6 ä‡‹–F:¬×)1Â1á?lQ&Ïùµ¬h<JÍ‹¤f‡d–EÕº*ñx\n\0¼À.\"B -…#£ÀÎ—t¿IÎ«õ›Ğ	I8 ²’8dh	«èƒx€Ÿ§~°ƒ	L!K(úBXµ£-Èì‘hÎåc/Öræ×PÕIõ«NÊ2È|Éç×¶ŸÒ|\"µM‘'¡K,\\H°Ée5*o]4—ÒFP	2›Í<)ˆT¾“o˜À\n¢¸ØI¶Ú¢Ä!¨(øˆ‰_8Xrç;uŠúàØNJù„¡ˆé[rû˜DC:¸@ÁÍ³Àlœ\0©e\\*x@AÈ¡&í(‘5Ã×,ªŠ˜#1xÀ º!T D„ª­(QƒŸáDJ|D D:\0ÉAÙĞ¹Ô ÁbaEÓ?rn°²WkxŒøX=i‡,\$3[‚r™9B•Æ±§dã¡ş\0ºÔH‘4­«É<(zÊºô?àsIbJ©g UÂ\n(}¨Š›J\"à¦A™€BĞ19…~ÅIé#Ú\$¹‘%d  e\"µ`Àìátª¨•'O= À @\$µˆO”\nmT×o+Zäñ™ø-­„¢êßPF?Ò_…I¤JËX Ä£2Â¢ê-V¶;ª?2¥Áá0¡*P3Éªõë_T<E¥JÅ\\(İ2ô €Ø)êIQ‘Šé¬©·óÉRŒL&¥Í!È¯KÁiÑ†’t»¤°ÎKúHRl¢È¬Es“¶‰…¿¤DøŠxÇ´¬i¾ºÖ!faBÉñó¼FÔËe>€Vç©É-QjÂI‘Å7§˜ş\"%RhÈ g£áMŒ³ø«Õ-b£58RÂ‹¨„¯Ä*ã§9ÔÆêŠ°«·Ô9¤2Q0ı‡¬IR[üZ£İN\0÷ÇÂ20£¡ŒÂĞ\\[@áQ\0¤ÔJx„ùµ…äEC{©â\$lp1=\0·RĞ¾É>E~ßÆê×„ˆÑ:0À˜%€R+)\0°	Æ‘Qá@(\"¡_jT•X\0˜„ì\r1“\0P“9#\0”ÍôòH;Bª|À™²LöZ‘¼ÆŠ‹6ù/B’à\nB{ñğà|HÄ,á	*;œ(õ`Ê2@6ª>¡	å?P\0/„¹ó\0|\\ÅeBÜ`›’jq©U/\rc©üêÔÒ†¤6(N\0º/\$à\n8µj*U…\$›ñºŠy*³=¬;ˆ„ğŸ\$f¬â8XØBCEşœr\"/Ÿàª‚kÚ%\\9k§ùèBšœğ0§F­À(¬ğ'ôUôªµÆ®m¤@k‰T\0Õ¹EáÍsEhyòe\nä) )“b7ªã(W%,ÈJ¤r¨ó2D¶rhEùŸ\n0Qê3Š U9TPOÀŠÕô‘°8j|¤}ÃR<0‹Èâ™Zl ĞØTáö°ŒÈÙÚ*¯\$ÎÀU\rÛ\"¤.ª Ts~Ë~(ğ3€aº¨œ@ˆÕ+là`:Î`­:O…iùBXÁ?Ê„¦é7‰¾Lj|Í:n—K:Ø²}²\0İÉUMc`P%nn\n,ì4á™Q'%+H.è‹\"#GĞ3`¥¡İèİ\n1fg\0Ğœ'¼k¦²qxD<\"Œ,a|{~şó¸ÜC<S»i•Bï\nkNş ÖG³}’Óàk:„–Îî­ÀİgÛ)˜JD°ˆ•hÃ›f¢\"™kV~³ámM`HO”kD‹¬^ˆ0/tj«l³\rŒ!Ïf<ÀGôÛTºÕvµ#@­ek@2«wéı´0ÜÜ­tÄÙ€Ä¯1ÄuÌyvË%8±?1¼ÛÊlæ×xtÇœmp­›fK3ZÜJ£=\0@—^p·ÂÛ‘¹¶æ³ø]Ò²'ëtÙ¡@C·bëå\r[ÈãVôµ-½ÀËo“-œ¦İ e·}ÀéYªÜ	-é‡-m³I\0+ƒÍVßDÛ[B+€ç(-Ù4ä«>®qè–i>=½î‡/0-¦cL“pJ b\ndáò)â«#áGËs­·ä\"ÒQĞN“œøˆ`.úÈÔyÈEtPŠqÔI]ó¤ëJ8¼€»rWTÅÁIµè‹f÷aG„.ë–„7yçËlÙÕA€³7'¥1	âS€-ÙxI§œm·ËÂL:eÎ‰AÆWøİÎ¶EIİâ—Wz€Ô3Wòı°)*/)CÊÇÿx*c]ì%÷}½âÅ»_ÏÌIvÍ²½'˜\$U÷İS4k”5WÊJC®˜ 7*œb%<WC@Â“Æ	À¼©»c{Ş´«ò”¬3)Xò˜&&¢eLìI”å¢,Nì 2k#p5 €´f4«ˆöÇºëz¯#â½ä\\®ºà¡ûNøbÔUˆğoyğ€ÈSÕ4¾`qÓ~1–=ì8å‰¸*áOOJêC¡ñ®âÚè'Dd,@kLñ¹à¤ƒ÷”\\âj2Í©Äê±<³@_q÷2Ÿ\0‚Õ±Á)`˜˜Àêı•s°±óF\0¡ÓâÀÖ\n­‚Fš×<*Àx*•Àë`ÔàÁ-ƒŸ\røˆ‡|@ÑñÔ7ğH@w€óÿ‰H]µå˜\0¶àü_w¾µh0!Ës¢1Ï¾¦Ç¬„hW°€.Ãê=WªR*÷A_Æ”åEDÔ· ?1,UbÌ9=tÈ4Ã¨¤äWˆ¢^åäÙ;‘ßè±Ì@™ò(1<DâEÌ‚Hx©T()0zŠ`Ñ_Ğ;¨›ALé±)\nÌK[fˆH—Œ‰Wo—@bBKÀiMŠ±Ãd+ï>èvI¶(z:äİ.İ€À 9uiÑ¤DYÖâ¾ûÉO`ö®á]I\0Œ°RÄ†,K,÷¨ã6L¸Ä\"\"£1gª(•­†|T.,ñ9vb+\rk]u¶&è©|©åb£SÍÅd[¼,gêèaJº(CÄök¤”\rFØÂ“+	€ñŒ9âÂL©¹)Â)UAßB‰U†hÂgà’c3xñ-n9±úü»äxÈ®2¯´q¬ibÖrY7é€kÌyìfˆ, §¼àÎ)¬Ùª¤J:«NÂ8ÜRcly\nõ¼2ÅWô;¬.>Åv6Q#A0­ê{Î­iùï7~@VXÀ…¢^¿å11-É+Ïv|£Ü]Vf¸¢û.›{	áÒÀ\r·§;ê1lp°/ÙõuF‘Çd‰\$PĞ®0=@kSÆ0h›ÁÉˆÂœ@‘Ñ/*(OæV.•´G>‰(rËÎ!˜6àª÷…®òY=XZ@Â:²'&06kE|š“Ï'|H;“¼æNò€gÒ%ËW™+Âæ¯4ù;Íƒ¯¯'x|f©9­ÌÚ(O¨ğd¦§é·w%9]¦×f}ÌÃGÖÔÄs¦µçÂ¾óÓ…÷XM0ÍéŒ†gQ·ª¶8Ì„ù+O}¶Í0}’9„ÖĞŞ»–ßNhÎ/mgDé“s…°ü¦Äà\nÍ74å‹³P~}O)©UgÜ9ùÉÖj§8Pœ„İ¸Á(Ğ%ÄóöÛjŞ7oAB×Ği)ˆüKò„½Ùu¤ë´ …}s±1è=odİV[Ä´\n¬ç²zl€MĞ·r:F#{Öğ*#°xœÜÜ°¯<Ds½™k/mw :^æë¦âÉ1¿ÄÏD¨˜2ºz*Ñòn’ª%ôŞåÓÚiâÃ™ *Ê!8-·á¦tH•'í„ã\rÍĞºĞ4™äİ8`‚¿\"”¡»»i]’ZZœ>Z\0Ş¦9û”ìÚ+äŸ‚~†á\$Ş­„€LÄP\\ì‡XA©¬ èÀóŒÍišççzÒhÂ\$÷Â‹SMÚT'•š„1×èÏDÍâ	˜Ë5E©\0Ä\$ãttÔ®¥ì:\rMÆ·S¦šÓ––lsªˆAfÖKàk,N…lÛD^zz²dS˜®/rt²Nù>ıo%i¥½\0J¯B©po¢ÜR“™Ãê/Ö˜Ù«x\nyœ+«ì,e4‚Îq5Q'JDˆ]¿B@mÓ´ÈÃR§Ski~úÜÎ¶t0ç[ 1€z	••&×û^“\nOÕ¶²ÉV÷ëÀ³GV@T*ŞH9ÑÏ‰G0\0'Ö`Ñ°\r‡åûbQKsLd…*;\ná×æÁ.Ä”UNpà,Lâ@TRàe øb€œFÀø˜yŸn> IKÀ¶rGû	@Ù‚?cI’İ“u%GöOô1„ ÖCöh¦5Tüy„üI­Ù:\\0¼àX¥Ë>öÊŠ0ËŞ¾ûQB¶‡©EI/-LBTÚ!bïœ÷6ìÿk`jp\0K„„Â>kƒdšâÄ/•äISk.+*Íû¡R›|gR¡ıøW\\wùÂÓtà.)¤^Zc8ÕZ€~FÀ°SÇµÔSmÌ•;b>\0jz=î¢T'Á>Ìåq‹y}:»u§µ&åÀWºDQ¢Ïc-ªËşÇ6<[‡e÷x›Ø èĞî[ú¹L©\0wmùl°t•zëç<S€&ğådbÜxÍúoiâgK©\r`ÖÂµÔ?D5u@b‘„N¸àO•ğ¤·¤ˆíøYÔ[õŸè£Àñ{ÃNïœré‰ût±¾ó\0ïÅtMsšcBW?°*Dƒ.põ€¤'2•Ge\rp*#­e¹ĞêÚÅCıÓø\"³QI\nˆ‚hiøQÁ@Œ™á\rl	ˆß´à_.‡¤Êt*á^œøsÁ9ğ€ïWhqÕê¸~,¤áYÎ¸‚ÄdQsÂ¦\r‡BjºõDÿÇ¡ ñ<<T)C´\n¶Šø°Í&¹D{\rĞlÖğÑ-RãÊ\r@rk§é–Ï¢ø +ZíûïP¾ÛÖÎèéu8È¨ôÇ€ÚsãÙˆŠøóoç#äÊg€Èuï›¹\$F&\n-v\"PÜÎæ¶Ûjšnntë1ßV®§»¥öêAwbxß„ÄDÑ5áÍ-Ô0³aœ\0\r§/!ÈI¢Ñúí|/‰‚‚Şh…án„Gf-Mdnaˆ^(eïa´¤Â¨·YŞÏZ,†S€EöN‘ƒ\\§Õó›¸=Ò4~MÍ´¸\rÆëıÒFt•Å¦ñu\"|`ÑÒEá²ÀRózœÂDÌ`â{Äè@“k/KæY¹šŠ®3sJ¡äƒ¿5XGÍª”%®9)Qà £QÜäá¦1t•h¶ô!TRæ²ñÑHÂâÚQİ\rŸCåEÔ0—#wçG2ÂŞ/¾Ö/ç‚é=^ –/ÔºñÎÎÄÙËE’¬\0{+òü€t–+¨äqßĞ±ªæ–IÍt·|ú÷ÈÕvêğqª¹ÔˆÆŒ&Ï\r\\ëVß =–°EbÚënOÎrn›ê‘X({‡É¹uzK­¯`=:ø\núÄß÷\0ªêÇĞ[é%™:p”ˆq+¦ÔR’ldY”ë\"ÅÇ[VÏu{H-­ÁH×_ıâ¢8j‰ëV†Õ5€’à\"\0\"N?E;+°O~»wNÃ];Lœ'„‰íSOFˆÔêä»±Dæ-×!#sNÉ<Õêô Â¯Ñşmu³¤ÈóG¯8ûÎTn]¶¼Îá:úzIMnœ O°8ÀèÄz5…o\\57<ÅÍÅ²#8â¨ñé?sNîº•ÛLõ¸	}úxîÖ&4î†?ç[àz½–ôó³·¶Éı¡Œ<*W¸èİóÀe}{HZ‹§±,(<oÔoÀxW¨t¶2íĞİ# A*·¡»Ÿo\\ç¼R²}xH>NP¸|QÉš|x°'È-° ÛÅ2\0 İ?Æ¾2*\r|]tö•pá\"¢Ú²JuuXybŞD\nÊZ|„H7 _òW‘®şGuXyH>T\r¨G»äş˜Qlˆ¼ñ¨ÉƒÂçn!Îu'Ä*ºC5¸İ>Uª2!b	£9PwÂİ4åü›õá¢}yèWŞ|ñâa\$¾g†éêÁ óTÇUË¡&~9(\\*½!b_Ïùû€w±7\\£Çğ‹]=ß\\*ä­€@ğ#N7Íªè¯Á5QN`@<\0°6!‰9ÆÑl…¥\$ˆwI\$4 õ¾2–ë\$¥&‚Ğì.RZòà—³Y†›uyá¤³ìpå‡&SI®İ@¨EJiL€cõºV®1Fñ1…äZ\r\r¦‚àh“¡kÚ»öHHŠñË¿®ªªöˆKı§ ?xµâ-0\nÛêdÍN3Kó„CÓ59)Ä¾:B#¨ÌdN5A1”Æ‰šÆøÌOd[3Ú œáh–[s~)±9 DNâyøáñş>”âÀX±Ÿ'È½ÎÏHèòç,–î)Ú‚½\"Âeó0;\0Ëqeo>¦Û=®|«2¦G+B¨@z·ˆÏäøò@]}rQîÁÒ k/Š|íGñ:Ñ¯äW\0ça4>”ò^|õïƒìgİoûXEä9p…üÅLrg“A—Ä6¼˜p¿eúïÛÇ1ï´*Åëã½7ÚÀ[ö>]ı#ë?jB¨~Ö/¿}Å3ÿ:ûœU\$ğ?¼<•¿GüäaÿïÁ\n>0#!iƒ>.{A}'hQÿLwë~ŸW_¨îªTh#dÀÅÃ»–ªdŠŸFQ¸“µóâ*{æø\"‰\"¤P{õŸà}Ş4 N×ÕÓióŸ­Õ\r_ÅÊØÄe?l4À2¡?\nå—F™ú	åôqÎUï×Ä½°_İÿ`_üõÇàˆjı¬{_k_Ûo÷~ÿ¿c*#ÿ(´/ü!Dn¤Fÿ`ïü?@sôBÚ!®?;ÜEâ²úÿ“ş¾ÿ\0kş	ÿ*NıìD;¼õ°+d\nZZdB»À÷ ‹Š`B5æP\n8¬Öéàğ‡Ìc#ou½¤kßËŠM“İ¯w‡.ìªFÀJ¦ˆÈ!|®Äˆ2Fc‹Y).¬§ºôXHyò[ëê~ˆ†ù€#/™&¢£öã[À ÿñÂŒˆY@ı¨À(|\r\0,O¼ñ0YbÔÎ²Å¬ï\$0×ÓÛaË‘–À“É ˆA\$Çú0,Ë@ªÓ°>>9úÁ\\tiø<—\0ã—q\0Ä}@`ñ\0fVjƒ°­dß '(“‚†€	!_²nõ 0+c’´µiig8a]'=-¬B!(§Ø8†_İëÆx²j©Œµ”)\rH5HïƒYn	,f«rœí}-d\$òÖH ¬2né´†Ü›È=à-­d©“€FE-dáé¨a‚N_z4@”À[ènãŒ\$x!!i0Tª”ÊuÀ8ÌÉ¸…¼¯ş\0PZ8ZÁ¹†êcçàÁ®+ĞŠ‰AAF(äÁØÛ`mg*¸vS, Ç†ÜğKcAşÛ¬ &Ä¨9êÀ…ÁŠücİ0w•+ˆn€Î=›°)\$ë…ĞQğ~AŠÛaæ\0004\0uñ{Ä(´¤\$°­y	!°„B‹Û A<µa„‘Az ¨ÁZA4\$ZY9.aX\r•ˆdÚAˆLÂv|oOz|ßÂšZÜ(îeíZ£Ä†À");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("v0œF£©ÌĞ==˜ÎFS	ĞÊ_6MÆ³˜èèr:™E‡CI´Êo:C„”Xc‚\ræØ„J(:=ŸE†¦a28¡xğ¸?Ä'ƒi°SANN‘ùğxs…NBáÌVl0›ŒçS	œËUl(D|Ò„çÊP¦À>šE†ã©¶yHchäÂ-3Eb“å ¸b½ßpEÁpÿ9.Š˜Ì~\n?Kb±iw|È`Ç÷d.¼x8EN¦ã!”Í2™‡3©ˆá\r‡ÑYÌèy6GFmY8o7\n\r³0²<d4˜E'¸\n#™\ròˆñ¸è.…C!Ä^tè(õÍbqHïÔ.…›¢sÿƒ2™N‚qÙ¤Ì9î‹¦÷À#{‡cëŞåµÁì3nÓ¸2»Ár¼:<ƒ+Ì9ˆCÈ¨®‰Ã\n<ô\r`Èö/bè\\š È!HØ2SÚ™F#8ĞˆÇIˆ78ÃK‘«*Úº!ÃÀèé‘ˆæ+¨¾:+¯›ù&2|¢:ã¢9ÊÁÚ:­ĞN§¶ãpA/#œÀ ˆ0Dá\\±'Ç1ØÓ‹ïª2a@¶¬+Jâ¼.£c,”ø£‚°1Œ¡@^.BàÜÑŒá`OK=`B‹ÎPè6’ Î>(ƒeK%! ^!Ï¬‰BÈáHS…s8^9Í3¤O1àÑ.Xj+†â¸îM	#+ÖF£:ˆ7SÚ\$0¾V(ÙFQÃ\r!Iƒä*¡X¶/ÌŠ˜¸ë•67=ÛªX3İ†Ø‡³ˆĞ^±ígf#WÕùg‹ğ¢8ß‹íhÆ7µ¡E©k\rÖÅ¹GÒ)íÏt…We4öVØ½Š…ó&7\0RôÈN!0Ü1Wİãy¢CPÊã!íåi|Àgn´Û.\rã0Ì9¿Aî‡İ¸¶…Û¶ø^×8vÁl\"bì|…yHYÈ2ê9˜0Òß…š.ı:yê¬áÚ6:²Ø¿·nû\0Qµ7áøbkü<\0òéæ¹¸è-îBè{³Á;Öù¤òã W³Ê Ï&Á/nå¥wíî2A×µ„‡˜ö¥AÁ0yu)¦­¬kLÆ¹tkÛ\0ø;Éd…=%m.ö×Åc5¨fì’ï¸*×@4‡İ Ò…¼cÿÆ¸Ü†|\"ë§³òh¸\\Úf¸PƒNÁğqû—ÈÁsŸfÎ~PˆÊpHp\n~ˆ«>T_³ÒQOQÏ\$ĞVßŞSpn1¤Êšœ }=©‚LëüJeuc¤ˆ©ˆØaA|;†È“Nšó-ºôZÑ@R¦§Í³‘ Î	Áú.¬¤2†Ğêè…ª`REŠéí^iP1&œ¸Şˆ(Š²\$ĞCÍY­5á¸Øƒø·axh@ÑÃ=Æ²â +>`€ş×¢Ğœ¯\r!˜b´“ğr€ö2pø(=¡İœø!˜es¯X4GòHhc íM‘S.—Ğ|YjHƒğzBàSVÀ 0æjä\nf\rà‚åÍÁD‘o”ğ%ø˜\\1ÿ“ÒMI`(Ò:“! -ƒ3=0äÔÍ è¬Sø¼ÓgW…e5¥ğzœ(h©ÖdårœÓ«„KiÊ@Y.¥áŒÈ\$@šsÑ±EI&çÃDf…SR}±ÅrÚ½?x\"¢@ng¬÷À™PI\\U‚€<ô5X\"E0‰—t8†Yé=‚`=£”>“Qñ4B’k ¬¸+p`ş(8/N´qSKõr¯ƒëÿiîO*[JœùRJY±&uÄÊ¢7¡¤‚³úØ#Ô>‰ÂÓXÃ»ë?AP‘òCDÁD…ò\$‚Ù’ÁõY¬´<éÕãµX[½d«d„å:¥ìa\$‚‹ˆ†¸Î üŠWç¨/É‚è¶!+eYIw=9ŒÂÍiÙ;q\r\nÿØ1è³•xÚ0]Q©<÷zI9~Wåı9RDŠKI6ƒÛL…íŞCˆz\"0NWŒWzH4½ x›gû×ª¯x&ÚF¿aÓƒ†è\\éxƒà=Ó^Ô“´şKH‘x‡¨Ù“0èEÃÒ‚Éšã§Xµk,ñ¼R‰ ~	àñÌ›ó—Nyº›Szú¨”6\0D	æ¡ìğØ†hs|.õò=I‚x}/ÂuNçƒü'R•åìn'‚|so8r•å£tèæéÈa¨\0°5†PòÖ dwÌŠÇÆÌ•q³¹Š€5(XµHp|K¬2`µ]FU’~!ÊØ=å Ê|ò,upê‚\\“ ¾C¨oçT¶eâ•™C‚}*¨¥f¢#’shpáÏ5æ‹›³®mZ‹xàçfn~v)DH4çe††v“ÉVªõbyò¶TÊÇÌ¥,´ôœ<Íy,ÖÌ«2¹ôÍz^÷¥” Kƒ˜2¢xo	ƒ ·•Ÿ2Ñ I”ùa½hõ~ ­cê€ejõ6­×)ÿ]¦Ô¢5×ÍdG×ŠEÎtË'Ná=VĞİÉœ@Ğşƒàb^åÌÚöp:k‡Ë1StTÔ™FF´—`´¾`øò{{Ôï­é4÷•7ÄpcPòØ·öõVÀì9ÂÙ‰Lt‰	M¶µ­Ò{öC©l±±n47sÉPL¬˜!ñ9{l a°Á‰–œ½!pG%Ü)<Á·2*ä<Œ9rV‘Éø\\©³”]îWËtn\r<Ä—Ş0£vJ“æ ±Iãi ™1›½Ys{uHÕ°?ëÛ–‘ƒ®ÇÎU€oäAß’r`SˆÿCc€ï”ôv‘Ë³Jé‡c§µõÔû=Ïã-H/À®Øq'E° ï–w|ŠÂNß{\r};™ø>şxèrÛãÁu5ˆB¸*\0¹àìÈM³©„ïÚaîí\0à{HU·öçCâ¹WŒå»³ÉyB'Í<Ç6ó[“´s¾Ùíyÿî¾ë»ç@Ùï{ïQàŸ™ü>?/<µK@  „À¨B|aH\"„¾ R	á@>~@œBhEL\$ğ®[Š°Sa \"„Ğ‚0ìFe`b\0ˆüÀ‚@‚\n`Š=ÒínÚù.*Ì”îO”èÏ˜´œnï ò¯<jO¦lM”\"mRÊÎğ/±¦*î&Tè‚™ÄTû _E4èÌúÏ8Üğç|R0*ñoÖÊBo>S%\$“ª ÈN¸<î|ÎÅÎ¾—ğy¯7\n§Ì÷íŞ´,é¯¢óğú°¶ì¬íPtíĞ\"l&Tîo—íE05nùüão©ĞrøğväîéĞèùÆÖ£BpğòpËÏ\nÔçPÙİĞ.-,æÔq÷ÀÖø3\r/‹p°‘PßŠ b¾éÆÁ%mĞèÎP2?P‰°ñ@ó°÷0(ö/gpz÷0è`ÜÑgÏ…ğ×Ï‘‘\\å¬³qòñ>ø‘pú@\\©ªuëŒ@Â Ø\$Ne°Q‘¦úçÌè0(A(¦mc‚L'`Bh\r-†!Íb`ñÛk`Ê ¶Ùäş‡`NË0§	§Ğ¯nN×`ú»D\0Ú@~¦ÄÆÀ`KâÃÂ] ×\r¨|®©ÀÊ¾àA#ËöiÔYåxfã¢\r‰4 ,vÌ\0Ş‹QØÉ NÀñXoÏìí´© q©'ª tšr\$°änpì6%ê%lyMbØÊ•(âS)L')€¶Ş¯L²MIŒs {&ñ KHœ@d×l¶wf0Éíx§Ö6§ö~3¯X½h0\"ä»DÎ+òA¬\$‰Â`b‹\$àÇ%2VÅL¾Ì Q\"’¢%’¦ÖR©FVÀNy+F\n ¤	 †%fzŒƒ½+1Z¿ÄØMÉ¾ÀR%@Ú6\"ìbNˆ5.²ä\0æWàÄ¤d€¾4Å'l|9.#`äõeæ†€¶Ø£j6ëÎ¤Ãv ¶ÄÍvÚ¥¤\rh\rÈs7©Œ\"@¾\\DÅ°i8cq8Ä	Â\0Ö¶bL. ¶\rdTb@E è \\2`P( B'ã€¶€º0 ¶/àô|Â–3ú³şì&R.Ss+-¢áàcAi4K˜}é:“¬¾àºÁ\0O9,©B€ä@ÀCC€ÂA'B@N=Ï;“Š7S¿<3ÇDI„ÚMW7³ÒEDö\rÅ¨°v¹”@½DÈº‡9 ñl~\rïdö”ƒ5”z^’r!ñ}I¥”ŒíÅsBè¦\0eT—K!ÁK‚UH´ô/”ƒ¨2ƒi%<=ĞÆØ^ úÃgÙ8ƒr7sÒÆÇ%N³»E@vÃsl5\rpàÇ\$­@¤ Î£PÀÔ\râ\$=Ğ%4‡änX\\XdúÔzÙ¬~Oàæxë:‚”m\" &‰€g5Qn½(àµ•5&rs˜ N\r9ìÔÂ.I‹Y63g6µ]QsvÃb/O ò|È¨@Êy§^ur\"UvIõ{V-MVuOD h`’5…t€úÉ\0ÔÓTõ,	(ßê®qŒR™Gˆ.l6[S0@Ñ%ˆ´¶C}T7æ“85mYë‰ú)õ8ÛCú¹râ;ôØ¦)´M+ÿ4	À ÉÇ4õÌ|©Îª1ÔZJ`×‰5X,¬L\0›7T\rx­çH‘„dR*Ş‡¦ÛJĞ¦\r€Øõ†52˜–Àğ—-Cm1S„R‹éªT`N¢e@'Æ¦*ª*`ø>£€˜\0|¢ğI!®E,¨ag”.€ËcupÆÃ9—`B¸ªaa¶¨Şpê`¤mî6ÒàR~†\0öàÖg-cmO´ñ1ˆ\reIN”QNíqo\rş‡nqˆÜ¶ôR6ùn´Snít¤wÆÃ¦\r ]a¶¤š-Ïa*¬¯—\\5Wpv^ OV`AFÀèœŒ3#82põH'J'nM('M=jÂ9k´ZbBn<î@‚<ç \0¾fe¤:\0ìK(ú™N´õ¼vğõïí-!é—1¶ŞH(›QgôÂÂµÉ—y‘<€’ íd¢\\¥c\\òs,uÖËƒq0­~¢i~ëÎÌe°Ñ¶¢Ò*Ñ~—öÈ ù~ ÆMØmÙÒÓ}WØ˜\rîÄ æ@Ô\"i¤\$Bãòácägä5b?Ê6!wÖÓ+xl1…`¾†`ŞÁ	s„Ø ê÷‹î÷‰¨Ë.ÇvCnhEd QƒÓid\"6µ…´`¨\"&fì˜xµ(\"â2ê˜Qzˆç\$Ä[0%±0lw u×Ú>wë%Ø±‰µ%»w²ZÌ\"-ÿ‹uí%—ì÷ó¤Yˆg±ş>x\\Ë-ï…„×¤¼àà-v—\\˜ıx^'M	‘PùÌİY‘Pëìİù)‘8û%ƒC§ˆ˜@ØDF ÌÒ\r@Ä\0¼\\à¾0Nâñ.„S\$¹ˆYIˆÕCŠI Øi÷>xPÍ¸Í’¹:Í·ò=–ˆT,â'LìùÙÍqĞQ2ÍŒ¼\r¬ñšÌÒÏd¼­‘Î”ĞÙÑ@ÂÑ’ĞŞÒ9F‘¸‹`ùO„˜f¸O•w¾\\hØ=¸}S™jGGW„‡˜AˆíL‡£RJ\$JP+Ó7˜§ŒL¯v,Ó™(Ìµ˜ÇZPìg¸ŞÔÚ&z+ Şájƒ—àË˜7 Í·¦-vAÃw•Ïh Ú^9óTöODù——Z—ºC˜¡˜mŠùŒ`OŒÀR¢yÓ’Ïì!ëGvzs¥˜G•\$IhYñ–•ç–Ùı—58¼¹xFøõ§ø«¨Y9¨š©i…İ8´šUƒöC”Ú[‚Ñe«‘«Zq¥uA«Â1§šÂ?ù…’ÙˆÌ9!°Œ½™:Ú“˜ì¼Ïøb0ƒ˜{\rúQh`Md7á{2ƒ Û²8ÖH`%Æ¸ºã{-ÁlÊCXk³H¡šÓ’Ù|\0Î}àX`ShÕ­XÊÖŒ»\r·™æOûy“¸X¸¤¸ :w7±·ÚÄ×nÆé²ŒÒ#û/¢:4š(MºŒ;­¢øc»DŸ£z;¼Z3¼»¹£›½¢Ò]‡¶ç ›Ø?˜.ªÅ¹€˜\roØbOì¨^`Ïº¶|ªë†×÷ŒÛÉ/ÙX×’]¼|Œ›Šü^œ!%XÙ½³8ÕÃÜ\$Ì;Äáz¹TåªxK·¹-~² 8X)<!Öèyï–x«9ø¯ú·ª:ûÄ Ù‰F‰†‰ú—’®xˆz+Uàºƒÿ¶¼šúA¬E˜; ª'Å%c­ùÛÅYßªœ³ªüw¯<{¦õ9ŸúøV:ıŠ`ÍÊÊ‡<ØáÁüG‹Ø¡ÇYõ¥\0å„Zü÷U Zq\nmx¿)_¿}YÇé_©z›­ ù­y\rÒYšÑ,Ûš3šLàÌÙªÑY²ÎÙ¸Ï»>“MÒí	œMœ™Í	ú)œ¸P\0u8 S!Z{Y¼äÔÜ9Î¸ÙÎúfV3oõOÏ¼şE½Ğ`CĞ­ñĞà¿¿XU¿Õ}Òlw©™0´}©­ÒÌÍ™İ7›Y3Ó¬Ó”Á›4ËİG›İJĞ¾&¹Ã¤ÙÆÍ­(ÙÎÊ-AÖ€V=f|ÒØØ@E/ß%\0r}şŞ®nnÇ\0äÇLy¡Œ„‚¶<+Óàö_¨|Êë#‘AÅö\"C[yÖÚEW³ŸérW²€f(\0æ¾äĞ›“>À)Â ŞÀÌ_ÈUëÂ,UØ\\ù#ı‹eˆ½*r›`ÜN‘YŒ Û*£=aş\\›Ö&œ^g4ŞmÃ¼íç¼ıØe#èî^°|Ş‚¡QXNÜçæüIç>©ç¤\0rÆ‰şí4®š^YèV#æ)éşkì>¥×¾ËêŞÎ™ŞÔšFÀW^…è’%¾İ’\$+˜ÕPÕkY*u¢~ÖÖ,¶ÅMÕ×WÍ‚hhGÀ¿K´\\C¬é¿7HmZæŠÖÀSZ_UÖ%æ\r­õb)õ´gg	qñûíö™ö@@ÕÍëóİÎ…tä\rJ°ÇàÛ”Ó×7sƒÿ¤¹¯”U¬K_1å÷t¾j&SåBi\0ØÂØ &\r¬ Ò`ø:µjÒFÀ~=TÌª¢¾gÏä¾‘íö!û›æ^hÃ^í×•ğ÷—½ë/[{ùB¡†™É(æ/š|ÍÖÈg•„ñj/Èd\\Ş–SÉ—ï­9¡ÁŒG`‰Œu­Ì1ÕMÙÊ?Éı§3}òQ\$qIòm~°”ˆG=‰êoVz¬\0_põ§´!tá„r{ÛÒ^Z&§ü	îüuÓX¸ö1@ÀĞG{äøõĞ¬¾	NIÒäÓÂô¨\$=0ÀBu82ÆS\"ñ6¸®Qpjëov\r<®ÕÉ¶U¥\0.ù¯Õ¨…EÁMÂ–\n8VÒoQ\\…`?ƒà¼L6¬ªÌ=\rŸl±¥¨¶”±ìÀà\"øàëõB2puì&\0åëÂ5¤\rj¥ª0VËA¼µ™…;v\0eH;”Ê‡TJ¢Å6pH?/\\àHµ@!pp¸C¦Ê+5„\\+a™8;’\r(*’³TÇÆ¢;ÉOŠ|¸”^Ld‘&/¨ñNI¥TÈô|#Èï–Gá©`j%Ç—ŠäDÔÙÛàZƒÄ¡4Éni€i­ 4·ó]@tÆÆ#5cõÄ¾÷ğ	ÕZˆ¢RñyR`@à¤\$I{z‹ÿ“’èƒ‡ïé4|’ ¼¦×‰ªÜ€@=hCEöÕH¶, ,ZîÙêƒi‘µµKˆºÃ P¦|,g°z*’ÊÆñáE)AjknK\nºÀC\"J79À}4›f¢€”ƒ*´4ë65‚¶Ãê­×«”Q\\¡†Şc“˜MáÑ\r‚{*Û1j¯„­è­lF‹Šmğ4¬ÅM¨* `âX¹GÀDÀA-qqab‹´±1ª9RÉH’¾Åb¡g8Š+¬l/³œ¦äô¹Å„æ ( Ê€L\" 8Èíè0(Dc‘¿#ihcŸŒ‘`œÇ8‡±¹A1‡\\ùuK(Ğ4!¶‘“ˆŠšd—Ò3¢8ŒûÎ¾ÑˆÑÆ®4¢j;#¯ˆÃ˜€ñ¯Às8ÀÆú5,ucnc€FöNˆ„pPa8ÇG8êrK‘–ÄÒÑÆÇñÏÚk›iÈË•4ˆA€	£8TÒ¨Æ26 ;*iãX‹‘—Â2%MàBJG² &íC*1T\n4	-#è.×%ÆÚ¯'z„#Ê8ó˜A+€@S.0Ó×€üõáII`UºQ°ÎUŒdd\$)¤È*]¼ÍíTééãÆCèè9M*…ğ	\$b+ù·Ñ½Î‘Äydtƒ\0-ÂùL‘ü8\$Âe\$ƒ¯Ú<AÉ!ëd\$p@]’d£¸ß&ÉM+2E’´yßˆ((_|ÅMdÀvU9!ÂeD	Ñ(úªW=„òÆ#øàÀ_é'´bN;¡'£¡\0²O¡<L†iAÉØ Ğ ”TŸ€¸£¡\0¾QÉJ# }Ba(ğ/ÊuƒGB”¼Ÿ%-)€Êhòu„´¥€~\0™IæU°•Pr…+1©’š’ª¤¤%51à„É’L`ÜE'(/€ÂQ‘Ã”¬£%TÉ)9ËOrŠ–TŸã],Ù?Ë<ÓaÕ	„¯Â‚œ€/|À\$Oğ@Z ĞI®XNœ|±%“,¹SK:]ha’”%¥ª)kÊşP\0,·¥»'à0J©:Òÿ	ä—Ã&ô¾šİV£0œÂÒújÙ‡JM¡*”x£ƒôP)™¬jKÈR û¦\\\ru\rÛ(ÃWÔÙáF: k‘ºğ„\0œÆNJ€˜P!Q2 'H *\0‚g T|ÎÀªŸ~g`D,ÍÏ¾\0#¨	³;(\0 À ÌõL‚šôÕf¯5'ÑÖ`'™´Î&t(‰™LögóA™¤Ï\0à'Ÿ’ksi™ñø&‚àÂødómøºP\"Ng`O›&ÄË˜  X@²	£Í%shôäg_Üsb™¨fÏ5ÉËM>s3›@Tİç77À+ŸònS”šdÓ§5'6s\0\\œÔç\0O:“ğNLS@ Pæ{;9ÚÍ¶p“FœÏ@78_Šl³9°\n¦–)Rg³9@aç—:iÛ\0şvS›Dòg®ú\0¸SàÁù\0øsõM\0BÈ\0+O“qš`§×>ÙÄ4	 T9Ûç7=°MâvÓ=qø'y;à'LîfàFšïf´) Ï–wP·TÓfÍ>\0¡OŠ|ïŸˆ€?0)OÖ~à|ŸÌşçæ`#N–´ \0¦ù>€'Ïª}“Õ ¬Íç™>ñ€¢~”Ÿe	\0“? *P…3¡\\ÿæ¥@ÓÍŒô5\r'¿C‰ğP–ˆ O¡E\n†MBÊ#Ğº‰T;Ÿµç»=jPŞƒ49¢¥çùEz#NÆ‰Ù¢”ÎÀFYÒÍÌ\\Ÿ½\0CA QJˆTVŸôí¦˜©­Œé7 \n˜˜vÓ0@–‚_®Q¸LÙRRc!Š†VÀ|”zÒÍ6¿šKKÑîõeS€£Š‘„4¥É\$„aI€ª|P„•A+‡¸.qKD-çS ®EvbCOŠ>Š¡H¬ªÙ<áí\r#äãLPÜ˜€sâ¥ºPÖ­2ƒ0˜ =¥*ÀWL‚“2dàt¤ \0Ø!ù<	àb°q€\\pa@‘Rd o™fKM İÓp ¡±§\0}ÈöñêzŠ\0Ñâéá2€Õ¦š3\"™ ˜)@\\*gÓrM#!Å<ÀÉO•XT\"›`\n];S CŠ…Î ”î5ÅBÍcPÓ²  [¨¬É\$4pÔ&¢•\"¨àiÇNPïÓ Ş'J©\rE&8z’ÔpÛ@>¨áBRÀİ›i\\‡¨uD*vzº‰SÄ‡\$*´ÌTZ¦\ndÕ6‰iª¢+˜€•J¥D‘1I‚:€=ĞPÛäĞÀÉÊÍ\"q@| p¦övjoT@SSÉÚ¦ìÀ*ÏƒÃ'8\n#äù‹Ö +`É‹îªœ†ìSC!âè:Q’Îjó‰š|ãgXÑ’Ï°dç£¬%aŒXš^O’uGò¢e€ø'„óp\0{VúĞ\0°gQWxXæZ„o>‚“×B'¡= 'Lä)”v Õ\07ŠÂ…1L…¶ªkêT»BÓR”t§GÕ‰,Ü‚ §R‡MZRWL©UÖˆ	çÅK	ÚWngl,T¢PÈ\0­:§`*Y•tSW\\à`\nèÀS_È_KŒîkh&[åª5\\Ôä\0ºÁU¯‰® şÛ\0î«•^\rŠrC\\±;Ø5EÔûÍ?W˜%à:†¶\n!PZ£öÌBwWW€¤\0{Å7ê¢\$ò+[Å1h˜B ÚĞ\0ºÆ63.kw,l0öØf¹¶:¥Œ‰5cĞ2XüFõ*ò­j“2ŠÚê©Ãe…eeÛLº·&±²}7««,2¾Áß¯Åy]D\\Œ¢G‚å.g2Ä8\njÀ´][]ÑM	dc†Ô}{ì£Té”˜Ş+xGÙs,¢\"”€Ü:vQí”\\nÛH”7ÚŸ€yu~ ïX=BÜ*Ğd™QEsëMÙæÏu?Z¸FHü»N¼,ÛD_±RÌ\0Mhç‹/„~­y”‘|C»V©…ª^™5R„³2%İZ¤ÇV„•s*lo{,[‡vB©µ1\0Æ¬‰•>×ôì/º†èëZéR™a\ná¶áëE¼ šAØÔ*Ãa¸2İ\0aËë@zç±\\70àar©­ŸÇ©ÊvfXØÂ°g8èZ^6g1ÀN©o›¤9%÷’S×ît\\çOHH…îÈ\r‚Ş\n·ğW^&ÚA‘¡ö/°å4­{2‚˜<Òv•&Xi½_šòÕÒ¬•f«ÅZÏVĞ·”\\–tà&ğğ0\0¦gbí;J‚I˜,Ë \n)… .R«\n•T-yDÑKxÂ¡_ /‚ç¶x:¥ŒH®»W5^UQ5ÑësUZ…•IUwfFù¶£¡RñÇSœÆÔé±}ƒDnzÛ¶FÈ´…˜­\"\0\\ó1\nvİ÷W¯++g2Ñ€€µİSR’KªC¶bÁLÂ%&=7ñ\n	6¤Ç\0r )ü\n>g‰1Ÿ¼ÑŞ„8)Xo³÷ƒà\$U€;ÄÆPèVŒ¯záJö¿}ãH;r+Ñdx„‘\r-ìÖ%BA&\\;Ìå'ñom|za<nÃp =È^=|µ›8á'@•`6„·¶ˆteDğá)}qŸ_d9\n‡	AŠ…#}µÚ_0¤Ãm¾™`å{âê\rÉrc>·ç¯~‘dß°L§?b%÷/—}ûú\r¾şÃ>³;\0\\£`ÖP\"-!Ûn!æ¡îş7ßˆÏ¯ö71Ì¼@ƒ¬¼MşˆÄBŒ‰*ùA«jnÌ\"`jû·òJÒ³ˆ°°1 E§1m6¥ú/„GÜ_»­b Œ˜sœ_zù£™'~Î]L:ßæ\\ùÀÒcF”a-8()éGÌ©‚añ®f,ÅÆÁFp¶E…ÿø?¿XÍğ)2³³Ö\")pi€‘»á4½1ĞÂ‹üªõÃ…½±^¬¹•pßX5—ÒÀPßíËxòÜ‹ƒwÂ	Ïï·~@ŠÉ5w ¿óÇ±&¬JáÁ˜zÀjlğdÌ	`P¸©Æ‡§‚²\"/d{8†¤şÉçXŒQd)8ÄÖ	qx3à–ÛÄÅ†b{…,1àä˜qMÅ†ˆ·>D\\X–3gŒãââgƒˆÅ¤níI„¢“bá6xxĞ€Ò3¢büdnU^!Š&Š|k“	9=Yv±¿ƒLqÏ\"Ç:²ÚDÚ+¼ÓßwõÅ×yŒÜ#áÖşêë»Ìt.òX|DŞxn÷ª¾<qI|HbùƒÄ•ır-µänö—°,Ğ„Ë:aÛİ^Ú.×ÆÈF×Äü[_Ì­÷¿ªú/Oz‘ÿ8ÙÌøø£\0Úğ;–åÀlÉHW…“Ám´O(¢‘½¨WŠ¥‰É'@¯\$È°ôÇ’Ct	>`tQBÃş²÷“€Ä¤lmÌ‰8JâO’„¶m%`Å^Kp1³ÅQÄá¡ï2KÓ\0ü=¢Ëy)2×¬µ4±“PšÂ7#È³Åò—ÒÄ‹-TQ÷>£Ô.%\rÜ¥ÙÒ¨²®ßkÀ¸6@Şf@ƒy¾Ì^ô ;ÌğÈö•¬áHKÃ>Êh)š\"©R2E(Ì ³ó-‰H¯0e.p³ì\$¨Ñ-æó7%¨)voÉ\$/¬º‰+.íÌÊ÷ce‘;Ñ^’ø™Êd­Wyåöv¤™@q 47Fy;ÀlYï\n„xJ™å§òªóĞñlÓ=…ÌxÊ&-àò]ü*d}WA@.OMÇï;<Ú©‰’\09HlàD+\0¼\0Ñ&ÚUR9øÛÌÅ“ŸÑ&›Ğ&`cÏÎ€CŒ¨DäÚlaÏ}Ü©¦=h(öD@Ğ„GÀÂ†ÛhP¬W«fˆ@ğoB'ÿ@š&H\0 À;è;EºNŒ´-ŸÌzhoC¹9Ğø4mM}lë z&‰4v½\r{EzˆõË4`?\rh{F«Ín˜@Ó¥B'…Ò›Ñdƒf“\0èEòB#xZaÓvˆš9=7`‰Ê¨°Ù—\0Šâ“òœ\rÿ–‰Wı½¸³>jª\rÙI€é™ùŠf¼!Ğæ¿Àš“°Ò+S‡ÿ<eÌÔ6ˆæiìÏfºòTˆÔ¦?š|û¾jH’Y™éZ5Sª9‹çêğ6”KN§G}+êxàBğEn)Dhèˆ‹ĞÓøn[1¨–—gÌÊ™¥“_Ù}ªvûØıU§ĞIX\0˜à:kSA@R+üû‚VÍí?åb“šÂ«åBÌÖjBò7“Aæ¦X†p6j#GyñÔXÍ\0æR¯èXf6ûC@r½²âÍPáÚ×Ö¹uødÂ€à&÷×&@ó¯Òëü¢zÈa&²•Ö%|hŞâYØGğ/±ÀØìy(Å¬ÖÆ@ß|,»d„áÙ5ÛŸ«—0ƒl›eV™@Ş·=’•}^[+Ù‰Wï‚lR±åPNà,Ù¶\nK´Q\0¥\\0¤0ŞÎCµ|ìÔûi»,Ùö0Í³]|íOg¡ÚÙÙ©¶²qŸ'ì€{Ñ‚UD`‘eLIPæÚ!Wö¯ítIû_ÙCğbÇ„ıKióixÿ¸‡‘Q;Õj¢zÉ)¹8 2–å¥úb™´¨Ğ\0ïFO/%ù–cªıvóL!öS^\0’[ñ˜Tˆ!ÛšÒñÖšà=hÙ`šF\0D@Æy2E\0ôÚŒA µÕšI’DFù¤nf³M©tíf¬i·ÕQb±J`êO\n©¯zÇWV“&¡û€(æ˜À(“PçŞYGH—…åµ®T ûİÜ¡GîXpñI‡-\$æ‡ƒ@=ï,†L\\›øƒ½¸­¼o‹/÷T‡ˆéªHêÃw!ÏúúXMz¡ÇãHÙû™:wK¦-;<|×{ÂÍo\0uØXª•dÜ?\\½f›FŸ{Ë†¬IyÜ²öI/qŠİ¯Çè3¢(İSÿ@\nä½¨’Ìà¯—­w¿	)	šmÌjÊÄïTij3’ûX¶Ô~÷â!\$¸Œ@»GÆ(—8oµÜ)ÍS!o½ä`{¯~˜‡It¹•ªë‹<>Ò8r7\nmÃ–|<¥¡?EàŞ7\0»9†WğæVk¾8â¾û£ìÊ÷%ºQ¸aÜs}—ÕÁÁÖ7‘±Ú61IÇÓŸMÉ]İ/´0N#o«‡Nš4àŠ·ôÅa-Ï#xœ×næ÷N^bšmÒ¶æuE’¾PækT\\à/€éÈŒğzõ÷¶áw³ïÉ^Br_€¼-xöE3¥ÀN\0+)··ÉíîxS{Şdó,×üË%ì­²—’»ÛB¿%´Âá½óÊßÃ¬i“_¾pW¯ H,OãzLoÜQQ7¡qŸ<FÜPz„/i/3v tÆpñDÓlíîps¬©˜šri€­k²¤W pÆé:ºp•Áº½Èf°¡}\r8gA‚“ü –+:\0lÈ®tğ‘¨ÔS]Ãå©§Î½\$/dtœ¦¹P-ŞU¨ØŒ„u¤D¹÷Ê>îë‡Ü¶åÀøWT®eéãƒü¼½\n'Âp:e.÷„?2øÓíGrS?<–àv’ù9¤åæœëœ{÷2R¹¥­ó«ºRÓ¯X8%Ç!lZ˜Šât,¶M¬ÛSfäKwÁÈò8_š>jsl\\İç77ğgÎ.ó¶İÈß:^e“Pö‘pL¸K#@õ`\\GLğ5Ù¡%îğB@Õ'zQm¾íÿ˜ê©m\$ºö3â€ôŒ!®\r†V\n‰œÕ	ƒ…İ\$íYT…ç×02õÔıwÁ% ËÔ,S¥]¸­ğsà:uó¶dc\r‰¯3ˆTŒá»7e(Ø¿vŠOr/õèhe.&0‹ºûÌns#ºåªïY>#)™ù­¦c_RÑˆƒ…q¯§Ğx\nˆìZRjp5ÓránğQ¡Ğ÷?½ =ïiˆ\0xŒ*¯-ÁˆàÆ{ø.û6F?#1G|€à\0.†x#*“nc>´`8k£%`S¼SáAŒ\rÂâœñ0ëü7°\n¸àmºşÚœ[ÄŞFõ;èFğ\$\0TÛkæ_¹û_ªnvè>8´ÿ¸ÂÙ2Úüµ@íhŞşWÚØì©œ,m€b*vœ\"ÇÛ0yvª¤p [<Ò#ç/®!+{ ²7•v0ùO®Å_7ê·»¨@Ìø\r5~\\FÌ°ÂÚNºyï`Öz³}	²o/ìÃĞ»UóÑ»—æÿ8—{®#ZÏ£öuéí§í„–Á¬·xkÓĞ7'‚˜{de¶©y\\GÕôıúÔæÄ¼ 1¼  d\$@•9\$M¿w6ô›W_dˆ‰Ù@•ö\\= )wgsPÈé=İÿş¶ˆ=së¿^„ˆ töÈz§g¿hí}C­¼Ëé‡njz'¸{YÖwiù_ë}×ì4¹›\r¼3Ù«‚\"g¨9óØï!rè¸:y™÷§¿àº û%ô¯‚ºëépåCõÀe¬×¿ˆ3 \nwd€˜ÓÇÚ`–pXR`ô]Lå¤ÃÕú¹eÁjDjUt×¼Áw±>–Ì_ª«+±X=·Ô^BÆîÃ\rÏKşb¾ßSd4ö6I÷ÏP´„,^9¹®†ıÃ¡u,}0—Å·x¶÷Êá\\ÇDœ!üÍÊÓ:kë—…”˜>`1è`:°ºPí_\\To×‰\$F&ıT— i0Èºı°’_nàJÿ÷/[Œ	~ïŞ{Ÿ÷ÀÛ·p‡ÁóŸC>õºnšå\0¤Àf×@ØÉyÇıóä[?®D›•ìG\0¹©4Z\0Û=õŞ,¨!ÀSÕù¨I\"ßÑ²ªÎF]‹½õ_%ù…kß¹òï_\réÜğä;1¶v?€t¯T®ä\"^ªş8mÃêeç^S7!È÷äÿ«Üş@†°F	³xÁføĞ‘^#ó÷'¼îoqûÔü¤›Äš¹äÈwÙæÕ¬E¿ë.ëóO©†€ºÅY£Éïşô(o³è¬ø‚ÊqNØ‡şÿˆyÄ¤Ïp[nw3:İë´ŒÑ;L»Ûîœòõ\0ı;*õP6)š½*È²¿Òè¦îUæéĞ´=Ô\$Šò¢\r“€V%\nRRA}‚”Ô\"Àf“ªP = 1Ø4É=&:>‰\$€^RMp†ö§\$IL\0ö1ñèğ¥€”ÒòÁ}‡Õ‹a	HÀz€‡	¥²	™£@ 1ÚVÉ=&•L	A	@Ì”ºT«îí<\r©^ÀjÀx.°%¬p.ï\0Q¸”£‹9\nø(à.	Û@†¦`(ìJ\0Ê¦ˆ>¯Á˜Ô\n÷!@2DÀÖ’´\$iE\0f¸@…v\nˆ*`£¼d’Wás€\\à|À>€H *©ƒ@ª¥ñQÇjš›ÚP‰;À|<|!IÀ†•È0#@=)ÒkŒ˜ºX0WÀ®1ØİZ@æU,D@› (P_@’t¡Ûp)AÁ/°cŒ³0-AmäÉk˜Á	P2ÀáBQğ5€T1A%{±©©=\$”PI=K(ğ°TÁV¼°*@sAÒ¢ÑÁ•Ü¦7@¿YƒÇÁÌ•\$\0•ÁÕ`.°9Áß¤ĞA\$ù¨Rp{¤ü¥„PV<Çô(OÂ#\$ po¥°*dPNA<`Ä AGÂòğ‘@€TD\$©I¥QBLN©C©=\0¶—Œå“ÿ[°•„<'A½	´Éi%“ôĞ£\$BWğ6%S	„\nĞ™ìĞL&°@}Äp„A	\\ãÁÓ„`¹Â#t£Ç­Ñ	ô,p&'z[¨ÂCŒ	`5Bº”¤\$`áAt•BS07A¥L†æx	ä\$æ	€û àĞbBA	Œ/æBöU,,0\0Ş–t¢2@×l\réRBÓi/°‰˜¾ĞL.rÂää.‰_€X(”Â\rC\nÌ/¹B—\nôƒAÅ!02C5<,Ğ‡BÑ€.Ğ¶<x–ğ”À-€tê]PWoÄ€ù N	ààÖ²‚×@ö‹J€ŞĞF@¬.ş<Ğá\nC¢Ø`€ˆˆã‚Œl—¹€ş\\\"ç !€ğø¿Dœ;`3€^ \n@€°'¸	C`Ÿ€„š( ™(àĞõ&‡¼\$äê\rL:éş¼ìŸÃ°¦óÛAjCíô<À<¦vQ‚À0€§\0Ÿ…CªºXH\0002€^;]	~œ†â°Ü°ò€cüD1–“(5Â“(€íä„49ÄFQ±ƒ ÄMÔE1@Ü;ë?D\$kä€ÄAD‘#DsDG‘D˜ôIÅfD…J±#Ä±TKQ .´¢ÁC–üH€©Ä­<Gq\0= !¡jCÂıaÄq …5	°ï²LúM±=ÃÈÉË@€Oä;­Å\r@ŠåÉE\"¡iqÄgO¯šŠÃ49éTÃâ+ğúD5¼?0ı€™èÑÿ‚2¨§AMD§Fñ7Ä‘äE€7Ä\\üFğ´R·dRQ6DÃ¼N1ÄZÄVŠr³A+vE\r\\M±DáLV1YÅkOñcÄkÜYqXE|X@ãCÂ2c‘iD©lLQ^Å›Yà'ê¥QoEY,\\1fE¯\n¦ğ¡hƒÌ]QdÅ]WÑYEÉÌF)#“h×põÄ<|*däÅ2x¶à7€Ş;¦©G€^€ã\"„\$h%Jv\0ÖXâÆ/¸+±¦w3GÅšbÓ3ïqüFq€¦5D9YªÃ\0Á.@øä€69\nt+SEÄ*°³Ô4\0DàºÅqS€é¬¢x™8±¤Ó\r|g@Š!_Ôà1ğ‡ÀÆ\$4áw­~Ğø1€¤n¿r¯\0*\0†#E±°ƒ0?	€'\0d¡ 	À’(HxàFÙ \$ƒù\0 Hş`(¦€Š±¾¦x…GD†tHë!’\nÉ ¬ÆŞ€FJJ|á6à>?)PBQFãœqÎ\$`¬\0>/jĞT‰\rˆÂZ\\sã:	(+ ÂEnÈ€!Fq^µÀÖ¬s1n2¢«dD1×Çx\0a/Ç~Fá´|İxHà>4Zä\\À6ÅĞŸœ_ì5z8•ñyƒP?™¶Š²‘†4 7ê¶\0¦œ\$y€0«Zî›¶ >\0‹Ä|ÆæGÎÜs,¥ÇÖ ı±^DS|s )€ò¾Xú5À›q1ô(LG#ƒ[ùüæ'HL¬i[½ H&H	ÜÒ1 S[&y°àK #G¸fğK!«[i\0#5Ğiü+÷\$N(î±É-JfğŠñË&9.E¥\$ĞT åÇB3!pñšÖ\$¨0 í´ôr†‘µ:’‚eÇ,º›Í[0ÉÈÉ1Ïº|¤E!¢€ø\$,†¡,…KäŠ@; ÊÀ0¡l\\™~\0¬L1Q€£Ùˆ\0%x@3ÈÁË× 2G`L[ÀÜ¯BÖÊb †€š2d…L\0èÅI›Á…€ß¹›Än+r³î€;È\n:Èà7Hî\riüàíŒ\$lOíÅI4Xd\r)D1ÔrÒ5)Ù„Q¢W„JÖè#±êH0Õ€<À†ˆú%hrEƒüû ‚QH¿\$Üs#	‚Ø™i¢W¢f¨gHa8!qÌ¾Ü˜D ı	^HğT±Ù‚9%ÌÊ\0˜àÀõH×!Ğ*JGiÉ\"¥ßŒ,MˆÉ\$Š´øÅÆM Y\"²IIÂãX;ÉŸ)(ç£@’s·™ñPäö\0ØÊšŒ”/a'POïğÈğÿ\0!`2€®ÀÆ±ÌÈ,™OñÊr²®Ë€ø-Äœ€::L•²„D~-ĞC¢_ÉFèR]HF 	òx9TâÒr‚‚²²Hb*pÉ[(ëÙ2´B\rÈT’’;-(üGÒ,:Ø|¤ Ö\0¬óœf†¦ÊjD<fÁ­p¬›@)(Ã“òi2ò(üšOº¼ü[¯Àƒ¼èL€ˆ†è°Ñ2¥\0èû´š'ôH1Pí„g Ä•®öÉP\nÔ•\0úÊµ%k]–\0Ù(b H¦(ø›aHH# I@)€Ò™\0‡Ò)Œ™+CşJì\rl¦oN\0øÌ©Êş¿¬©’Á°	ünŠg,0nòª›+ûH\"“I¶7\$„¡ƒItªà\r <Øa0H@‚Êå+¤¬¤)Ød°!a––T´rÂËD«Œ±rÕ€Ù,p\rD–›`MGXŞü­N=5 ZÆ¯><£P/ì„^g`‰Ë*+uòi%m%Ó¦âe€‹'L ‚ÊÔšÒ\nK.›QãÊæ),œÁË«.Ø!Ò]K½.Ô§¬gKº\"4¡\"İˆ•(”¡Â\$y-Ã¸ )€øW¸rïÊ{/˜M²ûK¹/”¾’ıc/Â£R ,E ÃQ¢A\0„®œƒ\0‡HQ°BL\"Ò¤†2…/\$ºî¿%/ô¾3Ì/0¤¼2<L,Ê¬s\r@	-óùâyÈ`;ôÄ2×\$N|ˆY—.¤Ã†oï0ÄœÓÌc%ÔÆÒkË¹+¼¾@Úf Ü²¿†^™s/êî½´ÈOêÌ„Bi%M×\0×2º\"W¿‚ş+A³!\$‰)€ó%ÄÈLŸª2¨9/‘0”Ë t,Ì33?'„ÌªyÌ¸CófšFûŞäØ€Ä§\0‚T å‘€Ø6ªpÈ‹3òª2¢†ÄĞÀï'ÚÏŞëöÄQ#ØÚàıH\n¾ğUL÷€¡/¡›ÄÛ·\\X	áj†šÀCãÍM\$µùw²T\0ò)4ÑU\nLŸkßO{†™\"DÔ!ŸÂ\nôÑ\0Û‚¼Ÿh>!?®G5[%¤òtÑ_(ŸkQLó>ÖÈ«IŒ¨¼”ŠáÉ™\$,fÈiä¹çñÇ¸ÏdÚ²Ç¹#ºslµ³à\n²\0IÒJKÃé\rÍm%09Í³Ş÷*·!­E9Ä­rµ¼€*i¯ÜÖÌ2SÄw\$|‚\0èJÁøa…¥ƒj‰[+£†à)Ïd…K¨T #F8(¬aÇd„§Ò/I.,“MtHØ1ËP!H)¼ÑóñR°N<cõoJêÔ¨€Œï8¥j~É¡,{ü†Z¤@\n“†?Á8q²ÒÎi9ÄƒJä\$çI‹Î8S´\0)GC9šbó£\0îÓé²¦³Y%iv‰\r©¼)0•à…0&	T€ªÜ¾ŞLºGô|§Ğ\0Ç-q2„ÎÇ+d¾o©ƒ8ÂKL-D¶ÍDÕ%<¬³t^s\nk;Ä­&o\"^8ñ:ÌŠİëP\"–	é;s[3ÃNã5° ,9Ó5˜ ,ÏücÚØr_±Ì€àY€à€c-<Ÿ1Å\0”ªÏFd°±„e-,ßÓÎâ¨¨Z‰–‚§-Œ“òÁ‚eH-’Ì‡ÿ=”±îˆÎ_-ŒµÓÌ‰_-r# <Mƒ-B›ÓâÍ‰>@\r“ãÎ¤dØÿŸÀ:È5³ÌšË.‘@9Ï­¨\r“ëE,`(81ÕÄê¸­^ËŠ	P2\"‚¦+q-N«&šDâ“™&!€9\nô+4éÁ¤HT\r|Á§ó‚ê4Iç²#ƒY°Z€;½w.ÄšÁµMÒ¼b’·Üä»œKæÖüsÉ;@«(.‹Jşİía\n\0ˆILsb‚\$ÖÉ†ÉN\nÉÈâso	_3ó8Ğ5-tôÑĞ/-pC¯¥;AŠ›Á´³)ôµÔƒõ?ˆSùŠò+˜\$1Ô4¨è³‘ÆDË\r]\0002\\\0Èü£ÀÖL¥)È\$¾2\\›À2MÍ2«Ñø‘(¼È@ÛĞG2àÓÄƒw>dÈM1PÓ8ms!W(¸kQQEH„TÏ\0ƒ\nyÂ`ÂÆÔÅÙÓpÈa'„ÿG—B«•'ò™¼sêHHT|u-×LåCØBñÛK°\$DvÅ¸¦ğ3úË°A=¢W„.l;!,˜B”»Pé/ıëîËbÉÁjÒL/?¼»û€ô•´ñá€«5T[Qp·d»[€‡D¼úMQt?İ´^ÆÍE›ÔQQ[Dğ?T_QL)-€:QEUÀ!¯CF\\ÅïnJ½LÎğÌ•¡»ÑˆilQÄ•lÈîôø\rÑG\$ÅrµïEõTyÆÍFíT{QˆÀ4gQCG(9xQ›F=ÿÑõG\\±ô}Q¯H˜`7ÇmÄ¬ÊÌ-!ñìÒ\$À³ûÒ 8%TR,R\$lÔ…mCĞÀ¤XdëÒF%\$ ÌTƒÌ‚„”É‹ Ñg2V‚T€!dÀ4ä(kT=²à+À4ÒZÅÀ %ßÊ~ğÉ”£­TÀ‡¯¥+RŸ;¨80\rŸÅäˆÃËL^††QZXQÔ6™ë8}¨ÅIŠ9\"dewK8;tŒÄF]-„JOôµÑJa¤Z\$`~ñãVÂñ\n˜“€-§0¢–AKÁa‚pÖ%X“\0ë&üÈ)‰8ÛZ ;ÏŞ0…A?IÀ¦Ø#\0ãGà¨M3³ ÉFü€¢HÄÔ\rŒñMõ'HQHÈú\rxòVƒ^£3ùM MÌƒD”o0\$ÄÁêÉ0ôyÂ“RŸ;ìŞ0I?!(DRÍ°¯(	†ÚGºO®‡ó3\0\r+£È`RLø`üÇQIü‡óšĞT˜r½Q\0#èk@ ÇO(aÓCĞ` mjÁ”ÁŒ„øùÀ„AÌà\0>\0F%€–)]HïÜ\rÆ¶ÁÕê] ÉB­\rì3°Âf4Ù&\rRğTjê\"Rğ]/dúŞ?ˆ\n`ÃÄŸx\n@ ¦r?C@(˜\néúT4\n@à&\0@@	ræœ\nx©ÂÊ\nd?\nÔ\0§PòzUOÈ\0\\à ö (\nàÆ'æ`DHd”ĞbÀaRD)aÃp1ZÛF—ì'\0®\rO¸´üÓöâ[áKR÷PE@P´TU@ÉTT}Ag!¥¸˜@ C”©_€v˜ûsš€Š­ji¤Fæ?Xı \"€ ?*ˆ0ñ¼&Œg!õ%Pm>ĞİÓñOÕ?•.Óş=,,ğÏT e	¹bÈ\0^ì(9ÆqO˜}uJøÊªKÔŸSJJP/Ô¨	åJõET³TeK”ÿT¿7él\0Y¸'•MTßP´úU)\n¢ZµMÔ–BÈ\0‚Õx	åWĞ»¥AWkÃ•ePõ]ÄªU0é-*ÈùÀğWğà6Õ¨3\\;¬ÇF=VY—õNƒå4=õh™åVZ±‡%VÍ[`6U»,A!M3â)0­…´™İ^`9F(ª‰ÁÕ|¿W¡xèYUûWiÀ<´©WÚœ5€NH­^­ÕîÕa„ÜÖXa†H\0É?ebc€Z‰ÀŒ·XC¦µ‡ÖXĞÂÀ3‚¼„ıdU†V+\rdT-gXÙyµ…V\\…dIVD¥iµzƒYXSHèVdA¨ËWÕhg«ÖÔb‹Ú€ç7È9…+\0«ÖEZxõšÆ}jc@V«XåiÕ­\0[X¨P¯ëZíjìÖÉZª£5³€Z¨:3<VemàŠİXˆ•‘ı[˜Oî4V‚h<U¼L[­hÁM¤\0OõšÖ&öHmu§,j/crÖ+(\ruš„ñ\\\raf6VZ°Œ‚ÉVíæ@7\0[X½,4ÓÖ\"›ÁéŠİ?¨“U…SZÕƒL«ÈV`\roUÊHê¬òçı\nM<r`¢€G¤ğ@/Å\r]˜K äWlPè%C75ßE&-ø\rÀ<Ö`+s[â·j§ãî•ÜŸW’›Õé\0Å^¥kÊøór€Z]%^ <WÁ^Õ{•ë.‡5f€1WœÉø:ØWZ™¶€À&9^µwq:W‹^R½uú×]©75üWy_ÜPÅY×`Z•ş5`0[Uü×o_ÙViµĞl¬Õá×q`=V×~¾ìê@À?+_³*6X2•BßŒC=GòØ@,•„VØ5^Xñ¡¼X`Å…ÖE\rBx!Í2ƒY ¬¶´/^=y6×_Ù64‘`my6Dhšgi¥z)5…C\nİ¸J•Ö[b‡v\"Ø¤ŠÖ_­‹v.Ø­\\¥[Cç\nÜN!%5ÏÇ0e}AM	†(˜gò…±b2dö &9a(\"QFÎíc¢¨6\$Xï0e•¹Œö\nõjÀØVôÔVAI½ sÖCVE`â>ˆ|kš–G©„ù5’tr¿d°VLµZŠÈvNY\"E’rú÷e“À3†ò\r€ #›½XU’T€Z]İe\$Ì^\rsÖWæ}y?Øa`â–V¡aÌ¨ØJr•üXƒ^]™ÿ×…f%—–e¦wcxCRSX+f…wVi5bU‡@2Xxõ™6;Y]øhvpÙŠ,•„¡TÙÍf—É€Ä]vlÙÚÔGj~¹	gˆ\r–^ÙÇ^]!jXãf}~öqYÑ2\$\0¨ş ¬ÌuÙ±gÍ›K®×Øm}Ö|Yõhyv\0Æ(}”\0007IàÀ+ÍoŒ,¢\0ıØ 0fUá6ÙŞŠŸ©Ú,	U£6€[hë=AjÚ@¥–‘×=cÕqÖs…’c³ùì®…5h°ŠñŠ\n5d˜›`Xç5ËBöÚs5ı¦`ÚkaTVQxÚiüPÖ—•îÅa‚BÚ£hõ¦Ö…\$6m|¯Y&•µ¥}®‡j?J~Ö¿73QÕçZh«å…ÚÇXà5Øeh\r§Â>•7-ymWĞ°ízµÑ´å]0i®YOge˜Â˜ÉW\0IõDUÈ4EZèÕÏc(Íuk\0ÛVÅ³wD9W‹³.íkà€Š>\rÍ.\0æ­„¶ÎÖ¢™ùõ}`<dGıZ›lèu¶¸Ü,m]¤ÚZ	Wmµà”Õè6EAdÚ2ü¡ü¡¼.İ¶ZmÅµ¶à,Am’ƒ¨[Š#è­Ïö	1d=ºVØsn­t6=ŠŞ!õ»–3[§nıµSV×eºÅõãÛUme€¶ëNç^’Öˆ¯ku‚vçZın…¾6ğÛo­¥`€×oÍ¶÷‚a-¸–ùÙræ¢L[couZ–ø[o•vú›ØÊ\r¿@ä[x>q6–	ŠÜ\nÃ( ĞœUgwÜ+peÂõ›Yymİ¿—	ÛınÅÂ×\\U]ÕÀÖ\\\\İuÄöÛtä%Ãc5Û}fdşUz9-pÈ\r×˜?g}•JŒÖB\rÈN`Ü‰r5Ç÷%Éáb›ùö²VÙomÀ·Ü3v·)ÛÒÕ½d%ÖİrÍµw¿*axZ¡‡†qV7)UérıÊÖö\\ÇeåÆ¢Æ´^vj/a>¾Gsu_W8\\Ã[h>÷\\Ëq²½f[mb=Åw¼'œX€[!Wû1FV•³¶Í[9Wm]ñ5ríÍõ`±×r½Îwmc² /€ñu=Ï•¿sıË:]GrÜe4¾ÆYVi¤BAªÂ½oÒ‡Ù7Z2A°ÆsKõ>±ª‚>èûà)\n²i€#&{Q‚`±@†%˜)r€ŠµNê ¸>ğüc€Vœ\"q\n‰]vÚ…n€Š\neÛ@\$¦Ş›ÜhB¬¡\0é›†µv%Û ÌÊ>èé¸§ö@	 °]œ=3\0*«R-ÙJ«T?ˆ\n@#ä>ø	 (]Øİ×r]åvEàe€¥wÕ0÷hxx`)€£Qhÿ)œGş +1vMâJ²€œ°	éÆ€¤;\\©ø€Š?¸0ªÔºÈ@\"İ´›€à+“*>øé¢\0¾gµ(>-E@!»vİwvİÆ(üu@^mxÅäŠŞ'vmâ Ñ]¥v 	Éş^˜?\"qWm^£v`,7ªŞX×&rJp­^›zíêˆŞ¥x¥Ù÷«¬ŞÈ	à!€Šœ3{W­ŞvÕÛ—eİ¾£-ÜW‡\0t-ßÀ\$€¢œÛ÷²€™vÀ\n …ÊDIé^wPi¦˜ÅíW®¿vÕá\0(&â\n@,ŞÇx]İ×½^Ù|â€ŞÁwØ•7¹\0WÅÜ×ƒ^W}5ówŒ&â(ü‰¯Şí|Ş`&]ëyİß¸]øÕß×¯Ş?àÍAûxUéC°Ş½{uõw±^/}Eã7ßxõçw‚¬\nEëãğ¦{yEåW–­yuæ\0Ò?]ï€']Å{ıÜ7eŞÇ|²j¡­_Î…ó—åß?}\rôwæƒS¸­Ö’WšŞn}ç7€‹yî\0— _Õ¥Û÷¢ß&-ô—¥€Q}Êk7c^1yÅçIıàyöW¡`{®÷¤Ş€’t·mTSQXû·oF*·_)QªgÕ^Ğp\\©è\0šÅG8¬™½Ú€&´? ş×m_õòê€˜¨	©Ÿ`~?¨\n÷m'£|ıà×±Ôî=ÙbU‚H7íƒ1„Ñ.'â²ÂU`—‚\rã!ŞÇ{¥îØ'x&Ş1=E˜TaìièÌ«Î\r¸!\0«|]ÿ8à¬FTUƒúø`é|¢oÄÊŞ•ƒıŞ©ˆy\0ÄTô?ĞÿƒÿG]vÆ×¡]”™¸\\ª&­>ë€¥v ü8Fà!„]DW»GcPğ	i£áOüUù­ßóD^|]CjÔ^(ÿÉèİ™}z¬ãı¦p^é›Ô7†\nx\$aŠ?•ícïŞDÒi\$?–ƒş€‚& \"Ş‰¦xm­„ğı˜láE… üWÍa½}~a™‡5ğ‰ŸĞ °üê?ÆHCùCÆ?@ı@(aŸ†Xja…„¶õŞ¯v® 8}­{*¸8â\n.\r»“)‡¶8m€†î©ôÙcğbf÷ÈaÕˆİõäÊ€}–\$\n¼â5ˆÅür\0\0¯2jÉ¬&zàØ#èŸ=C·¬§Ü­@‰›Â?à	ƒö*ìšäØ;_ˆfø*+Z«n\0\$áqQP5\"İ@0\\©íáx@MØ±¹âš.8«b|®¸­Ë{Êav\n¶·bâ%/ıÃøaÏ~æ8iç‡şõ>Ô/‡>8¹bÕ‡n#WÜ¦±…Pü‰Òâ§…ğü8¾§=|zkjTú*møâúš ü˜eÔûŠ0¨ƒ‹õîuÔJ¡şx&]çE¸ş )Kå‚À\\¸_A~Ğw¡c?ŒĞ*ØÆéˆë8Óì(ÿwĞTq…ú²Ğ­‚6ã3~6éÂ_`é¸ã]‚Èxq`µ††-¸Iâà&6ØâáÙˆæ˜Ôczuå¸•cw&-—…âİ†®Øáİ¤ õïØæÀ?V3Øëâ*­@\n˜ïbÉŠ¦6—v\0¨«Î<À«×S¾xõ\0Vš¸\n¸£cìŞÈ	)ø¦ox6>Øj§\r‰Õñø÷cáh\nxÂáÎ?ªz9\0ä.øû`#†=ù	bE}–>À+ß@rxØáçw0ıòeàìš0‰øa¨š& ×Œä«ÆC‰Ÿ]ÙŠîx¢â_|,|\nÌbLÕEU>&o‘2¬·bªÖN)X	\0’4|À&’kWÜÕ‘bzXÅì×Óá–\nf÷vdIw^&µŞÏ¸£ÄŠˆöàna\0!€—şD×Ï&‡“ñy2á‰wX	÷ÇäÒ EëDùİûuè¹7áÑƒô+ã\n¶.xÏäÿ-C˜Ùá¡P0µ]œ:²˜s«-|\"æÙ!`tX@'àn?.R`&·¬?’g¡rß}C·cK…Ğ\n  âÆ^SMë€¯w¸\\·ÃÁ\"kÕ`éy8 /§¦? ş7¬†´‚¬µåƒŞIÀaê \nà\"Ôï`	à*cBœöX¹câ^›†Y@«&{wx	ÉöÇ¾?İø>_öÂ·òùe¡v–[™†µ•\\æ·ÜdÆš>ê«S•ö4 \$§‘Æ]Ãùe¿Ş^!­eæXü—ƒ&­Sî_@(d’œF]àÂå ­:µ*ÕÏ}›z˜ißåèkXÑ_ñza2ƒ÷\0¨úrCñf#v­çªá™˜½ë9ŒÔ1weá1¹áo€nù‘æ5ƒ¶d4_f(«^eY’FÊcÉÀæA—ğ0©¡À›öX5`Ò.¦«U„n`—™Şj­<¾D€‡š%Néâ§¥Œ6 #]ÑY \$GbŸmèÃıb›†b}êf±•Cãğá…“0üUTõÂ­x#`­kzªÔ§÷Öm)èÔ;“^m*à_›NŠ á³‡(	øf-Œòn#ôc“‡¶oòù\nÈãú“)›öp‰ıÇ›Êg	Â+Uœéı\0E¾pÂ›Nqéœ&°Ÿ5Ù  à÷T\ni  €³{¾ãıaßv.rJß“,?FuX„çXŠzpüíˆ(ıRÉ–?(ÿy\"ÔõS¸y±gHœ\rèÕ?«Tšª„À#»SĞÉÏgs‰ˆ	ãó\0i‰†)€*\0q‰†o¹§\0¥…VzÀ\"äA‰‚jX˜e€>ıñ¸š(b?b´ª€‘w0	\0/ãùwµä	÷(b–r\$ËÕœ¾qÙ÷)Ÿrcóç\rŸ†~™‚äAš~—“a»y5êW“(b¢\n®ÉŸf×Œ2hsÏ¦‡zh`ŞL¦yé¡ç¦¹¢²Ø0'/–G`\"'ä Zˆ*=]Å}‡Êè9jµY<Ææ­5E@+çÔL®}‰öâÊ?\0ø5\0¿…&HyËfo¡¥îØ ı6jIı\0‰‰Î{X›g%œ¦~9Åçûxgº\$h—SzÙ!È–öùPå‘€.X^z™Éáp£‚sÊÖ'çSúµ·ÿ¨~Nhz3GÁ£VD &'Ú¢â~t[èãŠ®€ú·²™Âzİcø?õô-ìÕ{µï½bÄ?–&·äŞ¸­Vø´Ê&‹óÉHıØ4Ôû‘İOX´ãSíğà\$§ÍŒ\\\n É•µ¸‘	ë‹~  éI†¶PYÌh“Ÿq¹‚ VixÈÔEPî1ƒùß\"N7ÈhñvVh÷dExÚj·»^Ö—Ø;Ï¥öC@)dZ ş—Ú-€ş/¹ÏÆú`ø[æašuøºhíŒıãzı|^š	Àé¥ƒPşy++¦eF¹ôf'ŠF.¹Ô&zœRµU;+Zš#PY·İº ıö‘â5’€şWâªÏ|.UÙhUwö1*ñQ½ù*¼§å™ÂpùaC¹®€ù„H)ù'p­j‚@>eÉš–#óæætwĞg#öjWšÕ„ŞÏ	“é>ãíÙß6@\"@<ĞÆõÊ’âÇf`d€9Ì»[!\0‚ëC¨\r@VO.(b\rlÇ/! ;@Úƒ‚-4ÅóIªLĞBïTT”:©†o\"˜ˆajŸÌ\r}&²ÊbTÙ!RŠÊôÙš°ÍNé“RƒªL¨––G`­À3—jÊdYjÅ^hzrâÄ[J\0IS¼ƒ¡>¯zŸ\0Û«Ë]\0­jë?&°¡Ïˆ `.´l†µi¥­±Ø{C:âRÌí%\0003ÍÒj· ©Wæ³ºÊœ§­úÏk3@]­‘3ª*Ñ¥<€Â×á{Vc™‰âM˜A[¥\0ˆy@<§ ?„yCş¦ãš \n`++O¾|ù¦böXş¸c^	~Ny)úéàĞ	·‚ç#­ÕFà&†µ¥šzXãC&¸	k±›^QYJcIƒ^NºïëµN4™³`“ƒş¼Øë¶*}ãúk¯}»:÷kÑz%ÙwĞkà@H	ù¦¯Riö]µ¡åwÊ+,µC@Ã˜†õĞ>áHJ0è>²\0æÓPUTÏå ¼v›‡ø\0?Û\nj±FIÍò.‚ HĞ¶•êˆá{4£É¨]è\"°ìU#ˆ¤Û‰)!U)”¥‚!dê ä\0í\"‚ ÔL€ìÒTØJ¶¹äêï@\$Á²K,Ó´Î%âQ5±²–Ær‹€¿M™›ÓºŒ’ü«P#˜%ÂÁR	01®Èˆ]+ø8Û4±FÅÃÇ@Ã`dˆØ™¼€<S†)Cüê›9µº“ñ1ÑlÃD¶rÍØ#\n+ 4ŒkK8ô±¸“K!Ê{\"—š*—à>\0ë²ˆ5{%í\$µØkA³€Æ¼”§A˜9€±ÄÇTŠÇ¾üTr\0ë…Oµ\0as€ÆœmàÖÛhÈ•ñÊÇ/„fÑÍO`œmö	Æä	¬tCz><tD&¢\rŞØ´ÇM@‘œÇRŒur(m )ÀR.›¹bQ€¦îjÂZµ£a)€ô¤ L»l/Û«Û%Wâ‰F;¸8»ömØ/}›vGaB|~ò¢mè+'ôvmä-¤s'íî6¬ò”PîÍ1­‚Y· •ûƒ\0¾¨IB²Ë±²½¤Íl.8o[zR°1®Û1ƒõ¸r\$ÔR@\$S™½·Ì±û’\0ÑN!FÛaX fİRVh6›sÊ\\¦P;É[G(Jc1jÂ>ç!„Ñ¶\r®ÛönwV¦Û°Í“¹”Ù:²\rºLÙoÄÒm«é»¤\nË¸†É:˜=¹í²bî}V¦ë[›	_¸`J{¨#! ?3'íû¸¶ß¡¢îæí{€íû:`hrZ77vàr8¸ç´½‰AT†µ©xÉnJthµ³«˜\nh°aV{Q¼’èâWÈ4Ì²F,DğÒC˜•:±i[À–”h€Êş`ûùÃ†ìİ¼êee¥•ï½3ÏƒŠLÆñ;IH¼El ØìdÏô…ØPÉæ/M\$X¤ÄÀÙŞ3|òÑÎí!Us6ŸìÏaÀ…C‰Ğ#	6³ˆÒ¡´sŸ{\"ÉZĞVï²‚¡c„ı!¤İkòr_U©0VámÚí%¾õ<eóÈz²Õ4‚ ÜÉàØ¹¨d›õ%C?FÂ[+Éó¿<¹!,ï»½ur¬[baxgÂÊÖÌï£%©ã!†Ã#oĞZX8!Fí\$M”• :ğí2ml¸çOX¹ ;O»ä£û†÷#`mwSS>êlrNk0„Û‚@3NÆÛ3Iè>»›€ènáFDğ`_Z&|C¤îì`ÚImg¶ÖV—6eÊñÊu>Õ¾ñØÛkÌjÛjóÂ=¯A­GœOs°ûdpåE’ğŸ,‚ü'ì,¿t[QÎÄü’@š	_Âx%[IšÂğC‡c3ç\n3ª¤6tì!,ğ« ¨ÌìÌ	mDXr¿Ê5Ã˜¼3J}ÃP\"[½€ê4ã»CNL+q	û<‹\$ZX;Ìı¬Î@è»IÜé¯.¬\0<É˜Z²V½r#\rÀ[	4\nœí›ëµ`Fí’\0É¬œq†l\\•ï ñk©Ë#|Zì%K0K.\nIZ¼Ì @Ü„„ĞL®¼-ñ†ŒPd‰^ñŒ¢1Sª†ŞÌúÈDÏÆH.¼iZÃ¬}ªZÕî\r«Í¦œmë¬¯Ú¾‚Š\r‡:ñkÇ,sšÌ\0óÇGö±êóÇW\0001\0È¼’œuñİ¬_\\xqï«í¦œmZÇÂG€6î\rÈ€×?'b2rÈ?5ÆòÈG@•òÇo\"\\†r\"o yqéIG!òâñ_Ç¯#¼k\0ó­['StëEÎ³šÒòOK7%2cë?Én´À<ë4'\\urfÓA\\'HŒ\nêù€ú‰WL=/Nı…Ûu‘NmUwv”İNªEÚíÀ„ü‚y[G·P&€2‚^‘»ÛG?Mç*f‰¾M8ş)ÙsÔÂ©šc²òôƒY´uvk[EË: ‰\0Ç¿¡ùøH9Ëx…dØñËÈ`0Ø,hCBÆò£ËÇ)œ½†{ËJ|»ò 2[²ÜÒ–,K¶s	Ì{A1˜˜ãuòar¹Ë>ø›+–Vaz#ÜÍˆWÌèĞ«Ê­Ë74`)ÃŸ5+Ÿ00\$,ìsEÌğ8;\"îta	‚Wó[²Á…ûªÍ›ò‚pïÏ\nFÆà‹€ñÌdàœà[`G0&ÎÍÛİ ‡ÒsÃ2\\Å¿Øİ\\İs	\\†ÿÜÍ’t/5µÈva˜ ‡ˆ®N¨\"V	óg`œ+bMpGÏ<1…óÚ‹(<õóåËÔDü²sÊŸ=œŞsÏ´),vN¥¶œô\0ÿ@²\\¤Öÿ=üÙ¹ÇÏxaÆv†Í<ÖôOÔ@ˆ»Ğu:±öoñÏ—A ÔófÌÏ6’Ÿs[>}J‡ä\\5Îı¼ÖŒ¥\r×9¤&GÍœÇÒŠ¸Äı=GïË˜W„ê5ĞyŸ;\\ÅªË§H<©Ó¾7\0SGs-ÍŸHä&ûÀHï!ÿÑyÍj{Ï‰ƒ–\"B;álk5a8T‚´šØ¦W¥úZŸL .óĞ5€ÄtÓ`ŸL†WÜØ×M\"çtÓŸM¼áî†ÜÁ½€İIGMœ³óØfÓ7tâêÅ¢°uÓ9cç'\nW¿*ı6ÛÎÔ&Ú¯a¾\0¸/!Ë“£áƒ\\ÑÈáá>í¿ÓÈåöºÿ©ù°ôò YC¼á†š?O.àl@O=*††¦@­íWı„œßõj@`½ÔÄŠ|³Ğ\rÌp9àƒb×N4ÚWÔ9Š@úõMÏPâøÄíµ×Q|@ÈQÏO>]R„KÏÕ+ãÚWÏg[À‡óÿĞ\\|ùsÿbàHôtÿ\\§KÈrÙÔè;İyİt`/^]N]tÛ& ÂôóÏgá„W\$×f™_qOPı9ĞW\\`]C±A?FİCïA³pj v%B¯b½†ôÓ¾ÏÅõ# qı4ôY5ïPİ4€ç¸CĞº1T‡ã¥oq”tAG„\\&/k2}Å<ÆL°A6†5\"Wg,¸#Çg ‹‚³Z]1ÚÂ¤ÏIõÅ„ûÙGS\nHk\\õ‘]İg=Øë(=CÊ/Ö‚òR«]9Ú©•âm24İÉÌñ»Øâr«\\‘Ö‚Åú¶öîİÚÕÎíÔ?kûwíÌú´ÇÎt·gPãƒu‘¢2u«Häİ‰	ÃÍ“=ıº?ÙX¼ü2éİgkQÍÈàĞÿl5õÖ™ew8l_Ü§nËöMrÅvÓw9Üøì4óOÙws>‹a%DÔ]‰IEÛe}ltÒ({Õ]Ù__OcæËvHıCÖ9)÷mæuÓM}=Û\0»@07B	_ÏSò½‰\\Ò2Bİ½ôøUï ¢ğ¹e]nB°¯M-v´í\rªv\0ÉİpMÉ•Ì¤İïu!@w¿ÎH&½ñôÓ²ÄtÕÖ–ªïn•­u¾\"_´@°Ü)N ÑÚ¿oN†Wì±Şs\nå¥öQŞÉNálw\"İ|®½“‚¥Øı¤AjF/-€ı“wíİ4q}üÌeŞox’—÷\"9=œÌÍz3_Pü»KÚí:²fN©­h¹­Qs“Ù_XŠSjøàİS¿ÛÍĞ5¦%ìœÔvùÛå»Aáé†ÙÊëâFB¥;RŸ½pGG²\00063Ïø–@61·¥‡×ÈÇ{%Û+LSáöÉÄ?+¼ö™ÏfÄÆIu7‹üùtçÚat”\r7³—°º0ïiù‚ØWiä?3¬@n•½R @ŒømÎÄf€€øïÔÇlg!ÜÏAÍF&vä-ÎÜãğ«Ï×^\$ÒY¬éÜaj—(Óò·_Î¸¤o‘áÿ?óÿÛ•§õÌrKÃdCŸ÷ñ¾3w¿IÌ¤Ñûæ5¾\0qÛ°ŠöÖÎİ;wjÁºŸ—[ò¹·îê‚aö÷596‚#ÎLækvo]áÿd~aï\\¦Üÿ—Ôı'V¿uô4 @šò—`˜Ç{„G-Î_›üájÂeI:º›‚²íŒQË¹Ğze{yÔ,i:v‡«çzÒ}`„ÓIØ%¼äùÉ7'½ø]]¿Üùyı_gI;ôçŸR	v“İÏ¡›Ş„“ªx\rc@skÜ¡½§&+½O¡ÛÊn%èµµrür¯#t­'òïÏ¶4¬‡WV˜±‰U«ú=Ï066ï]è§¥}kS9w¡ÜÔzbÍ9>‡Y\n÷¥ ØzbÌÈMÁUzeˆY2ºúç¯\\¾˜”;³?¡Ûèx4M¥ÄÓ…zš¸?>.çé¨>¬q\"U`úÓ‰Ü˜7;2„»*tœŞìaa nüoƒ%?¬Qõ¹ğŠVzÍÜ§¬K¼Î«ëŸ¬>´÷5ç­AÇq¸'*À‡ù›ä”>´>FàÆ¡FôKÑ\"¾ÄmÌ/?<ÿøk×)ŞtZ|«~É¬f~®¾Mû%ì²‡ŒÚıæ=\r ¬t3<g\\•yl|ï½åÑÔlîQ`XòY\\î[ŒmÌ'ó—xÁş±\$Â©¥í\\sCã„º8ŸD¾t\\«îÆ\$öÍÍ¯µ½põw²çg­^Í%Ğ”İaöWÃÎşê´/îw™>b§ÍÅ7÷;ø`[„^çJ	§¹}s{Ğ‡şîï]Òo¼>G\\ó×=Îåìî¨îÇûâ{œ\$Œò†vëÖ'Aó\\˜™Ïg¾œãø §¾ãƒ{œO`[×îKs¸W™°kğ5³ÙûœÍ*Œû¾Ï¯¶{ç.Ÿ\0jeÁÿ®\0000ïùÂß\\mzp	gAÿ¹Ï÷½„EÙŸ½¹ƒçÆ@–V…N¯»âïSñÏ¿ûñOÈó›Çïî,h“DÎûÙè*S¿zßòÅ=…Òw™¿+v•ÒÏ+|AòÌÅİñP6*ÓävgBfÅ°éüÇßJ´WQÎgÂ²ÇÖ{ó(O²Ëbu1äêJ¬\$Êß!óÿïáü^ÿ!ÑÁgÊCTÕá\\İ Ô€Ê±6ÿx{ç¯Q<¹û:~G?ş¤{7çÍ\r¿KuM¹	X\\ñö])w·şÊ}+)¸}SIô×[à;ó¸‘¹‚Jç¨’?Hø•Ô¿Ö_Iø•ÒGÏŞÃıBüvGtM§×3|Ó×C\0¯p·gOR¶’-ÎH ß@ûq;^ôŠ¯ıŸâOƒIPÊ‰Şı\r‹Îfçw3ùëÏï¶Ÿ{!å°õC´İôÄ×µâ„éèŒœHuÚĞT—8{Vıvô‡êOHb1G¹k÷ß\n¨üCÖ8Kfô‡Ñ'ßªp„§+×¾Rõ¿Ïÿá>Xt—øLÈà2mU!¯ÑÁ?K“6O˜2~1÷©›Û£‹))g2a¯%ïO\\£wZeÈ¿Zó¹øei_”u?õ—áœã×‚\nÄáW6}ıYYÚ²ıÑáPÅ_~=’ü7\"¦+Ìø“F™½úg\\ÏÇ'øµbº&hçZ»êş¡ÓIÏœĞúƒÖ¯–«ş¨\nD²_~¦eyò¹;\0BëÄEò‰•ÒOU0\$Sz„¥&ğ*oU¼FMS‡rRt2ğr/”2¨e°“ÒŞµ,TÀ•MeÈ¼1«a‚eEéşT&.PĞ%&•íW%É¡u·ïÈF¤ôºSz¥½RT.ÿÌšP˜Œ6Ãª¿îõQ~ò–gï`‡şûü¿ğÿPğ•Xÿ\$Üõ9‚•D%íV\$jåÿMTEYğøCä›YÕ«øBPğô”ğWc ËW€ÆÑ%'şš·IUËÏøáŸ’º=³?æCÁG2ƒ£f·ú‘]63]°§Ù[…jöUòy™åÿ÷\0ÙZˆÃ¿ú¿ÿµsósôl2ÂMšÂ²ÀXX¬Àt\0e©«Ö9ÛVD´YfpèF„Ë[É\0=_ºÑ“éáA‰@€°¹\\è\0ëà	“'VæF*(µ¾Í[‰@\"Zš´ğ]©¸Ğ]€Rc­Éu|gÁÕÊ-SEL¢İe¥ À&ºÚÍ<ãh°\nœP\0f€t³ö0\"J¿Q•?×DşWûüTSO÷@ú¿ÊÂ‡qşkşG2¯ô)?Ó0)g\0µ`5ë*‘«Ç\0TØ_ä\0€(êLí*”…–ÀG\npÀz;¼JÀ˜’gh„ê&Z\\,¬Ê–ÓBë›ô,4°MÕy3´Ë,M“\\J\n\0Oi¬Óoô€úå\0@0|ø\n ¾Ã¿ÃUvºİÿyo%^ë¯Q¨)~~ìå Ş¨ÒˆC\nägÄEv ÍÚY±td²ÀÉõÆÄÛÁr‡öj‘¤£uFëµ516ÔÉµ…dEOËèÒ@ÏdÉE“ƒøÖ6á·`cÓh\n´ö2,´WŒ°skÂÎy9ªï`Aùšb2ƒ``ÉiˆS\"8;à`­¸ÊUŠ\n`*Ì©‰ô\0E\0U®ÓRpŒ)° bÌÅ=Ò§¨ (Ÿ	°Aa~Ä!’ÙZØ ,8 ‡0hšÁAæT«ò`€±óihÍö(\"«ãØ¸A#^:Ê¸®( ,]`•°éaU­»¨%`U?Á&\0VÖÍšFæhl—X—³_\0—5›³;æl%^XªÀ±erÍÙ“Bcz ª1Ï\0Wñ—Ë6X)ÌÕY»AS¸Ó¥‰³h KÉÃğA‚´Ãñ‹tòv0C`­Abj¤¦€0V ŒÀoeLÃ­£%OğGÙ¯AN‚Eh“¸.â ’4¥aËÆ,†PV˜ºÁ„^”»Í/”fÌ*`½°Îd°¾ÅˆÔH&cXsÁ8‚´¿‘‡x}ÖÌ<YÍ@ÔaŞN¹‰ÅZØ”1Åb¨Ä›¤Ö7Pi`°*zƒY‰|X,Ğjà\\\0+‚)• X-°FØˆ1‚ä€?37Ø4°H áÁzb@Çq¥4QP` Ş²)bë•€‹æPt ˜‡áƒ¡M‰+è4°fş1,^<œšóâµ¬òZ 14cĞÅ«»^FXìS¯u\0–ÑAŒ3-‡- i´›hŠ¾]Œ;FüŒd6@×fÁ-Só'EEå\r˜É±’ahÓÑ’\\e‹úŠß²Ç+0MøV	\rDÉ¿“e³vÜ88,#™Şª#_3ô!¶6Ö/òƒk…Œd\"8CÉÂòºõmÅµøpçùğŒ^=7M{bÖ•+R•FéØÃó0f4”Ñ8FPŒÛû¤¾VÂßa¾ó}–Z…j™“qfË1–1Và0¬Àj4gg6À@zö¦-ÍEÍAüTğÉ?0µ#A›ÀÚÌU­–Ã ’Ùm²İ^LÌ–±;%L<¶\nG\nRPGFÔå%S¥umd“åµª=vÖé,A’9êWàå¶	¨C t£¶ÉGHˆ½“st˜@~[›Š5),ÛF8şcîsØ¼\"m>Lj9ôqm©Ç#’lršõûm#Â[‰ƒ|–Å¶ãË6Ë‰‘M¶llfá(Ø7#äÍ£‹6‘IpÑµ˜Ä­ŸÂM;xHŞ0vÂ=ÁÏ•€¢lú#}´‹y†óAœ±¥F¸›õ+«ÆÀ…ÃøŞ6a:\n¨U˜¬ ïÉ‰Û:©ZløÙ|\$i —H‰løMK¥¨°›­6–~L7°2˜VüÍKÛ‡«î’õ¦ä0ÍÈÛÌ¤CÄÕ²“tPÌD¯QçÂò„vÚq\"ëìc5o&á‡†‰mÓµ·J¸1üÎ«8¶Æ3LˆÆà\rÖÇG)ü11¸šFHd0ÉÛG¹OæÜ¤k+¯¸b¯C·…îüºªS »ĞŸ\r@;x¢g¨Ô\n{TíàT…%]Ø põJ¸Ô–ÇĞ(…¥.”)¶¦Éf„À‰îwT\r 8œGÎígÜ›7âš™Üa2\\RCb£–DÉƒ°÷« ¨Á6¦O\\­ç›²méŞ€rvpõ¾{Ï6¯0Æ“¦9†^-B¡¹Î‹t7zâı+¥”„ÜŒØ%¿;lHqC‡É­8ÈWíÍFÚ7=€†”éÀ³§-ßø54rú 3·)Ìğ§.G:Ü…9b9#A(äœ%‹vh¸Áøh4Ë	\0gK5ìc³g×¢¢=äHÂÚoEëmÚ£o‡€ï	6t<õÔé!æ\0s\0ñ¨”vPé86XàÖàÛÖìn[Cµ\ràøŒƒ…Ñ¼(R„YŒãÊ´ÿP‘é¬ÕmÁÀêİ M©·¥RnZçÁò´?çFĞÿ[» ‡ÿ²äÇÌÊaj*Ú¦âË}@-ˆRû\r–~»=ù?P“Ûşõ\\õäÈÛÜè)€¾ÕPDò\\UtBEy1	Å•¾ÆKèèh£ÓÈ…&ŠŒ>‡ô¹ Â¼BØ†¯É\"Ä)ˆ^TdılCˆ…±¡jD/N¤;©\nD+ç«†›ûÇ²š\n¢¼˜‰¢ò\"(!HYtÙ1ÜDä®c©\"+:¿`3EH‹É\")ÄOˆÆ2#E¤ĞKOƒ¦Î…¨ß™M|Çàk¢7æni\nC~ec¢:½oNTô´F÷­æíD”¶@€€Ø:\$ğÓÊÉâ>D‰„Š¯¹³<Håeq#P¤D@?”8›c‘Èeˆ”Õ&éZª15	bp¥iå9\"PD9½åˆ7x*6\\@kˆ8™õ8ğWºJ\0ájñ=t&ÒTAÂCa¨á»ğ…¨¾%‘ºôË‘/ÎˆÄÁ\0×\r\n@pĞmn{€<9ê…©	&kŸ&ËI)âgÂ0]Lç±æ4¨šˆR\"h€gsxàÌAÈ›±7Ò¢DÏ*€ë‚¤?'½I„\"ZºRZ~ùş&Iò7SáyÚÉD•,_'ƒsJƒÖ¦-~‰ò2ıĞlN×IÑ/¢d†@tKpBdDäín]\"„\0dŠñ%ùX»Õ5µ3¶êŠø\rÀ<QG‰1BUàÅoĞàAôPØ¢íÂİw¥É…©qğcÈ¨¤¯¥\"‰7.s‰:(ìR§;ŠJ@©yT¾å˜`kçòÊ¢¤…€êà8#È§À·¢ <ğ\\Š¬]^\"Jx¨pŸ.w“ôtLH¨€]_kûMåJ*`¿È«/eŞ‰8¿Š±\nÈ-…`Ï`=˜Š¼ª)üUH­ KS¹µ°¦yÌT¸¬\nÁM*CùR0æº+ƒ¯ˆ­TÀ–óÕĞz<˜®QZ¢¾K¤÷v+Ñ”°±ZâÃ6(wÀkd+æ÷4	cÔÑ¿‰î§0Sá˜²à¤»Ÿíl®±İ#j½Të'¹)¿-YĞüÍ½üYX´®Ñß—>»tÿn:á§ª+_¢#Ã1Ìı5B,XÈ¯ÀbººCG“é'ÌX:ˆòATµqG`\n¤ãsnğ£IlÜ-Ì>İÑøä]VÃ¡°ò¢÷nãet„püAk&A‡ıa+Ú§JÒŞLÅâ%l_!ƒÚ¥‰íV›(=À‹TM²Ø¨• Çó¶õn:´N³Ä˜kIAÄêÄ\0ôèbbØ—¡oÎÔª:Ÿl¬F/Ê½T§Cwx7×utÜ³ì†Â‰\r^ós˜ÙÊ-[‹€“A½„.mj=XTÙ¦ÿÎòR°\n‹´0Y€É \"O×Aµ¤bLd‘ABxğ¸•«?#SO†èí!°t¨®°ÅS¶È…îŠ4•	§ `®F_ºÌK~ü¤nãx”§Ñ\0Şê:ÉyNŸ\r%ÀRgÃ‰Á—&D¸Œœøy»ÂÆ±wœ«ÅGae¿CÖ÷D/Fa…Ø™h*“´\0rG‚e}¬–äf—± mÀˆ™AtüIøìgªÀ£R/Fv†ZØ\\PLHÎ@~c:W-7Ã¿àmK‚¢Ü&½8-,hà¬ Œf–¥-?{I8r˜')Õğ»årŞM¼2Jp\r`‚»_z¬ã­í¤j8ÔªTâFª{†\r éô\$VñA\nß=­?[\rôJHÅÔmG—ÃîLæ¹÷“¤5~\râ€2‰±sˆáÁdé2u7)ƒs7Ûsîı%³ıç-‡\"Ä‘¦ùÁÒ’;hÀ¤	P–†NÚB\$»âç1&ãb»z|Ú‰BxI€+ÏtL£Zs:z+ƒ{Ô{n€^Ú\0h%ûÛ¯W~\nz@İ\"ÀwÚè–7p\"g;3^ƒÀszêö-0,—Y|‹µoAâ3’H¯.{#xF^mtè•N#È—iİÊ¶Ysån'{Øw=Æ‚ïÅ²,ŞımSŸÔäÍïH;Âˆ(ğæ@=Wi=Ş²<ë©İ{Ÿ'Ó‘Î\"ò¹òx`“ÀõÉ|(½1Î„ê<0¬óaÏcéÈ©Ç#£ãuÈê¡Å«¼·[±•ŞíÇq{PÏäqxéiÂœö;3H\rƒ©ö«‘ÅÜî?\0¥[Ìtç>Qf^ÅÇM’üq»Ûò·°í…•“½pn6ñê/kÊFË.\\Àã%zt¯\$6»İ‡æQÌU¶1lR÷°^lju;*dpF¦‹Àğ‚ 4B8ÔÏÒ	^Æãz}Ïü\nÈã5…(ûù©{äØÇ©,¦€‘ø2Êg\n±êÚ«G}ø3Ó°±êãØ=Ì\n4èı@R6±>Ç¬uÌ¡V=«Ø(ÃÑï`¹ zx8*Qñ)Oj#ä”À	M%ÄkÇûÑó€:¹ZAéD|ØÎ1õb+ÇÍ\\Uu\0ÔçÒîÀ¯\0q\r•ôh(«©tãóŒ¡V?C—Ç\\ü^ÅI•ÄÓ×åğşâÕ@e:0!kÅ3.Ñ”ºÁ[¦õ°ÀÀLJ”8níÉôSó·‘ò`F—m3µ÷FHíacÁ:Ÿ{û­ù;áè©ïn¬6\"ˆ¤!cÁ)‹A*<›|â–Æ;û¹˜àëš\0í«a}¢Ş¹óäKx¿‡]¥ÅŒ15(ÜƒèÚOj¸‹ ıíDf”™’ä!¾ëĞvB%(ÒéÆ>,\$‹‘¦B¨\0##]	HTš°d…·&\nXM@Çß2tå\\¤˜E®–!·›ä^8Ä§Ñ¶ä7 #¾óÕùa«×S	0ä7EŸXBİà.ú¶\0MÍØH„F/ZO`;ÊTëå?A­Gtvûº(„ÌÎr6ÇšuØåÍ8›(ÈOj>¯5 r<´…˜±£Ó‚H@Qy\"‘ÀìFgyIOÏö¸µ6nEaºùÏ3\\ë€k‘\\òµÑZi‘ÔÆÆ#xï<Xû•w„Ëb£C9Èp3#Ê»²7'ê|bœ!wZş¤¤Òª7õª©Ik“&„ü—á/åJÏì•U*gUb2ISZ`\0		.*i0şµüC”ƒ{,©˜K?Hşİ ×ò²4ätIUC#EıZ©2^ˆedk	øT§#iUJà{’7•I!jC‚†…S‰'åVÒ<Év?¨~‡•ıâ«¡ÏÚ¤‡ªÅ€¬«†G¤Ği0Ÿîx%Ø‡ÕÿKı`_hräg­“D!nHğ†\$¦€ô‚EW”®>Ip‘PÁ?•‘Ë@í L“WBEU®I5’buZ(U‚ROÍÄ+%VO\$ê\"HaÕejÂ¤¡ººˆî¯4“`a`s\0âÉPW—\$ÙbP6I*ÒS\\â+&’´°5tr0)#F‚AÉ&[*ŠmWˆy\$À®­Û’À¶‚Kaã·ÄÒXQÉc’&\"\$Y‘É-ëd?CVB²òId—BRa‡äŒˆv‘ì}&Kií¨`«×_?gG#=ÊDÖg¬ˆWšA*\0¨»±Ì²}¤ëIÏ0ö'<ÂÉ‰˜6°säÑÁÕbAq‰lÖ'ğR\0'11,Òz¹5UO«î˜ÈÂ.jÌV¤B„a	›Ô‚œOÄš;fJ`a`3h‚’Òy“&èCì”€«5\0'\0¿½…¬œµö«õÎ®ß_:¿İ}õÿRo—½¯“ªÀA²¡ÕÿàêÉÒ^Ÿ'Q~\\i<,h×±¯ä]ßà¢R´íÊÓ”‚dÂà@K4Fhğ:\n/M‚Åx4ò‡¬ÕW£×‚|É}‚;–Kì‹™·ÁYgç%­‹2°üğ„@)3fbÍîP³2FeEÙ¿°ïgŒÓIvK5éR‡˜ñ³¤aäÎ¼®3”|Òh î´ÁTüQ¦&?¡ü™ã1Jg¨…Ë=²¬ŒÙñ0ŸgÊU•’#?ò|ÅÃø´*ÖÁx­Q3Ömpf‰ö”b4M¨>ûÖ‚pWI³4*ÎÊñŠCA–„™0²Ÿ(À¤›ƒ,‚ˆ,´/\$Ë”ÊPœ£ÛFeX4b4M|­l°áÿš??cFñ•sGÖmí%W€¹‚RÒ*Kf’í'\$×‡íiÔÉ‰‰\\ (ãWå/	“°j	§û!ÆF¬‡€µ\n+LÔ1‚ùA(ÈÍ¡Ñ†5vf¥`8z9AêÍ„›YHØyiÂÓ¤ Jø\"§ú	Q!¬«Y’«TD¼©rî0@?t‚òBvm=	‰°hÍéS®T@36m,\réáù ¦ÌíŠ@ˆ¶‡l¬å©ÄÄ+8L©##£W\n8	[XxRéÛA¶‘§H‹lĞÁ­¥á`JÛûV €DR¨¼Î2¤+,:Õn!×£©kª'Û“ø¦ÎPŸºE9r‹%¤ˆ4˜§®¼¢µ¤EuLòî 4€7eö\\«ÊÍHúu5Õ9{Ø‰ÖˆtGã{ìÙ°(ÃIY“îÆHa98£w†e:Ş–TZ3zSåtƒ÷äK¦rÚ_-STWÁÒb{A§;CI¶7èä711DŞ˜æñ·r¹ˆÏ	Ô>Œ59ÁèæôîcDâ·ÏwöfFLH¸Óƒ	›<Å\$N8ûIâÊb÷€	‹·¢l~V&YLcpHS3)@ˆ2¯øìA).RãŠ¾ĞyHöŒøó·gÚ«&›\$„\\ŠğÖ[¤[µ“N±6œ¥6>¼·Hù!¬ıKNßP3è‡\r…äŸ¥>7èïAãúdõ:&äZ¤%7k®(„×°MÁ£Ëš[trp.I’¿Q•Êÿ‘Ğı¨–YÀ(ê_æ&5#ÜdÂ)Ïß)m‘ºªÂH,ºáÈr?dlHğ(ªr]Ò•X`Ë!@Bí\$\\|»§ûKeèØ\0É\0I¸ë†ğh¦LkÆxh&ÓñË€9€1\0p\0Ä¸Ušr÷@€8\0j\0Ğœ¾9{À\0€7!\0œfÄ½à \r@\0006—Ô\0à´¿€²ú¥ò€5—Ë/ò_@€òú€3—Û0€I€RıåòÌ	˜/¶`½É}`åüK÷6\0ÈÌÀÀ ¥÷Ëñ\0q/°´ÁÙ|€\rÀ\0000—Ó0J^ø êòæL —÷0¢_\0i…òü%îÌ\0g/æ_äÂSaS€Ì\n—øTôœÃ9…³&Ì4—À\0ÜÌÁI~òú\0Ì,—É/à‰&!L5—À\0Âb”Á)‡³\råûšLî\0ØœÂi~“%ïÌ˜¼ˆ°pó¦Ì_˜Ã1Fc4ÄiƒòùfKŞ˜Ô\0ÒbìÇ)‚óf\0000\0b\0ÚcŒÁà2û&*Ì˜•0 ÌÃÙ‹3€\0005˜0¢aP	2üfL&\$1h¼¿I‰ÒúÀ\0005™0~`4ÇY{ó\ræQKì˜o/ŞcôÆ9‹S&ÌF˜×0¦_äÂ@`\r <\0c˜¢\0Âa\$Ç“íS-æL|˜Ÿ0†e¤Â€	¦_KŞ˜ı0~`ÔÃ)–3fVÌ}\0b\0æcŒÅùS&KÌr˜Ó2f_¼Â©|2ÀLu˜·1`dÃÙ“ó0æL<—ç/ò_lÆ~ó@LÁ˜ä–cü¿É`\rfh€b\0j’aÈY‰æo\0004™3_ÄÄ™3\r@€4™=0jbLÀ‚§³5fJ\0004—Ñ36^é¯ùŸÓ@ægÌ&˜90Za¼É¹”ù¦X€5™Û/Şh(i“s%@LÅ˜/ºg\\Ài’s\rf\$M(˜µ4Nd\\ÉI\r¬&pÌQ—î\0Æ`ÄÑI‡³\"fˆKŞ˜U0’hÄ¿ù‘Dæ\0004™q0\"bÒ¹}s	&€\0002\0i/zaüÉ)Ó%&\nLå˜Ï3^jtÓÓaS&Ì™™5*cT¿é¡“\r@’Ëõ˜4\0ÒfôÉSV¦5Ì>š†\0ÚjTÊ	®:æjL\0g4jfÒY’ş&´L?™5¦a,Ğ	3!æ9L‰™3faìÑ 3?¦Kóš0_DÍTÎóf§Ëá™Ã2kÜ¿I¦3A%úKï™¥0¬ÄÕ9|“	f‘Lµ™y0:j¼Í9©ó:&LŸ\0c3ÒllĞi•³gæ8M —é5c´×yˆ³.æ¼Ì™×2fg¾Y«S\"f{Ì\0š«3†htÆ@7¦yÍ¢˜í0njTÑ)œó¦rLîLîl(«k	¢“\0¦\$Íq˜Ó5öfÕ)¤%÷Í¼˜5¹X\\Ëi˜sS&¤M4›Ã6b¿é£³rf—Í†šA1\"iÔĞ¹˜³[f0Lš<³¸ÙI±sX&€0›·/ækÔÉ‰ó~æLÍ‹š10îc8™²û¦ÍËà™¡2J`DÒY“óAJÎ\0™¯1†iÔ¿€3+fóM%™ë1‚n\\ßù˜Sf€M¯˜+4¾j|İiæùLÂš5jbdâI½SfnÌS5ÿ0å<ôáYƒó¦Q%˜W6şi´Â‰¬3væœLş˜8Jh¤Ñ)˜sGHãN\0˜“0BiÔÏé™Pç'L(˜Ó16müÜUbÍ…Lq›Í0–jìÑ9·@f5MQ™±2BdáY§s„¦tLH›q5¶l<ã	¨óPæøKä™g0nr´Ài@&‚Íè˜Zkşs\\Æ”óÓçEÎJ˜ƒ3Ör¼Æ©ÈRüçFÌr˜‘5®pôÆ©¥S\0%úÎfœw3¶clã9„Ó„&kL¡œ=1†a¼ç)„³tæ=N›M3`Ñ©gÎ“™»9\ndÀyÑÓA¦”Î{˜0pLØY„“(¦ÇNQ˜M7öpŒË9Ólç\0ÎÊ›2\"kÀù»3:&´N:™o4zv´Ì937§4Mq%:NhtÛƒ_ó\n¦®Í¹2Js¼ÁI€&ìM›ï5\nl,Ì9Îó'BM;š÷8Nw|çy“3¯&Lšñ2fs\\¿9‹-fÌkš0ÂaìÖ	‚’ùæeÎm™W/–ntİÉÉ3¼f¨NŸ3¾l\\Ê)s¦'/6°›‡/–rÁyÜS	§.Î¿™-7otÊéŸÓg'Ì8;5Xüè)»S§5Î’ë0–eDÓ‰Ãsvgˆ›\n™Ÿ0Z`ÜÒ9“g“Mº	0ªj4ä)s*¦=N\r˜s2vwÔê¹Ñ“N¦Ï%˜=;njìË9Åó5¦2Í÷š7udĞÙ­æÏL¯=\"o¼í9Ås&D=›÷/âcôÓyîøæËç:33fdôİ¹çÓ¼æO\n™»7{TÍiàS¦IM#—Ñ6–`”ÀYÆ³+§¸O#˜ƒ9È©ìÈ	£f&êMš\0–k¼çY{ó<¥óMM™%5jx,Úâ8ÓæAÏd˜±<†d”÷©¨ÓHåóO™œ½<‚z|ï	’“	'³M™‰9®mñYæ3e&?Oš}3êgôó‰˜S&®Í3™õ/šo¤óó\$§‹Ì˜Õ>êdÎI²kæeÍ¾Ÿ+>ReqSÙû ¦\$N—Ù<FpüÓYÄ2ùæOÏX`x,ú¹éóõ']Í\0—Ñ26k¤ÒéÒÓÂæñÏ‹>Õ:‚p”õî3âf‹NÆšï7æiÄÄé–rùæœM—Å2bfìĞ	¬3HæhLñ˜Ï/²t¤ñ|³ˆælÌÌš‘:h\$¾Y¬=æ³N\n˜C=>qüÿÆÖ®&0«ËLîÚÂa”Í	Èõ§VĞ!™]3ÎkŒÚ…SR(Î^˜›2.n¼¿:sxç@Máœ1@>ltåYøsØfÌ€ŸÙ6€äéÉ¨\"%öLÕ¹3vaUĞôæLHšG4Šptí	¼ã&zM€I@®xÔ÷Ys„§rÏ©™×1Bn4¾É•g}MÙ™½4.}ÖÙ¦óQ¦°KøŸ…=6z\$ÉIò3…fĞGšGkÉ™‘óöfxMt ó0Ö”óY‰³[&\rÏ¸˜!0>mßéï“‡f-M\"2®hôÿéSÿ&’ÍÎ™+9N{m9¼2ú&äOJ—¿>š‚Üüé²“ç'ROb¡E<ÆjÄü™Ó§fN™g<h4ÏÉÔó¨æıÌĞ¡7=sì½é3æèJÌ’•2’…ÜÆÉ±®¦9L'?r„¤Ç©©óvÀ’Ìô˜@vg|ÉÙü³+è9Ís›ÃAfzlÎ	º“&LjœÁ::‚ôZÓ©h\n@£F¢ ^T™ˆk¶ê®ş]ŠÁÕ‘EPşKµÙœ•©e¨¾ŠN#\nÆ]@Y30®cnMÙ¢’ô`ûìÃh2)hfÂ9°=F¬ŠIŸ“ñ^ìÅşÅXP?™iÉÄ¢8ÊN\rKèxY’3“ŠÅa¥Âô(IìQ¨”3naèÎQŠØ‰7,B˜)+¢NÇ‰›ª&°“ÙbBQ´ ¨È¸ÍŞÁµ³\r^¸ÉN#¶ORjá-\n„´Ñ‰øŠ( ÄÁ¯jøÉš\nö¦wÔV™ÆQaDiíÖ8àÀ,À²'< +A˜3Kãè²‡ñ_`‹KAµ%lÙ(…¯L“½ªN;ú'(ø(Ñv^ÎR‹Âõ–‰¬–%Fî•Ó+Åªô\0@#Â„(ÄÇw`)~Uñl(º¤K˜9ªÂÈü´da¯y_ÍuŒkq2ˆ÷Ù„Iœ£*M1–«0µFìØIå_÷	€Ò§x0ğcÚ}3_ùFÄ­L\r¸+”H˜Ñ±\0°Ó	 Bõ™J…p™mÁßäMT™ÅTr!Áà\0±G&Eæ[ Ö òû‘m!€¿Ğ0¬õ¨êRq°Œ,Pè”‰NH­/ÉD…Q»^x6ÌE¨òÂY\n»Ôš%V&¬Xp5Û´Ï”Ğ#vïBÚ™\$\\\0P&C]š À#2»„¥Gü›©4\n9K’¥µ;jî,ú‚:@Ô|Yš€S`Oñ|¤¦Pk@Ø¥2Á¤8ÈÀ\\§–4|š^Q#e&Ã\$¡2­0×´…¤>Ê‰¦3.Âw ™x3\0P×-€†§JJ„‡’¶H”Êiv¥¨ ”‰65lZ4¶)«C0&Y.›!½µ‹IBe9”KFEÿÍ(¯Q'IM š-´YÄËQq¤Ñ±ó0‚‹”}™D¨·\0 ¢İó\nFºâeY]…c»¡’K3%İ•i/Rq`ŸF†+\n12Ì9è‡Òu¤ÜNxL¥ x?dİ©8±d“'&ÃFĞ=iCÀĞdÌ•Œ½QX ²h¤‚Éb”\\ìd©MĞşdüÉÔ?¢‹t<p.›Lµ;€j™É–¼ª5#dÉm­ì/f6H5¦E¬™‡€(„D)‹¤¶F”Êóˆû • 0¥cA­d(Î©Fµ,VR,ø:²7\nÂ%—5\n”w\0ÂÊš¥ªÏ}(†£l¸ÙVVEDÊcö£²gWÆ¨qTpĞÕs(+Ph Ñ•¥Ö@¬¦¾Ì¾KÉæƒ (jğÊ@tºè²Qf¢ßIúNäÆ5!ú—•@´^Ğ¿f—µv¿ô‰iwAV^²ÔE“¬¥é´€h”Ó	cĞš!>€ óÑp^æ¨Ÿ‰?ZbĞkiuAN¦HÆm ôF—@è„ÒD_hÃ™8fw‹èi2“¢ÁL™x9àûÔÅ—şQY¦^Æ²%\"B²Œ¬é€Á\0V»Mv«V´Å˜!ÒÌc` ‰U3ö ì÷ÉªÀä`¹M2š¢øÖ@¡úÚòSXbM†Êí©9ËºXZ/—¦,½\nˆí4ZOŒ{)+Rû‚œ¨a¨CH2tÅšöµáƒgH¶›SÆ¾€ ¾‡ı¦KˆkHbÌ«¤çÓReğÖúš5ZOlhWËÆ¦,ÅõŸ}uîl¥™`Áx¥ëNŸM0Ø0^ilÁZƒ‘LZˆ«ösÔÊiĞ4;aNŒŸÚøõOôË)†S`‚N)}İ8f˜Ô²?Ó¤·Kü\nµ3x0Y	¸2Õk­N€1fŸ+ì©áSs§ÖÎ˜´êpÔ¤`ö1gÇLY7ĞÿĞ6×Å“f¦‰–—a8¨DĞo §Òğ'KÉ¯*{lŠU‡ı¥õOE;z-îi+…ƒJ\n˜#ö}tÂ)÷AµhzOJ˜c(4¬ÜiˆSècoÙv­1Z~´÷ñÓFè¨v˜å%Úcàé‡à§ü\0®™%@úf0SÀ*Ó)§TÒîš7ˆ5ŒßØïÔ¦kL\re3–sõé²t¨H½bš,še´oj²)¦½fšU=x6´ÓiªÓOd^Ò }CJkL´¤èÔ-b£Mf™S\"Škµ©©Ó_¨5Öš½DF4¨İ)´Á¥¤ûO²DåF[ØSÆ_ÍÖ›«):nôàÒÔY§¥Oê¢ÃGö!Ôg ÒÓ„¨ÁOq•Ã,˜LÍéÅ±©§Uî¢U92”ä˜ûÓ”¨OŠ£Œ¦êğ^ªÓŸ¨•N†=ARuiÓS(§Ræ]Bº†T÷)ØTg¢)Cå•HêXìì)dÔ§ÕP(]¨9@iŞÑe¨O¢U<:Õ(j+A¥§‘Q*UEz{”óSÎ¢R©‘M=ÿ,\\)êB¦‰˜İ/DÌ¹©lÔ¸`YO™£t!ztT¿h·Së©3Oµv¼!ˆgZ4S.©’ÂşŸ‚òš~M)úTÏ„9D\"ŸÍL(C•\0(Á/G^bÂ†Ÿı@jcUi1÷¦AH¥Mf+ßêmTë‚ÃE2ª‘4ëi¼A¨9LÈ®\r3J=TF €Ô%&‡S¶™•BŠ†5=jp/d¨PØ>íCŠ›ğ†*SV¨{SªššøºiÁşj!¿ªT¢5 x 5jˆTKbT^¢}Lz@p@i¸TT]×QZ\rEêˆ´¦ª…Óò¦÷G©ã*¥5©ÁU-aQš¨½Fšq5*:0\nh=QÂ¨íG&zì9×z°¤&àÌ\"		8r,9×Ï07ªsN^ª\rGª¦Ôô˜¸Õ©R“¬ªğ>êATò¨1O‚¨ÕPZ‘µEêFÒt¨¡U’¤½NZŸ•AjPU©EU\"¨-Jz£µ*`€Ô¬b®Ã~eU:[Á=êªTµ§¬½¦¨}±\0é|ƒîÕaOzsR2i‹»êÄ2f^zÌÊ¬`\0ì\\×w‡ú\0’&¬­6š|´¾Z7/t‚æ¼‚¬­R²Ì­—ş¯1¥úVZ¬ñ7j¤k»*\0TFÜ»¼°va­;×Ğ´Õ¢®-†I;*)²kº¯jg´Óİ{Q¨å'…\0ßÕÁ|HŠ²ú\n¹4›)³/L«oVæŠ³†	\0à~QxºÏ—S&:DÕjI Rû`n|™¹Aäæ´^jU”¢í(\$u]Ê´yÙ³€S\0¾V6­)½Bp…`ˆÕê«¼½d›;V7Õ~j¶³q¦ªÊÚMmê¾Tnéˆ³y¢ñX‡µŠÍ3—ÌÓ\0¾¼sM'ìZØJf0‘•q=´•…e³'fgXq˜ÅW)B”i)t2_äÍı¡y=0	*ˆšjUÈ§ÜÍş¯µbzõ}—øÖ/d^ÁN¯cf‘u|IÉ4(QXÊ“-ŠÇMv+ÔQ\0…XnŸudz¼Ëù€!\0CFÏO¸™ÅO¢}‚jÑTÈ&™Px¡>Ê~ôg+)\0Y©5YF˜Á>Â²Ò+*\0M¬¿Yb³8é?•–ë(Vh‚h¨˜â¡Ú)Õ” ~Â¬ MÖ°êRUšH3¼`%LÎ³šÌ´F«@/\n¬µJİOšĞ5¡+EV†¬ŞÀ²³‹0¥ÿr…I¸Õà_İZM#9ŠlkLV•g'Kæ§K:Ò´àX‡Ñ±­(ÏòšEMZÔLÿ)tÑ±¨PÃFó3B¬lk\$V³&oV’±­kf\0õ’Øn°à­kZõˆµY6-Tu °à¬cGm’\rlr¬lY„3ÖhT–´áV0ü”¹:“QeK2«mPÚ!UB@#ÖÊªR6¶U`ì7Y·å\0š¼öüš¬o©ªÑå'=[¡Q±?¦Äá+²­İV°«Ó,\nŞää«{¨ô§µG½ˆµ8êàøÉãVµkâÌQ“+3º>âYõà\0›W¸Ÿel*È@\nø€H'Ø}ƒû Æbu‹«Š3yf'Y.§C1ÙBŒÄ™]Ò¬\0¹+1Kİé/—«G[)‚åsµºÙ`“Ğ­5Z­¥^åE`‰³5®YHU¡«\n8Aükœ2»\0§[ò­!ªé,ğ	–¤6ö>}qÃ=•­jÑÖKh@ÈáS»#Š¡EQ¼…„§Q2¯KšµUÖ™b³¼&Vı#p+æ\0Â³’k Lö®¢öuÚ6€+Á¦ˆÊizË_Ö`MD{//®8©Y8\n›¤â*øÑ‚¥³)•’C(UÏ	œ³^ÜØõx²lÕ­k×“¬—^V¼lŠâL«Ì\0T­•^v±¥vÂ‡Mh£ß¨ç^q£%\"*K\0ƒõ\0We^Sº**L+¹šw´Ôko^n½›`ûµx+°5şi_[)£àI¦^«àÀìªÎ·“Ö½•f+à‡ö©ÓIxa=|åª@VK\0 \\\$­uëìV…dê¾£UdµC²‘˜%Rn&²Lı#\$–'ú«=Rqb›K2“Š÷iFuüIÂ.Ê€ßFy‹ÓªN5.¨ÊÒƒc<ÀI¥'R}4§˜	‡ö«&Á!‡«0Šõ´=Y¯@«eI¹}…všóUÇ+ÍVKôÇÎµÅ*½l )ÓVÊ`VÀ¶¯y3úSu¼ë°Á°6N–«“PVâA€AX¸´Ï›{†ÃÍG0?¦Ó^š¨|&7XD´¸f;`yõ*ğğ5*‡æ«İ]!„\0Bi€™*ØK­dÍ¹’¨ZÇìÀ*Ò)_aU®……Bgõ­XdØ[°,ÖÍ¡­K\0ì2äöÑ‚•\0šèäâ™’0?°ÔNI™%†‹\rÕ™‰ÇX°Õ[ø>ı†Ö\nö—„X!©ÓV²••†4kÎYí\0G	êÁş–bğ¦a³œW&‰aÁ˜Õ‡&IğujÉ³2`xM6•”:Ö«È?×¸dŸN¶RøSzµbéa²‘¥•Î–-‚*XvëX`¬–Â&µeŠ\rÀ¹\$‰btƒZYö,À<ØµÈ’Ám‚û \n½…SEf¬¶ÖàaZ¯+ŠjJaÊ÷5SƒºdhK±@Â\n\\)he_Ùª‘TÈ•Êø01ºI¼*¨q p“àf€Î±Ô80@3Ê¡GØÔ’ÌˆDĞjQt;aŸI*êÆŒ ğS3æ0€6%’aÚøà\$€€€b2–I	¸í!n‰>Ï˜ü¯öÈ˜n[\"³l‹ÙVÿdP,[# }ºY J\0ÚÉtÛ\$Ó.,”Ù%²4Å’Û#!ÂÁ\09²ed~É|ÇàM×];:31úÉà!²³Yl ‡8²‡0šÊ-‘K'à\r,¤Ù²dˆ&µ‘ë'¶Qì”YQ²IdÎÊ8{)¶O€’Ù1²­dªÊp[&ÖWì—Y\\IenÊyë-FfœYk—e¢c\r”,–V,¸¯²çdà1ÜÔk.BĞ¬©YB²õeÚËå•{/\0ì½!ô²ïeNËÕ”Ë0ö_¨jÙh²õeVÌ\r–/3‘¬ÇY]³+8Ì­–{1vQ¦…‡°³7dRh]˜dv`¬©Ğp²Ñ4.ÌU™, æ…ÙP³Uebh]™4vd¬ÖY”³?djh]•ë5ö`æ…Ùc³wf–ÉÕšyÖg¬ßÙB*{e¶Í¸c¹À¶q,ÙÙ«œfŠÈÅ˜9À¶a¬âÙFœf¦Îm•‰À¶k¬ìÙÒ²Oe¢p-•«9 çÙº³»eNp-›ë<öP¦Ùš³«dRÏmœË:6z'\"Yà²ßgÏm‹>v{&TÙö˜ÿe¢_\r—»=vQ­\0ÙĞ²ûhÈµ;@6~­ÙågÊĞm‘©ŞV¬ÒYBågRĞ à\$¶ƒ-\nÙFågrÏİ¡ë;öxçyYâ³ßhJÌµ¢K,VZ&aÙî´3h¢ËU¢«9‰ÃÚ-´1h†ĞÔØ»E–mY²´qhÂÑ5¢ûF³1íÌÏ´vtfÒ=¡C¶†¦ZG´ohFÒÔëHöˆ-(\0_™ÉhúÒ¬ÎKH6í(Ùæ´¸~tE¤«6“¢-\nZc´hÒÓ¡»-¢-*ZK²5:\"ÒÅ¦àÇs¢--ÚUizÓµ¢»<f¿íZ_´ùiŠÍ]§ËL¶ lûÚh´ùhrÓ4ÚûMs­8Ú‰´ëiÊÓL½ëQv‘­=ÍË²wjT•¨+`­Z_µ9jÔØ[)xíNZˆ³WjrÓm›KS–£mSÚ¢µ°Ê%•kV¨ìÀÚ³´9jÎÓm«;N6¬íW«÷´SjÖÕõ™‹VV¯­>­Cµ‰i*Õ…£;4v²m'€'µ“jŞÖ%«›X–®íbZ½XCiÖ­{R–°-kÚHµ²°²n]¬‹WÖ“moš›—k6Õ…¥+\\¦'Ù#µÓkRÖí­k\\mwZã³Ô\0ŒLÓ‰Ÿt1-gZöıkâiÄ½ù;­XLaµëk^k½°{\\\r¬-…Zæ˜\rl6×dË;bÉæ1[w;Ø°9˜–ÆgQÛ#lrj5²\\Vµæ´[ µÑjúhu²ZVÉ-xZàšKl‚ÓÅ¯Ûd–z–—Z¾*{lr_¥³‹döµåóÛ8¶Wk‚hõ³‹^V¹¦G[8¶ikÖ¼Â[c“H­¥Úµa1‚Ú]­i6ÏísMp¶—m×d¿›ivÑí™Zà—Ãlra]¶iÖ¯¦5Û`¶¥mzÚ¥®É„–Ø-¬[—Ëm‚Úå«	˜vÇ%ïÛt¶Çk^_e·Klö¹¥ó[t¶[mòÛm±yƒöİ-¸Ú¾5ÿlrbm¸‹nö¸&\$Ûˆ¶÷k²_¸‹oöä-ÀÛ™n\"Ü*Â°VÆ@’[#XYd.Üå«KYjıÀ’[&N’ÛMºy’VèmÊ\$¶b°	%¹›sö±m®šO=nzİµ¬«f¶æ§Ûœ´uk®İ=¨«x\0nÓÏ[“·ƒn¶Ô•¼9{¶¶íÑÛšµ½krÜı©›s–¹-è[¶š×oNİ5¼I‘¶ôíã[Ú·[kÒŞTÓ‹vVí­ƒ[á¶ošÜ]¾Kgvçí‰[æ·So_m¹É‚–ü­ñ™ü›j^Ş­¿i©öç-’Ûá—ÑoòŞÌ½Ù Öÿ-îÜ·[0*ßå¿[töÍíğÌ÷·9læàm¾»vÖÏnÛï—»màm¤`}AÚ™Ÿám\"Üü¿ë_÷\0æ\\˜ãnÂá,[ƒn‘À3Z™Sp¢İ}ºI–ç-¥[á¶™pÚßM¿kj\r®\0Ì¸-1şà,Çûu¶Ö®\\\n·‰m~ß\r¶‰7mÓÛe¸“pödÍ¹ËmwmÖÛo¸“qa­ÄËx–ŞmğÍ¡·9möâíÄ€Vç-ÃÛá·qšâÔÑ‚Vı¦‚Üd¸8Jã%ÃsS4-ŒÙ?¸ë7.ã½Æ²dçFn\0¸,tfà)Ñ›r§Fn9,,:3qbÔÍ¿‹y·!­Ûù¸÷nêá¡{…×mßÜwjfãıŸûyV¦nAÛÉ¹,¦Şe»»ÖóîEÜ’·¥rnŞ¥ÊS?“.=\\—µÕr~`åÇù~·f\\´¹ršÙ¥å/Ö5\$ÈÕg{ˆÕQÚ‚5^ª3hTÃAà\n`ØpU¨·T^¨åQ¶¶'*™U\0”OÂª­RBqL\"‰Ä/5b·UÒ¨+Cúé¡÷Z4/wcª¼½ˆ•QxE“®b4_s…H¸-õEëRQªfQQ’\rSš©Uéàm°l¹‰Qâª(æOB¥9®ÕªÈeĞ{•Z_Wi“%Rö«dëš°†*·Õ¹©T^«”®’ešÕ•G¿L²˜ñ–=ÔíwÕ]„´Ëj¯•jM·JJ S¹~ÆéunÊ\\´á©ş07a»L§e8j‚k·iIA_¹M.éÓI¢lÍoé‘“Ğ¹™Uæ›ÍRº…—RX®UÍº˜Árè½8kšäß©³°ô“ÅMºÎ+›—9no±*¦-uj‰MÕËœìq)¨İL¹ÖÌ\"\nÕÎêÒ÷S.xÜó§\ruµ•;)P—V.€Pºou¾©¥j[¯>)ÎÁv¤åIâ™õÔË å®/×ºt®œóºvWB+ÁS±º˜¾Bë½Øú“„á*OSÀ“·)ˆìÊxTk®¦]Q»;M	½k®”Ñ«I¿º½Y@u¥DkİZ\\S‰ºGRQƒı?¶&T=ê%UşºgRÂ§s\r*Îwn „]ºaÏtò •Óú‘lŠZä&su¢UÌ«¨ôóê%\\Ï¨•Tr\rÅÍ:”°mn©ÕUƒuuZOeD«¬9.o]c¨wnæíÎf%7:×[ƒKu }Ö¢€µî¸T’¨ÒëÕÛÊœ×_.÷S‡¤ïU2¡5Gš[0péDR^;v“¬«±•Ÿª>İ“§}R~NÜ¢U'îÎA×TÿR~í›´Uê¹AÛ»N\0¢	6(…j(êB\$®-J&æl¶¿Œ›¤á€(¦5t®Šeuû¿ÌélÒ8eìÏ6é5|tœkñ.Ó©¥J6õ))QW~lB¥7G\n¼ Ö=,Ÿl@äº\0ÆR”S¥”­ ¼Â\n£+K\"è3§Háã‰´nLàQ.ÄK¡s[Ÿ¥…yr„7[AŠÖ¤]mK+É»Ûe„ï´Q\"Âò%{E1ƒwK`Óå·pr|œ!µ;V’˜@µ°;bN-:bİæÆ7Ÿ†!Ş‚|ÌHl—ë{»Î©Â/C6eN˜Ø0sÚg(âP›¸ôRìœ!DUéH‰W¦œf^4	öôòÂA<w¦\n#‡HxØ-»õéˆ|×£áôC†\rÁç{Õğş(ÂÄ¨u=å8A…„‡ £·’¶õúA;ÒéÊD^[xrëÙ¼pöqÀBè¤=Oş›¹A0ÄÀ`€1€]DZİÑ_¼¶î€RãÏ7uŒİÒ6ØCÀaJ®ˆ‰r—z÷(£×\\ÉÁËÍâOØª|ÌˆÕ:·¬“şR½Æ^mÑıåöá@\0=5l!zQÑ|ëßÎ	ÉBúWØñô#d·İIÒÀÂÂÊhì´çİå (·Ä\rGEÅ+§|@UíğĞ71Ç!†;Rˆ9QC¸47Yƒæz_#½ìyÍÖ`\"kå7È”;‰au™œ©İ¤™±ÂÒ³^¸jëÁC¸sÇY‡â]f6artÙ’öhĞ€Ø¶Çpy­{ZåAåT¦¿tØfù›¯èX®›Y=«¾¨	)ºÛ¦È \rí¡øƒ7yRúĞc»ë—À_[W,	ró]öW@nË‡>_a‡#|yÓø&äÈÛ¡ÍŞ_”DI×ñ§ ?Î›\0º*u6ĞV›ï ÜHß€ŒëúúˆV”™´c‡:­YL\rjÕù÷.§¯d…h\nBû¼´ŠUá.oÉß™½bYüµó«ô!ñÜ˜}¿G¡¬ì0'¸wœÒ/UÅ¿,U}nKÂÀ’ ttGÈ¿!~Ág@ÔÒ/Ùß±\\40ÂôKÄ[õÃ<oÚDï½ÙLõó+öà ƒ^ºâ/33“€+7¨ÎÇnzìfô ¡ĞàJ›%ßÒ¿¶\"şè[è s/îŠrp,Õ¾eñ’ÌäRáq¿Ï°g­şõ„” B½‡•­8*8GÉf¡ŞÃÛ‡¦±rÿğ8»üé%K_çüäâ\0‡Û7Ço/ÊÊ^l]î\0 ‹©‘©\$ŠŒ¾yŠùğÙ1MÏo›7ÖRwzô##i›İ³[LŞcGzVèñ÷la ›L†@¾˜H	\\HPÑødC·üá\$cc±˜ÆÉà.xhïæ™fÉœŸ#9ˆzÉò4]5\0Ã\0¬ÅŠÀidiøQ§OØ0`Q¤¡ˆªşüÁ.8Áyò2™gWªƒg¸3ÀZàÍ¿åíÌ®\ráÜ¶™\"Á¸ki•Z‡A.+î‹'€£™f<O7DØ¦¾À6Oœ1ÇX`HoĞÅÀænô\"Ú¨§ß¨ÿ`ŠSÚê`(«¢ğ=ŞµÁ\rv¬>šV™J«\0n½OUºÀŒ(`Î»—j”™Ê”\\h»G®¯JBò¢«JÂ=í|İ°Qß†{ ‹A˜JÂşÁQ/~²T&jğ^nEÏ|zÀCç6¸/°Qß6†İ‰ùïÌwáp[ß…îüÓ@a\r Gd6ÅH†é×å\0000®­Ïƒ•Ş˜\$›âx8ÛL`ã7kƒ”İ¨!Kù×½ïU…½[ª\"g ÑçP=Ÿwr˜ğˆÖõdØ@@¢Ù\0G„IQNÀ7.,ba~~lK‚œ!	»ğˆE)MzÈJBÎXFAùXÆŠZOA!±²ØB p…„xi„´ÁøFÀ¢—iÂQıgôŒ»J’À“›cT/”»+ríUSÈÛFs\$“	¨&ö=Õh¡àV¦XXI‡Ş«¥€ áJ•ÈÕƒ[pø6T-q4Ğ;€ÄEPU‘\0QÃ\0ÄàP\"	œ½€b{g ) \niah4ÜLï\n1l+Ğû@9ái\0NUİáÈ„Nk\r`O™KJúgšÖ.Ëcå?h²jÃ	»”…\rRïT_›–®•_t,5 Ö¥Wœobµ>U„°æÂIw;5´‘…Õ¢s˜Â©a¨å§ÂÆ");}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo"‰PNG\r\n\n\0\0\0\rIHDR\0\0\09\0\0\09\0\0\0~6¶\0\0\0000PLTE\0\0\0ƒ—­+NvYt“s‰£®¾´¾ÌÈÒÚü‘üsuüIJ÷ÓÔü/.üü¯±úüúC¥×\0\0\0tRNS\0@æØf\0\0\0	pHYs\0\0\0\0\0šœ\0\0´IDAT8Õ”ÍNÂ@ÇûEáìlÏ¶õ¤p6ˆG.\$=£¥Ç>á	w5r}‚z7²>€‘På#\$Œ³K¡j«7üİ¶¿ÌÎÌ?4m•„ˆÑ÷t&î~À3!0“0Šš^„½Af0Ş\"å½í,Êğ* ç4¼Œâo¥Eè³è×X(*YÓó¼¸	6	ïPcOW¢ÉÎÜŠm’¬rƒ0Ã~/ áL¨\rXj#ÖmÊÁújÀC€]G¦mæ\0¶}ŞË¬ß‘u¼A9ÀX£\nÔØ8¼V±YÄ+ÇD#¨iqŞnKQ8Jà1Q6²æY0§`•ŸP³bQ\\h”~>ó:pSÉ€£¦¼¢ØóGEõQ=îIÏ{’*Ÿ3ë2£7÷\neÊLèBŠ~Ğ/R(\$°)Êç‹ —ÁHQn€i•6J¶	<×-.–wÇÉªjêVm«êüm¿?SŞH ›vÃÌûñÆ©§İ\0àÖ^Õq«¶)ª—Û]÷‹U¹92Ñ,;ÿÇî'pøµ£!XËƒäÚÜÿLñD.»tÃ¦—ı/wÃÓäìR÷	w­dÓÖr2ïÆ¤ª4[=½E5÷S+ñ—c\0\0\0\0IEND®B`‚";}exit;}if($_GET["script"]=="version"){$o=get_temp_dir()."/adminer.version";@unlink($o);$q=file_open_lock($o);if($q)file_write_unlock($q,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));@ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",HTTPS,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$Zc);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",'15');function
lang($u,$_f=null){$ua=func_get_args();$ua[0]=$u;return
call_user_func_array('Adminer\lang_format',$ua);}function
lang_format($Ri,$_f=null){if(is_array($Ri)){$Cg=($_f==1?0:1);$Ri=$Ri[$Cg];}$Ri=str_replace("'",'â€™',$Ri);$ua=func_get_args();array_shift($ua);$ld=str_replace("%d","%s",$Ri);if($ld!=$Ri)$ua[0]=format_number($_f);return
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
messageQuery($H,$Di,$Sc=false){restart_session();$Id=&get_session("queries");if(!idx($Id,$_GET["db"]))$Id[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\nâ€¦";$Id[$_GET["db"]][]=array($H,time(),$Di);$Yh="sql-".count($Id[$_GET["db"]]);$J="<a href='#$Yh' class='toggle'>".'SQL command'."</a>\n";if(!$Sc&&($Cj=driver()->warnings())){$t="warnings-".count($Id[$_GET["db"]]);$J="<a href='#$t' class='toggle'>".'Warnings'."</a>, $J<div id='$t' class='hidden'>\n$Cj</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Yh' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1000)."</code></pre>".($Di?" <span class='time'>($Di)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($Id[$_GET["db"]])-1)).'">'.'Edit'.'</a>':'').'</div>';}function
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
const thousandsSeparator = '".js_escape(',')."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ma!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> Â» ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:'Server');if($Ma===false)echo"$N\n";else{echo"<a href='".h($_)."' accesskey='1' title='Alt+Shift+1'>$N</a> Â» ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ma)))echo'<a href="'.h($_."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> Â» ';if(is_array($Ma)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> Â» ';foreach($Ma
as$x=>$X){$Xb=(is_array($X)?$X[1]:h($X));if($Xb!="")echo"<a href='".h(ME."$x=").urlencode(is_array($X)?$X[0]:$X)."'>$Xb</a> Â» ";}}echo"$Gi\n";}}echo"<h2>$Ii</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($l);$i=&get_session("dbs");if(DB!=""&&$i&&!in_array(DB,$i,true))$i=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
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
input_hidden("fields[$s][orig]",$Yf);edit_type("fields[$s]",$m,$jb,$kd);if($U=="TABLE")echo"<td>".checkbox("fields[$s][null]",1,$m["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$s'".($m["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Sb>".(driver()->generated?html_select("fields[$s][generated]",array_merge(array("","DEFAULT"),driver()->generated),$m["generated"])." ":checkbox("fields[$s][generated]",1,$m["generated"],"","","","label-default")),"<input name='fields[$s][default]' value='".h($m["default"])."' aria-labelledby='label-default'>",(support("comment")?"<td$pb><input name='fields[$s][comment]' value='".h($m["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");echo"<td>",(support("move_col")?icon("plus","add[$s]","+",'Add next')." ".icon("up","up[$s]","â†‘",'Move up')." ".icon("down","down[$s]","â†“",'Move down')." ":""),($Yf==""||support("drop_col")?icon("cross","drop_col[$s]","x",'Remove'):"");}}function
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
as$x=>$X){$Ah[]=idf_escape($x);if($wd)$wd[]=idf_escape($x);}}$I=driver()->select($a,$Ah,$Z,$wd,$Tf,$z,$D,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$D)$I->seek($z*$D);$vc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($D&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$z&&$vd&&$oe&&JUSH=="sql")$nd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'No rows.'."\n";else{$Ea=adminer()->backwardKeys($a,$oi);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$vd&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'Modify'."</a>");$qf=array();$qd=array();reset($M);$Xg=1;foreach($L[0]as$x=>$X){if(!isset($ij[$x])){$X=idx($_GET["columns"],key($M))?:array();$m=$n[$M?($X?$X["col"]:current($M)):$x];$B=($m?adminer()->fieldName($m,$Xg):($X["fun"]?"*":h($x)));if($B!=""){$Xg++;$qf[$x]=$B;$d=idf_escape($x);$Md=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($x);$Xb="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($x))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$pd=apply_sql_function($X["fun"],$B);$Sh=isset($m["privileges"]["order"])||$pd;echo($Sh?'<a href="'.h($Md.($Tf[0]==$d||$Tf[0]==$x||(!$Tf&&$oe&&$vd[0]==$d)?$Xb:'')).'">'."$pd</a>":$pd),"<span class='column hidden'>";if($Sh)echo"<a href='".h($Md.$Xb)."' title='".'descending'."' class='text'> â†“</a>";if(!$X["fun"]&&isset($m["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.'Search'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($x)."');");echo"</span>";}$qd[$x]=$X["fun"];next($M);}}$He=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$x=>$X)$He[$x]=max($He[$x],min(40,strlen(utf8_decode($X))));}}echo($Ea?"<th>".'Relations':"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$kd)as$of=>$K){$fj=unique_array($L[$of],$w);if(!$fj){$fj=array();foreach($L[$of]as$x=>$X){if(!preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~',$x))$fj[$x]=$X;}}$gj="";foreach($fj
as$x=>$X){$m=(array)$n[$x];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$m["type"])&&strlen($X)>64){$x=(strpos($x,'(')?$x:idf_escape($x));$x="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$m["collation"])?$x:"CONVERT($x USING ".charset(connection()).")").")";$X=md5($X);}$gj
.="&".($X!==null?urlencode("where[".bracket_escape($x)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($x));}echo"<tr>".(!$vd&&$M?"":"<td>".checkbox("check[]",substr($gj,1),in_array(substr($gj,1),(array)$_POST["check"])).($oe||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$gj)."' class='edit'>".'edit'."</a>"));foreach($K
as$x=>$X){if(isset($qf[$x])){$m=(array)$n[$x];$X=driver()->value($X,$m);if($X!=""&&(!isset($vc[$x])||$vc[$x]!=""))$vc[$x]=(is_mail($X)?$qf[$x]:"");$_="";if(preg_match('~blob|bytea|raw|file~',$m["type"])&&$X!="")$_=ME.'download='.urlencode($a).'&field='.urlencode($x).$gj;if(!$_&&$X!==null){foreach((array)$kd[$x]as$p){if(count($kd[$x])==1||end($p["source"])==$x){$_="";foreach($p["source"]as$s=>$Th)$_
.=where_link($s,$p["target"][$s],$L[$of][$Th]);$_=($p["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($p["db"]),ME):ME).'select='.urlencode($p["table"]).$_;if($p["ns"])$_=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($p["ns"]),$_);if(count($p["source"])==1)break;}}}if($x=="COUNT(*)"){$_=ME."select=".urlencode($a);$s=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$fj))$_
.=where_link($s++,$W["col"],$W["val"],$W["op"]);}foreach($fj
as$ue=>$W)$_
.=where_link($s++,$ue,$W);}$Nd=select_value($X,$_,$m,$Ci);$t=h("val[$gj][".bracket_escape($x)."]");$Gg=idx(idx($_POST["val"],$gj),bracket_escape($x));$qc=!is_array($K[$x])&&is_utf8($Nd)&&$L[$of][$x]==$K[$x]&&!$qd[$x]&&!$m["generated"];$Ai=preg_match('~text|json|lob~',$m["type"]);echo"<td id='$t'".(preg_match(number_type(),$m["type"])&&($X===null||is_numeric(strip_tags($Nd)))?" class='number'":"");if(($_GET["modify"]&&$qc&&$X!==null)||$Gg!==null){$_d=h($Gg!==null?$Gg:$K[$x]);echo">".($Ai?"<textarea name='$t' cols='30' rows='".(substr_count($K[$x],"\n")+1)."'>$_d</textarea>":"<input name='$t' value='$_d' size='$He[$x]'>");}else{$Ne=strpos($Nd,"<i>â€¦</i>");echo" data-text='".($Ne?2:($Ai?1:0))."'".($qc?"":" data-warning='".h('Use edit link to modify this value.')."'").">$Nd";}}}if($Ea)echo"<td>";adminer()->backwardKeysPrint($Ea,$L[$of]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$D){$Ic=true;if($_GET["page"]!="last"){if(!$z||(count($L)<$z&&($L||!$D)))$nd=($D?$D*$z:0)+count($L);elseif(JUSH!="sql"||!$oe){$nd=($oe?false:found_rows($S,$Z));if(intval($nd)<max(1e4,2*($D+1)*$z))$nd=first(slow_query(count_rows($a,$Z,$oe,$vd)));else$Ic=false;}}$jg=($z&&($nd===false||$nd>$z||$D));if($jg)echo(($nd===false?count($L)+1:$nd-$D*$z)>$z?'<p><a href="'.h(remove_from_uri("page")."&page=".($D+1)).'" class="loadmore">'.'Load more data'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $z, '".'Loading'."â€¦');",""):''),"\n";echo"<div class='footer'><div>\n";if($jg){$Ve=($nd===false?$D+(count($L)>=$z?2:1):floor(($nd-1)/$z));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".'Page'."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'Page'."', '".($D+1)."')); return false; };"),pagination(0,$D).($D>5?" â€¦":"");for($s=max(1,$D-4);$s<min($Ve,$D+5);$s++)echo
pagination($s,$D);if($Ve>0)echo($D+5<$Ve?" â€¦":""),($Ic&&$nd!==false?pagination($Ve,$D):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Ve'>".'last'."</a>");}else
echo"<legend>".'Page'."</legend>",pagination(0,$D).($D>1?" â€¦":""),($D?pagination($D,$D):""),($Ve>$D?pagination($D+1,$D).($Ve>$D+1?" â€¦":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$ec=($Ic?"":"~ ").$nd;$Mf="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$ec' : checked); selectCount('selected2', this.checked || !checked ? '$ec' : checked);";echo
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