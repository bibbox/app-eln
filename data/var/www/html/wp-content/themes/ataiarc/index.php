<?php 
list($bfa_ata, $cols, $left_col, $left_col2, $right_col, $right_col2, $bfa_ata['h_blogtitle'], $bfa_ata['h_posttitle']) = bfa_get_options();
get_header(); 
extract($bfa_ata); 
global $bfa_ata_postcount;
?>

<?php /* If there are any posts: */
if (have_posts()) : $bfa_ata_postcount = 0; /* Postcount needed for option "XX first posts full posts, rest excerpts" */ ?>

    <?php  if ($bfa_ata['widget_center_top'] <> '') { 
          echo bfa_parse_widget_areas($bfa_ata['widget_center_top']); 
	} ?>

	<!-- IARC Modifications - Baptiste Bouchereau -->
	
	<?php // Deactivated since 3.6.5
	# include 'bfa://content_above_loop'; 
	// Uses the following static code instead: ?>
	<?php echo "<br>" ?>
	<?php bfa_next_previous_page_links('Top'); // For MULTI post pages if activated at ATO -> Next/Previous Navigation:  ?>
	<?php echo "<br>" ?>
	
	<!-- On ajoute trois liens en haut de la table of content avant la boucle d'affichage des titre des pages
	Au clic sur chaque lien, on envoie des informations par url pour enregistrer le choix de l'utilisateur 
	Probleme : si l'utilisateur veut changer plusieurs fois de suite, l'url a deja ete modifiee et donc les informations sont fausses 
	On utilise preg_replace pour reformer l'url-->
	
	<?php 
	// IARC L.Alteyrac 20150901 in the search page, the URL already contains a "?", so the order by to add must start with a "&"
	$sep = (is_search() ? '&' : '?');
	$monUrl = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
	$monUrl = preg_replace("#(\\".$sep."choice=(datento|dateotn|updatedesc|updateasc|titleasc|titledesc|authorasc|authordesc))#",'', $monUrl);
	
	if( is_category() || is_day() || is_month() || is_tag() || is_search() )  // is_day()/is_month(): click in the calendar or in Archives widget
	{
		echo "<p style=\"text-indent: 10px;\" >Sort by: ";
		if ( !is_day() ) {
			echo "<a href=\"http://"; echo $monUrl; echo $sep; echo "choice=datento\">Date of creation ▼</a> | ";
			echo "<a href=\"http://"; echo $monUrl; echo $sep; echo "choice=dateotn\">Date of creation ▲</a> |   ";
			echo "<a href=\"http://"; echo $monUrl; echo $sep; echo "choice=updatedesc\">Last update ▼</a> | ";
			echo "<a href=\"http://"; echo $monUrl; echo $sep; echo "choice=updateasc\">Last update ▲</a> |   ";
		}
		echo "<a href=\"http://"; echo $monUrl; echo $sep; echo "choice=titleasc\">Title ▼</a> | ";
		echo "<a href=\"http://"; echo $monUrl; echo $sep; echo "choice=titledesc\">Title ▲</a> |   ";
		echo "<a href=\"http://"; echo $monUrl; echo $sep; echo "choice=authorasc\">Author ID ▼</a> | ";
		echo "<a href=\"http://"; echo $monUrl; echo $sep; echo "choice=authordesc\">Author ID ▲</a> | ";
		echo "</p>";
		echo "<form method=\"post\" action=\"\">";
		echo "<input name=\"allauthors\" type=\"checkbox\" value=\"1\" "; 
			if(isset($_POST['allauthors'])) { 
				echo "checked='checked' ";
			} else {
			}
			echo "onClick=\"submit();\"";
			echo "/>Show only my posts<br>";
		echo "</form>";
		echo "<p style=\"border-bottom:solid black 1px;\"> </p>";
		
	}
	if ( isset($_POST['allauthors']) && $_POST['allauthors'] == 1) {
		$query_string .= '&author='.$current_user->ID ;
		$posts = query_posts( $query_string );
	}
	
	if ( isset($_GET['choice']) ) {
		if($_GET['choice']== 'datento') 
		{
			$posts = query_posts( $query_string . '&orderby=date&order=desc' );
		}
		else if($_GET['choice']== 'dateotn') 
		{
			$posts = query_posts( $query_string . '&orderby=date&order=asc' );
		} 
		else if($_GET['choice']== 'updatedesc') 
		{
			$posts = query_posts( $query_string . '&orderby=modified&order=desc' );
		}
		else if($_GET['choice']== 'updateasc') 
		{
			$posts = query_posts( $query_string . '&orderby=modified&order=asc' );
		}
		else if($_GET['choice']== 'titleasc') 
		{
			$posts = query_posts( $query_string . '&orderby=title&order=asc' );
		}
		else if($_GET['choice']== 'titledesc') 
		{
			$posts = query_posts( $query_string . '&orderby=title&order=desc' );
		}
		else if($_GET['choice']== 'authorasc') 
		{
			$posts = query_posts( $query_string . '&orderby=author&order=asc' );
		}
		else if($_GET['choice']== 'authordesc') 
		{
			$posts = query_posts( $query_string . '&orderby=author&order=desc' );
		}
	}
	?>
	
	<!-- Fin des modifications -->
	
	<?php while (have_posts()) : the_post(); $bfa_ata_postcount++; ?>
	
		<?php // Deactivated since 3.6.5
		#include 'bfa://content_inside_loop'; 
		// Uses the following static code instead: ?>
		<?php bfa_next_previous_post_links('Top'); // For SINGLE post pages if activated at ATO -> Next/Previous Navigation  ?>
		<?php /* Post Container starts here */
		if ( function_exists('post_class') ) { ?>
		<div <?php if ( is_page() ) { post_class('post'); } else { post_class(); } ?> id="post-<?php the_ID(); ?>">
		<?php } else { ?>
		<div class="<?php echo ( is_page() ? 'page ' : '' ) . 'post" id="post-'; the_ID(); ?>">
		<?php } ?>
		
		<?php /* IARC L.Alteyrac 20120207: Display date button */
		if ( is_single() ) { ?>
		<div class="postdate">
			<div class="postmonth"><?php the_time('M') ?></div>  
			<div class="postday"><?php the_time('d') ?></div>  
			<div class="postyear"><?php the_time('Y') ?></div>
		</div> 
		<?php } ?>
		
		<?php /* IARC L.Alteyrac 20120221: Display Edit Link */ /* IARC L.Alteyrac 20120614: Display Print Link */
		if ( is_single() ) {
			echo '<p align="right">';
			echo bfa_postinfo('%edit(\'\', \' Edit\', \' | \')%');
			if(function_exists('wp_print')) { print_link(); }
			if ( is_plugin_active('post2pdf-converter/post2pdf-converter.php') ) {
						echo " | <a href=\"";
						echo plugins_url();
						echo "/post2pdf-converter/post2pdf-converter-pdf-maker.php?id="; 
						echo the_ID(); 
						echo "\"> Export as PDF </a>";
				  }				
			/* IARC L.Alteyrac 20130102 Display Add/Remove favorite link */
			if (function_exists('wpfp_link')) { echo "   |   "; wpfp_link(); }
			echo '</p>';
		} ?>
		
		<?php bfa_post_kicker('<div class="post-kicker">','</div>'); ?>
		<?php bfa_post_headline('<div class="post-headline">','</div>'); ?>
		<?php bfa_post_byline('<div class="post-byline">','</div>'); ?>
		<?php bfa_post_bodycopy('<div class="post-bodycopy clearfix">','</div>'); ?>
		<?php bfa_post_pagination('<p class="post-pagination"><strong>'.__('Pages:','atahualpa').'</strong>','</p>'); ?>
		<?php bfa_archives_page('<div class="archives-page">','</div>'); // Archives Pages. Displayed on a specific static page, if configured at ATO -> Archives Pages: ?>
		<?php if ( is_single() ) { ?>
		<?php /* IARC L.Alteyrac 20120613: add a line before "Author | Date | Category" line */ echo "<hr />" ?>
		<?php } ?>	
		<?php bfa_post_footer('<div class="post-footer">','</div>'); ?>
		</div><!-- / Post -->	
						
	<?php endwhile; ?>

	<?php // Deactivated since 3.6.5
	# include 'bfa://content_below_loop'; 
	// Uses the following static code instead: ?>
	<?php bfa_next_previous_post_links('Middle'); // Displayed on SINGLE post pages if activated at ATO -> Next/Previous Navigation: ?>
	<?php bfa_get_comments(); // Load Comments template (on single post pages, and static pages, if set on options page): ?>
	
	<?php
		/* IARC L.Alteyrac 20120220: display the list of post's revisions after comments */
		if ( is_single() ) {
			echo "<hr />";
			iarc_revisions_list();
		}
		/* IARC L.Alteyrac 20130627: display a line at TOC's end */
		if( is_category() ) echo "</br><p style=\"border-bottom:solid black 1px;\"> </p></br>";
	?>
	
	<?php bfa_next_previous_post_links('Bottom'); echo "<br>"; // Displayed on SINGLE post pages if activated at ATO -> Next/Previous Navigation: ?>
	<?php bfa_next_previous_page_links('Bottom'); echo "<br>"; // Displayed on MULTI post pages if activated at ATO -> Next/Previous Navigation: ?>

    <?php if ($bfa_ata['widget_center_bottom'] <> '') { 
          echo bfa_parse_widget_areas($bfa_ata['widget_center_bottom']); 
    } ?>

<?php /* END of: If there are any posts */
else : /* If there are no posts: */ ?>

<?php // Deactivated since 3.6.5
#include 'bfa://content_not_found'; 
// Uses the following static code instead: ?>
<h2><?php _e('Not Found','atahualpa'); ?></h2>
<p><?php _e("Sorry, but you are looking for something that isn't here.","atahualpa"); ?></p>

<?php endif; /* END of: If there are no posts */ ?>

<?php get_footer(); ?>