<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * HTML-структура шаблона
 *
 */

if ($fn = mso_fe('main/blocks/_start.php')) require($fn);
if ($fn = mso_fe('main/blocks/body-start.php')) require($fn);
if ($fn = mso_fe('main/blocks/content.php')) require($fn);
if ($fn = mso_fe('main/blocks/body-end.php')) require($fn);
?></body></html><?php if ($fn = mso_fe('main/blocks/_end.php')) require($fn) ?>