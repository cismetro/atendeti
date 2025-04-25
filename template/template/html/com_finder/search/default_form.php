<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if (JVERSION < 4)
{
	if ($this->params->get('show_advanced', 1) || $this->params->get('show_autosuggest', 1))
	{
		HTMLHelper::_('jquery.framework');
	
		$script = "
		jQuery(function() {";
	
		if ($this->params->get('show_advanced', 1))
		{
			/*
			* This segment of code disables select boxes that have no value when the
			* form is submitted so that the URL doesn't get blown up with null values.
			*/
			$script .= "
			jQuery('#finder-search').on('submit', function(e){
				e.stopPropagation();
				// Disable select boxes with no value selected.
				jQuery('#advancedSearch').find('select').each(function(index, el) {
					var el = jQuery(el);
					if(!el.val()){
						el.attr('disabled', 'disabled');
					}
				});
			});";
		}
	
		/*
		* This segment of code sets up the autocompleter.
		*/
		if ($this->params->get('show_autosuggest', 1))
		{
			HTMLHelper::_('script', 'jui/jquery.autocomplete.min.js', array('version' => 'auto', 'relative' => true));
	
			$script .= "
			var suggest = jQuery('#q').autocomplete({
				serviceUrl: '" . Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component') . "',
				paramName: 'q',
				minChars: 1,
				maxHeight: 400,
				width: 300,
				zIndex: 9999,
				deferRequestBy: 500
			});";
		}
	
		$script .= "
		});";
	
		Factory::getDocument()->addScriptDeclaration($script);
	}
}
else
{
	/*
	* This segment of code sets up the autocompleter.
	*/
	if ($this->params->get('show_autosuggest', 1)) {
		$this->document->getWebAssetManager()->usePreset('awesomplete');
		$this->document->addScriptOptions('finder-search', ['url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component', false)]);

		Text::script('JLIB_JS_AJAX_ERROR_OTHER');
		Text::script('JLIB_JS_AJAX_ERROR_PARSE');
	}
}
?>

<form action="<?php echo Route::_($this->query->toUri()); ?>" id="finder-search" method="get" class="js-finder-searchform">
	<?php echo $this->getFields(); ?>

	<?php //DISABLED UNTIL WEIRD VALUES CAN BE TRACKED DOWN. ?>
	<?php if (false && $this->state->get('list.ordering') !== 'relevance_dsc') : ?>
		<input type="hidden" name="o" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>">
	<?php endif; ?>
	<fieldset class="uk-margin">
		<div class="uk-grid-small" uk-grid>
			
            <div class="uk-width-expand@s">

                <div class="uk-search uk-search-default uk-width-1-1">
					<input type="text" name="q" id="q" class="js-finder-search-query uk-search-input" value="<?php echo $this->escape($this->query->input); ?>">
                </div>

            </div>
            <div class="uk-width-auto@s">

                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-auto@s">
                        <button name="Search" type="submit" class="uk-button uk-button-primary uk-width-1-1"><?= Text::_('JSEARCH_FILTER_SUBMIT') ?></button>
                    </div>
                    <?php if ($this->params->get('show_advanced', 1)) : ?>
                    <div class="uk-width-auto@s">
						<a href="#advancedSearch" role="button" aria-expanded="false" aria-controls="advancedSearch" uk-toggle="target: #advancedSearch; animation: uk-animation-fade" class="uk-button uk-button-default uk-width-1-1"><?= Text::_('COM_FINDER_ADVANCED_SEARCH_TOGGLE') ?>
						</a>
					</div>
                    <?php endif ?>
                </div>

            </div>
		</div>
	</fieldset>

	<?php if ($this->params->get('show_advanced', 1)) : ?>
		<fieldset id="advancedSearch" class="com-finder__advanced js-finder-advanced uk-margin"<?php if (!$this->params->get('expand_advanced', 0)) echo ' hidden'; ?>>
			<legend class="com-finder__search-advanced visually-hidden">
				<?php echo Text::_('COM_FINDER_SEARCH_ADVANCED_LEGEND'); ?>
			</legend>
			<?php if ($this->params->get('show_advanced_tips', 1)) : ?>
				<div class="com-finder__tips uk-card uk-card-default mb-3">
					<div class="uk-card-body">
					<?php if (version_compare(JVERSION, '4.0', '<')) : ?>
						<?= Text::_('COM_FINDER_ADVANCED_TIPS') ?>
					<?php else : ?>
						<?php echo Text::_('COM_FINDER_ADVANCED_TIPS_INTRO'); ?>
						<?php echo Text::_('COM_FINDER_ADVANCED_TIPS_AND'); ?>
						<?php echo Text::_('COM_FINDER_ADVANCED_TIPS_NOT'); ?>
						<?php echo Text::_('COM_FINDER_ADVANCED_TIPS_OR'); ?>
						<?php if ($this->params->get('tuplecount', 1) > 1) : ?>
						<?php echo Text::_('COM_FINDER_ADVANCED_TIPS_PHRASE'); ?>
						<?php endif; ?>
						<?php echo Text::_('COM_FINDER_ADVANCED_TIPS_OUTRO'); ?>
					<?php endif ?>
					</div>
				</div>
			<?php endif; ?>
			<div id="finder-filter-window" class="com-finder__filter">
				<?php echo HTMLHelper::_('filter.select', $this->query, $this->params); ?>
			</div>
		</fieldset>
	<?php endif; ?>
</form>