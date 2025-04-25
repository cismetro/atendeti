<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

use Joomla\Component\Contact\Site\Helper\RouteHelper;

$cparams = ComponentHelper::getParams('com_media');
$tparams = $this->item->params;
$canDo   = ContentHelper::getActions('com_contact', 'category', $this->item->catid);
$canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by === Factory::getUser()->id);
$htag    = $tparams->get('show_page_heading') ? 'h2' : 'h1';

?>

<?php if(JVERSION < 4) : ?>
<div class="contact<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Person">
	<?php if ($tparams->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($tparams->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<?php if ($this->contact->name && $tparams->get('show_name')) : ?>
		<div class="page-header">
			<h2>
				<?php if ($this->item->published == 0) : ?>
					<span class="label label-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
				<?php endif; ?>
				<span class="contact-name" itemprop="name"><?php echo $this->contact->name; ?></span>
			</h2>
		</div>
	<?php endif; ?>

	<?php $presentation_style = $tparams->get('presentation_style'); ?>
	<?php $accordionStarted = false; ?>
	<?php $tabSetStarted = false; ?>

	<?php if ($presentation_style === 'sliders') : ?>
	<?php echo $this->item->event->afterDisplayTitle; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<ul uk-accordion>
		<?php if ($this->params->get('show_info', 1)) : ?>
		<li class="uk-open">
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_CONTACT_DETAILS');?></a>	

		<div class="uk-accordion-content">
			<?php if ($this->contact->image && $tparams->get('show_image')) : ?>
				<div class="thumbnail pull-right">
				<?php echo HTMLHelper::_('image', $this->contact->image, $this->contact->name, array('itemprop' => 'image')); ?>
				</div>
			<?php endif; ?>

			<?php if ($this->contact->con_position && $tparams->get('show_position')) : ?>
				<div class="uk-margin">
					<?php echo Text::_('COM_CONTACT_POSITION'); ?>:
					<span class="contact-position" itemprop="jobTitle">
					<?php echo $this->contact->con_position; ?>
					</span>
				</div>
			<?php endif; ?>

			<?php echo $this->loadTemplate('address'); ?>

			<?php if ($tparams->get('allow_vcard')) : ?>
			<div class="uk-margin">
				<?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
				<a class="uk-link-reset" href="<?php echo Route::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->contact->id . '&amp;format=vcf'); ?>">
				<?php echo Text::_('COM_CONTACT_VCARD'); ?></a>
			</div>
			<?php endif; ?>

			<?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
				<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
				<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
			<?php endif; ?>
		</div>

		</li>

		<?php endif; ?> 

		<?php if ($tparams->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
		<li>
			<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_CONTACT_EMAIL_FORM');?></a>
			<div class="uk-accordion-content">
			<div class="card-body clearfix">
				<?php echo $this->loadTemplate('form'); ?>
			</div>
			</div>
		</li>
		<?php endif; ?> 

		<?php if ($tparams->get('show_links')) : ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>	
		<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('JGLOBAL_ARTICLES');?></a>
		<div class="uk-accordion-content">
			<?php echo $this->loadTemplate('articles'); ?>
		</div>
		</li>
		<?php endif; ?> 

		<?php if ($tparams->get('show_profile') && $this->contact->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>
		<li>
		<div class="card-header">
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_CONTACT_PROFILE');?></a>
		<div class="uk-accordion-content">
			<?php echo $this->loadTemplate('profile'); ?>
		</div>
		</li>
		<?php endif; ?> 

		<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
		<?php echo $this->loadTemplate('user_custom_fields'); ?>
		<?php endif; ?>

		<?php if ($this->contact->misc && $tparams->get('show_misc')) : ?>
		<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_CONTACT_OTHER_INFORMATION');?></a>
		<div class="uk-accordion-content">
			<?php echo $this->contact->misc; ?>
		</div>
		</li>
		<?php endif; ?>  

		</ul>
	<?php endif; ?>

	<?php if ($presentation_style === 'tabs') : ?>	
	<ul uk-tab>
	<?php if ($this->params->get('show_info', 1)) : ?>
		<li><a href="#"><?php echo Text::_('COM_CONTACT_DETAILS');?></a></li>
	<?php endif; ?>
	<?php if ($tparams->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
		<li><a href="#"><?php echo Text::_('COM_CONTACT_EMAIL_FORM');?></a></li>
	<?php endif; ?>
	<?php if ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
		<li><a href="#"><?php echo Text::_('JGLOBAL_ARTICLES');?></a></li>
	<?php endif; ?>
	<?php if ($tparams->get('show_links')) : ?>
		<li><a href="#"><?php echo Text::_('COM_CONTACT_LINKS');?></a></li>
	<?php endif; ?>
	<?php if ($tparams->get('show_profile') && $this->contact->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>
		<li><a href="#"><?php echo Text::_('COM_CONTACT_PROFILE');?></a></li>
	<?php endif; ?>
	<?php if ($this->contact->misc && $tparams->get('show_misc')) : ?>
		<li><a href="#"><?php echo Text::_('COM_CONTACT_OTHER_INFORMATION');?></a></li>
	<?php endif; ?>
	</ul>

	<ul class="uk-switcher uk-margin">
	<?php if ($this->params->get('show_info', 1)) : ?>
		<li>
			<?php if ($this->contact->image && $tparams->get('show_image')) : ?>
				<div class="thumbnail">
					<?php echo HTMLHelper::_('image', $this->contact->image, htmlspecialchars($this->contact->name,  ENT_QUOTES, 'UTF-8'), array('itemprop' => 'image')); ?>
				</div>
			<?php endif; ?>

			<?php if ($this->contact->con_position && $tparams->get('show_position')) : ?>
				<div class="contact-position">
				<span><?php echo Text::_('COM_CONTACT_POSITION'); ?>:</span>
				<?php echo $this->contact->con_position; ?>
				</div>
			<?php endif; ?>

			<?php echo $this->loadTemplate('address'); ?>

			<?php if ($tparams->get('allow_vcard')) : ?>
				<div class="uk-margin">
				<?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
				<a href="<?php echo Route::_('index.php?option=com_contact&view=contact&catid=' . $this->item->catslug . '&id=' . $this->item->slug . '&format=vcf'); ?>">
				<?php echo Text::_('COM_CONTACT_VCARD'); ?></a>
				</div>
			<?php endif; ?>

			<?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
				<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
				<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
			<?php endif; ?>				
		</li>
	<?php endif; ?>
	<?php if ($tparams->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
		<li>
			<?php echo $this->loadTemplate('form'); ?>
		</li>
	<?php endif; ?>
	<?php if ($tparams->get('show_links')) : ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>		
	<?php if ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
		<li>
			<?php echo $this->loadTemplate('articles'); ?>
		</li>
	<?php endif; ?>
	<?php if ($tparams->get('show_profile') && $this->contact->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>
		<li><?php echo $this->loadTemplate('profile'); ?></li>
	<?php endif; ?>
	<?php if ($this->contact->misc && $tparams->get('show_misc')) : ?>
		<li>
		<div class="contact-miscinfo">
		<?php echo $this->contact->misc; ?>
		</div>
		</li>
	<?php endif; ?>
	</ul>

	<?php endif; ?>

	<?php if ($presentation_style === 'plain') : ?>	
	<div class="<?php echo $this->params->get('presentation_style') ?>-style">
		<div class="contact-inner">

		<?php if ($this->params->get('show_info', 1) || $tparams->get('show_links')) : ?>
		<?php echo $this->item->event->beforeDisplayContent; ?>
		<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
			<?php echo $this->loadTemplate('user_custom_fields'); ?>
		<?php endif; ?>
		<?php endif; ?>

		<?php $show_contact_category = $tparams->get('show_contact_category'); ?>
		<?php $show_info_check = $this->params->get('show_info', 1) && (($this->contact->image && $tparams->get('show_image')) || ($this->contact->con_position && $tparams->get('show_position')) || (($this->params->get('address_check') > 0) &&
		($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) || ($tparams->get('allow_vcard')) ); ?>
		
		<?php if ($tparams->get('show_contact_category') != 'hide' || ($tparams->get('show_contact_list') && count($this->contacts) > 1) || ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) || $show_info_check || ($tparams->get('show_links')) || ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) || ($tparams->get('show_profile') && $this->contact->user_id && PluginHelper::isEnabled('user', 'profile')) || ($this->contact->misc && $tparams->get('show_misc')) ) : ?>
			
			<div class="uk-child-width-expand" uk-grid>
				<div class="uk-width-1-3@m">
					
					<?php if ($this->contact->image && $tparams->get('show_image')) : ?>
						<div class="uk-card uk-card-default">
						<div class="thumbnail uk-card-media-top">
							<?php echo HTMLHelper::_('image', $this->contact->image, htmlspecialchars($this->contact->name,  ENT_QUOTES, 'UTF-8'), array('itemprop' => 'image')); ?>
						</div>
						<div class="uk-card-body uk-margin-remove-first-child">
					<?php endif; ?>

					<?php if ($this->contact->misc && $tparams->get('show_misc')) : ?>
						<div class="contact-miscinfo">
							<?php echo $this->contact->misc; ?>
						</div>
					<?php endif ;?>

					<?php if ($this->params->get('show_info', 1) || $tparams->get('show_links')) : ?>
					<div class="contact-info">
						<?php if ($this->params->get('show_info', 1)) :?>
							<?php if ($this->contact->con_position && $tparams->get('show_position')) : ?>
							<dl class="contact-position dl-horizontal">
								<dt><?php echo Text::_('COM_CONTACT_POSITION'); ?>:</dt>
								<dd itemprop="jobTitle">
								<?php echo $this->contact->con_position; ?>
								</dd>
							</dl>
							<?php endif; ?>

							<?php if (($this->params->get('address_check') > 0) && ($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) : ?>
							<?php echo $this->loadTemplate('address'); ?>
							<?php endif; ?>
							
							<?php if ($tparams->get('allow_vcard')) : ?>
							<div class="uk-margin">
							<span><?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?></span>
							<a class="uk-link-reset" href="<?php echo Route::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->contact->id . '&amp;format=vcf'); ?>">
							<?php echo Text::_('COM_CONTACT_VCARD'); ?></a>
							</div>
							<?php endif; ?>

						<?php endif; ?>
					</div>

					<?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
						<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
						<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
					<?php endif; ?>

					<?php if ($tparams->get('show_links')) : ?>
						<?php echo $this->loadTemplate('links'); ?>
					<?php endif; ?>

					<?php endif; ?>

					<?php echo $this->item->event->afterDisplayTitle; ?>

					<?php if ($tparams->get('show_profile') && $this->contact->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>	
						<?php echo '<h3>'. Text::_('COM_CONTACT_PROFILE').'</h3>'; ?>
						<?php echo $this->loadTemplate('profile'); ?>
					<?php endif; ?>

					<?php $show_contact_category = $tparams->get('show_contact_category'); ?>

					<?php if ($show_contact_category === 'show_no_link') : ?>
						<h3>
							<span class="contact-category"><?php echo $this->contact->category_title; ?></span>
						</h3>
					<?php elseif ($show_contact_category === 'show_with_link') : ?>
						<?php $contactLink = ContactHelperRoute::getCategoryRoute($this->contact->catid); ?>
						<h3>
							<span class="contact-category"><a href="<?php echo $contactLink; ?>">
								<?php echo $this->escape($this->contact->category_title); ?></a>
							</span>
						</h3>
					<?php endif; ?>

					<?php if ($tparams->get('show_contact_list') && count($this->contacts) > 1) : ?>
						<form action="#" method="get" name="selectForm" id="selectForm">
							<label for="select_contact"><?php echo Text::_('COM_CONTACT_SELECT_CONTACT'); ?></label>
							<?php echo HTMLHelper::_('select.genericlist', $this->contacts, 'select_contact', 'class="inputbox" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link); ?>
						</form>
					<?php endif; ?>

					<?php if ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
						<?php echo '<h2>' . Text::_('JGLOBAL_ARTICLES') . '</h2>'; ?>
						<?php echo $this->loadTemplate('articles'); ?>
					<?php endif; ?>

					<?php if ($this->contact->image && $tparams->get('show_image')) : ?>
					</div>
					</div>
					<?php endif; ?>
				</div>

				<div class="uk-margin-remove-first-child">

				<?php endif; ?>

				<?php if ($tparams->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>
					<div class="contact-form">
						<div class="contact-title">
							<?php echo '<h3>' . Text::_('COM_CONTACT_EMAIL_FORM') . '</h3>'; ?>
						</div>
						<div class="contact-body">
							<?php echo $this->loadTemplate('form'); ?>
						</div>
				</div>
				<?php endif ;?>
					
		<?php if ($tparams->get('show_contact_category') != 'hide' || ($tparams->get('show_contact_list') && count($this->contacts) > 1) || ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) || $show_info_check || ($tparams->get('show_links')) || ($tparams->get('show_articles') && $this->contact->user_id && $this->contact->articles) || ($tparams->get('show_profile') && $this->contact->user_id && PluginHelper::isEnabled('user', 'profile')) || ($this->contact->misc && $tparams->get('show_misc')) ) : ?>
				</div>
			</div>
		<?php endif; ?>

		</div>
	
	</div>
	<?php endif;?>

	<?php if ($presentation_style != 'plain') : ?>
	<?php if ($tparams->get('show_contact_list') && count($this->contacts) > 1) : ?>
		<form action="#" method="get" name="selectForm" id="selectForm">
			<label for="select_contact"><?php echo Text::_('COM_CONTACT_SELECT_CONTACT'); ?></label>
			<?php echo HTMLHelper::_('select.genericlist', $this->contacts, 'select_contact', 'class="inputbox" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link); ?>
		</form>
	<?php endif; ?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>
</div>
<?php endif; ?>

<?php if(JVERSION >= 4) : ?>
	<div class="com-contact contact" itemscope itemtype="https://schema.org/Person">
    <?php if ($tparams->get('show_page_heading')) : ?>
        <h1>
            <?php echo $this->escape($tparams->get('page_heading')); ?>
        </h1>
    <?php endif; ?>
	
	<?php $show_contact_category = $tparams->get('show_contact_category'); ?>
	<?php $show_info_init = ($this->params->get('show_info', 1) && (($this->item->image && $tparams->get('show_image') || ($this->item->con_position && $tparams->get('show_position')) || ($tparams->get('allow_vcard'))))); ?>

	<?php if (($this->item->name && $tparams->get('show_name')) || ($show_contact_category === 'show_no_link' || $show_contact_category === 'show_with_link') || ($tparams->get('show_contact_list') && count($this->contacts) > 1) || ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) || $show_info_init) : ?>
		<div class="uk-child-width-expand" uk-grid>

		<div class="uk-width-1-3@m">

		<div class="uk-panel uk-tile-muted uk-padding uk-margin-remove-first-child uk-margin">
    
		<?php if ($this->item->name && $tparams->get('show_name')) : ?>
        <div class="page-header">
			<<?php echo $htag; ?>>
				<?php if ($this->item->published == 0) : ?>
					<span class="badge bg-warning text-light"><?php echo Text::_('JUNPUBLISHED'); ?></span>
				<?php endif; ?>
				<span class="contact-name" itemprop="name"><?php echo $this->item->name; ?></span>
			</<?php echo $htag; ?>>
		</div>
		<?php endif; ?>

    <?php if ($show_contact_category === 'show_no_link') : ?>
        <h3>
            <span class="contact-category"><?php echo $this->item->category_title; ?></span>
        </h3>
    <?php elseif ($show_contact_category === 'show_with_link') : ?>
        <?php $contactLink = RouteHelper::getCategoryRoute($this->item->catid, $this->item->language); ?>
        <h3>
            <span class="contact-category"><a href="<?php echo $contactLink; ?>">
                <?php echo $this->escape($this->item->category_title); ?></a>
            </span>
        </h3>
    <?php endif; ?>

    <?php echo $this->item->event->afterDisplayTitle; ?>

    <?php if ($tparams->get('show_contact_list') && count($this->contacts) > 1) : ?>
        <form action="#" method="get" name="selectForm" id="selectForm">
            <label for="select_contact"><?php echo Text::_('COM_CONTACT_SELECT_CONTACT'); ?></label>
            <?php echo HTMLHelper::_(
                'select.genericlist',
                $this->contacts,
                'select_contact',
                'class="form-select" onchange="document.location.href = this.value"',
                'link',
                'name',
                $this->item->link
            );
            ?>
        </form>
    <?php endif; ?>

    <?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
        <div class="com-contact__tags">
            <?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
            <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
        </div>
    <?php endif; ?>

    <?php echo $this->item->event->beforeDisplayContent; ?>

    <?php if ($this->params->get('show_info', 1)) : ?>
        <div class="com-contact__container">
            <?php echo '<h3>' . Text::_('COM_CONTACT_DETAILS') . '</h3>'; ?>

            <?php if ($this->item->image && $tparams->get('show_image')) : ?>
                <div class="com-contact__thumbnail uk-margin">
                    <?php echo LayoutHelper::render(
                        'joomla.html.image',
                        [
                            'src'      => $this->item->image,
                            'alt'      => $this->item->name,
                            'itemprop' => 'image',
                        ]
                    ); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->item->con_position && $tparams->get('show_position')) : ?>
                <dl class="com-contact__position contact-position dl-horizontal">
                    <dt><?php echo Text::_('COM_CONTACT_POSITION'); ?>:</dt>
                    <dd itemprop="jobTitle">
                        <?php echo $this->item->con_position; ?>
                    </dd>
                </dl>
            <?php endif; ?>

            <div class="com-contact__info">
                <?php echo $this->loadTemplate('address'); ?>

                <?php if ($tparams->get('allow_vcard')) : ?>
                    <?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
                    <a href="<?php echo Route::_('index.php?option=com_contact&view=contact&catid=' . $this->item->catslug . '&id=' . $this->item->slug . '&format=vcf'); ?>">
                    <?php echo Text::_('COM_CONTACT_VCARD'); ?></a>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>

	</div>

		</div>

		<div>
		<?php endif; ?>
		
		<?php if ($tparams->get('show_email_form') && ($this->item->email_to || $this->item->user_id)) : ?>
        <?php echo '<h3>' . Text::_('COM_CONTACT_EMAIL_FORM') . '</h3>'; ?>
	    <?php if ($canEdit) : ?>
			<div class="icons">
				<div class="float-end">
					<div>
						<?php echo HTMLHelper::_('contacticon.edit', $this->item, $tparams); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php echo $this->loadTemplate('form'); ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_links')) : ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_articles') && $this->item->user_id && $this->item->articles) : ?>
			<?php echo '<h3>' . Text::_('JGLOBAL_ARTICLES') . '</h3>'; ?>

			<?php echo $this->loadTemplate('articles'); ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_profile') && $this->item->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>
			<?php echo '<h3>' . Text::_('COM_CONTACT_PROFILE') . '</h3>'; ?>

			<?php echo $this->loadTemplate('profile'); ?>
		<?php endif; ?>

		<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
			<?php echo $this->loadTemplate('user_custom_fields'); ?>
		<?php endif; ?>

		<?php if ($this->item->misc && $tparams->get('show_misc')) : ?>
			<?php echo '<h3 class="uk-h4 uk-heading-bullet">' . Text::_('COM_CONTACT_OTHER_INFORMATION') . '</h3>'; ?>

			<div class="com-contact__miscinfo contact-miscinfo">
				<dl class="dl-horizontal">
					<dt>
						<?php if (!$this->params->get('marker_misc')) : ?>
							<span class="icon-info-circle" aria-hidden="true"></span>
							<span class="visually-hidden"><?php echo Text::_('COM_CONTACT_OTHER_INFORMATION'); ?></span>
						<?php else : ?>
							<span class="<?php echo $this->params->get('marker_class'); ?>">
								<?php echo $this->params->get('marker_misc'); ?>
							</span>
						<?php endif; ?>
					</dt>
					<dd>
						<span class="contact-misc">
							<?php echo $this->item->misc; ?>
						</span>
					</dd>
				</dl>
			</div>
		<?php endif; ?>
    <?php echo $this->item->event->afterDisplayContent; ?>

	<?php if (($this->item->name && $tparams->get('show_name')) || ($show_contact_category === 'show_no_link' || $show_contact_category === 'show_with_link') || ($tparams->get('show_contact_list') && count($this->contacts) > 1) || ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) || $show_info_init) : ?>
		</div>
</div>
<?php endif; ?>

</div>
<?php endif; ?>