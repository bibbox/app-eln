<?php

  $q=$_GET["q"];

  global $wpdb;

  $sql = "SELECT $wpdb->terms.name as category_name,";
  $sql .= "um1.meta_value as firstname, um2.meta_value as lastname, $wpdb->groups_rs.group_name as groupname ";
  //$sql .= "$wpdb->iarc_forms.date_of_issue ";
  $sql .= "FROM $wpdb->usermeta um1, $wpdb->usermeta um2, $wpdb->posts ";
  $sql .= "LEFT JOIN $wpdb->users ON ($wpdb->posts.post_author = $wpdb->users.ID) ";
  $sql .= "LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id ) ";
  $sql .= "LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id ) ";
  $sql .= "LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) ";
  $sql .= "LEFT JOIN $wpdb->user2group_rs ON ($wpdb->users.ID = $wpdb->user2group_rs.user_id) ";
  $sql .= "INNER JOIN $wpdb->groups_rs ON ($wpdb->user2group_rs.group_id = wp_groups_rs.ID) ";
  //$sql .= "LEFT JOIN $wpdb->iarc_forms ON ($wpdb->term_taxonomy.term_id = $wpdb->iarc_forms.cat_id) ";
  $sql .= "WHERE $wpdb->term_taxonomy.term_id = $q AND $wpdb->term_taxonomy.taxonomy = 'category' "; 
  $sql .= "AND um1.user_id = $wpdb->users.ID AND um1.user_id = um2.user_id AND um1.meta_key = 'first_name' AND um2.meta_key = 'last_name' ";
  $sql .= "AND $wpdb->groups_rs.group_name = 'LabStaff' ; ";

  $my_res = $wpdb->get_row($sql);

  echo "<table border='1'>
  <tr>
  <th>Firstname</th>
  <th>Lastname</th>
  <th>Group</th>
  </tr>";

  echo "<tr>";
  echo "<td>" . $my_res->firstname . "</td>";
  echo "<td>" . $my_res->lastname . "</td>";
  echo "<td>" . $my_res->groupname . "</td>";
  echo "</tr>";
   
  echo "</table>";
  
  
  
 
?>

<!-- <span class="form-elements">
			<label for="firstName">First Name</label>
		  <input name="firstName" type="text" class="text" value="" />
	    </span>
		<br>
	    <span class="form-elements reqField">
			<label for="lastName">Last Name</label>
		  <input name="lastName" type="text" class="text" value="" />
	    </span>
<br> -->
