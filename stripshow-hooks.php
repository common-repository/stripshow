<?php
/**
* stripshow-query.php
*
* This file contains the filters that stripShow uses to add functionality
* to WordPress's MySQL queries.
*/


/**
* Filters SQL query by year.
* This is a temporary filter -- must be removed after use;
*/
function stripshow_filter_years($query) {
	return "YEAR(post_date) as year";
	}

/**
* Filters SQL query by month.
* This is a temporary filter -- must be removed after use;
*/
function stripshow_filter_months($query) {
	return "MONTH(post_date) as month, YEAR(post_date) as year";
	}

/**
* Filters SQL query to return only days.
* This is a temporary filter -- must be removed after use;
*/
function filter_days_fields() {	
	return "DAYOFMONTH(post_date) AS day";
	}

/**
* Adds a distinct clause to SQL query.
* This is a temporary filter -- must be removed after use;
*/
function filter_distinct($query) {
	$query = 'DISTINCT';
	return $query;
	}

/**
* Filter for SQL query to get all posts for current comic's week.
* @uses StripShow::$current_comic
*/
function stripshow_get_week($query) {
	global $stripShow;
	$weekstarts = get_option('start_of_week');
	$date = $stripShow->current_comic->post_date;
	$query .= " AND WEEK(post_date,$weekstarts) = WEEK('$date',$weekstarts)";
	return $query;
	}

/**
* Replaces permalink with current comic's permalink.
* This is designed to be temporary, so you can write code on the index
* page that would otherwise return the permalink to the last blog post.
* For example, a share link from AddToAny might be placed under the comic. 
*/
function stripshow_intercept_permalink( $link ) {
	global $stripShow;
	return $stripShow->current_comic->permalink;
	
	}