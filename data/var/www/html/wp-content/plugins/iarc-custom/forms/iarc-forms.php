<?php
/*
	IARC T.Cholin/L.Alteyrac
*/

if(!class_exists("IARCFormsPlugin")) {

	class IARCFormsPlugin {

		function IARCFormsPlugin(){
		
			// Ces informations sont nécessaires pour indiquer à wordpress quelles fonction php exécuter lors d'une requete ajax
			add_action( 'wp_ajax_getDetails', array( $this, 'getDetails_callback' ) );
			add_action( 'wp_ajax_getEnteredInfos', array( $this, 'getEnteredInfos_callback' ) );
			add_action( 'wp_ajax_submitFormPhp', array( $this, 'submitFormPhp_callback' ) );
			
		}
		
		// ------------------------------------
		// Install plugin: 
		// - create new table in the database
		// - store version number in the database
		// ------------------------------------
		// (Ne fonctionne pas actuellement)
		function iarc_forms_install(){

			global $wpdb;
			$table_iarc_forms = $wpdb->prefix . 'iarc_forms';

			$sql = "CREATE TABLE $table_iarc_forms (
			cat_id bigint( 20 ) NOT NULL,
			date_of_issue datetime default NULL,
			date_of_checking datetime default NULL,
			remark varchar(500) default NULL,
			date_of_archival datetime default NULL,
			PRIMARY KEY  ( cat_id )   
			)";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			add_option( "iarc_forms_version", $iarc_forms_version );
			

		}

		// -----------------------------------
		// Retourne la liste des notebooks qui n'ont qu'un seul auteur
		// Sert à remplir la ddl "Search by notebooks" du form
		// -----------------------------------
		function getNotebooks(){

			global $wpdb;
			
			$sql = "SELECT ";
				$sql .= "$wpdb->terms.term_id As category_id, ";
				$sql .= "$wpdb->terms.name AS category_name, ";
				$sql .= "um1.meta_value As firstname, ";
				$sql .= "um2.meta_value As lastname, ";
				$sql .= "$wpdb->users.ID AS user_id, ";
				$sql .= "$wpdb->groups_rs.group_name As groupname ";
			$sql .= "FROM ";
				$sql .= "$wpdb->usermeta um1, ";
				$sql .= "$wpdb->usermeta um2, ";
				$sql .= "$wpdb->users Left Join ";
				$sql .= "$wpdb->user2group_rs On $wpdb->users.ID = $wpdb->user2group_rs.user_id Inner Join ";
				$sql .= "$wpdb->groups_rs On $wpdb->user2group_rs.group_id = $wpdb->groups_rs.ID Inner Join ";
				$sql .= "v_1author_notebooks On $wpdb->users.ID = v_1author_notebooks.user_id Inner Join ";
				$sql .= "$wpdb->terms On v_1author_notebooks.category_id = $wpdb->terms.term_id ";
			$sql .= "WHERE ";
				$sql .= "um1.user_id = $wpdb->users.ID And ";
				$sql .= "um1.user_id = um2.user_id And ";
				$sql .= "um1.meta_key = 'first_name' And ";
				$sql .= "um2.meta_key = 'last_name' ";
			$sql .= "HAVING ";
				$sql .= "$wpdb->groups_rs.group_name = 'Labstaff' ";
			$sql .= "ORDER BY ";
				$sql .= "$wpdb->terms.term_id ";
			

			return $sql;
		}
		
		// -----------------------------------
		// Retourne la liste des auteurs des notebooks qui n'ont qu'un seul auteur
		// Sert à remplir la ddl "Search by authors" du form
		// -----------------------------------
		function getAuthors(){
			
			global $wpdb;
		
			$sql = "SELECT ";
				$sql .= "DISTINCT  ";
				$sql .= "um1.meta_value As firstname, ";
				$sql .= "um2.meta_value As lastname, ";
				$sql .= "$wpdb->users.ID AS user_id, ";
				$sql .= "$wpdb->groups_rs.group_name As group_name  ";
			$sql .= "FROM ";
				$sql .= "$wpdb->usermeta um1, ";
				$sql .= "$wpdb->usermeta um2, ";
				$sql .= "$wpdb->users Left Join ";
				$sql .= "$wpdb->user2group_rs On $wpdb->users.ID = $wpdb->user2group_rs.user_id Inner Join ";
				$sql .= "$wpdb->groups_rs On $wpdb->user2group_rs.group_id = $wpdb->groups_rs.ID Inner Join ";
				$sql .= "v_1author_notebooks On $wpdb->users.ID = v_1author_notebooks.user_id Inner Join ";
				$sql .= "$wpdb->terms On v_1author_notebooks.category_id = $wpdb->terms.term_id ";
			$sql .= "WHERE ";
				$sql .= "um1.user_id = $wpdb->users.ID And ";
				$sql .= "um1.user_id = um2.user_id And ";
				$sql .= "um1.meta_key = 'first_name' And ";
				$sql .= "um2.meta_key = 'last_name' ";
			$sql .= "HAVING ";
				$sql .= "$wpdb->groups_rs.group_name = 'Labstaff' ";
			$sql .= "ORDER BY ";
				$sql .= "$wpdb->users.ID ";
				
			return $sql;
		  }
		
		// -----------------------------------
		// Retourne les détails d'un notebook pour être affichés au dessus du formulaire
		// Cette fonction est lancée par ajax
		// -----------------------------------
		function getDetails_callback()
		{
			global $wpdb;
			$catID = intval( $_POST['catID'] );
			
			$sql = "SELECT Distinct $wpdb->terms.name as category_name,";
			$sql .= "um1.meta_value as firstname, um2.meta_value as lastname, $wpdb->groups_rs.group_name as groupname, ";
			$sql .= "MIN(wp_posts.post_date) as 'min_post_date', ";
			$sql .= "MAX(wp_posts.post_date) as 'max_post_date' ";
			$sql .= "FROM $wpdb->usermeta um1, $wpdb->usermeta um2, $wpdb->posts ";
			$sql .= "LEFT JOIN $wpdb->users ON ($wpdb->posts.post_author = $wpdb->users.ID) ";
			$sql .= "LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id ) ";
			$sql .= "LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id ) ";
			$sql .= "LEFT JOIN $wpdb->terms ON ($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id) ";
			$sql .= "LEFT JOIN $wpdb->user2group_rs ON ($wpdb->users.ID = $wpdb->user2group_rs.user_id) ";
			$sql .= "INNER JOIN $wpdb->groups_rs ON ($wpdb->user2group_rs.group_id = wp_groups_rs.ID) ";
			$sql .= "WHERE $wpdb->term_taxonomy.term_id = $catID AND $wpdb->term_taxonomy.taxonomy = 'category' "; 
			$sql .= "AND um1.user_id = $wpdb->users.ID AND um1.user_id = um2.user_id AND um1.meta_key = 'first_name' AND um2.meta_key = 'last_name' ";
			$sql .= "AND $wpdb->groups_rs.group_name != 'LabStaff' ";
			$sql .= "AND $wpdb->groups_rs.group_name != 'ELN Supervisor' ";
			$sql .= "AND $wpdb->groups_rs.group_name != 'Freezer' ";
			$sql .= "AND $wpdb->groups_rs.group_name != 'Nitrogen Tanks' ";
			$sql .= "Group by wp_term_taxonomy.term_id ";

			$my_res = $wpdb->get_row($sql);

			$minDate = new DateTime($my_res->min_post_date);
			$maxDate = new DateTime($my_res->max_post_date);
			
			echo "<label><b>Firstname : </b>" . $my_res->firstname . "</label></br></br>";
			echo "<label><b>Lastname : </b>" . $my_res->lastname . "</label></br></br>";
			echo "<label><b>Groupname : </b>" . $my_res->groupname . "</label></br></br>";
			echo "<label><b>Date of first entry : </b>" . $minDate->format('d/m/Y') . "</label></br></br>";
			echo "<label><b>Date of last entry : </b>" . $maxDate->format('d/m/Y') . "</label></br>";
	
			
			die();
		}
		
		// -----------------------------------
		// Retourne les informations d'un notebook déja entrées dans la table wp_iarc_forms,  pour être pré-remplies dans le formulaire
		// Cette fonction est lancée par ajax
		// -----------------------------------
		function getEnteredInfos_callback()
		{
			global $wpdb;
			$catID = intval( $_POST['catID'] );
			
			$sql = "SELECT wp_iarc_forms.date_of_issue, wp_iarc_forms.date_of_checking, wp_iarc_forms.remark, wp_iarc_forms.date_of_archival ";
			$sql .= "FROM wp_iarc_forms ";
			$sql .= "WHERE wp_iarc_forms.cat_id = $catID ";
			
			$my_res = $wpdb->get_row($sql);
			
			
			echo (json_encode($my_res));
			
			die();
			
		}
		
		// -----------------------------------
		// Submit du form : update de la table wp_iarc_forms
		// Cette fonction est lancée par ajax
		// -----------------------------------
		function submitFormPhp_callback()
		{
			global $wpdb;
			$catID = intval( $_POST['catID'] );
			$issueDateHasDP = $_POST['issueDateHasDP'];
			$checkingDateHasDp = $_POST['checkingDateHasDp'];
			$archivalDateHasDp = $_POST['archivalDateHasDp'];
			
			$strptimeFormat = '%d/%m/%Y';
			
			$remarks = strval( $_POST['remarks'] );
	
			$result = $wpdb->get_results ("SELECT $wpdb->iarc_forms.cat_id FROM $wpdb->iarc_forms WHERE $wpdb->iarc_forms.cat_id = $visitor_ip");
			
			if (count($result) == 0)
			{
				$wpdb->insert('wp_iarc_forms', 
							array( 
								'cat_id' => $catID
							));
			}
			
			$wpdb->update( 'wp_iarc_forms', 
						array( 'remark' => $remarks), 
						array( 'cat_id' => $catID )
					);
			
			if ($issueDateHasDP && $_POST['issueDate'] != null)
			{
				$issueDate = IARCFormsPlugin::createDateFromFormatString($_POST['issueDate'], $strptimeFormat);
				$wpdb->update( 'wp_iarc_forms', 
							array('date_of_issue' => $issueDate),
							array( 'cat_id' => $catID )
						);
			}
			
			if ($checkingDateHasDp && $_POST['checkingDate'] != null)
			{
				$checkingDate = IARCFormsPlugin::createDateFromFormatString($_POST['checkingDate'], $strptimeFormat);
				$wpdb->update( 'wp_iarc_forms', 
							array('date_of_checking' => $checkingDate),
							array( 'cat_id' => $catID )
						);
			}
			
			if ($archivalDateHasDp && $_POST['archivalDate'] != null)
			{
				$archivalDate = IARCFormsPlugin::createDateFromFormatString($_POST['archivalDate'], $strptimeFormat);
				$wpdb->update( 'wp_iarc_forms', 
							array('date_of_archival' => $archivalDate),
							array( 'cat_id' => $catID )
						);
			}	
			
			die();
		}
		
		// Fonction permettant de convertir une string de date d'un certain format ($strptimeFormat) en variable date reconnue par mysql
		function createDateFromFormatString($stringDate, $strptimeFormat) {
			date_default_timezone_set('Europe/Paris');
			
			$date = strptime($stringDate, $strptimeFormat);
			$date['tm_year'] += 1900;
			$date['tm_mon']++;
			$date['tm_mday']++;
			$timestamp = mktime(0 , 0, 0, $date['tm_mon'], $date['tm_mday'], $date['tm_year']);
			$datetime = new DateTime('@'. $timestamp);
			$result = $datetime->format('Y-m-d');
			return $result;
		}
		
		// -----------------------------------
		// Retourne tous les notebooks qui n'ont qu'un seul auteur, avec notamment leurs informations de checking (wp_iarc_forms)
		//(en gardant l'information du groupe, ce qui fait que la requete n'est pas la même 
		// que dans getNotebooks() : on se sert de la vue v_labstaff_1author_notebooks)
		// -----------------------------------
		function getNotebooksTable() {
				
			$sql = "SELECT ";
				$sql .= "wp_terms.term_id As 'Category ID', ";
				$sql .= "CONCAT(um1.meta_value,' ', um2.meta_value) As 'Author', ";
				$sql .= "wp_terms.name AS 'Notebook', ";
				$sql .= "wp_groups_rs.group_name As Groupname, ";
				$sql .= "DATE_FORMAT(wp_iarc_forms.date_of_issue, '%d/%m/%Y') As 'Issue date', ";
				$sql .= "DATE_FORMAT(wp_iarc_forms.date_of_checking, '%d/%m/%Y') As 'Checking date', ";
				$sql .= "wp_iarc_forms.remark As 'Remark', ";
				$sql .= "DATE_FORMAT(wp_iarc_forms.date_of_archival, '%d/%m/%Y') As 'Archival date' ";
			$sql .= "FROM ";
				$sql .= "wp_usermeta um1, ";
				$sql .= "wp_usermeta um2, ";
				$sql .= "wp_users Left Join ";
				$sql .= "wp_user2group_rs On wp_users.ID = wp_user2group_rs.user_id Inner Join ";
				$sql .= "wp_groups_rs On wp_user2group_rs.group_id = wp_groups_rs.ID Inner Join ";
				$sql .= "v_labstaff_1author_notebooks On wp_users.ID = v_labstaff_1author_notebooks.user_id Inner Join ";
				$sql .= "wp_terms On v_labstaff_1author_notebooks.category_id = wp_terms.term_id Left Join ";
				$sql .= "wp_iarc_forms On wp_terms.term_id = wp_iarc_forms.cat_id ";
			$sql .= "WHERE ";
				$sql .= "um1.user_id = wp_users.ID And ";
				$sql .= "um1.user_id = um2.user_id And ";
				$sql .= "um1.meta_key = 'first_name' And ";
				$sql .= "um2.meta_key = 'last_name' And ";
				$sql .= "wp_groups_rs.group_name != 'LabStaff' And ";
				$sql .= "wp_groups_rs.group_name != 'ELN Supervisor' And ";
				$sql .= "wp_groups_rs.group_name != 'Freezer' And ";
				$sql .= "wp_groups_rs.group_name != 'Nitrogen Tanks' ";
			$sql .= "ORDER BY ";
				$sql .= "Author, Notebook ";
				
			return $sql;
		}
		
		// -----------------------------------
		// Fonction main du plugin, qui se contente du rediriger vers la fonction d'affichage du formulaire 
		// ou du tableau selon le parametre passé dans le shortcode de la page
		// -----------------------------------
		function displayIARCForms($attrs) {
			
			if ($attrs["display"] == "form") {
			
				return $this->displayNotebooksForm();
			}
			else if ($attrs["display"] == "table") {
			
				return $this->displayNotebooksTable();
			}
		
		}
		
		// -----------------------------------
		// Fonction principale du tableau de notebooks
		// -----------------------------------
		function displayNotebooksTable() {
		
			//DataTables est un plugin jQuery permettant de disposer facilement
			// de fonctions de tri, recherche, etc sur un tableau
			wp_enqueue_style( 'wp-jquery-datatables', plugins_url( 'jquery-datatables/media/css/demo_table.css' ) );		
			wp_enqueue_script( 'wp-jquery-datatables', plugins_url( 'jquery-datatables/media/js/jquery.dataTables.js' ) ); 
			
			global $wpdb;
			
			ob_start(); // use output buffer of PHP
			
			$notebooks = $wpdb->get_results($this->getNotebooksTable(), ARRAY_A);
			
			echo "<div id='tableTitle' style=\"text-align: center;\"><font size=5px>Notebooks Table</font></div>";
			
			echo "<br><div style=\"text-align: right;\"><a href='/iarc-form/'>Click here to go to the notebook checking form</a></div><br>";
			
			echo "List of all LabStaff notebooks:<br>";
			echo "  - You can sort the data by clicking on the column's title<br>";
			echo "  - Click on a line to open the corresponding checking form.<br><br><hr><br>";
			
			// Construction du tableau
			echo '<table id="notebooksTable">';
			echo '<thead>
					<tr>';

			foreach (array_keys($notebooks[0]) as $columnNames) 
				{
					echo "<th>$columnNames</th>";
				}
			echo '</tr>
				</thead>
				<tbody>';
				
			foreach ( $notebooks as $row ) {
				echo "<tr>";
				foreach ($row as $key => $value) 
				{ 
					echo "<td>$value</td>";
				}
				echo "</tr>";
			}
			echo '</tbody>
				</table>';
				
			
			?>
			<script>
					//On applique le plugin jQuery DataTable sur le tableau
					jQuery(document).ready( function () {
						var notebooksTable = jQuery('#notebooksTable').dataTable({
							
							"aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
							"iDisplayLength" : 50,
							"sScrollY": "auto",
							fnDrawCallback: function(){									//Fonction permettant de d'afficher le formulaire d'un notebook quand on clique dessus via le tableau
								 jQuery("#notebooksTable tbody tr").click(function () {
									 var position = notebooksTable.fnGetPosition(this); //get position of the selected row
									 var id = notebooksTable.fnGetData(position)[0];    //value of the first column (hidden)
									 document.location.href = "/iarc-form/?id=" + id;   //redirect
								 });
								}
						});
						notebooksTable.fnSetColumnVis( 0, false );
						jQuery("#notebooksTable").css("width","100%");
					} );
			</script>
			<?php
		}
		
		// -----------------------------------
		// Fonction principale du formulaire de notebooks
		// -----------------------------------
		function displayNotebooksForm() {
			
			// jQuery DatePicker est un plugin pour saisir les dates via calendrier
			wp_enqueue_style( 'jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css' );
			wp_enqueue_style( 'wp-jquery-date-picker', plugins_url( 'jquery-date-picker/css/admin.css' ) );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'wp-jquery-date-picker', plugins_url( 'jquery-date-picker/js/admin.js' ) );
			
			global $wpdb;
			
			ob_start(); // use output buffer of PHP
			
			echo "<div style=\"text-align: center;\"><font size=5px>Notebook checking form</font></div>";
			
			echo "<br><div style=\"text-align: right;\"><a href='/notebooks-table/'>Click here to view the notebooks table</a></div>";
			
			echo "1) To find a notebook, select the author and/or the notebook. The current information will be displayed automatically.<br>";
			echo "2) You can change all the information that have not been already fill in.<br>";
			echo "3) Click on \"Submit\" to save the changes.<br><br><hr><br>";
			
			// On rempli la ddl "Select by notebooks"
			$notebooks_results = $wpdb->get_results($this->getNotebooks(), ARRAY_A); 
			$options_select_notebooks="";
			
			foreach ( $notebooks_results as $res ) {
				$res_cat_id=$res["category_id"];
				$res_cat_name=$res["category_name"];
				$user_id=$res["user_id"];
				$options_select_notebooks.="<OPTION VALUE='{\"cat_id\":\"$res_cat_id\",\"user_id\":\"$user_id\"}'>".$res_cat_name."</OPTION>";
				// NOTE : pour pouvoir gérer coté client le filtrage de la ddl des notebooks par celle des auteurs 
				// et à la fois pouvoir faire une requete ajax après avoir choisi un notebook, 
				// on se retrouve avec le besoin de stocker deux informations dans les "value" des options 
				// de la ddl des notebooks : l'id de l'auteur du notebook et l'id du notebook. 
				// Du coup on donne en value une string au format json stockant ces deux valeurs.
			}
				
			
			// On rempli la ddl "Select by authors"
			$authors_results = $wpdb->get_results($this->getAuthors(), ARRAY_A);
			$options_select_authors="";
			
			foreach ( $authors_results as $res ) {
				$name = $res["firstname"] . " " . $res["lastname"];
				$user_id = $res["user_id"];
				$options_select_authors.="<OPTION VALUE=\"$user_id\">".$name."</OPTION>";				
			}
			
?>
			<form class="form-ui" method="post" id="formCheckNotebooks" action="javascript:submitForm();">
			
				<span class="form-elements">
				<label><b>Search by author </b></label>
					<select name="authors" id="authors" >
					<option value="-1">Select an author...
					<?=$options_select_authors;?>
					</select>
				</span>
				<br><br>
				
				<span class="form-elements">
				<label><b>Search by notebook</b></label>
				  <select name="category" id="category" onChange="getInfos(this.value)" required> 
				  <option value="">Select an ELN... 
				  <?=$options_select_notebooks;?> 
				  </select>
				</span>
				<br><br><hr><br>
				<div id="details_div"><b>Details will be listed here.</b></div><br>
				
				<span class="form-elements">
					<label for="issueDate"><b>Issue Date : </b></label>
					<input type="text" name="issueDate" id="issueDate" readonly>
				</span>
				<br><br>
				<span class="form-elements">
					<label for="checkingDate"><b>Checking : </b></label>
				    <input type="text" name="checkingDate" id="checkingDate" readonly>
				</span>
				<br><br>
				<span class="form-elements">
					<label for="remarks"><b>Remarks : </b></label>
					<textarea name="remarks" id="remarks" style="height:100px;width:400px;"> </textarea>
				</span>
				<br><br>
				<span class="form-elements">
					<label for="archivalDate"><b>Date of archival : </b></label>
				  <input type="text" name="archivalDate" id="archivalDate" readonly>
				</span>
				<br><br>
				<input type="submit" value="Submit" >
			</form>

			<script>
				
				// Rappel : les options de la ddl des notebooks contiennent une string au format json 
				// pour pouvoir stocker 2 infos : id du notebook et id de l'auteur du notebook
				jQuery(function() {
					jQuery('#category').filterByText(jQuery('#authors'));
					
					// Si une variable "id" est envoyée par l'url (ce qui se passe quand on clique sur une ligne du tableau), 
					// on charge le formulaire avec les infos du notebook en question
					var id = getQuerystring('id',0);
					if (id != 0)
					{
						getInfos('{\"cat_id\":\"' + id +'\",\"user_id\":\"notUsedHere\"}');  //on lance getInfos qui affiche les details et infos du notebook passé dans l'url (on a pas besoin ici de l'id de l'user (et on ne le connait pas à ce moment))
						
						// Ce qui suis sert juste à selectionner le notebook dans la ddl dans un souci de cohérence d'affichage,
						// c'est purement graphique et ne déclenche pas l'affichage des détails de ce notebook:
						// les détails et infos sont déja en train d'être chargés par getInfos()
						var notebooks = this;		
						var options = [];
						jQuery(notebooks).find('option').each(function() {	//On parcoure tous les notebooks de la ddl
							if (jQuery(this).val() != "")
							{
								var obj = jQuery.parseJSON(jQuery(this).val());
								options.push({cat_id: obj.cat_id, user_id: obj.user_id, text: jQuery(this).text()}); //On push les données json de chaque options dans un array
							}
						});
						jQuery(notebooks).data('options', options);
						
						jQuery.each(options, function(i) {			
							var option = options[i];
							if(option.cat_id == id) { //si la variable json 'cat_id' (id du notebook) est égale à l'id du notebook passé dans l'url, on sélectionne ce notebook dans la ddl (pour ça on a besoin de sa 'value' exacte: la string json)										
								jQuery("#category option[value='{\"cat_id\":\"" + option.cat_id + "\",\"user_id\":\"" + option.user_id + "\"}']").prop('selected', true);
							}
						});
						
					}
				});
				
				//Fonction permettant de filtrer la ddl des notebooks en fonction de la selection de la ddl des auteurs
				// On se sert de la variable json 'user_id' stockée dans les 'value' des options de la ddl des notebooks
				jQuery.fn.filterByText = function(authors) {
					return this.each(function() {
						var notebooks = this;
						var options = [];
						jQuery(notebooks).find('option').each(function() { //On parcoure tous les notebooks de la ddl
							if (jQuery(this).val() != "")
							{
								var obj = jQuery.parseJSON(jQuery(this).val());
								options.push({cat_id: obj.cat_id, user_id: obj.user_id, text: jQuery(this).text()}); //On push les données json de chaque options dans un array
							}
						});
						jQuery(notebooks).data('options', options);
						jQuery(authors).change( function() {								// Se déclence quand on sélectionne un auteur de la ddl des auteurs
							var options = jQuery(notebooks).empty().data('options'); // On vide la ddl en vue de la re-remplir avec les notebooks répondant au filtre
							var author = jQuery(this).val();						// On récupère l'id de l'auteur selectionné
						  
							jQuery(notebooks).append(
									   jQuery('<option>').text("Select an ELN...").val('') // On remet la 1ère option qui sert de titre a la ddl notebooks
									);
							
							jQuery.each(options, function(i) {	// Pour chaque notebook de la ddl :
								var option = options[i];
								if(option.user_id == author || author == -1) { // Si le notebook a pour auteur celui selectionné (ou si l'auteur selectionné est égal à -1 : si on a cliqué l'option titre de la ddl auteurs : il faut remettre tous les notebooks dans la ddl notebooks)
									jQuery(notebooks).append(				// On met le notebook dans la ddl en lui donnant correctement sa string json en 'value'
									   jQuery('<option>').text(option.text).val('{"cat_id":"' + option.cat_id + '","user_id":"' + option.user_id +'"}')
									);
								}
							});
							jQuery.fn.clearInfos();
						});            
					});
				};
				
				// Lance une requete ajax qui lance la fonction getDetails_callback() en php qui retourne les détails du notebook selectionné
				function ajaxGetDetails(ddlValue) {
				
					return jQuery.ajax({
					  type: "POST",
					  url: "/wp-admin/admin-ajax.php",
					  data: { action: 'getDetails',
								catID: ddlValue},
					  
					  success: function(response) {
						jQuery("#details_div").html(response);
					  }
					});
				}
				
				// Lance une requete ajax qui lance la fonction getEnteredInfos_callback() en php qui retourne 
				// les informations de checking déja entrées pour le notebook sélectionné
				function ajaxGetEnteredInfos(ddlValue) {
					
					return jQuery.ajax({
						type: "POST",
						url: "/wp-admin/admin-ajax.php",
						data:{action:'getEnteredInfos',		// lance la fonction php getEnteredInfos_callback
							catID: ddlValue},
							
						success:function(response){		//utilise la réponse de getEnteredInfos_callback
						
							jQuery('#issueDate').val('');
							jQuery('#checkingDate').val('');
							jQuery('#remarks').val('');
							jQuery('#archivalDate').val('');
							
							var results = jQuery.parseJSON(response);
							if (results != null)						// Pré-remplie les champs date et le champ remarque du formulaire si des valeurs existent en base
							{
								if (results.date_of_issue != null && results.date_of_issue != "0000-00-00")
								{
									var timestamp = Date.parse(results.date_of_issue);
									var d = new Date(timestamp);
									
									jQuery('#issueDate').val(jQuery.datepicker.formatDate('dd/mm/yy',d));
								}
								if (results.date_of_checking != null && results.date_of_checking != "0000-00-00")
								{
									var timestamp = Date.parse(results.date_of_checking);
									var d = new Date(timestamp);
									
									jQuery('#checkingDate').val(jQuery.datepicker.formatDate('dd/mm/yy',d));
								}
								if (results.remark != null)
								{
									jQuery('#remarks').val(results.remark);
								}
								if (results.date_of_archival != null && results.date_of_archival != "0000-00-00")
								{
									var timestamp = Date.parse(results.date_of_archival);
									var d = new Date(timestamp);
									
									jQuery('#archivalDate').val(jQuery.datepicker.formatDate('dd/mm/yy',d));
								}
							}
						}
					});
				}
				
				// Lance les fonctions ajaxGetDetails() et ajaxGetEnteredInfos(), attend qu'elles aient 
				// finit leur exécution, puis affecte un datepicker aux champs dates qui n'ont pas été auto-remplis, 
				// ou le détruit s'ils l'ont été (on ne doit pas pouvoir modifier une date déja entrée)
				function getInfos(ddlValue) {
					
					if (ddlValue != "")
					{
						ddlValue = jQuery.parseJSON(ddlValue).cat_id;
					
						jQuery.when(ajaxGetDetails(ddlValue), ajaxGetEnteredInfos(ddlValue)).done(function(a1, a2){
						
							if( 0 < jQuery('#issueDate').length && jQuery('#issueDate').val().length == 0) {
								jQuery('#issueDate').removeAttr('style');
								jQuery('#issueDate').datepicker();
								jQuery('#issueDate').datepicker( "option", "dateFormat", "dd/mm/yy" );
							}
							else
							{
								jQuery('#issueDate').datepicker("destroy");
								jQuery('#issueDate').css("border","0px");
								jQuery('#issueDate').css("background-color","#eaf4f9");
								
							}
							
							if( 0 < jQuery('#checkingDate').length && jQuery('#checkingDate').val().length == 0) {
									jQuery('#checkingDate').removeAttr('style');
									jQuery('#checkingDate').datepicker();
									jQuery('#checkingDate').datepicker( "option", "dateFormat", "dd/mm/yy" );
							}
							else
							{
								jQuery('#checkingDate').datepicker("destroy");
								jQuery('#checkingDate').css("border","0px");
								jQuery('#checkingDate').css("background-color","#eaf4f9");
								
							}
							
							if( 0 < jQuery('#archivalDate').length && jQuery('#archivalDate').val().length == 0) {
									jQuery('#archivalDate').removeAttr('style');
									jQuery('#archivalDate').datepicker();
									jQuery('#archivalDate').datepicker( "option", "dateFormat", "dd/mm/yy" );
							}
							else
							{
								jQuery('#archivalDate').datepicker("destroy");
								jQuery('#archivalDate').css("border","0px");
								jQuery('#archivalDate').css("background-color","#eaf4f9");
							}
						});
					}
					else
					{
						jQuery.fn.clearInfos();
					}
				}
	
				jQuery.fn.clearInfos = function () {
					jQuery("#details_div").html("<b>Details will be listed here.</b>");
					jQuery('#issueDate').val("");
					jQuery('#checkingDate').val("");
					jQuery('#remarks').val("");
					jQuery('#archivalDate').val("");
					jQuery('#issueDate').removeAttr('style');
					jQuery('#checkingDate').removeAttr('style');
					jQuery('#archivalDate').removeAttr('style');
				}
				
				//Fonction lancée au submit du formulaire, lance la fonction submitFormPhp via ajax pour update la table wp_iarc_forms
				function submitForm() {
					var issueDateHasDP = 0;
					var checkingDateHasDp = 0;
					var archivalDateHasDp = 0;
					
					
					var obj = jQuery.parseJSON(jQuery('#category').val());
					var catID = obj.cat_id
					
					if(jQuery('#issueDate').hasClass('hasDatepicker')) 	//On vérifie si les champs date ont la classe datepicker: si un champ ne l'a pas c'est qu'il a été pré-rempli 
					{													// car il existait déja une valeur en base, et du coup la fonction submitFormPhp ne tentera pas de l'update
						issueDateHasDP = 1;
					}
					if(jQuery('#checkingDate').hasClass('hasDatepicker'))
					{
						checkingDateHasDp = 1;
					}
					if(jQuery('#archivalDate').hasClass('hasDatepicker'))
					{
						archivalDateHasDp = 1;
					}
					
					jQuery.ajax({
					  type: "POST",
					  url: "/wp-admin/admin-ajax.php",
					  data:  "action=submitFormPhp&" 	// lance la fontion submitFormPhp
							  + jQuery('#formCheckNotebooks').serialize().toString()
							  + "&catID=" + catID
							  + "&issueDateHasDP=" + issueDateHasDP 
							  + "&checkingDateHasDp=" + archivalDateHasDp 
							  + "&archivalDateHasDp=" + issueDateHasDP,
					  success: function(response) {
							alert("Operation successful.");
						}
					}); 
				}
				
				// Fonction pour récupérer coté client les variables passées dans l'url
				function getQuerystring(key, default_) {
				   if (default_==null) default_="";
				   key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
				   var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
				   var qs = regex.exec(window.location.href);
				   if(qs == null) return default_; else return qs[1];
			   }
				
			</script>
<?php			
		}
	}
}

//Short code
if(class_exists("IARCFormsPlugin")){
  $iarcFormsPlugin = new IARCFormsPlugin();
  add_shortcode('iarc-forms',array( $iarcFormsPlugin, 'displayIARCForms' ));
}

?>