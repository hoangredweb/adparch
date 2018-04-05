<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$categories = $displayData['categoriesList'];
$limit      = $displayData['limit'];
$categoryId = JFactory::getApplication()->input->getInt('parent_id', 0);
$homeUrl    = JRoute::_('index.php?option=com_reditem&view=explore');
?>
<script type="text/javascript">
	function submitForm(url)
	{
		var form = document.adminForm;

		form.task = "";
		form.action = url;
		form.submit();
	}

	function clear()
	{
		var form = document.adminForm;
		form.url = "<?php echo JRoute::_('index.php?option=com_reditem&task=explore.clear') ?>";
		form.task = 'explore.clear';
		form.view = 'explore';
		form.submit();
	}
</script>
<div class="toolbar">
	<span>
		<a href="<?php echo JRoute::_('index.php?option=com_reditem&task=explore.clear') ?>">
			<?php echo JText::_("COM_REDITEM_EXPLORE_EXPLORE"); ?>
		</a>
	</span> /
	<?php foreach ($categories as $category): ?>
		<?php if ($categoryId == $category->id): ?>
			<span><?php echo $category->title ?></span>
		<?php else: ?>
			<?php
			$url = 'index.php?option=com_reditem&view=explore&limit=' . $limit . '&parent_id=' . $category->id;
			$url = JRoute::_($url);
			?>
			<span>
				<a href="<?php echo $url ?>">
					<?php echo $category->title; ?>
				</a>
			</span> /
		<?php endif; ?>
	<?php endforeach;?>
</div>
