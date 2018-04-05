<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Item
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

ReditemHelperSystem::loadGoogleMapJavascriptLibrary();

$latlng = JText::_('COM_REDITEM_ITEM_LATITUDE_AND_LONGTITUDE_NUMBER_DEFAULT');
$gmapField = $this->form->getField('itemLatLng', 'params');
$itemAddressField = $this->form->getField('itemAddress', 'params');
$params = JComponentHelper::getParams('com_reditem');

if ($gmapField->value) :
	$latlng = $gmapField->value;
endif;
?>

<?php if (empty($params->get('googleApiKey', ''))) : ?>
<div class="alert alert-info">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<div class="pagination-centered">
		<h3><?php echo JText::sprintf(
				'COM_REDITEM_ITEM_GMAP_GKEY_MISSING',
				JRoute::_('index.php?option=com_redcore&view=config&layout=edit&component=com_reditem')
			); ?></h3>
	</div>
</div>
<?php else: ?>
<script type="text/javascript">
	var geocoder;
	var map;
	var marker;

	(function($){
		$(document).ready(function(){

			// Item address event
			$("#itemAddressButton").click(function(event){
				event.preventDefault();
				getLocationFromAddress($('#jform_params_itemAddress').val(), $('#jform_params_itemLatLng'));
			});

			$('#jform_params_itemAddress').on("keyup", function(event){
				if (event.which == 13 || event.keyCode == 13)
				{
					event.preventDefault();
					getLocationFromAddress($(this).val(), $('#jform_params_itemLatLng'));
				}
			});

			// Item location event
			$("#itemLatLngButton").click(function(event){
				event.preventDefault();
				var locationArray = $('#jform_params_itemLatLng').val().split(',');
				getAddressFromCode(locationArray[0], locationArray[1], $('#jform_params_itemAddress'));
			});

			$('#jform_params_itemLatLng').on("keyup", function(event){
				if (event.which == 13 || event.keyCode == 13)
				{
					event.preventDefault();
					var value = $(this).val();
					if (value.indexOf(',') != -1)
					{
						var locationArray = value.split(',');
						var positionLat = parseFloat(locationArray[0]);
						var positionLng = parseFloat(locationArray[1]);

						if (isNaN(positionLat) || isNaN(positionLng))
						{
							return false;
						}

						getAddressFromCode(parseFloat(locationArray[0]), parseFloat(locationArray[1]), $('#jform_params_itemAddress'));
					}
				}
			});

			// Get current location
			$('#itemGmapGetLocation').click(function(event){
				event.preventDefault();
				getCurrentPosition($(this));
			});

			// Avoid Gmap error inside tab
			$("#item-gmap-wrapper").on("shown", function(){
				initialize();
			});
		});
	})(jQuery);

	/**
	 * Initialize gmap data
	 *
	 * @return void
	 */
	function initialize()
	{
		(function($){
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
			map = new google.maps.Map(document.getElementById('item_gmap_field_canvas'), mapOptions);

			// When user click on map, place pin icon and get this position
			google.maps.event.addListener(map, 'click', function(event) {
				getAddressFromCode(event.latLng.lat(), event.latLng.lng(), $('#jform_params_itemAddress'));
			});

			<?php if ($gmapField->value): ?>
			marker = new google.maps.Marker({
				map: map,
				position: latlng,
				draggable: true
			});

			// User start drag pin icon, clear address input field
			google.maps.event.addListener(marker, 'dragstart', function() {
				$('#jform_params_itemAddress').attr('disabled', true);
				$('#jform_params_itemLatLng').attr('disabled', true);
			});

			// When user stop drag, get current position
			google.maps.event.addListener(marker, 'dragend', function() {
				$('#jform_params_itemAddress').attr('disabled', false);
				$('#jform_params_itemLatLng').attr('disabled', false);
				$('#jform_params_itemLatLng').val(marker.getPosition().lat() + ',' + marker.getPosition().lng());
				getAddressFromCode(marker.getPosition().lat(), marker.getPosition().lng(), $('#jform_params_itemAddress'));
			});
			<?php endif; ?>
		})(jQuery);
	}

	/**
	 * Get location data (latitude, longtitude) from address
	 *
	 * @return void
	 */
	function getLocationFromAddress(address, target)
	{
		(function($){
			$('#jform_params_itemAddress').attr('disabled', true);
			$('#jform_params_itemLatLng').attr('disabled', true);
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
						$('#jform_params_itemAddress').attr('disabled', true);
						$('#jform_params_itemLatLng').attr('disabled', true);
					});

					// When user stop drag, get current position
					google.maps.event.addListener(marker, 'dragend', function() {
						// Set position value in input field
						$('#jform_params_itemAddress').attr('disabled', false);
						$('#jform_params_itemLatLng').attr('disabled', false);
						$('#jform_params_itemLatLng').val(marker.getPosition().lat() + ',' + marker.getPosition().lng());
						getAddressFromCode(marker.getPosition().lat(), marker.getPosition().lng(), $('#jform_params_itemAddress'));
					});

					// Set position value in input field
					$(target).val(results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng());

					getAddressFromCode(marker.getPosition().lat(), marker.getPosition().lng(), $('#jform_params_itemAddress'));
				}

				$('#jform_params_itemAddress').attr('disabled', false);
				$('#jform_params_itemLatLng').attr('disabled', false);
			});
		})(jQuery);
	}

	/**
	 * Get address from location data (latitude, longtitude)
	 *
	 * @return void
	 */
	function getAddressFromCode(lat, lng, target)
	{
		(function($){
			$('#jform_params_itemAddress').attr('disabled', true);
			$('#jform_params_itemLatLng').attr('disabled', true);
			var location = new google.maps.LatLng(lat, lng);
			geocoder.geocode({'latLng': location}, function(results, status){
				if (status == google.maps.GeocoderStatus.OK) {

					map.setCenter(location);

					// Clear current marker position
					if (typeof marker != 'undefined')
					{
						marker.setMap(null);
					}

					// Add new position for this marker
					marker = new google.maps.Marker({
						map: map,
						position: location,
						draggable: true
					});

					// User start drag pin icon, clear address input field
					google.maps.event.addListener(marker, 'dragstart', function() {
						$('#jform_params_itemAddress').attr('disabled', true);
						$('#jform_params_itemLatLng').attr('disabled', true);
					});

					// When user stop drag, get current position
					google.maps.event.addListener(marker, 'dragend', function() {
						// Set position value in input field
						$('#jform_params_itemAddress').attr('disabled', false);
						$('#jform_params_itemLatLng').attr('disabled', false);
						$('#jform_params_itemLatLng').val(marker.getPosition().lat() + ',' + marker.getPosition().lng());
						getAddressFromCode(marker.getPosition().lat(), marker.getPosition().lng(), $('#jform_params_itemAddress'));
					});

					$(target).val(results[0].formatted_address);
				}

				$('#jform_params_itemAddress').attr('disabled', false);
				$('#jform_params_itemLatLng').attr('disabled', false);
			});
		})(jQuery);
	}

	/**
	 * Get current location (latittude, longtitude) from current browser
	 *
	 * @return void
	 */
	function getCurrentPosition(target)
	{
		(function($){
			if (navigator.geolocation)
			{
				$(target).attr('disabled', true);
				$('#jform_params_itemAddress').attr('disabled', true);
				$('#jform_params_itemLatLng').attr('disabled', true);

				// Try W3C Geolocation (Preferred)
				navigator.geolocation.getCurrentPosition(function(position) {
						var location = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);

						map.setCenter(location);

						// Clear current marker position
						if (typeof marker != 'undefined')
						{
							marker.setMap(null);
						}

						// Add new position for this marker
						marker = new google.maps.Marker({
							map: map,
							position: location,
							draggable: true
						});

						// User start drag pin icon, clear address input field
						google.maps.event.addListener(marker, 'dragstart', function() {
							$('#jform_params_itemAddress').attr('disabled', true);
							$('#jform_params_itemLatLng').attr('disabled', true);
						});

						// When user stop drag, get current position
						google.maps.event.addListener(marker, 'dragend', function() {
							// Set position value in input field
							$('#jform_params_itemAddress').attr('disabled', false);
							$('#jform_params_itemLatLng').attr('disabled', false);
							$('#jform_params_itemLatLng').val(marker.getPosition().lat() + ',' + marker.getPosition().lng());
							getAddressFromCode(marker.getPosition().lat(), marker.getPosition().lng(), $('#jform_params_itemAddress'));
						});

						$('#jform_params_itemLatLng').val(location.lat() + ',' + location.lng());
						getAddressFromCode(location.lat(), location.lng(), $('#jform_params_itemAddress'));

						$(target).attr('disabled', false);
						$('#jform_params_itemAddress').attr('disabled', false);
						$('#jform_params_itemLatLng').attr('disabled', false);
					},
					function() {
						$(target).attr('disabled', false);
						$('#jform_params_itemAddress').attr('disabled', false);
						$('#jform_params_itemLatLng').attr('disabled', false);
						alert("<?php echo JText::_('COM_REDITEM_ITEM_GET_CURRENT_LOCATION_ERROR_SERVICE_FAIL'); ?>");
					}
				);
			}
			else
			{
				alert("<?php echo JText::_('COM_REDITEM_ITEM_GET_CURRENT_LOCATION_ERROR_BROWSER_FAIL'); ?>");
			}
		})(jQuery);
	}

	google.maps.event.addDomListener(window, 'load', initialize);
</script>

<fieldset class="form-horizontal">
	<div class="control-group">
		<div class="control-label">
			<?php echo $gmapField->label; ?>
		</div>
		<div class="controls">
			<div class="input-append">
				<?php echo $gmapField->input; ?>
				<a class="btn add-on" id="itemLatLngButton" href="javascript:void(0);" onclick="javascript:void(0)">
					<i class="icon-map-marker"></i>
				</a>
			</div>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo $itemAddressField->label; ?>
		</div>
		<div class="controls">
			<div class="input-append">
				<?php echo $itemAddressField->input; ?>
				<a class="btn add-on" id="itemAddressButton" href="javascript:void(0);">
					<i class="icon-map-marker"></i>
				</a>
			</div>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
		</div>
		<div class="controls">
			<a href="javascript:void(0);" class="btn btn-primary" id="itemGmapGetLocation">
				Get Current Location
			</a>
			<p></p>
			<div id="item_gmap_field_canvas"></div>
		</div>
	</div>
</fieldset>
<?php endif; ?>
