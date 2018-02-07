<?php

if ( !current_user_can( 'manage_options' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

echo "<link rel='stylesheet' type='text/css' href='" . plugins_url() . "/iarc-custom/settings/iarc-options.css' />"; 

$doc_pages = array(
				array('id'=>'favPosts',	'header'=>'Favorite Posts',	   'parent' => ''),
				array('id'=>'ug',		'header'=>'User Guide', 	   'parent' => 'Documentation'),
				array('id'=>'elnInstr', 'header'=>'ELN Instructions',  'parent' => 'Documentation'),
				array('id'=>'sugg',		'header'=>'Suggestions/Ideas', 'parent' => 'Documentation'),
				array('id'=>'extra1',	'header'=>'Additionnal Link 1','parent' => ''),
				array('id'=>'extra2',	'header'=>'Additionnal Link 2','parent' => '')
				);

$iarc_options = get_option('iarc_options');

if ( isset($_POST['submit']) ) {
	$iarc_options['dashboardBoxTitle'] = $_POST['dashboardBoxTitle'];
	$iarc_options['dashboardInfo'] 	   = stripslashes($_POST['dashboardInfo']);
	$iarc_options['loginPageText'] 	   = stripslashes($_POST['loginPageText']);
	$iarc_options['htmlEditorHeight']  = (is_numeric($_POST['htmlEditorHeight'])? $_POST['htmlEditorHeight'] : "600");
	$iarc_options['displayEditorInfo'] = $_POST['displayEditorInfo'];
	$iarc_options['editorInfo'] 	   = stripslashes($_POST['editorInfo']);
	$iarc_options['maxAttachmentSize'] = (is_numeric($_POST['maxAttachmentSize'])? $_POST['maxAttachmentSize'] : "2");
	$iarc_options['withNotice']  	   = $_POST['withNotice'];
	$iarc_options['disableAutosave']   = $_POST['disableAutosave'];
	$iarc_options['displayComments']   = $_POST['displayComments'];
	$iarc_options['displayHome'] 	   = $_POST['displayHome'];
	
	foreach ( $doc_pages as $doc_page ) {
		$iarc_options['buttons'][$doc_page['id']] = array('id' => $doc_page['id'],
									  'parent' => $doc_page['parent'],
									  'display' => $_POST[$doc_page['id'].'Display'],
									  'title' => $_POST[$doc_page['id'].'Title'],
									  'page' => $_POST[$doc_page['id'].'Page']);
	}
		
	$iarc_options['colSortableList']['author'] 	 	= isset($_POST['colAuthor']) ? $_POST['colAuthor'] : '0';
	$iarc_options['colSortableList']['categories'] 	= isset($_POST['colCategory']) ? $_POST['colCategory'] : '0';
	$iarc_options['colSortableList']['tags'] 	 	= isset($_POST['colTag']) ? $_POST['colTag'] : '0';
}
update_option('iarc_options', $iarc_options);

if ( !empty($_POST ) ) : ?>
	
	<?php if ( !is_numeric($_POST['htmlEditorHeight']) ) : ?>
		<div class="notice notice-warning"><p><strong><?php _e( 'Warning: the height of HTML Editor entered is not a number, so it had been fixed to default value (600px)' ); ?></strong></p></div>
	<?php endif; ?>
	
	<?php foreach ( $doc_pages as $doc_page) {
		if ( $iarc_options['buttons'][$doc_page['id']]['display'] == '1' && empty($iarc_options['buttons'][$doc_page['id']]['page']) ) : ?>
		<div class="notice notice-error">
		<p><strong><?php _e( 'No page selected for "' . $iarc_options['buttons'][$doc_page['id']]['title'] . '". The entry won\'t be added in the menu.'); ?></strong></p>
		</div>
		<?php 
		$iarc_options['buttons'][$doc_page['id']]['display'] = '0';
		update_option('iarc_options', $iarc_options);
		endif; 
	}
	?>
	
	<div class="notice notice-success"><p><strong><?php _e( 'Settings saved ' ); ?></strong></p></div>
	
<?php endif; ?>
	
<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

	<h1>IARC Customization Options</h1>

	<h2>Login page message</h2>
	<p>
		<label for="loginPageText">Enter the Login Page Text (HTML can be used):</label>
		<br><br>
		<textarea id="loginPageText" style="width:80%; height:250px;" name="loginPageText"><?php if ( isset($iarc_options['loginPageText']) ) _e($iarc_options['loginPageText']); ?></textarea>
		<br>
	</p>
	<br>
	<h2>Dashboard message</h2>
	<p>
		<label for="dashboardBoxTitle">Box Title:</label>
		<input type="text" id="dashboardBoxTitle" name="dashboardBoxTitle" style="width:300px" 
			value="<?php if ( isset($iarc_options['dashboardBoxTitle']) ) _e($iarc_options['dashboardBoxTitle']); else echo "Welcome"; ?>">
		</input>
		<br><br>
		<p style="margin-bottom: 0">
		<label for="dashboardInfo">Information to add in the Dashboard (HTML can be used):</label>
		<br><br>
		<textarea id="dashboardInfo" style="width:80%; height:150px;" name="dashboardInfo"><?php if ( isset($iarc_options['dashboardInfo']) ) _e($iarc_options['dashboardInfo']); ?></textarea>
		<br><span style="font-size: 12px">Tip: you can use the css class eln-info for the p tag, to display a nice "notebook" icon before the text</span>
		</p>
	</p>
	<br>
	<h2>Editor</h2>
	<h4>Height</h4>
	<p>
		<label for="htmlEditorHeight">Default Height:</label>
		<input type="text" id="htmlEditorHeight" name="htmlEditorHeight" style="width:60px" 
			value="<?php if ( isset($iarc_options['htmlEditorHeight']) ) _e($iarc_options['htmlEditorHeight']); else echo "600"; ?>">
		</input>px
	</p>
	<h4>Editor Info</h4>
	<p style="margin-bottom: 0">
		<label for="editorInfo">Enter information that will be inserted before the editor, ie to make an announcement (HTML can be used):</label>
		<br><br>
		<textarea id="editorInfo" style="width:80%; height:100px;" name="editorInfo"><?php if ( isset($iarc_options['editorInfo']) ) _e($iarc_options['editorInfo']); ?></textarea>
		<br>
		<p style="margin-top: 0">
			<?php if ($iarc_options['displayEditorInfo'] == '') $iarc_options['displayEditorInfo'] = '1'; ?>
			<input type="hidden" name="displayEditorInfo" value="0" />
			<input type="checkbox" name="displayEditorInfo" value="1"<?php checked( $iarc_options['displayEditorInfo'] == '1' ); ?> />
			Display this information in the Editor
			<br>
			<?php if ( !isset($iarc_options['withNotice']) ) $iarc_options['withNotice'] = '1'; ?>
			<input type="hidden" name="withNotice" value="0" />
			<input type="checkbox" name="withNotice" value="1"<?php checked( $iarc_options['withNotice'] == '1' ); ?> />
			Display this information as a WordPress Notice
		</p>
	</p>
	<br>
	<h2>Media Library</h2>
	<p>
		<label for="maxAttachmentSize">Maximum upload file size:</label>
		<input type="text" id="maxAttachmentSize" name="maxAttachmentSize" style="width:30px" 
			value="<?php if ( isset($iarc_options['maxAttachmentSize']) ) _e($iarc_options['maxAttachmentSize']); else echo "2"; ?>">
		</input> MB
	</p>
	<br>
	<h2>Autosave</h2>
	<p>The WordPress autosave functionality is activated in post-new.php.</p>
	<p>Disable Autosave to avoid Uncategorized posts&nbsp
	<?php if ( !isset($iarc_options['htmlEditorHeight']) || $iarc_options['disableAutosave'] == '') $iarc_options['disableAutosave'] = '1'; ?>
	<input type="hidden" name="disableAutosave" value="0" />
	<input type="checkbox" name="disableAutosave" value="1"<?php checked( $iarc_options['disableAutosave'] == '1' ); ?> />
	</p>
	<br>
	<h2>Admin Bar</h2>
	
	<p class="admin-bar-title">Select the links to display in the WordPress Admin Bar:</p>
	<table>
		<tr>
			<th>Comments</th>
			<td>
				<?php if ($iarc_options['displayComments'] == '') $iarc_options['displayComments'] = '0'; ?>
				<input type="hidden" name="displayComments" value="0" />
				<input type="checkbox" name="displayComments" value="1"<?php checked( $iarc_options['displayComments'] == '1' ); ?> />
			</td>
		</tr>
		<tr></tr>
		<tr>
			<th>Home Page</th>
			<td>
				<?php if ( !isset($iarc_options['displayHome']) ) $iarc_options['displayHome'] = '1'; ?>
				<input type="hidden" name="displayHome" value="0" />
				<input type="checkbox" name="displayHome" value="1"<?php checked( $iarc_options['displayHome'] == '1' ); ?> />
			</td>
		</tr>
	</table>
	
	<p class="admin-bar-title">For the following links, you can also change the title of the link and the (ELN) page to open:</p>
	<table>
		<?php 
		foreach ( $doc_pages as $doc_page) {
			isset($option_html) ? $option_html .= "<tr>" : $option_html = "<tr>";
			$option_html .= "<th>" . $doc_page['header'] . "</th>";
			
			$option_html .= "<td>";
			if ( !isset($iarc_options['buttons'][$doc_page['id']]['display']) ) $iarc_options['buttons'][$doc_page['id']]['display'] = '0';
			$option_html .= "<input type=\"hidden\" name=\"" . $doc_page['id'] ."Display\" value=\"0\" />";
			$option_html .= "<input type=\"checkbox\" name=\"" . $doc_page['id'] ."Display\" value=\"1\"";
			if ( $iarc_options['buttons'][$doc_page['id']]['display'] == '1' ) $option_html .= " checked = 'checked'" ;
			$option_html .= "/></td>";
			
			$option_html .= "<td>";
			$option_html .= "<label class=\"admin-bar\">Display Title:</label>";
			$option_html .= "<input type=\"text\" id=\"" . $doc_page['id'] . "Title\" name=\"" . $doc_page['id'] . "Title\" style=\"width:150px\" ";
			if ( !isset($iarc_options['buttons'][$doc_page['id']]['title']) ) $option_html .= "value=\"" . $doc_page['header'] . "\"";
			else $option_html .= "value=\"" . $iarc_options['buttons'][$doc_page['id']]['title'] . "\"";
			$option_html .= "></input>";
			$option_html .= "</td>";
			
			$option_html .= "<td>";
			$option_html .= "<label class=\"admin-bar\">Page:</label>";
			$dropdown_args = array( 'echo' => '0', 'id' => $doc_page['id'] . 'Page', 'name' => $doc_page['id'] . 'Page');
			if ( !isset($iarc_options['buttons'][$doc_page['id']]['page']) ) 
			  $dropdown_args['show_option_none'] = 'Please select a page';
			else
			  $dropdown_args['selected'] = $iarc_options['buttons'][$doc_page['id']]['page'];
			$option_html .= wp_dropdown_pages( $dropdown_args );
			$option_html .= "</td>";
			
			if ( $doc_page['id'] == 'favPosts' ) {
				$option_html .= "<td style=\"font-style:italic\">(Needs the plugin WP Favorite Posts)</td>";
			}
			
			$option_html .= "</tr>";
		}
		echo $option_html;
		?>
	</table>
	<br>
		
	<h2>Dashboard</h2>
	
	<p>Select which columns in "Posts" list should be sortable:</p>
	<table>
		<tr>
		<th>Author</th>
		<td>
			<input type="checkbox" name="colAuthor" value="1"<?php checked( $iarc_options['colSortableList']['author'] == '1') ; ?> />
		</td>
		</tr>
		<tr>
		<th>Category</th>
		<td>
			<input type="checkbox" name="colCategory" value="1"<?php checked( $iarc_options['colSortableList']['categories'] == '1' ); ?> />
		</td>
		</tr>
		<tr>
		<th>Tag</th>
		<td>
			<input type="checkbox" name="colTag" value="1"<?php checked( $iarc_options['colSortableList']['tags'] == '1' ); ?> />
		</td>
		</tr>
	</table>
	<br>	
	
	<div class="submit">
		<input type="submit" name="submit" value="<?php _e('Update Settings') ?>" />
	</div>

</form>
</div>