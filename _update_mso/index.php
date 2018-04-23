<?php
ini_set('max_execution_time', 180); // максимум 3 минуты на всё про всё даём

define('BASEPATH', dirname(realpath(__FILE__)) . '/');
define('BASEPATH_MSO', realpath(BASEPATH . '../') . '/');

v_get_file(
	'https://raw.githubusercontent.com/maxsite/cms/master/application/libraries/maxsite_lib.php', 
	BASEPATH . 'last-version.txt'
);

$v_last_version = v_get_version(BASEPATH . 'last-version.txt');

echo 'Последняя версия <a href="http://max-3000.com/">MaxSite CMS</a>: ' . $v_last_version . '<br>';

$v_site = v_get_version(BASEPATH_MSO . 'application/libraries/maxsite_lib.php');

if ($v_site)
	echo 'Ваша версия MaxSite CMS: ' . $v_site . '<br>';
else
	echo 'MaxSite CMS на вашем сервере не найдена<br>';

if ($v_site >= $v_last_version) 
{
	echo 'Обновление не требуется<br>';
	die();
}


if (file_exists(BASEPATH . 'master.zip') ) @unlink(BASEPATH . 'master.zip');	

delete_files(BASEPATH . 'cms-master', true, 10);


v_get_file(
	'https://codeload.github.com/maxsite/cms/zip/master', 
	BASEPATH . 'master.zip'
);


if ( !file_exists('master.zip') ) die('Не удалось загрузить master.zip');

echo 'Обновление...<br>';

// выполняем распаковку

require_once(BASEPATH . 'pclzip.lib.php');

$archive = new PclZip(BASEPATH . 'master.zip');

if ($archive->extract() == 0)
{
	die("Error : " . $archive->errorInfo(true));
}	
else 
{
	// удалим ошибочные каталоги и лишние файлы после распаковки
	unlink(BASEPATH . 'cms-master/application/cache/db/.gitkeep/.gitkeep');
	rmdir(BASEPATH . 'cms-master/application/cache/db/.gitkeep');
	
	unlink(BASEPATH . 'cms-master/application/cache/html/.gitkeep/.gitkeep');
	rmdir(BASEPATH . 'cms-master/application/cache/html/.gitkeep');
	
	unlink(BASEPATH . 'cms-master/application/cache/rss/.gitkeep/.gitkeep');
	rmdir(BASEPATH . 'cms-master/application/cache/rss/.gitkeep');
	
	unlink(BASEPATH . 'cms-master/.gitignore');
	
	echo 'Unzip master.zip OK!<br>';
}

// копирование
$res = smartCopy(BASEPATH . 'cms-master/application', BASEPATH_MSO . 'application');
$res = smartCopy(BASEPATH . 'cms-master/install', BASEPATH_MSO . 'install');
$res = smartCopy(BASEPATH . 'cms-master/system', BASEPATH_MSO . 'system');
$res = smartCopy(BASEPATH . 'cms-master/uploads', BASEPATH_MSO . 'uploads');
$res = smartCopy(BASEPATH . 'cms-master/index.php', BASEPATH_MSO . 'index.php');


if (!file_exists(BASEPATH_MSO . 'index.php')) die ('Ошибка копирования (index.php)');

if (!file_exists(BASEPATH_MSO . 'application/libraries/maxsite_lib.php')) die ('Ошибка копирования (maxsite_lib.php)');

if (!file_exists(BASEPATH_MSO . 'application/config/database.php'))
	echo '<a href="../install">Перейти к инсталяции MaxSite CMS</a><br>';
else
	echo '<a href="../">Перейти к сайту</a><br>';


$v_site = v_get_version(BASEPATH_MSO . 'application/libraries/maxsite_lib.php');
echo 'Новая версия MaxSite CMS: ' . $v_site . '<br>';

echo 'Готово! Не забудьте оставить своё мнение в твиттере о <a href="http://max-3000.com/">MaxSite CMS</a>!';


// функции

function v_get_version($path)
{
	if (file_exists($path)) 
	{
		$file = file_get_contents($path);

		$pattern = '/\$version = \'(.*?)\';/';
		
		if (preg_match($pattern, $file, $matches, PREG_OFFSET_CAPTURE, 3))
		{
			if ($val = $matches[1][0]) return $val;
		}
		
		return 0;
	}
	else
		return 0; // ошибка — значение не получено
}

function v_get_file($in_file, $out_file)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $in_file);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$data = curl_exec ($ch);
	$error = curl_error($ch); 
	curl_close ($ch);

	if ($error)
	{
		print_r($error);
		die();
	}

	$destination = $out_file;
	$file = fopen($destination, "w+");
	fputs($file, $data);
	fclose($file);
}

/** 
* Copy file or folder from source to destination, it can do 
* recursive copy as well and is very smart 
* It recursively creates the dest file or directory path if there weren't exists 
* @param $source //file or folder 
* @param $dest ///file or folder 
* @param $options //folderPermission,filePermission 
* @return boolean 
*/ 
function smartCopy($source, $dest, $options=array('folderPermission'=>0755, 'filePermission'=>0755)) 
{ 
	$result=false; 
	 
	if (is_file($source)) 
	{ 
		if ($dest[strlen($dest)-1]=='/') 
		{ 
			if (!file_exists($dest)) 
			{ 
				 cmfcDirectory::makeAll($dest,$options['folderPermission'],true); 
			} 
			
			$__dest = $dest . "/" . basename($source); 
		}
		else 
		{ 
			$__dest=$dest; 
		}
		
		$result=copy($source, $__dest); 
		
		chmod($__dest,$options['filePermission']); 
		 
	 } elseif(is_dir($source)) { 
		if ($dest[strlen($dest)-1]=='/') { 
			if ($source[strlen($source)-1]=='/') { 
				//Copy only contents 
			} else { 
				//Change parent itself and its contents 
				$dest=$dest.basename($source); 
				@mkdir($dest); 
				chmod($dest,$options['filePermission']); 
			} 
		} else { 
			if ($source[strlen($source)-1]=='/') { 
				//Copy parent directory with new name and all its content 
				@mkdir($dest,$options['folderPermission']); 
				chmod($dest,$options['filePermission']); 
			} else { 
				//Copy parent directory with new name and all its content 
				@mkdir($dest,$options['folderPermission']); 
				chmod($dest,$options['filePermission']); 
			} 
		}

		$dirHandle=opendir($source); 
		while($file=readdir($dirHandle)) 
		{ 
			if($file!="." && $file!="..") 
			{ 
				if(!is_dir($source."/".$file)) { 
					$__dest=$dest."/".$file; 
				} else { 
					$__dest=$dest."/".$file; 
				} 
				//echo "$source/$file ||| $__dest<br />"; 
				$result=smartCopy($source."/".$file, $__dest, $options); 
			} 
		} 
		closedir($dirHandle); 
		 
	} else { 
		$result=false; 
	} 
	return $result; 
}

/**
 * Delete Files
 *
 * Deletes all files contained in the supplied directory path.
 * Files must be writable or owned by the system in order to be deleted.
 * If the second parameter is set to TRUE, any directories contained
 * within the supplied base directory will be nuked as well.
 *
 * @access	public
 * @param	string	path to file
 * @param	bool	whether to delete any directories found in the path
 * @return	bool
 */ 
function delete_files($path, $del_dir = FALSE, $level = 0)
{
	// Trim the trailing slash
	$path = rtrim($path, DIRECTORY_SEPARATOR);

	if ( ! $current_dir = @opendir($path))
	{
		return FALSE;
	}

	while (FALSE !== ($filename = @readdir($current_dir)))
	{
		if ($filename != "." and $filename != "..")
		{
			if (is_dir($path.DIRECTORY_SEPARATOR.$filename))
			{
				// Ignore empty folders
				if (substr($filename, 0, 1) != '.')
				{
					delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);
				}
			}
			else
			{
				@unlink($path.DIRECTORY_SEPARATOR.$filename);
			}
		}
	}
	@closedir($current_dir);

	if ($del_dir == TRUE AND $level > 0)
	{
		return @rmdir($path);
	}

	return TRUE;
}

 # end of file