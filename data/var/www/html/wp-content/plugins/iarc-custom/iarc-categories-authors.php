<?php

/*
 * IARC L.Alteyrac 20160829
 * Extends class CategoryAuthorPlugin (@see categories_authors.php) to customize queries and display
 * 2 functions are overridden:
 *		- assembleDBRequest
 *		- display_categoryauthorcontent
*/

if ( class_exists( 'CategoryAuthorPlugin' ) ) {
	
	class Iarc_CategoryAuthorPlugin extends CategoryAuthorPlugin {
		
		function assembleDBRequest($sortKey, $sortOrder, $offset = 0, $limit = -1)
		{
			global $wpdb;
			$categoryAuthorOptions = get_option($this->adminOptionsName);

			if($limit == -1) { $limit = $this->defaultNumberOfItemsPerPage; }
			if($offset < 0)  { $offset = 0; }
			$cond = "";
			$ucond = "";	
			if(($categoryAuthorOptions['showAdminContributions'] == $this->c_no) && (!empty($this->strAdminUserIDs)))
				{
				$cond .= "AND ($wpdb->posts.post_author NOT IN ($this->strAdminUserIDs))";
				}
			if($categoryAuthorOptions['showUncategorized'] == $this->c_no)
				{
				$ucond = " AND ($wpdb->terms.term_id <> $this->const_uncategorized)";
				$cond .= $ucond;
				}

				

			if ($sortKey == $this->sortKeyAuthor) 
				{
				$sql = "SELECT DISTINCT $wpdb->posts.post_author FROM $wpdb->posts ";
				$sql .= "LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id ) ";
				$sql .= "LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id ) ";
				$sql .= "LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) "; 
				$sql .= "WHERE ($wpdb->posts.post_status='publish') $cond LIMIT $offset,$limit";
				$authorarr = $wpdb->get_col( $sql );
				$authors = "";
				if(!empty($authorarr)) { $authors = implode(",", $authorarr); }
				else return "";
				
				// IARC L.Alteyrac 2013-01-31: Changes into SQL Query, to be able to display First Name, Last Name and Group of Authors
				$sql  = "SELECT $wpdb->posts.post_author as author_id, $wpdb->term_taxonomy.term_id as category_id, $wpdb->users.user_nicename as nicename, ";
				$sql .= "$wpdb->users.user_login as user_login, $wpdb->users.display_name as display_name, $wpdb->terms.name as category_name, ";
				$sql .= "um1.meta_value as firstname, um2.meta_value as lastname, grs.group_name as groupname ";
				$sql .= "FROM $wpdb->usermeta um1, $wpdb->usermeta um2, $wpdb->user2group_rs u2g, $wpdb->user2group_rs u2gLab, $wpdb->groups_rs grs, $wpdb->posts ";
				$sql .= "LEFT JOIN $wpdb->users ON ($wpdb->posts.post_author = $wpdb->users.ID) ";
				$sql .= "LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id ) ";
				$sql .= "LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id ) ";
				$sql .= "LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) WHERE ";
				$sql .= "($wpdb->term_taxonomy.taxonomy = 'category') AND ($wpdb->posts.post_status='publish') $ucond AND ($wpdb->posts.post_author IN ($authors))  ";
				$sql .= "AND um1.meta_key = 'first_name' AND um2.meta_key = 'last_name' AND $wpdb->users.ID = um1.user_id AND um1.user_id = um2.user_id ";
				$sql .= "AND um1.user_id = u2g.user_id AND u2g.group_id = grs.id and um1.user_id not in (31,39,40) ";
				// IARC L.Alteyrac 20130219: add a filter to show only Lab users (30 = id of group "LabStaff", 31 = id of group "Freezer")
				$sql .= "and u2gLab.group_id = 30 and u2g.group_id not in (30, 31) and u2gLab.user_id = u2g.user_id ";
				// IARC L.Alteyrac 20130219: order by lastname, not displayname ($wpdb->users.display_name)
				$sql .= "ORDER BY lastname " . $sortOrder . ", $wpdb->terms.name ASC";
				}
			else
				{
				$sql = "SELECT $wpdb->term_taxonomy.term_id FROM $wpdb->term_taxonomy ";
				$sql .= "LEFT JOIN $wpdb->term_relationships ON ( $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id ) ";
				$sql .= "LEFT JOIN $wpdb->posts ON ( $wpdb->term_relationships.object_id = $wpdb->posts.ID ) ";
				$sql .= "LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) ";
				$sql .= "WHERE ( $wpdb->term_taxonomy.taxonomy = 'category') AND ($wpdb->posts.post_status='publish') $cond GROUP BY $wpdb->term_taxonomy.term_id  LIMIT $offset,$limit";
				$catarr = $wpdb->get_col( $sql );
				$categories = "";
				if(!empty($catarr)) { $categories = implode(",", $catarr); }
				else return $sql;//"";
				
				// IARC L.Alteyrac 2013-01-30: Changes into SQL Query, to be able to display First Name, Last Name and Group of Authors
				$sql  = "SELECT $wpdb->posts.post_author as author_id, $wpdb->term_taxonomy.term_id as category_id, $wpdb->users.user_nicename as nicename, ";
				$sql .= "$wpdb->users.user_login as user_login, $wpdb->users.display_name as display_name, $wpdb->terms.name as category_name, ";
				$sql .= "um1.meta_value as firstname, um2.meta_value as lastname, grs.group_name as groupname ";
				$sql .= "FROM $wpdb->usermeta um1, $wpdb->usermeta um2, $wpdb->user2group_rs u2g, $wpdb->user2group_rs u2gLab, $wpdb->groups_rs grs, $wpdb->posts ";
				$sql .= "LEFT JOIN $wpdb->users ON ($wpdb->posts.post_author = $wpdb->users.ID) ";
				$sql .= "LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id ) ";
				$sql .= "LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id ) ";
				$sql .= "LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) WHERE ";
				$sql .= "($wpdb->term_taxonomy.taxonomy = 'category') AND ($wpdb->posts.post_status='publish') $cond AND ( $wpdb->term_taxonomy.term_id IN ($categories))  ";
				$sql .= "AND um1.meta_key = 'first_name' AND um2.meta_key = 'last_name' AND $wpdb->users.ID = um1.user_id AND um1.user_id = um2.user_id ";
				$sql .= "AND um1.user_id = u2g.user_id AND u2g.group_id = grs.id and um1.user_id not in (31,39,40) ";
				// IARC L.Alteyrac 20130219: add a filter to show only Lab users (30 = id of group "LabStaff", 31 = id of group "Freezer")
				$sql .= "and u2gLab.group_id = 30 and u2g.group_id not in (30, 31) and u2gLab.user_id = u2g.user_id ";
				// IARC L.Alteyrac 20130219: order by lastname, not displayname ($wpdb->users.display_name)
				$sql .= "ORDER BY $wpdb->terms.name " . $sortOrder . ", lastname ASC";	
				}	

			return $sql;
		}

		function display_categoryauthorcontent($atts) 
		{
			global $debug_flag;
			global $tstr;
			global $wpdb;
			

			$retstr = "";	
			$categoryAuthorOptions = get_option($this->adminOptionsName);
			$itemsPerPage = floatval($categoryAuthorOptions['numberOfItemsPerPage']);
			$sortKey = $categoryAuthorOptions['defaultSortKey'];
			$citationStyle = '';
			$debug_flag = 0;
			if(is_array($atts))		
				{
				if( isset($atts['debug'] ) && ( $atts['debug'] == "1" ) ) { $debug_flag = 1; }
			  if( isset($atts['sortkey']) && ( ( $atts['sortkey'] == $this->sortKeyAuthor ) || ($atts['sortkey'] == $this->sortKeyCategory ) ) )
					{
					$sortKey = $atts['sortkey'];
					}
				}
			else
				{
				if(isset($_GET['sortkey']) && ( ( $_GET['sortkey'] == $this->sortKeyAuthor ) || ($_GET['sortkey'] == $this->sortKeyCategory ) )) 
					{
					$sortKey = $_GET['sortkey'];
					}
				}
			$pageToDisplay = 1;
			$tpd = 1;
			$rowOffset = 0;
			if(isset($_GET['pd']) && (is_numeric($tpd = $_GET['pd'])) && ($tpd > 0)) 
				{
				$pageToDisplay = intval($tpd);
				$rowOffset = ($pageToDisplay-1) * $itemsPerPage;
				}
			$showAdmin = true;
			//check the admin users if we have to
			if($categoryAuthorOptions['showAdminContributions'] == $this->c_no)
				{
				$showAdmin = false;	
				$this->getAdminUsers();	
				}
			else
				{
				$retstr .= "\n <!-- showAdminContributions active --> \n";	
				}
			$showUncategorized = ($categoryAuthorOptions['showUncategorized'] == $this->c_yes ) ? true : false;
			$hasAdmins = empty($this->strAdminUserIDs) ? false : true;	
			$admin_id_arr = $this->adminUserIDs;

			$this->totalNumberOfRecords = $this->getTotalNumberOfRecords($sortKey);
			$request = $this->assembleDBRequest($sortKey, $this->sort_asc, $rowOffset, $itemsPerPage);
			$myrows = $wpdb->get_results( $request, ARRAY_A );
			
			if(!empty($myrows))
				{

				$numPages = ceil($this->totalNumberOfRecords/$itemsPerPage);
				if($numPages > 1) { $showPaginator = 1; }
				else { $showPaginator = 0; }
				if ( is_front_page() || is_paged() || is_search() ) { $showPaginator = 0; } //don't show the paginator on the front page (workaround for 
				else 
					{																	//get_posts-on-frontpage-bug of wordpress 2.6
					if(is_category() || is_archive()) //don't show the paginator if the category/archive has subcategories 
						{																//or multiple posts in it
						$mycat = get_cat_id(single_cat_title("", false)); 
						$has_cat_children = (bool)(get_category_children($mycat));
						$posts = get_posts(array('category' => $mycat, 'post_parent' => null) );
						if($has_cat_children || (count($posts) > 1)) { $showPaginator = 0; }
						}
					else 
						{ //there can be multiple posts on a page
						$posts = get_posts(array('post_parent' => get_the_ID()) );
						if((count($posts)>1)) { $showPaginator = 0; }
						}
					}

				$paginatorString = "";
				if($showPaginator == 1) 
					{
					$paginatorString = $this->assemblePaginatorString($pageToDisplay, $numPages);
					}

				$retstr .= $paginatorString;

				$numrows = count($myrows); 
				if($sortKey == $this->sortKeyCategory)
					{
					$curr_cat = "";	
					$author_arr = array();
					for($i = 0; $i < $numrows; $i++)
						{
						$row = $myrows[$i];
						if($curr_cat != $row["category_name"] ) //a new category
							{
							if(count($author_arr) > 0)
								{
								$retstr .= "<span class=\"casubordinate\"> ; Authors: " . implode(", ", array_unique($author_arr)) . "</span><br/><br/>\n";
								unset($author_arr);
								$author_arr = array();
								}
							$curr_cat = $row["category_name"];	
							$adminSoleAuthor = false;
							if($showAdmin || !$adminSoleAuthor)
								{
								// IARC L.Alteyrac 2013-01-30: "Category" replaced by "Notebook"
								$retstr .= "<span class=\"casuperordinate\">Notebook: <a href=\"" . get_category_link(intval($row['category_id'])) . "\">" . $curr_cat . "</a></span>";
								}
							}
						// IARC L.Alteyrac 2013-01-30: Display First Name, Last Name and Group of Authors										
						$author_arr[] = "<a href=\"" . get_author_posts_url(intval($row["author_id"]),  $row["nicename"]) . "\">" . $row["firstname"]." ". $row["lastname"] . "</a> (" . $row["groupname"] . ")";	
						}
					if(!empty($author_arr))
						{
						$retstr .= "<span class=\"casubordinate\"> ; Authors: " . implode(", ", array_unique($author_arr)) . "</span>";
						}
					}
				else //sorting by author
					{
					$curr_aut = "";	
					$cat_arr = array();
					foreach($myrows as $row) 
						{
						if($curr_aut != $row["author_id"]) //a new author
						  {
							if(count($cat_arr) > 0)
								{
								// IARC L.Alteyrac 20130131: "Categories" replaced by "Notebooks"
								$retstr .= "<span class=\"casubordinate\">Notebooks: " . implode(", ", array_unique($cat_arr)) . "</span><br/><br/>\n";
								unset($cat_arr);
								$cat_arr = array();
								}
							$curr_aut = $row["author_id"];
							$user = get_userdata( $curr_aut );
							$registered = $user->user_registered;
							$registerdate = '<span>'. date_i18n(get_option('date_format'),strtotime($registered) ) .'</span>' ;
						
							// IARC L.Alteyrac 2013-01-31: Display First Name, Last Name, Group of Authors and date of registration										
							$retstr .= "<span class=\"casuperordinate\">Author: <a href=\"" . get_author_posts_url($row['author_id'], $row["nicename"]) . "\">" .  $row["firstname"]." ". $row["lastname"] . "</a>"
										. "</span> (" . $row["groupname"] . ", registered on ". $registerdate . ")<br/>";
							}
						$cat_arr[] = "<a href=\"" . get_category_link(intval($row["category_id"])) . "\">" . $row["category_name"] . "</a>";	
						}
					if(count($cat_arr)>0)
						{
						$retstr .= "<span class=\"casubordinate\">Notebooks: " .	implode(", ", array_unique($cat_arr)) . "</span>";
						}
					}
				$retstr .= $paginatorString;
				if(($numPages > 1) && ($showPaginator == 0)) 
					{ //show a "read more ..."-link
					$ppost = get_post(get_the_ID(), ARRAY_A);
					$retstr .= "<div style='width:100%; text-align:right;'><a href='" . $ppost['guid'] . "'>read more ...</a></div>";	
					}
				}
			else
				{
				$retstr .= "The database seems to be empty<br/>$request<br/>$totalNumberOfRecords";	
				}


			if($debug_flag == 1) $retstr .= "::" . $tstr;	
			return $retstr;
		}
	}
}	

?>