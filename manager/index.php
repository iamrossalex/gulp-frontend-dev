<?php
	ob_start();
	$decodedUri = urldecode($_SERVER['REQUEST_URI']);
	if ($decodedUri[strlen($decodedUri) - 1] == '/') $decodedUri = substr($decodedUri,0,strlen($decodedUri) - 1);
	$parentUri = substr($decodedUri,0,strrpos($decodedUri,'/'));
	if (strlen($decodedUri) > 1 && empty($parentUri)) $parentUri = '/';
	if ($decodedUri == DIRECTORY_SEPARATOR) $decodedUri = '';
	$tpldir = realpath(__DIR__ . '/dest' . $decodedUri);
	// $tpldir = realpath(__DIR__ . $decodedUri);
	
	if ($decodedUri != '/')
		$folderFormated = implode(' :: ',array_reverse(explode('/',substr(urldecode($decodedUri),1)))); else $folderFormated = ':: Home';
    if ($decodedUri != '/' && file_exists($tpldir . 'index.php')) {
        // header("HTTP/1.1 301 Moved Permanently");
		// header("Location: ". $decodedUri . 'index.php');
        exit;
    }
    function zipFolder($path, $filename = 'package.zip') {
		$zipFile = $path."/".$filename;
		$zipArchive = new ZipArchive();
		if (!$zipArchive->open($zipFile, ZIPARCHIVE::OVERWRITE)) return false;
		$zipArchive->addGlob($path."/*.*");
		if (!$zipArchive->status == ZIPARCHIVE::ER_OK) return false;
		$zipArchive->close();
	}
	function getH1($filename) {
		$start = '# ';
		$end = PHP_EOL;
		$string = file_get_contents($filename);
		$string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="author" content="http://webaliser.org/" />
	<title><?=$folderFormated;?> :: Frontend storage ::</title>
	<style>
		.txt1 {width:100%; max-width:650px; margin: 0 auto;}
		.txt1 ul {
			list-style:none;
			margin:10px 0;
			padding:0;
		}
		.txt1 ul > li {
			margin-bottom:10px;
			list-style-position: inside;
			text-indent:0;
			overflow: hidden;
			font-style: normal;
			color: #595959;
			border:1px solid #cacaca;
			border-radius:12px;
			width:80%;
			margin-left:auto;
			margin-right:auto;
		}
		.txt1 ul > li:hover {background-color:#f0f0f0;}
		.txt1 ul > li:hover a {}
		.txt1 img, .txt1 a img, .txt1 p img .txt1 p a img{}
		.txt1 a img:hover, .txt1 p a img:hover {margin-top:-1px;margin-left:-1px;margin-bottom:1px;margin-right:1px;-webkit-box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, 0.6);-moz-box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, 0.6);box-shadow: 1px 1px 2px 1px rgba(0, 0, 0, 0.6);}
		.txt1 a {display:block; text-decoration:none; padding:5px 15px 5px 15px; font-size:18px; line-height:18px; color:#00f; letter-spacing: 0.5px; font-family: sans-serif;}
		.txt1 a:hover {text-decoration:none;}
		.txt1 a.locked {color:#777;}
		.txt1 a.red {color:#F00; font-weight:bold;}
		.txt1 a.folder {font-weight:bold; color:#000;}
		.txt1 a.file {font-style:italic;}
		.txt1 a span {display:block; font-size:11px; color:#bbb; line-height:18px; font-weight:400; letter-spacing: 0;}
		.btn {display:inline-block; padding:10px 15px; color:#aaa; border:1px solid #eee;text-decoration:none;}
		.btn:hover {background-color:#eee;color:#777;}
	</style>
</head>
<body>
	<div style="height:30px;"></div>
	<div class="txt1">
	<ul>
<?php
	if (file_exists($tpldir)) {
		$dir = opendir($tpldir);
		$dirs = $files = $res = array();
        if (!empty($parentUri)) $dirs[] = '..';
		while(false !== ($elem = readdir($dir))) {
			switch ($elem) {
				default:
					if (
						$elem[0] == '.'
						|| $elem == 'vhost.conf'
					) continue 2;
					if (!is_dir($tpldir . DIRECTORY_SEPARATOR . $elem) && !is_link($tpldir . DIRECTORY_SEPARATOR . $elem)) $files[] = $elem;
					else $dirs[] = $elem;
				break;
				case '.':
				case '..':
					continue 2;
				break;
			}
		}
		closedir($dir);
		if (sizeof($dirs)>0) {
			asort($dirs);
			foreach($dirs as $elem) {
				if($elem[0] == '_') continue;
				if ($elem == '..') echo '<li><a href="'.$parentUri.'" class="folder">..</a></li>';
				else echo '<li><a href="'.$decodedUri.'/'.$elem.'" class="folder">'.$elem.((file_exists($tpldir . DIRECTORY_SEPARATOR . $elem.'/README.md') && $elem != '..') ? ('<span>'.getH1($tpldir . DIRECTORY_SEPARATOR . $elem.'/README.md').'</span>') : ('')).'</a></li>';
			}
		}
		if (sizeof($files)>0 && $tpldir != './') {
			asort($files);
			foreach($files as $elem) {
				echo '<li><a href="'.$decodedUri.'/'.$elem.'"'.(($elem == 'index.html' || $elem == 'index.htm' || $elem == 'index.php')?(' class="file red"'):(' class="file"')).'>'.$elem.'</a></li>';
			}
		}
	} else {
		$expUrl = explode("/", $decodedUri);
		switch($decodedUri[1]) {
			case '-':
				$url = explode("/-",$decodedUri);
				if (file_exists($url[1])) {
					echo "yes";
				} else {
					echo "no";
				}
			break;
			case '!': // delete file or folder
				echo "!";
			break;
			case '~': // Is folder, zip folder
				echo "~";
			break;
			case '=': // Make Directory
				echo "=";
			break;
			case '+': // Create file
				echo "+";
			break;
			case '*': // If archive, unpack
				echo "*";
			break;
			case '@': // Edit file in text-editor
				echo "@";
			break;
			case '$':// Edit file in code editor
				echo "$";
			break;
			default:
				$start = ob_get_clean();
				header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
				echo $start;
				echo "Папки или файла &lt; ".$tpldir." &gt; нет на сервере";
			break;
		}
	}
?>
	</ul></div>
	<div style="height:30px;"></div>
        <?php
            // $login = 'q';
            // $password = 'q';
            // echo $login.':{SHA}'.base64_encode(sha1($password, true));
        ?>
</body>
</html>
<?

?>
