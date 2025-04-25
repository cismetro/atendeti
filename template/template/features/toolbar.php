<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use HelixUltimate\Framework\Platform\Helper;
use Joomla\CMS\Helper\ModuleHelper;

/**
 * Helix Ultimate contact information.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureToolbar
{
	/**
	 * Template parameters
	 *
	 * @var		object	$params		The parameters object
	 * @since	1.0.0
	 */
	private $params;
	public $position;
	public $load_pos;
	/**
	 * Constructor function
	 *
	 * @param	object	$params		The template parameters
	 *
	 * @since	1.0.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
		$this->position = 'toolbar-left';
	}
	
	/**
	 * Render the contact features
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
		$doc = Factory::getDocument();
		$header_style = $this->params->get('header_style');
		$outside = $this->params->get('boxed_layout') && $this->params->get('boxed_header_outside');
		$outside_transparent = $this->params->get('toolbar_transparent') && ! empty($this->params->get('transparent_header'));

		$toolbar_visibility = $this->params->get('toolbar_visibility');
		$toolbar_breakpoint_options = $this->params->get('mobile_breakpoint_options', 'm');
		$social_pos = $this->params->get('social_pos');
		$contact_pos = $this->params->get('contact_pos');

		// Toolbar.
		$toolbar_wrap[] = 'tm-toolbar';
		
		if($header_style == 'style-18') {
			$toolbar_wrap[] = $this->params->get('toolbar_transparent') ? 'uk-' . $this->params->get('overlay_header') : 'tm-toolbar-default';
		} else {
			$toolbar_wrap[] = ($outside && $outside_transparent) ? 'uk-' . $this->params->get('transparent_header') : 'tm-toolbar-default';
			$outside ? $toolbar_wrap[] = 'tm-outside' : '';
		}

		if(! empty($toolbar_visibility)) {
			$toolbar_wrap[] = 'uk-visible@'.$toolbar_breakpoint_options;
		}

		$toolbar_container = $this->params->get('toolbar_maxwidth', 'default');
		$toolbar_container_cls[] = 'uk-flex uk-flex-middle';

		$toolbar_center = $this->params->get('toolbar_center', '0');
		
		include_once 'contact.php';
		include_once 'social.php';

		/**
		 * Helper classes for-
		 * social icons, contact info, search.
		 */

		$contact = new HelixUltimateFeatureContact( $this->params );
		$social  = new HelixUltimateFeatureSocial( $this->params );

		// Toolbar Width Container

		if ($outside) {
			$toolbar_container_cls[] = $toolbar_container === 'expand' ? 'uk-container uk-container-expand' : 'container tm-page-width';
		} else {
			$toolbar_container_cls[] = $toolbar_container != 'default' ? 'uk-container uk-container-' . $toolbar_container : 'container';
		}

		$toolbar_container_cls[] = $this->params->get('toolbar_center') ? 'uk-flex-center' : '';

		$toolbar_wrap   = implode( ' ', array_filter( $toolbar_wrap ) );
		$toolbar_container_cls   = implode( ' ', array_filter( $toolbar_container_cls ) );

		$tbl_subnav_cls = $this->params->get('tbl_subnav_divider') ? ' uk-subnav-divider' : '';
		$tbr_subnav_cls = $this->params->get('tbr_subnav_divider') ? ' uk-subnav-divider' : '';

		$toolbar_left_menu = Helper::createModule('mod_menu', [
			'title' => 'Main Menu',
			'params' => '{"menutype":"' . $this->params->get('toolbar_left_menu', 'mainmenu') . '","base":"","startLevel":"1","endLevel":"0","showAllChildren":"0","tag_id":"","class_sfx":"' . $tbl_subnav_cls . '","window_open":"","layout":"_:subnav","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}',
			'name' => 'menu'
		]);

		$toolbar_right_menu = Helper::createModule('mod_menu', [
			'title' => 'Main Menu',
			'params' => '{"menutype":"' . $this->params->get('toolbar_right_menu', 'mainmenu') . '","base":"","startLevel":"1","endLevel":"0","showAllChildren":"0","tag_id":"","class_sfx":"' . $tbr_subnav_cls . '","window_open":"","layout":"_:subnav","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}',
			'name' => 'menu'
		]);

		$enable_toolbar_left_menu = $this->params->get('toolbar_left_enable_menu', 0);
		$enable_toolbar_right_menu = $this->params->get('toolbar_right_enable_menu', 0);
		
		$output = '';

		if ($doc->countModules('toolbar-left') || $doc->countModules('toolbar-right') || $social_pos === 'toolbar-left' || $social_pos === 'toolbar-right' || $contact_pos === 'toolbar-left' || $contact_pos === 'toolbar-right' || $enable_toolbar_left_menu || $enable_toolbar_right_menu ) {
			$output .= '<div class="' . $toolbar_wrap . '">';
			$output .= '<div class="' . $toolbar_container_cls . '">';
		
			if ($toolbar_center) {
		
			$output .= '<div>';
			$output .= '<div class="uk-grid-medium uk-child-width-auto uk-flex-middle uk-grid" uk-grid="margin: uk-margin-small-top">';
		
			if ($doc->countModules('toolbar-left') || $social_pos === 'toolbar-left' || $contact_pos === 'toolbar-left' || $enable_toolbar_left_menu ) {
		
				if ($contact_pos === 'toolbar-left') {
					$output .= '<div>';
						$output .= $contact->renderFeature();
					$output .= '</div>';
				}
		
				if ($social_pos === 'toolbar-left') {
					$output .= '<div>';
						$output .= $social->renderFeature();
					$output .= '</div>';
				}

				if ($enable_toolbar_left_menu) {
					$output .= ModuleHelper::renderModule($toolbar_left_menu, ['style' => 'grid_stack']);
				}
		
				$output .= '<jdoc:include type="modules" name="toolbar-left" style="grid_stack" />';
			}
		
			if ($doc->countModules('toolbar-right') || $social_pos === 'toolbar-right' || $contact_pos === 'toolbar-right' || $enable_toolbar_right_menu ) {
		
				$output .= '<jdoc:include type="modules" name="toolbar-right" style="grid_stack" />';
		
				if ($contact_pos === 'toolbar-right') {
					$output .= '<div>';
						$output .= $contact->renderFeature();
					$output .= '</div>';
				}
		
				if ($social_pos === 'toolbar-right') {
					$output .= '<div>';
						$output .= $social->renderFeature();
					$output .= '</div>';
				}

				if ($enable_toolbar_right_menu) {
					$output .= ModuleHelper::renderModule($toolbar_right_menu, ['style' => 'grid_stack']);
				}
			}
			$output .= '</div>';
			$output .= '</div>';
			} else {
		
			if ($doc->countModules('toolbar-left') || $social_pos === 'toolbar-left' || $contact_pos === 'toolbar-left' || $enable_toolbar_left_menu ) {
		
				$output .= '<div class="toolbar-left">';
				$output .= '<div class="uk-grid-medium uk-child-width-auto uk-flex-middle uk-grid" uk-grid="margin: uk-margin-small-top">';
		
				if ($contact_pos === 'toolbar-left') {
					$output .= '<div>';
					$output .= $contact->renderFeature();
					$output .= '</div>';
				}
		
				if ($social_pos === 'toolbar-left') {
					$output .= '<div>';
						$output .= $social->renderFeature();
					$output .= '</div>';
				}

				if ($enable_toolbar_left_menu) {
					$output .= ModuleHelper::renderModule($toolbar_left_menu, ['style' => 'grid_stack']);
				}
		
				$output .= '<jdoc:include type="modules" name="toolbar-left" style="grid_stack" />';
		
				$output .= '</div>';
				$output .= '</div>';
			}
		
			if ($doc->countModules('toolbar-right') || $social_pos === 'toolbar-right' || $contact_pos === 'toolbar-right' || $enable_toolbar_right_menu ) {
		
				$output .= '<div class="toolbar-right uk-margin-auto-left">';
				$output .= '<div class="uk-grid-medium uk-child-width-auto uk-flex-middle uk-grid" uk-grid="margin: uk-margin-small-top">';
		
				$output .= '<jdoc:include type="modules" name="toolbar-right" style="grid_stack" />';
		
				if ($contact_pos === 'toolbar-right') {
					$output .= '<div>';
					$output .= $contact->renderFeature();
					$output .= '</div>';
				}
		
				if ($social_pos === 'toolbar-right') {
					$output .= '<div>';
					$output .= $social->renderFeature();
					$output .= '</div>';
				}
		
				if ($enable_toolbar_right_menu) {
					$output .= ModuleHelper::renderModule($toolbar_right_menu, ['style' => 'grid_stack']);
				}

				$output .= '</div>';
				$output .= '</div>';
			}

			}

			$output .= '</div>';
			$output .= '</div>';
			
		}

		return $output;
    }
}
