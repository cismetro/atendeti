<?php

defined ('JPATH_BASE') or die();

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

$version = new Version();
$JoomlaVersion = $version->getShortVersion();

// use Joomla\Component\Content\Site\Helper\RouteHelper;

$item = $displayData;
$params = $displayData->params;
$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tmpl_params = $template->params;
$detail_center_content = $tmpl_params->get('blog_center_content');

$meta_margin = $tmpl_params->get('leading_blog_list_meta_margin', 'default');
$meta_style = $tmpl_params->get('blog_details_meta_style', 'list');
$meta_margin_cls = $meta_margin == 'default' ? 'uk-margin-top' : 'uk-margin-' . $meta_margin . '-top';

if($meta_style == 'list'){
	$meta_margin_cls .= $detail_center_content ? ' uk-flex-center' : '';
} else {
	$meta_margin_cls .= $detail_center_content ? ' uk-text-center' : '';
}

$useDefList = $params->get('show_publish_date') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author');

$author = $published = $category = '';

if ($params->get('show_author')) {
	$author = ($item->created_by_alias ?: $item->author);
	if (!empty($item->contact_link ) && $params->get('link_author') == true) {
		$author = HTMLHelper::_('link', $item->contact_link, $author);
	}
}

if ($params->get('show_publish_date')) {
	$published = HTMLHelper::_('date', $item->publish_up, Text::_('DATE_FORMAT_LC3'));
	$published = '<time datetime="' . HTMLHelper::_('date', $item->publish_up, 'c') . "\">$published</time>";
}

if ($params->get('show_category')) {
	$category = $item->category_title;
	if ($params->get('link_category') && !empty($item->catid)) {
		$category = '<a href="' . Route::_(version_compare($JoomlaVersion, '4.0.0', '>=') ? Joomla\Component\Content\Site\Helper\RouteHelper::getCategoryRoute($item->catid) : ContentHelperRoute::getCategoryRoute($item->catid)) . '" itemprop="genre">' . $category . '</a>';
	}
}

?>	

<?php if ($useDefList) : ?>
	<?php
		switch ($meta_style)
		{
			case 'list':
				// List
				$blocks = array_filter([
					$published ?: '',
					$author ? "<span>{$author}</span>" : '',
					$category ?: '',
				]);	
				?>

				<ul class="<?php echo $meta_margin_cls; ?> uk-margin-remove-bottom uk-subnav uk-subnav-divider">
					<?php foreach ($blocks as $block) : ?>
						<li><?= $block ?></li>
					<?php endforeach ?>
				</ul>

				<?php
				break;

			default:
				// Sentence
				?>
				<p class="<?php echo $meta_margin_cls; ?> uk-margin-remove-bottom uk-article-meta">
				<?php if ($author && $published) : ?>
					<?php Text::printf('TPL_META_AUTHOR_DATE', $author, $published); ?>
				<?php elseif ($author) : ?>
					<?php Text::printf('TPL_META_AUTHOR', $author); ?>
				<?php elseif ($published) : ?>
					<?php Text::printf('TPL_META_DATE', $published); ?>
				<?php endif; ?>
		
				<?php if ($category) : ?>
					<?php Text::printf('TPL_META_CATEGORY', $category); ?>
				<?php endif; ?>
			</p>
			<?php
				break;
		}
	?>
<?php endif; ?>