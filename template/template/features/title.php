<?php
/**
 * @package Helix_Ultimate_Framework
 * @author JoomShaper <support@joomshaper.com>
 * Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
/**
 * Helix Ultimate Site Title.
 *
 * @since	1.0.0
 */
class HelixUltimateFeatureTitle
{
	/**
	 * Template parameters
	 *
	 * @var		object	$params		The parameters object
	 * @since	1.0.0
	 */
	private $params;
	public $position;
	/**
	 * Constructor function
	 *
	 * @param	object	$params		The template parameters
	 *
	 * @since	1.0.0
	 */
	public function __construct($params)
	{
		$this->position = 'title';
		$this->params = $params;
	}

	/**
	 * Render the logo features.
	 *
	 * @return	string
	 * @since	1.0.0
	 */
	public function renderFeature()
	{
		$app = Factory::getApplication();
		$menuitem   = $app->getMenu()->getActive();
		$page_title_container = $this->params->get('page_title_maxwidth', 'default');
		$page_title_container_cls = $page_title_container != 'default' ? 'uk-container uk-container-' . $page_title_container : 'container';

		$page_title_style = $this->params->get('page_title_tyle', '');
		$page_title_style_cls = $page_title_style ? ' uk-'.$page_title_style : '';
		$page_title_padding 	 	 = $this->params->get('page_title_padding', '');
		$page_title_padding_cls = $page_title_padding ? ' uk-section uk-section-'.$page_title_padding : ' uk-section';
		
		$page_title_background_size 	 	 = $this->params->get('page_title_background_size', '');
		$page_title_background_size_cls = $page_title_background_size ? ' uk-background-'.$page_title_background_size : '';

		$page_title_bg_position 	 	 = $this->params->get('page_title_bg_position', 'center-center');

		$page_title_bg_visibility 	 	 = $this->params->get('page_title_bg_visibility', '');
		$page_title_bg_visibility_cls = $page_title_bg_visibility ? ' uk-background-image@'.$page_title_bg_visibility : '';

		$page_title_bg_blendmode 	 	 = $this->params->get('page_title_bg_blendmode', '');
		$page_title_bg_blendmode_cls = $page_title_bg_blendmode ? ' uk-background-blend-'.$page_title_bg_blendmode : '';

		$page_title_bg_color 	 	 = $this->params->get('page_title_bg_color');
		$page_title_bg_color_cls = $page_title_bg_color ? 'background-color: ' . $page_title_bg_color . ';' : '';

		$theme_page_title_background_image 	 	 = $this->params->get('page_title_bg_image');
		$page_title_align = $this->params->get('page_title_align', '');
		$page_title_align_cls = ! empty($page_title_align) ? ' uk-text-'.$page_title_align : '';

		$header_style = $this->params->get('header_style');
		$header_height = $this->params->get('header_height', '82px');
		if($menuitem)
		{
			$params = $menuitem->getParams();

			if($params->get('helixultimate_enable_page_title', 0))
			{
				$page_title 		 = $menuitem->title;
				$page_heading 	 	 = $params->get('helixultimate_page_title_heading', 'h2');
				$page_title_alt 	 = $params->get('helixultimate_page_title_alt');
				$page_subtitle 		 = $params->get('helixultimate_page_subtitle');
				$page_title_bg_color = $params->get('helixultimate_page_title_bg_color');
				$page_title_bg_image = $params->get('helixultimate_page_title_bg_image');

				if($page_heading == 'h1')
				{
					$page_sub_heading = 'h2';
				}
				else
				{
					$page_sub_heading = 'h3';
				}

				$style = '';

				
				if($page_title_bg_color)
				{
					$style .= 'background-color: ' . $page_title_bg_color . ';';
				} else {
					$style .= $page_title_bg_color_cls;
				}

				if($page_title_bg_image)
				{
					$style .= 'background-image: url(' . Uri::root(true) . '/' . $page_title_bg_image . ');';
				} elseif ($theme_page_title_background_image) {
					$style .= 'background-image: url(' . Uri::root(true) . '/' . $theme_page_title_background_image . ');';
				}

				if($style)
				{
					$style = ' style="' . $style . '"';
				}

				if($page_title_alt)
				{
					$page_title 	 = $page_title_alt;
				}

				$output = '';

				if($page_title_bg_image)
				{
					$output .= '<div class="sp-page-title uk-background-norepeat'.$page_title_padding_cls.$page_title_style_cls.$page_title_background_size_cls.$page_title_bg_visibility_cls.$page_title_bg_blendmode_cls.' uk-background-'.$page_title_bg_position.'"'. $style .'>';
				} elseif ($theme_page_title_background_image) {
					$output .= '<div class="sp-page-title uk-background-norepeat'.$page_title_padding_cls.$page_title_style_cls.$page_title_background_size_cls.$page_title_bg_visibility_cls.$page_title_bg_blendmode_cls.' uk-background-'.$page_title_bg_position.'"'. $style .'>';
				} else {
					$output .= '<div class="sp-page-title'.$page_title_padding_cls.$page_title_style_cls.'"'. $style .'>';
				}

				if($header_style == 'style-18') {
					$output .= '<div class="tm-header-placeholder uk-margin-remove-adjacent uk-visible@m" style="height: '.$header_height.';"></div>';
				}

				$output .= '<div class="'. $page_title_container_cls . $page_title_align_cls .'">';

				$output .= '<'. $page_heading .' class="uk-heading-primary">'. $page_title .'</'. $page_heading .'>';

				if($page_subtitle)
				{
					$output .= '<'. $page_sub_heading .' class="uk-text-large uk-margin-remove-top">'. $page_subtitle .'</'. $page_sub_heading .'>';
				}

				$output .= '<jdoc:include type="modules" name="breadcrumb" style="none" />';

				$output .= '</div>';
				$output .= '</div>';

				return $output;

			}

		}

	}
}
