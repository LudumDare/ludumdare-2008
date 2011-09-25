<?php
if( !class_exists('ManageDarenatePlusExpenses') ):
	class ManageDarenatePlusExpenses {
		function ManageDarenatePlusExpenses() { //constructor
			if( $_GET['page'] == 'darenateplusExpenses' && ( $_GET['doaction'] || $_GET['delete'] ) )
				$this->Actions();
				
			if( $_POST['addexpense'] )
				$this->Add();

			if( $_POST['updateexpense'] )
				$this->Update();
		}
		
		function Actions(){
			global $wpdb;
			$tb = $wpdb->prefix.'expenses';
			if( $_GET['action'] == 'delete' || $_GET['delete']):
				if( $_GET['action'] ) $dIDs = $wpdb->escape($_GET['donor']);
				$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=darenateplusExpenses';
				if( $_GET['delete'] ) $dIDs[] = $wpdb->escape($_GET['delete']);
				foreach( $dIDs as $dID ):
					$del = "DELETE FROM $tb WHERE ID = $dID LIMIT 1";
					//echo $del; exit;
					$wpdb->query($del);
					$msg = 2;
				endforeach;
				header("Location:$mngpg&msg=2");
			endif;
		}
		
		function Manage(){
			global $wpdb;
			if( $_GET['edit'] ):
				$this->Edit();
			elseif( $_GET['add'] ):
				$this->Add();
				$this->Edit();
			else:
			$tb = $wpdb->prefix.'expenses';
			$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=darenateplusExpenses';
			$donors = $wpdb->get_results("SELECT * FROM $tb ORDER BY ID ASC");
			if(  $_GET['s'] ):
				$s = $wpdb->escape($_GET['s']);
				$sq = "SELECT * FROM $tb WHERE name LIKE '%$s%' OR email LIKE '%$s%' OR url LIKE '%$s%' OR comment LIKE '%$s%' ORDER BY ID ASC";
				$donors = $wpdb->get_results($sq);
			endif;
			
			?>
            <div class="wrap">
            	<h2><?php _e('Manage Expenses', 'dplus');?></h2>
                <form id="donate-filter" action="<?php echo $mngpg;?>" method="get"><input type="hidden" name="page" value="DarenatePlus" />
<p class="search-box">
	<label class="screen-reader-text" for="page-search-input"><?php _e('Search Expenses:','dplus');?></label>
	<input type="text" id="donate-search-input" name="s" value="" />
	<input type="submit" value="<?php _e('Search Expenses','dplus');?>" class="button" />

</p>
<div class="tablenav">


<div class="alignleft actions">
<select name="action">
<option value="-1" selected="selected"><?php _e('Bulk Actions', 'dplus');?></option>
<option value="delete"><?php _e('Delete', 'dplus');?></option>
</select>
<input type="submit" value="<?php _e('Apply', 'dplus');?>" name="doaction" id="doaction" class="button-secondary action" />
</div>

<br class="clear" />
</div>

<div class="clear"></div>

<table class="widefat page fixed" cellspacing="0">
  <thead>
  <tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col" id="donorname" class="manage-column column-donorname" style=""><?php _e('Expense Name', 'dplus');?></th>
	<th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Amount', 'dplus');?></th>
    <th scope="col" id="comment" class="manage-column column-comment" style=""><?php _e('Comment', 'dplus');?></th>
	<th scope="col" id="date" class="manage-column column-date" style=""><?php _e('Date', 'dplus');?></th>
  </tr>
  </thead>

  <tfoot>
  <tr>

	<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
	<th scope="col" class="manage-column column-donorname" style=""><?php _e('Expense Name', 'dplus');?></th>
	<th scope="col" class="manage-column column-amount" style=""><?php _e('Amount', 'dplus');?></th>
    <th scope="col" class="manage-column column-comment" style=""><?php _e('Comment', 'dplus');?></th>
	<th scope="col" class="manage-column column-date" style=""><?php _e('Date', 'dplus');?></th>
  </tr>
  </tfoot>

  <tbody>
  <?php
  	foreach( $donors as $dn ):
	if( $alt ) $alt = false; else $alt = 'alternate';
		?>
        <tr class="<?php echo $alt;?> iedit">
        	<th scope="row" class="check-column"><input type="checkbox" name="donor[]" value="<?php echo $dn->ID;?>" /></th>
            <td class="donorname"><strong><a class="row-title" href="<?php echo $mngpg.'&amp;edit='.$dn->ID;?>" title="<?php _e('Edit', 'dplus'); echo $dn->name;?>"><?php echo $dn->name;?></a></strong>
            	<div class="row-actions">
                	<span class="edit"><a href="<?php echo $mngpg.'&amp;edit='.$dn->ID;?>" title="<?php _e('Edit this Expense', 'dplus');?>"><?php _e('Edit','dplus');?></a> | </span><span class="delete"><a class="submitdelete" title="<?php _e('Delete this Expense','dplus');?>" href="<?php echo $mngpg.'&amp;delete='.$dn->ID;?>"><?php _e('Delete','dplus');?></a> </span>
                </div>
                <div class="hidden" id="inline_<?php echo $dn->ID;?>">
                	<div class="name"><?php echo $dn->name;?></div>
                    <div class="email"><?php echo $dn->email;?></div>
                    <div class="url"><?php echo $dn->url;?></div>
                    <div class="comment"><?php echo $dn->comment;?></div>
                    <div class="diplay"><?php echo $dn->display;?></div>
                    <div class="amount"><?php echo $dn->amount;?></div>
                    <div class="currency"><?php echo $dn->currency;?></div>
                    <div class="date"><?php echo $dn->date;?></div>
                    <div class="user_id"><?php echo $dn->user_id;?></div>
                    <div class="status"><?php echo $dn->status;?></div>
                    <div class="txn_id"><?php echo $dn->txn_id;?></div>
                </div></td>
                <td class="amount"><?php echo $dn->amount.' '.$dn->currency;?></td>
                <td class="comment"><?php echo $dn->comment;?></td>
                <td class="date"><?php echo $dn->date;?></td>
        </tr>
        
        <?php
	endforeach;
  ?>
  
  </tbody>
  </table>
  </form>
                
            </div>
            
            <?php
			endif;
		}

		function Add() {
			global $wpdb, $user_ID;
			$table_name = $wpdb->prefix."expenses";
			$txn_id = 0;
			$uID = $user_ID;
			
			//USE SECURE INSERT!
			$wpdb->query(
				$wpdb->prepare("INSERT INTO $table_name
				( name, email, url, comment, display, amount, currency, date, user_id, status, txn_id )
				VALUES ( %s, %s, %s, %s, %d, %s, %s, %s, %d, %s, %s )", 
			    $_POST['name'], $_POST['email'], $_POST['url'], strip_tags($_POST['comment']), $_POST['display'], $_POST['amount'], $_POST['currency'], date('Y-m-d H:i:s'), $uID, $_POST['status'], $txn_id )
		    );

			$_POST['notice'] = 'Expense Added';
		}
		
		function AddPage() {
			global $currency, $user_ID;
			get_currentuserinfo();
			$dplus = get_option( 'DarenatePlus' );
			if( $_POST['notice'] )
				echo '<div id="message" class="updated fade"><p><strong>' . $_POST['notice'] . '</strong></p></div>';
			?>
           <div class="wrap">
            	<h2><?php _e('Add Expense', 'dplus');?></h2>
                <form method="post" action="">
                    <input type="hidden" name="addexpense" value="true" /><!--<input type="hidden" name="dID" value="<?php echo $dID;?>" />-->
                    <table class="form-table">
                    <tbody>
                    	<tr valign="top">
                    		<th scope="row"><label for="name"><?php _e('Expense Name', 'dplus');?></label></th>
                   			<td><input name="name" id="name" value="" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="email"><?php _e('Reference Email', 'dplus');?></label></th>
                   			<td><input name="email" id="email" value="" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="url"><?php _e('Reference URL', 'dplus');?></label></th>
                   			<td><input name="url" id="url" value="" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="comment"><?php _e('Expense Comment', 'dplus');?></label></th>
                   			<td><textarea name="comment" id="comment" cols="45" rows="5"></textarea></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="display"><?php _e('Display on Recognition Wall', 'dplus');?></label></th>
                   			<td><select name="display" id="display"><option value="0" <?php if(!true) echo 'selected="selected"';?>>No</option> <option value="1" <?php if(true) echo 'selected="selected"';?>>Yes</option></select></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="amount"><?php _e('Expense Amount', 'dplus');?></label></th>
                   			<td><input name="amount" id="amount" value="" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="currency"><?php _e('Expense Currency', 'dplus');?></label></th>
                   			<td><input name="currency" id="currency" value="USD" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="status"><?php _e('Payment Status', 'dplus');?></label></th>
                   			<td><input name="status" id="status" value="" class="regular-text" type="text"></td>
                   		</tr>
                    </tbody>
                    </table>
                    <p class="submit">
                    <input name="Submit" class="button-primary" value="<?php _e('Save Changes','dplus');?>" type="submit">
                    </p>

                </form>

            </div>
			<?php
		}
		
		function Edit(){
			global $wpdb;
			$tb = $wpdb->prefix.'expenses';
			$dID = $_GET['edit'];
			$donor = $wpdb->get_row("SELECT * FROM $tb WHERE ID=$dID");
			?>
            <div class="wrap">
            	<h2><?php _e('Edit Expense Details', 'dplus');?></h2>
                <form method="post" action="">
                    <input type="hidden" name="updateexpense" value="true" /><input type="hidden" name="dID" value="<?php echo $dID;?>" />
                    <table class="form-table">
                    <tbody>
                    	<tr valign="top">
                    		<th scope="row"><label for="name"><?php _e('Expense Name', 'dplus');?></label></th>
                   			<td><input name="name" id="name" value="<?php echo $donor->name;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="email"><?php _e('Reference Email', 'dplus');?></label></th>
                   			<td><input name="email" id="email" value="<?php echo $donor->email;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="url"><?php _e('Reference URL', 'dplus');?></label></th>
                   			<td><input name="url" id="url" value="<?php echo $donor->url;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="comment"><?php _e('Expense Comment', 'dplus');?></label></th>
                   			<td><textarea name="comment" id="comment" cols="45" rows="5"><?php echo $donor->comment;?></textarea></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="display"><?php _e('Display on Recognition Wall', 'dplus');?></label></th>
                   			<td><select name="display" id="display"><option value="0" <?php if(!$donor->display) echo 'selected="selected"';?>>No</option> <option value="1" <?php if($donor->display) echo 'selected="selected"';?>>Yes</option></select></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="amount"><?php _e('Expense Amount', 'dplus');?></label></th>
                   			<td><input name="amount" id="amount" value="<?php echo $donor->amount;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="currency"><?php _e('Expense Currency', 'dplus');?></label></th>
                   			<td><input name="currency" id="currency" value="<?php echo $donor->currency;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="date"><?php _e('Expense Date', 'dplus');?></label></th>
                   			<td><input name="date" id="date" value="<?php echo $donor->date;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="user_id"><?php _e('User', 'dplus');?></label></th>
                   			<td><input name="user_id" id="user_id" value="<?php echo $donor->user_id;?>" class="regular-text" type="text"></td>
                   		</tr>
                        <tr valign="top">
                    		<th scope="row"><label for="status"><?php _e('Payment Status', 'dplus');?></label></th>
                   			<td><input name="status" id="status" value="<?php echo $donor->status;?>" class="regular-text" type="text"></td>
                   		</tr>
                    </tbody>
                    </table>
                    <p class="submit">
                    <input name="Submit" class="button-primary" value="<?php _e('Save Changes','dplus');?>" type="submit">
                    </p>

                </form>

            </div>
            <?php
		}
		
		function Update(){
			global $wpdb;
			$tb = $wpdb->prefix.'expenses';
			$dID = $_POST['dID'];
			unset($_POST['updateexpense']);
			unset($_POST['dID']);
			unset($_POST['Submit']);
			foreach( $_POST as $key => $val ):
				$update[] = $key." = '".$val."'";
			endforeach;
			$wpdb->query("UPDATE $tb SET ".implode(', ',$update)."WHERE ID=$dID" );
			$mngpg = get_option('siteurl').'/wp-admin/admin.php?page=darenateplusExpenses';
			header("Location:$mngpg&msg=1");
		}
	}
endif;

if( class_exists('ManageDarenatePlusExpenses') )
	$manageDPExpenses = new ManageDarenatePlusExpenses();