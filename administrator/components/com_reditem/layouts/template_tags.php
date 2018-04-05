<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tags = $displayData['tags'];
?>
<?php if (!empty($tags)): ?>
	<ul>
	<?php foreach ($tags as $tag => $tagDesc): ?>
		<?php if (is_array($tagDesc)): ?>
			<ul>
				<?php foreach ($tagDesc as $subTag => $subTagDesc) : ?>
					<li class="block">
						<button type="button" class="btn-tag btn btn-small"><?php echo $subTag; ?></button>&nbsp;&nbsp;<?php echo $subTagDesc; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<li class="block">
				<button type="button" class="btn-tag btn btn-small"><?php echo $tag; ?></button>&nbsp;&nbsp;<?php echo $tagDesc; ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
