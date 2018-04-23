<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// стандартный вывод рубрики

if ($fn = mso_find_ts_file('type/category/units/category-header.php')) require($fn);

if ($f = mso_page_foreach('category-do-pages')) require($f); // подключаем кастомный вывод
if (function_exists('ushka')) echo ushka('category-do-pages');

if ($fn = mso_find_ts_file('type/category/units/category-do-pages.php')) require($fn);

if ($full_posts) // полные записи
{
	if ($fn = mso_find_ts_file('type/category/units/category-full.php')) require($fn);
}
else // вывод в виде списка
{
	if ($fn = mso_find_ts_file('type/category/units/category-list.php')) require($fn);
}

if ($f = mso_page_foreach('category-posle-pages')) require($f); // подключаем кастомный вывод
if (function_exists('ushka')) echo ushka('category-posle-pages');

mso_hook('pagination', $pagination);


# end of file