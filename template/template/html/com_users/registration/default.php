<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

?>
<div class="tm-form-registration<?php echo $this->pageclass_sfx; ?>">
	<div class="uk-flex uk-flex-center">
		<div class="uk-width-large uk-background-muted uk-padding">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<div class="page-header">
					<h1 class="el-title uk-h3 uk-margin-small-top uk-margin-remove-bottom"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
				</div>
			<?php endif; ?>
			<form id="member-registration" action="<?php echo Route::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
				<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
					<?php $fields = $this->form->getFieldset($fieldset->name); ?>
					<?php if (count($fields)) : ?>
						<fieldset>
							<?php if (isset($fieldset->label)) : ?>
								<legend><?php echo Text::_($fieldset->label); ?></legend>
							<?php endif; ?>
							<div class="uk-grid-small" uk-grid>
								<?php foreach ($fields as $field) : ?>
									<?php if ($field->hidden) : ?>
										<?php echo $field->input; ?>
									<?php else : ?>
										<?php $fieldName = $field->getAttribute('name'); ?>
										<?php if (($fieldName == 'password1') || ($fieldName == 'password2') || ($fieldName == 'email1') || ($fieldName == 'email2')) : ?>
											<div class="uk-width-1-1@m">
											<?php else : ?>
												<div class="uk-width-1-1@m">
												<?php endif; ?>
												<div class="uk-margin">
													<?php echo $field->label; ?>
													<?php if (!$field->required && $field->type !== 'Spacer') : ?>
														<span class="optional"><?php echo Text::_('COM_USERS_OPTIONAL'); ?></span>
													<?php endif; ?>
													<?php echo $field->input; ?>
												</div>
												</div>
											<?php endif; ?>
										<?php endforeach; ?>
											</div>
						</fieldset>
					<?php endif; ?>
				<?php endforeach; ?>
				<div class="uk-margin">
					<div class="uk-form-controls">
						<button type="submit" class="uk-button uk-button-primary validate">
							<?php echo Text::_('JREGISTER'); ?>
						</button>
						<a class="uk-button uk-button-secondary" href="<?php echo Route::_(''); ?>" title="<?php echo Text::_('JCANCEL'); ?>">
							<?php echo Text::_('JCANCEL'); ?>
						</a>
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="registration.register" />
					</div>
				</div>
				<?php echo HTMLHelper::_('form.token'); ?>
			</form>
		</div>
	</div>
</div>