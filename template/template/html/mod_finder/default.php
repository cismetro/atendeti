<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   (C) 2011 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Module\Finder\Site\Helper\FinderHelper;

// Load the smart search component language file.
$lang = $app->getLanguage();
$lang->load('com_finder', JPATH_SITE);

$input = '<span uk-search-icon class="uk-position-z-index"></span><input type="text" name="q" id="mod-finder-searchword' . $module->id . '" class="js-finder-search-query uk-search-input form-control" value="' . htmlspecialchars($app->input->get('q', '', 'string'), ENT_COMPAT, 'UTF-8') . '"'
    . ' placeholder="' . Text::_('MOD_FINDER_SEARCH_VALUE') . '">';

$output = '';

$output .= $input;

Text::script('MOD_FINDER_SEARCH_VALUE');

if (version_compare(JVERSION, '4.0', '<')) {

HTMLHelper::_('stylesheet', 'com_finder/finder.css', array('version' => 'auto', 'relative' => true));

$script = "";
/*
 * This segment of code sets up the autocompleter.
 */
if ($params->get('show_autosuggest', 1))
{
	HTMLHelper::_('script', 'jui/jquery.autocomplete.min.js', array('version' => 'auto', 'relative' => true));

	$script .= "jQuery(document).ready(function() {
	var suggest = jQuery('#mod-finder-searchword" . $module->id . "').autocomplete({
		serviceUrl: '" . Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component') . "',
		paramName: 'q',
		minChars: 1,
		maxHeight: 400,
		width: 300,
		zIndex: 9999,
		deferRequestBy: 500
	});";
}

$script .= '});';

JFactory::getDocument()->addScriptDeclaration($script);

} else {
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_finder');

/*
 * This segment of code sets up the autocompleter.
 */
if ($params->get('show_autosuggest', 1)) {
    $wa->usePreset('awesomplete');
    $app->getDocument()->addScriptOptions('finder-search', ['url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component', false)]);

    Text::script('JLIB_JS_AJAX_ERROR_OTHER');
    Text::script('JLIB_JS_AJAX_ERROR_PARSE');
}

$wa->useScript('com_finder.finder');
}
?>

<form class="mod-finder js-finder-searchform uk-search uk-search-default uk-width-1-1" action="<?php echo Route::_($route); ?>" method="get" role="search">
    <?php echo $output; ?>
    <?php if (version_compare(JVERSION, '4.0', '<')) : ?>
        <?php 
            HTMLHelper::_('behavior.core'); 
            HTMLHelper::addIncludePath(JPATH_SITE . '/components/com_finder/helpers/html');
        ?>
            <?php $show_advanced = $params->get('show_advanced', 0); ?>
            <?php if ($show_advanced == 2) : ?>
                <br />
                <a href="<?php echo Route::_($route); ?>"><?php echo JText::_('COM_FINDER_ADVANCED_SEARCH'); ?></a>
            <?php elseif ($show_advanced == 1) : ?>
                <div id="mod-finder-advanced<?php echo $module->id; ?>">
                    <?php echo HTMLHelper::_('filter.select', $query, $params); ?>
                </div>
            <?php endif; ?>
            <?php echo modFinderHelper::getGetFields($route, (int) $params->get('set_itemid', 0)); ?>
    <?php else: ?>
        <?php $show_advanced = $params->get('show_advanced', 0); ?>
        <?php if ($show_advanced == 2) : ?>
            <br>
            <a href="<?php echo Route::_($route); ?>" class="mod-finder__advanced-link"><?php echo Text::_('COM_FINDER_ADVANCED_SEARCH'); ?></a>
        <?php elseif ($show_advanced == 1) : ?>
            <div class="mod-finder__advanced js-finder-advanced">
                <?php echo HTMLHelper::_('filter.select', $query, $params); ?>
            </div>
        <?php endif; ?>
        <?php echo FinderHelper::getGetFields($route, (int) $params->get('set_itemid', 0)); ?>
    <?php endif ?>
</form>