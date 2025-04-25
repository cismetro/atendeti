<?php
/**
 * Dialog Dropbar
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

$dialog_dropbar_animation = $data->params->get('dialog_dropbar_animation') ? ' animation:' . $data->params->get('dialog_dropbar_animation') . ';' : '';

$dialog_dropbar_content_width = $data->params->get('dialog_dropbar_content_width', '');
$dropbar_content_width = '';
if (in_array($dialog_dropbar_content_width, ['medium', 'large', 'xlarge', '2xlarge'])) {
  $dropbar_content_width = ' uk-width-' . $dialog_dropbar_content_width . ' uk-margin-auto';

} elseif ($dialog_dropbar_content_width == 'container') {
  $dropbar_content_width = ' container uk-margin-auto';
}

$dialogmainmenuType = $data->params->get('dialog_navbar_menu', 'mainmenu', 'STRING');
$dialogmaxLevel = $data->params->get('dialog_navbar_menu_max_level', 0, 'INT');

$menuModule = Helper::createModule('mod_menu', [
	'title' => 'Main Menu',
	'params' => '{"menutype":"' . $dialogmainmenuType . '","base":"","startLevel":"1","endLevel":"' . $dialogmaxLevel . '","showAllChildren":"1","tag_id":"","class_sfx":"uk-nav uk-nav-' . $dialog_menu_style_cls . ' uk-nav-accordion","window_open":"","layout":"_:nav","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0", "hu_offcanvas": 1}',
	'name' => 'menu'
]);

$searchModule = Helper::getSearchModule();

$dropbarTarget = $data->params->get('header_style') == '.tm-headerbar-bottom' ? : '.uk-navbar-container';

?>

<div id="tm-dialog" class="uk-dropbar uk-dropbar-large uk-dropbar-top" uk-drop="clsDrop:uk-dropbar; flip:false; container:.tm-header; target-y:.tm-header <?php echo $dropbarTarget; ?>; mode:click; target-x:.tm-header <?php echo $dropbarTarget; ?>; stretch:true; bgScroll:false;<?php echo $dialog_dropbar_animation;?> animateOut:true; duration:300; toggle:false">

<div class="tm-height-min-1-1 uk-flex uk-flex-column<?php echo $dropbar_content_width; ?>">

<?php if ( $data->params->get('dialog_center_vertical') ) : ?>
  	<div class="uk-margin-auto-vertical<?php echo $dialog_menu_horizontally; ?>">
  <?php else: ?>
  	<div class="uk-margin-auto-bottom<?php echo $dialog_menu_horizontally; ?>">
<?php endif; ?>

<?php if (! $doc->countModules('dialog') && ! $data->params->get('dialog_show_menu') && ! $data->params->get('dialog_enable_search') && ! $data->params->get('dialog_enable_social') && ! $data->params->get('dialog_enable_contact') ) : ?>
    <p class="uk-alert uk-alert-warning">
        <?php echo JText::_('HELIX_ULTIMATE_NO_MODULE_DIALOG'); ?>
    </p>
<?php endif; ?>

<?php if ( $data->params->get('dialog_show_menu') ) : ?>
  <?php echo ModuleHelper::renderModule($menuModule); ?>
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

<?php if ( $data->params->get('dialog_center_vertical') ) : ?>
	</div>
  <?php else: ?>
	</div>
<?php endif; ?>

</div>

</div>