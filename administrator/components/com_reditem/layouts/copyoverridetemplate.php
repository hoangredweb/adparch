<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

jimport('joomla.filesystem.folder');

$types     = $displayData['types'];
$templates = $displayData['templates'];
$typeIds   = $displayData['typeIds'];
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#btnStart').on('click', function(event){
				event.preventDefault();
				if ($('input[name="template[]"]:checked').length > 0) {
					$('input[name="template[]"]:checked').each(function(){
						var url = 'index.php?option=com_reditem&task=types.ajaxCopyOverrideTemplate&folder=' + $(this).val() + '&typeIds=<?php echo $typeIds ?>';
						var result = $('#' + $(this).attr('data-result'));

						$.ajax({
							url: url,
							cache: false,
							type: "GET"
						})
						.success(function(data){
							if (data == "1") {
								$(result).html("<?php echo JText::_('COM_REDITEM_COPY_OVERRIDE_TEMPLATE_SUCCESS') ?>").addClass('text-success');
							}
							else {
								$(result).html("<?php echo JText::_('COM_REDITEM_COPY_OVERRIDE_TEMPLATE_FAIL') ?>").addClass('text-error');
							}

							$(this).removeAttr('checked');
						});

						// window.parent.closeModal();
					});
				}
			});
		});
	})(jQuery);
</script>

<div class="alert alert-warning">
	<?php echo JText::_('COM_REDITEM_COPY_OVERRIDE_WARNING_FORCE_COPY') ?>
</div>
<?php if (!empty($templates)): ?>
	<table class="table table-striped">
		<thead>
			<th width="1%">#</th>
			<th><?php echo JText::_('COM_REDITEM_COPY_OVERRIDE_TEMPLATE') ?></th>
			<th width="60%"><?php echo JText::_('COM_REDITEM_COPY_OVERRIDE_TEMPLATE_FOLDER') ?></th>
			<th width="10%"><?php echo JText::_('COM_REDITEM_COPY_OVERRIDE_TEMPLATE_RESULT') ?></th>
		</thead>
	<?php foreach ($templates as $template): ?>
		<tr>
			<td><input type="checkbox" value="<?php echo $template->element ?>" name="template[]" data-result="result<?php echo $template->extension_id ?>" />
			</td>
			<td>
				<?php echo ucfirst($template->name) ?>
			</td>
			<td>
				<?php foreach ($types as $type): ?>
					<?php
					$typeName = str_replace('-', '_', JFilterOutput::stringURLSafe($type->title));
					$folder = JPATH_ROOT . '/templates/' . $template->element . '/html/layouts/com_reditem/type_' . $typeName;
					?>
					<p>/templates/<?php echo $template->element ?>/html/layouts/com_reditem/type_<?php echo $typeName ?>
						<?php if (JFolder::exists($folder)): ?>
							<span class="badge badge-warning">Exist</span>
						<?php else: ?>
							<span class="badge badge-default">Not exist</span>
						<?php endif; ?>
					</p>
				<?php endforeach; ?>
			</td>
			<td>
				<strong><span id="result<?php echo $template->extension_id ?>"></span></strong>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	<hr />
	<a href="javascript:void(0);" class="btn btn-primary" id="btnStart"><?php echo JText::_('COM_REDITEM_COPY_OVERRIDE_TEMPLATE_COPY') ?></a>
<?php endif; ?>
