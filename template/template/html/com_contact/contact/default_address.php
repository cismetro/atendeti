<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;

$icon = $this->params->get('contact_icons') == 0;

/**
 * Marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>

<?php if(JVERSION >= 4) : ?>
<ul class="contact-address uk-list" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
	<?php
		if (
			($this->params->get('address_check') > 0) &&
			($this->item->address || $this->item->suburb  || $this->item->state || $this->item->country || $this->item->postcode)
		) : ?>
		<li>
			<?php if ($icon && !$this->params->get('marker_address')) : ?>
				<span class="uk-margin-small-right icon-address" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_ADDRESS'); ?></span>
			<?php else : ?>
				<span class="<?php echo $this->params->get('marker_class'); ?>">
					<?php echo $this->params->get('marker_address'); ?>
				</span>
			<?php endif; ?>

			<?php if ($this->item->address && $this->params->get('show_street_address')) : ?>
			<span class="contact-street" itemprop="streetAddress">
				<?php echo nl2br($this->item->address, false); ?>
			</span>
		<?php endif; ?>
		</li>

		<?php if ($this->item->suburb && $this->params->get('show_suburb')) : ?>
			<li>
				<span class="contact-suburb" itemprop="addressLocality">
					<?php echo $this->item->suburb; ?>
				</span>
			</li>
		<?php endif; ?>
		
		<?php if ($this->item->state && $this->params->get('show_state')) : ?>
			<li>
				<span class="contact-state" itemprop="addressRegion">
					<?php echo $this->item->state; ?>
				</span>
			</li>
		<?php endif; ?>

		<?php if ($this->item->postcode && $this->params->get('show_postcode')) : ?>
			<li>
				<span class="contact-postcode" itemprop="postalCode">
					<?php echo $this->item->postcode; ?>
				</span>
			</li>
		<?php endif; ?>

		<?php if ($this->item->country && $this->params->get('show_country')) : ?>
			<li>
				<span class="contact-country" itemprop="addressCountry">
					<?php echo $this->item->country; ?>
				</span>
			</li>
		<?php endif; ?>
	<?php endif; ?>

<?php if ($this->item->email_to && $this->params->get('show_email')) : ?>
	<li>
		<?php if ($icon && !$this->params->get('marker_email')) : ?>
			<span class="icon-envelope uk-margin-small-right" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_EMAIL_LABEL'); ?></span>
		<?php else : ?>
			<span class="<?php echo $this->params->get('marker_class'); ?>">
				<?php echo $this->params->get('marker_email'); ?>
			</span>
		<?php endif; ?>
	
		<span class="contact-emailto">
			<?php echo $this->item->email_to; ?>
		</span>
	</li>
<?php endif; ?>

<?php if ($this->item->telephone && $this->params->get('show_telephone')) : ?>
	<li>
		<?php if ($icon && !$this->params->get('marker_telephone')) : ?>
				<span class="icon-phone uk-margin-small-right" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_TELEPHONE'); ?></span>
		<?php else : ?>
			<span class="<?php echo $this->params->get('marker_class'); ?>">
				<?php echo $this->params->get('marker_telephone'); ?>
			</span>
		<?php endif; ?>

		<span class="contact-telephone" itemprop="telephone">
			<?php echo $this->item->telephone; ?>
		</span>
	</li>
<?php endif; ?>

<?php if ($this->item->fax && $this->params->get('show_fax')) : ?>
	<li>
		<?php if ($icon && !$this->params->get('marker_fax')) : ?>
			<span class="icon-fax uk-margin-small-right" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_FAX'); ?></span>
		<?php else : ?>
			<span class="<?php echo $this->params->get('marker_class'); ?>">
				<?php echo $this->params->get('marker_fax'); ?>
			</span>
		<?php endif; ?>

		<span class="contact-fax" itemprop="faxNumber">
		<?php echo $this->item->fax; ?>
		</span>
	</li>
<?php endif; ?>

<?php if ($this->item->mobile && $this->params->get('show_mobile')) : ?>
	<li>
		<?php if ($icon && !$this->params->get('marker_mobile')) : ?>
			<span class="icon-mobile uk-margin-small-right" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_MOBILE'); ?></span>
		<?php else : ?>
			<span class="<?php echo $this->params->get('marker_class'); ?>">
				<?php echo $this->params->get('marker_mobile'); ?>
			</span>
		<?php endif; ?>

		<span class="contact-mobile" itemprop="telephone">
			<?php echo $this->item->mobile; ?>
		</span>
	</li>
<?php endif; ?>

<?php if ($this->item->webpage && $this->params->get('show_webpage')) : ?>
	<li>
		<?php if ($icon && !$this->params->get('marker_webpage')) : ?>
			<span class="icon-home uk-margin-small-right" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('COM_CONTACT_WEBPAGE'); ?></span>
		<?php else : ?>
			<span class="<?php echo $this->params->get('marker_class'); ?>">
				<?php echo $this->params->get('marker_webpage'); ?>
			</span>
		<?php endif; ?>

		<span class="contact-webpage">
			<a href="<?php echo $this->item->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
			<?php echo $this->escape(PunycodeHelper::urlToUTF8($this->item->webpage)); ?></a>
		</span>
	</li>
<?php endif; ?>
</ul>
<?php endif; ?>

<?php if(JVERSION < 4) : ?>
<ul class="contact-address uk-list" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
	<?php if (($this->params->get('address_check') > 0) &&
		($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) : ?>
		<?php if ($this->contact->address && $this->params->get('show_street_address')) : ?>
			<li>
				<span class="contact-street" itemprop="streetAddress">
				<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-map-marker-alt"></i></span>
				<?php echo nl2br($this->contact->address); ?>
				</span>
			</li>
		<?php endif; ?>

		<?php if ($this->contact->suburb && $this->params->get('show_suburb')) : ?>
			<li>
				<span class="contact-suburb" itemprop="addressLocality">
				<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-map-marked-alt"></i></span>
					<?php echo $this->contact->suburb; ?>
				</span>
			</li>
		<?php endif; ?>
		<?php if ($this->contact->state && $this->params->get('show_state')) : ?>
			<li>
				<span class="contact-state" itemprop="addressRegion">
				<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-location-arrow"></i></span>
					<?php echo $this->contact->state; ?>
				</span>
			</li>
		<?php endif; ?>
		<?php if ($this->contact->postcode && $this->params->get('show_postcode')) : ?>
			<li>
				<span class="contact-postcode" itemprop="postalCode">
				<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-magic"></i></span>
					<?php echo $this->contact->postcode; ?>
				</span>
			</li>
		<?php endif; ?>
		<?php if ($this->contact->country && $this->params->get('show_country')) : ?>
		<li>
			<span class="contact-country" itemprop="addressCountry">
			<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-globe"></i></span>
				<?php echo $this->contact->country; ?>
			</span>
		</li>
		<?php endif; ?>
	<?php endif; ?>

<?php if ($this->contact->email_to && $this->params->get('show_email')) : ?>
	<li>
		<span class="contact-emailto">
		<span class="uk-margin-small-right uk-icon-button"><i class="far fa-envelope"></i></span>
			<?php echo $this->contact->email_to; ?>
		</span>
	</li>
<?php endif; ?>

<?php if ($this->contact->telephone && $this->params->get('show_telephone')) : ?>
	<li>
		<span class="contact-telephone" itemprop="telephone">
		<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-phone-alt"></i></span>
			<?php echo $this->contact->telephone; ?>
		</span>
	</li>
<?php endif; ?>
<?php if ($this->contact->fax && $this->params->get('show_fax')) : ?>
	<li>
		<span class="contact-fax" itemprop="faxNumber">
		<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-fax"></i></span>
		<?php echo $this->contact->fax; ?>
		</span>
	</li>
<?php endif; ?>
<?php if ($this->contact->mobile && $this->params->get('show_mobile')) : ?>
	<li>
		<span class="contact-mobile" itemprop="telephone">
		<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-mobile-alt"></i></span>
			<?php echo $this->contact->mobile; ?>
		</span>
	</li>
<?php endif; ?>
<?php if ($this->contact->webpage && $this->params->get('show_webpage')) : ?>
	<li>
		<span class="contact-webpage">
		<span class="uk-margin-small-right uk-icon-button"><i class="fas fa-external-link-alt"></i></span>
			<a href="<?php echo $this->contact->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
			<?php echo $this->escape(PunycodeHelper::urlToUTF8($this->item->webpage)); ?></a>
		</span>
	</li>
<?php endif; ?>
</ul>
<?php endif; ?>