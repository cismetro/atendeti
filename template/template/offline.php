<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined ('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$twofactormethods	= [];
if(version_compare(JVERSION, '4.2.0', '<'))
{
	$twofactormethods 	= AuthenticationHelper::getTwoFactorMethods();
}
$doc 				= Factory::getDocument();
$app              	= Factory::getApplication();

ob_start();
?>

<form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login" class="uk-grid-small" uk-grid>

	<div>
		<div class="uk-inline">
		<span class="uk-form-icon uk-icon" uk-icon="icon: user"></span>
			<input name="username" type="text" class="uk-input" id="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>">
		</div>
	</div>

	<div>
		<div class="uk-inline">
			<label class="sr-only" for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
			<span class="uk-form-icon uk-form-icon-flip uk-icon" uk-icon="icon: lock"></span>
				<input name="password" type="password" class="uk-input" id="password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
		</div>
	</div>

	<?php if (count($twofactormethods) > 1) : ?>
		<div>
			<div class="uk-inline">
				<label class="sr-only" for="secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
				<span class="uk-form-icon uk-form-icon-flip uk-icon" uk-icon="icon: lock"></span>
				<input name="secretkey" type="text" class="uk-input" id="secretkey" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
			</div>
		</div>
	<?php endif; ?>

	<div>
		<input type="submit" name="Submit" class="uk-button uk-button-primary" value="<?php echo Text::_('JLOGIN'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>

</form>

<?php
$login_form = ob_get_clean();
echo LayoutHelper::render('comingsoon', array('language' => $this->language, 'direction' => $this->direction, 'params' => $this->params, 'login' => true, 'login_form' => $login_form));