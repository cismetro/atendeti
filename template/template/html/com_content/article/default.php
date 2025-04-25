<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$template = HelixUltimate\Framework\Platform\Helper::loadTemplateData();
$tmpl_params = $template->params;

$relatedArticles = [];
if ($tmpl_params->get('related_article'))
{
	$args['catId'] =  $this->item->catid;
	$args['maximum'] = $tmpl_params->get('related_article_limit');
	$args['itemTags'] = $this->item->tags->itemTags;
	$args['item_id'] = $this->item->id;
	$relatedArticles = HelixUltimate\Framework\Core\HelixUltimate::getRelatedArticles($args);
}

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images ?? "");
$urls    = json_decode($this->item->urls ?? "");
$canEdit = $params->get('access-edit');
$currentDate = Factory::getDate()->format('Y-m-d H:i:s');
$user    = Factory::getUser();
$info    = $params->get('info_block_position', 0);
$page_header_tag = 'h1';
$attribs = json_decode($this->item->attribs ?? "");
$article_format = (isset($attribs->helix_ultimate_article_format) && $attribs->helix_ultimate_article_format) ? $attribs->helix_ultimate_article_format : 'standard';

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));
$isExpired  = JVERSION < 4
	? (strtotime($this->item->publish_down) < strtotime(Factory::getDate())) && $this->item->publish_down != Factory::getDbo()->getNullDate()
	: !is_null($this->item->publish_down) && $this->item->publish_down < $currentDate;

$version = new Version();
$JoomlaVersion = $version->getShortVersion();

$detail_center_content = $tmpl_params->get('detail_center_content');

$title_margin = $tmpl_params->get('blog_details_title_margin', 'default');
$title_margin_cls = $title_margin == 'default' ? 'uk-margin-top' : 'uk-margin-' . $title_margin . '-top';
$title_margin_cls .= $detail_center_content ? ' uk-text-center' : '';

$content_margin = $tmpl_params->get('blog_details_content_margin', 'default');
$content_margin_cls = $content_margin == 'default' ? 'uk-margin-top' : 'uk-margin-' . $content_margin . '-top';

$dropcap = $tmpl_params->get('blog_details_dropcap', 0);
$dropcap_cls = $dropcap ? ' uk-dropcap' : '';
$article_content_width = $tmpl_params->get('article_content_width', '');
$article_image_margin = $tmpl_params->get('article_image_margin', 'default');
$article_image_margin_cls = $article_image_margin == 'default' ? ' uk-margin-top' : ' uk-margin-'.$article_image_margin.'-top';
$tag_cls = $detail_center_content ? ' class="uk-text-center"' : '';

?>
	<div class="uk-article" itemscope itemtype="https://schema.org/Article" data-permalink="<?php echo Route::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language), true, 0, 1); ?>">
		<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getApplication()->get('language') : $this->item->language; ?>">
		<meta property="author" typeof="Person" content="<?php echo $this->escape($this->item->author); ?>">
		<meta property="dateModified" content="<?php echo HTMLHelper::_('date', $this->item->modified, 'c'); ?>">
		<meta property="datePublished" content="<?php echo HTMLHelper::_('date', $this->item->publish_up, 'c'); ?>">
		<meta class="uk-margin-remove-adjacent" property="articleSection" content="<?php echo $this->escape($this->item->category_title); ?>">

	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
		<?php $page_header_tag = 'h2'; ?>
	<?php endif;
	if (!empty($this->item->pagination) && !$this->item->paginationposition && $this->item->paginationrelative)
	{
		echo $this->item->pagination;
	}
	?>

	<?php if ($params->get('float_fulltext') == 'none') : ?>
		<div class="uk-text-center">
	<?php if ($article_format == 'gallery') : ?>
		<?php echo LayoutHelper::render('joomla.content.blog.gallery', array('attribs' => $attribs, 'id' => $this->item->id)); ?>
	<?php elseif ($article_format == 'video') : ?>
		<?php echo LayoutHelper::render('joomla.content.blog.video', array('attribs' => $attribs)); ?>
	<?php elseif ($article_format == 'audio') : ?>
		<?php echo LayoutHelper::render('joomla.content.blog.audio', array('attribs' => $attribs)); ?>
	<?php else : ?>
		<?php echo LayoutHelper::render('joomla.content.full_image', $this->item); ?>
	<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if ($article_content_width): ?>
		<div class="uk-container uk-container-<?php echo $article_content_width; ?>">
	<?php endif; ?>

	<?php // Todo Not that elegant would be nice to group the params 
	?>
	<?php $useDefList = ($params->get('show_publish_date') || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author')); ?>

	<?php if ($info == 0) : ?>
		<?php echo LayoutHelper::render('joomla.content.article_meta_block', $this->item); ?>
	<?php endif; ?>

	<?php if ($params->get('show_title') || $params->get('show_author')) : ?>
		<?php if ($params->get('show_title')) : ?>
			<<?php echo $page_header_tag; ?> class="<?php echo $title_margin_cls; ?> uk-margin-remove-bottom uk-article-title" itemprop="headline">
				<?php echo $this->escape($this->item->title); ?>
			</<?php echo $page_header_tag; ?>>
		<?php endif; ?>
		<?php if ($this->item->state == 0) : ?>
			<span class="uk-label uk-label-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
		<?php endif; ?>
		<?php if (strtotime($this->item->publish_up) > strtotime(Factory::getDate())) : ?>
			<span class="uk-label uk-label-warning"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
		<?php endif; ?>
		<?php if ($isExpired) : ?>
			<span class="uk-label uk-label-warning mb-2"><?php echo Text::_('JEXPIRED'); ?></span>
		<?php endif; ?>
	<?php endif; ?>

	<?php // Content is generated by content plugin event "onContentAfterTitle" 
	?>

	<?php echo $this->item->event->afterDisplayTitle; ?>

	<?php if ($assocParam && !empty($this->item->associations)) : ?>
		<div class="cat-list-association uk-margin-small-top">
		<?php if (JVERSION < 4): ?>
			<?php $associations = ContentHelperAssociation::displayAssociations($this->item->id); ?>
		<?php else: ?>
			<?php $associations = Joomla\Component\Content\Site\Helper\AssociationHelper::displayAssociations($this->item->id); ?>
		<?php endif; ?>
		<span class="icon-globe icon-fw" aria-hidden="true"></span>
		<?php echo Text::_('JASSOCIATIONS'); ?>
		<?php foreach ($associations as $association) : ?>
			<?php if ($this->params->get('flags', 1) && $association['language']->image) : ?>
				<?php $flag = HTMLHelper::_('image', 'mod_languages/' . $association['language']->image . '.gif', $association['language']->title_native, ['title' => $association['language']->title_native], true); ?>
				<a href="<?php echo Route::_($association['item']); ?>"><?php echo $flag; ?></a>
			<?php else : ?>
				<?php $class = 'btn btn-secondary btn-sm btn-' . strtolower($association['language']->lang_code); ?>
				<a class="<?php echo $class; ?>" title="<?php echo $association['language']->title_native; ?>" href="<?php echo Route::_($association['item']); ?>"><?php echo $association['language']->lang_code; ?>
					<span class="visually-hidden"><?php echo $association['language']->title_native; ?></span>
				</a>
			<?php endif; ?>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ($info != 0) : ?>
		<?php echo LayoutHelper::render('joomla.content.article_meta_block', $this->item); ?>
	<?php endif; ?>

	<?php if ($params->get('float_fulltext') != 'none') : ?>
		<div class="uk-text-center<?php echo $article_image_margin_cls; ?>">
	<?php if ($article_format == 'gallery') : ?>
		<?php echo LayoutHelper::render('joomla.content.blog.gallery', array('attribs' => $attribs, 'id' => $this->item->id)); ?>
	<?php elseif ($article_format == 'video') : ?>
		<?php echo LayoutHelper::render('joomla.content.blog.video', array('attribs' => $attribs)); ?>
	<?php elseif ($article_format == 'audio') : ?>
		<?php echo LayoutHelper::render('joomla.content.blog.audio', array('attribs' => $attribs)); ?>
	<?php else : ?>
		<?php echo LayoutHelper::render('joomla.content.full_image', $this->item); ?>
	<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php // Content is generated by content plugin event "onContentBeforeDisplay" 
	?>
	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (
		isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position)))
		|| (empty($urls->urls_position) && (!$params->get('urls_position')))
	) : ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php if ($params->get('access-view')) : ?>
				<?php
				if (!empty($this->item->pagination) && !$this->item->paginationposition && !$this->item->paginationrelative) :
					echo $this->item->pagination;
				endif;
				?>

				<?php if (($tmpl_params->get('social_share') || $params->get('show_vote')) && !$this->print) : ?>

				<div class="uk-flex uk-flex-middle<?php echo $detail_center_content ? ' uk-flex-center' : '' ?> uk-margin-top">

				<?php if ($detail_center_content) : ?>
				<div>

				<div class="uk-grid-medium uk-child-width-auto uk-flex-middle uk-grid" uk-grid>
				<?php endif; ?>
				
				<?php if ($params->get('show_vote')) : ?>
				<div>

				<?php HTMLHelper::_('jquery.token'); ?>
				<?php echo LayoutHelper::render('joomla.content.rating', array('item' => $this->item, 'params' => $params)) ?>
					
				</div>

				<?php endif; ?>

				<?php if ($tmpl_params->get('social_share')) : ?>

				<?php if ($detail_center_content && $params->get('show_vote') ) : ?>	
				<div>
				<?php elseif (!$detail_center_content && $params->get('show_vote')) : ?>
				<div class="uk-margin-auto-left@s">
				<?php else : ?>
					<div>
				<?php endif; ?>

				<?php echo LayoutHelper::render('joomla.content.social_share', $this->item); ?>
					
				</div>

				<?php endif; ?>

				<?php if ($detail_center_content) : ?>
				</div>

			</div>
			<?php endif; ?>

			</div>

			<?php endif; ?>

			<?php if (isset($this->item->toc)) :
				echo $this->item->toc;
			endif; ?>

			<div class="<?php echo $content_margin_cls; echo $dropcap_cls; ?>" property="text">
				<?php echo $this->item->text; ?>
			</div>

			<?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
				<?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
				<p<?php echo $tag_cls; ?>><?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?></p>
			<?php endif; ?>

			<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) : ?>
				<?php echo $this->loadTemplate('links'); ?>
				<?php endif; ?>
				<?php // Optional teaser intro text for guests ?>
				<?php elseif ($params->get('show_noauth') == true && $user->guest) : ?>
				<?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
				<?php echo HTMLHelper::_('content.prepare', $this->item->introtext); ?>
				<?php // Optional link to let them register to see the whole article. ?>

				<?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
					<?php $menu = Factory::getApplication()->getMenu(); ?>
					<?php $active = $menu->getActive(); ?>
					<?php $itemId = $active->id; ?>
					<?php $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
					<?php $link->setVar('return', base64_encode(version_compare($JoomlaVersion, '4.0.0', '>=') ? Joomla\Component\Content\Site\Helper\RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language) : ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language))); ?>
					<p class="readmore">
						<a href="<?php echo $link; ?>" class="register">
							<?php $attribs = json_decode($this->item->attribs ?? ""); ?>
							<?php
							if ($attribs->alternative_readmore == null) :
								echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
							elseif ($readmore = $attribs->alternative_readmore) :
								echo $readmore;
								if ($params->get('show_readmore_title', 0) != 0) :
									echo HTMLHelper::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
								endif;
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo Text::sprintf('COM_CONTENT_READ_MORE_TITLE');
							else :
								echo Text::_('COM_CONTENT_READ_MORE');
								echo HTMLHelper::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
							endif; ?>
						</a>
					</p>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($params->get('show_create_date') || $params->get('show_modify_date') || $params->get('show_hits')) : ?>
				<ul class="uk-list">

					<?php if ($params->get('show_create_date')) : ?>
						<li>
							<time datetime="<?php echo HTMLHelper::_('date', $this->item->created, 'c'); ?>" itemprop="dateCreated">
								<?php echo Text::sprintf('TPL_META_DATE_CREATED', HTMLHelper::_('date', $this->item->created, Text::_('DATE_FORMAT_LC3'))); ?>
							</time>
						</li>
					<?php endif ?>

					<?php if ($params->get('show_modify_date')) : ?>
						<li>
							<time datetime="<?php echo HTMLHelper::_('date', $this->item->modified, 'c'); ?>" itemprop="dateModified">
								<?php echo Text::sprintf('TPL_META_DATE_MODIFIED', HTMLHelper::_('date', $this->item->modified, Text::_('DATE_FORMAT_LC3'))); ?>
							</time>
						</li>
					<?php endif ?>

					<?php if ($params->get('show_hits')) : ?>
						<li>
							<meta content="UserPageVisits:<?php echo $this->item->hits; ?>" itemprop="interactionCount">
							<?php echo Text::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
						</li>
					<?php endif ?>
				</ul>
			<?php endif ?>

			<?php if (!$this->print) : ?> 
				<?php if ($params->get('show_print_icon') || $params->get('show_email_icon') || $canEdit) : ?>
					<ul class="uk-subnav">
					
						<?php if ($params->get('show_print_icon') && JVERSION < 4 ) : ?>
							<li><?php echo HTMLHelper::_('icon.print_popup', $this->item, $params); ?></li>
						<?php endif; ?>

						<?php if ($params->get('show_email_icon') && JVERSION < 4 ) : ?>
							<li><?php echo HTMLHelper::_('icon.email', $this->item, $params); ?></li>
						<?php endif; ?>

						<?php if ($canEdit && !$this->print) : ?>
							<li class="article-can-edit">
								<?php echo HTMLHelper::_('icon.edit', $this->item, $params); ?>
							</li>
						<?php endif; ?>

					</ul>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($useDefList) : ?>
					<div id="pop-print">
						<?php echo HTMLHelper::_('icon.print_screen', $this->item, $params); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ($article_content_width ): ?>
				</div>
			<?php endif; ?>

			<?php
			if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition) :
				echo $this->item->pagination;
			?>
			<?php endif; ?>

			<?php echo LayoutHelper::render('joomla.content.blog.author_info', $this->item); ?>
			<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
			<?php echo $this->item->event->afterDisplayContent; ?>

			<?php if (!$this->print) : ?>
				<?php echo LayoutHelper::render('joomla.content.blog.comments.comments', $this->item); ?>
			<?php endif; ?>

	</div>

	<?php if($tmpl_params->get('related_article') && count($relatedArticles) > 0 ): ?>
	<?php 
		echo LayoutHelper::render('joomla.content.related_articles', ['articles'=>$relatedArticles, 'item'=>$this->item]); 
	?>
	<?php endif; ?>