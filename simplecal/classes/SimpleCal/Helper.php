<?php

namespace SimpleCal;

class Helper {
	// Filter events for a given month and year
	public static function get_events_calendar($month, $year) {

	}

	// Retrieve the date from within the loop
	public static function event_get_the_date(string $date_or_time = 'both', string $start_or_end = "both", string $date_format = 'M d, Y', string $time_format = 'g:i a', string $span_link = ' - ', string $date_time_link = ' at ', bool $nbsp_on_null = false ) {
		// TODO: Add support for "doors" time. Maybe a separate function?
		global $post;
		$nbsp = '&nbsp;';

		if ($post) {
			$post_timezone = ($post->simplecal_event_timezone) ? new \DateTimeZone($post->simplecal_event_timezone) : Plugin::$tz;
			$starttimestamp = new \DateTime($post->simplecal_event_start_timestamp, $post_timezone);
			
			if ($post->simplecal_event_end_timestamp && ($post->simplecal_event_start_timestamp != $post->simplecal_event_end_timestamp)) {
				$endtime = $post->simplecal_event_end_timestamp;
				$endtimestamp = new \DateTime($endtime, $post_timezone);
			}
		} else { // This is for block themes and their API-based nonsense
			$post_timezone = Plugin::$tz;
			$starttimestamp = new \DateTime('now',$post_timezone);
		}

		$date_string = '';

		// Formatting is too different to mess with nested switches and whatnot
		switch ($start_or_end) {
			case 'start':
				switch ($date_or_time) {
					case 'date' :
						$date_string .= $starttimestamp->format($date_format);
						break;
					case 'both':
						$date_string .= $starttimestamp->format($date_format);
						if ($post && !$post->simplecal_event_all_day) { // If it's *not* an all-day event, include the start time
							$date_string .= $date_time_link;
						}
					case 'time':
						if ($post && !$post->simplecal_event_all_day) { // If it's *not* an all-day event, include the start time
							$date_string .= $starttimestamp->format($time_format);
						}
						break;
				}
				
				return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
			case 'end':
				if (!isset($endtime)) {
					return ($nbsp_on_null ? $nbsp : null);
				}

				switch ($date_or_time) {
					case 'date' :
						$date_string .= $endtimestamp->format($date_format);
						break;
					case 'both':
						$date_string .= $endtimestamp->format($date_format);
						if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, include the end time
							$date_string .= $date_time_link;
						}
					case 'time':
						if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, include the end time
							$date_string .= $endtimestamp->format($time_format);
						}
						break;
				}
				return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
			case 'both':
				switch ($date_or_time) {
					case 'date' :
						$date_string .= $starttimestamp->format($date_format);
						break;
					case 'both':
						$date_string .= $starttimestamp->format($date_format);
						if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, append date-time separation
							$date_string .= ' ';
						}
					case 'time':
						if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, include the start time
							$date_string .= $starttimestamp->format($time_format);
						}
						break;
				}

				if (!isset($endtime)) {
					return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
				}

				if ($starttimestamp->format('ymd') == $endtimestamp->format('ymd')) { // If start and end date are the same
					if ('date' == $date_or_time) { // If it's only meant to return dates, then just return the start date
						return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
					}

					if (!$post->simplecal_event_all_day || $starttimestamp->format('Hi') != $endtimestamp->format('Hi')) { // If it's not an all-day event and start and end times are different, append the end time
						if ('both' == $date_or_time) $date_string .= ' ';
						$date_string .= $span_link . $endtimestamp->format($time_format);
					}

					return $date_string;
				} else { // If start and end date are different
					if ('time' != $date_or_time) { // Don't include the date if it's set to only return the time.
						$date_string .= $span_link . $endtimestamp->format($date_format);
					}
					
					switch ($date_or_time) {
						case 'date': // If it's only meant to return dates, return only the date portion
							return $date_string;
							case 'both':
								if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, append date-time separation
								$date_string .= ' ';
							}
						case 'time':
							if (! $post->simplecal_event_all_day) { // If it's *not* an all-day event, include the end time
								$date_string .= $span_link . $endtimestamp->format($time_format);
							}
							break;
					}
					
				}
				return ($nbsp_on_null && !$date_string ? $nbsp : $date_string);
		}
	}

	public static function event_has_valid_end_timestamp() {
		global $post;
		
		if ($post->simplecal_event_end_timestamp && ($post->simplecal_event_start_timestamp != $post->simplecal_event_end_timestamp)) {
			return true;
		} else {
			return false;
		}
	}


	public static function event_get_the_location($link_type = 'none', $check_only = false, $as_plain_text = false) {
		global $post;
		
		if ($post->simplecal_event_venue_name || $post->simplecal_event_city) {
			$link = urlencode(implode(", ", array_filter([$post->simplecal_event_venue_name, $post->simplecal_event_street_address, $post->simplecal_event_city, $post->simplecal_event_state, $post->simplecal_event_country], 'strlen')));
			if ($link) $link = 'https://maps.google.com/maps?q=' . $link;
			
			$address = '';
			$address .= $post->simplecal_event_venue_name ? '<span class="simplecal_list_item_venue_name">' . $post->simplecal_event_venue_name . '<span>' : '';
			$address .= $post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state) ? '<span class="simplecal_list_item_venue_separator">, </span>' : '';
			$address .= $post->simplecal_event_city ? '<span class="simplecal_list_item_city">' . $post->simplecal_event_city . '</span>' : '';
			$address .= $post->simplecal_event_city && $post->simplecal_event_state ? '<span class="simplecal_list__item_city_separator">, </span>' : '';
			$address .= $post->simplecal_event_state ? '<span class="simplecal_list_item_state">' . $post->simplecal_event_state . '</span>' : '';

			if ($check_only) {
				return !empty($address);
			}

			if ($as_plain_text) {
				$address = preg_replace('~<([^<>]*)>~','',$address);
			}

			switch ($link_type) {
				case 'text':
					$data = "<a href='$link' target='_blank'>$address</a>";
					break;
				case 'after':
					$data = "$address (<a href='$link' target='_blank'>Map</a>)";
					break;
				default:
					$data = $address;
			}

			return $data;
		} else {
			return null;
		}
	}

	public static function get_formatted_website($url, $link_type = 'text', $link_text = null) {
		if (preg_match('/^(?:https?:\/\/)?(?:www\.)?(.*\..*)\/?/', $url, $matches)) {
			$domain = explode('/',$matches[1])[0];
		} else {
			$domain = explode('/',$url)[0];
		}

		if (empty($link_text)) {
				$link_text = $domain;
		}

		switch ($link_type) {
			case 'text':
				return "<a href=\"$url\" title=\"$url\" target=\"_blank\">$link_text</a>";
			case 'after':
				return "$url (<a href=\"$url\" title=\"$url\" target=\"_blank\">$link_text</a>)";
			case 'none': // For consistency in behavior
			default:
				return $url;
		}
	}

	public static function get_state_input($field_value, $dom_name = 'simplecal_state', $dom_id = 'simplecal_state', $addl_attrs = null) {
		if (empty($field_value)) {
			$field_value = get_option('simplecal_last_state','');
		}

		$states = [
			'AL'=>'Alabama',
			'AK'=>'Alaska',
			'AZ'=>'Arizona',
			'AR'=>'Arkansas',
			'CA'=>'California',
			'CO'=>'Colorado',
			'CT'=>'Connecticut',
			'DE'=>'Delaware',
			'DC'=>'District of Columbia',
			'FL'=>'Florida',
			'GA'=>'Georgia',
			'HI'=>'Hawaii',
			'ID'=>'Idaho',
			'IL'=>'Illinois',
			'IN'=>'Indiana',
			'IA'=>'Iowa',
			'KS'=>'Kansas',
			'KY'=>'Kentucky',
			'LA'=>'Louisiana',
			'ME'=>'Maine',
			'MD'=>'Maryland',
			'MA'=>'Massachusetts',
			'MI'=>'Michigan',
			'MN'=>'Minnesota',
			'MS'=>'Mississippi',
			'MO'=>'Missouri',
			'MT'=>'Montana',
			'NE'=>'Nebraska',
			'NV'=>'Nevada',
			'NH'=>'New Hampshire',
			'NJ'=>'New Jersey',
			'NM'=>'New Mexico',
			'NY'=>'New York',
			'NC'=>'North Carolina',
			'ND'=>'North Dakota',
			'OH'=>'Ohio',
			'OK'=>'Oklahoma',
			'OR'=>'Oregon',
			'PA'=>'Pennsylvania',
			'RI'=>'Rhode Island',
			'SC'=>'South Carolina',
			'SD'=>'South Dakota',
			'TN'=>'Tennessee',
			'TX'=>'Texas',
			'UT'=>'Utah',
			'VT'=>'Vermont',
			'VA'=>'Virginia',
			'WA'=>'Washington',
			'WV'=>'West Virginia',
			'WI'=>'Wisconsin',
			'WY'=>'Wyoming'
		];

		$output = "<select name='$dom_name' id='$dom_id' $addl_attrs>";
		$output .= "<option>--</option>";
		foreach ($states as $code=>$name) {
			$output .= "<option value=\"$code\"" . ($field_value == $code ? ' selected ' : '') . ">$name</option>";
		}

		$output .= "</select>";
		return $output;
	}

	public static function state_input($field_value = null, $dom_name = null, $dom_id = null, $addl_attrs = null) {
		echo self::get_state_input($field_value, $dom_name, $dom_id, $addl_attrs);
	}

	public static function get_country_input($field_value, $dom_name = 'simplecal_country', $dom_id = 'simplecal_country', $addl_attrs = null) {
		if (empty($field_value)) { $field_value = get_option('simplecal_last_country', null);} // Apparently null colaescing assignment (??=) doesn't work when the variable is passed?

		$output = "<select name='$dom_name' id='$dom_id' $addl_attrs>";

		// TODO: Check if file exists
		$file_data = file_get_contents(Plugin::$path . '/util/countries.json'); // Built on data from geonames.org. Thanks, GeoNames!
		$countries = json_decode($file_data);

		foreach ($countries as $code=>$name) {
			$output .= "<option value='$name' " . ($field_value == $name ? 'selected' : null ) . ">$name</option>";
		}
		$output .="</select>";
		return $output;
	}

	public static function country_input($field_value = null, $dom_name = null, $dom_id = null, $addl_attrs = null) {
		echo self::get_country_input($field_value, $dom_name, $dom_id, $addl_attrs);
	}

	public static function timezone_list() {
		static $timezones = null;
		
		if ($timezones === null) {
			$timezones = [];
			$offsets = [];
			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			
			foreach (\DateTimeZone::listIdentifiers() as $timezone) {
				$now->setTimezone(new \DateTimeZone($timezone));
				$offsets[] = $offset = $now->getOffset();
				$timezones[$timezone] = '(' . self::format_GMT_offset($offset) . ') ' . self::format_timezone_name($timezone);
			}
			
			array_multisort($offsets, $timezones);
		}
		
		return $timezones;
	}
	
	public static function format_GMT_offset($offset) {
		$hours = intval($offset / 3600);
		$minutes = abs(intval($offset % 3600 / 60));
		return 'GMT' . ($offset!==false ? sprintf('%+03d:%02d', $hours, $minutes) : '');
	}
	
	public static function format_timezone_name($name) {
		$name = str_replace('/', '- ', $name);
		$name = str_replace('_', ' ', $name);
		$name = str_replace('St ', 'St. ', $name);
		return $name;
	}
}

?>