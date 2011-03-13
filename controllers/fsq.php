<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class fsq extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->spark('foursquare/1.0');
	}
	
	function index() {
		$date 		= $this->foursquare->date;
		$venue_name = $this->foursquare->venue_name;
		$venue_cat 	= $this->foursquare->venue_cat;
		$venue_type = $this->foursquare->date;
		$venue_icon = $this->foursquare->venue_icon;
		$comment 	= $this->foursquare->comment;
		$address 	= $this->foursquare->address;
		$city 		= $this->foursquare->city;
		$state 		= $this->foursquare->state;
		$geolong 	= $this->foursquare->geolong;
		$geolat 	= $this->foursquare->geolat;
		
		echo "<!DOCTYPE html>";
		echo "<html>";
		echo "<head>";
		echo "<meta name=\"viewport\" content=\"initial-scale=1.0, user-scalable=no\" />";
		?>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			var lat   = "<?php echo $geolat;?>";
			var ln    = "<?php echo $geolong;?>";
			var venue = "<?php echo $venue_name;?>";
			
			function initialize() {
				var latlng = new google.maps.LatLng(lat, ln);
				var myOptions = {
					zoom: 11,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				
				var marker = new google.maps.Marker({
					position: latlng,
					title: venue
				});
				
				var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				marker.setMap(map);
			}
		</script>
		</head>
		<body onload='initialize()'>
			<h1><?php echo $venue_name; ?> (<?php echo $venue_type; ?>)</h1>
			<img class='fs_venue_icon' src=' <?php echo $venue_icon; ?> ' width='32' height='32' alt='Foursquare venue icon' />
			<img src='<?php echo $this->foursquare->get_map_url('600', '400'); ?> ' /><br/>
			
			<hr>
			
			<div id="map_canvas" style="width: 600px; height: 400px;"></div>
		</body>
		</html>
		
		<?php
	}
}
?>
