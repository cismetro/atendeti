<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$item    = $displayData['data'];
$display = $item->text;
$app = Factory::getApplication();

switch ((string) $item->text)
{
	// Check for "Start" item
	case Text::_('JLIB_HTML_START') :
		$icon = $app->getLanguage()->isRtl() ? 'fas fa-angle-double-right' : 'fas fa-angle-double-left';
		$aria = Text::_('JLIB_HTML_GOTO_POSITION_START');
		break;

	// Check for "Prev" item
	case $item->text === Text::_('JPREV') :
		$icon = '<span uk-pagination-previous></span>';
		$aria = Text::_('JLIB_HTML_GOTO_POSITION_PREVIOUS');
		break;

	// Check for "Next" item
	case Text::_('JNEXT') :
		$icon = '<span uk-pagination-next></span>';
		$aria = Text::_('JLIB_HTML_GOTO_POSITION_NEXT');
		break;

	// Check for "End" item
	case Text::_('JLIB_HTML_END') :
		$icon = $app->getLanguage()->isRtl() ? 'fas fa-angle-double-left' : 'fas fa-angle-double-right';
		$aria = Text::_('JLIB_HTML_GOTO_POSITION_END');
		break;

	default:
		$icon = null;
		$aria = Text::sprintf('JLIB_HTML_GOTO_PAGE', strtolower($item->text));
		break;
}

if ($icon !== null)
{
	$display = $icon;
}


if ($displayData['active']) {
    if ($item->base > 0) {
        $limit = 'limitstart.value=' . $item->base;
    } else {
        $limit = 'limitstart.value=0';
    }

    $class = 'active';

    if ($app->isClient('administrator')) {
        $link = 'href="#" onclick="document.adminForm.' . $item->prefix . $limit . '; Joomla.submitform();return false;"';
    } elseif ($app->isClient('site')) {
        $link = 'href="' . $item->link . '"';
    }
} else {
    $class = (property_exists($item, 'active') && $item->active) ? 'uk-active' : 'uk-disabled';
}

?>
<?php if ($displayData['active']) : ?>
    <li>
        <a aria-label="<?php echo $aria; ?>" <?php echo $link; ?>>
            <?php echo $display; ?>
        </a>
    </li>
<?php elseif (isset($item->active) && $item->active) : ?>
    <?php $aria = Text::sprintf('JLIB_HTML_PAGE_CURRENT', strtolower($item->text)); ?>
    <li class="<?php echo $class; ?>">
        <a aria-current="true" aria-label="<?php echo $aria; ?>" href="#"><?php echo $display; ?></a>
    </li>
<?php else : ?>
    <li>
        <span class="<?php echo $class; ?>" aria-hidden="true"><?php echo $display; ?></span>
    </li>
<?php endif; ?>
