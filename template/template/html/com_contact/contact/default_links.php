<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
?>

<?php if ($this->params->get('presentation_style') === 'sliders') : ?>
<li>
	<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_CONTACT_LINKS');?></a>
		<div class="uk-accordion-content">
<?php endif; ?>
<?php if ($this->params->get('presentation_style') === 'tabs') : ?>
	<li>
<?php endif; ?>
<?php if ($this->params->get('presentation_style') === 'plain') : ?>
	<?php echo '<h3>' . Text::_('COM_CONTACT_LINKS') . '</h3>'; ?>
<?php endif; ?>

<div class="contact-links">
	<ul class="uk-nav">
		<?php
		// Letters 'a' to 'e'
		foreach (range('a', 'e') as $char) :
			$link = $this->contact->params->get('link' . $char);
			$label = $this->contact->params->get('link' . $char . '_name');

			if (!$link) :
				continue;
			endif;

			// Add 'http://' if not present
			$link = (0 === strpos($link, 'http')) ? $link : 'http://' . $link;

			// If no label is present, take the link
			$label = $label ?: $link;
			?>
			<li>
				<a href="<?php echo $link; ?>" itemprop="url">
					<?php echo $label; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<?php if ($this->params->get('presentation_style') === 'sliders') : ?>
	</div>
 </li>
<?php endif; ?>
<?php if ($this->params->get('presentation_style') === 'tabs') : ?>
</li>
<?php endif; ?>
