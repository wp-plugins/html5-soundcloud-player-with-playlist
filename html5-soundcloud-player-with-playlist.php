<?php
/*
Plugin Name: HTML5 SoundCloud Player with Playlist 
Plugin URI: http://html5plus.svnlabs.com/shop/html5-soundcloud-player-with-playlist/
Description: HTML5 SoundCloud Player with Playlist for SoundCloud Sets https://soundcloud.com/svnlabs/sets/svnlabs
Version: 1.0.6
Author: Sandeep Verma
Author URI: http://www.svnlabs.com/
*/ 


 
// Some Defaults

$vars = array(
"id" => "",
"html5soundcloudlink" => "https://soundcloud.com/svnlabs/sets/svnlabs",
"clientid" => "",
"clientsecret" => "",
"player" => "horizontal",
"bcolor" => "cccccc",
"id3" => 0,
"image" => "",
"title" => "",
"artist" => "",
"facebook" => "",
"twitter" => "",
"shadow" => "1",
"autoplay" => "0",
"download" => "0",
"background" => "ffffff"
);

if(isset($_REQUEST['id']))
 $id =	$_REQUEST['id'];
else
 $id = "";


//Create database tables
function html5soundcloud_db_create () {
    html5soundcloud_create_table_player();
}


function html5soundcloud_create_table_player(){
    //Get the table name with the WP database prefix
    global $wpdb;
    $table_name_playlist = $wpdb->prefix . "html5soundcloud_playlist";
	
     
	//Check if the table already exists and if the table is up to date, if not create it
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name_playlist'") != $table_name_playlist) {
        $sql = "CREATE TABLE " . $table_name_playlist . " (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`params` text,
					`adddate` datetime NOT NULL,
					PRIMARY KEY (`id`)	
            );";

        $wpdb->query($sql);
		
		//$sql1 = "INSERT INTO `". $table_name_playlist ."` (`id`, `url`, `size`, `xml`, `sandbox`, `adddate`) VALUES (1, 'localhost', 'full', 'sample.xml', '1', '2012-09-20 08:51:41'); ";
		//$wpdb->query($sql1);
		
		
		}
		
	
}

register_activation_hook( __FILE__, 'html5soundcloud_db_create' );


// Put our defaults in the "wp-options" table

foreach($vars as $k=>$v)
{
 add_option("soundcloud-html5-player-".$k, $v);
}


//grab options from the database





//AWS access info
//include the S3 class 
//if (!class_exists('S3'))require_once('aws/S3.php');

//AWS access info
//if (!defined('awsAccessKey')) define('awsAccessKey', $awskey);
//if (!defined('awsSecretKey')) define('awsSecretKey', $awssecretkey);


// Start the plugin
if ( ! class_exists( 'SoundCloud_HTML5_Player' ) ) {
	class SoundCloud_HTML5_Player {
// prep options page insertion
		function add_config_page() {
			if ( function_exists('add_submenu_page') ) {
				add_options_page('HTML5 SoundCloud Player Options', 'HTML5 SoundCloud Player Options', 10, basename(__FILE__), array('SoundCloud_HTML5_Player','config_page'));
			}	
	}
// Options/Settings page in WP-Admin
		function config_page() {
		
		  global $vars,$wpdb;
		  $table_name_playlist = $wpdb->prefix . "html5soundcloud_playlist";
		  
		  
		  if(isset($_REQUEST['id'])){
			$id		=	$_REQUEST['id'];
		  
		  
		  $params =  array();
		  
		
			if ( isset($_POST['submit']) ) {
				$nonce = $_REQUEST['_wpnonce'];
				if (! wp_verify_nonce($nonce, 'soundcloud-html5-player-updatesettings') ) die('Security check failed'); 
				if (!current_user_can('manage_options')) die(__('You cannot edit the search-by-category options.'));
				check_admin_referer('soundcloud-html5-player-updatesettings');	
			// Get our new option values
			
			
			foreach($vars as $k=>$v)
            {
			  update_option("soundcloud-html5-player-".$k, mysql_real_escape_string( $_POST[$k] ));
			  
			  $params[$k] = $_POST[$k];
			  			  
			}
			
		    // Update the DB with the new option values
			
			
			
			if(isset($id) && $id!="")
			{
			
			$sql1 = "UPDATE `". $table_name_playlist ."` set `params` = '".serialize($params)."', adddate = now() where `id` = '".$id."' ";
		    $wpdb->query($sql1);
			
			}
			else
			{				
							
			$sql1 = "INSERT INTO `". $table_name_playlist ."` (`id`, `params`, `adddate`) VALUES ('', '".serialize($params)."', now() ); "; 
		    $wpdb->query($sql1);				
			
			$id = mysql_insert_id();
			
			}
			
			
			}	
			
			
			$usql		=	"SELECT * FROM $table_name_playlist WHERE id='$id'";
			$uraaesults 	= 	$wpdb->get_row( $usql  );
			
			$uresults = unserialize($uraaesults->params);
			
			foreach($vars as $k=>$v)
            {
			
			  $$k = $uresults[$k];
			
			}
			
			
			}
			else
			{
			
			foreach($vars as $k=>$v)
            {
			  $$k = get_option("soundcloud-html5-player-".$k);	
			  
			  //echo $k . " = ". $$k."<br />";
			  
			}
			
			//echo $bucketname;
			
			
			}

			
?>

<?php 

$show = isset($_REQUEST['show'])?$_REQUEST['show']:"";

if($show == "list")
{
  include("list.php");
  exit(1);
}

?>

<div class="wrap">
  
    <h2>HTML5 SoundCloud Player with Playlist Options - <a href="options-general.php?page=html5-soundcloud-player-with-playlist.php&show=list" style="background-color:#D84937; padding:5px; height:35px; color:#ffffff; font-weight:bold;">Saved Players</a></h2>
  
  
  <?php
  
  // Check for CURL
if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
	exit("\nERROR: CURL extension not loaded\n\n");
  
  
  ?>
  
  
  <form action="" method="post" id="soundcloud-html5-player-config">
  

Create new application on SoundCloud <a href="http://soundcloud.com/you/apps" target="_blank">http://soundcloud.com/you/apps</a> & get Client Id and Client Secret 
  
    <table class="form-table">
      <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('soundcloud-html5-player-updatesettings'); } ?>
       
      
      
            
      <tr>
        <th scope="row" valign="top"><label for="html5soundcloudlink">SoundCloud Set URL:</label></th>
        <td><input type="text" name="html5soundcloudlink" id="html5soundcloudlink" class="regular-text" value="<?php echo $html5soundcloudlink; ?>"/></td>
      </tr>
      
      
      
      
          
      
     <tr>
        <th scope="row" valign="top"><label for="html5soundcloudlink">Client ID:</label></th>
        <td><input type="text" name="clientid" id="clientid" class="regular-text" value="<?php echo $clientid; ?>"/></td>
      </tr>
      
      
      <tr>
        <th scope="row" valign="top"><label for="html5soundcloudlink">Client Secret:</label></th>
        <td><input type="text" name="clientsecret" id="clientsecret" class="regular-text" value="<?php echo $clientsecret; ?>"/></td>
      </tr>
      
      
      
          <tr>
        <th scope="row" valign="top"><label for="player">Player:</label></th>
        
        <td>
        <select name="player">
        <option value="horizontal" <?php if($player=="horizontal") { ?> selected="selected" <?php } ?> >horizontal</option>
        <option value="vertical"  <?php if($player=="vertical") { ?> selected="selected" <?php } ?>>vertical</option>
        </select>
        </td>
      </tr>
 
 
           <tr>
        <th scope="row" valign="top"><label for="player">ID3 Tag Enabled?:</label></th>
        
        <td>
        <input name="id3" type="checkbox" <?php if($id3==1) { ?> checked="checked" <?php } ?> value="1"  />  &nbsp;(Paid Feature)
        </td>
      </tr>
      
      
      
      
                 <tr>
        <th scope="row" valign="top"><label for="player">Player Shadow:</label></th>
        
        <td>
        <input name="shadow" type="checkbox" <?php if($shadow==1) { ?> checked="checked" <?php } ?> value="1"  />
        </td>
      </tr>
      
      
                 <tr>
        <th scope="row" valign="top"><label for="player">Player Autoplay:</label></th>
        
        <td>
        <input name="autoplay" type="checkbox" <?php if($autoplay==1) { ?> checked="checked" <?php } ?> value="1"  />
        </td>
      </tr>
      
      
      
                 <tr>
        <th scope="row" valign="top"><label for="player">Downloads:</label></th>
        
        <td>
        <input name="download" type="checkbox" <?php if($download==1) { ?> checked="checked" <?php } ?> value="1"  />&nbsp;(Paid Feature)
        </td>
      </tr>
      
      
 
      
      <tr>
        <th scope="row" valign="top"><label for="player">Player BG Color:</label></th>
        
        <td>
        #<input class="color" name="bcolor" id="bcolor" type="text" value="<?php echo $bcolor; ?>" />
        </td>
      </tr>
      
      
      
          <tr>
        <th scope="row" valign="top"><label for="player">Player Background:</label></th>
        
        <td>
        #<input class="color" name="background" id="background" type="text" value="<?php echo $background; ?>" />
        </td>
      </tr>
      
      

      
      
      
      <tr>
        <th scope="row" valign="top"><label for="player">Player Artwork:</label></th>
        
        <td>
        <input name="image" id="image" type="text" class="regular-text" value="<?php echo $image; ?>" />
        </td>
      </tr>
      
      
            <tr>
        <th scope="row" valign="top"><label for="player">Title:</label></th>
        
        <td>
        <input name="title" id="title" type="text" class="regular-text" value="<?php echo $title; ?>" />
        </td>
      </tr>
      
      
            <tr>
        <th scope="row" valign="top"><label for="player">Artist:</label></th>
        
        <td>
        <input name="artist" id="artist" type="text" class="regular-text" value="<?php echo $artist; ?>" />
        </td>
      </tr>
      
      
      
         
      
     
      
       <tr>
        <th scope="row" valign="top"><label for="player">Facebook:</label></th>
        
        <td>
        <input name="facebook" id="facebook" type="text" class="regular-text" value="<?php echo $facebook; ?>" /> &nbsp;(Paid Feature)
        </td>
      </tr>
      
       <tr>
        <th scope="row" valign="top"><label for="player">Twitter:</label></th>
        
        <td>
        <input name="twitter" id="twitter" type="text" class="regular-text" value="<?php echo $twitter; ?>" />&nbsp;(Paid Feature)
        </td>
      </tr>
      
      
      
     
      
      
      <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
      
      
      
      <input type="hidden" name="plugin_url" id="plugin_url" value="<?php echo plugin_dir_url( __FILE__ ); ?>"  />
      
      

    </table>
    <br/>
    <span class="submit" style="border: 0;">
    <input type="submit" name="submit" value="Save Settings" />
    </span>
  </form>
 <?php SoundCloud_html5_player(); ?>
<br />
<?php /*?><h3>PHP Code for template php files</h3>
<code>&lt;?php SoundCloud_html5_player(); ?&gt;</code><?php */?>

<?php

$prms = ' ';

foreach($vars as $k=>$v)
{

 $prms .= $k.'="'.get_option("soundcloud-html5-player-".$k).'" ';
  
}  

?>

<h3>Shortcode for Page or Post</h3>


<?php if($id!="") { ?>
<code><a href="options-general.php?page=shoutcast-icecast-html5-radio-player.php&show=list">[html5soundcloud id="<?php echo $id; ?>"]</a><br /></code> &nbsp;OR <br />
<?php } ?>

<code>[html5soundcloud <?php echo $prms; ?>]</code><br /><br />


<?php //echo $scode; ?>

<?php

$pluginurl	=	plugin_dir_url( __FILE__ );


//$iframe = '<iframe src="'.$pluginurl.'video.php?cloudfrontlink='.$cloudfrontlink.'&brcolor='.$brcolor.'&bcolor='.$bcolor.'&download='.$download.'&facebook='.$facebook.'&twitter='.$twitter.'&channeltitle='.$channeltitle.'&shuffle='.$shuffle.'" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" width="367" height="227"></iframe>';


?>
<br />

<?php /*?><hr />

<h3>Embed Anywhere</h3>

<textarea cols="60" rows="10" onFocus="this.select();" style="border:1px dotted #343434" ><?php echo $iframe; ?></textarea><?php */?>

<!-- Paypal etc.  -->


<strong>Get Pro Version <a href="http://html5plus.svnlabs.com/shop/html5-soundcloud-player-with-playlist/" target="_blank">HTML5 SoundCloud Player with Playlist</a></strong>

<br />
<br />



<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=181968385196620";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="fb-like" data-href="https://www.facebook.com/Html5Mp3Player" data-send="true" data-width="450" data-show-faces="true"></div> 




 </div>
<?php		}
	}
} 


  
// Base function 
function SoundCloud_html5_player($atts = null, $content = null) {

// Plugin Url

global $vars;

if(isset($_REQUEST['id']))
 $id =	$_REQUEST['id'];
else
 $id = get_option("soundcloud-html5-player-id"); 

$attrs = array(); 
$prms = '';

foreach($vars as $k=>$v)
{

 $attrs[$k] = get_option("soundcloud-html5-player-".$k);
 
/*if($id!="") 
{
 $prms = 'id='.$id.'&';
}
else
{ */	 

if($k!="id")
 $prms .= $k."=".get_option("soundcloud-html5-player-".$k)."&";
 
/*}*/
 
 
}

//print_r($attrs);

$pluginurl	=	plugin_dir_url( __FILE__ );

//$prms .= 'pluginurl='.$pluginurl;

extract( shortcode_atts( $attrs, $atts ) );


echo '<br />';


$prms = substr($prms, 0, strlen($prms)-1);
 

if($player == "horizontal")
{
$iframe = '<iframe src="http://html5soundcloud.svnlabs.com/html5fullsoundcloud.php?'.$prms.'" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" width="586" height="227"></iframe>';
}
else
{
$iframe = '<iframe src="http://html5soundcloud.svnlabs.com/html5bigsoundcloud.php?'.$prms.'" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" width="367" height="434"></iframe>';
}

echo $iframe;

}

// insert into admin panel
add_action('admin_menu', array('SoundCloud_HTML5_Player','add_config_page'));
add_shortcode( 'html5soundcloud', 'SoundCloud_html5_player' );


function  SoundCloud_html5_method() {
	
    wp_register_script( 'custom-script7', plugins_url( '/js/jscolor-new.js', __FILE__ ) );
    wp_enqueue_script( 'custom-script7' );
	
	//wp_enqueue_script( 'my-js4534534534', plugins_url( '/js/jscolor.js', __FILE__ ), false );
	
		
/*	wp_register_script( 'custom-script1', plugins_url( '/html5lyrics/js/jscolor.js', __FILE__ ) );
    wp_enqueue_script( 'custom-script1' );
	
	wp_register_script( 'custom-script2', plugins_url( '/html5lyrics/js/core.js', __FILE__ ) );
    wp_enqueue_script( 'custom-script2' );
*/	
	
	
}    
 
add_action('wp_enqueue_scripts', 'SoundCloud_html5_method');


?>