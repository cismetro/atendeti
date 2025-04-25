<?php
/**
 * Offcanvas (Collapsed)
 */

 defined ('_JEXEC') or die('Restricted Access');

use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;

use Joomla\CMS\Factory;

$doc                  = Factory::getDocument();

$data = $displayData;

$navbar_search = $data->params->get('search_position');
$feature_folder_path = JPATH_THEMES . '/' . $data->template->template . '/features';
$dialog_offcanvas_mode = $data->params->get('dialog_offcanvas_mode', 'slide');
$dialog_offcanvas_overlay = $data->params->get('dialog_offcanvas_overlay') ? ' overlay: true; ' : '';

$dialog_offcanvas_flip = $data->params->get('dialog_offcanvas_flip', 0) ? ' flip: true;' : '';
$dialog_menu_horizontally = $data->params->get('dialog_menu_horizontally', 0) ? ' uk-text-center' : '';

include_once $feature_folder_path . '/contact.php';
include_once $feature_folder_path . '/cookie.php';
include_once $feature_folder_path . '/logo.php';
include_once $feature_folder_path . '/menu.php';
include_once $feature_folder_path . '/mobile.php';
include_once $feature_folder_path . '/search.php';
include_once $feature_folder_path . '/social.php';

$social_pos = $data->params->get('social_pos');
$contact_pos = $data->params->get('contact_pos');

/**
 * Helper classes for-
 * social icons, contact info, site logo, Menu header, toolbar, cookie, search.
 */

$contact = new HelixUltimateFeatureContact( $data->params );
$cookie  = new HelixUltimateFeatureCookie( $data->params );
$logo    = new HelixUltimateFeatureLogo( $data->params );
$menu    = new HelixUltimateFeatureMenu( $data->params );
$mobile    = new HelixUltimateFeatureMobile( $data->params );
$search  = new HelixUltimateFeatureSearch( $data->params );
$social  = new HelixUltimateFeatureSocial( $data->params );
$logo_init = $data->params->get('logo_image') || $data->params->get('logo_text') || $doc->countModules('logo');

$dialog_menu_style_cls = $data->params->get('dialog_menu_options') ? 'primary' : 'default';
$dialog_menu_style_cls .= $data->params->get('dialog_menu_divider', 0) ? ' uk-nav-divider' : '';
$dialog_menu_style_cls .= $data->params->get('dialog_menu_horizontally', 0) ? ' uk-nav-center' : '';

$dialogmainmenuType = $data->params->get('dialog_navbar_menu', 'mainmenu', 'STRING');
$dialogmaxLevel = $data->params->get('dialog_navbar_menu_max_level', 0, 'INT');

$menuModule = Helper::createModule('mod_menu', [
	'title' => 'Main Menu',
	'params' => '{"menutype":"' . $dialogmainmenuType . '","base":"","startLevel":"1","endLevel":"' . $dialogmaxLevel . '","showAllChildren":"1","tag_id":"","class_sfx":"uk-nav uk-nav-' . $dialog_menu_style_cls . '","window_open":"","layout":"_:canvas","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0", "hu_offcanvas": 1}',
	'name' => 'menu'
]);

$searchModule = Helper::getSearchModule();

?>

<div id="tm-dialog" class="uk-offcanvas offcanvas-menu" uk-offcanvas="mode:<?php echo $dialog_offcanvas_mode; ?>;<?php echo $dialog_offcanvas_overlay; echo $dialog_offcanvas_flip; ?>">

<div class="uk-offcanvas-bar uk-flex uk-flex-column offcanvas-inner<?php echo $dialog_menu_horizontally; ?>">

<button class="uk-offcanvas-close uk-close-large" type="button" uk-close></button>

<?php if ( $data->params->get('dialog_center_vertical') ) : ?>
  <div class="uk-margin-auto-vertical">
<?php endif; ?>

<?php if (! $doc->countModules('dialog') && ! $data->params->get('dialog_show_menu') && ! $data->params->get('dialog_enable_search') && ! $data->params->get('dialog_enable_social') && ! $data->params->get('dialog_enable_contact') ) : ?>
    <p class="uk-alert uk-alert-warning">
        <?php echo JText::_('HELIX_ULTIMATE_NO_MODULE_DIALOG'); ?>
    </p>
<?php endif; ?>

<?php if ( $data->params->get('dialog_show_menu') ) : ?>
	<?php echo ModuleHelper::renderModule($menuModule, ['style' => 'sp_xhtml']); ?>
<?php endif; ?>

<?php if ( $data->params->get('dialog_center_vertical') ) : ?>
  </div>
<?php endif; ?>

<?php if ( $doc->countModules( 'dialog' ) ) : ?>
	<jdoc:include type="modules" name="dialog" style="offcanvas_xhtml" />
<?php endif; ?>

<?php if ( $data->params->get('dialog_enable_search') ) : ?>
  <div class="uk-margin-top">
  <?php echo ModuleHelper::renderModule($searchModule, ['style' => 'sp_xhtml']); ?>
	</div>
<?php endif; ?>

<?php if ( $data->params->get('dialog_enable_social') ) : ?>
  <div class="uk-margin-top">
	<?php echo $social->renderFeature(); ?>
  </div>
<?php endif; ?>

<?php if ( $data->params->get('dialog_enable_contact') ) : ?>
  <div class="uk-margin-top">
	<?php echo $contact->renderFeature(); ?>
  </div>
<?php endif; ?>

</div>

</div>