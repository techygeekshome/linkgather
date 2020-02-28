<?php
/*
* @package LinkGather
*/
/*
Plugin Name: LinkGather
Plugin URI: https://blog.techygeekshome.info/2019/03/wordpress-plugin-linkgather/?utm_medium=referral&utm_source=plugin&utm_campaign=linkgather+pluginuri+link
Description: LinkGather will gather links looking for posts, pages and JetPack shortlinks, then present you with a full list which you can also download in CSV file format.
Version: 1.0.1
Author: TechyGeeksHome
Author URI: https://blog.techygeekshome.info/?utm_medium=referral&utm_source=plugin&utm_campaign=linkgather+authoruri+link
License: GPLv2 or later
Text Domain: LinkGather

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/* Security Checks */
if ( ! defined( 'ABSPATH' ) ) 
	{
		die;
	}

/* Add Menu */
add_action( 'admin_menu', 'LinkGather_nav' );
add_action( 'admin_post_posts.csv', 'linkgather_exportpostlinks' );
add_action( 'admin_post_pages.csv', 'linkgather_exportpagelinks' );
add_action( 'admin_post_wpme.csv', 'linkgather_exportwpmelinks' );

/* Add Menu Main Menu */
function LinkGather_nav()
	{
		/* Page Title, Menu Title, Capability, Menu Slug, Function, Icon */
		add_menu_page( 'Homepage', 'LinkGather', 'manage_options', 'home_page', 'linkgather_home', 'dashicons-admin-links' );
		/* Parent Slug, Page Title, Menu Title, Capability, Menu Slug, Function */
		add_submenu_page( 'home_page', 'Home', 'Home', 'manage_options', 'home_page', 'linkgather_home' );
		/* Parent Slug, Page Title, Menu Title, Capability, Menu Slug, Function */
		add_submenu_page( 'home_page', 'Post Links', 'Post Links', 'manage_options', 'post_page', 'linkgather_getpostlinks' );
		/* Parent Slug, Page Title, Menu Title, Capability, Menu Slug, Function */
		add_submenu_page( 'home_page', 'Page Links', 'Page Links', 'manage_options', 'page_page', 'linkgather_getpagelinks' );
		/* Parent Slug, Page Title, Menu Title, Capability, Menu Slug, Function */
		add_submenu_page( 'home_page', 'wp.me Links', 'wp.me Links', 'manage_options', 'wpme_page', 'linkgather_getwpmeshortlinks' );
	}
	
/* Class to Active and Deactivate */
class LinkGather
	{
		function activate()
		{
			flush_rewrite_rules();
		}
		
		function deactivate()
		{
			flush_rewrite_rules();
		}
	}

/* All Functions */

/* Load Home Page Function */
function linkgather_home()
	{
		if ( !current_user_can( 'manage_options' ) ) 
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		echo '<div class="wrap">';
		echo '<p><h1>LinkGather by TechyGeeksHome</h1></p>';
		echo '<p><h2>Version Information</h2></p>';
		echo '<p>New Release - Version 1.0.1 - May 2019</p>';
		echo '<p><h2>Video Demo</h2></p>';
		echo '<p><iframe width="560" height="315" src="https://www.youtube.com/embed/Xfi8YoAj4eQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></p>';
		echo '<p><h2>Instructions</h2></p>';
		echo '<p>Once you have downloaded and installed the plugin, you should then go ahead and activate it which will add a new menu item on the left side of your WordPress admin console called LinkGather.</p>';
		echo '<p>When you hover over this menu option, you will then see a sub-menu with the different sections available to you:</p>';
		?>
		<img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/LinkGather-Sub-Menu.png' ?>" />;
		<?php
		echo '<p><h2>Post Links</h2></p>';
		echo '<p>Click the <b>Post Links</b> option to take you to the page that will display all of your WordPress sites published posts links:</p>';
		?>
		<img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/LinkGather-Post-Links-Page.png' ?>" />;
		<?php		
		echo '<p>You will now see a table with two columns, one for the title of your posts and another for the links of your posts. Depending on how many posts you have will depend on how long this list will be.</p>'; 
		echo '<p>Underneath the link table, you will see that LinkGather has also calculated how many post links you have.</p>'; 
		echo '<p>Below that, you will see the <b>Download Post Links</b> download link – click it to export to CSV file format.</p>'; 
		echo '<p><h2>Page Links</h2></p>'; 
		echo '<p>The above procedure is exactly the same for Page Links, however instead of bringing back published posts, it will bring back published pages instead.</p>'; 
		echo '<p><h2>wp.me Links</h2></p>'; 
		echo '<p>The option for wp.me Links is again exactly the same procedure as those above, however, wp.me links are actually built in short links created by the JetPack plugin. If you have the JetPack plugin installed, you must have the short links option also enabled for our plugin to be able to gather those links.</p>'; 
		echo '<p>If you do not have the JetPack short links module enabled, our plugin will just pull back basic post links instead.</p>'; 
		echo '<p>Assuming that you have JetPack short links enabled, clicking on our plugins wp.me Links option will bring back all those short links that LinkGather has found.</p>'; 
		echo '<p>Clicking on the <b>Download wp.me Shortlinks</b> will download the list of all the short links as shown.</p>'; 
		echo '</div>';
		echo '<h4 align="right">Developed by: <a href="https://blog.techygeekshome.info/?utm_medium=referral&utm_source=plugin&utm_campaign=linkgather+bottom+link" target="_blank">TechyGeeksHome</a> 2019&nbsp;&nbsp;&nbsp;</h4>';
	}
	
/* Run query to get all published posts and return the WP.me Shortlinks generated by JetPack, if not JetPack is enabled, it will show all standard post links */
function linkgather_getpostlinks() 
	{
		if ( !current_user_can( 'manage_options' ) ) 
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		echo '<div class="wrap">';	
		echo "<h1>LinkGather by TechyGeeksHome</h1>";
	 	echo "<h2>Published Posts Links</h2>";
		echo "<table style='width:85%' bgcolor='#9BC5F1'>";
		echo "<tr bgcolor='#ffffff'>";
		echo "<th align='left'>Title</th>";
		echo "<th align='left'>Link</th>";
		echo "</tr>";
		$posts = new WP_Query('post_type=post&posts_per_page=-1&post_status=publish');
		$posts = $posts->posts;
		foreach($posts as $post)
			{				
				switch ($post->post_type)
				{
					default:
					$postlinks = get_permalink($post->ID);
					$posttitle = $post->post_title;
					$countposts = wp_count_posts();
					$publishedpostscount = $countposts->publish;
					break;
				}
				echo "<tr>";
				echo "<td align='left' bgcolor='#ffffff'>{$posttitle}</td>";
				echo "\n";
				echo "<td align='left' bgcolor='#ffffff'>{$postlinks}</td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<p>";
			echo "<b>LinkGather has found <u>{$publishedpostscount}</u> post links.</b>";
			echo "<p>";
			echo '<a href="' . admin_url( 'admin-post.php?action=posts.csv' ) . '">Download Post Links</a>';
			echo "<p>";
			echo '<h4 align="right">Developed by: <a href="https://blog.techygeekshome.info/?utm_medium=referral&utm_source=plugin&utm_campaign=linkgather+bottom+link" target="_blank">TechyGeeksHome</a> 2019&nbsp;&nbsp;&nbsp;</h4>';
	}

/* Run query to get all published pages */
function linkgather_getpagelinks()
	{
		if ( !current_user_can( 'manage_options' ) ) 
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		echo "<div class='wrap'>";		
		echo "<h1>LinkGather by TechyGeeksHome</h1>";
	 	echo "<h2>Published Page Links</h2>";
		$pages = get_pages( 'post_status=publish' );
		echo "<table style='width:85%' bgcolor='#9BC5F1'>";
		echo "<tr bgcolor='#ffffff'>";
		echo "<th align='left'>Title</th>";
		echo "<th align='left'>Link</th>";
		echo "</tr>";
			foreach ( $pages as $page )
				{
					$pagetitle = $page->post_title;
					$pagelink = get_permalink( $page->ID );
					$countpages = wp_count_posts('page');
					$publishedpagescount = $countpages->publish;				
					echo "<tr>";
					echo "<td align='left' bgcolor='#ffffff'>{$pagetitle}</td>";
					echo "\n";
					echo "<td align='left' bgcolor='#ffffff'>{$pagelink}</td>";
					echo "</tr>";
				}
		echo "</table>";
		echo "<p>";
		echo "<b>LinkGather has found <u>{$publishedpagescount}</u> published pages.</b>";
		echo "<p>";
		echo '<a href="' . admin_url( 'admin-post.php?action=pages.csv' ) . '">Download Page Links</a>';
		echo "</div>";
		echo '<h4 align="right">Developed by: <a href="https://blog.techygeekshome.info/?utm_medium=referral&utm_source=plugin&utm_campaign=linkgather+bottom+link" target="_blank">TechyGeeksHome</a> 2019&nbsp;&nbsp;&nbsp;</h4>';
	}

function linkgather_getwpmeshortlinks() 
	{
		if ( !current_user_can( 'manage_options' ) ) 
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		echo '<div class="wrap">';	
		echo "<h1>LinkGather by TechyGeeksHome</h1>";
	 	echo "<h2>wp.me Post Shortlinks</h2>";
		echo "<b>Please Note: </b>wp.me shortlinks will only be returned if you have JetPack installed and wp.me shortlinks enabled. Otherwise, standard post links will be returned.";
		echo "<p>";
		echo "<table style='width:85%' bgcolor='#9BC5F1'>";
		echo "<tr bgcolor='#ffffff'>";
		echo "<th align='left'>Title</th>";
		echo "<th align='left'>Link</th>";
		echo "</tr>";
		$posts = new WP_Query('post_type=post&posts_per_page=-1&post_status=publish');
		$posts = $posts->posts;
			foreach($posts as $post)
			{
				switch ($post->post_type)
				{
					default:
					$wpmeshortlinks = wp_get_shortlink($post->ID);
					$wpmetitle = $post->post_title;
					$countwpmeshortlinks = wp_count_posts();
					$publishedwpmeshortlinkscount = $countwpmeshortlinks->publish;
					break;
				}
				echo "<tr>";
				echo "<td align='left' bgcolor='#ffffff'>{$wpmetitle}</td>";
				echo "\n";
				echo "<td align='left' bgcolor='#ffffff'>{$wpmeshortlinks}</td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<p>";
			echo "<b>LinkGather has found <u>{$publishedwpmeshortlinkscount}</u> wp.me shortlinks.</b>";
			echo "<p>";
			echo '<a href="' . admin_url( 'admin-post.php?action=wpme.csv' ) . '">Download wp.me Shortlinks</a>';
			
			echo "</div>";
			echo '<h4 align="right">Developed by: <a href="https://blog.techygeekshome.info/?utm_medium=referral&utm_source=plugin&utm_campaign=linkgather+bottom+link" target="_blank">TechyGeeksHome</a> 2019&nbsp;&nbsp;&nbsp;</h4>';
	}

function linkgather_exportpostlinks() 
	{
		if ( !current_user_can( 'manage_options' ) ) 
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$posts = new WP_Query('post_type=post&posts_per_page=-1&post_status=publish');
		$posts = $posts->posts;
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="posts.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');
		$file = fopen('php://output', 'w');
		fputcsv($file, array('Post Title', 'URL'));
		foreach($posts as $post)
			{
				switch ($post->post_type)
					{
						default:
						$shortlinks = wp_get_shortlink($post->ID);
						$shorttitle = $post->post_title;
						break;
					}
				fputcsv($file, array($shorttitle, $shortlinks));
			}
		 exit();
	}
	
function linkgather_exportpagelinks()
	{
		if ( !current_user_can( 'manage_options' ) ) 
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$pages = get_pages( 'post_status=publish' );	
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="pages.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');
		$file = fopen('php://output', 'w');
		fputcsv($file, array('Page Title', 'URL'));
		foreach ( $pages as $page )
			{
				$pagetitle = $page->post_title;
				$pagelink = get_permalink( $page->ID );
				fputcsv($file, array($pagetitle, $pagelink));
			}	
		exit();	
	}
	
function linkgather_exportwpmelinks()
	{
		if ( !current_user_can( 'manage_options' ) ) 
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$posts = new WP_Query('post_type=post&posts_per_page=-1&post_status=publish');
		$posts = $posts->posts;
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="wpme.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');
		$file = fopen('php://output', 'w');
		fputcsv($file, array('Post Title', 'URL'));
		foreach($posts as $post)
			{
				switch ($post->post_type)
				{
					default:
					$wpmeshortlinks = wp_get_shortlink($post->ID);
					$wpmetitle = $post->post_title;
					break;
				}
				fputcsv($file, array($wpmetitle, $wpmeshortlinks));
			}
		exit();
	}

/* Load Class */
if ( class_exists( 'LinkGather' ))
	{
		$LinkGather = new LinkGather();
	}

// activation
register_activation_hook( __FILE__, array( $LinkGather, 'activate' ));

// deactivation
register_deactivation_hook( __FILE__, array( $LinkGather, 'deactivate' ));