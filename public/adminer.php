<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.1.0
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.1.0";error_reporting(24575);set_error_handler(function($Xc,$Zc){return!!preg_match('~^Undefined (array key|offset|index)~',$Zc);},E_WARNING|E_NOTICE);$_d=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($_d||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$xl=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($xl)$$X=$xl;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Qb=adminer()->credentials();$J=Driver::connect($Qb[0],$Qb[1],$Qb[2]);return(is_object($J)?$J:null);}function
idf_unescape($s){if(!preg_match('~^[`\'"[]~',$s))return$s;$zf=substr($s,-1);return
str_replace($zf.$zf,$zf,substr($s,1,-1));}function
q($R){return
connection()->quote($R);}function
idx($_a,$v,$gc=null){return($_a&&array_key_exists($v,$_a)?$_a[$v]:$gc);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_searchable(array$j,array$X){if(!isset($j["privileges"]["where"]))return
false;$V=$j["type"];$kj=$X["val"];$Pa='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Pa~",$V))return
false;if(preg_match(number_type(),$V)){$Vg='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$Vg.(preg_match('~IN$~',$X["op"])?"( *, *$Vg)*":'').'$~',$kj);}if(preg_match('~^(small)?date|^timestamp~',$V))return(bool)preg_match('~^\d+-\d+-\d+~',$kj);if(preg_match('~^time~',$V))return(bool)preg_match('~^\d+:\d+~',$kj);if(preg_match('~^bool~',$V)||(JUSH=="mssql"&&$V=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$kj);return
true;}function
remove_slashes(array$Ul,$_d=false){$J=array();foreach($Ul
as$v=>$X)$J[stripslashes($v)]=(is_array($X)?remove_slashes($X,$_d):($_d?$X:stripslashes($X)));return$J;}function
bracket_escape($s,$Ia=false){static$cl=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($s,($Ia?array_flip($cl):$cl));}function
url_escape($R){static$cl=array();if(!$cl){$cl=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$bb)$cl[$bb]=sprintf('%%%02X',ord($bb));for($q=0;$q<256;$q++){if($q<32||$q>126)$cl[chr($q)]=sprintf('%%%02X',$q);}}return
strtr((string)$R,$cl);}function
min_version($Xl,$Rf="",$f=null){$f=connection($f);$Fj=$f->server_info;if($Rf&&preg_match('~([\d.]+)-MariaDB~',$Fj,$_)){$Fj=$_[1];$Xl=$Rf;}return$Xl&&version_compare($Fj,$Xl)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($ph,$Y){return(function_exists('ini_set')?\ini_set($ph,$Y):false);}function
ini_bool($Te){$X=ini_get($Te);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Te){$X=ini_get($Te);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($K,$Bh){$Vf=(int)ini_get("max_input_vars");return($Vf?(int)floor(($Vf-$Bh)/$K):0);}function
max_input_vars_error(){$Te="max_input_vars";return
sprintf('Maximum number of allowed fields exceeded. Please increase %s.',"<b>$Te = ".ini_get($Te)."</b>");}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($Wl,$O,$Nl,$F){$_SESSION["pwds"][$Wl][$O][$Nl]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$j=0,$Cb=null){$Cb=connection($Cb);$I=$Cb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$j]:false);}function
get_vals($H,$c=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$c];}return$J;}function
get_key_vals($H,$f=null,$Ij=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($Ij)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$i="<p class='error'>"){$Cb=connection($f);$J=array();$I=$Cb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$i&&(defined('Adminer\PAGE_HEADER')||$i=="-- "))echo$i.adminer()->error()."\n";return$J;}function
unique_array($K,array$u){foreach($u
as$t){if(preg_match("~^(PRIMARY|UNIQUE)$~",$t["type"])&&!$t["partial"]){$J=array();foreach($t["columns"]as$v){if(!isset($K[$v]))continue
2;$J[$v]=$K[$v];}return$J;}}}function
where_function($Sd,$c,array$j){if($Sd=="md5")return"MD5(".(is_blob($j)||JUSH!='sql'||preg_match("~^utf8~",$j["collation"])?$c:"CONVERT($c USING ".charset(connection()).")").")";return(in_array($Sd,driver()->functions)||in_array($Sd,driver()->grouping)?apply_sql_function($Sd,$c):$c);}function
where(array$Z,array$k=array()){$J=array();foreach((array)$Z["where"]as$v=>$X){$v=bracket_escape($v,true);$c=idf_escape($v);$j=idx($k,$v,array());$ud=$j["type"];$ff=$j&&(is_blob($j)||preg_match('~binary~',$ud));$J[]=$c.($ff&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$ud=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$j["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($ud,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($j,q($X)))))));if(JUSH=="sql"&&preg_match('~char|text~',$ud)&&preg_match("~[^ -@]~",$X))$J[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$v)$J[]=idf_escape($v)." IS NULL";foreach((array)$Z["col"]as$q=>$pb){$X=idx($Z["val"],$q);$J[]=where_function(idx($Z["fun"],$q),idf_escape($pb),idx($k,$pb,array())).($X!==null?" = ".q($X):" IS NULL");}return
implode(" AND ",$J);}function
where_columns(array$k){$J=array();foreach((array)$_GET["null"]as$v)$J[$v]=true;foreach(array_keys((array)$_GET["where"])as$v)$J[bracket_escape($v,true)]=true;foreach((array)$_GET["col"]as$pb)$J[$pb]=true;return
array_intersect_key($J,$k);}function
where_check($X,array$k=array()){parse_str($X,$eb);remove_slashes(array(&$eb));return
where($eb,$k);}function
where_link($q,$c,$Y,$mh="="){$jh=($Y!==null?$mh:"IS NULL");return"&where[$q][col]=".url_escape($c).($jh!=first(adminer()->operators())?"&where[$q][op]=".url_escape($jh):"")."&where[$q][val]=".url_escape($Y);}function
convert_fields(array$d,array$k,array$N=array()){$J="";foreach($d
as$v=>$X){if($N&&!in_array(idf_escape($v),$N))continue;$Aa=convert_field($k[$v]);if($Aa)$J
.=", $Aa AS ".idf_escape($v);}return$J;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($B,$Y,$If=2592000){header("Set-Cookie: $B=".rawurlencode($Y).($If?"; expires=".gmdate("D, d M Y H:i:s",time()+$If)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($B=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($Fl,$Ib){$http_response_header=null;$Yc=array();set_error_handler(function($Xc,$i)use(&$Yc){$Yc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$i);return
true;});$J=file_get_contents($Fl,false,$Ib);restore_error_handler();$ne=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($J,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($ne,0,''),$_)?$_[1]:''),(array)$ne,($J===false?implode("\n",$Yc):''),);}function
get_settings($Lb){parse_str($_COOKIE[$Lb],$Jj);return$Jj;}function
get_setting($v,$Lb="adminer_settings",$gc=null){return
idx(get_settings($Lb),$v,$gc);}function
save_settings(array$Jj,$Lb="adminer_settings"){$Y=http_build_query($Jj+get_settings($Lb));cookie($Lb,$Y);$_COOKIE[$Lb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($Hd=false){$Il=ini_bool("session.use_cookies");if(!$Il||$Hd){session_write_close();if($Il&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($v){return$_SESSION[$v][DRIVER][SERVER][$_GET["username"]];}function
set_session($v,$X){$_SESSION[$v][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Wl,$O,$Nl,$h=null){$El=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($Wl=='mssql'||$Wl=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$El,$_);return"$_[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Wl!="server"||$O!=""?url_escape($Wl)."=".url_escape($O)."&":"")."username=".url_escape($Nl).($h!=""?"&db=".url_escape($h):"").($_[2]?"&$_[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($z,$A=null){if($A!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($z!==null?$z:$_SERVER["REQUEST_URI"]))][]=$A;}if($z!==null){if($z=="")$z=".";header("Location: $z");exit;}}function
query_redirect($H,$z,$A,$Di=true,$ed=true,$od=false,$Pk=""){if($ed){$bk=microtime(true);$od=!connection()->query($H);$Pk=format_time($bk);}$Wj=($H?adminer()->messageQuery($H,$Pk,$od):"");if($od){adminer()->error
.=adminer()->error().$Wj.script("messagesPrint();")."<br>";return
false;}if($Di)redirect($z,$A.$Wj);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$H:(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";");}function
queries($H){remember_query($H);return
connection()->query($H);}function
apply_queries($H,array$U,$ad='Adminer\table'){foreach($U
as$S){if(!queries("$H ".$ad($S)))return
false;}return
true;}function
queries_redirect($z,$A,$Di){$yi=implode("\n",Queries::$queries);$Pk=format_time(Queries::$start);return
query_redirect($yi,$z,$A,$Di,false,!$Di,$Pk);}function
format_time($bk){return
sprintf('%.3f s',max(0,microtime(true)-$bk));}function
relative_uri($El=''){return
preg_replace_callback('~^[^?]*~',function($_){return
str_replace(":","%3A",$_[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($El?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Gh=""){return
substr(preg_replace("~(?<=[?&])($Gh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($B,$fc=false){$wd=$_FILES[$B];if(!$wd)return
null;foreach($wd
as$v=>$X)$wd[$v]=(array)$X;$J=array();foreach($wd["error"]as$v=>$i){if($i)return$i;$l=$wd["name"][$v];$Xk=$wd["tmp_name"][$v];$Gb=file_get_contents($fc&&preg_match('~\.gz$~',$l)?"compress.zlib://$Xk":$Xk);if($fc){$bk=substr($Gb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$bk))$Gb=iconv("utf-16","utf-8",$Gb);elseif($bk=="\xEF\xBB\xBF")$Gb=substr($Gb,3);}$J[]=array($l,$Gb);}return$J;}function
get_file($v,$fc=false,$mc=""){$zd=get_files($v,$fc);if(!is_array($zd))return$zd;$J='';foreach($zd
as$wd){$Gb=$wd[1];$J
.=$Gb;if($mc)$J
.=(preg_match("($mc\\s*\$)",$Gb)?"":$mc)."\n\n";}return$J;}function
upload_error($i){$dg=($i==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($i?'Unable to upload a file.'.($dg?" ".sprintf('Maximum allowed file size is %sB.',$dg):""):'File does not exist.');}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
utf8_length($X){return
strlen(preg_replace('~[\x80-\xBF]~','',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u','#,##0',$_);$Nj=strlen($_[3]);$J=number_format($X,0,".","");$J=preg_replace('~\B(?=(\d{'.(strlen($_[2])?:$Nj).'})*\d{'.$Nj.'}$)~',$_[1],$J);return
strtr($J,preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$T,$v){$X=idx($T,$v,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$xa=($v=="Rows"&&(JUSH=="sqlite"||$T["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($xa?"~ ":"").format_number($X);}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($S,$pd=false){$J=table_status($S,$pd);return($J?reset($J):array("Name"=>$S));}function
column_foreign_keys($S){$J=array();foreach(adminer()->foreignKeys($S)as$m){foreach($m["source"]as$X)$J[$X][]=$m;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$v=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$v];$_POST["fields"][$X]=$_POST["field_vals"][$v];}}foreach((array)$_POST["fields"]as$v=>$X){$B=bracket_escape($v,true);$J[$B]=array("field"=>$B,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($B==driver()->primary),);}return$J;}function
dump_headers($ze,$Ag=false){$J=adminer()->dumpHeaders($ze,$Ag);$Dh=$_POST["output"];if($Dh!="text"||$J=="tar"){$zb=($Dh!="text"&&$Dh!="file"&&preg_match('~^[0-9a-z]+$~',$Dh)?".$Dh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($ze).".$J$zb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){$ml=$_POST["format"]=="tsv";foreach($K
as$v=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($ml?'\t':'[,;]|^$').'~',$X))$K[$v]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($ml?"\t":";")),$K)."\r\n";}function
parse_csv($Tb,$vj){$J=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Tb,$Tf);foreach($Tf[0]as$K){preg_match_all("~((?>\"[^\"]*\")+|[^$vj]*)$vj~",$K.$vj,$Uf);$J[]=$Uf[1];}return$J;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?str_replace('""','"',substr($X,1,-1)):$X);}function
apply_sql_function($o,$c){return($o?($o=="unixepoch"?"DATETIME($c, '$o')":($o=="count distinct"?"COUNT(DISTINCT ":strtoupper("$o("))."$c)"):$c);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($l){if(is_link($l))return;$n=@fopen($l,"c+");if(!$n)return;@chmod($l,0660);if(!flock($n,LOCK_EX)){fclose($n);return;}return$n;}function
file_write_unlock($n,$Xb){rewind($n);fwrite($n,$Xb);ftruncate($n,strlen($Xb));file_unlock($n);}function
file_unlock($n){flock($n,LOCK_UN);fclose($n);}function
first(array$_a){return
reset($_a);}function
password_file($Ob){$l=get_temp_dir()."/adminer.key";if(!$Ob&&!file_exists($l))return'';$n=file_open_lock($l);if(!$n)return'';$J=stream_get_contents($n);if(!$J){$J=rand_string();file_write_unlock($n,$J);}else
file_unlock($n);return$J;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$y,array$j,$Nk){if(is_array($X)){$J="";if(array_filter($X,'is_array')==array_values($X)){$qf=array();foreach($X
as$W)$qf+=array_fill_keys(array_keys($W),null);foreach(array_keys($qf)as$of)$J
.="<th>".h($of);foreach($X
as$W){$J
.="<tr>";foreach(array_merge($qf,$W)as$Pl)$J
.="<td>".select_value($Pl,$y,$j,$Nk);}}else{foreach($X
as$of=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($of):"")."<td>".select_value($W,$y,$j,$Nk);}return"<table>$J</table>";}if(!$y)$y=adminer()->selectLink($X,$j);if($y===null){if(is_mail($X))$y="mailto:$X";if(is_url($X))$y=$X;}$X=driver()->value($X,$j);$J=adminer()->editVal($X,$j);if($J!==null){if(!is_utf8($J))$J="\0";elseif($Nk!=""&&is_shortable($j))$J=shorten_utf8($J,max(0,+$Nk));else$J=h($J);}return
adminer()->selectVal($J,$y,$j,$X);}function
is_blob(array$j){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$j["type"])&&!in_array($j["type"],idx(driver()->structuredTypes(),'User types',array()));}function
is_mail($Oc){$Ca='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Cc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Yh="$Ca+(\\.$Ca+)*@($Cc?\\.)+$Cc";return
is_string($Oc)&&preg_match("(^$Yh(,\\s*$Yh)*\$)i",$Oc);}function
is_url($R){$Cc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Cc?\\.)+$Cc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$R);}function
is_ipv6($ia){$p='[\da-f]{1,4}';$ef='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($p:){7}$p|($p:){6}$ef|(($p:)*$p)?::(($p:)*($p|$ef))?)$~iD",$ia);}function
is_shortable(array$j){return!preg_match('~'.number_type().'|date|time|year~',$j["type"]);}function
url_host($ve){return(strpos($ve,":")!==false?"[$ve]":$ve);}function
server_parts(array$Th){return
array("scheme"=>(string)$Th["scheme"],"host"=>(string)$Th["host"],"port"=>(string)$Th["port"],"socket"=>(string)$Th["socket"],"path"=>(string)$Th["path"],);}function
parse_server($O){if($O=="")return
server_parts(array());if($O[0]==":"&&!is_ipv6($O)){$Qi=substr($O,1);if(preg_match('~^\d+$~D',$Qi))return
server_parts(array("port"=>$Qi));return(preg_match('~^/[-\w.:/]*$~D',$Qi)?server_parts(array("socket"=>$Qi)):null);}$ij="";if(preg_match('~^([-+.\w]+)://~',$O,$_)){$ij=strtolower($_[1]);$O=substr($O,strlen($_[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$O,$_))return(is_ipv6($_[1])?server_parts(array("scheme"=>$ij,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4])):null);if(is_ipv6($O))return
server_parts(array("scheme"=>$ij,"host"=>$O));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$O,$_))return
server_parts(array("scheme"=>$ij,"host"=>$_[1],"port"=>$_[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$O,$_)?server_parts(array("scheme"=>$ij,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4])):null);}function
count_rows($S,array$Z,$gf,array$p){$H=" FROM ".table($S).($Z?" WHERE ".implode(" AND ",$Z):"");return($gf&&(JUSH=="sql"||count($p)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$p).")$H":"SELECT COUNT(*)".($gf?" FROM (SELECT 1$H GROUP BY ".implode(", ",$p).") x":$H));}function
slow_query($H){$h=adminer()->database();$Qk=adminer()->queryTimeout();$Oj=driver()->slowQuery($H,$Qk);$f=null;if(!$Oj&&support("kill")){$f=connect();if($f&&($h==""||$f->select_db($h))){$rf=number(get_val(connection_id(),0,$f));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$rf&token=".get_token()."'); }, 1000 * $Qk);");}}ob_flush();flush();$J=@get_key_vals(($Oj?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$Ai=rand(1,1e6);return($Ai^$_SESSION["token"]).":$Ai";}function
verify_token(){list($Yk,$Ai)=explode(":",$_POST["token"]);return($Ai^$_SESSION["token"])==$Yk&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($R,$sc=""){$ta=array_flip(str_split(compress_alphabet()));$w=strlen($R);$Sl=($w?13*($w-1)/2-$ta[$R[0]]:0);$Pa="";$Qi=0;$Ri=0;for($q=1;$q<$w;$q+=2){$Qi=($Qi<<13)+$ta[$R[$q]]*93+$ta[$R[$q+1]];$Ri+=13;while($Ri>=8&&$Sl>=8){$Ri-=8;$Sl-=8;$Pa
.=chr($Qi>>$Ri);$Qi&=(1<<$Ri)-1;}}if($Pa=="")return"";if($sc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$sc)),$Pa,ZLIB_FINISH);return($sc==""&&function_exists('gzinflate')?gzinflate($Pa):inflate($Pa,$sc));}function
inflate($Pa,$sc=""){$Ff=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$Gf=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$wc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$yc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$J=$sc;$G=0;do{$Ad=inflate_bits($Pa,$G,1);$V=inflate_bits($Pa,$G,2);if(!$V){$G=($G+7)&~7;$w=inflate_bits($Pa,$G,16);$G+=16;$J
.=substr($Pa,$G>>3,$w);$G+=$w<<3;}else{if($V==1){$Nf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$zc=array_fill(0,30,5);}else{$Mf=inflate_bits($Pa,$G,5)+257;$xc=inflate_bits($Pa,$G,5)+1;$D=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$qg=array_fill(0,19,0);$pg=inflate_bits($Pa,$G,4)+4;for($q=0;$q<$pg;$q++)$qg[$D[$q]]=inflate_bits($Pa,$G,3);$rg=inflate_table($qg);$Hf=array();while(count($Hf)<$Mf+$xc){$nk=inflate_symbol($Pa,$G,$rg);if($nk==16)$Hf=array_merge($Hf,array_fill(0,inflate_bits($Pa,$G,2)+3,end($Hf)));elseif($nk==17)$Hf=array_merge($Hf,array_fill(0,inflate_bits($Pa,$G,3)+3,0));elseif($nk==18)$Hf=array_merge($Hf,array_fill(0,inflate_bits($Pa,$G,7)+11,0));else$Hf[]=$nk;}$Nf=array_slice($Hf,0,$Mf);$zc=array_slice($Hf,$Mf);}$Of=inflate_table($Nf);$Ac=inflate_table($zc);while(($nk=inflate_symbol($Pa,$G,$Of))!=256){if($nk<256)$J
.=chr($nk);else{$w=$Ff[$nk-257]+inflate_bits($Pa,$G,$Gf[$nk-257]);$_c=inflate_symbol($Pa,$G,$Ac);$ah=strlen($J)-$wc[$_c]-inflate_bits($Pa,$G,$yc[$_c]);for($q=0;$q<$w;$q++)$J
.=$J[$ah+$q];}}}}while(!$Ad);return($sc==""?$J:substr($J,strlen($sc)));}function
inflate_bits($Pa,&$G,$Nb){$J=0;for($q=0;$q<$Nb;$q++){$J+=((ord($Pa[$G>>3])>>($G&7))&1)<<$q;$G++;}return$J;}function
inflate_table(array$Hf){$S=array();$ob=0;for($Qa=1;$Qa<=max($Hf);$Qa++){foreach($Hf
as$nk=>$w){if($w==$Qa){$S[$Qa][$ob]=$nk;$ob++;}}$ob<<=1;}return$S;}function
inflate_symbol($Pa,&$G,array$S){$ob=0;$Qa=0;do{$ob=($ob<<1)+inflate_bits($Pa,$G,1);$Qa++;}while(!isset($S[$Qa][$ob]));return$S[$Qa][$ob];}function
script($Sj,$bl="\n"){return"<script".nonce().">$Sj</script>$bl";}function
script_src($Fl,$jc=false){return"<script src='".h($Fl)."'".nonce().($jc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($bd,$ee,$ya=null){$za=array();foreach(array_slice(func_get_args(),2)as$X)$za[]=json_encode($X,256);return" data-on$bd='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$ee(".implode(", ",$za).")")."'";}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($R){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$R);}function
nl_br($R){return
str_replace("\n","<br>",$R);}function
checkbox($B,$Y,$hb,$vf="",$b="",$mb="",$xf=""){$J="<input type='checkbox' name='$B' value='".h($Y)."'".($hb?" checked":"").($vf==""&&$mb?" class='$mb'":"").($xf?" aria-labelledby='$xf'":"").$b.">";return($vf!=""?"<label".($mb?" class='$mb'":"").">$J".h($vf)."</label>":$J);}function
optionlist($C,$rj=null,$Jl=false){$J="";foreach($C
as$of=>$W){$rh=array($of=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($of).'">';$rh=$W;}foreach($rh
as$v=>$X)$J
.='<option'.($Jl||is_string($v)?' value="'.h($v).'"':'').($rj!==null&&($Jl||is_string($v)?(string)$v:$X)===$rj?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($B,array$C,$Y="",$b="",$xf=""){static$vf=0;$wf="";if(!$xf&&substr($C[""],0,1)=="("){$vf++;$xf="label-$vf";$wf="<option value='' id='$xf'>".h($C[""]);unset($C[""]);}return"<select name='".h($B)."'".($xf?" aria-labelledby='$xf'":"")."$b>".$wf.optionlist($C,$Y)."</select>";}function
html_radios($B,array$C,$Y="",$vj=""){$J="";foreach($C
as$v=>$X)$J
.="<label><input type='radio' name='".h($B)."' value='".h($v)."'".($v==$Y?" checked":"").">".h($X)."</label>$vj";return$J;}function
confirm($A=""){return
on('click','confirmClick',$A?:'Are you sure?');}function
print_fieldset($r,$Ef,$am=false){echo"<fieldset><legend>","<a href='#fieldset-$r' class='toggle'>$Ef</a>","</legend>","<div id='fieldset-$r'".($am?"":" class='hidden'").">\n";}function
bold($Ra,$mb=""){return($Ra?" class='active $mb'":($mb?" class='$mb'":""));}function
js_escape($R){return
str_replace("<","\\x3C",addcslashes($R,"\r\n'\\"));}function
js_escape_re($R){return
addcslashes(preg_quote($R,"/"),"\r\n");}function
pagination_href($E){return
remove_from_uri("page|next").($E?"&page=$E".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($E,$Ub){return" ".($E==$Ub?($E?"<b>".($E+1)."</b>":$E+1):'<a href="'.h(pagination_href($E)).'">'.($E+1)."</a>");}function
hidden_fields(array$ui,array$Ce=array(),$ni=''){$J=false;foreach($ui
as$v=>$X){if(!in_array($v,$Ce)){if(is_array($X))hidden_fields($X,array(),$v);else{$J=true;echo
input_hidden(($ni?$ni."[$v]":$v),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$Dl){$Dl=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($Dl?on('submit','uploadProgress',ME."upload=$Dl",SESSION_NAME."=$Dl"):"");}function
file_input($b,$Qi=""){$Xf="max_file_uploads";$Yf=ini_get($Xf);$dg="upload_max_filesize";$eg=ini_bytes($dg);$ki=ini_bytes("post_max_size");if($ki&&$ki<$eg){$dg="post_max_size";$eg=$ki;}$fg=ini_get($dg);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$Yf,sprintf('Increase %s.',"$Xf = $Yf"),$eg,sprintf('Increase %s.',"$dg = $fg")).">$Qi":'File uploads are disabled.');}function
enum_input($V,$b,array$j,$Y,$Rc=""){preg_match_all("~'((?:[^']|'')*)'~",$j["length"],$Tf);$ni=($j["type"]=="enum"?"val-":"");$hb=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($j["null"]&&$ni?"<label><input type='$V'$b value='null'".($hb?" checked":"")."><i>$Rc</i></label>":"");foreach($Tf[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$hb=(is_array($Y)?in_array($ni.$X,$Y):$Y===$X);$J
.=" <label><input type='$V'$b value='".h($ni.$X)."'".($hb?' checked':'').'>'.h(adminer()->editVal($X,$j)).'</label>';}return$J;}function
input(array$j,$Y,$o,$Ga=false,$Al=false){$B=h(bracket_escape($j["field"]));echo"<td class='function'>";$Wc=driver()->enumLength($j);if($Wc){$j["type"]="enum";$j["length"]=$Wc;}$C=($j["type"]=="enum"||$j["type"]=="set");if(is_array($Y)&&!$o&&!$C)$o="json";$mf=($o=="json"||preg_match('~^jsonb?$~',$j["full_type"]));if($mf&&$Y!=''&&(JUSH!="pgsql"||$j["type"]!="json")&&(is_array($Y)||!$_POST["save"]))$Y=json_encode(is_array($Y)?$Y:json_decode($Y),128|64|256);$Pi=(JUSH=="mssql"&&$Al&&$j["auto_increment"]);if($Pi&&!$_POST["save"])$o=null;$Td=(isset($_GET["select"])||$Pi?array("orig"=>'original'):array())+adminer()->editFunctions($j);$b=" name='fields[$B]".($C?"[]":"")."'".($Ga?" autofocus":"");echo
driver()->unconvertFunction($j)." ";$S=$_GET["edit"]?:$_GET["select"];if($j["type"]=="enum")echo
h($Td[""])."<td>".adminer()->editInput($S,$j,$b,$Y);else{$ge=(in_array($o,$Td)||isset($Td[$o]));$Bd=0;foreach($Td
as$v=>$X){if($v===""||!$X)break;$Bd++;}echo(count($Td)>1?"<select name='function[$B]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($Td,$o===null||$ge?$o:"")."</select>":h(reset($Td)))."<td".($Bd&&count($Td)>1?on('input','skipOriginal',$Bd):"").">";$Ve=adminer()->editInput($S,$j,$b,$Y);if($Ve!="")echo$Ve;elseif(preg_match('~bool~',$j["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($j["type"]=="set")echo
enum_input("checkbox",$b,$j,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($j)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($mf)echo"<textarea$b cols='50' rows='12' class='jush-json'>".h($Y).'</textarea>';elseif(($Mk=preg_match('~text|lob|memo~i',$j["type"]))||preg_match("~\n~",$Y)){if($Mk&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$L'";}echo"<textarea$b>".h($Y).'</textarea>';}else{$rl=driver()->types();$pl=$rl[$j["type"]];if(preg_match('~date|time|year~',$j["type"])){$Od=(preg_match('~time~',$j["type"])&&preg_match('~^\d+$~',$j["length"])?$j["length"]+1:0);$gg=($pl?$pl+$Od:0);}elseif(!preg_match('~int|vector~',$j["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$j["length"],$_))$gg=(preg_match("~binary~",$j["type"])?2:1)*$_[1]+($_[3]?1:0)+($_[2]&&!$j["unsigned"]?1:0);else$gg=($pl?$pl+($j["unsigned"]?0:1):0);echo"<input".((!$ge||$o==="")&&preg_match('~^'.int_type().'$~',$j["type"])&&!preg_match('~\[]~',$j["full_type"])?" type='number'":"")." value='".h($Y)."'".($gg?" data-maxlength='$gg'":"").(preg_match('~char|binary~',$j["type"])&&$gg>20?" size='".($gg>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($S,$j,$Y),(count($Td)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$j){$s=bracket_escape($j["field"]);$o=idx($_POST["function"],$s);if($o=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?idf_escape($j["field"]):false);if($o=="NULL")return"NULL";if(is_blob($j)&&ini_bool("file_uploads")){$wd=get_file("fields-$s");if(!is_string($wd))return
false;return
driver()->quoteBinary($wd);}$Y=idx($_POST["fields"],$s);if($Y===null)return
false;if($j["type"]=="enum"||driver()->enumLength($j)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($j["auto_increment"]&&$Y=="")return
null;if($j["type"]=="set")$Y=implode(",",(array)$Y);if($o=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($j,$Y,$o);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$uj="<ul>\n";foreach(table_status('',true)as$S=>$T){$B=adminer()->tableName($T);if(isset($T["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($S,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($S)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($S),array(),$T)),1));if(!$I||$I->fetch_row()){$si="<a href='".h(ME."select=".url_escape($S)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$B</a>";echo"$uj<li>".($I?$si:"<p class='error'>$si: ".adminer()->error())."\n";$uj="";}}}echo($uj?"<p class='message'>".'No tables.':"</ul>")."\n";}function
on_help($Mk,$Mj=0){return
on('mouseover','helpMouseover',$Mk,$Mj).on('mouseout','helpMouseout');}function
on_help_value($Ki="",$Oi=""){return
on('mouseover','helpValueMouseover',$Ki,$Oi).on('mouseout','helpMouseout');}function
edit_form($S,array$k,$K,$Al,$i='',$H='',$Pk=''){$vk=adminer()->tableName(table_status1($S,true));page_header(($Al?'Edit':'Insert'),$i,array("select"=>array($S,$vk)),$vk);adminer()->editRowPrint($S,$k,$K,$Al,$H,$Pk);if($K===false){echo"<p class='error'>".'No rows.'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$Mc=false;$gm=($Al&&!isset($_GET["select"])?where_columns($k):array());$Jb=(count($gm)!=count($k));if(!$Jb)$gm=array();if(!$k)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ga=!$_POST;foreach($k
as$B=>$j){echo"<tr".($gm[$B]?on('change','whereChange'):"")."><th>".adminer()->fieldName($j);$gc=idx($_GET["set"],bracket_escape($B));if($gc===null){$gc=$j["default"];if($j["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$gc,$Mi))$gc=$Mi[1];if(JUSH=="sql"&&preg_match('~binary~',$j["type"]))$gc=bin2hex($gc);}$Y=($K!==null?($j["type"]=="set"&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$Al&&$j["auto_increment"]?"":(isset($_GET["select"])?false:$gc)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$j);if(($Al&&!isset($j["privileges"]["update"]))||$j["generated"])echo"<td class='function'><td>".select_value($Y,'',$j,null);else{$Mc=true;$o=($_POST["save"]?idx($_POST["function"],bracket_escape($B),""):($Al&&preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Al&&$Y==$j["default"]&&preg_match('~^[\w.]+\(~',$Y))$o="SQL";if(preg_match("~time~",$j["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$o="now";}if($j["type"]=="uuid"&&$Y=="uuid()"){$Y="";$o="uuid";}if($Ga!==false)$Ga=($j["auto_increment"]||$o=="now"||$o=="uuid"?null:true);input($j,$Y,$o,$Ga,$Al);if($Ga)$Ga=false;}}if(!fields($S)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Mc){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"])&&$Jb){$tc=($gm&&($i!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Al?'Save and continue editing':'Save and insert next')."' title='Ctrl+Shift+Enter'$tc".($Al?on('click','ajaxForm','Saving…'):"").">\n";}}echo($Al?"<input type='submit' name='delete' value='".'Delete'."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($Yh,$w){return
str_repeat("$Yh{0,65535}",$w/65535)."$Yh{0,".($w%65535)."}";}function
shorten_utf8($R,$w=80,$jk=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$w).")($)?)u",$R,$_))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$w).")($)?)",$R,$_);return(isset($_[2])?h($_[1]).$jk:h(preg_replace('~\n[^\n]*\z~',"\n",$_[1]))."$jk<i>…</i>");}function
icon($ye,$B,$xe,$Sk,$b=""){return"<button ".($B?"type='submit' name='$B'":"draggable='true' tabindex='-1'")." title='".h($Sk)."' class='icon icon-$ye".($B?"":" jsonly")."'$b><span>$xe</span></button>";}function
copy_icon(){$Mb='Copy';return"<a href='' class='jsonly icon-copy' title='$Mb'><span>$Mb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('&c(<]iDp;+<8]XG-X#ET@P~g+44jkGE
JJMWB[P=;i#!X$XZ(f>Cx5+d&ydL<""!*rBpff!fkm^bQBjtpU)4hnlgdu9u"]lNs!9kv`}pLnMu[)!?
/nW}!<x5Ey]-bY_G+1cjtywbHdyuU[S<dax0
Hp*-71^<Q=:@:nTh|W:o^Mww"/r*_nT8FCXI``P&A[5^Z%0OT*zx^Qb+nx0qMeS5DapbVg]7?)iJ*"[4}C}*JqDk!0#.uZ{4cX*U8U!(c3>W+"$E"oK-|>@iWX6l|1W=f$g36bV,e8dwLN)WK,R-6f"$IOZ^&_g;N%[.eN;seVrL3)^@fQ(N)+I_+gr(D**O1?;]IF:DMgNjlmV/-JRE},jU`0E2b2_Fpf9

)@uK@TEX-E0q&d=R):2^8UCGmhA|!EnaiTCO9E]<D0H?4_DJf^E8g[o_!1xI!9j?+-`Te!B-`bsPmV=<$,`bFq(52j+hR#Loh4Z}^`&?XpC=4^4pi:lSI}%9<`@NphbTIeYKquP`DgCzfIErRzu1$VT.5awB]A5%[)d{5>a=mnN&rWqBo=.X
Tw&_{V
%Sq9f#Zp&+ZNQ$++;qk~9wv.sKdb"*)BaZ-Rd8a*dAT@)}2F<vSWC)>4(fBxIN6/FUTxn7"S8sH._j
MZyJaF1No9&(oC9Mj$!&r8G6`DEb/r[V9k:,KeQN$Nwm)C1*6S8"llf@pFAAoG/4D@sAUMa$tH0Nxe=WAn8ClYb]V:PG5NQvPEQ9MY9a"sK8d$sbwSU9DlE^VUB#8K_[0Il&{[@9qK~em90k2Er
iHE.IGE5lo=3Fm@1=MM34!r=KC}^AB%"z=;3e,S7Dr[A__{9aL7@?3]wUkew{s
Kr!tOUQ-Od;Xa6m5Lry}k~Z=lWG79i_<MR<{""v1@`P$4PJ;aven@*_GdL;_o"lx_$c<8y9IWTU|Osq9JkImMylT@c$N(Ln|xF2ss%m473U{*{D&9j!_shhxM3N9+>!6ukH~>U
H6~=q:K!~4_*?a.4,ptom8{8q3gi"*zP4(s").ZttK/:m
,t"S~Q*$5DrPlMp-tKNCfZarEOEgXu9y0WwS(y~/"Un1KV"hvF`RGjMPEjEne@V+cmCuTMpXT
<t`2aO<&@?M[UKSJ+*(YwINSt.pPpSJ;Pn5PD=Gc@R=fm!.[e,wG]@IPL]wWa`gc/:~U:Wc$DJ3Gj+
2eUL"[c!@Q8p/,S~mqr"<?
0XMM:b3+dE#]9i$gNv-yec"LJ(+ph]UoG0`k!AB#F!a[~"@r{?A`C#3&71G"4N)hTgk;>?fkeTe)six>@!2!b$R[u0<Md[!hN7}_o1btnf93]BD9{VpB7;n2}HZpvTVo}"
"mq2=-TaS&Q9bUSx
m8v3jj]B%?BHH/CR?FCX_P>PM^]p}*zU98:7d>nF79Q=@92WSCV!/R^0!J2RHE/C<o_N.3^=^6>mK*`hrF`bmW7=kKgPpE@c[x}cOZ[.MuN]nA82wYRQ8aZFL[SRTJA,U_~([MmpHPx&0YNv.m|cO3FQP%+]W#l?:I7(8
Qt@Y,sn_!b6U.0s52(&_K
beRDYJFd8xxs
y7>Hsl2^V9ah^&4i7&"0LF2Jlf[4*eQJeztm
JIetUG(Umx<Dk.qq"liXSR7P4F:]|9:iolL8zc]6"%%C00zP5j&j:Fp,%VTWQDr$"&D-Q&Qpv*oEM1[cNQ_1h%h%fVP0zlH<t,;#H#%e.Fctm:?&09}8ejQ*#ymG%jTVfbjP6;CorQ=bgXl.5%mFrp[6o*F;W8G^}-l`bf]DL*(kY"xL(><J6DZ_Rlgkh]45v^dCUo56(_=9-sm*<t1iF
)4~N~y7m1DJ-;BB*/=;Yg4XxvvRVBmW#.dz#B8/X+<l42=Z2,5[02HjT3Di@]*OIO&j7M^_O)poc3*pnK_@!
ZXx#*C:2R*2aX>hFKR_Sf$CNf3PXwcyB#C.PPia{43H8rWms:/D]=
`P(1b)vuJ]-DX3OymFG/^jL=62O+EV]OZI#L3(RXt^6GGf2SmK"w3"oMIIpDk=
nhqe5h`l}R}m[E~rO(2Y(FP.Wp8rOdY&o>@&ChKb<[1,
P}
y9--6(;1qd/E^LVj`I?CT!xlqLeQzbctp%/D45[9
"b*;u
h?yA5i"gG,jX2uN?QRi]3~8m8R(p(vf8o-qldR!QpK-hY3<)lT_E?$&2M*";9z
j7M[dJ)y!BJxwJ8J~hxwN_2,:qA07U.1,+koMfT-GI%xZ+Q$Jn9x)jLKPYSz"RfZ/`?j&"6$Sb91?&EC|<cJhdb=#B=l`m%]H/^$,IRO~y}lpNeoGTY8>ms9r.i&[,1j=GllhJuQO#+J|^PA.xi+4o1hK7Zg0rGb@qM#[aE8y/4WT;H@o"g-,G:,mE0cR$z9kE_L7MR:C(+SqQ:=HZ|[y8}
:hIGZCd$t&:ipF5IEU0:/?]o.k@3-l&W~<d!SCn;ArSFfgBCZ+7HwO$2O8.L~]<>w_i#jz$N/K.EsFVmy=@*XvZ$|C[m)I*4u1x+IC[B*e8=Mv;6C=m&HjrVB8^8tl/%mG5AJqLEqV_.
raqA(w^!JQr~HA2RrIrZD!
dKb`Z$h%b^V-Tk!MQ
W^>#@-1ECw:Y]wCsC!)z(FVv]N$R2A>wzA]B=@5IvZlb@(S2QTwRYw`]}qM6q56IV"N+4h*rAmJa;amtJwpnA-:,lPYy]!Pq!Mrl1r{_ba0@qW]w=+[vZyf[Cq5oTK;
-q!xZ,of!b-XuG`));GLkyiuX^:uXjf)hOEwwBTBuoDBaB;pFpK5ZnJVU,^B-+X4[z%`bMc)LnsvWZp]<crLlyGNXw2f0s4R,6tJ}R;fGx?M9SS`Z9`z#DG<wQMA~c#nmiLtU(SB1]&F;jScPh/>KCqskFd7%;z6xBb
+yww]o6,iy@NZ-&%]Aro:UgqKy$Ggs"]P@I7;MI`y`)d,dEBuV;6mj~
r0R3t,?Ttu<BBh]#-Eax`c#8<#iH@hq*y]HS;pZGw4kIg+pHO$nq0X!UA(2*BY@Ko
U]|f1]S03<5(Gp58GG(j_yMN=V"2i7zuMax#/4g=*M/H|^z$Q.rL1dxgIvqFHSX:jnkhldH?T!v/aS^d<FlQa@;3nOpCKrTmdt(m3bGdSpQl?iVIaCp
,;B4T*yH<orQLC7svv_Y6<8>(i:s5m2$bL`[@XQBIJS7dojJ@&?86w8J5lx6+b}t6:7IV4TUtyPb-yyvli0t1$=NAp+z%q+bW5gg
w>4qL[w=>mxM3n_%NlV)@x)+m?94$
bsT+@?R1)Kqa;g]6k?#65eL212qW[
6NadoRd+C<D8-.rJga:M6$+8(NOuc2@Ffg.^)Yf{wc+4MAx)QF?<SwBii&08AiS]Y&RF7mR59Y/o;8bo(r7xlSXuFAl6Y+$>"dEOPhM>LRe#oxJg[][iQj&n<X)L%ru^.nci31X2TN7{6Fc$+}?qR(k*Z_p24n):;%s]@/?<SmHQy=%`DM"2v-1Ln|dk9WUd,U>h]b`$d&P/CPM;#iH~TnmpB2Dr@D$6
)3^7YLMcULxiqQBn2
i/mGe0ya:1{L?k}%!wtJl+3D:03naJzsG(zjZ0n7fg4G9`BM:Wyy.mdVLv
Ov`qxB)v:kz)[.4C73^EOH&
3?V<M{s{_hM?lV>_VI38B&$mPs.(ASw?[B.%Es
pl>sgD+n!aDgUnasypNOi8I[yc/Im+wnlPDTM?.RC?h.%Oo.E_<5qnlgG<z5IY<VPLDY;pRatkmP"G8MM/CVR<_/^[rwR]I"-q&R{=.r|`GE:po4vXi=HL7:Krmy9i/"is[er&L.|YD[;NTo3_jT{3Li6;IDNKXYvI)Kn$&CqRbMppf69?J,a]Ch$BhO_%^S1]GVJ"3iqK%?lH;?B/qD-2&tOOuG0Qasf7maik>++jPLp.k+1n&mOi[(ZS}
H5]<I9/!HL[u^$RW2I`1K"SD=I+.+vKi>^W
)0_6&^)hx.LX"V6n;S~Zh62V"7>X!Xn"s8,-G0dy|Mn>Kfh&9gU]wceog<M){aw3rFpa#u&kTh%XTN5LO`R,3t5"YxO,!c[yB`St$]^K..mU[]dvHp1JUwcP>P8BjJM$B%!NP+y%"!%_rhw<J][hJr3c/`T?F4vjd0X[c,-.KI_o1iGA%6^!aHi%I3P*-KikjbcAM#80I^PK0Tq=Jr-HH*x&(RIa"Bpk"^~vbVRrTHA[KhLz$a&_g(Zq)LA4vugp@qPdy7{r1GqBYu@som-
y)S$><*`e=yC}S;vuIY4WGlsgp#[o>x+%HEcM+"27k-oInllT&Ka]C{w5ldP<w]a$FbKcx1Kj`sVdbipVpUX&Hh9a$^*.
e9^6{)<@lw.C3Oh:DR)ZlfwMPY6!{1IT/Ri=G1=8{@5d"bv6|yl$M]^4>"k)3dxj_<w@e`qv_BoLj#.6;Rl:h^"Ku`0pwd2hguL+CjYTOc
a=npT;Q:g!R.C#R"');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(')OsbOb3V?!K0U*,j#-$TY2N&[`b!>wsTd_N`GuxPN9GOol*1@VDLlh_fdc430fu#lZ-r!f<.+=s=X(J2e>*"$r2geZo4@leYjQ1%,Ya^fK)KWrns9HN3Za[M&Ua[o)7sBH/u8kXg}4drw:$n$88?$
q.DLTGX#<D1t"V<MYp_Ma&R!lNy=^42%5+QTJ"M_zEIVt2b&@<iW5HXxa7"+HENrVp[-(?;l^q7O9Hb]:Sr
,WOw[;eXJ3/AYxWiY8v=afr;mm
2j7~=*!Bp~Z"dLH|e`)gkNjaXDNCg,tOd/Bee9aAhUna-ZLB;OF8<%r2e1x*xX$ZiG_Ot<kzJ%FMb$)(Q`hL2F*U3b$cI[XzX_yVm!=X`6&,RA>7e!9gn|F:S?FGgzw]+AWONX6E]$Hu$5^-Av"t[SRPD-dDP9jn"tZoFsSBWi!U
]MxVmGbSp6ix~D-FZ7DoJXY/zE9!l0/]_ZhqV=[.*yn"zS|U3V:p0%cK5pT+2_?0*<"/w-9$DgzF7#yWi<W,3"4>QoJftal+Tm>(PeM9JHTs;vxkWm9$<A7*iHsBl8Ig]>qQ38jy4P@0/ej$G,X[`Y>gf_|8q*^2Dnu#YI<#>h+;DK|$/DDimVm(m`WCVEYX1jS%84q"FCpAaU/4Yf
Q<ovd>ujL>jlSK$ADUHDsn1a>o@
;@5f]$+ZQNcbu-^=v>xaijt5[sMndunEa-5T28EWI"G!j1uhd)s:ch9c-:STXv8Dq82x=D]meVP[+d`LIY+k0"G?9H47
NBubq<z`![Z&|@7?P6j_[UcU{fnW0X^j_=5(,s<ii_zJS27M>X{xnK3M[W-rsA0k}H{mrK*vZ2&pNC@DA0;NWwLj&)j-eg5PfwA;O70]r,58hd_Eqn{Y@Ws+We9XpZFh)z(-@LIrbPy8da(hAcZV#?1X}E7dx7tw`28WL.XVqgdV!&yvq?3hO5.EHdr-kP>4[llRl9i0C+sj[+"u^v6Y#jXxd');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('#c4]`nsZ32#tW"t=D[}-dt|D
t4.fB*UvVm*X5y`rIcq94l$pS];=:p"0Z-:)cc`G+XY!YDcCJS7Ye"8kvSK!rgB,
QGJBTN|9mkJ=oHNq=
u]0z"ZHVgjFqY]+!jcRjDHI9j.ysjATBA2+)D`tcn";#,vw[ec:F"59cxe:"MJsOExc#f]Jg3B4x(I*HPir:p%TWdy7<JoX
iM~/S"3o!mqy^s(l{673#
UR+s.b5"R*rVPQ)5R>!BUk]Meh`t?]]h)NXY_6dtZ`u<ni1
t#S`]*gu(E`2MmXu"JjrVD{.|Ews"l1_B>"X)U)FYUs-SK|fcE<.1w]=xN?+w:atRAu#>28:4Fx6t0nVm=*PMYYT5c:pQ0`UuR^"GtbTV
Sv4(hKlWQ)A^,D[.qZthMKtOz=(pI`auf/O1Jk%HeF%q2;XksE`/(bq7yT!`{.1@_;~Q7jS[7L2t="<22MYBka8;8t=v.Oovq[@d"hqnY/`L,M>^Mxaw?CcwvZESN346m,i`yy5`os@!KIs
LwX0>aHRquo@LfTs;:(QjF]$(c~F_5+yGP[WAhmMSqi#mY5=2y6[u7$+$G;5a3v.bZOGDv:m:HuWpq^:6yhmi?e&wvYRYq"=:9cRVLDk%LObrwOwi,`nj6crk7rUJH48n1gc0]3B2-l5|qN2!FVaqNLA$Sb-F)5B}:)hPrSo,E#]l:nJ:h]$0u9.Jtj]KU,.Z3lPi6iJ47DGK#ZU]O#`SW[OgIS9pM:
3*oro2*HBY`+ddG/M_juYpRw-Q6@chBx[$q:)#rJw!OCE[@#xk(.LZRqw`>nJ-"#8opCF`pa
2r2`a7cu9)ug0t%KI62Q06yM("TW<3sgmUjOL!=f-)x7T5A6.t"]LwxS@-Zvy6C]G]S!a5
iXSu,;tb]-CQ<y<l$(+Bvx%FpjdKq537f+!1^v4>6BaZI>zWN"RCYCy$
?(>9Kt@N(S8I?qTI4F2.2(Dou)eZ"YmpCZ(=[vNk3z:!tWTqB-UZ-x7z<7np_N:EUG=#,`DoAKA3RdcOfDmS!?m$
*hz5492s_]Y=:`{r418#G4@=u5Q?-
70qTmA.U~PmF"N0=2P3w_nu+(C]6el:LD+q,<@=U2_q/3&0-
;V8vYB]HUVKkuFNe1,c4MFm&AYT9uH8E+ia(6E:mlzZ9>n$t.Mcb<~qnp7$`SuT{_yrN5NmfpLyb(K&+o3SVsu#1Rh7O?h`0Yfoeo0s
f|#]X6RVTDd?ndC&kAsE*@waxjA(*.)=Vi=7ofQ-TtY3Jpw/tSOG>C67LyCrh}Ps/whiIsu&De(lN}IG5HK"OsQ,&e-HG~%_?q(.g8?#N70!A?cYW
o
XDC`S"-g9PBCP=8+
d>K)b4KSJ;4f_JHM^k%Q|o^yA1%#?GG>^x#lnL{c$s}jpy$sh_7sL6}Y&&ML+^NlAd+4,mW/G^NS}9Gp5EaHRU#8-V8KB0F(C-UyCZ?n]Q3Bia"FJJ)z(%FR)XSiI$U&<f.0JXa&oE9>dPR+dB0M@yiIV"
cLcFA0&*NCIl3K_3iPNuE>.Q<B#3H3eqx.%?
GGUL:<nOsDU1oH1XI+]riTK`G<:0/Z[,XiM3BOL7;^$W)`X$;)8jXcN27Q$v[3Cb}GaN>-<1W]o=%HX0k=@3*N+CSyw3?D/^>#AG>daACASsOj"?%wM!rLf!|k47}8$][5oTW2B16"e*
j![q(u?3gj._<w6=6&@vSNWGNd"sF`M(J2]Q#~0C"{S[k&s.%8D~8{J)Q_H697M6y.I_Z/2c^GrF%6*RH?2!XO]N0~8&C2Y(^-YTXOS=!>Pk2:<GrA.+MfW6#R<n5iF~,l&)3tRMxJsq4YRQFJ;Txe!6M?(XlCs.K`X*G8HWf|LC]3$CH|W(,]FdD4ZGS7vxvl2w]rx.2;-]A9Y}+,?<#<;
)?PiuI2"w4K@KmuC%m96tK!?pfRhNO5bH~<%rAyE*i<6L/<7Zt3tvphyP-gTn#`12@r]s$d5T/
6cjl0%JXj.10y,p8Zl3eR/aThJd^L6FM>1*:_K>X.rP8X7;bJ@<A*Fo%qFCr~GsOq@+9Z_zi)O[R-NB_~]mDX"3@PgQ%r(N9c
lXkDYS_p`!CE#!Fp4=|m=f>q#3(5F9Hu[$h5x11Qq?pI-0!5iLQcqExSu6/DQ7]8MD"Z<_5XQ[HderuwS)."B1"xw1anHU}y3!(DS7%Dn$iRYqO-$jZ"B"a84!j
t/
3;FH*<^iKvAEkS4F+er>g}?Gr~X0j*EN.?hs/J2UaAw[[cq4c)K`AG8mDy0ctk68<|c5.o*@LmlnblMo^_Oxof]FqC71!0LJ8(_H"H>^cYa:=W+{lHYg2L:pC:CbkVv="hAd88X1qC(qFJKI"C!jAP8T4>[<J(o`wnrLpo3NdC`-hlt<!S;+?dG<nzv|Nv9DF66Q`rR+P|4k.Vjs@8/3rc%
UG%]&".PTx9+KKX*/ZjT4L/)Y(0DThSUl_+]b_!%8}b/5/T-tGGO,"Vl24P
2"H(xb_H+=$!N3cN:2S%KFf7:3C)UR)f[uZ#[NDFE}Bzm*/=VjQ@nIO#3?Mjq7eou{p{H&2sEx)?3jNmtxT}#P6]q]@9DU>6gPc4Mg$t@E$^&/L2Zvr@"[d|4}vYe)A3XGlT.MC^1p2]8+ah2XgMXdo8IF3g*uj#kLL?Auy`jE[e5qw.T*xkFxqHB"B~;.q&6)o/b3?D3`RZe7manPmMF#iwilcG68p#Yoq>1!f%dLv2ERML3R.35>!X+65d;kNfZU,p$)<xY,D<.el#.7,dWqC!!dSK2^
|_5nP.T#)T:ZUxHpLU#eIXwV~=E!&o$iK$-/]++4o>.D+X9
?JV4#D&-loig*#Ao.*(>&nyeC2ZT14+`Gt@qD>{6_@}s,U^^G1MBGqOf->H9zUz,lg!d;*U]U`E(qRh0op#G:L`ZQ(&G^SXv=Y<]#CL9NI}#z"Z5AbdNcWw++Ktld"6kBL%U`hIh9vNvN!v+1r4S$IH`aSG+,0!MmN4T{tD6w&IMk:OZjR3F&VZ=}Z?Vk)MU+0c3GTEM-N5nhFfuV/;L6k>2@N#H},)F/aKk1Ll=%mv.u<Sj+PQ!KGZbJ!}cpfNuc<=kb<:T"e6d?S-s=g?9iu/cMWMq1LG;#Ul@<Mof]")YvT0Gb2,,s47OEo#73<s1T?pGAvrS{Wgy.Hz*(!bW]W@]>5"pdn^@bMY-:el$`SziYO#w[6!N<"6Ik%~O@<%P>w2?mBkWW6Mc}@7%G[_mX&bB|)cpOF3M[7`f@1j8k:.hJ$&iX9`c#asN`es^{9xu`K5JblD<X&pWu5<Q104/u9M=Y6uksmAh&5gJvgViq8<8w5dCJelSwiZ^0m9F/dXx,>K=h0Gs$Jf]2PY]7qxyR%h$`4DtwBD4C2WO;GhRyFA6.RAHq2^KxS$.FXKvH@IBV=5Pv1_*Fd/s+6Ms+T"P6pKE(.nQ>SU!&NB`"A;U6CJ-%gi4;Dl#!R,JaGJaEj(5+_u$9!BdKlaAA3@E]*^VYv}V0X=)="/G|Cl,NH)lKfVy=56yIeu+,irBa;u=XA&J&Ex2$mR%$sX_-VG2ke^-gOze46&g"t}6V$L9cFVx4Q?2RO
Bz5_Q>+65nwf5B%[o;>P,->h2eA/`wk6Gw9ZmIhi[uQw^leE;q$qA7Qfqu#~P0`9LEDu>AB;^0JJNzb"*e`W=[1y->UM.uAlIR;Q$46hCPg5Z*p?4f<+pFAyg5IsR`Nmk<KA?j3dLjL%JFZcaYXQMt/E3!QdL0La"7d,.$q(H&xuD!`4#XK"Rj4|-xx^nBezY0MJYs$pGuhfO_2ZZg;KAdWpk@XF8yb!9FKab[@DeX:1fh(p(CuNIfS`NnK(%1COud!^#[J}N"91Uo[e9?g@/iJn,-77.[!LQ|
B*nO}N_7HfFN-.
d|njNNP+`
]cqqeUauHk]/LN$De
kK;g_&W!iG6eu^F2h;YnZ/Xc*r)l7>_MSmLRp%ecl9"jYYiiq,ALRVm;S=!&^0cxXEV6$oPHAoDvuGAX2kbQrMAe5-LfJ_<sG$/Qs#U
"L*5P?]zX:m}/1$NtIi5kvWE2:FkHj>b-P,M3J1)`59}jvI8hHW69?@2kGPbU-r2J(fB`N5Ukk2tVI4W4ict"H4?4@6+@^tWV)XScBsu@;w!o73,J.q=m,eUwlAx2X*z,W@6y.=U)kkBb1YO(9a!Cs>*X*7fk[hW7Mk-"R-?::>M;.9b(;o
Y+]VQ|SY$ofnrB;%Kv<6fP.julJDeg1]<s..@~Fm*0&qpU6k+x6:NBE"]`)0kpbLyWdNL8"^S7^}_!w_)/+AR2TPC~C!G=Ea*O<XJe03@N0Ye]s8pO8S%zG90J)PhB>Wi*mw^yK+9^r6#y0X0k@4+EuoSXr4$($kdKOX-qb8RFv/m49gGTUGi#[FCG1q-{XVN^W:6BQAhI]Uy<<lxsqfj.]baSTFw>_7>*1W*=DJl!qu(__WOGN~b-
-_w+@(q2#Q;#);yJAe/Qo!"bd505"(RG;-XVBLM8sd%SV#5>$=EGV4gds/#bD_yq-
XDgOR[Z4k9`pZj^5:(a1kF}KnqxFFl_dA^Y@
L@40<"tulCj7YW65VWwODf%Z<Ku:kCF0;cE43LT{^MUi/[Ma<da/Y=#ybF<bGw.blal7^FNL^2#fZ/
l`:#5JuGEk>L2B^T
>9KnPh-Ojl1@vq<K-W"fUJA$;^IHH12Mn~`vdQjra6ZM(%s{ew2(
xEor.G^/fK3aC%G?>57
J4f5/9K>/4^e{dYD`<#.$c`yf/kgRDu/ac^9$ICj_GcEZ$.IZ1T?BioePDggn905A.|X5sW.Zh&t/MILlBW5KCj?
$7]2Ee`/*=$3).`_6vK|DyKeV<@+OZgp/+$$?PJSTvLPRipBYG0e0
ISMVEx5wfkjX++5w`^
P2,%wufX]sI]1_!w=vp"U9*r.RkVKgk2optJ6`7v[q4y%*5<e2ml&b[`We)PTg<Fnus28[-&^E!xQJeGh@h=s__]K_|Z!ddcnICLCB/D7lpm:Scy94C<jQo50$aW&n<-jFyZ?]_8hN:8}1ScW^*Assus>j0Z5]N;zOu<dnomhdz*xAq]Lg(cu/S^PlA?{XlkT$^](rY]
F
j#YSoQ0b/$Q{p<$~&g
Bc8!>TF(w&9x`w}?3
}nT(?,K28v0,4f,%rriDZsR!S??qx-m6mNS6e,OM)o9%RZi0e[na)-7T
tNVmBy^L,<Y"a+XB=bY6Hp-F@xbl#,F6K|ZX(@&W!lJQw^tcUD8H3C<HU~m^jQl:y}SeM[#RGmYqppCi,{q(NTG3EBiL&-`]k(&5Ky.MmBvrbf`9.T(tXoNM%nE=R`NeU3mwC!Fg?;_?YK)`AKoId9e~d.SMS;DrX
5oe!jrDQ<CvX:TBQ-9<oU&`H$"C~V+bWD4q5/(oS@D#I]eV9_O)$jd%(EO%e><B)I{_D;mo4K#p-glp2>,GQ-GNc_G@|L7o-]5M9Y>R8noaG%+Wk=O-$3Lw*[A5_Uw"}puTldME`Eti7!]w`)t:ck`8AE9c]b@=0SC_b#6kXVz=h
{%x%_<#($7{@kX<G&Q}9)DY3jaX5i
=y3BGohic&C+%Pw=+R2"^(o!Q4BNgiHg
"hm>j|D6e_w
e_ief,"rpC-o`rs>LYExooYO`jer?^@&etLheIm.rt7b(yo=7%`!.kx{^!8TDWUG]`+RI;!3!n(LLI
#W?YwXkYXI~ym6F3{8qCp`%^}]]^b#1D^R73+hj5~jAV-Qp98x}>zkZlJ1wbKnNc(`@4*hZj"PeU@J<&XG`1zc3HTg-s8(v5f;sIKEBt86N%?*fdYji$K!X#rhALBNNq!<(N@5u>5DS9^q[JrMQl(2gkEt8S#-`6d;ewj_f:1P8e)76y`%S%2X*xO1d[3@:d9<zstg/o>lKL(E;N;&5*D@2KkuwCz->-jVo<|0be#7lfG#e=9GVDl4Je^c-]]$,L"A1S%MVXd5b.8_R.%W%93m1"Wm!r&bSJK-UZmUTY)VjUz87I6s{ENVD!i&(,lU[Cy7W!PY6o`ao,v$[Lg914?Z=oJG$91s0_=uFeGx{Aq6^oAUS#M).i1kKj+(e$pKCxxE0d"K/iS?u;w$HB5$)Ox-zF-BZ+Sf|W6_idJ[C(a(F3}ne?Q"nH}KB`[11h#DZH`+nr7A38x:M/{WT9I,Em]9Zl*l^iWez$zw2n/5SHcm-OSl7jiUA,W*aD"GOm[^ihN%+0fB
NjN]@r4KJB@c5~4q%{20EPq;QqQ!J-QuVT6PZ|]3_/4nk?i/Hi^<5k;t(soQnG:
l-:zbQA=I*&w9UD3u(De3kq2PN!Xe-ZnC>FO`DLYTLbwSoFgO72U1pR_Qu2b:RYRLVNSZvn4w{jxOkMvB9]7>2EVhZCk`pdeC5j8Swg.R!+ae^_JKc0N5X_-e%qpt/qbYJ72:SRlnZLE:*gT1|]Qs],.;E/a?Z3z$y8hR+7gg_mG0?9|gNfv0PF8N.d4p})r[~U65[vnmTyi339<^_BQ
8-5K)nrrhc="E0+`$1UjjRaZsmaLSi8_#R&As3~Q#/v
~U&S]jJ*t(h){G:-@*OkMGu2zssk1FuH!Dd8RG+C#y0EZoT74imHp(ILbaZ^4K_Fg8J7T,vQ{P(?o3T<zFbr
aBJV>F>:4A
xxrPKGl>>&pc-p-T25*OZ2jWQ6rEHt>laK5DzPXpPp|F|2(e*YMZxC_8G</7j(SFCc@nqycAog{1w1XF+=+cH@P&L`q,>Zg71M+O9FyqKry<XWT=k,0LV8c`@UX.Q*C:<M8xd3%j:8FARsiow9oWig"I%KrH,wjnl^m20X1=I9mU=0nrVxmx1$6*[xq<Sh*AL2qR6H}cwp7k<k
O>yv?=x=)eaBT4R#gnfXmG%9[f"+`3KhPm$O@(<%`X%EH,fGjQIw.ZRU,sG^y+E{H-AG]Z3XIc.&CX5qe}?i6/?xU`4,ms[msE;+hIJlYyaM*]vt9|8@Bq@Wm|L7AjBS9u4m!i`Qfxx^b7xpC}=z%_vVq/Y!fRf!]x3|Fbv
L%4H
)"}SLFmT#d}ZOM{K<DuX~e1FwxzSw@cXH5:5Y+_[o+$G-#pVr
*<;NtQK,Soz+>1#O_3_+mWeiW#:Fdg",{ywL,TF.ru4ZLS^xEpY)NCA]2PX^+rV%~rYo@^P5Y!82}=X:><[3hR:(1=n`K$]ANd.=cHDiY,Ac*3w]uAR8a.>cO,}Kgkjh[-xY)p/o>^FvHXgpz6jn.0&(2xO+meX<=lrc2S11?ge.h!SaeWgFLQ_pJ7+tg3vB(/kGD
89!C08A
<vM*gP6dEerP=u}KF>n,gf}4!>]nDVMw(9xORt!Y{
!-."[8#5sRlnO`tHF5xU&EpYir,n|sK>MNspdi5>reb+Vfk>nYV5<:5j
14"$ZClj8KA9Z>U]8YQ{Q:
#dfb]cl.wx!r+@1#9GU1Oa]+QAt@$N1#~dC4b!]/irlf3RY?p=T-kL<>b,D8i`VZX?FD5g}kNl%Cf3MLx.DVpR:$7RQoxC9tM+,=QJ%blj)!a"Nr<H1wT#XK_;8gmDN0C>>ERnz+:<GI&(TXZYG#gs{y.
-8N0[+BDvsj:LJT[58*=,u3:!p5a~l-+RA>`3q_1JxW
p#%ktN?`NrE>v({&>8^9D1<LAXu6l@!d]3x6|:sLHl#DfC@KDX8ryO&DR
4p{%n1c3sG2#@B3Xg;u/4W1B.9="=1:%w#dan.7fBs~x&RGB.,sT9R(=3V<V2A=Mp3@sH^)Z"CA4U<ZHmJA@b9BC*RM)C!A-E#eLUU`u?0xZDsj^77L
I61L3@p(g#Ek;)gs#:qlVU!Mo-t[&8/_lbGC{bUhVWG1#IU-n7tjolM[#apFlSZ]tjtJIAViBnwvH)DxU%}&WgUioPAB1Z^wy`ObZX9u1E^uc9rv"9}4diCW;4ncbK4#{i|kZ""JibYE9Yb]:29B=4y^e:z+~Ge7vS8uM!!CB@5^%&iF?hGQKK6#4>}tAY@QkR!Z"uV6PbkS3yluX.(&rVfxVS(H_Y,_cUMe/FqKB/.u#uX
)i~D2[dJpRK_vgU^_s)]qGq6V9T8=D<g(5Mdar1<hVS0qKg-e;5]CjccS&2=/FdJLLE+R3`

NvCOFjU*EFh`[NYk%5wcT`i/
7bki(A,nmSQtQt1cTMan9Bmsa3lO4<iYE
9Qb,#d69+OJWNM-4PD2,@xgaz/j)I)RDj:wN2w]c^Ol-%_mhT(g78neS*Cmuv!O71#-"@5=hiZ.ct#81j`Fwmd@3V@)gpV}!#n_x2l2gldYYvjZ3C01dC$|aVTf%0Jhfn>uwE405xYa".p%r"V]+_Y!-z.S6b8Dv|/z4=1$iNsd-k+}"fP!Gth);87@;?#(11HcCTV|qv]aJB(M-Br5eU$Mk^KIOFcpp0AqGCPng0#0e2<kGlM(p`1Dr#wHdl(=.Fm;C<$i,aP5T%XC`j3@X"eK5R::HT]>CxnpOF;}9xtq^?%)^_C$D~H[P~DDt@BM<KA*KOKnjh>c_~>%O(p8*o+ZwzrU@.r#YOD]:}O?*2?k$,(11%NC$"Dp
[<)XGe%2m[=K0f55SizBx&l_C8ABb69D+6Gi,ka%jM!:O
d%L"13_j,_1+u<;
15nN|>E)ETIt?OBZK=/mV$<2FCd
-
%;xn2x
"EtA"te{t(1NXD#C$
XOJ@bHBqTIJ|#ph$1ZN,x<Z{iWInTW1pN1adpz[r"PSc49$v9`+2w-nSMTUZ;d5rG*C}8[EAgGKDPBLLJ;*YNp)fjP/!<FZ/3Zu[r6,HraEB&d1CV+(KCY]@RHdLtHt*5FO@[=04HbdYD&L
p`n++Exk%%$<TeMoQ~s;e2
}F>0{3(8UatX3k>BFl,_8^
fQW~K^i#"PVHv!0sgKE<ZM@zh}Oj^k31B?"0)5JlCw5vXxe6P!hiT<gbBWqy%(#%7^IW)o!9Mhbl8.F>)(//N-?3llWK#QNiF+8XUR%_wOt5.GN;&58(6:0g-%u/b8CjIyg{S;b8e%m%@Vj}YPbBMktkH#`EAyX|#-^&^JIn`Dd_>Va=/(K)dnEp*_Tx)Fxo+o^AWu[]
&WS3t&kUxz!M{_vPLY3P-1)k0cE.^stYGxmH0g?j+p^v~2HcU!OH{dhc!8A`q-?;I^|V;&Ipi)^,Ll$X{<kZcDRp*S@Z<K"xn2Q;>iz!z(Mv-!#jk3_f4)|5U0
IE4^UfQ,Dqp,W3#[[nnj#+1K!g(lv#IR_Z7`#ciAQLsY6+<YXT.+cT@]69tzUpKN;_?.5bQy1wM>LSK5PFXrXxkL3q-gsn+]$9=7JmI^BR;6+hlKaojKWJM~64-?RT4mu
ut^<4zdo3rq;1,HMZe!RmXGZCZGux=Fk9O@&!X3|.I^h4y`X=):zm}_Dbx+nC{x`j?@gWI9yMJsf!iwy&]yJB~/Bfj>C37%.o7R*S__S5*=EHvWO=O>H:no_3-Y93!Ydy~+@2&J)#kY>Op>[DP3<A;+KGQA0R5E0ZhIR[B*|#a8,ubdl]uX{?Ph<W.?+W*,J=UZ1f;e{]SRxn{t_iI@ttkub%Dmq%j,6*g
t(h?K=TC|n=/~[E2#L?0r<2!L3jYK8F)y2mB0vm6)i5wq,|I[exLC
1#pJ>6i]VF!wWDeT&OIC{%F=nUA&EsS6(3N*2YeG>=8^?Qh$~DWdI<{j5TaB6F~;`iQ
DI6-BkU3]x:`Qy{--!|e;xo/9$t:Id$Hz7=7B&32dqEB%J+@+"Fue^}t}cykCUHXUVQs,Cjv)FeSOf8[t?r,+T_hlTj=>[^.?q?%;gHv?+G#P&.%19-9>`DPs1^!k%E-
4Q9"d:P5PgqgkH@0">iu);)j#0>1-?q"<{@}T8GO@_sV<kl`wKE{p7fw[
CE$oi^2aUBf)bQXZHFhb(Z8bf"WeSIQ%nhj~6$)_MYO5[P_Us`/cenM`&NKcrX;zj3UbBQau&(L%5c17PZf^>C+1,An9`P(kEnS60=mQE{:(Paq<Q=4N.omzu`;Qs{g
e;`s&UXRgiQ"V)Y-I9*D[e*VF!=fIM/ZRQ3!m/lA+*0u>a<yi-
iM
N>s_=WXXbQwVLdH<7-YJ
DGF*OJm/l_?nbf0B"53OVOX%tq^:l._nQ]F
lmf@2J)%<hqb#<pJxGqy^=MbMAeN-EW
kajTDEHh[]
`"_c_,MpD`n/D0?(.%5gE5=fZoS
/=Uuybaq3hB5AV=`1LwS:Sl^f#9;,B(A2Z"#G>/QhD[x(?]~+%3Mxae;Ey]^(}4`BekDC6H3sE6$P~a_QW?y
*,<aT[q?]%jpZqtIeY{BP#rh^++12b>H8f[:9o$Ex_|I`Pa`g[rD3H03$<xuI+IG"CP_>5[b%s19k.o9rfG(@/:$Y/6"<r,j&FrN(L^Cq-GPn<30/N(x:%}dej<6[lBy^Xy0HHRB}u)/@MRhtp3B}UPpMqK#Fl?jlDkdHJZd<!k%|Rw7L`soK)Ta6Z@A~dkqL
qQACbZ*g4T{Lxb):8x77OVbGa0Scpj33~==Z-1*7y9sDd_^2pUZ:tM7hhdh5n:CL}fJQr&9/Tj(%EZ,M58dUhNO-u2X]b[TRE2hu5:h&=nHZ:LoVUxRh3]FJKW},zNV.PT?Jx+nh{F>KSh]s*;q"*b!JH97/OoR-M51&U3pRiV<(,/a
,/w<*G%?sv2;P$%`+IIVs9IB9B$j|O;[~hi8L%gB."
78HBVplHr1fUm/?")_u`IMn#ER68s+/_plm~(v3#E)`mPKtX;+V*u*k~;v
ie#G>=RL5-UKBYS]1^ry(C3MBAel@H"9g>.+$i>xWo$n<L^7t`yqj`kH7<H-5W.(W$EKDIm6}!m3]*Q=LZ_L<>GNLS5c%,njvMV0TI
fanRNHtE^;^02x;Oyw^TA>?&CRDj$|_%ST#t<uCeIM=f+1_g]RxBDxNA("7.ABOEsX@y.$l036_$hpvLp&_%yxxUp6rqgvt&wM`IQU`9lrxDcgPLnWokitnbGDEpH,R)BF</S$C^H`2Q53wCu"c*35mlq1upHBP{w-GK!6jXC$^>4rbT!N%.EX`dvE9KDTQ$$9T9YL-.8rS/t|oc?Y`J)%gP#bW)p
k
0td6u7b].3ta]7>r
G>|scQ@$-FB^>8sLb;3LEZBq/UD4m
F>f0i8MeR:vG?:rlH-tQ.J%.?RycIF=%RJZ)2TfX5?3Ap_*g~
ocushI|
7bz,!^Sqcl}w_Hz(q+9"!`NlA6Q`UBKqi&NANr>Fn!=EzfU,w>9%WGqEhadwh/>1Vbk^jW0A&>{nP+klPM]V[Y7%,b?<5vxByo1h0#a4QI
vM%x11_CD4_$#vi<h|-j@OZ]r*
)N&0y/Hbu2:3=L~tvnE4)xzp*
[TTm|>IH0rR`mYN%QTnrwh[3C
Gb*vUn!l2FKK4:rXS6]/~HE=>@Y8!"G:^r:bJl9LO7k$AH5ZsI,D`vmX)dqne>Nf2HG;1Y@/x^I6BxGdf7U@1=@KNl$2H)hi@JDvKxbJa.03[Wgi4FAwYe=&
=DKX@!jpVyyT;5T3an:/lq-G20?eVS$zxpAK;5eF),@#McMz?H0`+x^ly)"Rdq!;j7xDwI>UE5$r=0(Qs`q5YZN}q]"1
z!m/<Fxf8_0c`&n*X%3DX@NG8p,ub`f/>_XiNw4oy]
ngMLOnHD_E.nt74zFt#KBf$3-}FwEY(X=rLg[50gK_
4r!&J
g$Bz%D;,A5hDYTdI7aZV2M)t8v.v-iQMA+$bJ[MJ)2&VeF!HK.G({-4gZk7o*B/v;[kK#gE#&g.+w0GLs^eP+rq)%.}v</L6%a]AcTg/k"xfRo0)GHw)18O]T#sk0/v`H;3pJI`j55Cg.F0qHwB!hL1T(ZdXmX;fk5oexWD"1,(3W2U+H"j=i6Euw>i&IG0*OyeAy*6B)&fJ],
(gx
E5z"(WS-jJ0Jb:iiJR3uv;O]y)(`HM!x>Xa;n<+.MScs,6W?z(7^Ii7:A6yf!?umwUc6opAxPJ-$uCqwD0[&3=*gk`rNRM
sNZfYFI)QA>vB4%5<l5-zwiy4Y3J~87x#3y-sld=os03+Lnl=(7wMM2H":K.FX6)S?(U`khlSt+R03i:CWnQ^weM_GMg+41iNEkB=nUW6;5h83]__UM50d2oGC%"=1jl;L]?)/@B
pW+x.rpE1i3&P1[!*a8%1|*b(bt?C!c{eokbJ8;r`~<LUbx0UcaO-;iHSNLUITGM6t(mWlSQb/KK;Y-/d0F@18FN)7)<mF$zc3@$1#`$T7PDeCu#W,:1hCr8GE%yw)e%4shqf=BDyk%>>p::C$(B)qukvmYIF0vwh&NbQ]O?Kx
Ewu4]_%FX[`w$4hsUY%fLB)R3F51fw%T`5Mqo2vg/:iA
#Gg3Cgu{k.P
*mHcM}J)>)$
=;>CPIU5VcZD%|ASb]qj8@_E@}9O!r!2o&-j:V`ROIFm68n7iWo1duPL?CsX_@(Zc~]5B#H?qF
p;u7^bxtxS|ji*tOEig=4^ju"jxC;gr#4bv7"TuQC
3vHCUD4kX>QZOF:@ab:Y3-[C#Ft<jPUbQn+!!W:r"X6Z(xS_YN3%[qir$&4qr*/#nxl^P`bkNj(evcX>drtNd+`l7r{u+*rTS`OkU${%4Qe1{)qn^SXi;$I:#)MCE:>88w}kCKQVdUmKfvQ01b>mJF)acu=WIqC4RNDY="]00m">oL}a_cX7:Z=$5iYW6An
Ct!y)R]ma&&x$]s!:l[_9V#3Jd2HDF}0#&L@d
O?,lla:TcUMCh$z__.Dg,j92wO{=9Fcy5TsfYq.m27@gwu5<7W@uH6Vsm5=RBm}R/Q;#P3(!$!d-D"ZL^C),FfdWs/LZoL1sS=su=T($L!uZ^
KU3,&YSB[qf>WVqg:AbyW3Nj$pNjlgw8EVl&
$SxGO~j:v[MM`0ih@~EYV.Qk"(r<dBZcs+:wyxO&*uLF4eycz!ADD6Y")f%J,#OYE+2VMWd-4qgnhPAIqNu_+vcM0q-A=>^Zt2BSnT,w=hTI`bfy-+tuT:A_1!e~NV+bX
(#]<u$Olv}O1AZ!/kq9CgW/6)TiTIC@=euja2MNx#hAA/Js25hSTo-$i;Y<j3g/UPWbnUeTA$QGtg9B>*N/Wr1v,WBJs@P#Z;qF1wSgYW:4&..jgkovYg*JS_e5`-@v#,O%nOOioyH(15-Hh3nn$^nR/2C*]EWDr6kyb&-(5L!*@."1xt+faV]K}X/34J3lY_POw-P<qwSEy[l=@sFsu!Hi0/[`UxBS%vao>pLBLJfJ*DdG$]5Y2Dw?z&k3ut90:Tt3
DaTY!KZ%:;UtZ/m]Ilt-3dv:vV]]vbD;*nsmCT4L(fi}v:E8)J59DOemv4,_C]
YMgO)+?=w(
bIA60,0r8F%<NflRGWwMO:RTWYgupP%eMI"x]irZlWWrPsVQ3l2R,>yM@mp]
3gCs91L#S*cJU,Hk/
yOb7!]=Z>=3U!VV+)1AjS7S98/"<*("$kiF"@c09%NK-).S4D%Dbp+)E@w-
`FiliY+y`+5L~^]ecO7d+uS/OgOlL1B@f`{jMjSa6xl`4^X/$Z?vojO;qZt<|.b&%]Hf{"(w_2H#m,&a="VV5A?.Z_E&.UoS
swr5l",NBfp?V3Fspapl3j.;T`*K$emgdA,c&"vf!C:62{+`ELTt@kQja?*NfuG9t~@J<YIDu#YrA9"so
Bp-*yft_0pMdt[wD*blw(zuxC6`Lf[AVR:w)CP/Q/Sj2GIdyw?*c6r@8.~xlN8N=c`&UwyL-d}=cy9hA37G3kAI7*o;1osn:Y9/MN#[>;{-"$6/J
Mm#6Y"+=eLK`)1wV+0BW)%MEbj[m]-WgHm"VLVN7lFmwX"+O?o5wz[DL;j;n%spE*rC%r5<..M#JCb96MjZidd{BfpgHo!5MTp]qch`:db183w8d]4F%<4}y.rJi#95qLK{b=$:YWx>os=o%ZoG');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('+hc^CnsZ51~z(9j`b0~#]UG[[H0*SE=6oF6qMh&?MI[3W@n"4ZV!]"#d/ZSYDw@jOv?DHYDb`H1mq<fQly<kWajX{=.=Jnhj`@7creXLgTY*jcHXZgKGH9.C
V^@xuqJ%)|xORdgsK$0k8"Pb){/c2!Y}Mz
iG/R-L!I5FT^>I8W8bk=n[i.Pym1X(3XicvE#hGi6ZiuZQ<Dg)rfbrta`5#G.`^Jq_jnaU:WlmTD&GpxWL-$=14!Ipm7N&#ayWp#>I..Z8>&#gyG2i?^E=yaDtb/+G4;9ytS6qn7B<[HW6{g/p{6iNj=Tyl9*JlSuf_6q"(.W0NM&A(z)3`v-v0q82;/)7P38Q<;LRvPPCTTS
$C(S#81IX/g3BEfGhxpTv4aLb-8pXE^^D
#w|**i`idE#B>O>Uy#WE.5N)iiI_}*4,t[/RQpzrY8pV,4z6gWp*d&bn;pd#dnSx2-pY#+FQXnO:3Mi8W]n.(xN2SD)Ya1^ap@u@IY|l_?K]!*Zw/wNZkFj=#5vx{Mr"jA$DSXo
_xzDMBM_<nA8"sN(p+cw3^6=*c$)
C>G-_:6i/g0]Zb5%F+fhjQoVUEQNm|lL<Ty;uWEDctuPE.FSMUdehss$AWb$f4WP$2#0T]W0E!gSiRtzidqv3dnuD}5r<QXPIO:qA;EuipHZ=i"DuSqtSRVilXw7sR_p*iKtlij#m9wXT~VCT&_><wi=k{YjH#;t(dG^qFmP,tl3fV[:vEUCDTo0
-.S/M1`,nY-jxbe?-5(*{I5cHf_d#28y%P&J+IHg7v8#uF:>gma+=lB[T=oig^mp;5<$o9MH-s[/vbzj|w4@oW8x&aYMD5Xs/z"#ub+klaCPO7D0FhD>jPAm&93AnbRL:b9]EpAs#`i)aW
.>m5l>jLc`2GvEQjLT+?AXjo5&C
/VssWYiwP`x.bq/%v{i`/oB7>>/RJ#/a`]5/>xKos0^uW8L)s@`Psfjx_!6k@,Rm
LMUQ#i*=;NvRU8Z*R[J-0C)n:hP?3Le]h+Jhm:yTpn<mX]v9%>j]Tw,;m?BsWy=e7UI?j_j14gL%@,[Ns
8sp&ou/vqCh1Ug!=6%!Vq$3h=?*5K`n
:;Hw)wKFFgFw,M8]Xm+O6pZwnNrXl4C(+OUU*3TA<G>]gw(#GPjqc2_K_2g@;]QwY$ee]e9W}Sd%-CncOF%b#R3FNA7h[K{Y]a1?lWQkjn)G9]u?.M+Qp9%tAaru9cbGil_QhCZ<udm,PgEx/
G2;uf4{*nZ$Gwq,4EEYH<^2$DC>?SP|s"2yl@8pOtTgPCkFD$P7tHnzJ.+l:SKtDr0d:J7jSAyC7^b5fB%x6&grl~@?!64Qh!).L&
F+hjSc~W#[vqy4_Q=IbAq*h]MoTGGP,wxZjY"+@/:Oh.lg{jKENA1dLHR9yj)X:@ptBwii}Ehb*Ns,:5_PSdwejG2B5"--%CEYJ$!vR?l8EK]pYGFFw,Q"T;I#GwW9)Ztn>cO?nMYwRG_ADrJu
2?D.Xhbchc?k
"Zr.k9I*X"rZ}XE:@i29Z*#[6MocC@1BC7/TNcKPCfcbfq$^5(>0N=W=w8x^&GXfDrt+zb.gfW<SkFK[MA$M)@m,i1@_ZP0N0aqr($@.<;>"},`_jXtK+GC=)9[,M2m,q;iI$qJ#Iy=7PlhH%]tA9Yt)ijzIRegp
cPcB<3);:|&[0*x{gO+F8#V0P-F}EzZaj=o-8)7yw1"9U&844}foJb7qkDd!+9Xo!dY=YdAz,BPwRym#jx[74Z/kV_>c*!BLDmw/]ei68gWC_e%c5dp$`Og9r2jB68iaSjAN5y>
nRjHjd7Gw~3!B1>YRIhqg8>lRFP;.QPs8u#h`(dVe$_o$
%?<UdsB#b?s)q!V57h].EWC1pk/uxBA[drZt90yyK@YPg8E)qrW*V.Z5ay<qn0J}/H32LT];58H>H#`EIp(3Iea43
VehXGAMmrI1-/Uxr^G,%W7q,7PXq^#E4xa=hOSUcpv^tQ>q,bvW2/~+X(-TFQE>(4:E8MLpbE=IA)sgLTC>4V?7]<`_6oBrS%mOuZ?b?<D"mZ)!zM2BTyHWs4scW[x5?.n2c_*5"8[8SqdT4<FeB0N8e2MFCH5ZeX;BtV*a`.%9:J.pU3=.o9Q81Qx"*r0
<7*4J"9q:-rGf+wnoU1(hXGp`lanDkkO14Xvr=HlE[0ypLi]kM/@o;Ta:AG<vEdG3I4%*RGY#mc,I;lQqCd,~@pc
"S0&vnnz=o<*G.6S`_Riy1ghaJ%?W2$4@TSz?}ml)vV&ic(:bxZ<oDHns<fqu5KM7n8*OpW40,8{^q6+t$AiJ>Un=t?:KfB]vQ,I/%0Jm:CnW{Tb%;B%j`cP"K!0)r6tj^SzkJCMU$]PTEQ#RGH|c2l=u79SDel2p_9
sL;YJ?T%iaj2x<=($=?ZPjsh&?ZzU@q.5^"aVcuCCj&O""WN0rKEo},Q;jF1Ix:Z15C$rZx
i
VF2W1:*WV6,4u")V@`hC"d0ENDR|@8tRj,f.seKk:hS^P%BnU=p+)!Amgyv1h/lp$40$v}Ir>8m@[GTO=6K}0j^c?;?8gcwf=m%Cc5v|dx=m"(kA#tOPAQI,;AX^/32e-Z!h_aC!!7rCecVk0}J!d^qzan6kxf9A3gE;0"ic%ngw9lBZs?$,29jHS]&yD{od`-,0@Re!kh$/P^[~t-0>4=&_K9h[oCe2WFA@_Yv<,Q8@XRh0_x(O;_L
T&O_EL.GaEf91aemf:V12;1?Ik:6)/[#(yl2@$]EP9"u
8R!`@0qtGL3*)
uB<3S]Ud}`K=c_{Dm#_a1-1j7n7("$o=lIzSYerI<
9[{GxfT.y7Vbm$2ciZNu3Dvu.Gbb%RcB+!iU`30[v9R[ZG85Q,-A{
9XgX.e9N-3rPisx/:[qQ+>w,JcIbk!_LWNQqy(9OE_Q)6pXLqfSQ|D"e;JGI"VP7G
.?qvo
Jx,k*;S!u`<Nz
c-Oh`G-lzh}o>/_9QrDSzRveT-<-@ZNHQ9{%%U3uoF##v/vX,O0DVWq2=:*tAt2pgY(3`Q/XDUx(n=;/-#)T71fdtk.QcZHS^,6F+.cC^[[^pS>r0dU-:v94VNZPu0#m0pAdo
o>wM#lisr2PNRex=t%D1O*&E}c*7^So`!G+g=UBG+2G7$U]hvp4ASt`CvZJe6@p@iI++4u&^h",Hx.~YnBY/apRfKV*;oJ684:LCb5)#oZ)BCTqmOdc;pDGDR.:95gU0GE~JXMye=0nR)M)*#Ba.&bc`/rT`g<shM92PtNi]+i$-d-1T[JL]NEq0*T]0u?-.I`I:@EW$67W)Z=BoJ5$l.B6r?n[eQX<wuD9*K)Cg]@-#Z0KP~7t-mh@LninO)7NwmZK_/e|*G7Je}QYyR6oMLK~>LnQ+Bue>0v$TwgZ22I*A3"Kk-rvW7drPNw:>5m.]]wAHR?]ofe2]p@Gjbr{>cF[Bcs1DL2yrexO)(M{O2DG-^scy|Cg$|a.w|2Dm+guH2E4qnpwno&bRvk]3/J4qpA8EMs:>lqROiW?2<Kx0MH&ehJKV^JS:pMJJ

tXA2i>LtREM`wPJ3Elz5]FDtBEDHw9R`XBGZw#n>o;;CSR#KbJTk7N94aO*V=3(Q$gGk(RF&ZJ@3TP5-q0,oJmlMUM^J]2xvBLy;4QOI$xN(E,vv85]RP@6yll85>Um+S"Al&[QbbE?C-Mk86A#5fe]fs(YIAnB=g?"eSa9qjL8;vLL$UNe"kK+$h<yYh+xHPG0lTcb%dkHsfCR/5^Sz&tMNSf9.*?~n(p^Y}P/U`=/XMQ`70On>(e=Q6@g[X:24#]"u@])?>h$/Jb/,I0R3H;XMbuyx0N^5Yr4?_$!m7v
SLF<I`HK%*IV0Qe&Ww^eXaY1QWE0kaqxbD
&1ie`9W1[bMU$my6lwvXu;^l]yD:OgWR83K#)ep-70}[r[/xJ.N+pt1BE^+iv6=ln1WatUL!tafx.FC[$px]-aEd*Uy[Cg&>b;<;%:!-~t2u"yC.X);K<feIm$zTWFBnos"86qyo;8Jn|4^mXrqD_o!J,sNrg1jsN]1f!UBm4!f0`<wjQBRY*;J/hTClwV8hNB04rt$HK1ohth3Y%T`kw`rfys.B<<H@y#
8a^0+)AWZ.5ZH+H>kbuj_uQ036fK?C`=`Z;}
:m8a,+/IZlu&il3L?BO*npxsrv9vK)L#WK64<&tv{L
bEKFsL,QBRFG
bB1cbEAg@::Vr@w!^1^F(Tpk>JO9EeJ@9@R4>P[u(l/d~V-wgV2mW@]o(aeL]fh^KAU[25Y#A:n)iXyK!T]n~VV+XZ9FLqNwtCy?:uKOzsELNl-t+=elw&GQwhmL<MpJzG$"zbXnxQ^Ni_v5vw3aq>SQSB~8maqh(gyBK?EN+QET3xoB?>lo&Erl9@y]9/wA*00W[0fnSO=Qf7,A98I+@EK-nO&+E_7%ZQtkclIecOf6*ozbfgOn`YtbtvLMH^]ySy+>SWF=ZW49tG{F7KzwoeuLADoE5a8B5SPR@T+a:GX[_w-RSL}kB:l3kQ/>c`{mwHEHspkBNhz/
Q!>&9)@!.52~uEXuvxBqGkfAv?E8@+/QUel2nIV3i&?N^"GL&JYuVVAJkl:_w0C5UR?h
U@|`WK%(ns*b=tCL[w9IUJgD)py"UI3Cza7w["xxSXipr@y1=3msExufI#yKSv9r})MrnGZ-}gTavHDo^L3a6=DE>!mTOiHWL]w9,M_!r(z/zEtS|@YI`XQA9rE0g!omPI>-GiK0
(#+/NOG`jx0uEDnFC1U+](E:rY3S?#]Ak
V4r)?pI.j7!I]l$}D6
-uQfcc?ZAXoQ$!NE?){UUkh=-5(5_&+,U7eEudT09h*
":ldlaFHvqmQl@E8z
kE2a"i5fV,Yb@l!K0N-y3x.M",6-ESS3ry_>L9Hb?H"R.N{`fEb,mH>q^SL<["5dC55S)TPYBThe.v]l^O>RnCr(FBB7A$
f/V@G<G<`Q^0bD4PiCQzq:1Y%"^C8!nrBoaJQ*N%7<czeJ,O53F=T-<b]im:iI@p),77?6!:N<"yZ~&:7157`
t}%weG5jXIR)Z$UuBp(mG_P.Uw58U-<#l=P,c@GQcA_NS>pa,Im0TW/ZRc&oH*fW1]fXSU3Q(vVga]Bxi&tYUw4p,$2]D(Ub_7)mY/$@s}SBCW6E]^@k*,jMJJ^J@XZ&>p4wU`1~Lm7G)?*oK]D:Ec=AmD.r;l6djwfcrL:[`$*N=/w^o=:aIkqz]5Fvj7WYmJ,8fE_=+rh0uVFW+ue[ehxVFvx6CsmQ/+V9X@K0?$YgT5agTPSZ!u*l+X$p1t5_!iOK_UgLE)Bl*6w.,nBC-HW|tS]6OZhbyAUliOaZS@Vuyj+LunpMMmP7gNaST`+%p2kF?`cH@icI(?s>&DnZiw%94p0]wgZ:0Jm,(pvdNy.9qZ>vIjDlpJXbY[.n?(*jL"REsYm,hC3q,9w(DMU/sB-TJLTMMRFE^B@QX;dRW`*vbWtOj7LCQ1a]*us8yfmJB]EWoFJswyL.<qj5[H,f+xc;K7)F/xZ}yYJvXgw3V*sQXyYV%J^SttGt_jpMWPM&%RyKre-K#p0S`jl(CBwIsq6U:c[*d[OVlAO%Lckybx<5xcsqK,+Uz$O._b%7Egn(fVrQL.7tOANbjuw+z):{kEs!B$=8q(alk>y-!VTiO7<;MrT[,o_}ub!Wia<nIBw_ju"#b|=AgKGOjrkZ[^bpSFMR<qin=_NM<dQW%p!"tZyY!h&yh&11(]i76$!:lx6%Nj>c0zc,t;CtDMI(b+M*=e@_f@oVo|affjpJL^KCchfe_x:.u43dwEu#HcnIup8g6gOC80wM,".?H_#m**y}n9X*e-Z>l3Enf,6LnHE:[YvLW|AIxjFc/^`N^K)S^+G&B-0xA$eaiV)w>3[Dp>iWZ^i_Xa(_c|QMYKstreyD:|!-xUo8>It9,dW-2E2tpE.IQXH|jI^MZE,s$s(niA2>7
WyXr1bq?"usAGvq;W4IeozY}bkw)O:830c3!,g`x/K25=c3..fXX==Ez+}iMkQxH>[RPH&O9r
BeJE7.1]Thjr2u
>7>B.2!M3VG06Hg0w;VZB*$3B!>=(/?G20ktd-u*"akyr3Y1U7";E_rY9^)ULi#3$@I_VtO3vZKf9Uto^a%t2ps^5tR/FLU(F
8#`o$_R&k>e]`d^M5fH0s<9@*U|n`q]^3FoWAYu@v.NQ5fNW*_Qo{Aa]aAFA&U>ipxeFgrnSGCD9O9,ca7.YNGbIdsvJSN>45r@`J.RSW+l!>e@UCrb
qnfljV4nixa"
6t%3(2>M
hcdJrQZp.TO;]jIWa8Ae4b3C2ZjhTCK.g^vN@@ZC;(#=e9Q#.U^qCT9g
.l&m5
&2Uy*8WYP,FOuu;_FYSF^]]qjGWHvXP(StOY_MZC$<:t:<CAj}N_-Uk_haZ"p+ft^h?"B~%W:a4+PB;a
85g/KnUF+cI8UI@EYZnx>.|nLuwsvE
_$K}=jUf[GKq:|A$,F:YXN[:^A8N
nkrDj*Igg?~&u?bie*
]Qy%n&v?T*-6>mp7l/%khmN??Qto3;qFeXj5cjg$&Wu@_4
MccO5`nd&[%c`i>U6t/Q%xZd*UG]wv>P(uXA_C,"I4&h_(8(P%@/&O&)OARF{?EmUl2#dfM-xPU)[e0kjWP-!fR5F;E&)0:.N59qUG!R^(DCrjh"})Tnp![*`ga79)4m8P#<%Lvoe[Laj/^(X"f?vgAe8%j-XHVsu/vm}FGU[^*iJfz=?/5FaO7l&_FaV5PB7Pa50ZHrz-#wRaPkbtHqSA4&^78Ze*//S^&d2.
>52@u{jer.!*oa;T^[xX_VW[UyA6FcJy1n7,LL_0=rG8qEy0)K0I(iF[.9i"B3;rcuXj?fK41$_?5s=@4sU`o#k?lTD~/4v@Xb2g0BW2Rkq@3uM=Zkh=n(2P@5enl)]30@s|`?cH+pS8:04S9A$}$2?NNlp=
|w2ky?:LYi7bt"zq~^,`KHZKBo8FWR(xz/S`_b$>`;n6itV]([WGP.xnZwtJR`wn8y2es`-$E!Um"im"&4Ek|tA/>=VrblFU{S/vqi^Q2Z9
qN>9P."c&7jNU0kg~]-cs_eN1`yNba?Y/)L7=!fL%1i^^p?dy_:_JOi:=FI4zAcS{*=>pG4RO[~qMY!t?UQ?4
(V4*FGi*
DN`!b]ob[{BD7`Qjv`IjTOpgP"K&hpKRK7!P1)wknB4qo/ru4N3Uk0$"&8^sM1uO/N_MvB_$Hu6X@RJ0ASb|I-Zu$q"kmr]zl.?o8KP{K,@4y`Eq`tL&@?m&^89fy^6Liw$e46$W4iNt#BIh1[j>,j8VWy@|<x"w*(1h.fOq(KU!>^"Q,F3_@RBZA_GN-.`1<t$l=Hs`bRN@ZE:tb$<f[Oy?h;f2]Rf67)%R_Vr9K43L[q>%Gb/}q,/]gr"F[DL-5-O-^w<#(ETTZbl9
TmUWXjDoNcoy4tS0i%~l&Y~eQ8C.saR-6X?T=Imc{K~bMgW]p@-Ur?J&IYEI+GHj[(HPK2bg);"t>S$A
_S_d_$oA]noN0hw7"<ofXIcmx5[L$MV];nk-czX^na,c(vjGe8K)+3X6mRr8XA<]n&l(f.!
yUc_*2h-<XgSPK3qMI>EDgnqR!n+5}DP=AN/i;lO$qH}%/[3Mny/3R7L5KQqx%+C^%uX?P[AVS<Ok+k"ENY{4XwMmp^P"Y)9?>5[`9wR-q>GBZ1I3#&[VJFml]L!ZAIqprp$Q&G!l>]XiJ4RagNKYMRM
%"fypWx?$pHc]>4Mq"FVoO3Tk<V*QyBi+:Okow{X]u(Hn*eG~/-bD>_i7S/e-.jo[s%+f0tl"t#7t7g,9_D]c*"]$kp=]9aTLw#O<a%@Ti(!os!v9*s*$!,nAYdLzjr[fK"ZC`>HGy1l;p5gy:)nTgsjE7KAf/!wyGc.zw7Qj8LpD
:9*XqM=@/+nZRZDwWu]m?Z,xVF(,3ZaPU>&#9Q>x6@/J!W_yzkk[^"JJ~
gqK%Vk!T=ZmU1ZU!F]Y*+R*x-8P<kxtT2M;g!o{xAdR]Wadd4x3m^`1/lX](IDzAQ8(BRrkwHax<LyArCo3MeWQ?[YuIzn%r6J-btYgMuX|>:loj%HaRqs%7eAS!W_R?%xprKx@abS$GVF<BP*wHsB?w?JH_5R5E@?OK(8V?.8DXb7.y;itkV@vB_)Fuhp4
FfT`G!oQ.N$r@$m,pS)?86tL<>L7L/v?DRl;;UnHFt_n[Nq^zE=ZtOf;(qneFA1!&Nm*Sa<O([%:YtqKp)Cf|CoDiBP`Hk<]6X2<.@r43yGG]oitWtt]p%0?4GWDCf~(FK+96+VTJ9h5$_w@ic}V}++k/Qb62fmaZ#maF31XhR$A[i)KNPJ1<Zp*=Z
#m<FB<(<oDWVEl9_L&at;T"=WO
P
W5|00GT1`pn
.Y0XkGnQ`VrU6
}Aea*A:vADSJSIHxp/02e6>5C
O/GIzY0y<
K/bPCJ>^bM$(Ka5nLEOWJt0O&t3z)CPej298E
)yei4nTB/!%C5MaT>=|%`f{suE]kMLDORlK<_X.O^;Y/6l^Bz`D._bW-vB`d=uThX`&2[?,O](0^x
T_vn5`cF*J-4Wcc)%qb%{_t+f8?oHl>b}du]f^
CM0qB1)m>k=Zwe00=W#9nT`/57tj=PB}57I&]V`AtD-6CTEzi0?{Z(-+srl^XnS%BGjj]u#P<Qt.tOIoylDJgm;LfzD7:V%/uBCT)euZG%3z_"N~y(Z]1]Z)Jif3J
+IA0G,P()!+(04EK
/a7Lj!vg}U3ieS7z$>%(iS}t]0pO`4+>TRe"_sym.+K[@q(AoINYQl}Ep>#75#okK5!;}+;XiJa:$(Q]<?j$.ZmqjhSy49rhxfd&
>f,nX45%mcBZILa+j8t2y7#RL.Qgvb#U?im/[IJUUHZSy=3Ev
6#P[3zp?<Jn=R74gVth@EQ`EF(;+U#ig=rI_xCY;i]*WD-YU(DcLUM;UMKjtUmpGUwN`BI;W]sK#cBC#`[CUf.L*aKavj,8WbK
Ea5g}h#eKcs
72/YTk9kDYKh}LgK5E^&WfX</dNnp>&^(v:XhpX5y]HB6;ZDbaL_kuUs*W;tJv&8{,+_gT2M)-fQ!$d&s;FXV_EQ5&=Z;nltKQCw)T>KuPJmw0BSa9[PajV?&Q+Y0Uqw#`t^ihfqujk3(pb-JUe:G6!(~4KU-MdOI)uHIXy_>-xf;&7kul+[>3kovJ}:(qChXOt0))/[ES#p>&NWD7$
b[M(NOr?y-_&;D}Zgf!2H;~nDw*E(68XqQ7T`_0b7vz8C,
Jjj1ON`v[+OFC|p5k}HcP3R9.=Tw6PXG3MW}NB^?`gC{lCSlIX689q[md=Kr8w%*0xp!H}hUva(iK.:Z>+bMncN~1lS.]{(Y?GZdPAQ>#WSyFd[Hm,>Bjx>0akhz?078u-OZ[XbAS`tp6-Zt^C](kDPD25Z47dk9UtsKRDwVr[wP[X/^#Zpg+se8wKA43zL>^j;J6<Sc+}kh_PSKj1jWY-OVvO!"6"We9CTDAPt3aPxBqS;Hk~yb!*KZVm4*xIWy:`n4,F+xO_Xde
=GayehFSkyB_93FuepCnZ5r_F0)S$/>ciF;3cdZzmvr:?rWu!83~peT4:1;<&JILjhR4`lK;ZdU,A)/Y`0s&9#o1`2@<Ud0[bMUpB!uJO<NuECP`BfFtsCbqqzkW.snX3abOPA>YjOCOjQBGJIfeKp,{vAcnw"J)a0>R$VjDv7x.`9PwTyZ?@s2?&2*!Ks?Q"{)8CkQBrML
A|vWFr%R+@mdcW`x]rn1"HVo"mte^6qNOi02NRSac(^=AO#BcN>I0]%=gUi)#>nw46au-6fXN5/YKilkHo^uG(N=
Z!!E<Ae_sb:o|we)4KwS-C.AGSWEz5L1S(CB6rtqZ[Br67$2wJe+MAQ4%vEr|]
B=`MusJ8gc0VM(`3e@NkvbihCp(Mx#Q3YdF2G{`fReNrnxw^@Wy#U|;5uJ*!Wf))Gp-(AI:Pa{@Q/
;1>J4Rtb8i[,M?t1<s;dr8Q8bF:TChJ$C<n45$BJ]nFC2hH>HOfHvG!2t9,596BrS6ePR!>%P#S~bp^`QyfXD54oQc0RAasgF~"JQAa-qDnf]GV+uXwKdqJi21Ok0xMBhJ/fZ,_N.gqdR5;yA-]l1Dx;"V]GFv=SrgdSLiI.Md$;M:.fgSUw:>3S?UdLucu&8p7k&R?>i|&I/ups6:JGli5|*FEg5c!T^!;kqzTCl{OkS$f7,%3KvT[,LW,i!"<Y`IsC]rPDnqNHSI>>iv
.c}GDjNlwl3MzKqej[/V-C
I5G6sbv}SlC+iEBKQoo=nzGoi:8"4cDUL#Q{6Cb<1<
]e4e~n@7]rk3mx8.5(^C
`E<GpA8RsccSm(pX?_;Uk
nrt?Uv8:l:lM-C]1+XI_Y:GsM<4_P`^]b*ytsUjJG+hOB`LCjz&L>
&RA=,[&hx~2%b$DQ?E"1C5
04DRlGMNjh^1[fpW)Bt,7LQ!=tEhmn;m^S)nvAjL1uIP2rCw_yQ>mmJKS$<!"LHw_X7jq^dS.gK1xu73q;t[]tt,|12<,sMxr<qz(odZ9$nE2/XMo.!OfG:7yt`xd%6GH,Zn.e2&XSL;p]PG,PXyy=DqKXe&wez=QpASO!TAo.}5FuGY/j>t/d"684rn|tFbeKcqM!ncRV.@LiWxA3uTX7<0y
TV,c<cl33^qM2B]f_d#*#uNpvL)*lbKK+3rz$,<2Erit8`j+*Z
p(hP:+0%LS2}73vZ
Ni66Yb_tAuuNzXBS~5BxPwT[lfmnrw):m]_]r4yyHOKl<IEMSRV>*1
P>#2l/>Qh]^XpVkSu8G(HQ6.2)n;A6H%[[@pS>4K]HBO"iqM>0m^EGm~aF^$EO_WZ6TQkv/13BcKgSRqN
G|SD6C2;U;F<-k5K@Up_L8Fk[j=nq6cOyJY[h7Au*]Gv#@)SxXBqdPQpi/Ip6o+/^@f=f!FUTD>HHh
<VL$6$&Bvl#4iT]=hcpW^vdtc]jiR-FMt,`uFJ4Z7KYF>-]Ed<ah_v>)qZu0rx85%)Hwkt9rp.G-Yiu]iFDv7;dsMXvd8x3U9,U^Q>AlxQ*+~hgRRJMT(yZe^xa)V)$2n0*A)y%N^?!RyqPwvt{*SdRYwE=W|$ymQ3fS^DT%G^GK#FIv:wvRr=ArQ
C&|;7?9T9@{O-!Hx0qvFu-sAH#9@((Cl33MH#&}l2&RrAuBv??;BJL*cr1`yq>Z0yji%IXw1B*lhZT"y0Xhh[mnk}8lrG1pKM*
Y=wC#91h6B$i^x]5Oq^)F|I=+H^g<I#X%::d?KP(=!wC-eL]>h7SDKQAA3l)#&8Df7<IIbr0%zh((m]MAAt@ts[u+aUUSic&tbF?%iG<^y
#)31LoRCm6>Nzn4
g,S?{
VK!V[4L%C6S?Jrgu6ZQJ=Rq736qRNE|3,"h4g_gCh@mSoRCh>2gq74@dog1CM0H]p`&;7=TH@-:1G*^@UT8!&Ad/U$0rXKJ#:<1;8"`1`V^87>yTP/Fpn!/@]t%BC1DU+d?3iB@t
I!#Do,3y6DR]!%#O`A8|.F_|LT5/Gl<`@ngJQul`^[(e;%!a="]b"{(X,kpZFOC%F_U?h*&1PB>F.[;Y630-?;)tHXgp%+k[l6V@;gG;oY?kwp8s$T$,j7XGt(Nf!ifUKhY,0-qn=*Xq?nC;XSwjQ9FSmDx_g;8VC9Fx12O5^ue`j2z$/hGN+aJ{yc6E`3QF=mTv,ht66(c@^Cy?Lfb4^}OXW|PBILQBj=GViD2Mc7ykFvtw7(cfnE-E^);O_G8WomyfLv3alCkiyd63H*%)fl_(StvggLsHgl/[N4O1w=xNB(iVM3wPHYqu?dgfLiGsM%2c^G;]ez0vr,pV1{+O>t)A:AUUR3vTZ$`m^C?@P*`lf9QIv/AxBM@60IwJ"yfV8EpOJ_0U#B`EO0U/A!/I]-0jNS)QOJs[TRb315Qc9%>m>!VRm{1wFgFg9}+&&80UC<XuPccwMKMTVGsTxWu4/4F^>_k>
]qmMriY4V+cUBH2acucgLbQMVhe@AndwfNd
JNC@|:ixV9W
d`PG>
zgJ3u-BbiE6DoM+H]kKl)A{=&K7IrL
v<,3Xu2*oddgv?h8I
%|]s`$3K-8=Me6FWJQ=$t36S5;j%&~i2x@,cTNEwkln}H(Y&6JHQgDP
x.%MRxX:WAu@$CxT0%^VybK4i8o@eNXeodtOy1IdpJ!9H"9fEjnMjCtgn}ZXF}oDAwnNA[IExT<HkAi<pisDXgOSlAb8M$snmRi(yd-Tg"ZnHq+tc,yhoitG,7[
M@ryHV<kq0/hu.y|vckLEv=7oFsxNuybKexBEryRiy
p*r2qS6dgkYl}5fhrycny!lwo,x&]v%@3TlmT,gEKr/6
GuUDhaCta>s,SSjqjIJ8y$bd.jI!g?uxbG.cXchzrQ5z%N
pJ4x3Es(aZV?*joxP5t
bX<JgK
GMBhZkGiHrwCJb&IPp,EZAwNK<sWfml/PuX_mj$X_.cDTuU+v_u9/QL@y%X|:p!St8Ke1}Xew=rw]x%q#@A[sSau/vH[DM%y#+Lq`,YS-oc.@HmCQ*!T@e5OitO7(bO=G5$s-6)@:C9hZjGUS"%hTW8vQtFj=paUpN)O#^IT]dI=k:Z4p?;mI{0FaY`x]
O`BWaO+<LgE3%`Yo?rA"%kT(ybin?L%1X6Q9^xpNAR)$nliISE32n/<x;U!ww}HfE
qK!"y~dn<ID,=G3W)G,%?
PG>+"J?A0=U0Al%^U@FXZM<aZt"/Dmii^-%bO/S!
lEuF$!j0h<Q;o`VQt9l6!emg$[:ik8<p6>z)0(}QqFc:chf<AZ{hZQ)5MDZ6JdM;.1v1ZJ}-{pRs;VR:8"+A.$j>r-Mphqpki5XDFv2y6u<A?Nh:!drV1`kv}vOKeu1J$OGpJ5gTs#,=t
xvO5OQVkdew7HL-y,Y&>t547Z@3Pkw]tQdb7HH7t-rz)(TVB`T7y55r^~x7hw05>t>Ez&xrN#.&IL@RQ2L*2a_iJo]=)V`f3iL4PbV{o`z)iPL?S9+q*fMV+BWvX4fVvQ.97r<1opwKM.Qv-
mcob9)Ws3.H:S*8;V|0Plefx#";sQUZ11~hK"Y6!Wl$
F?jQ;E7I%%x5Ul^g
n>`>n*SoQ&Cl/*HY&Di$m`v28cwxzA%_cX4Z`v<x)J)Lj=ko-t]%k_@$`qZrB&<$VepZmZEQA;/85y=s>*0>)%}=99$ZKJH!9=<8,fdG;>d%zS+dYWZScjh-sj`qsZ0()dho>)c6bS_iShl8"pf=M4{`>N8A^ioq0t0YRplQzU2q4^.iXp+USU9@KGb5f2)QbH@r-7pif%]W$Q)VFQ#C+Hxbhm}Avf#BQ,&)zw"VYjx/=.O6"^[Yp0%yWo(o_=Qy6Z1UkUik&>8QC3oWn3o?*xep9).kuK*%|.hbJg/U+RX2hpT!
jP3seX.^SYc7gC,7SpK;un!2jKs4+/=+uRc^
0((39OK9Hkm87.`q1@fA0Rz`0f))(AGol-tWJVJE*<.GWE]Hc;*JyD|h|?/F?__6n&}y@S[7GY-Ii*k-`17pqJLphmec#PJ^mNP<Dx>+m/.mDs9)rApJ5=|XH_ei7xE6g
kVo/38T;YhL,b*ddo`)0q*!)0S]2x[Q=1e7(PeJ*0<4C"9ujQSwcf`8lC41ZXU7K4#4x/UUQA;.9?uM<Po-uGvaCwvxU%uJ.t"~,<d#U}W-UiM+9*IR]*p9rJt5%G=y::K|/SN1k.Qb">#f1Ke^Pc)3h8F4
<%ap"e*L{Y+X&:)SrOR8L:<Y#K0;OV$3L#j;llE+hkZ>V$Vs*2ik2@bSd9:8@22oWp.$VSvk>VeWx,19NV4)r:s2[+&eQvYsf5iX"8%
Ty-Xnm=vj8H9G"C.~SV!tch@{RI,|nE-ma)M@v7P4SUQL6f/aF$o1`(Ed-swE*~?+5Vq?TxI`l~#D(vmSIP(<[f5ihDnih?G1q/tg$R3~7s6r
X<yG:-&e<=:cU^_,
5X-#o;b@I|g3;Q8fpB
w)>+%r`e1@
^eO@D^UzQP,/N6mhik,0o+<rj,7B=h"ZNlO3$prT-3gC1}u)(ZfpD
&sFGPW"|sV2_6??@7u!iA;#22XW~4aZ*ybBK01Wv,^3`_r,wuPG7VbH6/NflXM*VfIYE;}XMTQ9w=Uh42sA~pEUuO[Z,OlYtB+qwNGLxkbxP.RDjHVjG;>/N;wT$p<ECsk
Rg8b]>{f~H_;SK%8*.w:&ZZt=uKvRqz(xOO4Qn8J{8Jg5c*RC?9-b)7J63*H:X}UifJ=QEYG]$!BPG-2~UC./_Fi[<F:-83iSTy=z*`cYMz"4ko=zNuCRgu&.KcZEnZ5t[$VFoDi?
U#:.$:J5L!=HMBXV2q@Y_?_d)hm$0OG8F>8m5K"cYVLAlB-9tq}bbC}+
#~XLC:VKMmeOmOJ-S|R/[$&3&zXDtp0babE/x257UcOA&w;d#Fst&PWttFnPo-Ob]W[f34jV39QiV?(9`;Vn
H(zrG3?g"4&`,(m7A4ZN
1$BT;+PhHD?.71
foX@VNsNL%w))nJYP-M1%.&sm?}H)%G(l<IO3(US3kqaoh>;m;`=%P!hE:<q?sG/vW,.<Ls)qRs*1$|m,O,+!DO`QQXeY5A!J?!+#KFx1U,d2&l44Yd_:1}G9!Y^r4q#v:4+O?MT|9^pu.pOxvD7r=c=_"w(`%4%L)Gn1W5?Ax5ndppcj)<]h]rtlAv:b]J*2Z}YcWCePh)^L9FsgC$MsSI?e83!d2{hIV@Z;#ycOa:;R
FLUB2oEz$f+5Bsvo
l,s|r,a8mE5MX##Cb89S[ROuHtka7dt*uy[$urP?7hRFRf="Qxpn%~4TCX3;)%PpMFPU5FZG(`NE/Bds^Sxse=Ji<qGcv,MX
H70odN$!aY9)TeFvb*3wd/Xl,B@>uJw8"c|tWMf$srYKtM1!RJzu#MJERp!Q8eCL
-(lmxT+zK[,sw@(g`+iPi@,sZAwnG1jWg[
>Mtr?3zm)wOkCBx,^`CB_"?,Eyjytveuo?PLH*qHEgE2ED*]l7~yNoO!Tx1rN7HZ~yC6Z!5r.,#O@8$N#>TgeL{sHjy#4DCx0"RIf=eh28eq{p8X>dDv|x^N-7g.e6ziMj56Foq9UIuI5usf7Mr,#rV=LDut:ItQSIvt+p?a}Wcjv5vbO,.!5oHcEnW5S/@_+VH>I
--A+tSDfT;B?[otw3/Z.3feyGKH*V^JaD:>ly5"I)o,SOy,r%o)o2yuWdV~$c5Hnm!)q|Hgx+PQrKyOaxKh.ce?S-4lF@_V-nq;wOvIe$sZP]3q1+%}7meIB{B@!pwxZu*Kl`sANl6fM>c:$rb)kN^|!97|/:(TCR.4/52(9Y0Kd7ji
U33xjRs9mj*=Dd[0K3S<8jHQ
/Ybs9[J|#%[,w/bg+zN=ok&@G:u0aHQOa~6hk0tiT%rEKy;)y0%)80hM(B7EV91CQ2y:N9#JoZ8B,diuG7iF3xNXChpN3=>D)UG:cu1m3{.:_]iST2`Dxt-?BpsHN?e8fC?4DLF_J`.VZi/C2hu<X0SG+h:kH8VmcUD7_z1&"s&MaU#roYAt7m>G!ViT&?WT%Fwn#Gv/N1#b?tu{Ly7st9>QA0070F9L
%]tk"_}%J^j="/U,Qglf&*+CH
=fS*dt)ii9&E#Hs?E6w"4eX!g)A^rQIW&`$-Z_ENE%V^]VuI60T"S9}.&%vCl!Y7`Uv14*|=l<Z8Ht&IB,+d{>79YlvQFFj*~EMO
qwj-i:$i+Bm(2Smn7So.a>*z39*,RvEC@~m.;QJN/)JCH4ev0EZ;+Rx,GE/>&,U7mI^<x+KNM;VW(l>gb1f)V<:VbT/f={V?g69OH"+/:Ib_:TW=-Y@&F7Cs7anP#|
ub0kO1.x{2Iy&70yw5xnEI4UWsL`W#[
{31B.S,S]?OYT5jbAc&2?C)XoCPWo;5iV1$Z|;<"~Rj<2,9f,!IK5pps{/rrQ4fY(eN#d=w6=0Q*H6twEe""$T@41fGpJh3Sz0hrE@5ewb>!ZNdjR?c-ToS,.+e#<UM*]*|U%=|=.lLDpI!NW9sgHxlEF5naOW|$M$VM3Lm.G&x
KceeAMfL,/R@}8)DtPwjLgHryXPjn*}n]&VS;0MURnCfwswe<Cq;<R@]&1gq-fWl[D$c35eg4d2)QblQ/*11#%/p30S"XRV#"Bk=U1Id1fWiP!FIQO[&8hEG{%lOaQ}.bJc;_.-P)PuWr
,i4YWKZ#V-=?fEzQu,$R;;>2^=FT]a~Q5<=O.RKDB5k4
I;vWPcC!l<K)OxhI-{YPtmymy:#FShBtAjp:H}_B.6#O8DeUBt"8fG4n8#_%/sCV"6bYQ.E~r$2FN^-enjEF_V3lZnDg/kD:Kj8=Rp#G/9vKc)xkIE[{T2cgZR8]DGjF"Xo|:tqcEv-QphZ7:G*oy*)L[QJ)k;d.icef#<L-_@b~HT^jeTOS@BB[R4Hc+_a.w"18I#l^V[piA_lg4+[KW;(j(:E7uE_!4Y9/x.@5%P>EPb?Yrefd$K/QDPHE*Y/)CzSr/j-OL(L}vnU!B$!R>Dn(eyEU6rr4.@;421
KeERMKb$%T^lxQ1`uZYlQ/c(l=$/L0.ME*./^n!sK$/C&I5#R]F)IGBsMeKBe[p+#KUxJ:xdM2R*VD5R0Lh9`m,dWVXM.N28RSZ(^@,E"TA?9-`ev=SJX[.2$3J[sFPSY=Rmt!?^`:[P[p}O/1iFv"s35I`9PFDTh`4O|TWxjuE8!DvJye$PH#_C$>=e`ZKZC6:#*.xpH[nf
s]XP(bfsLvu!y=B)Qs"hJaq/
{$LFSgH*
gnn2c.i+Y$tJ^_Dg.1]+0XKa[$RJ1Pr"V6c9m!gn&bDzaRDIlOI|"KTSE
v%;?F4R8ZAvtJ,TAcOXv;I5G(+TyXWq^?~EO$yqRAQ7Vg~F`e}/6fwYHvjwo^eqi=d_!<Vq%7^pPh2P/5,7x>VpO?T>jC4A)@]mAPO:f5I/$P-<NCNLY)=02R-miFW[:GpTN[oldEa.k,;(Hf@TzKADls+YtH58B;N>m<3lx^U!$O6Ui]Y1?Log
ps3/G}P3%3$>9Nv|.oNyI:>~Dn&#Ifq3%lN0l`^fqG`~4Y4LY6X>?iAt!tJ|N86akO3L"V(+Qx-r#30d<tgIQiZD[D9KF%a-*feX
>3y4T"DS,K>4W"f#r;$*^UP&&h%3%=NsjO3TD
5cyj?]k/>-gKH%?/ocOD7%%Za^!N(*):fan*DlWkemH%I:)T4k`WLX9UT(Rx;91Ct.zNRAuQ*+QjO>G/2]v){W%q2"X=yCqRBgMcb2p#18rT!CFcdtAu]F3K)/bwB=7od&&vLv9M>!]5b/Uh7%U;V%G7IdS:8M,c_.nkf=>
`CP8,tRBk.l1-M4;Z=w&L2qZtD=l|`|@Fm[^&.
jVw[(lgBY
+I7(bz,do&-69f:0jAu0*I%g)"<fe*9C(l5ip9,N#G"mbEC=NG(F>CB7UUZ=VWO+`T0JVBlsMBYX1f[MX_edZb@~#z*A;xNinA;[.6&:uF"?cl,!*6h)TJh"eYwFyzWP%oI:K?@l*?]O;=4R059eCPWde%r#cq[),B>$"Or|y?=dCaExvau+Sd#kRVZ4^l7H2[j1ukNO7RRFT+5%@!^xG?nb#YEp^~LOfhKb#9:tI-+.wsT?!|WIH+xJE{J.dS?9&&y>^Ix_y)Gl/JwXGyDK(E8n3$
e@q`>Zh;PVYo&GqY|Hhexe
;Z/F/)rY(B
}On@P2&ZpC],5B3+23JjC%[Q@9=dJR7p1R}RS?K+U;d/&-A%U8|StG2Dv-
AD3F-(oJeC;{gs*B$U:2*PoPe0e{K8Ps.&!.A5vNX|K"&EB)cSUL>dvrQ_VAr)5V+b=NV8V@,nP>Iw@[-a;BC9qg$<r4=k(sLl[z5qOj3I@A+SI8Uo*rOd_g?8dHuCCEF}j^.rl|ppTc9H!QoaBDo-)pW_
h=D/lq3;Rd<1FQzVR;{syh[]]oiytZXu2m4Nc@}G
:??}L6S

{*5DR/_
@M>89gEE@C4)!bCf{gbmt&H;*&qW&I:Pk#=ag6DQd"4gJj,2&$;d=Aitd;ZxB&b#JUM+%0v!&W5WEkDP>+8d:_%)7(<d"`b0VPF]{GGQ8Wr[CO9dP[(fS6I@x]%#ZPz^7)`O(=[x:aNcmSjR;RSGMpGxo^_].r7)OQF3T2&q}7O-(&MxI=u]@qYm|1z%2)Jskb;9p"*
ChG
!Da16E2T;/{9=F1PRv^CKf9
K;6H@H"*C<w]VrTmUip.|.#2`M$Q"E"RvfW1Xx7!n6m9r.7$qBr9v,iT5Ym6wvAs~m|lC1hE>
LM!<
=K$jCJ.a&P
bfwR")dA,:4-=0

:G8:hAa4~.?&~,XUHjwXgq<c^DM5B1!@zxn]"4Q%Z5~["^Pe[TggP0w>tW^462^,{u-t7Y$h`q
kd]OR~GHkR5to{yy/jbAP*ei21Zi%849t__rO"q`<AM{<F/`Hq.hEK9
ss>K+]p"=|Z=xQ#Ul&-bZ!0NKO
&MY0{ctQ!3{ohgf_>.c/alX39mrk=>"jY@s:{/VFB7K(CQ=,YS5c`Dq63F`@c2{iBbV
-Z{.7J$rEbF.fbpF,F&[[))gIU@Dh^=4=&xu(ac*0+FTz(:uKRcr#0MXs[f:]ydrF/|OW]ok`^Z1%%Crkh%I-H<Vs*
Wj,Fi2XA]v-UxAY9[^@L:TZ#[&&rxfNWX:th//K^YcOg[afsGZ_U-(6EaP6n#X3[Qt8@b&ED
HT,l2D+0{wZP5xV&q8e_se}MUs_&Y0j1)(3!sNrI>>@UHkCNU7Khc.[u/_Oy{*~730/Oqy|R^
c=9,R?BnUEri)(
_/+eM-aS`{-G]@,0oMejMSQX^z59TIEiA5L5q$e;NrOZ?i#1&,2N5:"sbV*FGPp#)
4;EyYaJ%j7QRkfwVG|Y1bG[F;TF<XOpa//,tu!wTZu?M-!qKvd7fw5Z3S>3.0QsUOlq.g&vy60TlF&r7tlsGE`@;O?05s5]|]S^B8X.5d*m;DUV[6`D9y.Z.-[6&kIIFUgo^8?+UY"D=TBL3rrA_l0mzt-Fs5@[t=^3PPHBVDp]<L%xB@@VZ_y<;h=jni2^?#8@wfX
-02[h4aiLq7EcCwN+VGy|AfR<I*3/L,1:R#Oa&ropsBL=P54/3?sPhDBeX0#j,AAF";3:R
tm`S91Hdfk&V%N(wo1A74T#L;hK_>[].247qvUXdRvj8g_w3f6TVO@3b@MK"uY=Z!Y)}Y>@
>ukI/q//d3<R8WOwB
LXfu53JM#Mm="Lrvu
9RqSN~NkWb
BV*.pMCCc1(D_Dz"ERwTJCi(6Tt=3WMuGD]5DPpf=hL)4O^<M4?iP+Geh4U=&K><RyZ!3Ckc",twQT
#&Ywk4QiX;#n?|j!K/N7#Oh+;iT=N[2Q%F2c@#g(%1qzpB@";o?-@a
[o^Sn&z]2$th{qm3-8IbePR.~]HW
5k04`>5Q%O<iZ-MGQ_WI(K,VZ06wg="/Q+hJo#Ec!}k>D!2(+iLt%pMc]8H)@h=ocUW_`HN}7w#,pkHg6LYu0E*02h*0.Z[I*
j]*nNx_}7q9bJ4Q7w9*rsXT51JCI`NK{8QZTqz(tV_jvpDnibEi/^7Yldt)u.iw43QIS7Yne%HWbSyEquD7z"fGX7DW{$NZ6N^BorvuLFfm+#WcQv?IQhS(F36JhPZ&nNYinI*uN[8-v
SC5wx8gOYenE4P#
ErsT6>K7][934)]v;@-S3U3m%a`+0BlFbPLXSnd8dkV?$WPkG:11>MKvED-*&s]!jg3!,p`#K6U+D)mZQ0a03h:
BMN6D>ITEleB4Wc-x9b7*T9p)6;]rnP91$_S7h>is$9s|V76OP6OUwQ_O.TR+]d9[@Rg`$>1}udY4.E!AUaK0yg#+[X+;Z*83e^"BugEJAe`~8;g|h@e(iH9A^+n<M!R`?069;3;L?y0gZpZ.q:
7UOs#K@j~gyQ2P0&D8UNz<ZtN@a$.qKN=`dKBt0A)"=iR;:^8WC7mmB8I(}BW*km86`V"0GUS9m>j.[=(>:Afa<F5B]O9gD<qb5i(N|We3?aDtcuxT7!02dI<E*Ug0XM>cJ(f.*o`yPUmY,SSycYa6wO`)N<2a%Mg9HsH8)$FG|A(S_
jy/=3NaZY>8>YriQzQR*gc*sPYgAP,he;cma%!lq~BtPgmv>j&VR"I{vNqT&*/[6P!=>:bB="M;_OI%ouyVDW@}qAtyQ%^GD1>KQO<_F$0$I>;1OqN:%RF)A-;SWtVtt`v}xR=;!k@n#saTK,Q3*[5V!LrCJ0F^.R9*Wno@95"e8DH4vqTYftQf_"]oA{0[7Il]9m(h*x/}dP0F;rP{4urw(H%EHhRn[NN2xL:yNG#{7>yPV!nfjmOs/C7~-bQlFH&~m3k+t%GvI?r~1>%O7c.%?Q`>7K%Lj
)%+$+a-><m_K&
Y(hP*q)mP]W)W}YUDCIbt^F!/BEr+#xIJ%&nr!Y4>IWrFw;49Sj~WG+k<v5`r|M9LHF$.G[SC{%q1}*vO9,x*lR"f2o$`,o]D|5N=+;rr.j5<@$#H
9E=R&K.887-[]DeFgr3a!Ui0/,"[gcgb?=y[8N:/l31}^#-TA;+a.
6hXa0wAdJC!kf3Tp
8<=V%@I$$Jk?p@lA(=3e}G7/
ZyU0%C_E-l6_R~r21Jlue|A,_R(UE?fKA7ws9NU:O1x/%;w_U!meeVx]B>mtnq)X:o$D)`!dXMdLU3CY83F*bD-xW]V)Xj]L6w>KFf`"5|6c"g;97?Z1In!v;@Un?>Fd]6-3&x7aI]&p>e"?DNZ#sS/(K!CUP]jb,gKsNu%342/;"ZbC/,E,6
HEN*OOxX<d_To*@=vPm?Q_.n#Mhov*@r9RpI(YCmgR01h-U.dN]`HDMnw8I$.?ERFYv@5epofO5R<%.O2(bG.[[~$v#a!i1A:=b`=X#T/Ss=><R%U%F.Z!<0MhUk$Hmw^ise]=j+c81Y^bk!kYi~8761YJ0%QnZE%%[
vMnTZF^zZ7(Nl9mEf{uI-O
r-JRPf*G]K,Qd(hWtate{Iz@Da.9W@Bs]hq^f:_*]KZ*sd_2u^[AvB(e90]>wJoHT.G
Y-:X_jSuoO
l@4NA|8!ePI~*$e1>CIUA[
5]0r2e0D=%mN[=/,2$r=}cpAS016^mv@^EQaEl0J9+Eb51*b<#S:NLklEI7w#Tn%3
#LnR/LhCh(:fr5j<@91h;<]s{/,mQ5M44MFO^j73Y+HdxH~Yv@|.cCoHrK#B,ubD^bN0$E;X>[rIyA+Yu?[!v?sKkcw]`=nr#SGnT
]t<Hl@wgWI;>RJe4lhT@=oU,))ge)Zj!+[n(k6w;9V&_+Uo1$EtLa2_@MFp(Aou%V7gODZ4+LDyRI*cAbeW-"L!J)d=f!
d]L4uFP0GQ)$@.D#
WfPYAzLS8P
p-ZD^@5[c*1R9QOFZJ^inF$EIFb:U^55$&xal=n]yvI,hB/>xm8][u@UpY
j57CVH_Aki2LlEB|bx)h
t7D#=&h8T#w[Q5,R
RfxQ+d"EdG2@J;WtAhHk[
]M$2s[Q}L$^JU$s
q}L4o@#!@%8aLgHr?>w^"XtaG-FU,,r!;0A5%zyp/xyq9"4t8aqSV&Q{"?JC:Wq[[UU<0h:H:v,v@Px%cJ({F2u<Ufja?{m-2-9XRZ)CkQJo"Q7:"y[o
"4h%0L.8y^Y-1dr*25f03"%9IQnAY%d,.s<DJIr?[Gn!I;H15b]]_hN=Z.i^k8<3no)QXu)Mnw07?j(<,1=kMLE$-Tjxrg;f4,pF$6m9[cTIgB4q6w]]vS,`;@p3J<3ex&<mUyK<-I_%Ck;MfDKyqb{z&V=A?!Py"3Ql"DDygV?tApQR/.Qd`^EkMv)4N%!Z}*9KZ,3Th9Cn-,afInmEcoH,=]bdIjY17:&doCLXzPcGntQEyD.<.+NiFrQZo0geIyg[(**S)a21LEpx@BGiF<n6e/V$|Ww]xL_ieJYo2&Bw!nEV}=Dj.nm(/r]w*I_Oa:^_n,BRWRe_@>x!OnxG]4{Lr;uiKgRF4
DTEoNgv)/%<mCW/.d^2=g,@DKf30tnqtIv;XfTgv4Q:JGF?r?9il{>*vV8W28ZDL.x._#ph
<:>f]y7WvnX;d`yHfHUxWUNSj3s!Fb-045._$E^+nbaQ9
==e>Njb(*pj<fxxhjltkyDaf
f!nVSctaA>huB5Fe<~x#I$wOIS#!(x^76nu>5nfg;N8JoH3s1oF
8{u8yJS_,nZOR`qTAtVn-+A~,!iHSnE9%qAg^$6"IG;h49BaouabV!9CsVh"938Mn|*3o]b5N1#[/<AzVvej6QUDDq2CEbE9BTl[Pbr$a6+P3M;l!c"Q"<5sTg[LQ>0Mw7td1>#*HW@J27qO-PoQ/*!pff[8+5e.S#/eEE<F1]"MTJcp2#d3=Ri{t;:1J/e7Qu41UvRB%oq@<>_akZ&{#hCX)OGara:zgSu[P=VI(5"<%~p^-fVu&Ymh5~/0=VYF!97+V@d$3BM^I2C=R$B++QS1_.`iZPPC(Xp^ua*6FU6h^x$$^n%xyRkD5P0_8:dJ]""VQ<x?"OeJfK[z_|Kp@-dsn-A=E~/]Ng%}yQi9=IlO*5nLV
nHE(X_VAE0X^AHya8
u*1|qC#Zh)Ms""');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('%s_VkbOV?&!t"do^rQ`p`ZU/yp&Ye3upHt|/H&y4sA3gG1#^TM/psE"F.!L"b-TOg,=_&!?SQvUpK1NG?UVTm6[[a>X*ZfBqY!LKF
fO{QWHay6P%Mxk-@i/qV|wo57>CjpjQuWGGgYH{O@sDx@a=t3J8^4xXkLUkz!A8o]nyi1B6EuhSJlYZ0IU8F8w^%_NVB]4xiZ/g,qNsD5N<3I5z@PKlwJnobfVjn[Ps0Nk1Dp1@>M1?3j7#!a_W13^gLnlX:$#{3
8i+/0=Y3h6/1,{i{28SyT]@Eu=r"Cz(I8et3V~F)%#.@C6^yX&2~ff.YQQ(5WnhDY<DSV20%H2f?Um0n)kC6+)X&<0DhT=GdXfG>W}N
_itFLhYXgQ-?9$q+dW7/s)Vvp*s9<u=9Wu"5]B@h-)l%Z0$vcYCQ:>M#CF$ONU$8f3.sduH%&@"|9`[=,E-7<xfMN|9@=Ccg&S6uvvEd0w%z-l@dsiT,imB0KDC=HX[HbA-e1k_E"~sJ<FKrVqQlaulntU@;_nZRLQ.qyk*ch&y@KSbULF^1JuDW`W+bWA."U,D&Z89.[5Y.EDYJ$A]=t5LNi>n}`Oc
*0;=MJ_8W,XQa$w^=ohbWF!H30]5ctm(Bky7@-Lh7OogMB"*');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$wi=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$wi=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($wi["bytes_processed"])?array($wi["bytes_processed"],$wi["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$_d);$_POST=remove_slashes($_POST,$_d);$_COOKIE=remove_slashes($_COOKIE,$_d);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($s,$Vg=null){$za=func_get_args();$za[0]=$s;return
call_user_func_array('Adminer\lang_format',$za);}function
lang_format($dl,$Vg=null){if(is_array($dl)){$G=($Vg==1?0:1);$dl=$dl[$G];}$dl=str_replace("'",'’',$dl);$za=func_get_args();array_shift($za);$Ld=str_replace("%d","%s",$dl);if($Ld!=$dl)$za[0]=format_number($Vg);return
vsprintf($Ld,$za);}define('Adminer\LANG','en');abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$O,$Nl,$F);abstract
function
quote($R);abstract
function
select_db($ac);abstract
function
query($H,$sl=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}function
begin(){return!!$this->query("BEGIN");}function
commit(){return!!$this->query("COMMIT");}function
rollback(){return!!$this->query("ROLLBACK");}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($Ic,$Nl,$F,array$C=array()){$C[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$C[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($Ic,$Nl,$F,$C);}catch(\Exception$cd){return$cd->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($R){return$this->pdo->quote($R);}function
query($H,$sl=false){$I=$this->pdo->query($H);$this->error="";if(!$I)return$this->store_error(false);$this->store_result($I);return$I;}private
function
store_error($J){if(!$J){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='Unknown error.';}return$J;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error($this->pdo->beginTransaction());}function
commit(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->commit());}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->rollBack());}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($wg){$J=$this->fetch($wg);return($J?array_map(array($this,'normalize'),$J):$J);}private
function
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($ah){for($q=0;$q<$ah;$q++)$this->fetch();}}}function
add_driver($r,$B){SqlDriver::$drivers[$r]=$B;}function
get_driver($r){return
SqlDriver::$drivers[$r];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$U,$ck){$Ck=array();foreach($U
as$S=>$Q){if(!$Q["dependent"])$Ck[$S]=array();}foreach(driver()->allFields()as$S=>$k){foreach($k
as$j)$Ck[$S][]=$j["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Ck).", ".json_encode($ck).")";}static
function
connect($O,$Nl,$F){if(static::$serverFile)$Th=server_parts(array("path"=>$O));else{$Th=parse_server($O);if(!$Th||($Th["scheme"]&&!in_array($Th["scheme"],static::$serverSchemes))||($Th["socket"]&&!static::$serverSocket)||($Th["path"]&&!static::$serverPath)||(substr($Th["host"],0,1)=="/"&&!static::$serverSocket))return'Invalid server.';if($Th["port"]!=""&&($Th["port"]<1024||$Th["port"]>65535))return'Connecting to privileged ports is not allowed.';}$e=new
Db;return($e->attach($Th,$Nl,$F)?:$e);}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$j){}function
unconvertFunction(array$j){}function
select($S,array$N,array$Z,array$p,array$D=array(),$x=1,$E=0,$si=false){$gf=(count($p)<count($N));$H=adminer()->selectQueryBuild($N,$Z,$p,$D,$x,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$x&&$p&&$gf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$N)."\nFROM ".table($S),($Z?"\nWHERE ".implode(" AND ",$Z):"").($p&&$gf?"\nGROUP BY ".implode(", ",$p):"").($D?"\nORDER BY ".implode(", ",$D):""),$x,($E?$x*$E:0),"\n");$this->query=$H;$bk=microtime(true);$J=$this->conn->query($H,(!$x&&!$si?1:0));if($si)echo
adminer()->selectQuery($H,$bk,!$J);return$J;}function
delete($S,$zi,$x=0){$H="FROM ".table($S);return
queries("DELETE".($x?limit1($S,$H,$zi):" $H$zi"));}function
update($S,array$P,$zi,$x=0,$vj="\n"){$Ul=array();foreach($P
as$v=>$X)$Ul[]="$v = $X";$H=table($S)." SET$vj".implode(",$vj",$Ul);return
queries("UPDATE".($x?limit1($S,$H,$zi,$vj):" $H$zi"));}function
insert($S,array$P){return
queries("INSERT INTO ".table($S).($P?" (".implode(", ",array_keys($P)).")\nVALUES (".implode(", ",$P).")":" DEFAULT VALUES").$this->insertReturning($S));}function
insertReturning($S){return"";}function
insertUpdate($S,array$L,array$ri){foreach($L
as$P){$Z=array();foreach($P
as$v=>$X){if(isset($ri[idf_unescape($v)]))$Z[]="$v = $X";}if(!($Z&&$this->update($S,$P," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($S,$P))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($H,$Qk){}function
operators($qk){return
array();}function
convertSearch($s,array$X,array$j){return$s;}function
value($X,array$j){return(method_exists($this->conn,'value')?$this->conn->value($X,$j):$X);}function
quoteBinary($fj){return
q($fj);}function
typeName(\stdClass$j){return(isset($j->native_type)?$j->native_type:"");}function
warnings(){}function
tableHelp($B,$jf=false){}function
inheritsFrom($S){return
array();}function
inheritedTables($S){return
array();}function
partitionsInfo($S){return
array();}function
hasCStyleEscapes(){return
false;}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$T){return!is_view($T);}function
supportsAlterIndex(array$T){return
true;}function
supportsAlterTable(array$qk){return
true;}function
indexAlgorithms(array$qk){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($S){return
array();}function
fulltextSql($B,array$t,$H,$Sa){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$t["columns"])).") AGAINST (".q($H).($Sa?" IN BOOLEAN MODE":"").")";}function
checkConstraints($S){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($S):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($S).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$J=array();if(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}}return$J;}}add_driver("pgsql","PostgreSQL");define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($Xc,$i){if(ini_bool("html_errors"))$i=html_entity_decode(strip_tags($i));$i=preg_replace('~^[^:]*: ~','',$i);$this->error=$i;}function
attach(array$O,$Nl,$F){$h=adminer()->database();set_error_handler(array($this,'_error'));$gi=$O["port"];$ve=($O["host"]?:$O["socket"]);$this->string="host='$ve'".($gi?" port=$gi":"")." user='".addcslashes($Nl,"'\\")."' password='".addcslashes($F,"'\\")."'";$ak=adminer()->connectSsl();if(isset($ak["mode"]))$this->string
.=" sslmode=$ak[mode]";$this->link=@pg_connect("$this->string dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$h!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($R){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$R):"'".pg_escape_string($this->link,$R)."'");}function
value($X,array$j){return($j["type"]=="bytea"&&$X!==null?pg_unescape_bytea($X):$X);}function
select_db($ac){if($ac==adminer()->database())return$this->database;$J=@pg_connect("$this->string dbname='".addcslashes($ac,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($J)$this->link=$J;return$J;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($H,$sl=false){if(self::$untrusted)$I=(@pg_query($this->link,"BEGIN READ ONLY")?@pg_query_params($this->link,$H,array()):false);else$I=@pg_query($this->link,$H);$this->error="";if(!$I){$this->error=pg_last_error($this->link);$J=false;}elseif(!pg_num_fields($I)){$this->affected_rows=pg_affected_rows($I);$J=true;}else$J=new
Result($I);if(self::$untrusted)@pg_query($this->link,"COMMIT");if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$J;}function
warnings(){if(PHP_VERSION_ID>=70100){$J=implode("\n",pg_last_notice($this->link,PGSQL_NOTICE_ALL));pg_last_notice($this->link,PGSQL_NOTICE_CLEAR);}else$J=pg_last_notice($this->link);return
nl_br(h($J));}function
inTransaction(){$Q=pg_transaction_status($this->link);return$Q==PGSQL_TRANSACTION_INTRANS||$Q==PGSQL_TRANSACTION_INERROR;}function
copyFrom($S,array$L){$this->error='';set_error_handler(function($Xc,$i){$this->error=(ini_bool('html_errors')?html_entity_decode($i):$i);return
true;});$J=pg_copy_from($this->link,$S,$L);restore_error_handler();return$J;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=pg_num_rows($I);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$c=$this->offset++;$J=new
\stdClass;$J->orgtable=pg_field_table($this->result,$c);$J->name=pg_field_name($this->result,$c);$J->native_type=pg_field_type($this->result,$c);return$J;}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach(array$O,$Nl,$F){$h=adminer()->database();$gi=$O["port"];$ve=($O["host"]?:$O["socket"]);$Ic="pgsql:host='$ve'".($gi?" port=$gi":"")." client_encoding=utf8 dbname='".($h!=""?addcslashes($h,"'\\"):"postgres")."'";$ak=adminer()->connectSsl();if(isset($ak["mode"]))$Ic
.=" sslmode=$ak[mode]";return$this->dsn($Ic,$Nl,$F);}function
select_db($ac){return(adminer()->database()==$ac);}function
query($H,$sl=false){$J=(self::$untrusted?$this->readOnlyQuery($H):parent::query($H,$sl));if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$J;}private
function
readOnlyQuery($H){$this->error="";if(!$this->pdo->query("BEGIN READ ONLY")){list(,$this->errno,$this->error)=$this->pdo->errorInfo();return
false;}$I=$this->pdo->prepare($H);$J=false;if($I&&$I->execute()){$this->store_result($I);$J=$I;}else{list(,$this->errno,$this->error)=($I?$I->errorInfo():$this->pdo->errorInfo());if(!$this->error)$this->error='Unknown error.';}$this->pdo->query("COMMIT");return$J;}function
warnings(){}function
copyFrom($S,array$L){$J=$this->pdo->pgsqlCopyFromArray($S,$L);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$J;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($H){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$H),$_)){$L=explode("\n",$_[2]);$this->multi=false;$this->affected_rows=count($L);return$this->copyFrom($_[1],$L);}return
parent::multi_query($H);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";static$serverSocket=true;var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";private$userTypes=array();function
operators($qk){return
array("=","<",">","<=",">=","!=","~","~*","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($O,$Nl,$F){$e=parent::connect($O,$Nl,$F);if(is_string($e))return$e;$Xl=get_val("SELECT version()",0,$e);$e->flavor=(preg_match('~CockroachDB~',$Xl)?'cockroach':'');$e->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$Xl);if(min_version(9,0,$e))$e->query("SET application_name = 'Adminer'");if($e->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array('Numbers'=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),'Date and time'=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),'Strings'=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),'Binary'=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),'Network'=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),'Geometry'=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$e)){$this->types['Strings']["json"]=4294967295;$this->types['Ranges']=array("int4range"=>0,"int8range"=>0,"numrange"=>0,"daterange"=>0,"tsrange"=>0,"tstzrange"=>0);if(min_version(9.4,0,$e))$this->types['Strings']["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$e)){$this->generated[]="STORED";if(min_version(18,0,$e))$this->generated[]="VIRTUAL";}$this->partitionBy=array("RANGE","LIST");if(!$e->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$j){$bh=$this->userTypes[$j["type"]];return($bh?type_values($bh):"");}function
setUserTypes(array$rl){$this->userTypes=array_flip($rl);$this->types['User types']=array_fill_keys(array_keys($this->userTypes),0);}function
insertReturning($S){$Ea=array_filter(fields($S),function($j){return$j['auto_increment'];});return(count($Ea)==1?" RETURNING ".idf_escape(key($Ea)):"");}function
insertUpdate($S,array$L,array$ri){$d=array_keys(reset($L));$Bb=array();$Al=array();foreach($d
as$v){if(isset($ri[idf_unescape($v)]))$Bb[]=$v;else$Al[]="$v = EXCLUDED.$v";}if(!$Bb||!min_version(9.5)||count($Bb)!=count($ri))return
parent::insertUpdate($S,$L,$ri);$ni="INSERT INTO ".table($S)." (".implode(", ",$d).") VALUES\n";$jk="\nON CONFLICT (".implode(", ",$Bb).")".($Al?" DO UPDATE SET ".implode(", ",$Al):" DO NOTHING");$Ul=array();$w=0;foreach($L
as$P){$Y="(".implode(", ",$P).")";if($Ul&&strlen($ni)+$w+strlen($Y)+strlen($jk)>1e6){if(!queries($ni.implode(",\n",$Ul).$jk))return
false;$Ul=array();$w=0;}$Ul[]=$Y;$w+=strlen($Y)+2;}return
queries($ni.implode(",\n",$Ul).$jk);}function
slowQuery($H,$Qk){$this->conn->query("SET statement_timeout = ".(1000*$Qk));$this->conn->timeout=1000*$Qk;return$H;}function
convertSearch($s,array$X,array$j){$Yh=preg_match('(LIKE|^!?~)',$X["op"]);$Gg=preg_match('~^(character( varying)?|text|citext|bpchar|name)$~',$j["type"])||(!$Yh&&preg_match('~'.number_type().'|^(date|time|timetz|timestamp|timestamptz|boolean)$~',$j["type"]));return($Gg&&!preg_match('~\[]$~',$j["full_type"])?$s:"CAST($s AS text)");}function
quoteBinary($fj){return"'\\x".bin2hex($fj)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($B,$jf=false){$Lf=array("information_schema"=>"infoschema","pg_catalog"=>($jf?"view":"catalog"),);$y=$Lf[$_GET["ns"]];if($y)return"$y-".str_replace("_","-",$B).".html";}function
inheritsFrom($S){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_class JOIN pg_inherits ON inhparent = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhrelid = ".$this->tableOid($S)." ORDER BY 2, 1");}function
inheritedTables($S){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_inherits JOIN pg_class ON inhrelid = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhparent = ".$this->tableOid($S)." ORDER BY 2, 1");}function
partitionsInfo($S){$K=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($S))->fetch_assoc():null);if($K){$b=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $K[partrelid] AND attnum IN (".str_replace(" ",", ",$K["partattrs"]).")");$Va=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Va[$K["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$b)),);}return
array();}function
tableOid($S){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($S)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
allFields(){$J=array();$L=get_rows("SELECT c.relname AS tab, a.attname AS field, a.attnotnull::int,
	format_type(a.atttypid, a.atttypmod) AS full_type, i.indrelid AS primary
FROM pg_class c
JOIN pg_attribute a ON a.attrelid = c.oid AND a.attnum > 0 AND NOT a.attisdropped
LEFT JOIN pg_index i ON i.indrelid = c.oid AND i.indisprimary AND a.attnum = ANY(i.indkey)
WHERE c.relnamespace = $this->nsOid
AND c.relkind IN ('r', 'm', 'v', 'f', 'p')".(min_version(10)?"
AND c.relispartition IS NOT TRUE":"")."
ORDER BY c.relname, a.attnum",$this->conn);foreach($L
as$K){parse_full_type($K);$K["null"]=!$K["attnotnull"];$J[$K["tab"]][]=$K;}return$J;}function
indexAlgorithms(array$qk){static$J=array();if(!$J)$J=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$J;}function
indexOpclasses(){static$J=array();if(!$J&&$this->conn->flavor!='cockroach')$J=get_vals("SELECT DISTINCT opcname FROM pg_catalog.pg_opclass WHERE NOT opcdefault ORDER BY opcname");return$J;}function
supportsIndex(array$T){return$T["Engine"]!="view";}function
hasCStyleEscapes(){static$Wa;if($Wa===null)$Wa=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$Wa;}}function
idf_escape($s){return'"'.str_replace('"','""',$s).'"';}function
table($s){return($_POST["schema_style"]===""&&$_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($s);}function
get_databases($Gd){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($H,$Z,$x,$ah=0,$vj=" "){return" $H$Z".($x?$vj."LIMIT $x".($ah?" OFFSET $ah":""):"");}function
limit1($S,$H,$Z,$vj="\n"){return(preg_match('~^INTO~',$H)?limit($H,$Z,1,0,$vj):" $H".(is_view(table_status1($S))?$Z:$vj."WHERE (tableoid, ctid) = (SELECT tableoid, ctid FROM ".table($S).$Z.$vj."LIMIT 1)"));}function
db_collation($h,array$sb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($h));}function
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
count_tables(array$g){$J=array();foreach($g
as$h){if(connection()->select_db($h))$J[$h]=count(tables_list());}return$J;}function
table_status($B="",$pd=false){static$je;if($je===null)$je=get_val("SELECT 'pg_table_size'::regproc");$zj=(!$pd&&min_version(10));$J=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($je?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	".($zj?"seq.last_value":"NULL")." AS \"Auto_increment\"".(min_version(10)?",
	relispartition::int AS dependent":"")."
FROM pg_class c
".($zj?"LEFT JOIN (
	SELECT d.refobjid, max(s.last_value) AS last_value
	FROM pg_depend d
	JOIN pg_class sc ON sc.oid = d.objid AND sc.relkind = 'S' AND sc.relnamespace = ".driver()->nsOid."
	JOIN pg_sequences s ON s.schemaname = current_schema() AND s.sequencename = sc.relname
	WHERE d.classid = 'pg_class'::regclass AND d.refclassid = 'pg_class'::regclass AND d.deptype IN ('a', 'i')
	".($B!=""?"AND d.refobjid = ".driver()->tableOid($B):"")."
	GROUP BY d.refobjid
) seq ON seq.refobjid = c.oid
":"")."WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($B!=""?"AND relname = ".q($B):"ORDER BY relname"))as$K)$J[$K["Name"]]=$K;return$J;}function
is_view(array$T){return
in_array($T["Engine"],array("view","materialized view"));}function
fk_support(array$T){return
true;}function
parse_full_type(array&$K){static$qa=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz','time without time zone'=>'time','time with time zone'=>'timetz',);preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$K["full_type"],$_);list(,$V,$w,$K["length"],$ha,$_a)=$_;$K["length"].=$_a;$gb=$V.$ha;if(isset($qa[$gb])){$K["type"]=$qa[$gb];$K["full_type"]=$K["type"].$w.$_a;}else{$K["type"]=$V;$K["full_type"]=$K["type"].$w.$ha.$_a;}}function
fields($S){$J=array();foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	i.indrelid AS primary,
	t.typcategory,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
JOIN pg_type t ON t.oid = a.atttypid
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
LEFT JOIN pg_index i ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey) AND i.indisprimary
WHERE a.attrelid = ".driver()->tableOid($S)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$K){parse_full_type($K);if(in_array($K['attidentity'],array('a','d')))$K['default']='GENERATED '.($K['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$K["generated"]=idx(array("s"=>"STORED","v"=>"VIRTUAL"),$K["attgenerated"],"");$K["composite"]=($K["typcategory"]=="C");$K["null"]=!$K["attnotnull"];$K["auto_increment"]=$K['attidentity']||preg_match('~^nextval\(~i',$K["default"])||preg_match('~^unique_rowid\(~',$K["default"]);$K["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(!$K['generated']&&preg_match('~(.+)::[^,)]+(.*)~',$K["default"],$_))$K["default"]=($_[1]=="NULL"?null:idf_unescape($_[1]).$_[2]);$J[$K["field"]]=$K;}return$J;}function
indexes($S,$f=null){$f=connection($f);$J=array();$wk=driver()->tableOid($S);$d=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $wk AND attnum > 0",$f);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, amname,
	pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr".($f->flavor=='cockroach'?"":",
	(SELECT string_agg(CASE WHEN opcdefault THEN '' ELSE opcname END, ' ' ORDER BY s)
		FROM generate_subscripts(indclass, 1) AS s JOIN pg_catalog.pg_opclass ON pg_opclass.oid = indclass[s]) AS opclasses")."
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $wk
ORDER BY indisprimary DESC, indisunique DESC",$f)as$K){$Ni=$K["relname"];$J[$Ni]["type"]=($K["indisprimary"]?"PRIMARY":($K["indisunique"]?"UNIQUE":"INDEX"));$J[$Ni]["columns"]=array();$J[$Ni]["descs"]=array();$J[$Ni]["algorithm"]=$K["amname"];$J[$Ni]["partial"]=$K["partial"];$Ne=preg_split('~(?<=\)), (?=\()~',$K["indexpr"]);foreach(explode(" ",$K["indkey"])as$Oe)$J[$Ni]["columns"][]=($Oe?$d[$Oe]:array_shift($Ne));foreach(explode(" ",$K["indoption"])as$Pe)$J[$Ni]["descs"][]=(intval($Pe)&1?'1':null);$J[$Ni]["opclasses"]=($K["opclasses"]!=""?explode(" ",$K["opclasses"]):array());$J[$Ni]["lengths"]=array();}return$J;}function
foreign_keys($S){$J=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, condeferred::int AS deferred, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($S)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$K){$K['deferrable']=($K['deferrable']?'':'NOT ').'DEFERRABLE'.($K['deferred']?' INITIALLY DEFERRED':'');if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$K['definition'],$_)){$K['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$_[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$_[2],$Sf)){$K['ns']=idf_unescape($Sf[2]);$K['table']=idf_unescape($Sf[4]);}$K['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$_[3])));$K['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$_[4],$Sf)?$Sf[1]:'NO ACTION');$K['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$_[4],$Sf)?$Sf[1]:'NO ACTION');$J[$K['conname']]=$K;}}return$J;}function
view($B){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($B).")")));}function
collations(){return
array();}function
information_schema($h,$M=""){$ok=array("information_schema","pg_catalog","pg_toast");if(connection()->flavor=='cockroach'){$ok[]="crdb_internal";$ok[]="pg_extension";}return
in_array($M!=""?$M:get_schema(),$ok);}function
error(){$J=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$J,$_))$J=$_[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($_[3]).'})(.*)~','\1<b>\2</b>',$_[2]).$_[4];return
nl_br($J);}function
create_database($h,$rb){return
queries("CREATE DATABASE ".idf_escape($h).($rb?" ENCODING ".idf_escape($rb):""));}function
drop_databases(array$g){connection()->close();return
apply_queries("DROP DATABASE",$g,'Adminer\idf_escape');}function
rename_database($B,$rb){connection()->close();return!!queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($B));}function
auto_increment(){return"";}function
alter_table($S,$B,array$k,array$Id,$wb,$Sc,$rb,$Ea,$Qh){$ua=array();$yi=array();if($S!=""&&$S!=$B)$yi[]="ALTER TABLE ".table($S)." RENAME TO ".table($B);$wj="";foreach($k
as$j){$c=idf_escape($j[0]);$X=$j[1];if(!$X)$ua[]="DROP $c";else{$Rl=$X[5];unset($X[5]);if($j[0]==""){if(isset($X[6]))$X[1]=($X[1]==" bigint"?" big":($X[1]==" smallint"?" small":" "))."serial";$ua[]=($S!=""?"ADD ":"  ").implode($X);if(isset($X[6]))$ua[]=($S!=""?"ADD":" ")." PRIMARY KEY ($X[0])";}else{if($c!=$X[0])$yi[]="ALTER TABLE ".table($B)." RENAME $c TO $X[0]";$ua[]="ALTER $c TYPE$X[1]";$xj=$S."_".idf_unescape($X[0])."_seq";$ua[]="ALTER $c ".($X[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) (STORED|VIRTUAL)~','EXPRESSION\1',$X[3]):(isset($X[6])?"SET DEFAULT nextval(".q($xj).")":"DROP DEFAULT"));if(isset($X[6]))$wj="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($xj)." OWNED BY ".idf_escape($S).".$X[0]";$ua[]="ALTER $c ".($X[2]==" NULL"?"DROP NOT":"SET").$X[2];}if($j[0]!=""||$Rl!="")$yi[]="COMMENT ON COLUMN ".table($B).".$X[0] IS ".($Rl!=""?substr($Rl,9):"''");}}$ua=array_merge($ua,$Id);if($S==""){$Q="";if($Qh){$nb=(connection()->flavor=='cockroach');$Q=" PARTITION BY $Qh[partition_by]($Qh[partition])";if($Qh["partition_by"]=='HASH'){$Rh=+$Qh["partitions"];for($q=0;$q<$Rh;$q++)$yi[]="CREATE TABLE ".idf_escape($B."_$q")." PARTITION OF ".idf_escape($B)." FOR VALUES WITH (MODULUS $Rh, REMAINDER $q)";}else{$pi="MINVALUE";foreach($Qh["partition_names"]as$q=>$X){$Y=$Qh["partition_values"][$q];$Mh=" VALUES ".($Qh["partition_by"]=='LIST'?"IN ($Y)":"FROM ($pi) TO ($Y)");if($nb)$Q
.=($q?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$X)?$X:idf_escape($X))."$Mh";else$yi[]="CREATE TABLE ".idf_escape($B."_$X")." PARTITION OF ".idf_escape($B)." FOR$Mh";$pi=$Y;}$Q
.=($nb?"\n)":"");}}array_unshift($yi,"CREATE TABLE ".table($B)." (\n".implode(",\n",$ua)."\n)$Q");}elseif($ua)array_unshift($yi,"ALTER TABLE ".table($S)."\n".implode(",\n",$ua));if($wj)array_unshift($yi,$wj);if($wb!==null)$yi[]="COMMENT ON TABLE ".table($B)." IS ".q($wb);foreach($yi
as$H){if(!queries($H))return
false;}if($Ea!=""){foreach(fields($B)as$sd=>$j){if($j["auto_increment"])return!!queries("SELECT setval(pg_get_serial_sequence(".q(table($B)).", ".q($sd)."), $Ea)");}}return
true;}function
alter_indexes($S,$ua){$Ob=array();$Fc=array();$yi=array();foreach($ua
as$X){if($X[0]!="INDEX")$Ob[]=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");elseif($X[2]=="DROP")$Fc[]=idf_escape($X[1]);else$yi[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($S."_"))." ON ".table($S).($X[3]?" USING $X[3]":"")." (".implode(", ",$X[2]).")".($X[4]?" WHERE $X[4]":"");}if($Ob)array_unshift($yi,"ALTER TABLE ".table($S).implode(",",$Ob));if($Fc)array_unshift($yi,"DROP INDEX ".implode(", ",$Fc));foreach($yi
as$H){if(!queries($H))return
false;}return
true;}function
truncate_tables(array$U){return!!queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$U)));}function
drop_kinds(array$U){$J=array("MATERIALIZED VIEW"=>array(),"VIEW"=>array(),"TABLE"=>array());foreach($U
as$B=>$T)$J[strtoupper($T["Engine"])][]=table($B);return
array_filter($J);}function
drop_views(array$Zl){return
drop_tables($Zl);}function
drop_tables(array$U){$dk=array();foreach($U
as$S)$dk[$S]=table_status1($S);foreach(drop_kinds($dk)as$tf=>$Fg){if(!queries("DROP $tf ".implode(", ",$Fg)))return
false;}return
true;}function
move_tables(array$U,array$Zl,$Gk){foreach(array_merge($U,$Zl)as$S){$Q=table_status1($S);if(!queries("ALTER ".strtoupper($Q["Engine"])." ".table($S)." SET SCHEMA ".idf_escape($Gk)))return
false;}return
true;}function
trigger($B,$S){if($B=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$d=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($S)." AND trigger_name = ".q($B);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$K)$d[]=$K["event_object_column"];$J=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$K){if($d&&$K["Event"]=="UPDATE")$K["Event"].=" OF";$K["Of"]=implode(", ",$d);if($J)$K["Event"].=" OR $J[Event]";$J=$K;}return$J;}function
triggers($S){$J=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($S))as$K){$hl=trigger($K["trigger_name"],$S);$J[$hl["Trigger"]]=array($hl["Timing"],$hl["Event"]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($B,$V){$C=routine_options($V);$sj=array_intersect_key(array("VOLATILITY"=>"CASE p.provolatile WHEN 'i' THEN 'IMMUTABLE' WHEN 's' THEN 'STABLE' ELSE 'VOLATILE' END","NULL_INPUT"=>"CASE WHEN p.proisstrict THEN 'RETURNS NULL ON NULL INPUT' ELSE 'CALLED ON NULL INPUT' END","SECURITY"=>"CASE WHEN p.prosecdef THEN 'SECURITY DEFINER' ELSE 'SECURITY INVOKER' END","PARALLEL"=>"CASE p.proparallel WHEN 's' THEN 'PARALLEL SAFE' WHEN 'r' THEN 'PARALLEL RESTRICTED' ELSE 'PARALLEL UNSAFE' END",),$C);foreach($sj
as$v=>$N)$sj[$v]="$N AS \"$v\"";$L=get_rows('SELECT r.routine_definition AS definition, LOWER(r.external_language) AS language, '.($sj?implode(', ',$sj).', ':'').'r.*
FROM information_schema.routines r
LEFT JOIN pg_catalog.pg_proc p ON p.oid::text = substring(r.specific_name, \'[0-9]+$\')
WHERE r.routine_schema = current_schema() AND r.specific_name = '.q($B));if(!$L)return
array();$J=$L[0];$J["options"]=array_intersect_key($J,$C);$J["returns"]=array("type"=>preg_replace('~^_(.*)~','\1[]',"$J[type_udt_name]"));$J["fields"]=get_rows("SELECT COALESCE(parameter_name, ordinal_position::text) AS field,
	CASE data_type WHEN 'USER-DEFINED' THEN udt_name WHEN 'ARRAY' THEN substr(udt_name, 2) || '[]' ELSE data_type END AS type,
	character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ".q($B)."
ORDER BY ordinal_position");return$J;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()'.(connection()->flavor=='cockroach'?'':"
AND substring(specific_name, '[0-9]+\$')::oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_proc'::regclass AND deptype = 'e')").'
ORDER BY SPECIFIC_NAME');}function
routine_languages(){$J=array();foreach(get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language")as$yf)$J[$yf]=(preg_match('~sql$~',$yf)?"pgsql":"txt");return$J;}function
routine_options($Yi){$nb=(connection()->flavor=='cockroach');$nj=($nb?array():array("SECURITY"=>array("SECURITY INVOKER","SECURITY DEFINER")));if($Yi=="PROCEDURE")return$nj;return
array("VOLATILITY"=>array("VOLATILE","STABLE","IMMUTABLE"),"NULL_INPUT"=>array("CALLED ON NULL INPUT","RETURNS NULL ON NULL INPUT"),)+$nj+($nb?array():array("PARALLEL"=>array("PARALLEL UNSAFE","PARALLEL RESTRICTED","PARALLEL SAFE"),));}function
routine_id($B,array$K){$J=array();foreach($K["fields"]as$j){$w=$j["length"];$J[]=$j["type"].($w?"($w)":"");}return
idf_escape($B)."(".implode(", ",$J).")";}function
last_id($I){$K=(is_object($I)?$I->fetch_row():array());return($K?$K[0]:0);}function
explain(Db$e,$H){return$e->query("EXPLAIN $H");}function
found_rows(array$T,array$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($T["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$Mi))return$Mi[1];}function
types($md=false){$nb=connection()->flavor=='cockroach';$uf=($nb?"'e'":"'b','c','d','e'".(min_version(9.2)?",'r'":""));return
get_key_vals("SELECT t.oid, t.typname
FROM pg_type t
WHERE t.typnamespace = ".driver()->nsOid."
AND t.typtype IN ($uf)".($nb?"
AND t.typelem = 0":"
AND (t.typrelid = 0 OR (SELECT c.relkind FROM pg_class c WHERE c.oid = t.typrelid) = 'c')"."
AND NOT EXISTS (SELECT 1 FROM pg_type e WHERE e.typarray = t.oid)".($md?'':"
AND t.oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_type'::regclass AND deptype = 'e')"))."
ORDER BY t.typname");}function
type_values($r){$Wc=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $r ORDER BY enumsortorder");return($Wc?"'".implode("', '",array_map('addslashes',$Wc))."'":"");}function
collation_name($bh){return(min_version(9.1)?"(SELECT collname FROM pg_collation WHERE oid = $bh AND collname != 'default')":"NULL");}function
type_definition($r){$V=first(get_rows("SELECT typtype, typisdefined::int AS defined, typrelid FROM pg_type WHERE oid = $r"));$J=array("kind"=>($V?$V["typtype"]:""),"definition"=>"");if(!$V||!$V["defined"])return$J;switch($J["kind"]){case'e':$Ul=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $r ORDER BY enumsortorder");$J["definition"]="AS ENUM (".implode(", ",array_map('Adminer\q',$Ul)).")";break;case'c':$d=array();foreach(get_rows("SELECT attname, format_type(atttypid, atttypmod) AS full_type, ".collation_name("attcollation")." AS collation
FROM pg_attribute
WHERE attrelid = $V[typrelid] AND attnum > 0 AND NOT attisdropped
ORDER BY attnum")as$K)$d[]=idf_escape($K["attname"])." $K[full_type]".($K["collation"]?" COLLATE ".idf_escape($K["collation"]):"");$J["definition"]="AS (\n\t".implode(",\n\t",$d)."\n)";break;case'd':$Cc=first(get_rows("SELECT format_type(typbasetype, typtypmod) AS base, typnotnull::int AS notnull, typdefault, ".collation_name("typcollation")." AS collation
FROM pg_type WHERE oid = $r"));$J["definition"]="AS $Cc[base]".($Cc["collation"]?" COLLATE ".idf_escape($Cc["collation"]):"").($Cc["typdefault"]!=""?" DEFAULT $Cc[typdefault]":"").($Cc["notnull"]?" NOT NULL":"");foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE contypid = $r AND contype != 'n' ORDER BY conname")as$K)$J["definition"].=" CONSTRAINT ".idf_escape($K["conname"])." $K[definition]";break;case'r':$Bi=first(get_rows("SELECT format_type(rngsubtype, NULL) AS subtype,
(SELECT opcname FROM pg_opclass WHERE oid = rngsubopc) AS subtype_opclass,
".collation_name("rngcollation")." AS collation,
NULLIF(rngcanonical, 0)::regproc::text AS canonical,
NULLIF(rngsubdiff, 0)::regproc::text AS subtype_diff".(min_version(14)?",
(SELECT typname FROM pg_type WHERE oid = rngmultitypid) AS multirange_type_name":"")."
FROM pg_range WHERE rngtypid = $r"));$C=array();foreach(array("subtype"=>0,"subtype_opclass"=>1,"collation"=>1,"canonical"=>0,"subtype_diff"=>0,"multirange_type_name"=>1)as$v=>$ad){if($Bi[$v]!="")$C[]=strtoupper($v)." = ".($ad?idf_escape($Bi[$v]):$Bi[$v]);}$J["definition"]="AS RANGE (".implode(", ",$C).")";}return$J;}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return(string)get_val("SELECT current_schema()");}function
set_schema($M,$f=null){$_GET["ns"]=$M;$J=get_val("SELECT set_config('search_path', ".q(idf_escape($M)).", false) FROM pg_namespace WHERE nspname = ".q($M),0,$f);driver()->setUserTypes(types(true));return!!$J;}function
drop_sql(array$U){$J="";foreach(drop_kinds($U)as$tf=>$Fg)$J
.="DROP $tf IF EXISTS ".implode(", ",$Fg).";\n";return($J?"$J\n":"");}function
foreign_keys_sql($S){$J="";$Ed=foreign_keys($S);ksort($Ed);foreach($Ed
as$Dd=>$Cd){$J
.="ALTER TABLE ONLY ".table($S)." ADD CONSTRAINT ".idf_escape($Dd)." ".preg_replace_callback('~( REFERENCES )([^(.]+)\(~',function(array$_){return$_[1].table(idf_unescape($_[2]))."(";},$Cd["definition"]).";\n";}return($J?"$J\n":$J);}function
indexes_sql($S,$ri=""){$J="";$H="SELECT indexdef, quote_ident(schemaname) || '.' || quote_ident(tablename) AS qualified, quote_ident(current_database()) AS db
FROM pg_catalog.pg_indexes
WHERE schemaname = current_schema() AND tablename = ".q($S).($ri!=""?" AND indexname != ".q($ri):"");foreach(get_rows($H,null,"-- ")as$K)$J
.="\n\n".str_replace(array(" $K[db].$K[qualified] USING "," $K[qualified] USING ")," ".table($S)." USING ",$K["indexdef"]).";";return$J;}function
create_sql($S,$Ea,$hk){$Ui=array();$zj=array();$_j=array();$yj=array();$Q=table_status1($S);if(is_view($Q)){$Yl=view($S);$Ob="CREATE ".strtoupper($Q["Engine"])." ".table($S)." AS ".rtrim($Yl["select"],";").";";return
rtrim($Ob.indexes_sql($S),';');}$k=fields($S);if(count($Q)<2||empty($k))return"";$J="CREATE TABLE ".table($Q['Name'])." (\n    ";$uk=q(table($Q['Name']));foreach($k
as$j){$Aj="";if($j['default']=="nextval('$Q[Name]_$j[field]_seq')"){$Aj=table("$Q[Name]_$j[field]_seq");$j['default']=null;$j['full_type']=preg_replace('~int(eger)?~','serial',$j['full_type']);}$Kh=idf_escape($j['field']).' '.$j['full_type'].preg_replace_callback('~(nextval\(\')([^.\']+)\'~',function(array$_){return$_[1].str_replace("'","''",table(idf_unescape($_[2])))."'";},default_value($j)).($j['null']?"":" NOT NULL");$Ui[]=$Kh;if(preg_match('~nextval\(\'([^\']+)\'\)~',$j['default'],$Tf)){$xj=$Tf[1];$Vj=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($xj)):"SELECT * FROM $xj"),null,"-- "));$wj=table(idf_unescape($xj));$zj[]=($hk=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $wj;\n":"")."CREATE SEQUENCE $wj INCREMENT $Vj[increment_by] MINVALUE $Vj[min_value] MAXVALUE $Vj[max_value]"." CACHE $Vj[cache_value];";if(get_val("SELECT pg_get_serial_sequence($uk, ".q($j['field']).")"))$_j[]="\n\nALTER SEQUENCE $wj OWNED BY ".table($Q['Name']).".".idf_escape($j['field']).";";if($Ea)$yj[]=$wj;}elseif($Ea&&$j['auto_increment']){$wj=($Aj?"":get_val("SELECT pg_get_serial_sequence($uk, ".q($j['field']).")::regclass"));$yj[]=($wj?table(idf_unescape($wj)):$Aj);}}if(!empty($zj))$J=implode("\n\n",$zj)."\n\n$J";$ri="";foreach(indexes($S)as$Le=>$t){if($t['type']=='PRIMARY'){$ri=$Le;$Ui[]="CONSTRAINT ".idf_escape($Le)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$t['columns'])).")";}}foreach(driver()->checkConstraints($S)as$Db=>$Eb)$Ui[]="CONSTRAINT ".idf_escape($Db)." CHECK ($Eb)";$J
.=implode(",\n    ",$Ui)."\n)";$Mh=driver()->partitionsInfo($Q['Name']);if($Mh)$J
.="\nPARTITION BY $Mh[partition_by]($Mh[partition])";$J
.=(min_version(12)?"":"\nWITH (oids = ".($Q['Oid']?'true':'false').")").";";$J
.=implode($_j);if($Q['Comment'])$J
.="\n\nCOMMENT ON TABLE ".table($Q['Name'])." IS ".q($Q['Comment']).";";foreach($k
as$sd=>$j){if($j['comment'])$J
.="\n\nCOMMENT ON COLUMN ".table($Q['Name']).".".idf_escape($sd)." IS ".q($j['comment']).";";}$J
.=indexes_sql($S,$ri);foreach(array_filter($yj)as$wj){$Vj=first(get_rows("SELECT last_value, is_called::int FROM $wj",null,"-- "));if($Vj['is_called'])$J
.="\n\nDO \$\$ BEGIN PERFORM setval(".q($wj).", $Vj[last_value]); END \$\$;";}return
rtrim($J,';');}function
truncate_sql($S){return"TRUNCATE ".table($S);}function
truncate_all_sql(array$U){return($U?"TRUNCATE ".implode(", ",array_map('Adminer\table',$U)).";\n\n":"");}function
trigger_sql($S){$Q=table_status1($S);$J="";foreach(triggers($S)as$gl=>$fl){$hl=trigger($gl,$Q['Name']);$J
.="\nCREATE TRIGGER ".idf_escape($hl['Trigger'])." $hl[Timing] $hl[Event] ON ".table($Q['Name'])." $hl[Type] $hl[Statement];;\n";}return$J;}function
use_sql($ac,$hk=""){$B=idf_escape($ac);$J="";if(preg_match('~CREATE~',$hk)){if($hk=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $B;\n";$J
.="CREATE DATABASE $B;\n";}return"$J\\connect $B";}function
use_schema_sql($M,$hk){$B=idf_escape($M);$J="";if(preg_match('~CREATE~',$hk)){if($hk=="DROP+CREATE")$J="DROP SCHEMA IF EXISTS $B CASCADE;\n";$J
.="CREATE SCHEMA IF NOT EXISTS $B;\n";}return$J."SET search_path TO $B";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field(array$j){if(preg_match('~^(geometry|geography)$~',$j["type"])&&strpos($j["full_type"],"[")===false)return"ST_AsEWKT(".idf_escape($j["field"]).")";}function
unconvert_field(array$j,$J){return($j["composite"]?"$J::$j[type]":$J);}function
support($qd){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|fast_status|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table'.'|transaction_ddl|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|deferrable').(connection()->flavor=='cockroach'||!min_version(9.1)?'':'|extension').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$qd);}function
kill_process($r){return
queries("SELECT pg_terminate_backend(".number($r).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+67e1c390")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($Ob=false){return
password_file($Ob);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($O){return
h($O);}function
database(){return
DB;}function
databases($Gd=true){return
get_databases($Gd);}function
pluginsLinks(){}function
operators($qk=null){return
driver()->operators($qk);}function
schemas(){$J=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$J))array_unshift($J,$_GET["ns"]);return$J;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Rb){return$Rb;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$ve=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$tj=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer".($ve!=""?" - $ve":""),'short_name'=>'Adminer','description'=>'Database management in a single PHP file','start_url'=>$tj,'scope'=>$tj,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+67e1c390",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($Wb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$wg){$l="adminer$wg.css";if(file_exists($l)){$wd=file_get_contents($l);$J["$l?v=".crc32($wd)]=($wg?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$wd)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.'System'.'<td>',input_hidden("auth[driver]","pgsql")."PostgreSQL"),adminer()->loginFormField('server','<tr><th>'.'Server'.'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".'hostname[:port] or :socket'."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.'Username'.'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.'Password'.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.'Database'.'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
loginFormField($B,$oe,$Y){return$oe.$Y."\n";}function
login($Pf,$F){if($F=="")return'Adminer does not support accessing a database without a password.'.require_password_link(null);if(!Driver::$passwords)return'The database does not support passwords.'.require_password_link($F);if(!password_required())return'The server accepts any password, so filling it in protects nothing.'.require_password_link($F);return
true;}function
tableName(array$qk){return
h($qk["Name"]);}function
fieldName(array$j,$D=0){$V=$j["full_type"].($j["null"]?" NULL":"");$wb=$j["comment"];return'<span title="'.h($V.($wb!=""?($V?": ":"").$wb:'')).'">'.h($j["field"]).'</span>';}function
commentValue($V,$wb){if($wb==""||$V=='TABLE'||$V=='COLUMN')return
h($wb);$mi=function($fj,$Za='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$Za>",preg_replace('~\|$~m',"",rtrim($fj))));};$S='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$S?$K$S?($K*)$S?~m",function($_)use($mi){return"<table>\n".($_[1]?"<thead>".$mi($_[2],'th')."<tbody>\n":$mi($_[2])).$mi($_[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($wb))))."</pre>\n";}function
commentInput($V,$b,$wb){$Y=h($wb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($V=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$qk,$P=""){$B=$qk["Name"];echo'<p class="links">';$Lf=array();if($B!="")$Lf["select"]='Select data';if(support("table")||support("indexes"))$Lf["table"]='Show structure';$jf=false;if(support("table")){$jf=is_view($qk);if($jf){if(support("view"))$Lf["view"]='Alter view';}elseif(function_exists('Adminer\alter_table')&&$B!="")$Lf["create"]='Alter table';}if($P!==null)$Lf["edit"]='New item';foreach($Lf
as$v=>$X)echo" <a href='".h(ME)."$v=".url_escape($B).($v=="edit"?$P:"")."'".bold(isset($_GET[$v])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($B,$jf)),"?"),"\n";}function
foreignKeys($S){return
foreign_keys($S);}function
backwardKeys($S,$pk){return
array();}function
backwardKeysPrint(array$Ja,array$K){}function
selectQuery($H,$bk,$od=false){$J="\n";if(!$od&&($cm=driver()->warnings())){$r="warnings";$J=", <a href='#$r' class='toggle'>".'Warnings'."</a>"."$J<div id='$r' class='hidden'>\n$cm</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($bk).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($H)."' class='hover'>".'Edit'."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
explain(Db$e,$H,array$vh){$I=explain($e,$H);if(!$I)return"";ob_start();print_select_result($I,$e,$vh);return
ob_get_clean();}function
rowDescription($S){return"";}function
rowDescriptions(array$L,array$Jd){return$L;}function
selectLink($X,array$j){}function
selectVal($X,$y,array$j,$Ah){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$j["type"])&&!preg_match("~var~",$j["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$j["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($j)&&!is_utf8($X))$J="<i>".lang_format(array('%d byte','%d bytes'),strlen($Ah))."</i>";return($y?"<a href='".h($y)."'".(is_url($y)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$j){return$X;}function
config(){return
array();}function
tableStructurePrint(array$k,$qk=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".'Column'."<th>".'Type'.(support("comment")?"<th>".'Comment':"")."<tbody>\n";$Ml=(support("type")?types():array());foreach($k
as$j){echo"<tr><th>".h($j["field"]);$V=h($j["full_type"]);$rb=h($j["collation"]);echo"<td><span title='$rb'>".(in_array($V,$Ml)?"<a href='".h(ME.'type='.url_escape($V))."'>$V</a>":$V.($rb&&isset($qk["Collation"])&&$rb!=$qk["Collation"]?" $rb":""))."</span>",($j["null"]?" <i>NULL</i>":""),($j["auto_increment"]?" <i>".'Auto Increment'."</i>":""),(isset($j["default"])?" <span title='".'Default value'."'>[<b>".($j["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($j["default"])),80,"</code>"):h($j["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$j["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$u,array$qk){$Lh=false;foreach($u
as$B=>$t)$Lh|=!!$t["partial"];echo"<table>\n";$hc=first(driver()->indexAlgorithms($qk));foreach($u
as$B=>$t){ksort($t["columns"]);$si=array();foreach($t["columns"]as$v=>$X)$si[]="<i>".h($X)."</i>".($t["lengths"][$v]?"(".h($t["lengths"][$v]).")":"").($t["descs"][$v]?" DESC":"");echo"<tr title='".h($B)."'>","<th>".h($t["type"]).($hc&&$t['algorithm']!=$hc?" (".h($t['algorithm']).")":""),"<td>".implode(", ",$si);if($Lh)echo"<td>".($t['partial']?"<code class='jush-".JUSH."'>WHERE ".h($t['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$N,array$d){print_fieldset("select",'Select',$N);$q=0;$N[""]=array();foreach($N
as$v=>$X){$X=idx($_GET["columns"],$v,array());$c=select_input(" name='columns[$q][col]' data-default=''".on('change',($v!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$q][fun]",array(-1=>"")+array_filter(array('Functions'=>driver()->functions,'Aggregation'=>driver()->grouping)),$X["fun"]," data-default=''".on('change',($v!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$q++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$u,$qk=null){print_fieldset("search",'Search',$Z);foreach($u
as$q=>$t){if($t["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$t["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$q]' value='".h(idx($_GET["fulltext"],$q))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$q]",1,isset($_GET["boolean"][$q]),"BOOL"):''),"</div>\n";}$nh=adminer()->operators($qk);foreach(array_merge((array)$_GET["where"],array(array()))as$q=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],$nh)))echo"<div>".select_input(" name='where[$q][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".'anywhere'.")"),html_select("where[$q][op]",$nh,$X["op"]," data-default='".h(first($nh))."'".on('change','selectFirstChange')),"<input type='search' name='where[$q][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$D,array$d,array$u){print_fieldset("sort",'Sort',$D);$q=0;foreach((array)$_GET["order"]as$v=>$X){if($X!=""){echo"<div>".select_input(" name='order[$q]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$q]",1,isset($_GET["desc"][$v]),'descending')."</div>\n";$q++;}}echo"<div>".select_input(" name='order[$q]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$q]",1,false,'descending')."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($x){echo"<fieldset><legend>".'Limit'."</legend><div>","<input type='number' name='limit' class='size' value='".h($x?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($Nk){echo"<fieldset><legend>".'Text length'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Nk)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$u){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>","<script".nonce().">\n","const indexColumns = ";$d=array();foreach($u
as$t){$Vb=reset($t["columns"]);if($t["type"]!="FULLTEXT"&&$Vb)$d[$Vb]=1;}$d[""]=1;foreach($d
as$v=>$X)json_row($v);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Pc,array$d){}function
selectColumnsProcess(array$d,array$u){$N=array();$p=array();foreach((array)$_GET["columns"]as$v=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$N[$v]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$p[]=$N[$v];}}return
array($N,$p);}function
selectSearchProcess(array$k,array$u,$qk=null){$J=array();foreach($u
as$q=>$t){if($t["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$q)!="")$J[]=driver()->fulltextSql($q,$t,$_GET["fulltext"][$q],isset($_GET["boolean"][$q]));}$nh=adminer()->operators($qk);foreach((array)$_GET["where"]as$v=>$X){$X+=array("col"=>"","op"=>first($nh),"val"=>"");$_GET["where"][$v]=$X;$pb=$X["col"];if("$pb$X[val]"!=""&&in_array($X["op"],$nh)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Ab=array();foreach(($pb!=""?array($pb=>$k[$pb]):$k)as$B=>$j){$ni="";$_b=" $X[op]";if(preg_match('~IN$~',$X["op"]))$_b
.=" ".($X["val"]!=""?process_in($X["val"]):"(NULL)");elseif($X["op"]=="SQL")$_b=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$_))$_b=" $_[1] ".q("%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$ni="$X[op](".q($X["val"]).", ";$_b=")";}elseif(!preg_match('~NULL$~',$X["op"]))$_b
.=" ".q($X["val"]);if($pb!=""||is_searchable($j,$X))$Ab[]=$ni.driver()->convertSearch(idf_escape($B),$X,$j).$_b;}$J[]=(count($Ab)==1?$Ab[0]:($Ab?"(".implode(" OR ",$Ab).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$k,array$u){$J=array();foreach((array)$_GET["order"]as$v=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$v])?" DESC".(JUSH=='pgsql'&&idx($k[$X],"null")?" NULLS LAST":""):"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Jd){return
false;}function
selectQueryBuild(array$N,array$Z,array$p,array$D,$x,$E){return"";}function
messageQuery($H,$Pk,$od=false){restart_session();$se=&get_session("queries");if(!idx($se,$_GET["db"]))$se[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$se[$_GET["db"]][]=array($H,time(),$Pk);$Yj="sql-".count($se[$_GET["db"]]);$J="<a href='#$Yj' class='toggle'>".'SQL command'."</a> ".copy_icon()."\n";if(!$od&&($cm=driver()->warnings())){$r="warnings-".count($se[$_GET["db"]]);$J="<a href='#$r' class='toggle'>".'Warnings'."</a>, $J<div id='$r' class='hidden'>\n$cm</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Yj' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($Pk?" <span class='time'>($Pk)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($se[$_GET["db"]])-1)).'">'.'Edit'.'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($S,array$k,$K,$Al,$H='',$Pk=''){echo($H!=""?"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>($Pk)</span>\n":"");}function
editFunctions(array$j){$J=($j["null"]?"NULL/":"");$ke=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$v=>$Td){if(!$v||(!isset($_GET["call"])&&$ke)){foreach($Td
as$Yh=>$X){if(!$Yh||preg_match("~$Yh~",$j["type"]))$J
.="/$X";}}if($v&&$Td&&!preg_match('~set|bool~',$j["type"])&&!is_blob($j))$J
.="/SQL";}if($j["auto_increment"]&&!$ke)$J='Auto Increment';return
explode("/",$J);}function
editInput($S,array$j,$b,$Y){if($j["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".'original'."</i></label> ":"").enum_input("radio",$b,$j,$Y,"NULL");return"";}function
editHint($S,array$j,$Y){return"";}function
processInput(array$j,$Y,$o=""){if($o=="SQL")return$Y;$B=$j["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$o))$J="$o()";elseif(preg_match('~^current_(date|timestamp)$~',$o))$J=$o;elseif(preg_match('~^([+-]|\|\|)$~',$o))$J=idf_escape($B)." $o $J";elseif(preg_match('~^[+-] interval$~',$o))$J=idf_escape($B)." $o ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$o))$J="$o(".idf_escape($B).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$o))$J="$o($J)";return
unconvert_field($j,$J);}function
dumpOutput(){$J=array('text'=>'open','file'=>'save');if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($h){}function
dumpTable($S,$hk,$jf=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($hk)dump_csv(array_keys(fields($S)));}else{if($jf==2){$k=array();foreach(fields($S)as$B=>$j)$k[]=idf_escape($B)." $j[full_type]";$Ob="CREATE TABLE ".table($S)." (".implode(", ",$k).")";}else$Ob=create_sql($S,$_POST["auto_increment"],$hk);set_utf8mb4($Ob);if($hk&&$Ob){if(($hk=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$jf==1)echo"DROP ".($jf==2?"VIEW":"TABLE")." IF EXISTS ".table($S).";\n";if($jf==1)$Ob=remove_definer($Ob);echo"$Ob;\n\n";}}}function
dumpData($S,$hk,$H,array$N=array(),array$Z=array(),array$p=array(),array$D=array()){if($hk){$Zf=(JUSH=="sqlite"?0:1048576);$k=array();$_e=false;if($_POST["format"]=="sql"){if($hk=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($S).";\n";$k=fields($S);if(JUSH=="mssql"){foreach($k
as$j){if($j["auto_increment"]){echo"SET IDENTITY_INSERT ".table($S)." ON;\n";$_e=true;break;}}}}$I=($H!=""?connection()->query($H,1):driver()->select($S,($N?:array("*")),$Z,$p,$D,0));if($I){$We="";$Ua="";$qf=array();$Ud=array();$jk="";$rd=($S!=''?'fetch_assoc':'fetch_row');$Nb=0;while($K=$I->$rd()){if(!$qf){$Ul=array();foreach($K
as$X){$j=$I->fetch_field();if(idx($k[$j->name],'generated')){$Ud[$j->name]=true;continue;}$qf[]=$j->name;$v=idf_escape($j->name);$Ul[]="$v = VALUES($v)";}$jk=($hk=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Ul):"").";\n";}if($_POST["format"]!="sql"){if($hk=="table"){dump_csv($qf);$hk="INSERT";}dump_csv($K);}else{if(!$We)$We="INSERT INTO ".table($S)." (".implode(", ",array_map('Adminer\idf_escape',$qf)).") VALUES";foreach($K
as$v=>$X){if($Ud[$v]){unset($K[$v]);continue;}$j=$k[$v];$K[$v]=($X===null?"NULL":($X===false?0:unconvert_field($j,preg_match(number_type(),$j["type"])&&!preg_match('~\[~',$j["full_type"])&&is_numeric($X)?$X:(!is_blob($j)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$fj=($Zf?"\n":" ")."(".implode(",\t",$K).")";if(!$Ua)$Ua=$We.$fj;elseif(JUSH=='mssql'?$Nb%1000!=0:strlen($Ua)+4+strlen($fj)+strlen($jk)<$Zf)$Ua
.=",$fj";else{echo$Ua.$jk;$Ua=$We.$fj;}}$Nb++;}if($Ua)echo$Ua.$jk;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($_e)echo"SET IDENTITY_INSERT ".table($S)." OFF;\n";}}function
dumpFilename($ze){return
friendly_url($ze!=""?$ze:(SERVER?:"localhost"));}function
dumpHeaders($ze,$Ag=false){$Dh=$_POST["output"];$kd=(preg_match('~sql~',$_POST["format"])?"sql":($Ag?"tar":"csv"));header("Content-Type: ".($Dh=="gz"?"application/x-gzip":($kd=="tar"?"application/x-tar":($kd=="sql"||$Dh!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Dh=="gz"){ob_start(function($R){return
gzencode($R);},1e6);}return$kd;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.'Alter database'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'Alter schema':'Create schema')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'Privileges'."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".'Routines'."</a>\n":""),(support("sequence")?"<a href='#sequences'>".'Sequences'."</a>\n":""),(support("type")?"<a href='#user-types'>".'User types'."</a>\n":""),(support("event")?"<a href='#events'>".'Events'."</a>\n":"");return
true;}function
navigation($vg){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Og=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Og)<0?h($Og):"").version_iframe()."</a>","</span></h1>\n";if($vg=="auth"){$Dh="";foreach((array)$_SESSION["pwds"]as$Wl=>$Gj){foreach($Gj
as$O=>$Ol){$B=h(get_setting("vendor-$Wl-$O")?:get_driver($Wl));foreach($Ol
as$Nl=>$F){if($B&&$F!==null){$ec=$_SESSION["db"][$Wl][$O][$Nl];foreach(($ec?array_keys($ec):array(""))as$h)$Dh
.="<li><a href='".h(auth_url($Wl,$O,$Nl,$h))."'>($B) ".h("$Nl@").($O!=""?adminer()->serverName($O):"").h($h!=""?" - $h":"")."</a>\n";}}}}if($Dh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Dh</ul>\n";}else{$U=array();if($_GET["ns"]!==""&&!$vg&&DB!=""){connection()->select_db(DB);$U=table_status('',true);}adminer()->syntaxHighlighting($U);adminer()->databasesPrint($vg);$ga=array();if(DB==""||!$vg){if(support("sql")){$ga['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".'SQL command'."</a>";$ga['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".'Import'."</a>";}$ga['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'Export'."</a>";}$Fe=$_GET["ns"]!==""&&!$vg&&DB!="";if($Fe&&function_exists('Adminer\alter_table'))$ga['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'Create table'."</a>";$ga=adminer()->menuActions($ga,$vg);echo($ga?"<p class='links'>\n".implode("\n",$ga)."\n":"");if($Fe){if($U)adminer()->tablesPrint($U);else
echo"<p class='message'>".'No tables.'."</p>\n";}}}function
syntaxHighlighting(array$U){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.1.0+67e1c390",true);$yg=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($yg?script("addEventListener('DOMContentLoaded', () => {\n$yg\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($U){$Lf=array();foreach($U
as$S=>$V)$Lf[]=js_escape_re($S);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Lf).')(?!\$)\b/g',false);$Zj=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($Zj))){foreach(routines()as$K)json_row(js_escape(ME).'function='.url_escape($K["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($K["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$ck=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Fa=Driver::jushAutocomplete($U,$ck);echo($Fa?"addEventListener('DOMContentLoaded', () => { autocompleter = $Fa; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($vg){if(support("single_db"))return;$g=adminer()->databases();if(DB&&$g&&!in_array(DB,$g))array_unshift($g,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$bc=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".'Database'."'>".'DB'.": ".($g?html_select("db",array(""=>"")+$g,DB,$bc):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".'Use'."'".($g?" class='hidden'":"").">\n";if(support("scheme")){if($vg!="db"&&DB!=""&&connection()->select_db(DB)){echo"<br><label>".'Schema'.": ".html_select("ns",array(""=>"")+adminer()->schemas(),$_GET["ns"],$bc)."</label>";if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ga,$vg){return$ga;}function
tablesPrint(array$U){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($U
as$S=>$Q){$S="$S";$B=adminer()->tableName($Q);if($B!=""&&!$Q["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($S).'"'.bold($_GET["select"]==$S||$_GET["edit"]==$S,"select hover")." title='".'Select data'."'>".'select'."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($S).'"'.bold(in_array($S,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($Q)?"view":"structure"))." title='".'Show structure'."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($r){return
kill_process($r);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($fi){$Ec=SqlDriver::$drivers;$qe=" href='https://www.adminer.org/plugins/#use'".target_blank();if($fi===null){$fi=array();$Na="adminer-plugins";if(is_dir($Na)){foreach(glob("$Na/*.php")as$l){$xd=SqlDriver::$drivers;$this->includeOnce($l);foreach(array_diff_key(SqlDriver::$drivers,$xd)as$r=>$B)$this->driverFiles[$r]=$l;}}if(file_exists("$Na.php")){$He=$this->includeOnce("$Na.php");if(is_array($He)){foreach($He
as$v=>$ci)$fi[is_object($ci)?get_class($ci):$v]=$ci;}else$this->error
.=sprintf('%s must <a%s>return an array</a>.',"<b>$Na.php</b>",$qe)."<br>";}foreach(get_declared_classes()as$mb){if(!$fi[$mb]&&(preg_match('~^Adminer\w~i',$mb)||is_subclass_of($mb,'Adminer\Plugin'))){$Ii=new
\ReflectionClass($mb);$Fb=$Ii->getConstructor();if($Fb&&$Fb->getNumberOfRequiredParameters())$this->error
.=sprintf('<a%s>Configure</a> %s in %s.',$qe,"<b>$mb</b>","<b>$Na.php</b>")."<br>";else$fi[$mb]=new$mb;}}}$af=array_filter($fi,function($ci){return!is_object($ci);});if($af){$this->error
.=sprintf('Every plugin must <a%s>be an object</a>.',$qe)."<br>";$fi=array_diff_key($fi,$af);}$this->drivers=array_diff_key(SqlDriver::$drivers,$Ec);$this->plugins=$fi;$ja=new
Adminer;$fi[]=$ja;$Ii=new
\ReflectionObject($ja);foreach($Ii->getMethods()as$sg){foreach($fi
as$ci){$B=$sg->getName();if(method_exists($ci,$B))$this->hooks[$B][]=$ci;}}}function
includeOnce($l){return
include_once"./$l";}static
function
checksum($l){$wd=str_replace("\r","",file_get_contents($l));$wd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$wd);return
dechex(crc32($wd));}function
checksums(){$yd=array_values($this->driverFiles);foreach($this->plugins
as$ci){$Ii=new
\ReflectionObject($ci);$yd[]=$Ii->getFileName();}$J=array();foreach($yd
as$l)$J[basename($l,'.php')]=self::checksum($l);return$J;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'ed1ef78f','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'c78461b3','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'b366423e','elastic'=>'4eb1abb2','firebird'=>'f97b3387','igdb'=>'377e2c72','imap'=>'8c17cac','mongo'=>'978b30a6','redis'=>'8f9ea81e','simpledb'=>'88d32277',);}function
__call($B,array$Ih){$za=array();foreach($Ih
as$v=>$X)$za[]=&$Ih[$v];$J=null;foreach($this->hooks[$B]as$ci){$Y=call_user_func_array(array($ci,$B),$za);if($Y!==null){if(!self::$append[$B])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($s,$Vg=null){$za=func_get_args();$za[0]=idx($this->translations[LANG],$s)?:$s;return
call_user_func_array('Adminer\lang_format',$za);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($Uh){$this->password_hash=$Uh;}function
description(){return'Require a password verified by Adminer';}function
credentials(){$F=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($F)&&!password_required()?"":$F));}function
login($Pf,$F){if($this->passwordMatches($F))return
true;}protected
function
passwordMatches($F){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($F),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($Sk,$i="",$Ta=array(),$Tk="",$Rg=false){if($Rg){header("HTTP/1.1 404 Not Found");$i=($i?:'Not found.');}page_headers();if(is_ajax()&&$i){page_messages($i);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Uk=$Sk.($Tk!=""?": $Tk":"");$Vk=strip_tags($Uk.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'en\' dir=\'ltr\' class=\'ltr nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Vk,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.1.0+67e1c390"),'">
';$Sb=adminer()->css();if(is_int(key($Sb)))$Sb=array_fill_keys($Sb,'light');$he=in_array('light',$Sb)||in_array('',$Sb);$fe=in_array('dark',$Sb)||in_array('',$Sb);$Wb=($he?($fe?null:false):($fe?:null));$ig=" media='(prefers-color-scheme: dark)'";if($Wb!==false)echo"<link rel='stylesheet'".($Wb?"":$ig)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.1.0+67e1c390")."'>\n";echo"<meta name='color-scheme' content='".($Wb===null?"light dark":($Wb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.1.0+67e1c390");if(adminer()->head($Wb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+67e1c390")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Sb
as$Fl=>$wg){$b=($wg=='dark'&&!$Wb?$ig:($wg=='light'&&$fe?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($Fl)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape('You are offline.')."';
const numberFormat = '".js_escape('#,##0')."';
const numberDigits = '".js_escape('0123456789')."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".'Menu'."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Ta!==null){$y=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($y?:".").'">'.get_driver(DRIVER).'</a> » ';$y=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$O=adminer()->serverName(SERVER);$O=($O!=""?$O:'Server');if($Ta===false)echo"$O\n";else{echo"<a href='".h($y.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$O</a> » ";$mj="";if(is_string($Ta)){$mj=$Ta;$Ta=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($Ta))){$cc="$y&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($cc.($_GET["ns"]==""?$mj:"")).'">'.h(DB).'</a> » ';}if(is_array($Ta)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$mj).'">'.h($_GET["ns"]).'</a> » ';foreach($Ta
as$v=>$X){$nc=(is_array($X)?$X[1]:h($X));if($nc!="")echo"<a href='".h(ME."$v=").url_escape(is_array($X)?$X[0]:$X)."'>$nc</a> » ";}}echo"$Sk\n";}}echo"<h2>$Uk</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($i);adminer()->serviceWorker();$g=&get_session("dbs");if(DB!=""&&$g&&!in_array(DB,$g,true))$g=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($Rg){page_footer($Rg===true?"":$Rg);exit;}}function
service_worker(){$Li=has_passwords();$ob=($Li?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.1.0+67e1c390")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$ob\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$Gj){foreach($Gj
as$Ol){foreach($Ol
as$F){if($F!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Rb){$me=array();foreach($Rb
as$v=>$X)$me[]="$v $X";header("Content-Security-Policy: ".implode("; ",$me));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$Kl=array();foreach(array_keys(adminer()->css())as$Fl)$Kl[preg_replace('~\?.*~','',$Fl)]=true;$J=array();foreach(array("adminer.css","adminer-dark.css")as$l){if($Kl[$l]&&file_exists($l)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($l),$_);$J[$l]=array((string)$_[1],Plugins::checksum($l));}}return$J;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'216f097b','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Qg;if(!$Qg)$Qg=base64_encode(rand_string());return$Qg;}function
page_messages($i){$El=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$og=idx($_SESSION["messages"],$El);if($og){echo"<div class='message'>".implode("</div>\n<div class='message'>",$og)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$El]);}if($i)echo"<div class='error'>$i</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($vg=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($vg);echo"</div>\n";if($vg!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="Username">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'Logout\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Cg){while($Cg>=2147483648)$Cg-=4294967296;while($Cg<=-2147483649)$Cg+=4294967296;return(int)$Cg;}function
long2str(array$W,$bm){$fj='';foreach($W
as$X)$fj
.=pack('V',$X);if($bm)return
substr($fj,0,end($W));return$fj;}function
str2long($fj,$bm){$W=array_values(unpack('V*',str_pad($fj,4*ceil(strlen($fj)/4),"\0")));if($bm)$W[]=strlen($fj);return$W;}function
xxtea_mx($lm,$km,$kk,$of){return
int32((($lm>>5&0x7FFFFFF)^$km<<2)+(($km>>3&0x1FFFFFFF)^$lm<<4))^int32(($kk^$km)+($of^$lm));}function
encrypt_string($ek,$v){if($ek=="")return"";$v=array_values(unpack("V*",pack("H*",md5($v))));$W=str2long($ek,true);$Cg=count($W)-1;$lm=$W[$Cg];$km=$W[0];$xi=floor(6+52/($Cg+1));$kk=0;while($xi-->0){$kk=int32($kk+0x9E3779B9);$Jc=$kk>>2&3;for($Eh=0;$Eh<$Cg;$Eh++){$km=$W[$Eh+1];$Bg=xxtea_mx($lm,$km,$kk,$v[$Eh&3^$Jc]);$lm=int32($W[$Eh]+$Bg);$W[$Eh]=$lm;}$km=$W[0];$Bg=xxtea_mx($lm,$km,$kk,$v[$Eh&3^$Jc]);$lm=int32($W[$Cg]+$Bg);$W[$Cg]=$lm;}return
long2str($W,false);}function
decrypt_string($ek,$v){if($ek=="")return"";if(!$v)return
false;$v=array_values(unpack("V*",pack("H*",md5($v))));$W=str2long($ek,false);$Cg=count($W)-1;$lm=$W[$Cg];$km=$W[0];$xi=floor(6+52/($Cg+1));$kk=int32($xi*0x9E3779B9);while($kk){$Jc=$kk>>2&3;for($Eh=$Cg;$Eh>0;$Eh--){$lm=$W[$Eh-1];$Bg=xxtea_mx($lm,$km,$kk,$v[$Eh&3^$Jc]);$km=int32($W[$Eh]-$Bg);$W[$Eh]=$km;}$lm=$W[$Cg];$Bg=xxtea_mx($lm,$km,$kk,$v[$Eh&3^$Jc]);$km=int32($W[0]-$Bg);$W[0]=$km;$kk=int32($kk-0x9E3779B9);}return
long2str($W,true);}$ai=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($v)=explode(":",$X);$ai[$v]=$X;}}function
add_invalid_login(){$La=get_temp_dir()."/adminer-invalid";foreach(glob("$La*")?:array($La)as$l){$n=file_open_lock($l);if($n)break;}if(!$n)$n=file_open_lock("$La-".rand_string());if(!$n)return;$cf=json_decode(stream_get_contents($n),true);$Pk=time();if($cf){foreach($cf
as$df=>$X){if($X[0]<$Pk)unset($cf[$df]);}}$af=&$cf[adminer()->bruteForceKey()];if(!$af)$af=array($Pk+30*60,0);$af[1]++;file_write_unlock($n,json_encode($cf));}function
check_invalid_login(array&$ai){$cf=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$l){$n=file_open_lock($l);if($n){$cf=json_decode(stream_get_contents($n),true);file_unlock($n);break;}}$v=adminer()->bruteForceKey();$af=idx($cf,$v,array());$Pg=($af[1]>29?$af[0]-time():0);if($Pg>0){$i=lang_format(array('Too many unsuccessful logins, try again in %d minute.','Too many unsuccessful logins, try again in %d minutes.'),ceil($Pg/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$v==$_SERVER["REMOTE_ADDR"])$i
.='<br>'.sprintf('Use the %s <a%s>plugin</a> if Adminer runs behind a reverse proxy.','<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($i,$ai,false);}}function
password_required(){static$J;if($J===null){$J=(bool)get_session("password_required");if(!$J){$Qb=adminer()->credentials();$J=!is_object(Driver::connect($Qb[0],$Qb[1],""));if($J)set_session("password_required",true);}}return$J;}function
require_password_link($F){$zg="<a href='https://www.adminer.org/password/'".target_blank().">".'More options'."</a>";if(!function_exists('password_hash'))return" $zg";$di=($F!==null?$F:base64_encode(substr(pack("H*",rand_string()),0,12)));$le=password_hash($di,PASSWORD_DEFAULT);$l="adminer-plugins.php";$gd=file_exists("adminer-plugins.php");if($gd)$Ze=($F!==null?sprintf('Add this line to %s to require the entered password:',"<b>$l</b>"):sprintf('Add this line to %s to require the password %s:',"<b>$l</b>","<b>$di</b>"));else{$l="<button name='password_less' value='".h($le)."' class='link'>$l</button>";$Ze=($F!==null?sprintf('Save %s next to Adminer to require the entered password:',$l):sprintf('Save %s next to Adminer to require the password %s:',$l,"<b>$di</b>"));}$Jf="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($le)."'</span>),";$J="<p>$Ze
<pre><code class='jush'>".($gd?$Jf:"&lt;?php\n<a>return</a> <a>array</a>(\n$Jf\n);")."</code></pre>
<p>$zg
";return" <a href='#password-less' class='toggle'>".'Require a password.'."</a>
<div id='password-less' class='hidden'>".($gd?$J:"<form action='' method='post'>\n".$J.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Da=$_POST["auth"];if($Da&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$Wl=$Da["driver"];$O=$Da["server"];$Nl=$Da["username"];$F=(string)$Da["password"];$h=$Da["db"];set_password($Wl,$O,$Nl,$F);$_SESSION["db"][$Wl][$O][$Nl][$h]=true;if($Da["permanent"]){$v=implode("-",array_map('base64_encode',array($Wl,$O,$Nl,$h)));$ti=adminer()->permanentLogin(true);$ai[$v]="$v:".base64_encode($ti?encrypt_string($F,$ti):"");cookie("adminer_permanent",implode(" ",$ai));}if(!array_diff(array_keys($_POST),array("auth","token"))||$Wl!=DRIVER||$O!=SERVER||$Nl!==$_GET["username"]||$h!=DB)redirect(auth_url($Wl,$O,$Nl,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$v)set_session($v,null);unset_permanent($ai);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.'.' '.'Thanks for using Adminer. Consider <a href="https://www.adminer.org/en/donation/">donating</a>.');}elseif($ai&&!$_SESSION["pwds"]){session_regenerate_id();$ti=adminer()->permanentLogin();foreach($ai
as$v=>$X){list(,$lb)=explode(":",$X);list($Wl,$O,$Nl,$h)=array_map('base64_decode',explode("-",$v));set_password($Wl,$O,$Nl,decrypt_string(base64_decode($lb),$ti));$_SESSION["db"][$Wl][$O][$Nl][$h]=true;}}function
unset_permanent(array&$ai){foreach($ai
as$v=>$X){list($Wl,$O,$Nl,$h)=array_map('base64_decode',explode("-",$v));if($Wl==DRIVER&&$O==SERVER&&$Nl==$_GET["username"]&&$h==DB)unset($ai[$v]);}cookie("adminer_permanent",implode(" ",$ai));}function
auth_error($i,array&$ai,$bf=true){$Hj=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Hj]||$_GET[$Hj])&&!$_SESSION["token"])$i='Session expired. Please log in again.';elseif($bf&&($F=get_password())!==null){restart_session();add_invalid_login();if($F===false)$i
.=($i?'<br>':'').sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> the %s method to make it permanent.',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($ai);}}if(!$_COOKIE[$Hj]&&$_GET[$Hj]&&ini_bool("session.use_only_cookies"))$i='Session support must be enabled.';$Ih=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Ih["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header('Login',$i,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".'The action will be performed after successful login with the same credentials.'."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($ai);page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($ai);$Qb=adminer()->credentials();$e=Driver::connect($Qb[0],$Qb[1],$Qb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Pf=null;if(!is_object($e)||($Pf=adminer()->login($_GET["username"],get_password()))!==true){$i=(is_string($e)?nl_br(h($e)):(is_string($Pf)?$Pf:'Invalid credentials.')).(preg_match('~^ | $~',get_password())?'<br>'.'There is a space in the entered password, which might be the cause.':'');auth_error($i,$ai);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header('Logout','Invalid CSRF token. Submit the form again.');page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Da&&$_POST["token"])$_POST["token"]=get_token();$i='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$i='Invalid CSRF token. Submit the form again.'.' '.'If you did not send this request from Adminer, close this page.';}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$i=sprintf('The POST data is too large. Reduce the data or increase the %s configuration directive.',"<b>post_max_size</b>");if(isset($_GET["sql"]))$i
.=' '.'You can upload a large SQL file via FTP and import it from the server.';}function
print_select_result($I,$f=null,array$vh=array(),&$x=0,&$Kc=false){$Lf=array();$u=array();$d=array();$U=array();$ri=array();$Mc=array();$rl=array();$J=array();$xg=$Kc;$Kc=false;for($q=0;(!$x||$q<$x)&&($K=$I->fetch_row());$q++){if(!$q){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($xg?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($lf=0;$lf<count($K);$lf++){$j=$I->fetch_field();$B=$j->name;$S=(isset($j->table)?$j->table:"");$uh=(isset($j->orgtable)?$j->orgtable:"");$th=(isset($j->orgname)?$j->orgname:$B);$ql=driver()->typeName($j);if($vh&&JUSH=="sql")$Lf[$lf]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($uh!=""){$pa=($S!=""?$S:$uh);if($S!="")$J[$S]=$uh;if(!isset($u[$pa])){if(!isset($ri[$uh])){$ri[$uh]=array();foreach(indexes($uh,$f)as$t){if($t["type"]=="PRIMARY"){$ri[$uh]=array_flip($t["columns"]);break;}}}$U[$pa]=$uh;$u[$pa]=$ri[$uh];$d[$pa]=$ri[$uh];}if(isset($d[$pa][$th])){unset($d[$pa][$th]);$u[$pa][$th]=$lf;$Lf[$lf]=$pa;}elseif($xg&&isset($j->orgname)&&$j->db==DB&&!is_blob(array("type"=>$ql)))$Mc[$lf]=array($pa,$th,preg_match('~text|json|lob~',$ql));}$rl[$lf]=$ql;echo"<th title='".h(trim(($uh!=""?"$uh.$th":($j->name!=$th?$th:""))." ".$ql))."'>".h($B).($vh?'':"");}foreach($Mc
as$lf=>$Za){if($d[$Za[0]])unset($Mc[$lf]);}echo"<tbody>\n";}$Ae=array();foreach($u
as$pa=>$t){if($t&&!$d[$pa]){$s="";foreach($t
as$pb=>$lf){if($K[$lf]===null){$s=null;break;}$s
.="&where[".url_escape(bracket_escape($pb))."]=".url_escape($K[$lf]);}$Ae[$pa]=$s;}}echo"<tr>";foreach($K
as$v=>$X){$y="";if(isset($Lf[$v])){if($vh&&JUSH=="sql"){$S=$K[array_search("table=",$Lf)];$y=ME.$Lf[$v].url_escape($vh[$S]!=""?$vh[$S]:$S);}elseif(idx($Ae,$Lf[$v])!==null)$y=ME."edit=".url_escape($U[$Lf[$v]]).$Ae[$Lf[$v]];}$b="";$Za=idx($Mc,$v);if($Za&&idx($Ae,$Za[0])!==null&&is_utf8($X)){$Kc=true;$b=" data-name='".h("val[".bracket_escape($U[$Za[0]])."][".bracket_escape(substr($Ae[$Za[0]],1))."][".bracket_escape($Za[1])."]")."' data-text='".($Za[2]?1:0)."'";}$X=select_value($X,$y,array('type'=>(preg_match('~binary~',$rl[$v])?'blob':$rl[$v])),null);echo"<td".(preg_match(number_type(),$rl[$v])?" class='number'":"")."$b>$X";}}$x=$q;echo($q?"</table>\n</div>":"<p class='message'>".'No rows.')."\n";return$J;}function
textarea($B,$Y,$L=10,$tb=80,$nf=JUSH){echo"<textarea name='".h($B)."' rows='$L' cols='$tb' class='sqlarea jush-".h($nf)."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$C,$Y="",$bi=""){if($C&&$Y!=""&&!isset($C[$Y]))$C=array($Y=>$Y)+$C;$Fk=($C?"select":"input");return"<$Fk$b".($C?"><option value=''>$bi".optionlist($C,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$bi'>");}function
json_row($v,$X=null,$ad=true){static$Bd=true;if($Bd)echo"{";if($v!=""){echo($Bd?"":",")."\n\t\"".addcslashes($v,"\r\n\t\"\\/").'": '.($X!==null?($ad?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Bd=false;}else{echo"\n}\n";$Bd=true;}}function
flat_collations(){$sb=collations();return(is_array(reset($sb))?call_user_func_array('array_merge',array_values($sb)):$sb);}function
edit_type($v,array$j,array$sb,array$Kd=array(),array$nd=array()){$V=(string)$j["type"];echo"<td><select name='".h($v)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($V&&!array_key_exists($V,driver()->types())&&!isset($Kd[$V])&&!in_array($V,$nd))$nd[]=$V;$fk=driver()->structuredTypes();if($Kd)$fk['Foreign keys']=$Kd;echo
optionlist(array_merge($nd,$fk),$V),"</select><td>","<input name='".h($v)."[length]' value='".h($j["length"])."' size='3'".(!$j["length"]&&preg_match('~var(char|binary)$~',$V)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($sb?"<input list='collations' name='".h($v)."[collation]'".option_types($V,'('.text_type().')$')." value='".h($j["collation"])."' placeholder='(".'collation'.")'>":''),(driver()->unsigned?"<select name='".h($v)."[unsigned]'".option_types($V,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$j["unsigned"]).'</select>':''),(isset($j['on_update'])?"<select name='".h($v)."[on_update]'".option_types($V,'timestamp|datetime').'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?"CURRENT_TIMESTAMP":$j["on_update"])).'</select>':''),($Kd?"<select name='".h($v)."[on_delete]'".option_types($V,'`')."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",driver()->onActions),$j["on_delete"])."</select> ":" ");}function
option_types($V,$rl){return" data-types='".h($rl)."'".(preg_match("~$rl~",$V)?"":" class='hidden'");}function
process_length($w){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$w))return"(max)";$Vc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Vc(?:\\s*,\\s*$Vc)*+\\s*\\)?\\s*\$~",$w)&&preg_match_all("~$Vc~",$w,$Tf)?"(".implode(",",$Tf[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$w)));}function
process_in($X){$Vc=driver()->enumLength;if(preg_match("~^\\s*\\(?\\s*$Vc(?:\\s*,\\s*$Vc)*+\\s*\\)?\\s*\$~",$X)&&preg_match_all("~$Vc~",$X,$Tf))return"(".implode(", ",$Tf[0]).")";$J=array();foreach(explode(",",$X)as$kf)$J[]=q(trim($kf));return"(".implode(", ",$J).")";}function
process_type(array$j,$qb="COLLATE"){return" $j[type]".process_length($j["length"]).(preg_match(number_type(),$j["type"])&&in_array($j["unsigned"],driver()->unsigned)?" $j[unsigned]":"").(preg_match('~'.text_type().'~',$j["type"])&&$j["collation"]?" $qb ".(JUSH=="mssql"?$j["collation"]:q($j["collation"])):"");}function
process_field(array$j,array$nl){if($j["on_update"])$j["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$j["on_update"]);return
array(idf_escape(trim($j["field"])),process_type($nl),($j["null"]?" NULL":" NOT NULL"),default_value($j),(preg_match('~timestamp|datetime~',$j["type"])&&$j["on_update"]?" ON UPDATE $j[on_update]":""),(support("comment")&&$j["comment"]!=""?" COMMENT ".q($j["comment"]):""),($j["auto_increment"]?auto_increment():null),);}function
default_value(array$j){if($j["default"]===null)return"";$gc=str_replace("\r","",$j["default"]);$Ud=$j["generated"];return(in_array($Ud,driver()->generated)?(JUSH=="mssql"?" AS ($gc)".($Ud=="VIRTUAL"?"":" $Ud"):" GENERATED ALWAYS AS ($gc) $Ud"):(preg_match('~^GENERATED ~i',$gc)?" $gc":" DEFAULT ".(preg_match('~char|binary|text|json|enum|set|String~',$j["type"])||preg_match('~^(?![a-z])~i',$gc)?(JUSH=="sql"&&preg_match('~text|json~',$j["type"])?"(".q($gc).")":q($gc)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($gc)":$gc)))));}function
edit_fields(array$k,array$sb,$V="TABLE",array$Kd=array()){$k=array_values($k);$ic=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$xb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($V=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($V=="TABLE"?'Column name':'Parameter name'),"<th id='label-type'>".'Type'."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".'Length',"<th>".'Options';if($V=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".'Auto Increment'."'>AI</abbr>",doc_link(array('pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'cockroach'=>"serial",)),"<th id='label-default'$ic>".'Default value',(support("comment")?"<th id='label-comment'$xb>".'Comment':"");$_f=!support("move_col");echo"<td>".icon("plus","add[".($_f?count($k):0)."]","+",'Add next',($_f?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($k
as$q=>$j){$q++;$wh=$j[($_POST?"orig":"field")];$uc=(isset($_POST["add"][$q-1])||(isset($j["field"])&&!idx($_POST["drop_col"],$q)))&&(support("drop_col")||$wh=="");echo"<tr".($uc?"":" hidden").">\n",($V=="PROCEDURE"?"<td>".html_select("fields[$q][inout]",explode("|",driver()->inout),$j["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",'Move')." ":"");if($uc)echo"<input name='fields[$q][field]' value='".h($j["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$q-1])?" autofocus":"").">";echo
input_hidden("fields[$q][orig]",$wh);edit_type("fields[$q]",$j,$sb,$Kd);if($V=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$q][null]",1,$j["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$q'".($j["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$ic>".(driver()->generated?html_select("fields[$q][generated]",array_merge(array("","DEFAULT"),driver()->generated),$j["generated"])." ":checkbox("fields[$q][generated]",1,$j["generated"],"","","","label-default"));$b=" name='fields[$q][default]' aria-labelledby='label-default'";$Y=h($j["default"]);echo(preg_match('~\n~',$j["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$q][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$xb>".adminer()->commentInput('COLUMN',$b,$j["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$q]","+",'Add next')." ":""),($wh==""||support("drop_col")?icon("cross","drop_col[$q]","x",'Remove'):"");}}function
process_fields(array&$k){if($_POST["add"]){$k=array_values($k);array_splice($k,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Fc,$Ob,$Gc,$Lk,$Hc,$z,$ng,$lg,$mg,$eh,$Kg){if($_POST["drop"])query_redirect($Fc,$z,$ng);elseif($eh=="")query_redirect($Ob,$z,$mg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($z,$lg,queries($Fc)&&queries($Ob)&&driver()->commit());driver()->rollback();}elseif($eh!=$Kg){$Pb=queries($Ob);queries_redirect($z,$lg,$Pb&&queries($Fc));if($Pb&&$Gc)queries($Gc);}else
queries_redirect($z,$lg,queries($Lk)&&queries($Hc)&&queries($Fc)&&queries($Ob));}function
create_trigger($hh,array$K){$Rk=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$hh.$Rk:$Rk.$hh).preg_replace('~[\s;]+$~',''," $K[Type]\n$K[Statement]").";";}function
q_dollar($R){$mc='$$';while(strpos($R.$mc,$mc)!=strlen($R))$mc='$_'.substr($mc,1);return$mc.$R.$mc;}function
routine_collate($rb){static$db=array();if($rb&&!$db){foreach(collations()as$cb=>$Tl){foreach((array)$Tl
as$X)$db[$X]=$cb;}}return($db[$rb]?"CHARACTER SET ".q($db[$rb])." ":"")."COLLATE";}function
create_routine($Yi,array$K){$P=array();$k=$K["fields"];ksort($k);foreach($k
as$j){if($j["field"]!=""){$Ue=(preg_match("~^(".driver()->inout.")\$~",$j["inout"])?$j["inout"]:"");$P[]="\n  ".(JUSH=="mssql"?"@$j[field]".process_type($j).($Ue?" $Ue":""):($Ue?"$Ue ":"").idf_escape($j["field"]).process_type($j,routine_collate($j["collation"])));}}$kc="";$C=array();foreach(routine_options($Yi)as$v=>$Ul){$Y=idx($K["options"],$v,"");if($v=="DEFINER")$kc=($Y?" $v=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$Ul){if($Y!="")$C[]="$v ".q($Y);}elseif($Y!=reset($Ul)&&in_array($Y,$Ul))$C[]=$Y;}$yf=$K["language"];$lc=preg_replace('~[\s;]+$~','',$K["definition"]);$Bc=(JUSH=="pgsql"||($yf&&$yf!="sql"));$Hh=($P?implode(",",$P)."\n":"");return"CREATE$kc $Yi ".table(trim($K["name"])).(JUSH=="mssql"&&$Yi=="PROCEDURE"?rtrim($Hh):" ($Hh)").($Yi=="FUNCTION"?"\nRETURNS".process_type($K["returns"],routine_collate($K["returns"]["collation"])):"").($yf?" LANGUAGE $yf":"").($C?"\n".implode(" ",$C):"").($Bc?" AS ".q_dollar("\n".trim($lc)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$lc;");}function
remove_definer($H){$kc=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($kc).')','\1',$H);}function
format_foreign_key(array$m){$h=$m["db"];$Sg=$m["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$m["source"])).") REFERENCES ".($h!=""&&$h!=$_GET["db"]?idf_escape($h).".":"").($Sg!=""&&$Sg!=$_GET["ns"]?idf_escape($Sg).".":"").idf_escape($m["table"])." (".implode(", ",array_map('Adminer\idf_escape',$m["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$m["on_delete"])?" ON DELETE $m[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$m["on_update"])?" ON UPDATE $m[on_update]":"").($m["deferrable"]?" $m[deferrable]":"");}function
tar_file($l,$Wk){$J=pack("a100a8a8a8a12a12",$l,644,0,0,decoct($Wk->size),decoct(time()));$jb=8*32;for($q=0;$q<strlen($J);$q++)$jb+=ord($J[$q]);$J
.=sprintf("%06o",$jb)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$Wk->send();echo
str_repeat("\0",511-($Wk->size+511)%512);}function
doc_version(){$Fj=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$Fj,$_);return($_[1]>=18?$_[1]:"19");}$Ki=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$Xl=(preg_match($Ki,$Fj,$_)?$_[0]:"");if(JUSH=='mssql')return($Xl>=15?"sql-server-ver$Xl":($Xl==12?"azuresqldb-current":"sql-server-2017"));return$Xl;}function
doc_link(array$Xh,$Mk="<sup>?</sup>"){$Xl=doc_version();$Gl=array('sql'=>"https://dev.mysql.com/doc/refman/$Xl/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Xl)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$Xl/",);if(connection()->flavor=='maria'){$Gl['sql']="https://mariadb.com/kb/en/";$Xh['sql']=(isset($Xh['mariadb'])?$Xh['mariadb']:str_replace(".html","/",$Xh['sql']));}if(connection()->flavor=='cockroach'&&isset($Xh['cockroach'])){$Gl['pgsql']="https://docs.cockroachlabs.com/docs/v$Xl/";$Xh['pgsql']=$Xh['cockroach'];}return($Xh[JUSH]?"<a href='".h($Gl[JUSH].$Xh[JUSH].(JUSH=='mssql'?"?view=$Xl":""))."'".target_blank().">$Mk</a>":"");}function
db_size($h){if(!connection()->select_db($h))return"?";$J=0;foreach(table_status()as$T)$J+=$T["Data_length"]+$T["Index_length"];return
format_number($J);}function
set_utf8mb4($Ob){static$P=false;if(!$P&&preg_match('~\butf8mb4~i',$Ob)){$P=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header('Database'.": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$g=adminer()->databases();if($g)redirect(ME."db=".url_escape($g[0]));}if($_POST["db"]&&!$i)queries_redirect(substr(ME,0,-1),'Databases have been dropped.',drop_databases($_POST["db"]));page_header('Select database',$i,false);echo"<p class='links'>\n";foreach(array('database'=>'Create database','privileges'=>'Privileges','processlist'=>'Process list','variables'=>'Variables','status'=>'Status',)as$v=>$X){if(support($v))echo"<a href='".h(ME)."$v='>$X</a>\n";}echo"<p>".sprintf('%s version: %s through PHP extension %s',get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".sprintf('Logged in as: %s',"<b>".h(logged_user())."</b>")."\n";$g=adminer()->databases();if($g){$ij=support("scheme");$sb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".'Database'.(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".'Refresh'."</a>":"")."<th>".'Collation'."<th>".'Tables'."<th>".'Size'." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".'Compute'."</a>"."<tbody>\n";$g=($_GET["dbsize"]?count_tables($g):array_flip($g));foreach($g
as$h=>$U){$Xi=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($h);$r=h("Db-".$h);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$h,in_array($h,(array)$_POST["db"]),"","","",$r):""),"<th><a href='$Xi' id='$r'>".h($h)."</a>";$rb=h(db_collation($h,$sb));echo"<td>".(support("database")?"<a href='$Xi".($ij?"&amp;ns=":"")."&amp;database=' title='".'Alter database'."'>$rb</a>":$rb),"<td align='right'><a href='$Xi&amp;schema=' id='tables-".h($h)."' title='".'Database schema'."'>".($_GET["dbsize"]?format_number($U):"?")."</a>","<td align='right' id='size-".h($h)."'>".($_GET["dbsize"]?db_size($h):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".'Drop'."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$ja=adminer();$fi=($ja
instanceof
Plugins?$ja->plugins:array());$Ec=($ja
instanceof
Plugins?$ja->drivers:array());$rc=design_checksums();if($fi||$Ec||$rc){$kb=($ja
instanceof
Plugins?$ja->checksums():array());$Xg=Plugins::officialChecksums();$Bl=function($Fl){return" (<a href='$Fl'".target_blank()." class='update'>".VERSION."</a>)";};$ei=function($wd)use($kb,$Xg,$Bl){return($kb[$wd]&&$Xg[$wd]&&$kb[$wd]!==$Xg[$wd]?$Bl("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".'Loaded plugins'."</h3>\n<ul>\n";foreach($fi
as$ci){$Ii=new
\ReflectionObject($ci);$oc=(method_exists($ci,'description')?$ci->description():"");if(!$oc){if(preg_match('~^/[\s*]+(.+)~',$Ii->getDocComment(),$_))$oc=$_[1];}$jj=(method_exists($ci,'screenshot')?$ci->screenshot():"");echo"<li><b>".get_class($ci)."</b>".h($oc?": $oc":"").($jj?" (<a href='".h($jj)."'".target_blank().">".'screenshot'."</a>)":"").$ei(basename((string)$Ii->getFileName(),'.php'))."\n";}foreach($Ec
as$r=>$B)echo"<li><b>".h($r)."</b>: ".h($B).$ei(basename((string)$ja->driverFiles[$r],'.php'))."\n";if($rc){$Zg=official_design_checksums();foreach($rc
as$l=>$qc){list($B,$jb)=$qc;$Yg=$Zg["$B/$l"];echo"<li><b>".h($l)."</b>".h($B?": $B":"").($Yg&&$Yg!==$jb?$Bl("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~&db=[^&]+~','\0&ns='.url_escape(get_schema()),relative_uri()));if(!set_schema($_GET["ns"]))page_header('Schema'.h(": $_GET[ns]"),adminer()->error(),true,"","ns");}}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Hb){$this->size+=strlen($Hb);fwrite($this->handler,$Hb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$k=fields($a);header("Content-Type: application/octet-stream");$Ul=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Ul)).".".friendly_url($_GET["field"]));$N=array(idf_escape($_GET["field"]));$I=driver()->select($a,$N,array(where($_GET,$k)),$N);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$k[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$k=fields($a);if(!$k)$i=adminer()->error();$T=table_status1($a);$B=adminer()->tableName($T);$i=$i?:h($T["Error"]);page_header(($k&&is_view($T)?$T['Engine']=='materialized view'?'Materialized view':'View':'Table').": ".($B!=""?$B:h($a)),$i,array(),"",!$k);$Wi=array();foreach($k
as$v=>$j)$Wi+=$j["privileges"];adminer()->selectLinks($T,(isset($Wi["insert"])||!support("table")?"":null));$wb=$T["Comment"];if($wb!="")echo"<p class='nowrap'>".'Comment'.": ".adminer()->commentValue('TABLE',$wb)."\n";if($k)adminer()->tableStructurePrint($k,$T);function
tables_links(array$U){echo"<ul>\n";foreach($U
as$K){$y=preg_replace('~ns=[^&]*~',"ns=".url_escape($K["ns"]),ME);echo"<li><a href='".h($y."table=".url_escape($K["table"]))."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>";}echo"</ul>\n";}$Se=driver()->inheritsFrom($a);if($Se){echo"<h3>".'Inherits from'."</h3>\n";tables_links($Se);}if(support("indexes")&&driver()->supportsIndex($T)){echo"<div>\n","<h3 id='indexes'>".'Indexes'."</h3>\n";$u=indexes($a);if($u)adminer()->tableIndexesPrint($u,$T);if(driver()->supportsAlterIndex($T))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.'Alter indexes'."</a>\n";echo"</div>\n";}if(!is_view($T)&&driver()->supportsAlterTable($T)){if(fk_support($T)){echo"<div>\n","<h3 id='foreign-keys'>".'Foreign keys'."</h3>\n";$Kd=foreign_keys($a);if($Kd){echo"<table>\n","<thead><tr><th>".'Source'."<th>".'Target'."<th>".'ON DELETE'."<th>".'ON UPDATE'."<td class='hover'><tbody>\n";foreach($Kd
as$B=>$m){echo"<tr title='".h($B)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$m["source"]))."</i>";$y=($m["db"]!=""?preg_replace('~db=[^&]*~',"db=".url_escape($m["db"]),ME):($m["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".url_escape($m["ns"]),ME):ME));echo"<td><a href='".h($y."table=".url_escape($m["table"]))."'>".($m["db"]!=""&&$m["db"]!=DB?"<b>".h($m["db"])."</b>.":"").($m["ns"]!=""&&$m["ns"]!=$_GET["ns"]?"<b>".h($m["ns"])."</b>.":"").h($m["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$m["target"]))."</i>)","<td>".h($m["on_delete"]),"<td>".h($m["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($B)).'">'.'Alter'.'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.'Create foreign key'."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".'Checks'."</h3>\n";$fb=driver()->checkConstraints($a);if($fb){echo"<table>\n";foreach($fb
as$v=>$X)echo"<tr title='".h($v)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($v))."'>".'Alter'."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.'Create check'."</a>\n","</div>\n";}}if(support(is_view($T)?"view_trigger":"trigger")&&driver()->supportsAlterTable($T)){echo"<div>\n","<h3 id='triggers'>".'Triggers'."</h3>\n";$kl=triggers($a);if($kl){echo"<table>\n";foreach($kl
as$v=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($v)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($v))."'>".'Alter'."</a>\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.'Create trigger'."</a>\n","</div>\n";}$Kj=driver()->shadowTables($a);if($Kj){echo"<h3 id='shadow-tables'>".'Shadow tables'."</h3>\n";tables_links($Kj);}$Re=driver()->inheritedTables($a);if($Re){echo"<h3 id='partitions'>".'Inherited by'."</h3>\n";$Mh=driver()->partitionsInfo($a);if($Mh)echo"<p><code class='jush-".JUSH."'>BY ".h("$Mh[partition_by]($Mh[partition])")."</code>\n";tables_links($Re);}}elseif(isset($_GET["schema"])){page_header('Database schema',"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($S,array$Hi,array&$d){if(!isset($d[$S])){$d[$S]=0;foreach((array)idx($Hi,$S)as$B=>$Ji){if($B!=$S)$d[$S]=max($d[$S],schema_column($B,$Hi,$d)+1);}}return$d[$S];}function
type_class($V){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$v=>$X){if(preg_match("~$v|$X~",$V))return" class='$v'";}}$xk=array();$zk=array();$yk=array();$td=array();$ba=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ba,$Tf,PREG_SET_ORDER);foreach($Tf
as$q=>$_){$xk[$_[1]]=array((float)$_[2],(float)$_[3]);$zk[]="\n\t'".js_escape($_[1])."': [ $_[2], $_[3] ]";}$M=array();$Hi=array();$Kd=array();$sa=driver()->allFields();$re=array();$_k=array();foreach(table_status('',true)as$S=>$T){if(!is_view($T)){if(adminer()->tableName($T)!=""&&!$T["dependent"])$_k[$S]=$T;else$re[$S]=true;}}foreach($_k
as$S=>$T){$G=0;$M[$S]["fields"]=array();foreach($sa[$S]as$j){$G+=1.25;$td[$S][$j["field"]]=$G;$M[$S]["fields"][$j["field"]]=$j;}foreach(adminer()->foreignKeys($S)as$X){if($X["db"]==""&&$X["ns"]==""&&!$re[$X["table"]]){$Kd[$S][]=$X;$Hi[$X["table"]][$S]=array();}}}$d=array();$Wd=array();$jm=array();$be=array();foreach(array_keys($M)as$B)schema_column($B,$Hi,$d);arsort($d);foreach($d
as$B=>$c){$tg=null;foreach((array)idx($Kd,$B)as$X){if($X["table"]!=$B&&$M[$X["table"]])$tg=($tg===null?$d[$X["table"]]:min($tg,$d[$X["table"]]));}$d[$B]=max($c,(int)$tg-1);}foreach($M
as$B=>$S){$c=$d[$B];$Wd[$c][]=$B;$Ok=.75*strlen($B);foreach($S["fields"]as$j)$Ok=max($Ok,.65*strlen($j["field"]));$jm[$c]=max(idx($jm,$c,0),ceil($Ok)+1);}foreach($Kd
as$B=>$Tl){foreach($Tl
as$X){$ae=$d[$B]+(idx($d,$X["table"],$d[$B])>$d[$B]?1:0);$be[$ae]=idx($be,$ae,0)+1;}}ksort($Wd);$pe=0;$im=0;$ub=0;$qi=null;$tk=array();$Bk=array();foreach($Wd
as$c=>$U){if($qi!==null){$ub=round($ub+$jm[$qi]+1.7+idx($be,$c,0)*.1,1);$D=array();foreach($U
as$B){$kk=0;$Nb=0;$Hg=array_keys((array)idx($Hi,$B));foreach((array)idx($Kd,$B)as$X)$Hg[]=$X["table"];foreach($Hg
as$Dg){if($M[$Dg]&&$d[$Dg]<$c){$kk+=$M[$Dg]["pos"][0];$Nb++;}}$D[$B]=($Nb?$kk/$Nb:$pe);}asort($D);$U=array_keys($D);}$Zk=0;foreach($U
as$B){$G=1.25*count($M[$B]["fields"]);$M[$B]["pos"]=($xk[$B]?:array($Zk,$ub));$tk[$B]=$M[$B]["pos"][1];$Bk[$B]=$jm[$c];$Zk+=2.5+$G;$pe=max($pe,$M[$B]["pos"][0]+2.5+$G);$im=max($im,round($M[$B]["pos"][1]+$jm[$c],1));if(!$xk[$B])$yk[]="\n\t'".js_escape($B)."': [ ".$M[$B]["pos"][0].", ".$M[$B]["pos"][1]." ]";}$qi=$c;}$Df=array();$Ma=array();foreach($Kd
as$B=>$Tl){foreach($Tl
as$X){$Hk=idx($tk,$X["table"],$tk[$B]);$Tj=$tk[$B]+$Bk[$B];$Vi=($Hk-1>$Tj);$Bf=($Vi?$Tj+1:min($tk[$B],$Hk)-1);$La=idx($Ma,(string)$Bf,0);$Ma[(string)$Bf]=$La+1;$Bf=round($Vi?min($Bf+$La*.1,$Hk-1):$Bf-$La*.1,1);while($Df[(string)$Bf])$Bf-=.0001;$M[$B]["references"][$X["table"]][(string)$Bf]=array($X["source"],$X["target"]);$Hi[$X["table"]][$B][(string)$Bf]=$X["target"];$Df[(string)$Bf]=true;}}echo'<div id="schema" style="height: ',$pe,'em; width: ',$im,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$zk)."\n",'};
const tablePosDefault = {',implode(",",$yk)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$pe,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($M
as$B=>$S){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$S["pos"][0]."em; left: ".$S["pos"][1]."em; width: ".$Bk[$B]."em;'>",'<a href="'.h(ME).'table='.url_escape($B).'"><b>'.h($B)."</b></a>";foreach($S["fields"]as$j){$X='<span'.type_class($j["type"]).' title="'.h($j["type"].($j["length"]?"($j[length])":"").($j["null"]?" NULL":'')).'">'.h($j["field"]).'</span>';echo"<br>".($j["primary"]?"<i>$X</i>":$X);}foreach((array)$S["references"]as$Ik=>$Ji){foreach($Ji
as$Bf=>$Ei){$Cf=$Bf-$S["pos"][1];$hk=($Cf>0?"left: 100%; width: calc($Cf"."em - 100%)":"left: $Cf"."em");$im=($Cf>0?"100%":(-$Cf)."em");$q=0;foreach($Ei[0]as$Sj)echo"\n<div class='references' title='".h($Ik)."' id='refs$Bf-".($q++)."' style='$hk"."; top: ".$td[$B][$Sj]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $im;'></div></div>";}}foreach((array)$Hi[$B]as$Ik=>$Ji){foreach($Ji
as$Bf=>$Jk){$Cf=$Bf-$S["pos"][1];$q=0;foreach($Jk
as$Gk)echo"\n<div class='references arrow' title='".h($Ik)."' id='refd$Bf-".($q++)."' style='left: $Cf"."em; top: ".$td[$B][$Gk]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Cf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($M
as$B=>$S){foreach((array)$S["references"]as$Ik=>$Ji){if($M[$Ik]){foreach($Ji
as$Bf=>$Ei){$ug=$pe;$bg=-10;foreach($Ei[0]as$v=>$Sj){$hi=$S["pos"][0]+$td[$B][$Sj];$ii=$M[$Ik]["pos"][0]+$td[$Ik][$Ei[1][$v]];$ug=min($ug,$hi,$ii);$bg=max($bg,$hi,$ii);}echo"<div class='references' id='refl$Bf' style='left: $Bf"."em; top: $ug"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($bg-$ug)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($ba)),'" id="schema-link">Permanent link</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$i){$gc=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$mk){if(support($mk))$gc[$mk."s"]='';}save_settings(array_intersect_key($_POST+$gc,array_flip(array("output","format","db_style","schema_style","table_style","data_style"))+$gc),"adminer_export");$ra=(DB==""||$_GET["ns"]==="");$U=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$kd=dump_headers((count($U)==1?key($U):DB),($ra||count($U)>1));$if=preg_match('~sql~',$_POST["format"]);if($if){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$hk=$_POST["db_style"];$g=array(DB);if(DB==""){$g=$_POST["databases"];if(is_string($g))$g=explode("\n",rtrim(str_replace("\r","",$g),"\n"));}foreach((array)$g
as$h){adminer()->dumpDatabase($h);if(connection()->select_db($h)){if($if&&$hk)echo
use_sql($h,$hk).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$M){if($M!=""){if(DB==""&&information_schema(DB,$M))continue;set_schema($M);}if($if&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$dk=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$jd=array();$Zb=array();foreach($dk
as$B=>$T){if($ra||in_array($B,(array)$_POST["tables"]))$jd[$B]=$T;if($ra||in_array($B,(array)$_POST["data"]))$Zb[$B]=$T;}if($if){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($jd);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$ll=array();foreach($Zb
as$B=>$T){if(!is_view($T)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($jd[$B])))$ll[]=$B;}echo
truncate_all_sql($ll);}$Ch="";if($_POST["types"]){foreach(types()as$r=>$V){$lc=type_definition($r);$Wg=($lc["kind"]=='d'?"DOMAIN":"TYPE");if($lc["definition"])$Ch
.=($hk!='DROP+CREATE'?"DROP $Wg IF EXISTS ".table($V).";;\n":"")."CREATE $Wg ".table($V)." $lc[definition];\n\n";else$Ch
.="-- Could not export type $V\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$B=$K["ROUTINE_NAME"];$Yi=$K["ROUTINE_TYPE"];$Ob=create_routine($Yi,array("name"=>$B)+routine($K["SPECIFIC_NAME"],$Yi));set_utf8mb4($Ob);$Ch
.=($hk!='DROP+CREATE'?"DROP $Yi IF EXISTS ".table($B).";;\n":"")."$Ob;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$Ob=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($Ob);$Ch
.=($hk!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$Ob;;\n\n";}}echo($Ch&&JUSH=='sql'?"DELIMITER ;;\n\n$Ch"."DELIMITER ;\n\n":$Ch);}if($_POST["table_style"]||$_POST["data_style"]){$Zl=array();foreach($dk
as$B=>$T){$S=array_key_exists($B,$jd);$Xb=array_key_exists($B,$Zb);if($S||$Xb){$Wk=null;if($kd=="tar"){$Wk=new
TmpFile;ob_start(array($Wk,'write'),1e5);}adminer()->dumpTable($B,($S?$_POST["table_style"]:""),(is_view($T)?2:0));if(is_view($T))$Zl[]=$B;elseif($Xb){$k=fields($B);$N=array("*");$Kb=convert_fields($k,$k);if($Kb)$N[]=substr($Kb,2);adminer()->dumpData($B,$_POST["data_style"],"",$N);}if($if&&$_POST["triggers"]&&$S&&($kl=trigger_sql($B)))echo"\nDELIMITER ;;\n$kl\nDELIMITER ;\n";if($kd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$h/")."$B.csv",$Wk);}elseif($if)echo"\n";}}if($if&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($jd
as$B=>$T){if(!is_view($T))echo
foreign_keys_sql($B);}}if($if){foreach($Zl
as$Yl)adminer()->dumpTable($Yl,$_POST["table_style"],1);}if($kd=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header('Export',$i,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$dc=array('','USE','DROP+CREATE','CREATE');$gj=(JUSH=="mssql"?array('','DROP+CREATE','CREATE'):$dc);$Ak=array('','DROP+CREATE','CREATE');$Yb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Yb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".'Output'."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".'Format'."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".'Database'."<td>".html_select('db_style',$dc,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],'User types'):"").(support("routine")?checkbox("routines",1,$K["routines"],'Routines'):"").(support("event")?checkbox("events",1,$K["events"],'Events'):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".'Schema'."<td>".html_select('schema_style',$gj,$K["schema_style"]):""),"<tr><th>".'Tables'."<td>".html_select('table_style',$Ak,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],'Auto Increment').(support("trigger")?checkbox("triggers",1,$K["triggers"],'Triggers'):""),"<tr><th>".'Data'."<td>".html_select('data_style',$Yb,$K["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'Export\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$oi=array();if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".'All'."'".on('click','formCheck','^schemas\[').">".'Schema'."</label>","<tbody>\n";foreach(adminer()->schemas()as$M){if(!information_schema(DB,$M))echo"<tr><td>".checkbox("schemas[]",$M,true,$M,"","block")."\n";}}elseif(DB!=""){$hb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$hb class='jsonly' title='".'All'."'".on('click','formCheck','^tables\[').">".'Table'."</label>","<th style='text-align: right;'><label class='block'>".'Data'."<input type='checkbox' id='check-data'$hb class='jsonly' title='".'All'."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$Zl="";$Dk=tables_list();foreach($Dk
as$B=>$V){$ni=preg_replace('~_.*~','',$B);$hb=($a==""||$a==(substr($a,-1)=="%"?"$ni%":$B));$si="<tr><td>".checkbox("tables[]",$B,$hb,$B,"","block");if($V!==null&&!preg_match('~table~i',$V))$Zl
.="$si\n";else
echo"$si<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$hb)."</label>\n";$oi[$ni]++;}echo$Zl;if($Dk)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$g=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($g?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".'All'."'".on('click','formCheck','^databases\[').">":"").'Database'."</label>","<tbody>\n";if($g){foreach($g
as$h){if(!information_schema($h)){$ni=preg_replace('~_.*~','',$h);echo"<tr><td>".checkbox("databases[]",$h,$a==""||$a=="$ni%",$h,"","block")."\n";$oi[$ni]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Bd=true;foreach($oi
as$v=>$X){if($v!=""&&$X>1){echo($Bd?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$v%")."'>".h($v)."</a>";$Bd=false;}}}elseif(isset($_GET["sql"])){if(!$i&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$i&&$_POST["val"]){$ma=0;$ik=true;$ab=array();$ej=0;foreach($_POST["val"]as$L)$ej+=count($L);$Oa=$ej>1&&driver()->begin();foreach($_POST["val"]as$rk=>$L){$S=bracket_escape($rk,true);$k=fields($S);$sk=indexes($S);foreach($L
as$s=>$K){parse_str(bracket_escape($s,true),$Z);$ul=array();foreach($Z["where"]as$v=>$X)$ul[bracket_escape($v,true)]=$X;if(!$k||$Z["null"]||array_diff_key($ul,$k)||!unique_array($ul,$sk)){$ik=false;break
2;}$P=array();$N=array();foreach($K
as$pf=>$X){$v=bracket_escape($pf,true);$j=idx($k,$v);if(!$j){$ik=false;break
3;}$P[idf_escape($v)]=(preg_match('~char|text~',$j["type"])||$X!=""?adminer()->processInput($j,$X):"NULL");$N[$pf]=$v;}$_i=where($Z,$k);if(!driver()->update($S,$P," WHERE $_i",0," ")){$ik=false;break
2;}$ma+=connection()->affected_rows;$d=array();foreach($N
as$v)$d[]=idf_escape($v);$Cl=driver()->select($S,$d,array($_i),$d);$Mg=($Cl?$Cl->fetch_row():array());$lf=0;foreach($N
as$pf=>$v){$j=$k[$v];$gk=array('type'=>(preg_match('~binary~',$j["type"])?'blob':$j["type"]));$ab["val[$rk][$s][$pf]"]=select_value(idx($Mg,$lf++),"",$gk,null);}}}if($Oa&&$ik)$ik=driver()->commit();queries_redirect(null,lang_format(array('%d item has been affected.','%d items have been affected.'),$ma),$ik);if($Oa&&!$ik)driver()->rollback();page_headers();page_messages($i);foreach($ab
as$B=>$X)echo"<div data-name='".h($B)."' hidden>$X</div>\n";exit;}restart_session();$te=&get_session("queries");$se=&$te[DB];if(!$i&&$_POST["clear"]){$se=array();redirect(remove_from_uri("history"));}stop_session();$ka=get_settings("adminer_import");if($_POST&&$ka)save_settings($ka,"adminer_import");page_header((isset($_GET["import"])?'Import':'SQL command'),$i);$Kf=driver()->lineComment();if(!$i&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$mc=driver()->delimiter;$n=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$Xj=adminer()->importServerPath();$n=@fopen((file_exists($Xj)?$Xj:"compress.zlib://$Xj.gz"),"rb");$H=($n?fread($n,1e6):false);}else$H=get_file("sql_file",true,$mc);if(is_string($H)){if(($jg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($jg,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$xi=$H.(preg_match("~$mc\\s*\$~",$H)?"":$mc);if(!$se||first(end($se))!=$xi){restart_session();$se[]=array($xi,time());set_session("queries",$te);stop_session();}}$Uj="(?:\\s|/\\*[\s\S]*?\\*/|(?:$Kf)[^\n]*\n?|--\r?\n)";$ah=0;$Rc=true;$Mb=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$vb=0;$Yc=array();$Jh='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Kf.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$al=microtime(true);while($H!=""){if(!$ah&&preg_match("~^$Uj*+DELIMITER\\s+(\\S+)~i",$H,$_)){$mc=preg_quote($_[1]);$H=substr($H,strlen($_[0]));}elseif(!$ah&&JUSH=='pgsql'&&preg_match("~^($Uj*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$_)){$mc="\n\\\\\\.\r?\n";$Mb=true;$ah=strlen($_[0]);}else{preg_match("($mc\\s*|$Jh)",$H,$_,PREG_OFFSET_CAPTURE,$ah);list($Md,$G)=$_[0];if(!$Md&&$n&&!feof($n))$H
.=fread($n,1e5);else{if(!$Md&&rtrim($H)=="")break;$ah=$G+strlen($Md);if($Md&&!preg_match("(^$mc)",$Md)){$Xa=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($H[$G-1])=="e"));$Yh=($Md=='/*'?'\*/':($Md=='['?']':(preg_match("~^(?:$Kf)~",$Md)?"\n":preg_quote($Md).($Xa?'|\\\\.':''))));while(preg_match("($Yh|\$)s",$H,$_,PREG_OFFSET_CAPTURE,$ah)){$fj=$_[0][0];if(!$fj&&$n&&!feof($n))$H
.=fread($n,1e5);else{$ah=$_[0][1]+strlen($fj);if(!$fj||$fj[0]!="\\")break;}}}else{$xi=substr($H,0,$G+($Mb?3:0));$H=substr($H,$ah);$ah=0;if($Mb){$mc=driver()->delimiter;$Mb=false;}$ob="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($xi)."</code>";if(preg_match("~^$Uj*+\$~",$xi)&&!preg_match('~/\*M?!~',$xi)){echo($_POST["only_errors"]?"":"<pre>$ob</pre>\n");continue;}$Rc=false;$vb++;$si="<pre id='sql-$vb'>$ob</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Uj*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$xi,$_)!==0){echo$si,"<p class='error'>".sprintf('%s queries are not supported.',preg_match('~ATTACH~i',$_[1])?'ATTACH':'VACUUM INTO')."\n";$Yc[]=" <a href='#sql-$vb'>$vb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$si;ob_flush();flush();}$bk=microtime(true);if(connection()->multi_query($xi)&&$f&&preg_match("~^$Uj*+USE\\b~i",$xi))$f->query($xi);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$si:""),"<p class='error'>".'Error in query'.(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$Yc[]=" <a href='#sql-$vb'>$vb</a>";if($_POST["error_stops"])break
2;}else{$y=ME."sql=".url_escape(trim($xi));$Pk=" <span class='time'>(".format_time($bk).")</span>".(strlen($y)<1900?" <a href='".h($y)."'>".'Edit'."</a>":"");$ma=connection()->affected_rows;$cm=($_POST["only_errors"]?"":driver()->warnings());$dm="warnings-$vb";if($cm)$Pk
.=", <a href='#$dm' class='toggle'>".'Warnings'."</a>";$hd="";$id="explain-$vb";if(is_object($I)){$x=$_POST["limit"];$Ug=$x;$Kc=!$_POST["only_errors"];if($Kc)echo"<form action='' method='post'>\n";$vh=print_select_result($I,$f,array(),$Ug,$Kc);if(!$_POST["only_errors"]){$Ug=max($I->num_rows,$Ug);echo"<p class='sql-footer'>".($Ug?($x&&$Ug>$x?sprintf('%d / ',$x):"").lang_format(array('%d row','%d rows'),$Ug):""),$Pk;if($f&&preg_match("~^($Uj|\\()*+SELECT\\b~i",$xi)&&($hd=adminer()->explain($f,$xi,$vh))!="")echo", <a href='#$id' class='toggle'>Explain</a>";if($Kc)echo", <input type='submit' name='save' value='".'Save'."' class='jsonly' disabled"." title='".'Ctrl+click on a value to modify it.'."'".on('click','sqlSave','Saving…').">";$r="export-$vb";echo", <a href='#$r' class='toggle'>".'Export'."</a><span id='$r' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ka["output"])." ".html_select("format",adminer()->dumpFormat(),$ka["format"]).input_hidden("query",$xi)."<input type='submit' name='export' value='".'Export'."'".($x?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Uj*+(CREATE|DROP|ALTER)$Uj++(DATABASE|SCHEMA)\\b~i",$xi)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang_format(array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),$ma)."$Pk\n";}echo($cm?"<div id='$dm' class='hidden'>\n$cm</div>\n":""),($hd!=""?"<div id='$id' class='hidden explain'>\n$hd</div>\n":"");}$bk=microtime(true);}while(connection()->next_result());}}}}}if($Rc)echo"<p class='message'>".'No commands to execute.'."\n";else{$Ge=connection()->inTransaction();driver()->rollback();if($Ge)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang_format(array('%d query executed OK.','%d queries executed OK.'),$vb-count($Yc))," <span class='time'>(".format_time($al).")</span>\n";elseif($Yc&&$vb>1)echo"<p class='error'>".'Error in query'.": ".implode("",$Yc)."\n";}}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$Dl="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"));else
echo
on_upload_progress($Dl);echo'>
';$ed="<input type='submit' value='".'Execute'."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$xi=$_GET["sql"];if($_POST)$xi=$_POST["query"];elseif($_GET["history"]=="all")$xi=$se;elseif($_GET["history"]!="")$xi=idx($se[$_GET["history"]],0);echo"<p>";textarea("query",$xi,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$ed\n",'Limit rows'.": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$ce=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".'File upload'."</legend><div>",($Dl?input_hidden(ini_get("session.upload_progress.name"),$Dl):""),"SQL$ce: ".file_input(" name='sql_file[]' multiple","\n$ed"),($Dl?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$De=adminer()->importServerPath();if($De)echo"<fieldset><legend>".'From server'."</legend><div>",sprintf('Webserver file %s',"<code>".h($De)."$ce</code>")," <input type='submit' name='webfile' value='".'Run file'."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),'Stop on error')."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),'Show only errors')."\n",input_token();if(!isset($_GET["import"])&&$se){print_fieldset("history",'History',$_GET["history"]!="");for($X=end($se);$X;$X=prev($se)){$v=key($se);list($xi,$Pk,$Nc)=$X;echo'<div><a href="'.h(ME."sql=&history=$v").'" class="hover">'.'Edit'."</a>"." <span class='time' title='".@date('Y-m-d',$Pk)."'>".@date("H:i:s",$Pk)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Kf).*~m",'',$xi))),80,"</code>").($Nc?" <span class='time'>($Nc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'Edit all'."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$k=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$k):""):where($_GET,$k));$Al=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($k
as$B=>$j){if((!$Al&&!isset($j["privileges"]["insert"]))||adminer()->fieldName($j)=="")unset($k[$B]);}if($_POST&&!$i&&!isset($_GET["select"])){$z=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$z=($Al?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$z))$z=ME."select=".url_escape($a);$u=indexes($a);$vl=unique_array($_GET["where"],$u);$_i="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($z,'Item has been deleted.',driver()->delete($a,$_i,$vl?0:1));else{$P=array();foreach($k
as$B=>$j){$X=process_input($j);if($X!==false&&$X!==null)$P[idf_escape($B)]=$X;}if($Al){if(!$P)redirect($z);queries_redirect($z,'Item has been updated.',driver()->update($a,$P,$_i,$vl?0:1));if(is_ajax()){page_headers();page_messages($i);exit;}}else{$I=driver()->insert($a,$P);$Af=($I?last_id($I):0);queries_redirect($z,sprintf('Item%s has been inserted.',($Af?" $Af":"")),$I);}}}$K=null;$H="";$Pk="";if($Z){$N=array();$qj=array("*");foreach($k
as$B=>$j){if(isset($j["privileges"]["select"])){$Aa=($_POST["clone"]&&$j["auto_increment"]?"''":convert_field($j));$c=($Aa?"$Aa AS ":"").idf_escape($B);$N[]=$c;if($Aa)$qj[]=$c;}}$K=array();if(!support("table")){$N=array("*");$qj=$N;}if($N){$bk=microtime(true);$I=driver()->select($a,$N,array($Z),$N,array(),(isset($_GET["select"])?2:1));$H=str_replace("SELECT ".implode(", ",$N),"SELECT ".implode(", ",$qj),driver()->query);$Pk=format_time($bk);if(!$I)$i=adminer()->error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!$k&&driver()->primary!=""){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$v=>$X){if(!$Z)$K[$v]=null;$k[$v]=array("field"=>$v,"null"=>($v!=driver()->primary),"auto_increment"=>($v==driver()->primary));}}}if($_POST["save"]){$ji=array();foreach((array)$_POST["fields"]as$v=>$X)$ji[bracket_escape($v,true)]=$X;$K=$ji+($K?$K:array());}edit_form($a,$k,$K,$Al,$i,$H,$Pk);}elseif(isset($_GET["create"])){function
referencable_primary($tj){$J=array();foreach(table_status('',true)as$vk=>$S){if($vk!=$tj&&!$S["dependent"]&&fk_support($S)){foreach(fields($vk)as$j){if($j["primary"]){if($J[$vk]){unset($J[$vk]);break;}$J[$vk]=$j;}}}}return$J;}$a=$_GET["create"];$Oh=driver()->partitionBy;$Sh=($Oh&&$a!=""?driver()->partitionsInfo($a):array());$Gi=referencable_primary($a);$Kd=array();foreach($Gi
as$vk=>$j)$Kd[str_replace("`","``",$vk)."`".str_replace("`","``",$j["field"])]=$vk;$yh=array();$T=array();$Rg=false;if($a!=""){$yh=fields($a);$T=table_status1($a);$Rg=(count($T)<2);}$va=($a==""||driver()->supportsAlterTable($T));$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$i)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$i){if($_POST["drop"])queries_redirect(substr(ME,0,-1),'Table has been dropped.',drop_tables(array($a)));else{$k=array();$sa=array();$Hl=false;$Id=array();$xh=reset($yh);$oa=" FIRST";foreach($K["fields"]as$j){$m=$Kd[$j["type"]];$nl=($m!==null?$Gi[$m]:$j);if($j["field"]!=""){if(!$j["generated"])$j["default"]=null;$vi=process_field($j,$nl);$sa[]=array($j["orig"],$vi,$oa);if(!$xh||$vi!==process_field($xh,$xh)){$k[]=array($j["orig"],$vi,$oa);if($j["orig"]!=""||$oa)$Hl=true;}if($m!==null)$Id[idf_escape($j["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$Kd[$j["type"]],'source'=>array($j["field"]),'target'=>array($nl["field"]),'on_delete'=>$j["on_delete"],));$oa=" AFTER ".idf_escape($j["field"]);}elseif($j["orig"]!=""){$Hl=true;$k[]=array($j["orig"]);}if($j["orig"]!=""){$xh=next($yh);if(!$xh)$oa="";}}$Qh=array();if(in_array($K["partition_by"],$Oh)){foreach($K
as$v=>$X){if(preg_match('~^partition~',$v))$Qh[$v]=$X;}foreach($Qh["partition_names"]as$v=>$B){if($B==""){unset($Qh["partition_names"][$v]);unset($Qh["partition_values"][$v]);}}$Qh["partition_names"]=array_values($Qh["partition_names"]);$Qh["partition_values"]=array_values($Qh["partition_values"]);if($Qh==$Sh)$Qh=array();}elseif(preg_match("~partitioned~",$T["Create_options"]))$Qh=null;$A='Table has been altered.';if($a==""){cookie("adminer_engine",$K["Engine"]);$A='Table has been created.';}$B=trim($K["name"]);$z=ME.(support("table")?"table=":"select=").url_escape($B);$I=alter_table($a,$B,(JUSH=="sqlite"&&($Hl||$Id)?$sa:$k),$Id,($K["Comment"]!=$T["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$T["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$T["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$Qh);if($I&&!Queries::$queries&&$a!=""&&!$k&&!$Id)redirect($z);queries_redirect($z,$A,$I);}}page_header(($a!=""?'Alter table':'Create table'),$i,array("table"=>$a),h($a),$Rg);if(!$_POST){$rl=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($rl["int"])?"int":(isset($rl["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$T;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($yh
as$j){if($j["generated"])$j["default"]=ltrim($j["default"]);$j["generated"]=$j["generated"]?:(isset($j["default"])?"DEFAULT":"");$K["fields"][]=$j;}if($Oh){$K+=$Sh;$K["partition_names"][]="";$K["partition_values"][]="";}}}$sb=flat_collations();$Tc=driver()->engines();foreach($Tc
as$Sc){if(!strcasecmp($Sc,$K["Engine"])){$K["Engine"]=$Sc;break;}}$Wf=max_input_vars(12,20);if($Wf){$re=(count($K["fields"])>$Wf?"":" hidden");echo"<p".($re?" id='max-fields' data-columns='$Wf'":"")." class='error$re'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo'Table name'.": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",(!$va?h($T["Engine"])."\n":($Tc?html_select("Engine",array(""=>"(".'engine'.")")+$Tc,$K["Engine"],on('change','helpClose').on_help_value())."\n":""));if($sb)echo"<datalist id='collations'>".optionlist($sb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".'collation'.")'>\n");echo"<input type='submit' value='".'Save'."'>\n";}if(support("columns")&&$va){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$sb,"TABLE",$Kd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",'Auto Increment'.": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),'Default values',on('click','columnShowClick',6),"jsonly");$yb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$yb,'Comment',on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($yb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$K["Comment"]);}echo'<p>
<input type=\'submit\' value=\'Save\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$a)),'>
';if($Oh&&(JUSH=='sql'||$a=="")){$Ph=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",'Partition by',$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$Oh),$K["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($K["partition"])."'>)\n",'Partitions'.": <input type='number' name='partitions' class='size".($Ph||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($Ph?"":" class='hidden'").">\n","<thead><tr><th>".'Partition name'."<th>".'Values'."<tbody>\n";foreach($K["partition_names"]as$v=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($v==count($K["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$v)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Me=array("PRIMARY","UNIQUE","INDEX");$T=table_status1($a,true);$Je=driver()->indexAlgorithms($T);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$T["Engine"]))$Me[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$T["Engine"]))$Me[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$T["Engine"]))$Me[]="VECTOR";$u=indexes($a);$k=fields($a);$ri=array();if(JUSH=="mongo"){$ri=$u["_id_"];unset($Me[0]);unset($u["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$i&&!$_POST["add"]&&!$_POST["drop_col"]){$ua=array();foreach($K["indexes"]as$t){$B=$t["name"];if(in_array($t["type"],$Me)){$d=array();$Hf=array();$pc=array();$lh=array();$Ke=(support("partial_indexes")?$t["partial"]:"");$Ie=(in_array($t["algorithm"],$Je)?$t["algorithm"]:"");$P=array();ksort($t["columns"]);foreach($t["columns"]as$v=>$c){if($c!=""){$w=idx($t["lengths"],$v);$nc=idx($t["descs"],$v);$kh=idx($t["opclasses"],$v);$P[]=($k[$c]?idf_escape($c):$c).($w?"(".(+$w).")":"").($kh!=""?" ".idf_escape($kh):"").($nc?" DESC":"");$d[]=$c;$Hf[]=($w?:null);$pc[]=$nc;$lh[]="$kh";}}$fd=$u[$B];if($fd){ksort($fd["columns"]);ksort($fd["lengths"]);ksort($fd["descs"]);if($t["type"]==$fd["type"]&&array_values($fd["columns"])===$d&&(!$fd["lengths"]||array_values($fd["lengths"])===$Hf)&&array_values($fd["descs"])===$pc&&(!$fd["opclasses"]||array_values($fd["opclasses"])===$lh)&&$fd["partial"]==$Ke&&(!$Je||$fd["algorithm"]==$Ie)){unset($u[$B]);continue;}}if($d)$ua[]=array($t["type"],$B,$P,$Ie,$Ke);}}foreach($u
as$B=>$fd)$ua[]=array($fd["type"],$B,"DROP");if(!$ua)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),'Indexes have been altered.',alter_indexes($a,$ua));}page_header('Indexes',$i,array("table"=>$a),h($a));$vd=array_keys($k);if($_POST["add"]){foreach($K["indexes"]as$v=>$t){if($t["columns"][count($t["columns"])]!="")$K["indexes"][$v]["columns"][]="";}$t=end($K["indexes"]);if($t["type"]||array_filter($t["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($u
as$v=>$t){$u[$v]["name"]=$v;$u[$v]["columns"][]="";}$u[]=array("columns"=>array(1=>""));$K["indexes"]=$u;}$Hf=(JUSH=="sql"||JUSH=="mssql");$lh=driver()->indexOpclasses();$Lj=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">Index Type
';$Be=" class='idxopts".($Lj?"":" hidden")."'";if($Je)echo"<th id='label-algorithm'$Be>".'Algorithm'.doc_link(array('pgsql'=>'indexes-types.html','cockroach'=>'create-index#parameters',));echo'<th><input type="submit" hidden>','Columns'.($Hf?"<span$Be> (".'length'.")</span>":"");if($Hf||support("descidx"))echo
checkbox("options",1,$Lj,'Options',on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">Name
';if(support("partial_indexes"))echo"<th id='label-condition'$Be>".'Condition';echo'<td><noscript>',icon("plus","add[0]","+",'Add next'),'</noscript>
<tbody>
';if($ri){echo"<tr><td>PRIMARY<td>";foreach($ri["columns"]as$v=>$c)echo
select_input(" disabled",array_combine($vd,$vd),$c),"<label><input disabled type='checkbox'>".'descending'."</label> ";echo"<td><td>\n";}$lf=1;foreach($K["indexes"]as$t){if(!$_POST["drop_col"]||$lf!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$lf][type]",array(-1=>"")+$Me,$t["type"],($lf==count($K["indexes"])?on('change','indexesAddRow'):""),"label-type");if($Je)echo"<td$Be>".html_select("indexes[$lf][algorithm]",array_merge(array(""),$Je),$t['algorithm'],"","label-algorithm");echo"<td>";ksort($t["columns"]);$q=1;foreach($t["columns"]as$v=>$c){echo"<span>".select_input(" name='indexes[$lf][columns][$q]' title='".'Column'."'".on('change','indexesChangeColumn',(JUSH=="sql"?"":$_GET["indexes"]."_")),($k&&($c==""||$k[$c])?array_combine($vd,$vd):array()),$c)," <span$Be>",($Hf?"<input type='number' name='indexes[$lf][lengths][$q]' class='size' value='".h(idx($t["lengths"],$v))."' title='".'Length'."'>":"");if($lh){$kh=idx($t["opclasses"],$v);echo
html_select("indexes[$lf][opclasses][$q]",array(""=>"(".'operator class'.")")+array_combine($lh,$lh)+($kh!=""?array($kh=>$kh):array()),$kh),doc_link(array('pgsql'=>'indexes-opclass.html'));}echo(support("descidx")?checkbox("indexes[$lf][descs][$q]",1,idx($t["descs"],$v),'descending'):""),"<br>","</span></span>";$q++;}echo"<td><input name='indexes[$lf][name]' value='".h($t["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Be><input name='indexes[$lf][partial]' value='".h($t["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$lf]","x",'Remove',on('click','editingRemoveRow','indexes$1[type]'));}$lf++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'Save\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$i&&!$_POST["add"]){$B=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'Database has been dropped.',drop_databases(array(DB)));}elseif($B!==DB){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($B),'Database has been renamed.',rename_database($B,(string)$K["collation"]));}else{$g=explode("\n",str_replace("\r","",$B));$ik=true;$zf="";foreach($g
as$h){if(count($g)==1||$h!=""){if(!create_database($h,(string)$K["collation"]))$ik=false;$zf=$h;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($zf),'Database has been created.',$ik);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),'Database has been altered.');}}page_header(DB!=""?'Alter database':'Create database',$i,array(),h(DB));$sb=collations();$B=DB;if($_POST)$B=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$sb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Vd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Vd,$_)&&$_[1]){$B=stripcslashes(idf_unescape("`$_[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n",($sb?html_select("collation",array(""=>"(".'collation'.")")+$sb,$K["collation"]).'':"")."\n",'<input type=\'submit\' value=\'Save\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",'Add next')."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$K=$_POST;if($_POST&&!$i){$y=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$y,'Schema has been dropped.');else{$B=trim($K["name"]);$y
.=url_escape($B);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($B),$y,'Schema has been created.');elseif($_GET["ns"]!=$B)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($B),$y,'Schema has been altered.');else
redirect($y);}}page_header($_GET["ns"]!=""?'Alter schema':'Create schema',$i);if(!$K)$K["name"]=$_GET["ns"];echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($K["name"]),'" autocapitalize="off">
<input type=\'submit\' value=\'Save\'>
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',$_GET["ns"])).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$aa=($_GET["name"]?:$_GET["call"]);$cj=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$Yi=routine($_GET["call"],$cj);page_header('Call'.": ".h($aa),$i,"#routines","",!$Yi);$Ee=array();$Ch=array();foreach($Yi["fields"]as$q=>$j){if(substr($j["inout"],-3)=="OUT"&&JUSH=='sql')$Ch[$q]="@".idf_escape($j["field"])." AS ".idf_escape($j["field"]);if(!$j["inout"]||preg_match('~^(IN|OUTPUT)~',$j["inout"]))$Ee[]=$q;}if(!$i&&$_POST){$Ya=array();foreach($Yi["fields"]as$v=>$j){$X="";if(in_array($v,$Ee)){$X=process_input($j);if($X===false)$X="''";if(isset($Ch[$v]))connection()->query("SET @".idf_escape($j["field"])." = $X");}if(isset($Ch[$v]))$Ya[]="@".idf_escape($j["field"]);elseif(in_array($v,$Ee))$Ya[]=$X;}$za=implode(", ",$Ya);$H=(isset($_GET["callf"])||JUSH!="mssql"?(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($Yi["returns"],"type")=="record"?"* FROM ":"").table($aa)."($za)":"EXEC ".table($aa).($za!=""?" $za":""));$bk=microtime(true);$I=connection()->multi_query($H);$ma=connection()->affected_rows;echo
adminer()->selectQuery($H,$bk,!$I);if(!$I)echo"<p class='error'>".adminer()->error()."\n";else{$f=connect();if($f)$f->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$f);else
echo"<p class='message'>".lang_format(array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),$ma)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Ch)print_select_result(connection()->query("SELECT ".implode(", ",$Ch)));}}echo'
<form action="" method="post">
';if($Ee){echo"<table class='layout'>\n";foreach($Ee
as$v){$j=$Yi["fields"][$v];$B=$j["field"];echo"<tr><th>".adminer()->fieldName($j);$Y=idx($_POST["fields"],$B);if($Y!=""){if($j["type"]=="set")$Y=implode(",",$Y);}input($j,$Y,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'Call\'>
',input_token(),'</form>

',adminer()->commentValue($cj,$Yi['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$K=$_POST;if($_POST&&!$i&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$Gk=array();foreach($K["source"]as$v=>$X)$Gk[$v]=$K["target"][$v];$K["target"]=$Gk;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $B"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$ua="ALTER TABLE ".table($a);$I=($B==""||queries("$ua DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B)));if(!$K["drop"])$I=queries("$ua ADD".format_foreign_key($K));}queries_redirect(ME."table=".url_escape($a),($K["drop"]?'Foreign key has been dropped.':($B!=""?'Foreign key has been altered.':'Foreign key has been created.')),$I);if(!$K["drop"])$i='Source and target columns must have the same data type, there must be an index on the target columns and the referenced data must exist.';}$Rg=false;if(!$_POST&&$B!=""){$Kd=foreign_keys($a);$K=idx($Kd,$B,array());$Rg=!$K;}page_header(($B!=""?'Alter foreign key':'Create foreign key'),$i,array("table"=>$a),h($B!=""?$B:$a),$Rg);if($_POST){ksort($K["source"]);if($_POST["change"]||$_POST["change-js"])$K["target"]=array();else$K["source"][]="";}elseif($B!="")$K["source"][]="";else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$Sj=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$zh=get_schema();set_schema($K["ns"]);}$Fi=array_keys(array_filter(table_status('',true),function(array$T){return!$T["dependent"]&&fk_support($T);}));$Gk=array_keys(fields(in_array($K["table"],$Fi)?$K["table"]:reset($Fi)));$b=on('change','foreignChange');echo"<p><label>".'Target table'.": ".html_select("table",$Fi,$K["table"],$b)."</label>\n";if(support("scheme")){$hj=array_filter(adminer()->schemas(),function($M){return!information_schema(DB,$M);});echo"<label>".'Schema'.": ".html_select("ns",$hj,$K["ns"]!=""?$K["ns"]:$_GET["ns"],$b)."</label>";if($K["ns"]!="")set_schema($zh);}elseif(JUSH!="sqlite"){$ec=array();foreach(adminer()->databases()as$h){if(!information_schema($h))$ec[]=$h;}echo"<label>".'DB'.": ".html_select("db",$ec,$K["db"]!=""?$K["db"]:$_GET["db"],$b)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'Change\'></noscript>
<table>
<thead><tr><th id="label-source">Source<th id="label-target">Target<tbody>
';$lf=0;foreach($K["source"]as$v=>$X){echo"<tr>","<td>".html_select("source[".(+$v)."]",array(-1=>"")+$Sj,$X,($lf==count($K["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$v)."]",$Gk,idx($K["target"],$v),"","label-target");$lf++;}echo'</table>
<p>
<label>ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$K["deferrable"]).' ':''),doc_link(array('pgsql'=>"sql-createtable.html#SQL-CREATETABLE-PARMS-REFERENCES",'cockroach'=>"foreign-key",)),'<p>
<input type=\'submit\' value=\'Save\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'Add column\'></noscript>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$_h="VIEW";if(JUSH=="pgsql"&&$a!=""){$Q=table_status1($a);$_h=strtoupper($Q["Engine"]);}if($_POST&&!$i){$B=trim($K["name"]);$Aa=" AS\n$K[select]";$z=ME."table=".url_escape($B);$A='View has been altered.';$V=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$V=="VIEW"&&$_h=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$Aa,$z,$A);else{$Kk="adminer_".uniqid();drop_create("DROP $_h ".table($a),"CREATE $V ".table($B).$Aa,"DROP $V ".table($B),"CREATE $V ".table($Kk).$Aa,"DROP $V ".table($Kk),($_POST["drop"]?substr(ME,0,-1):$z),'View has been dropped.',$A,'View has been created.',$a,$B);}}$Rg=false;if(!$_POST&&$a!=""){$K=view($a);$Rg=!$K["select"];$K["name"]=$a;$K["materialized"]=($_h!="VIEW");if(!$i)$i=adminer()->error();}page_header(($a!=""?'Alter view':'Create view'),$i,array("table"=>$a),h($a),$Rg);echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],'Materialized view'):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$aa=($_GET["name"]?:$_GET["procedure"]);$Yi=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$i){foreach($K["fields"]as$v=>$j){if($j["field"]=="")unset($K["fields"][$v]);}$fh=routine($_GET["procedure"],$Yi);$dh=($fh?routine_id($aa,$fh):"");$Jg=routine_id($K["name"],$K);$Ob=create_routine($Yi,$K);$z=substr(ME,0,-1);$A='Routine has been altered.';if(!$_POST["drop"]&&$dh==$Jg&&connection()->flavor!="mysql")queries_redirect($z,$A,queries(substr_replace($Ob,(JUSH=="mssql"?' OR ALTER':' OR REPLACE'),6,0)));else{$Kk="adminer_".uniqid();drop_create("DROP $Yi $dh",$Ob,"DROP $Yi $Jg",create_routine($Yi,array("name"=>$Kk)+$K),"DROP $Yi ".routine_id($Kk,$K),$z,'Routine has been dropped.',$A,'Routine has been created.',$aa,$K["name"]);}}$Rg=false;if(!$_POST&&$aa!=""){$K=routine($_GET["procedure"],$Yi);$Rg=!$K;$K["name"]=$aa;}page_header(($aa!=""?(isset($_GET["function"])?'Alter function':'Alter procedure').": ".h($aa):(isset($_GET["function"])?'Create function':'Create procedure')),$i,"#routines","",$Rg);if(!$_POST&&$aa=="")$K["language"]="sql";$sb=(JUSH=="sql"?flat_collations():array());$Zi=routine_languages();echo($sb?"<datalist id='collations'>".optionlist($sb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($Zi?"<label>".'Language'.": ".html_select("language",array_keys($Zi),$K["language"],on('change','routineLanguage',$Zi))."</label>\n":""),'<input type=\'submit\' value=\'Save\'>
';$aj=strtolower($Yi);echo
doc_link(array('pgsql'=>"sql-create$aj.html",'cockroach'=>"create-$aj",),"?"),'<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($K["fields"],$sb,$Yi);if(isset($_GET["function"])){echo"<tr><td>".'Return type';edit_type("returns",(array)$K["returns"],$sb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20,80,($Zi[$K["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$aa)),'>
';$bj=routine_options($Yi);if($bj){$qh=false;foreach($bj
as$v=>$Ul){$gc=($Ul?reset($Ul):"");$K["options"][$v]=idx($K["options"],$v,$gc);if($K["options"][$v]!=$gc)$qh=true;}print_fieldset("options",'Options',$qh);echo"<table class='layout'>\n";foreach($bj
as$v=>$Ul){$vf="label-option-$v";$Sk=str_replace("_"," ",$v);$N=array();foreach($Ul
as$Y)$N[$Y]=(strpos($Y,"$Sk ")===0?substr($Y,strlen($Sk)+1):$Y);echo"<tr><th id='$vf'>$Sk<td>".($N?html_select("options[$v]",$N,$K["options"][$v],"",$vf):"<input name='options[$v]' value='".h($K["options"][$v])."' aria-labelledby='$vf' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$ca=$_GET["sequence"];$K=$_POST;if($_POST&&!$i){$y=substr(ME,0,-1);$B=trim($K["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($ca),$y,'Sequence has been dropped.');elseif($ca=="")query_redirect("CREATE SEQUENCE ".idf_escape($B),$y,'Sequence has been created.');elseif($ca!=$B)query_redirect("ALTER SEQUENCE ".idf_escape($ca)." RENAME TO ".idf_escape($B),$y,'Sequence has been altered.');else
redirect($y);}$Rg=(!$_POST&&$ca!=""&&!get_val("SELECT relname FROM pg_class WHERE relkind = 'S' AND relnamespace = ".driver()->nsOid." AND relname = ".q($ca)));page_header(($ca!=""?'Alter sequence'.": ".h($ca):'Create sequence'),$i,"#sequences","",$Rg);if(!$K)$K["name"]=$ca;echo'
<form action="" method="post">
<p><input name="name" value="',h($K["name"]),'" autocapitalize="off">
<input type=\'submit\' value=\'Save\'>
';if($ca!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',$ca)).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){function
enum_values($lc){$Y="'(?:[^']|'')*'";if(!preg_match('~^AS\s+ENUM\s*\(\s*('.$Y.'(?:\s*,\s*'.$Y.')*)\s*\)$~i',$lc,$_))return
null;preg_match_all('~'.$Y.'~',$_[1],$Tf);return$Tf[0];}function
add_enum_values($V,$ch,$Ig){$gh=enum_values($ch);$Ng=enum_values($Ig);if($gh===null||$Ng===null)return
null;$J=array();$q=0;foreach($Ng
as$Y){if($Y===idx($gh,$q))$q++;else$J[]="ALTER TYPE ".idf_escape($V)." ADD VALUE $Y".($q<count($gh)?" BEFORE ".$gh[$q]:"");}return($q==count($gh)?$J:null);}$da=$_GET["type"];$K=$_POST;$ol=($da!=""?array_search($da,types(true)):0);$V=($ol?type_definition(+$ol):array());$Wg=($V["kind"]=='d'?"DOMAIN":"TYPE");if($_POST&&!$i){$y=substr(ME,0,-1);$B=trim($K["name"]);$Aa=trim(str_replace("\r","",$K["as"]));$Lg=(preg_match('~^AS\s+(?!ENUM\b|RANGE\b|\()~i',$Aa)?"DOMAIN":"TYPE");$A='Type has been altered.';$ua=(!$_POST["drop"]&&$da!=""&&$Lg==$Wg?($Aa==$V["definition"]?array():add_enum_values($da,$V["definition"],$Aa)):null);if($ua!==null){if($da!=$B)$ua[]="ALTER $Wg ".idf_escape($da)." RENAME TO ".idf_escape($B);if(!$ua)redirect($y);$od=false;foreach($ua
as$H){if(!queries($H)){$od=true;break;}}queries_redirect($y,$A,!$od);}else
drop_create("DROP $Wg ".idf_escape($da),"CREATE $Lg ".idf_escape($B)." $Aa","","","",$y,'Type has been dropped.',$A,'Type has been created.',$da,$B);}page_header(($da!=""?'Alter type'.": ".h($da):'Create type'),$i,"#user-types","",($ol===false));if(!$K){$K["name"]=$da;$K["as"]=($da!=""?$V["definition"]:"AS ");}echo'
<form action="" method="post">
<p>
','Name'.": <input name='name' value='".h($K['name'])."' autocapitalize='off'>\n",doc_link(array('pgsql'=>"sql-createtype.html",'cockroach'=>"create-type",),"?");textarea("as",$K["as"]);echo"<p><input type='submit' value='".'Save'."'>\n";if($da!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',$da)).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B="$_GET[name]";$K=$_POST;if($K&&!$i){$z=ME."table=".url_escape($a);$ng='Check has been dropped.';$lg='Check has been altered.';$mg='Check has been created.';if(JUSH=="sqlite")queries_redirect($z,($K["drop"]?$ng:($B!=""?$lg:$mg)),recreate_table($a,$a,array(),array(),array(),"",array(),"$B",($K["drop"]?"":$K["clause"])));else{$ua="ALTER TABLE ".table($a);$eb=" CHECK ($K[clause])";$Kk="adminer_".uniqid();drop_create("$ua DROP CONSTRAINT ".idf_escape($B),"$ua ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"").$eb,"$ua DROP CONSTRAINT ".idf_escape($K["name"]),"$ua ADD CONSTRAINT ".idf_escape($Kk).$eb,"$ua DROP CONSTRAINT ".idf_escape($Kk),$z,$ng,$lg,$mg,$B,$K["name"]);}}$Rg=false;if(!$K){$ib=driver()->checkConstraints($a);$Rg=($B!=""&&!$ib[$B]);$K=array("name"=>$B,"clause"=>$ib[$B]);}page_header(($B!=""?'Alter check':'Create check'),$i,array("table"=>$a),h($B!=""?$B:$a),$Rg);echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo'Name'.': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'cockroach'=>"check",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type=\'submit\' value=\'Save\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$jl=trigger_options();$K=trigger($B,$a);$Rg=($B!=""&&!$K);$K+=array("Trigger"=>$a."_bi");if($_POST){if(!$i&&in_array($_POST["Timing"],$jl["Timing"])&&in_array($_POST["Event"],$jl["Event"])&&in_array($_POST["Type"],$jl["Type"])){$hh=" ON ".table($a);$Fc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$hh:"");$z=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Fc,$z,'Trigger has been dropped.');else{if($B!="")queries($Fc);queries_redirect($z,($B!=""?'Trigger has been altered.':'Trigger has been created.'),queries(create_trigger($hh,$_POST)));if($B!="")queries(create_trigger($hh,$K+array("Type"=>reset($jl["Type"]))));}}$K=$_POST;}page_header(($B!=""?'Alter trigger':'Create trigger'),$i,array("table"=>$a),h($B!=""?$B:$a),$Rg);$il=on('change','triggerChange',"^".preg_quote($a,"/")."_[ba][iud]$",$a);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>Time
<td>',html_select("Timing",$jl["Timing"],$K["Timing"],$il),'<tr><th>Event<td>',html_select("Event",$jl["Event"],$K["Event"],$il),(in_array("UPDATE OF",$jl["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>Type<td>',html_select("Type",$jl["Type"],$K["Type"]),'<tr><th>Name<td><input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$i){$sf=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$sf++;}queries_redirect(ME."processlist=",lang_format(array('%d process has been killed.','%d processes have been killed.'),$sf),$sf||!$_POST["kill"]);}}page_header('Process list',$i);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$q=-1;foreach(adminer()->processList()as$q=>$K){if(!$q){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($K
as$v=>$X)echo"<th>$v".doc_link(array('pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$v=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$v=="Info"&&preg_match("~Query|Killed~",$K["Command"]))||(JUSH=="pgsql"&&$v=="query")||(JUSH=="oracle"&&$v=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(($K["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($K["db"])."&":ME)."sql=".url_escape($X)).'">'.'Clone'.'</a>'.' '.copy_icon():h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($q+1)."/".sprintf('%d in total',max_connections()),"<p><input type='submit' value='".'Kill'."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$T=table_status1($a);$u=indexes($a);$k=fields($a);$Kd=column_foreign_keys($a);$bh=$T["Oid"];$Wi=array();$d=array();$lj=array();$sh=array();$Nk=null;foreach($k
as$v=>$j){$B=adminer()->fieldName($j);$Eg=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($j["privileges"]["select"])&&$B!=""){$d[$v]=$Eg;if(is_shortable($j))$Nk=adminer()->selectLengthProcess();}if(isset($j["privileges"]["where"])&&$B!="")$lj[$v]=$Eg;if(isset($j["privileges"]["order"])&&$B!="")$sh[$v]=$Eg;$Wi+=$j["privileges"];}list($N,$p)=adminer()->selectColumnsProcess($d,$u);$N=array_unique($N);$p=array_unique($p);$gf=count($p)<count($N);$Z=adminer()->selectSearchProcess($k,$u,$T);$D=adminer()->selectOrderProcess($k,$u);$x=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$wl=>$K){$Aa=convert_field($k[key($K)]);$N=array($Aa?:idf_escape(key($K)));$Z[]=where_check(bracket_escape($wl,true),$k);$J=driver()->select($a,$N,$Z,$N);if($J)echo
first($J->fetch_row());}exit;}$ri=$yl=array();foreach($u
as$t){if($t["type"]=="PRIMARY"){$ri=array_flip($t["columns"]);$yl=($N?$ri:array());foreach($yl
as$v=>$X){if(in_array(idf_escape($v),$N))unset($yl[$v]);}break;}}if($bh&&!$ri){$ri=$yl=array($bh=>0);$u[]=array("type"=>"PRIMARY","columns"=>array($bh));}if($_POST&&!$i){$fm=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ib=array();foreach($_POST["check"]as$eb)$ib[]=where_check($eb,$k);$fm[]="((".implode(") OR (",$ib)."))";}$hm=$fm;$fm=($fm?"\nWHERE ".implode(" AND ",$fm):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$pj=($N?:array("*"));$Kb=convert_fields($d,$k,$N);if($Kb)$pj[]=substr($Kb,2);$H="";if(is_array($_POST["check"])&&!$ri){$Pd=implode(", ",$pj)."\nFROM ".table($a);$Yd=($p&&$gf?"\nGROUP BY ".implode(", ",$p):"").($D?"\nORDER BY ".implode(", ",$D):"");$tl=array();foreach($_POST["check"]as$X)$tl[]="(SELECT".limit($Pd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$k).$Yd,1).")";$H=implode(" UNION ALL ",$tl);}adminer()->dumpData($a,"table",$H,$pj,$hm,($gf?$p:array()),$D);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Kd)){if($_POST["save"]||$_POST["delete"]){$I=true;$ma=0;$Oa=false;$P=array();if(!$_POST["delete"]){foreach($k
as$B=>$X){$s=bracket_escape($B);if(isset($_POST["fields"][$s])||$_FILES["fields-$s"]){$X=process_input($k[$B]);if($X!==null&&($_POST["clone"]||$X!==false))$P[idf_escape($B)]=($X!==false?$X:idf_escape($B));}}}if($_POST["delete"]||$P){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($P)).")\nSELECT ".implode(", ",$P)."\nFROM ".table($a):"");if($_POST["all"]||($ri&&is_array($_POST["check"]))||$gf){$I=($_POST["delete"]?driver()->delete($a,$fm):($_POST["clone"]?queries("INSERT $H$fm".driver()->insertReturning($a)):driver()->update($a,$P,$fm)));$ma=connection()->affected_rows;if(is_object($I))$ma+=$I->num_rows;}else{$Oa=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$em="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$k);$I=($_POST["delete"]?driver()->delete($a,$em,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$em)):driver()->update($a,$P,$em,1)));if(!$I)break;$ma+=connection()->affected_rows;}if($Oa&&$I&&!driver()->commit())$I=false;}}$A=lang_format(array('%d item has been affected.','%d items have been affected.'),$ma);if($_POST["clone"]&&$I&&$ma==1){$Af=last_id($I);if($Af)$A=sprintf('Item%s has been inserted.'," $Af");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$A,$I);if($Oa)driver()->rollback();if(!$_POST["delete"]){$ji=(array)$_POST["fields"];edit_form($a,array_intersect_key($k,$ji),$ji,!$_POST["clone"],$i);page_footer();exit;}}elseif(!$_POST["import"]){$I=true;$ma=0;$Oa=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$wl=>$K){$P=array();foreach($K
as$v=>$X){$v=bracket_escape($v,true);$P[idf_escape($v)]=(preg_match('~char|text~',$k[$v]["type"])||$X!=""?adminer()->processInput($k[$v],$X):"NULL");}$I=driver()->update($a,$P," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($wl,true),$k),($gf||$ri?0:1)," ");if(!$I)break;$ma+=connection()->affected_rows;}if($Oa)$I=$I&&driver()->commit();queries_redirect(remove_from_uri(),lang_format(array('%d item has been affected.','%d items have been affected.'),$ma),$I);if($Oa)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$wd=get_file("csv_file",true);if(!is_string($wd))$i=upload_error($wd);elseif(!preg_match('~~u',$wd))$i='File must be in UTF-8 encoding.';else{$tb=array_keys($k);$vj=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Tb=parse_csv($wd,$vj);$ma=count($Tb);driver()->begin();$L=array();foreach($Tb
as$v=>$Ul){if(!$v&&!array_diff($Ul,$tb)){$tb=$Ul;$ma--;}else{$P=array();foreach($Ul
as$q=>$pb)$P[idf_escape($tb[$q])]=($pb==""&&$k[$tb[$q]]["null"]?"NULL":q(csv_value($pb)));$L[]=$P;}}$I=(!$L||driver()->insertUpdate($a,$L,$ri));if($I)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang_format(array('%d row has been imported.','%d rows have been imported.'),$ma),$I);driver()->rollback();}}}}$vk=adminer()->tableName($T);if(is_ajax()){page_headers();ob_start();}else
page_header('Select'.": $vk",$i,array(),"",(!$k&&support("table")));$P=null;if(isset($Wi["insert"])||!support("table")){$P="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$_)?$_[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$P
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($T,$P);if(!$d&&support("table"))echo"<p class='error'>".'Unable to select the table.'."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($N,$d);adminer()->selectSearchPrint($Z,$lj,$u,$T);adminer()->selectOrderPrint($D,$sh,$u);adminer()->selectLimitPrint($x);if($Nk!==null)adminer()->selectLengthPrint($Nk);adminer()->selectActionPrint($u);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".'Invalid CSRF token. Submit the form again.'.' '.'If you did not send this request from Adminer, close this page.'."\n";page_footer();exit;}}$E=$_GET["page"];$Nd=null;if($E=="last"){$Nd=get_val(count_rows($a,$Z,$gf,$p));$E=floor(max(0,intval($Nd)-1)/$x);}$oj=$N;$Xd=$p;if(!$oj){$oj[]="*";$Kb=convert_fields($d,$k,$N);if($Kb)$oj[]=substr($Kb,2);}foreach($N
as$v=>$X){$j=$k[idf_unescape($X)];if($j&&($Aa=convert_field($j)))$oj[$v]="$Aa AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$v=>$X){if(isset($oj[$v])&&$X["fun"])$oj[$v].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$gf&&$yl){foreach($yl
as$v=>$X){$oj[]=idf_escape($v);if($Xd)$Xd[]=idf_escape($v);}}$I=driver()->select($a,$oj,$Z,$Xd,$D,$x,$E,true);if(!is_object($I))echo"<p class='error'>".(adminer()->error()?:'Unknown error.')."\n";else{if(JUSH=="mssql"&&$E)$I->seek($x*$E);$Qc=array();$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}$ie=($x&&(support("cursor")?$_GET["next"]!="":count($L)>=$x));if(is_ajax()&&$ie)header("X-Next-Page: ".pagination_href($E+1));if($_GET["modify"]&&$L){$cg=max_input_vars(count($L[0])+1,20);echo($cg&&count($L)>$cg?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($Dl).">\n";if($_GET["page"]!="last"&&$x&&$p&&$gf&&JUSH=="sql")$Nd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'No rows.'."\n";else{$Ka=adminer()->backwardKeys($a,$vk);$Ti=array();reset($N);foreach($L[0]as$v=>$X){if(!isset($yl[$v])){$X=idx($_GET["columns"],key($N))?:array();$Ti[$v]=array("fun"=>$X["fun"],"col"=>($N?$X["col"]:$v));next($N);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$p&&$N?"":"<td class='hover check'><input type='checkbox' id='all-page' class='jsonly' title='".'All rows on this page'."'".on('click','formCheck','^check').">");$Fg=array();$Ci=1;foreach($Ti
as$v=>$X){$j=$k[$X["col"]];$B=($j?adminer()->fieldName($j,$Ci):($X["fun"]?"*":h($v)));if($B!=""){$Ci++;$Fg[$v]=$B;$c=idf_escape($v);$we=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($v);$nc="&desc[0]=1";$Pj=preg_replace('~ DESC( NULLS LAST)?$~','',$D[0]);$Rj=($Pj==$c||$Pj==$v);echo"<th id='th[".h(bracket_escape($v))."]'".($Rj?" aria-sort='".($Pj==$D[0]?"ascending":"descending")."'":"").">";$Sd=apply_sql_function(h($X["fun"]),$B);$Qj=isset($j["privileges"]["order"])||$X["fun"];echo($Qj?"<a href='".h($we.($Rj&&$Pj==$D[0]?$nc:''))."'>$Sd</a>":$Sd);$kg=($Qj?"<a href='".h($we.$nc)."' title='".'descending'."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($j["privileges"]["where"]))$kg
.="<a href='#fieldset-search' title='".'Search'."' class='text jsonly'".on('click','selectSearch',$v)."> =</a>";echo($kg?"<span class='column'>$kg</span>":"");}}$Hf=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$v=>$X)$Hf[$v]=max($Hf[$v],min(40,utf8_length($X)));}}echo($Ka?"<th>".'Relations':"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$Kd)as$Cg=>$K){$vl=unique_array($L[$Cg],$u);if(!$vl){$vl=array();foreach($L[$Cg]as$v=>$X){if(!in_array(idx(idx($Ti,$v,array()),"fun"),driver()->grouping))$vl[$v]=$X;}}$wl="";$q=0;foreach($vl
as$v=>$X){$Si=idx($Ti,$v,array());$Sd=idx($Si,"fun","");$pb=($Sd?$Si["col"]:$v);$j=(array)$k[$pb];$ff=is_blob($j);if(!$Sd&&(JUSH=="sql"||JUSH=="pgsql")&&($ff||preg_match('~'.text_type().'~',$j["type"]))&&strlen($X)>64){$Sd="md5";$X=md5($ff?(string)driver()->value($X,$j):$X);}if($Sd){$wl
.="&fun[$q]=".url_escape($Sd)."&col[$q]=".url_escape($pb).($X!==null?"&val[$q]=".url_escape($X===false?"f":$X):"");$q++;}else$wl
.="&".($X!==null?"where[".url_escape(bracket_escape($pb))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($pb));}echo"<tr>".(!$p&&$N?"":"<td class='hover check'>".($gf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$wl)."' class='edit'>".'edit'."</a> ").checkbox("check[]",substr($wl,1),in_array(substr($wl,1),(array)$_POST["check"])));foreach($K
as$v=>$X){if(isset($Fg[$v])){$Sd=$Ti[$v]["fun"];$pb=$Ti[$v]["col"];$j=(array)$k[$v];if($X!=""&&(!isset($Qc[$v])||$Qc[$v]!=""))$Qc[$v]=(is_mail($X)?$Fg[$v]:"");$y="";if(is_blob($j)&&$X!="")$y=ME.'download='.url_escape($a).'&field='.url_escape($v).$wl;if(!$y&&$X!==null){foreach((array)$Kd[$v]as$m){if(count($Kd[$v])==1||end($m["source"])==$v){$y="";foreach($m["source"]as$q=>$Sj)$y
.=where_link($q,$m["target"][$q],$L[$Cg][$Sj]);$y=($m["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($m["db"]),ME):ME).'select='.url_escape($m["table"]).$y;if($m["ns"])$y=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($m["ns"]),$y);if(count($m["source"])==1)break;}}}if($Sd=="count"&&$pb==""){$y=ME."select=".url_escape($a);$q=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$vl))$y
.=where_link($q++,$W["col"],$W["val"],$W["op"]);}foreach($vl
as$of=>$W){if(idx(idx($Ti,$of,array()),"fun")){$y="";break;}$y
.=where_link($q++,$of,$W);}}$xe=select_value($X,$y,$j,$Nk);$s=bracket_escape($wl);$r=h("val[$s][".bracket_escape($v)."]");$li=idx(idx($_POST["val"],$s),bracket_escape($v));$Al=idx($j["privileges"],"update");$Mc=!is_array($K[$v])&&!is_blob($j)&&is_utf8($X)&&$L[$Cg][$v]==$X&&!$Sd&&!$j["generated"]&&$Al;$V=($Sd=="min"||$Sd=="max"?$k[$pb]["type"]:$j["type"]);$Mk=preg_match('~text|json|lob~',$V);$hf=preg_match(number_type(),$V)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$Sd);echo"<td id='$r'".($hf&&($X===null||is_numeric(strip_tags($xe))||$V=="money")?" class='number'":"");if(($_GET["modify"]&&$Mc&&$X!==null)||$li!==null){$de=h($li!==null?$li:$X);echo">".($Mk?"<textarea name='$r' cols='30' rows='".(substr_count($X,"\n")+1)."'>$de</textarea>":"<input name='$r' value='$de' size='$Hf[$v]'>");}else{$Qf=strpos($xe,"<i>…</i>");echo($Al?" data-text='".($Qf?2:($Mk?1:0))."'".($Mc?"":" data-warning='".'Use the edit link to modify this value.'."'"):"").">$xe";}}}if($Ka)echo"<td>";adminer()->backwardKeysPrint($Ka,$L[$Cg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$la=get_settings("adminer_import");if($L||$E||$ie){$dd=true;if($_GET["page"]!="last"){if(!$x||(count($L)<$x&&($L||!$E)))$Nd=($E?$E*$x:0)+count($L);elseif(JUSH!="sql"||!$gf){$Nd=($gf?false:found_rows($T,$Z));if(intval($Nd)<max(1e4,2*($E+1)*$x))$Nd=first(slow_query(count_rows($a,$Z,$gf,$p)));elseif(JUSH=='sql'||JUSH=='pgsql')$dd=false;}}if(!support("cursor"))$ie=(($Nd===false?count($L)+1:$Nd-$E*$x)>$x);$Fh=($x&&($ie||$E));if($Fh)echo($ie?'<p><a href="'.h(pagination_href($E+1)).'" class="loadmore"'.on('click','selectLoadMore','Loading…').'>'.'Load more data'.'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Fh){$ag=($Nd===false?$E+($L?(count($L)>=$x?2:1):0):floor(($Nd-1)/$x));echo"<fieldset><legend>".'Page'."</legend>";if(!support("cursor")){echo
pagination(0,$E).($E>5?" …":"");for($q=max(1,$E-4);$q<min($ag,$E+5);$q++)echo
pagination($q,$E);if($ag>0)echo($E+5<$ag?" …":""),($dd&&$Nd!==false?pagination($ag,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$ag'>".'last'."</a>");}else
echo
pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($ie?pagination($E+1,$E)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$vc=($dd?"":"~ ").$Nd;$vf=($Nd!==false?($dd?"":"~ ").lang_format(array('%d row','%d rows'),$Nd):"");echo
checkbox("all",1,0,$vf,on('click','countRows',$vc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".'Ctrl+click on a value to modify it.'."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>Modify</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'Save\'',($_GET["modify"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'Edit\'>
<input type=\'submit\' name=\'clone\' value=\'Clone\'>
<input type=\'submit\' name=\'delete\' value=\'Delete\'',confirm(),'>
</div></fieldset>
';$Ld=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]){unset($Ld['sql']);break;}}if($Ld){print_fieldset("export",'Export'." <span id='selected2'></span>");$Dh=adminer()->dumpOutput();echo($Dh?html_select("output",$Dh,$la["output"])." ":""),html_select("format",$Ld,$la["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Qc,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".'Import'."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($Dl?input_hidden(ini_get("session.upload_progress.name"),$Dl):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$la["format"])." <input type='submit' name='import' value='".'Import'."'>".($Dl?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$p&&$N?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$Q=isset($_GET["status"]);page_header($Q?'Status':'Variables');$Vl=($Q?adminer()->showStatus():adminer()->showVariables());if(!$Vl)echo"<p class='message'>".'No rows.'."\n";else{echo"<table>\n";foreach($Vl
as$K){echo"<tr>";$v=array_shift($K);echo"<th><code class='jush-".JUSH.($Q?"status":"set")."'>".h($v)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$lk=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$T){json_row("Comment-$B",h($T["Comment"]).($T["Error"]?" <span class='error'>".h($T["Error"])."</span>":""));if(!is_view($T)||preg_match('~materialized~i',$T["Engine"])){foreach(array("Engine","Collation")as$v)json_row("$v-$B",h($T[$v]));foreach(array_keys($lk+array("Auto_increment"=>0,"Rows"=>0))as$v){if(array_key_exists($v,$T))json_row("$v-$B",format_status($T,$v));if($T[$v]!=""&&isset($lk[$v]))$lk[$v]+=($T["Engine"]!="InnoDB"||$v!="Data_free"?$T[$v]:0);}}}if(function_exists('Adminer\db_status'))$lk=db_status();foreach($lk
as$v=>$X)json_row("sum-$v",format_number($X));json_row("");}elseif($_GET["script"]=="kill"){if(!$i)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$h=>$X){json_row("tables-$h",format_number($X));json_row("size-$h",db_size($h));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$U=tables_list();if($U)redirect(ME.(support("table")?"table=":"select=").url_escape(key($U)));}$hg=ME.(isset($_GET["select"])?"select=&":"");$Ek=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Ek&&!$i&&!$_POST["search"]){$I=true;$A="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$A='Tables have been truncated.';}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$A='Tables have been moved.';}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$A='Tables have been copied.';}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$A='Tables have been dropped.';}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$S){foreach(get_rows("PRAGMA integrity_check(".q($S).")")as$K)$A
.="<b>".h($S)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$S){foreach(get_rows("DBCC CHECKTABLE (".q(table($S)).") WITH TABLERESULTS")as$K)$A
.="<b>".h($S)."</b>: ".h($K["MessageText"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$A='Tables have been optimized.';}elseif(!$_POST["tables"])$A='No tables.';elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$A
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(relative_uri(),$A,$I);}page_header(($_GET["ns"]==""?'Database'.": ".h(DB):'Schema'.": ".h($_GET["ns"])),$i,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$D=$_GET["order"];$Qd=($D||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".'Tables and views'."</h3>\n";$Dk=($Qd?table_status():tables_list());if(!$Dk)echo"<p class='message'>".'No tables.'."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".'Search data in tables'." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".'Search'."'>\n","</div></fieldset>\n";if(!$i&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.'All'.'"'.on('click','formCheck','^(tables|views)\[').'>','<th'.(!$D&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($hg,0,-1)).'">'.'Table'.'</a>';$d=array("Engine"=>array('Engine'.''));if(collations())$d["Collation"]=array('Collation'.'');if(function_exists('Adminer\alter_table'))$d["Data_length"]=array('Data Length'.doc_link(array('pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT',)),"create",'Alter table',);if(support("indexes"))$d["Index_length"]=array('Index Length'.doc_link(array('pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),"indexes",'Alter indexes',);$d["Data_free"]=array('Data Free'.'',"edit",'New item');if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=array('Auto Increment'.'',"auto_increment=1&create",'Alter table',);$d["Rows"]=array('Rows'.doc_link(array('pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS',)),"select",'Select data',);if(support("comment"))$d["Comment"]=array('Comment'.doc_link(array('pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE','cockroach'=>'comment-on')),);$Ba=array('Engine','Collation','Comment');foreach($d
as$v=>$c)echo"<th".($D==$v?" aria-sort='".(in_array($v,$Ba)?"ascending":"descending")."'":"")."><a href='".h($hg)."order=$v'>$c[0]</a>";echo"<tbody>\n";if($D){uasort($Dk,function($fa,$Ha)use($D,$Ba){$J=($fa[$D]<$Ha[$D]?-1:($fa[$D]>$Ha[$D]?1:0));return(in_array($D,$Ba)?$J:-$J);});}$U=0;$lk=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($Dk
as$B=>$Q){$Yl=($Qd?is_view($Q):$Q!==null&&!preg_match('~table|sequence~i',$Q));$Q=($Qd?$Q:array('Engine'=>$Q));$r=h("Table-".$B);echo'<tr><td class="hover">'.checkbox(($Yl?"views[]":"tables[]"),$B,in_array("$B",$Ek,true),"","","",$r),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($B)."' title='".'Show structure'."' id='$r'>".h($B).'</a>':h($B));if($Yl&&!preg_match('~materialized~i',$Q['Engine'])){$Sk='View';echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($B)."' title='".'Alter view'."'>$Sk</a>":$Sk),"<td align='right'><a href='".h(ME)."select=".url_escape($B)."' title='".'Select data'."'>?</a>";if(support("comment"))echo'<td>'.h($Q['Comment']);}else{if($Qd){foreach(array_keys($lk)as$v)$lk[$v]+=($Q["Engine"]!="InnoDB"||$v!="Data_free"?idx($Q,$v):0);}foreach($d
as$v=>$c){$r=" id='$v-".h($B)."'";echo($c[1]?"<td align='right'><a href='".h(ME."$c[1]=").url_escape($B)."'$r title='$c[2]'>".format_status($Q,$v)."</a>":"<td$r>".h(idx($Q,$v,'?')).($v=="Comment"&&$Q["Error"]?" <span class='error'>".h($Q["Error"])."</span>":""));}$U++;}echo"\n";}echo"<tr><td class='hover'><th>".sprintf('%d in total',count($Dk)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($Qd&&function_exists('Adminer\db_status'))$lk=db_status();foreach($lk
as$v=>$kk)echo($d[$v]?"<td align='right' id='sum-$v'>".($Qd?format_number($kk):""):"");echo"\n","</table>\n",($Qd?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$Ql="<input type='submit' value='".'Vacuum'."'".on_help("VACUUM")."> ";$oh="<input type='submit' name='optimize' value='".'Optimize'."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$si=(JUSH=="sqlite"?$Ql."<input type='submit' name='check' value='".'Check'."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$Ql.$oh:(JUSH=="mssql"?"<input type='submit' name='check' value='".'Check'."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".'Analyze'."'".on_help("ANALYZE TABLE")."> ".$oh."<input type='submit' name='check' value='".'Check'."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".'Repair'."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".'Truncate'."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".'Drop'."'".confirm().on_help("DROP TABLE").">":"");echo($si?"<div class='footer'><div>\n<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>$si\n</div></fieldset>\n":"");$g=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($g)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".'Move to another database'." <span id='selected3'></span></legend><div>";$h=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($g?html_select("target",$g,$h):'<input name="target" value="'.h($h).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".'Move'."'>",(support("copy")?" <input type='submit' name='copy' value='".'Copy'."'> ".checkbox("overwrite",1,$_POST["overwrite"],'overwrite'):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$U).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".'Create table'."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".'Create view'."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".'Routines'."</h3>\n";$dj=routines();if($dj){echo"<table class='odds'>\n",'<thead><tr><th>'.'Name'.'<th>'.'Type'.'<th>'.'Return type'."<td class='hover'><tbody>\n";foreach($dj
as$K){$B=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".url_escape($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($K["SPECIFIC_NAME"]).$B).'" title="'.'Call'.'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($K["SPECIFIC_NAME"]).$B).'">'.'Alter'."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a>':'').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n","</div>\n";}if(support("sequence")){echo"<div>\n","<h3 id='sequences'>".'Sequences'."</h3>\n";$zj=get_vals("SELECT relname FROM pg_class WHERE relkind = 'S' AND relnamespace = ".driver()->nsOid." ORDER BY relname");if($zj){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."<tbody>\n";foreach($zj
as$X)echo"<tr><th><a href='".h(ME)."sequence=".url_escape($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links hover'><a href='".h(ME)."sequence='>".'Create sequence'."</a>\n","</div>\n";}if(support("type")){echo"<div>\n","<h3 id='user-types'>".'User types'."</h3>\n";$Ml=types();if($Ml){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."<tbody>\n";foreach($Ml
as$X)echo"<tr><th><a href='".h(ME)."type=".url_escape($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links hover'><a href='".h(ME)."type='>".'Create type'."</a>\n","</div>\n";}}elseif(support("extension")){$md=get_rows("SELECT e.extname, e.extversion, n.nspname, obj_description(e.oid, 'pg_extension') AS comment
FROM pg_extension e
JOIN pg_namespace n ON n.oid = e.extnamespace
ORDER BY e.extname");if($md){echo"<div>\n","<h3 id='extensions'>".'Extensions'."</h3>\n","<table class='odds'>\n","<thead><tr><th>".'Name'."<th>".'Version'."<th>".'Schema'."<th>".'Comment'."<tbody>\n";foreach($md
as$K)echo"<tr><th><code class='jush-pgsqlext'>".h($K["extname"])."</code>","<td>".h($K["extversion"]),"<td><a href='".h(substr(ME,0,-1).url_escape($K["nspname"]))."'>".h($K["nspname"])."</a>","<td>".h($K["comment"]),"\n";echo"</table>\n","</div>\n";}}}}page_footer();