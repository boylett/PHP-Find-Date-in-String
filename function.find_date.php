<?php
/**
 * Find Date and/or time in a String
 *
 * @author   Etienne Tremel
 * @license  http://creativecommons.org/licenses/by/3.0/ CC by 3.0
 * @link     http://www.etiennetremel.net
 * @version  0.2.1
 *
 * @param string  find_date( ' some text 01/01/2012 some text' ) or find_date( ' some text October 5th 86 some text' )
 * @return mixed  false if no date found else array: array( 'day' => 01, 'month' => 01, 'year' => 2012, 'hours' => 0, 'minutes' => 0, 'seconds' => 0 )
 */

function find_date($string)
	{
		$date = array
		(
			'year'    => 0,
			'month'   => 0,
			'day'     => 0,
			'hours'   => 0,
			'minutes' => 0,
			'seconds' => 0
		);

		try
		{
			// Define month name:
			$month_names = array
			(
				"january",
				"february",
				"march",
				"april",
				"may",
				"june",
				"july",
				"august",
				"september",
				"october",
				"november",
				"december"
			);

			$short_month_names = array_map(function($string)
			{
				return substr($string, 0, 3);
			}, $month_names);

			// Define day name
			$day_names = array
			(
				"monday",
				"tuesday",
				"wednesday",
				"thursday",
				"friday",
				"saturday",
				"sunday"
			);

			$short_day_names = array_map(function($string)
			{
				return substr($string, 0, 3);
			}, $day_names);

			// Define ordinal number
			$ordinal_number = array('st', 'nd', 'rd', 'th');

			$day   = "";
			$month = "";
			$year  = "";

			// Match dates: 01/01/2012 or 30-12-11 or 1 2 1985
			preg_match('/([0-9]?[0-9])[\.\-\/ ]+([0-1]?[0-9])[\.\-\/ ]+([0-9]{2,4})/', $string, $matches);

			if($matches)
			{
				if($matches[1])
				{
					$day = $matches[1];
				}
				if($matches[2])
				{
					$month = $matches[2];
				}
				if($matches[3])
				{
					$year = $matches[3];
				}
			}

			// Match dates: Sunday 1st March 2015; Sunday, 1 March 2015; Sun 1 Mar 2015; Sun-1-March-2015
			preg_match('/(?:(?:' . implode('|', $day_names) . '|' . implode('|', $short_day_names) . ')[ ,\-_\/]*)?([0-9]?[0-9])[ ,\-_\/]*(?:' . implode('|', $ordinal_number) . ')?[ ,\-_\/]*(' . implode('|', $month_names) . '|' . implode('|', $short_month_names) . ')[ ,\-_\/]+([0-9]{4})/i', $string, $matches);
			
			if($matches)
			{
				if(empty($day) and $matches[1])
				{
					$day = $matches[1];
				}

				if(empty($month) and $matches[2])
				{
					$month = array_search(strtolower($matches[2]),  $short_month_names);

					if(!$month)
					{
						$month = array_search(strtolower($matches[2]),  $month_names);
					}

					$month = $month + 1;
				}

				if(empty($year) and $matches[3])
				{
					$year = $matches[3];
				}
			}

			// Match dates: March 1st 2015; March 1 2015; March-1st-2015
			preg_match('/(' . implode('|', $month_names) . '|' . implode('|', $short_month_names) . ')[ ,\-_\/]*([0-9]?[0-9])[ ,\-_\/]*(?:' . implode('|', $ordinal_number) . ')?[ ,\-_\/]+([0-9]{4})/i', $string, $matches);
			
			if($matches)
			{
				if(empty($month) and $matches[1])
				{
					$month = array_search(strtolower($matches[1]),  $short_month_names);

					if(!$month)
					{
						$month = array_search(strtolower($matches[1]),  $month_names);
					}

					$month = $month + 1;
				}

				if(empty($day) and $matches[2])
				{
					$day = $matches[2];
				}

				if(empty($year) and $matches[3])
				{
					$year = $matches[3];
				}
			}

			// Match month name:
			if(empty($month))
			{
				preg_match('/(' . implode('|', $month_names) . ')/i', $string, $matches_month_word);

				if($matches_month_word and $matches_month_word[1])
				{
					$month = array_search(strtolower($matches_month_word[1]),  $month_names);
				}

				// Match short month names
				if(empty($month))
				{
					preg_match('/(' . implode('|', $short_month_names) . ')/i', $string, $matches_month_word);

					if($matches_month_word and $matches_month_word[1])
					{
						$month = array_search(strtolower($matches_month_word[1]),  $short_month_names);
					}
				}

				if(!is_numeric($month))
				{
					return $date;
				}
			}

			// Match 5th 1st day:
			if(empty($day))
			{
				preg_match('/([0-9]?[0-9])(' . implode('|', $ordinal_number) . ')/', $string, $matches_day);

				if($matches_day and $matches_day[1])
				{
					$day = $matches_day[1];
				}
			}

			// Match Year if not already setted:
			if(empty($year))
			{
				preg_match('/[0-9]{4}/', $string, $matches_year);

				if($matches_year and $matches_year[0])
				{
					$year = $matches_year[0];
				}
			}

			if(!empty($day) and !empty($month) and empty($year))
			{
				preg_match('/[0-9]{2}/', $string, $matches_year);

				if($matches_year and $matches_year[0])
				{
					$year = $matches_year[0];
				}
			}

			// Day leading 0
			if(1 == strlen($day))
			{
				$day = '0' . $day;
			}

			// Month leading 0
			if(1 == strlen($month))
			{
				$month = '0' . $month;
			}

			// Check year:
			if(2 == strlen($year) and $year > 20)
			{
				$year = '19' . $year;
			}
			else if(2 == strlen($year) and $year < 20)
			{
				$year = '20' . $year;
			}

			$date['year']  = $year;
			$date['month'] = $month;
			$date['day']   = $day;

			// Look for time-like strings, eg: 08:56am or 12.01pm
			preg_match("/(([1-2]?[0-9])(?:am|pm)|([1-2]?[0-9])\s?(?::|\.)\s?([0-9]{1,2})\s?((?::|\.)\s?([0-9]{1,2})|(am|pm))?)/i", $string, $time);

			if(!empty($time))
			{
				$stamp = explode(":", date("H:i:s", strtotime(date("Y-m-d") . ' ' . $time[0])));

				$date['hours']   = abs($stamp[0]);
				$date['minutes'] = abs($stamp[1]);
				$date['seconds'] = abs($stamp[2]);

				if((stristr($time[5], 'p') or stristr($time[7], 'p')) and $date['hours'] <= 12)
				{
					$date['hours'] += 12;

					if($date['hours'] == 24)
					{
						$date['hours'] = 0;
					}
				}
			}

			// Return false if nothing found:
			if(empty($year) and empty($month) and empty($day))
			{
				return false;
			}
		}
		catch(Exception $e){}
		
		return $date;
	}
