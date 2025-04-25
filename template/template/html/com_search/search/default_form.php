<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

// Ordering
$this->lists['ordering'] = HTMLHelper::_('select.genericlist', [

    HTMLHelper::_('select.option', 'newest', Text::_('COM_SEARCH_NEWEST_FIRST')),
    HTMLHelper::_('select.option', 'oldest', Text::_('COM_SEARCH_OLDEST_FIRST')),
    HTMLHelper::_('select.option', 'popular', Text::_('COM_SEARCH_MOST_POPULAR')),
    HTMLHelper::_('select.option', 'alpha', Text::_('COM_SEARCH_ALPHABETICAL')),
    HTMLHelper::_('select.option', 'category', Text::_('JCATEGORY')),

], 'ordering', 'class="uk-select uk-form-width-medium"', 'value', 'text', $this->get('state')->get('ordering'));

?>
<form id="searchForm" action="<?php echo JRoute::_('index.php?option=com_search'); ?>" method="post">
<div class="uk-panel">
	<fieldset class="uk-fieldset">
		<div class="uk-grid-small" uk-grid>
			<div class="uk-width-expand@s">
				<div class="uk-search uk-search-default uk-width-1-1">
					<input type="text" name="searchword" title="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" placeholder="<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="uk-search-input" />
				</div>
			</div>
			<div class="uk-width-auto@s">
			<button name="Search" onclick="this.form.submit()" class="uk-button uk-button-primary uk-width-1-1"><?= Text::_('JSEARCH_FILTER_SUBMIT') ?></button>
			</div>
		</div>
     </fieldset>

	 <div class="uk-grid-row-small uk-child-width-auto uk-text-small uk-margin" uk-grid>

	<div>

	<?php if ($this->params->get('search_phrases', 1)) : ?>
		<fieldset class="uk-margin uk-fieldset">

			<div class="uk-grid-collapse uk-child-width-auto" uk-grid>
				<legend class="uk-h6">
						<?php echo Text::_('COM_SEARCH_FOR'); ?>
				</legend>
				<div>
					<?php echo $this->lists['searchphrase']; ?>
				</div>
			</div>

		</fieldset>
	<?php endif; ?>

	</div>

    <div>

	<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset class="uk-margin uk-fieldset">

			<div class="uk-grid-small uk-child-width-auto" uk-grid>
				<legend class="uk-h6">
						<?php echo Text::_('COM_SEARCH_SEARCH_ONLY'); ?>
				</legend>
				<div>
				<div class="uk-grid-small uk-child-width-auto" uk-grid>
				<?php foreach ($this->searchareas['search'] as $val => $txt) : ?>
					<?php $checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : ''; ?>
					<label for="area-<?php echo $val; ?>" class="checkbox">
						<input type="checkbox" class="uk-checkbox" name="areas[]" value="<?php echo $val; ?>" id="area-<?php echo $val; ?>" <?php echo $checked; ?> />
						<?php echo JText::_($txt); ?>
					</label>
				<?php endforeach; ?>
				</div>
				</div>
			</div>

		</fieldset>
	<?php endif; ?>

	</div>
    </div>
</div>

<div class="uk-grid-small uk-flex-middle uk-margin-medium" uk-grid>

	<?php if (!empty($this->searchword)) : ?>
		<div class="uk-width-expand@s">
			<div class="uk-h3 ">
				<?php echo Text::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', '<span class="uk-badge uk-badge-info">' . $this->total . '</span>'); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($this->total > 0) : ?>
	<div class="uk-width-auto@s">

		<div class="uk-grid-small uk-child-width-auto" uk-grid>
			<div>
				<div><?php echo $this->lists['ordering']; ?></div>
			</div>
			<div>
				<div><?php echo $this->pagination->getLimitBox(); ?></div>
			</div>
		</div>

	</div>
	<?php endif; ?>

</div>
	
</form>
