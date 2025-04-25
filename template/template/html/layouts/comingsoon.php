<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use HelixUltimate\Framework\Core\HelixUltimate;

extract($displayData);

$app = Factory::getApplication();
$doc = Factory::getDocument();

/**
 * Load the bootstrap file for enabling the HelixUltimate\Framework namespacing.
 *
 * @since	2.0.0
 */
$bootstrap_path = JPATH_PLUGINS . '/system/helixultimate/bootstrap.php';

if (file_exists($bootstrap_path))
{
	require_once $bootstrap_path;
}
else
{
	die('Install and activate <a target="_blank" rel="noopener noreferrer" href="https://www.joomshaper.com/helix">Helix Ultimate Framework</a>.');
}

$theme = new HelixUltimate;
$site_title = $app->get('sitename');

$offline_title_style = $params->get('comingsoon_title_tyle', '');
$offline_title_style_cls = $offline_title_style ? ' uk-'.$offline_title_style : '';

$offline_title_background_size 	 	 = $params->get('comingsoon_title_background_size', '');
$offline_title_background_size_cls = $offline_title_background_size ? ' uk-background-'.$offline_title_background_size : '';

$offline_title_bg_position 	 	 = $params->get('comingsoon_title_bg_position', 'center-center');

$offline_title_bg_blendmode 	 	 = $params->get('comingsoon_title_bg_blendmode', '');
$offline_title_bg_blendmode_cls = $offline_title_bg_blendmode ? ' uk-background-blend-'.$offline_title_bg_blendmode : '';

$offline_title_bg_color 	 	 = $params->get('comingsoon_title_bg_color');
$offline_title_bg_color_cls = $offline_title_bg_color ? 'background-color: ' . $offline_title_bg_color . ';' : '';
$style = '';

if($offline_title_bg_color)
{
	$style .= 'background-color: ' . $offline_title_bg_color . ';';
} else {
	$style .= $offline_title_bg_color_cls;
}

if($params->get('comingsoon_bg_image'))
{
  $style .= 'background-image: url(' . Uri::base(true) . '/' . $params->get('comingsoon_bg_image') . ');';

}

if($style)
{
	$style = 'style="' . $style . '"';
}

?>

<!doctype html>
<html class="coming-soon" lang="<?php echo $language; ?>" dir="<?php echo $direction; ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<?php
			$theme->head();
			$theme->add_js('uikit.min.js, uikit-icons.min.js');
			$theme->add_js('custom.js');
			$theme->add_css('font-awesome.min.css');
      $theme->add_css('uikit.min.css');
      $theme->add_css('presets/' . $params->get('preset', 'preset1') . '.css');
			$theme->add_css('custom.css');

			//Custom CSS
			if ($custom_css = $params->get('custom_css'))
			{
				$doc->addStyledeclaration($custom_css);
			}

			//Custom JS
			if ($custom_js = $params->get('custom_js'))
			{
				$doc->addScriptdeclaration($custom_js);
			}
		?>
	</head>
<body>

  <jdoc:include type="message" />
 

	<?php if($params->get('comingsoon_bg_image')) : ?>
		<div class="uk-background-norepeat<?php echo $offline_title_style_cls; echo $offline_title_background_size_cls; echo $offline_title_bg_blendmode_cls; ?> uk-background-<?php echo $offline_title_bg_position; ?> uk-flex uk-flex-center uk-flex-middle uk-text-center"<?php echo $style; ?> uk-height-viewport="expand: true">
		<?php else: ?>
			<div class="uk-panel uk-flex uk-flex-center uk-flex-middle uk-text-center<?php echo $offline_title_style_cls; ?>"<?php echo $style; ?> uk-height-viewport="expand: true">
			<?php endif; ?>
      
      <div class="container uk-text-center">

        <?php if($params->get('comingsoon_logo')) : ?>
          <img class="coming-soon-logo" src="<?php echo $params->get('comingsoon_logo'); ?>" alt="<?php echo htmlspecialchars($site_title ?? ""); ?>">
        <?php endif; ?>

        <?php if($params->get('comingsoon_title')) : ?>
          <h1 class="uk-heading-primary"><?php echo htmlspecialchars($params->get('comingsoon_title') ?? ""); ?></h1>
        <?php else: ?>
          <h1 class="uk-heading-primary"><?php echo htmlspecialchars($site_title ?? ""); ?></h1>
        <?php endif; ?>

        <?php if($params->get('comingsoon_content')) : ?>
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="uk-h3">
                <?php echo $params->get('comingsoon_content'); ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <div class="coming-soon-content uk-margin">
                  <?php echo $app->get('offline_message'); ?>
                </div>
              </div>
            </div>
          <?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
            <div class="row justify-content-center">
              <div class="col-lg-8">
                <div class="coming-soon-content">
                  <?php echo Text::_('JOFFLINE_MESSAGE'); ?>
                </div>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <?php
				$facebook 	= $params->get('facebook');
				$twitter  	= $params->get('twitter');
        $tiktok  	= $params->get('tiktok');
        $twitch  	= $params->get('twitch');
        $discord  	= $params->get('discord');
				$youtube 	= $params->get('youtube');
				$linkedin 	= $params->get('linkedin');
				$dribbble 	= $params->get('dribbble');
				$behance 	= $params->get('behance');
				$skype 		= $params->get('skype');
				$telegram 	= $params->get('telegram');
				$vk 		= $params->get('vk');

        if( $params->get('comingsoon_social_icons') && ( $facebook || $twitter || $tiktok || $twitch || $discord || $youtube || $linkedin || $dribbble || $behance || $skype || $telegram || $vk ) )
        {
          $social_output  = '<div class="social-icons">';

          if( $facebook )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $facebook .'"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>';
          }
          if( $twitter )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $twitter .'"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" style="width: 13.56px;position: relative;top: -1.5px;"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg></a>';
          }
          if( $tiktok )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $tiktok .'"><i class="fab fa-tiktok" aria-hidden="true"></i></a>';
          }
          if( $twitch )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $twitch .'"><i class="fab fa-twitch" aria-hidden="true"></i></a>';
          }
          if( $discord )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $discord .'"><i class="fab fa-discord" aria-hidden="true"></i></a>';
          }
          if( $youtube )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $youtube .'"><i class="fab fa-youtube" aria-hidden="true"></i></a>';
          }
          if( $linkedin )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $linkedin .'"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>';
          }
          if( $dribbble )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $dribbble .'"><i class="fab fa-dribbble" aria-hidden="true"></i></a>';
          }
          if( $behance )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $behance .'"><i class="fab fa-behance" aria-hidden="true"></i></a>';
          }
          if( $telegram )
          {
            $social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $telegram .'"><i class="fab fa-telegram" aria-hidden="true"></i></a>';
          }
					if( $vk )
					{
						$social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" target="_blank" rel="noopener noreferrer" href="'. $vk .'"><i class="fab fa-vk" aria-hidden="true"></i></a>';
					}
					if( $skype )
					{
						$social_output .= '<a class="uk-icon-button uk-link-reset uk-margin-small-right" href="skype:'. $skype .'?chat"><i class="fab fa-skype" aria-hidden="true"></i></a>';
					}
          $social_output .= '</div>';

          echo $social_output;
        }
        ?>

        <?php if($params->get('comingsoon_date')) : ?>
          <?php $comingsoon_date = $params->get("comingsoon_date"); ?>

          <div class="uk-grid-small uk-child-width-auto uk-flex-center uk-margin-large" uk-grid uk-countdown="date: <?php echo $comingsoon_date; ?>">
            <div>
              <div class="uk-countdown-number uk-countdown-days"></div>
              <div class="uk-countdown-label uk-margin-small uk-text-center uk-visible@s"><?php echo JText::_("HELIX_ULTIMATE_DAYS"); ?></div>
            </div>
            <div class="uk-countdown-separator">:</div>
            <div>
              <div class="uk-countdown-number uk-countdown-hours"></div>
              <div class="uk-countdown-label uk-margin-small uk-text-center uk-visible@s"><?php echo JText::_("HELIX_ULTIMATE_HOURS"); ?></div>
            </div>
            <div class="uk-countdown-separator">:</div>
            <div>
              <div class="uk-countdown-number uk-countdown-minutes"></div>
              <div class="uk-countdown-label uk-margin-small uk-text-center uk-visible@s"><?php echo JText::_("HELIX_ULTIMATE_MINUTES"); ?></div>
            </div>
            <div class="uk-countdown-separator">:</div>
            <div>
              <div class="uk-countdown-number uk-countdown-seconds"></div>
              <div class="uk-countdown-label uk-margin-small uk-text-center uk-visible@s"><?php echo JText::_("HELIX_ULTIMATE_SECONDS"); ?></div>
            </div>
          </div>

        <?php endif; ?>

        <?php if($theme->count_modules('comingsoon')) : ?>
          <div class="coming-soon-position">
            <jdoc:include type="modules" name="comingsoon" style="sp_xhtml" />
          </div>
        <?php endif; ?>

        <?php if(isset($login) && $login) : ?>
          <div class="uk-flex uk-flex-center">
            <?php echo $login_form; ?>
          </div>
        <?php endif; ?>

        <?php $theme->after_body(); ?>

        </div>

      <?php if($params->get('comingsoon_bg_image')) : ?>
      </div>
      <?php endif; ?>
    

</body>
</html>
