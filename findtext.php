<?php
define("SEARCH_IGNORE", "powcache");

if (!is_writable(session_save_path())) {
    echo 'Session path "'.session_save_path().'" is not writable for PHP!'; 
}


echo "disable_functions: " . (exec_enabled() ? "Disable" : "Enable");
echo "<br /> Caution: don't use query which contains both \" and ' at same time <br />";

function exec_enabled() {
  $disabled = explode(',', ini_get('disable_functions'));
  return !in_array('exec', $disabled);
}
?>



<?php



    define("SLASH", stristr($_SERVER['SERVER_SOFTWARE'], "win") ? "\\" : "/");
   $q = "";
   $path = "";
   $results = "";
   
    $path    = isset($_POST['path']) ? $_POST['path'] : dirname(__FILE__) ;
    $q        = isset($_POST['q']) ? $_POST['q'] : "";
	
	$q = str_replace('"',"'",$q);
	
    function php_grep($q, $path){
       $ret = "";
        $fp = opendir($path);
        while($f = readdir($fp)){
            if( preg_match("#^\.+$#", $f) ) continue; // ignore symbolic links
            $file_full_path = $path.SLASH.$f;
            if(strpos($file_full_path, SEARCH_IGNORE) == false){
                if(is_dir($file_full_path)) {
                    $ret .= php_grep($q, $file_full_path);
                } else if( stristr(file_get_contents($file_full_path), $q) ) {
    				
                    $ret .= "$file_full_path\n";
                }
            }
        }
        return $ret;
    }

    if($q != ""){
		if(stristr($q, "'"))
		{
			$results = php_grep($q, $path);
			$results .= php_grep(str_replace("'",'"',$q), $path);
		}
		else
		{
			$results = php_grep($q, $path);
		}
    }

   
   
    echo <<<HRD

    <pre >
    <form method=post>
        <table>
            <tr>
                <td>Path:</td>
                <td><input name=path size=100 value="$path" /></td>
            </tr>
            <tr>
                <td>Search:</td>
                <td><input name=q size=100 value="$q" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type=submit></td>
            </tr>            

        </table>
    </form>
   
$results
   
    </pre >
   
HRD;

?>