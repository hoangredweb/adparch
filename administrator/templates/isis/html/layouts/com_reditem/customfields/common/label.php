<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;
$customfield      = $displayData['customfield'];
$params           = new JRegistry($customfield->params);
$required         = $params->get('required', 0);
$tooltip          = $params->get('tooltip', '');
$isTooltipEnabled = $params->get('enable_tooltip', "1");
?>
<label for="<?php echo $customfield->divId ?>" id="<?php echo $customfield->divId ?>-lbl" style="width: 100%;">
	<?php echo $customfield->name ?>
	<?php if ($required): ?><span>&nbsp;*</span><?php endif ?>
	<?php if ($isTooltipEnabled && !empty($tooltip)): ?>
        <p class="help-block small"><?php echo $tooltip ?></p>
	<?php endif; ?>
</label>
