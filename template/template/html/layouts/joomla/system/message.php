<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined('JPATH_BASE') or die;
use Joomla\CMS\Language\Text;

$msgList = $displayData['msgList'];

?>
<div id="system-message-container">
    <?php if (is_array($msgList) && !empty($msgList)) : ?>
    <div id="system-message">
        <?php foreach ($msgList as $type => $msgs) : ?>
            <div class="uk-alert uk-alert-<?php echo $type; ?>" uk-alert>
                <?php // This requires JS so we should add it trough JS. Progressive enhancement and stuff. ?>
                <a href="#" class="uk-alert-close uk-close" uk-close></a>

                <?php if (!empty($msgs)) : ?>
                    <h3><?php echo Text::_($type); ?></h3>
                    <?php foreach ($msgs as $msg) : ?>
                        <p><?php echo $msg; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>