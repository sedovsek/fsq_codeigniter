<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * Foursquare Location fetcher library for CodeIgniter
 * Connect to foursquare to retrive your last location info.
 * Author: Robert Sedovšek (robert.sedovsek@gmail.com)
 
 * Original source can be found here:
 * http://code.google.com/p/foursquare-php/
 * by Elie Bursztein (fourlocfetcher@elie.im)
 
 * modified and tweaked to work with CodeIgniter by Robert Sedovšek
 * Version: 1.0
 * License: GPL
 */

class foursquare {
	public $url  = 'http://api.foursquare.com/v1/user.json';
	private $user;
	private $pass;
	
	public $date       = "";
	public $venue_name = "";
	public $venue_cat  = "";
	public $venue_type = "";
	public $venue_icon = "http://foursquare.com/img/categories/question.png";
	public $comment    = "";
	public $address    = "";
	public $city       = "";
	public $state      = "";
	public $geolong    = "";
	public $geolat     = "";
	
	function __construct() {
		$this->_CI =& get_instance();
		
		$this->user = $this->_CI->config->item('foursquare_user');
		$this->pass = $this->_CI->config->item('foursquare_pass');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->user.":".$this->pass);
		curl_setopt($ch, CURLOPT_USERAGENT, "fetcher " . time());
		curl_setopt($ch, CURLOPT_SSLVERSION,3);
		$data = curl_exec($ch);
		curl_close($ch);
		
		//decoding data
		$fd = json_decode($data);
		
		if (isset($fd->{"unauthorized"})) {
			die("Foursquare widget API: wrong login or password");
		}
		
		//parsing
		try {
			$this->date      = $fd->{"user"}->{"checkin"}->{"created"};
			$this->venue_name = $fd->{"user"}->{"checkin"}->{"venue"}->{"name"};
			
			if (isset($fd->{"user"}->{"checkin"}->{"venue"}->{"primarycategory"}->{"fullpathname"})) {
				$this->venue_cat  =  $fd->{"user"}->{"checkin"}->{"venue"}->{"primarycategory"}->{"fullpathname"};
				$this->venue_cat  = str_replace(':', '/', $this->venue_cat);
			}
			
			if (isset($fd->{"user"}->{"checkin"}->{"venue"}->{"primarycategory"}->{"nodename"})) {
				$this->venue_type =  $fd->{"user"}->{"checkin"}->{"venue"}->{"primarycategory"}->{"nodename"};
				$this->venue_icon =  $fd->{"user"}->{"checkin"}->{"venue"}->{"primarycategory"}->{"iconurl"};
			}
			
			if (isset($fd->{"user"}->{"checkin"}->{"shout"})) {
				$this->comment   =  $fd->{"user"}->{"checkin"}->{"shout"};
			}
			
			if (isset($fd->{"user"}->{"checkin"}->{"venue"}->{"address"})) {
				$this->address   =  $fd->{"user"}->{"checkin"}->{"venue"}->{"address"};
				$this->city      = $fd->{"user"}->{"checkin"}->{"venue"}->{"city"};
				$this->state     = $fd->{"user"}->{"checkin"}->{"venue"}->{"state"};
				$this->geolat    = $fd->{"user"}->{"checkin"}->{"venue"}->{"geolat"};
				$this->geolong   = $fd->{"user"}->{"checkin"}->{"venue"}->{"geolong"};
			}
		} catch(Exception $e) {
			
		}
	}
	
	public function get_map_url($width, $height, $zoom = 12, $markerText = "me", $marker_color = "blue", $mobile = FALSE, $map_type = "roadmap") {
		$map_url     = "http://maps.google.com/maps/api/staticmap?";
		$map_url    .= "sensor=true";
		$map_url    .= "&center=". $this->geolat . ",". $this->geolong;
		
		$map_url    .= "&map_type=" . $map_type;
		
		$map_url    .= "&size=". $width . "x" . $height;
		$map_url    .= "&zoom=" . $zoom;
		
		$markerText = strtoupper(substr($markerText, 0, 1));
		$map_url    .= "&markers=color:".$marker_color."|label:".$markerText."|".$this->geolat.",".$this->geolong . "|";
		
		return $map_url;
	}
}
?>
