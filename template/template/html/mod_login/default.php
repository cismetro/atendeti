<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

JLoader::register('UsersHelperRoute', JPATH_SITE . '/components/com_users/helpers/route.php');

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');

?>
<form action="<?php echo Route::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
	<?php if ($params->get('pretext')) : ?>
		<div class="pretext mb-2">
			<?php echo $params->get('pretext'); ?>
		</div>
	<?php endif; ?>

	<div id="form-login-username" class="form-group">
		<?php if (!$params->get('usetext')) : ?>
			<div class="uk-margin">
	        <div class="uk-inline uk-width-1-1">
	            <input id="modlgn-username" type="text" name="username" class="uk-input" tabindex="0" size="18" placeholder="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>" />
	        </div>
	    </div>
		<?php else : ?>
			<label class="uk-form-label" for="modlgn-username"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
			<input id="modlgn-username" type="text" name="username" class="uk-input" tabindex="0" size="18" placeholder="<?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>" />
		<?php endif; ?>
	</div>

	<div id="form-login-password" class="form-group">
		<?php if (!$params->get('usetext')) : ?>

			<div class="uk-margin">
	        <div class="uk-inline uk-width-1-1">
	            <input id="modlgn-passwd" type="password" name="password" class="uk-input" tabindex="0" size="18" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>" />
	        </div>
	    </div>

		<?php else : ?>
			<label class="uk-form-label" for="modlgn-passwd"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
			<input id="modlgn-passwd" type="password" name="password" class="uk-input" tabindex="0" size="18" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>" />
		<?php endif; ?>
	</div>

	<?php if(version_compare(JVERSION, '4.2.0', '<')) : ?>
		<?php if (empty($twofactormethods) > 1) : ?>
			<div id="form-login-secretkey" class="mb-3">
				<?php if (!$params->get('usetext')) : ?>
					<div class="uk-margin">
						<div class="uk-inline uk-width-1-1">
							<input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="uk-input" tabindex="0" size="18" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>" />
						</div>
					</div>
				<?php else : ?>
					<label class="uk-form-label" for="modlgn-secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
					<input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="uk-input" tabindex="0" size="18" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>" />
					<small class="d-block uk-text-muted"><span class="fas fa-asterisk" aria-hidden="true"></span> <?php echo Text::_('JGLOBAL_SECRETKEY_HELP'); ?></small>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
		<div id="form-login-remember" class="uk-form-controls">
			<input type="checkbox" name="remember" class="uk-checkbox" value="yes" id="form-login-input-remember-<?php echo $module->id; ?>">
			<label class="uk-form-label" for="form-login-input-remember-<?php echo $module->id; ?>"><?php echo Text::_('MOD_LOGIN_REMEMBER_ME'); ?></label>
		</div>
	<?php endif; ?>

	<div id="form-login-submit" class="uk-margin">
		<button type="submit" tabindex="0" name="Submit" class="uk-button uk-button-primary login-button"><?php echo Text::_('JLOGIN'); ?></button>
	</div>

	<?php $usersConfig = ComponentHelper::getParams('com_users'); ?>
	<ul class="unstyled">
		<?php if ($usersConfig->get('allowUserRegistration')) : ?>
			<li>
				<a href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>">
				<?php echo Text::_('MOD_LOGIN_REGISTER'); ?> <span class="icon-arrow-right"></span></a>
			</li>
		<?php endif; ?>
		<li>
			<a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
			<?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
		</li>
		<li>
			<a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
		</li>
	</ul>

	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>

	<?php if ($params->get('posttext')) : ?>
		<div class="posttext mt-2">
			<?php echo $params->get('posttext'); ?>
		</div>
	<?php endif; ?>

</form>
