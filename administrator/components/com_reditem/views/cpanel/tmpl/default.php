<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
$i = 1;
?>

<div class="container" id="reditem-cpanel" class="accordion">
	<?php foreach ($this->icons as $group): ?>
	<div class="accordion-group navbar-inverse" id="reditem-cpanel-<?php echo $i;?>">
		<div class="accordion-heading navbar-inner">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#reditem-cpanel-<?php echo $i;?>" href="#collapse-cpanel-<?php echo $i;?>">
				<h4>
					<i class="<?php echo $group['icon'];?>"></i>
					<?php echo $group['text'];?>
				</h4>
			</a>
		</div>
		<div class="accordion-body collapse in" id="collapse-cpanel-<?php echo $i;?>">
			<div class="row-fluid">
			<?php foreach ($group['items'] as $item): ?>
				<?php if ($item['view'] != 'cpanel') :?>
				<?php $stat = (isset($this->stats[$item['view']])) ? $this->stats[$item['view']] : 0; ?>
				<div class="span2">
					<a href="<?php echo $item['link']; ?>" class="reditem-cpanel-icon-link">
						<div class="reditem-cpanel-icon-wrapper">
							<div class="reditem-cpanel-icon">
								<i class="<?php echo $item['icon']; ?> icon-5x"></i>
							</div>
							<?php if ($stat): ?>
								<!--<span class="badge reditem-cpanel-count"><?php echo $stat; ?></span>-->
							<?php endif; ?>
						</div>
						<div class="reditem-cpanel-text">
							<?php echo $item['text']; ?>
						</div>
					</a>
				</div>
				<?php endif;?>
			<?php endforeach;?>
			</div>
		</div>
	</div>
	<?php $i++;?>
	<?php endforeach; ?>
</div>
