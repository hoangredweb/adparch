<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

RHelperAsset::load('reditem.backend.min.css', 'com_reditem');

// Run plugin event
JPluginHelper::importPlugin('reditem_quickicon');
$dispatcher = RFactory::getDispatcher();
$icons      = $dispatcher->trigger('getSidebarIcons');
$icons      = $icons[0];
$option     = JFactory::getApplication()->input->get('option', '');
$component  = JFactory::getApplication()->input->get('component', '');
$stats      = ReditemHelperSystem::getStats();
$active     = null;

if (isset($displayData['active']))
{
	$active = $displayData['active'];
}
?>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('.reditem-sidebar .reditem-sidebar-item.active').parent().parent().addClass('in');
		});
	})(jQuery);
</script>

<?php if (!empty($icons)): ?>
<div class="accordion reditem-sidebar" id="reditemSideBarAccordion">
	<?php $index = 0; ?>
	<?php foreach ($icons as $group): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#reditemSideBarAccordion" href="#collapse<?php echo $index ?>">
				<i class="<?php echo $group['icon'];?>"></i>
				<?php echo $group['text'];?>
			</a>
		</div>
		<div id="collapse<?php echo $index ?>" class="accordion-body collapse">
			<?php if (!empty($group['items'])): ?>
				<ul class="nav nav-tabs nav-stacked">
				<?php foreach ($group['items'] as $icon): ?>
					<?php
					$class = '';
					$stat = (isset($stats[$icon['view']])) ? $stats[$icon['view']] : 0;
					?>
					<?php if ($active === $icon['view']): ?>
						<?php $class = 'active'; ?>
					<?php endif; ?>
					<li class="reditem-sidebar-item <?php echo $class ?>">
						<a href="<?php echo $icon['link'] ?>">
							<i class="<?php echo $icon['icon'] ?>"></i>
							<?php echo $icon['text'] ?>
							<?php if ($stat): ?>
							<!--<span class="badge pull-right"><?php echo $stat; ?></span>-->
							<?php endif;?>
						</a>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
	<?php $index++; ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>
