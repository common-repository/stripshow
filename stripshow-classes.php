<?php

/**
* One storyline. 
* An array of instances of this class, called $stripshow_story, constitutes the entire set of storylines.
* @package stripShow
*/
class Storyline {

	/**
	* This story's ID
	* @var int
	*/
	var $id;

	/**
	* The date on which this story starts.
	* @var string
	*/
	var $startdate;
	
	/**
	* The date on which this story ends.
	* Will be empty if this story is ongoing.
	* @var string
	*/
	var $enddate;
	
	/**
	* Name of the storyline.
	* @var string
	*/
	var $name;
	
	/**
	* The ID of this story's parent.
	* @var int
	*/
	var $parent;
	
	/**
	* This story's level.
	* Starts at 0.
	* @var int
	*/
	var $level;
	
	/**
	* Returns TRUE if storyline has children, FALSE if not.
	* @var bool
	*/
	var $has_children = FALSE;
	
	/**
	* An array of post objects that are in the story.
	* @var array
	*/
	var $parts = array();
	
	/**
	* An array of IDs of storylines that are children of the current one.
	* @var array
	*/
	var $children = array();
	
	/**
	* Finds children in the current story.
	* @uses $children
	* @uses $has_children
	* @uses $id
	*/
	function find_children($stories) {
		foreach($stories as $x) {
			if ($x->parent == $this->id) {
				array_push($this->children,$x->id);
				$this->has_children = TRUE;
				}
			}
		}
	
	/**
	* Get the level of the current story.
	* Doesn't return anything. Instead, changes the $level var for this object.
	*/
	function get_level() {
		global $stripshow_story;
		if ($this->parent > 0) {
			$a = $this->parent;
			$this->level = $stripshow_story[$a]->level + 1;
			}
		else $this->level = 0;
		}
		
	/**
	* Determine whether a given comic is within one of this story's children.
	* @param object $comic A comic object
	* @return bool Whether the comic is within one of the children of this story.
	*/
	function is_in_children($comic) {
		global $stripshow_story;
		foreach($this->children as $child) {
			if ($stripshow_story[$child]->is_in_story($comic)) return TRUE;
			}
		return FALSE;
		}
		
	/**
	* Determine whether a given comic is within this story.
	* @param object $comic A comic object
	* @return bool Whether the comic is a part of this story.
	* @uses is_in_children()
	* @uses $startdate
	* @uses $enddate
	* @uses $has_children
	*/
	function is_in_story_old($comic) {
		$date = $comic->post_date;
		if ($this->startdate <= $date) { // This comic's date is after the story's start date.
			if ($date < $this->enddate) { // This comic's date is within the story dates.
				if (!$this->has_children) { // This story has no children, so this comic is in the story.
					return TRUE;
				} else { // This story does have children, so we need to figure out if this strip is in one of those children.
					if ($this->is_in_children($comic)) return FALSE; // if this comic is in a child of this story, it's not in this story.
					else return TRUE;
						}
					return FALSE;
			} elseif (!isset($this->enddate) && $date <= date('Y-m-d H:i:s')) { // End date for this story isn't set, but the comic is before or during the actual date today.
				if ($this->has_children) { // This story has children -- need to check the children.
					if ($this->is_in_children($comic)) return FALSE; // if this comic is in a child of this story, it's not in this story.
					else return TRUE;
				} else { // This story has no children.
					return TRUE;
					}
			} else { // The comic is later than the actual date.
				return FALSE;
				}
		 } else { // Not after start date, so not in story.
			return FALSE;
			}
		// In theory, we should never get here. But if we do, return FALSE just in case.
		return false;
		}

	function is_in_story($comic) {
		global $stripshow_story;
		$date = $comic->post_date;
		if ($this->startdate <= $date) { // This comic's date is after the story's start date.
			if ($date < $this->enddate) { // This comic's date is within the story dates.
				if (!$this->has_children) { // This story has no children, so this comic is in the story.
					return TRUE;
				} else { // This story does have children, so we need to figure out if this strip is in one of those children.
					foreach ($this->children as $child) {
						if ($stripshow_story[$child]->is_in_story($comic) ) return FALSE;
						}
					}
					return FALSE;
			} elseif (!isset($this->enddate) && $date <= date('Y-m-d H:i:s')) { // End date for this story isn't set, but the comic is before or during the actual date today.
				if ($this->has_children) { // This story has children -- need to check the children.
					if ($this->is_in_children($comic)) return FALSE; // if this comic is in a child of this story, it's not in this story.
					else {
						return TRUE;
						}
				} else { // This story has no children.
					return TRUE;
					}
			} else { // The comic is later than the actual date.
				return FALSE;
				}
		 } else { // Not after start date, so not in story.
			return FALSE;
			}
		// In theory, we should never get here. But if we do, return FALSE just in case.
		return false;
		}

		
	/**
	* Echoes the parts of this story in an unordered list
	*/
	function list_parts() {
		if (empty($this->parts)) return FALSE;
		echo '<ul class="storyline_parts">'."\n";
		foreach ($this->parts as $part) {
			echo '<li><a href="'.get_permalink($part->ID).'">'.$part->post_title."</a></li>\n";
			}
		echo "</ul>\n";
		}
	
	/**
	* Get the story's URL (which is the URL of the first comic in the story).
	* The "current story" should never be an empty story, since any calling
	* function will be from a member of some story.
	* @return string The URL
	* @uses $startdate
	*/
	function get_url() {
		global $wpdb, $stripShow;
		if ( !empty( $this->parts ) ) return get_permalink( $this->parts[0] );
		else return FALSE;
		}
	}

/**
* Represents one comic archive
* @package stripShow
*/
class StripShow {
	
	/**
	* A WP_Query object
	* @var WP_Query $comicQuery;
	*/
	var $comicQuery;
	
	/**
	* A post object for a random comic.
	* This is randomized only once; all calls to this variable
	* will return the same object.
	* @var object $random_comic
	*/
	var $random_comic;
	
	/**
	* A post object for the current comic.
	* @var object $current_comic
	*/
	var $current_comic;
	
	/**
	* A post object for the first comic.
	* @var object $first_comic
	*/
	var $first_comic;
	
	/**
	* A post object for the previous comic.
	* @var object $previous_comic
	*/
	var $previous_comic;
	
	/**
	* A post object for the next comic.
	* @var object $next_comic
	*/
	var $next_comic;
	
	/**
	* A post object for the first comic.
	* @var object $last_comic
	*/
	var $last_comic;
	
	/**
	* Finds the parent of a specified story.
	* Finds the parent of a Storyline object and writes that information back to the object itself.
	* This function does not appear to be used, and I'm not entirely sure why it exists.
	* @param Storyline $story The Storyline object
	* @return object $story The same Storyline object with $parent variable set
	*/
	function find_story_parent($story) {
		if ($story->parent) {
			// This is a child story.
			$this->storylines[$story->parent]->children[$story->id] = $story;
			$story->level = $this->storylines[$story->parent]->level+1;
			return FALSE;
			}
		else {
			return $story;
			}
		}
	
	/**
	* Find the enddates of stories.
	* This function doesn't appear to be used.
	* @param array $stories An array of Storyline objects
	* @param string $parent_end The end date of the parent story
	*/
	function find_story_enddates($stories,$parent_end='') {
		for ($a = 0; $a < sizeof($stories); $a++) {
			$now = current($stories);
			$next = next($stories);
			$now->enddate=$next->startdate;
			if (empty($next->startdate) && !empty($parent_end)) $now->enddate = $parent_end;
			if (!empty($now->children)) $this->find_story_enddates($now->children,$now->enddate);
			}
		}

	function find_story_for_comic($stories,$comic) {
		foreach($stories as $story) {
			if (!empty($story->children)) {
				$x = $this->find_story_for_comic($story->children,$comic);
				if ($x == 1) {
					if ($story->level > 0) {
						return 1; // comic has been entered in a child, and this story is a child.
						}
					else { 
						break;
						}
					}
				}
			// no children had this comic in it, time to see whether it's in this story itself.
			$comicdate = $comic->post_date;
			$startdate = $story->startdate;
			$enddate = $story->enddate;
			if ($comicdate >= $startdate) {
				// Comic date must be on or after the start date.
				if ($comicdate <= $enddate) {
					//We have a hit!
					array_push($story->parts,$comic);
					//echo "Found match.</div>";
					return 1;
					}
				elseif (empty($enddate)) {
					// this story has no end date
					array_push($story->parts,$comic);

					//echo 'Found match.</div>';
					return 1;
					}
				}
			}
		//echo '</div>';
		return 0; // no matches found within this set of stories.
		}
	
	/**
	* Finds the current comic
	* Populates the $current_comic variable in this object
	* @uses $current_comic
	* @uses comicQuery
	*/
	function get_current_comic() {
		global $post,$wp_query,$wpdb;
		// If the current post is set, and it's a comic, then we simply put the $post object into $current_comic.
		if (!empty($post)) {
			if (is_comic($post) && !is_home()) {
				$this->current_comic = $post;
				$this->current_comic->permalink = get_permalink( $post->ID );
				$this->comicQuery = $wp_query;
				return;
				}
			}
			$this->current_comic = $this->comicQuery->post;
		}

	/**
	* Finds a comic or comics, whether by date or proximity to the current comic.
	* @param string $which A string of 'first','previous','next','last','random', or 'custom' to search by date.
	* @param int $howmany The number of comics to return
	* @param string $date The date for which to find comics, if finding by date
	* @return object $thepost The post object of the comic we find, or today's comic if no results were found
	* @uses stripshow_db_query
	* @uses stripshow_comic_category
	*/
	function get_comics_old($which,$howmany=1,$date='') {
		global $wpdb;
		if (empty($this->current_comic)) return FALSE;
		$post = $this->current_comic;
		$current_date = $post->post_date;
		$multiple = FALSE;
		$limit = 1;
		switch ($which) {
			case 'first':
				$clause = "ORDER BY post_date ASC";
				break;
			case 'previous_few':
				$limit = $howmany;
			case 'previous':
				$clause = "AND post_date < '$current_date' ORDER BY post_date DESC";
				break;
			case 'next_few':
				$limit = $howmany;
			case 'next':
				$clause = "AND post_date > '$current_date' ORDER BY post_date ASC";
				break;
			case 'last':
				$clause = "ORDER BY post_date DESC";
				break;
			case 'random':
				$clause = "ORDER BY RAND()";
				break;
			case 'custom':
			default:
				if ($date) {
					$formatted_date_notime = date('Y-m-d 00:00:00',strtotime($date));
					$formatted_date_withtime = date('Y-m-d H:i:s',strtotime($date));
					if ($formatted_date_withtime == $formatted_date_notime) { //Date does not include time
						$clause = "AND (DATE(post_date) = '$formatted_date_notime') ORDER BY post_date DESC";
						}
					else { // date includes time
						$clause = "AND (post_date = '$formatted_date_withtime') ORDER BY post_date DESC";
						}
					}
				else {
					return $this->current_comic;
					$clause = "AND ID = $post->ID";
					}
			}
		$querystring = stripshow_db_query() . $clause . " LIMIT $limit";
		//echo "<!-- $which: $querystring -->";
		if ($limit == 1) $thepost = $wpdb->get_row($querystring);
		else $thepost = $wpdb->get_results($querystring);
		if ($thepost) {
			return $thepost; 
			}
		else return FALSE;
		}
	
	/**
	* Sets a WordPress query that includes only comics.
	*/
	function set_up_comics() {
		$querystring = '&cat='.stripshow_comic_category();
		global $wp_query;
		$allComics = new WP_Query();
   		$allComics->set('cat',stripshow_comic_category());
		$allComics->set('orderby','post_date');
		$allComics->set('order','DESC');
		$allComics->set('nopaging',1);
		$indexgoesto = get_option('stripshow_indexgoesto');
		$wp_query->query_vars['category__not_in'] = $buffer_var;
		$allComics->get_posts();
		$bufferComics = wp_clone($allComics);
		$bufferComics->set('post_status','future');
		$bufferComics->get_posts();
		$this->allComics = $allComics;
		$this->bufferComics = $bufferComics;
		$this->comicQuery = new WP_Query( $querystring.'&suppress_filters=1&posts_per_page=1' );
		$this->comicQuery = wp_clone( $this->allComics );
		$this->comicQuery->set( 'showposts', 1 );
		$this->comicQuery->set( 'nopaging', 0 );
		switch( $indexgoesto ) {
		case 'first':
			$this->comicQuery->set( 'order', 'ASC' );
			break;
		case 'random':
			$this->comicQuery->set( 'orderby', 'rand' );
			$this->comicQuery->set( 'order', '' );
			break;
		default:
			$this->comicQuery->set( 'order', 'DESC' );
			break;
		}
		$this->comicQuery->get_posts();
		}

	function get_comics($mode,$howmany=1,$date='') {
		$temp = wp_clone($this->allComics);
		$temp->set('orderby','post_date');
		switch($mode) {
			case 'first':
				$temp->set('order','ASC');
				break;
			case 'last':
				$temp->set('order','DESC');
				break;
			case 'next':
			case 'next_few':
				$temp->set('order','ASC');
				add_filter('posts_where','filter_next',1);
				break;
			case 'previous':
			case  'previous_few':
				$temp->set('order','DESC');
				add_filter('posts_where','filter_previous',1);
				break;
			case 'random':
				$temp->set('orderby','rand');
				break;
			case 'custom':
			default:
				$temp->set('order','DESC');
				if ($date) {
					$GLOBALS['stripshow_date_to_search_for'] = $date;
					add_filter('posts_where','filter_date',1);
					}
				else {
					$clause = "AND ID = $post->ID";
					}
				break;
			}
		$temp->set('nopaging',0);
		$temp->set('posts_per_page',$howmany);
		$temp->get_posts();
		$posts = $temp->posts;
		unset($temp);
		remove_filter('posts_where','filter_next',1);
		remove_filter('posts_where','filter_previous',1);
		remove_filter('posts_where','filter_date',1);
		if (sizeof($posts) > 1) return $posts;
		else return $posts[0];
		}

	function get_random_comic() {
		$original_query = $this->allComics->query;
		//echo $original_query;
		$this->allComics->query($original_query . '&orderby=rand');
		$first_temp = $this->allComics->posts;
		//echo '<pre>',var_dump($this->allComics->posts),'</pre>';
		$first_comic = $first_temp[0];
		return $first_comic;
		}
	
	

	/**
	* Constructor for the StripShow class.
	* This constructor sets up the current comic and populates
	* variables for the other comic links: first, previous, next, last,
	* and random.
	* @uses get_current_comic
	* @uses get_comics
	* @uses $random_comic
	* @uses $first_comic
	* @uses $next_comic
	* @uses $previous_comic
	* @uses $last_comic
	*/
	function StripShow() {
		//echo 'Constructing stripshow class';
		$this->set_up_comics();
		$this->get_current_comic();
		$GLOBALS['current_comic_date'] = $this->current_comic->post_date;
		$this->next_comic = $this->get_comics('next',1);
		$this->previous_comic = $this->get_comics('previous',1);
		$this->first_comic = $this->get_comics('first',1);
		$this->last_comic = $this->get_comics('last',1);
		$this->random_comic = $this->get_comics('random',1);
		}
	}
function filter_next($clause='') {
	global $wpdb;
	$current_date = $GLOBALS['current_comic_date'];
	$clause .= " AND $wpdb->posts.post_date > '$current_date'";
	//echo $clause;
	return $clause;
	}
function filter_previous($clause='') {
	global $wpdb;
	$current_date = $GLOBALS['current_comic_date'];
	$clause .= " AND $wpdb->posts.post_date < '$current_date'";
	//echo $clause;
	return $clause;
	}
function filter_date($clause='') {
	$date = $GLOBALS['stripshow_date_to_search_for'];
	global $wpdb;
	//echo $date;
	$formatted_date_notime = date('Y-m-d 00:00:00',strtotime($date));
	$formatted_date_withtime = date('Y-m-d H:i:s',strtotime($date));
	if ($formatted_date_withtime == $formatted_date_notime) { //Date does not include time
		$clause .= "AND (DATE($wpdb->posts.post_date) = '$formatted_date_notime')";
		}
	else { // date includes time
		$clause .= "AND ($wpdb->posts.post_date = '$formatted_date_withtime')";
		}

	//echo $clause;
	return $clause;
	}
	