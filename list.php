<?php
error_reporting(0);

global $wpdb;
$table		=	$wpdb->prefix."html5soundcloud_playlist";


$isuccess = isset($_REQUEST['isuccess'])?$_REQUEST['isuccess']:"";
$ierror = isset($_REQUEST['ierror'])?$_REQUEST['ierror']:"";

$action = isset($_REQUEST['action'])?$_REQUEST['action']:"add";
$show = isset($_REQUEST['show'])?$_REQUEST['show']:"list";


if(isset($_GET['id'])){
	$id		=	$_GET['id'];
}


$usql		=	"SELECT * FROM $table WHERE id='$id'";
$uresults 	= 	$wpdb->get_row( $usql  );


if($action=="delete") {

$delete		=	$wpdb->query(
							"DELETE FROM $table WHERE id='$id'"
						);
						
$isuccess	=	"Item deleted successfully";						
						
}						


?>


<h2>HTML5 SoundCloud Player with Playlist Options - <a href="options-general.php?page=html5-soundcloud-player-with-playlist.php" style="background-color:#D84937; padding:5px; height:35px; color:#ffffff; font-weight:bold;">Home</a></h2>



<?php if(!empty($isuccess)): ?>
        
<span style="color:green;"><?php echo $isuccess; ?></span>

<?php elseif(!empty($ierror)): ?>

<span style="color:red;"><?php echo $ierror; ?></span>
       
<?php endif ?>

<table class="wp-list-table widefat fixed" cellspacing="0" style="margin-top:20px;">
	<thead>
	<tr>		
        <th scope="col" width="10%"><a href="#">ID</a></th>
        <th scope="col" width="15%"><a href="#">Description</a></th>
        <th scope="col" width="10%"><a href="#">Shortcode</a></th>
        <th scope="col" width="10%"><a href="#">Edit</a></th>	
        <th scope="col" width="10%"><a href="#">Delete</a></th>	
     </tr>
	</thead>

	<tfoot>
	<tr>
	    <th scope="col" width="10%"><a href="#">ID</a></th>
        <th scope="col" width="15%"><a href="#">Description</a></th>
        <th scope="col" width="10%"><a href="#">Shortcode</a></th>
        <th scope="col" width="10%"><a href="#">Edit</a></th>	
        <th scope="col" width="10%"><a href="#">Delete</a></th>		
     </tr>
	</tfoot>

	<tbody id="the-list">
    
    <?php
		//$sql		=	"SELECT * FROM $table";

		//$results 	= 	$wpdb->get_results( $wpdb->prepare( $sql ) );
		
		$sql		=	mysql_query("SELECT * FROM $table");
		
		$mmm = 0;
		
	?>
	
    <?php while( $result = mysql_fetch_object($sql) ) {   $info = unserialize($result->params);   ?>
    <tr>
        <td width="1%"><?php echo $result->id; ?></td>
        <td width="10%">
		
        SoundCloud URL: <strong><?php echo $info['html5soundcloudlink']; ?></strong><br>
        
        Player: <?php echo $info['player']; ?><br>
        
        </td>
        <td width="10%">[html5soundcloud id="<?php echo $result->id; ?>"]</td>
        <td width="10%"><a href="<?php bloginfo('url'); ?>/wp-admin/options-general.php?page=html5-soundcloud-player-with-playlist.php&action=update&id=<?php echo $result->id; ?>">Update</a></td>
        <td width="10%"><a onclick="return confirm('are you sure?');" href="<?php bloginfo('url'); ?>/wp-admin/options-general.php?page=html5-soundcloud-player-with-playlist.php&show=list&action=delete&id=<?php echo $result->id; ?>">Delete</a></td>
	</tr>
	<?php $mmm=1; } ?>
	
  	
  </tbody>
</table>   

       
