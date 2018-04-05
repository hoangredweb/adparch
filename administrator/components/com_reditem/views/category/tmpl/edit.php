<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Category
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
JHtml::_('behavior.modal', 'a.modal-thumb');
JHtml::_('rbootstrap.tooltip');
JHtml::_('behavior.keepalive');

$gmapField = null;
$latlng = JText::_('COM_REDITEM_ITEM_LATITUDE_AND_LONGTITUDE_NUMBER_DEFAULT');

if ($this->useGmapField) :
	ReditemHelperSystem::loadGoogleMapJavascriptLibrary();
	$gmapField = $this->form->getField('categoryLatLng', 'params');

	if ($gmapField->value) :
		$latlng = $gmapField->value;
	endif;
endif;
?>

<script type="text/javascript">
	function jInsertEditorText(tag, editor)
	{
		var img = jQuery(tag);
		var field = jQuery("#" + editor + "_value_media");
		field.val(img.attr("src"));
		var imgPreview = "<img src='<?php echo JUri::root(); ?>" + img.attr("src") + "' style='max-width: 100px; max-height: 100px;' />";
		jQuery("#" + editor + "_media_preview").html(imgPreview);
	}

	function jInsertFieldValue(value, id) {
		(function($){
			$("#" + id).val(value);
			category_media_select();
		})(jQuery);
	}

	function clear_category_image()
	{
		jQuery('#jform_category_image_file').val('');
		jQuery('#jform_category_image_media').val('');
		jQuery('#jform_category_image').val('');
		jQuery('#cat-img-preview-container').remove();
		jQuery('#category_image_name').val('');
		jQuery('#category-image-ext').parent().hide();
	}

	function category_image_select()
	{
		jQuery('#cat-img-preview-container').remove();
		jQuery('#jform_category_image_media').val('');
		var file = jQuery('#jform_category_image_file').val().replace(/^.*[\\\/]/, '');
		var ext = file.split('.')[1].toUpperCase();
		jQuery('#category_image_name').val(file);
		jQuery('#category-image-ext').html(ext).parent().show();
	}

	function category_media_select()
	{
		jQuery('#cat-img-preview-container').remove();
		jQuery('#jform_category_image_file').val('');
		var file = jQuery('#jform_category_image_media').val().replace(/^.*[\\\/]/, '');
		var ext = file.split('.')[1].toUpperCase();
		jQuery('#category_image_name').val(file);
		jQuery('#category-image-ext').html(ext).parent().show();
	}

	Joomla.submitbutton = function(pressbutton)
	{
		submitform( pressbutton );
	};

	jQuery(document).ready(function()
	{
		// Disable click function on btn-group
		jQuery(".btn-group").each(function(index){
			if (jQuery(this).hasClass('disabled'))
			{
				jQuery(this).find("label").off('click');
			}
		});
	});
</script>

<?php if ($this->useGmapField) : ?>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$("#category_gmap_field_address").on("keyup", function(event){
				if (event.which == 13 || event.keyCode == 13)
				{
					event.preventDefault();
					codeAddress();
				}
			});
		});
	})(jQuery);
</script>
<script type="text/javascript">
	var geocoder;
	var map;
	var marker;
	function initialize()
	{
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(<?php echo $latlng; ?>);
		var mapOptions = {
			zoom: 8,
			center: latlng,
			panControl: false,
			zoomControl: false,
			mapTypeControl: false,
			scaleControl: false,
			streetViewControl: false,
			overviewMapControl: false,
		}
		map = new google.maps.Map(document.getElementById('category_gmap_field_canvas'), mapOptions);
		<?php if ($gmapField->value) : ?>
		marker = new google.maps.Marker({
			map: map,
			position: latlng,
			draggable: true
		});

		// User start drag pin icon, clear address input field
		google.maps.event.addListener(marker, 'dragstart', function() {
			document.getElementById('category_gmap_field_address').value = '';
		});

		// When user stop drag, get current position
		google.maps.event.addListener(marker, 'dragend', function() {
			// Set position value in input field
			document.getElementById('jform_params_categoryLatLng').value = marker.getPosition().lat() + ',' + marker.getPosition().lng();
		});
		<?php endif; ?>
	}

	function codeAddress()
	{
		var address = document.getElementById('category_gmap_field_address').value;
		geocoder.geocode( { 'address': address}, function(results, status){
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);

				// Clear current marker position
				if (typeof marker != 'undefined')
				{
					marker.setMap(null);
				}

				// Add new position for this marker
				marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location,
					draggable: true
				});

				// User start drag pin icon, clear address input field
				google.maps.event.addListener(marker, 'dragstart', function() {
					document.getElementById('category_gmap_field_address').value = '';
				});

				// When user stop drag, get current position
				google.maps.event.addListener(marker, 'dragend', function() {
					// Set position value in input field
					document.getElementById('jform_params_categoryLatLng').value = marker.getPosition().lat() + ',' + marker.getPosition().lng();
				});

				// Set position value in input field
				document.getElementById('jform_params_categoryLatLng').value = results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng();
			}
		});
	}

	google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php endif; ?>

<form enctype="multipart/form-data"
	action="index.php?option=com_reditem&task=category.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate" id="adminForm">
	<ul class="nav nav-tabs" id="categoryTab">
		<li class="active"><a href="#category-information" data-toggle="tab">
			<strong><?php echo JText::_('COM_REDITEM_GENERAL_INFORMATION'); ?></strong></a>
		</li>
		<li><a href="#category-options" data-toggle="tab">
			<strong><?php echo JText::_('COM_REDITEM_OPTIONS'); ?></strong></a>
		</li>
		<li>
			<a href="#category-fields" data-toggle="tab" id="additional-link"><strong><?php echo JText::_('COM_REDITEM_FIELD_FIELDS'); ?></strong></a>
		</li>
		<?php if ($this->canConfig) : ?>
		<li>
			<a href="#permission" data-toggle="tab">
				<strong><?php echo JText::_('COM_REDITEM_PERMISSIONS'); ?></strong>
			</a>
		</li>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="category-information">
			<div class="row-fluid">
				<div class="span8">
					<fieldset class="form-horizontal">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('title'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('title'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('alias'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('parent_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('parent_id'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('access'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('access'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('template_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('template_id'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('category_image'); ?>
							</div>
							<div class="controls">
								<div class="media">
									<?php if (!empty($catImage = $this->form->getValue('category_image'))) : ?>
									<div class="pull-left">
										<div class="img-preview-container" id="cat-img-preview-container" style="position:relative;">
											<img id="img_preview_category_image"
											     src="<?php echo JUri::root() . 'media/com_reditem/images/category/' . $this->item->id . '/'. $catImage; ?>"
											     class="img-polaroid"
											     style="max-width: 300px; max-height: 300px; margin-right: 20px;" />
										</div>
									</div>
									<?php endif; ?>
									<div class="media-body">
										<div class="control-group">
											<div class="input-prepend input-append">
												<span class="label label-info add-on" id="jform_category_image_name_desc" style="background-color: #3a87ad">
													<i class="icon-picture"></i>
													<span id="category-image-ext"><?php echo strtoupper(JFile::getExt($catImage)); ?></span>
												</span>
												<input type="text" disabled value="<?php echo $catImage?>" id="category_image_name" />
												<input type="hidden" id="jform_category_image" name="jform[category_image]" value="<?php echo $catImage;?>" />
												<input type="file" style="display: none" name="jform[category_image_file]" id="jform_category_image_file" onchange="category_image_select()" />
												<?php echo $this->form->getInput('category_image_media'); ?>
												<button class="btn btn-info" type="button" onclick="jQuery('#jform_category_image_file').click();">
													<i class="icon-upload"></i>
												</button>
												<a class="btn btn-success modal-thumb" rel="{handler: 'iframe', size: {x: 1050, y: 450}}" title=""
												   href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;fieldid=jform_category_image_media">
													<i class="icon-upload-alt"></i>
													<?php echo JText::_('COM_REDITEM_ITEM_IMAGE_MEDIA');?>
												</a>
												<?php if (!empty($catImage)) : ?>
													<button
														class="btn btn-danger" type="button"
														onclick="clear_category_image()"
													>
														<i class="icon-remove"></i>
													</button>
												<?php endif; ?>
											</div>
										</div>
										<?php echo $this->form->getField('category_image_title', 'params')->renderField();?>
										<?php echo $this->form->getField('category_image_alt', 'params')->renderField();?>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('featured'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('featured'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('published'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('published'); ?>
							</div>
						</div>
						<?php if ($this->item->id) : ?>
						<div class="control-group">
							<div class="control-label">
								<label><?php echo JText::_('COM_REDITEM_CATEGORY_DIRECT_LINK'); ?></label>
							</div>
							<?php $viewLink = JRoute::_(JUri::root() . 'index.php?option=com_reditem&view=categorydetail&id=' . $this->item->id); ?>
							<div class="controls">
								<a href="<?php echo $viewLink; ?>" target="_blank"><?php echo $viewLink; ?></a>
							</div>
						</div>
						<?php endif; ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('introtext'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('introtext'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('fulltext'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('fulltext'); ?>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="span4">
					<fieldset class="form-vertical">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('related_categories'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('related_categories'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('publish_up'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('publish_up'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('publish_down'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('publish_down'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<?php if ($this->useGmapField) : ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $gmapField->label; ?>
							</div>
							<div class="controls">
								<?php echo $gmapField->input; ?>
								<div class="clearfix"></div>
								<div class="category_gmap_field">
									<div id="category_gmap_field_panel">
										<input id="category_gmap_field_address" type="text" class="input" value="" placeholder="Odense, Denmark">
										<input type="button" class="btn" value="<?php echo JText::_('COM_REDITEM_CATEGORY_GEOCODE'); ?>" onclick="codeAddress()">
									</div>
									<div id="category_gmap_field_canvas"></div>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<?php endif; ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('created_user_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('created_user_id'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('created_time'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('created_time'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('modified_user_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('modified_user_id'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('modified_time'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('modified_time'); ?>
							</div>
							<div class="clearfix"></div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="category-options">
			<div class="row-fluid">
				<fieldset class="form-horizontal">
					<div class="control-group">
						<?php $unpublishItemActionField = $this->form->getField('category_item_unpublish_after_user_deactivated', 'params'); ?>
						<div class="control-label">
							<?php echo $unpublishItemActionField->label; ?>
						</div>
						<div class="controls">
							<?php echo $unpublishItemActionField->input; ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="control-group">
						<?php $unpublishItemActionField = $this->form->getField('category_item_unpublish_after_user_deleted', 'params'); ?>
						<div class="control-label">
							<?php echo $unpublishItemActionField->label; ?>
						</div>
						<div class="controls">
							<?php echo $unpublishItemActionField->input; ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="control-group">
						<?php $metaDescriptionField = $this->form->getField('meta_description', 'params'); ?>
						<div class="control-label">
							<?php echo $metaDescriptionField->label; ?>
						</div>
						<div class="controls">
							<?php echo $metaDescriptionField->input; ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="control-group">
						<?php $metaKeywordsField = $this->form->getField('meta_keywords', 'params'); ?>
						<div class="control-label">
							<?php echo $metaKeywordsField->label; ?>
						</div>
						<div class="controls">
							<?php echo $metaKeywordsField->input; ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="control-group">
						<?php $metaRobotsField = $this->form->getField('meta_robots', 'params'); ?>
						<div class="control-label">
							<?php echo $metaRobotsField->label; ?>
						</div>
						<div class="controls">
							<?php echo $metaRobotsField->input; ?>
						</div>
						<div class="clearfix"></div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="tab-pane" id="category-fields">
			<div class="row-fluid">
				<fieldset class="form-horizontal">
					<?php echo $this->loadTemplate('fields'); ?>
				</fieldset>
			</div>
		</div>
		<?php if ($this->canConfig) : ?>
		<div class="tab-pane" id="permission">
			<div class="row-fluid">
				<?php echo $this->form->getInput('rules'); ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<?php if ($this->fromExplore) :?>
	<input id="return" name="return" type="hidden" value="<?php echo base64_encode(RRoute::_('index.php?option=com_reditem&view=explore&parent_id=' . $this->parentId, false)); ?>" />
	<?php endif;?>
</form>
