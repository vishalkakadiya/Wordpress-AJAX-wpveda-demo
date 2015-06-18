<?php
/**
  Plugin Name: AJAX wpveda Plugin
  Plugin URI: https://profiles.wordpress.org/vishalkakadiya/
  Version: 1.0
  Author: Vishal Kakadiya
  Contributor: Vishal Kakadiya (https://profiles.wordpress.org/vishalkakadiya/)
  Description: AJAX demo with the custom table
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action( 'admin_menu', 'rtp_ajax_wpveda_menu_page' );

function rtp_ajax_wpveda_menu_page() {
	add_menu_page( 'AJAX wpveda', 'AJAX wpveda', 'manage_options', 'ajax-wpveda', 'rtp_ajax_wpveda_page' );
}

/**
 * Creating an table whenever the page is loaded
 */
function rtp_add_table_to_database() {
	$sql = "CREATE TABLE wp_ajax_wpveda_product (
			id int NOT NULL AUTO_INCREMENT,
			name VARCHAR(500) NOT NULL,
			price VARCHAR(10) NOT NULL,
			PRIMARY KEY (id)
	    );";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta( $sql );
}

function rtp_ajax_wpveda_page() {

	rtp_add_table_to_database();

	echo "<h1>AJAX wpveda demo</h1>";
	echo '<input type="button" id="ajax-wpveda-demo" value="Calulate Total Price" /><br /><br /><br />';
	echo '<div id="ajax-content"></div>';

	rtp_ajax_wpveda_product_table();
}

add_action( 'admin_footer', 'rtp_add_javascript' ); // Write our JS below here

function rtp_add_javascript() {
	?>
	<script type="text/javascript" >
		jQuery( document ).ready( function ( $ ) {

			jQuery( '#ajax-wpveda-demo' ).on( 'click', function () {

				// This does the ajax request
				$.ajax( {
					url: ajaxurl,
					data: {
						'action': 'rtp_ajax_wpveda_request',
					},
					success: function ( data ) {
						// This outputs the result of the ajax request
						$( '#ajax-content' ).html( data );
					},
					error: function ( errorThrown ) {
						console.log( errorThrown );
					}
				} );

			} );

		} );
	</script> <?php
}

function rtp_ajax_wpveda_product_table() {

	global $wpdb;
	$products = $wpdb->get_results( "SELECT * FROM wp_ajax_wpveda_product limit 20" );
//	foreach ( $products as $product ) {
//		$wpdb->get_results( "INSERT into wp_ajax_wpveda_product(name, price) VALUES('" . $product->name . "', '" . $product->price . "')" );
//	}
	?>
	<table border="1">
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Price</th>
		</tr>
		<?php foreach ( $products as $product ) { ?>
			<tr>
				<td><?php echo $product->id; ?></td>
				<td><?php echo $product->name; ?></td>
				<td><?php echo $product->price; ?></td>
			</tr>
		<?php } ?>
	</table>
	<?php
}

function rtp_ajax_wpveda_request() {

	global $wpdb;
	$result = $wpdb->get_results( "SELECT COUNT(id) as total_rows, SUM(price) as total_price FROM wp_ajax_wpveda_product" );

	echo "<p>Total <strong>({$result[0]->total_rows})</strong> rows processed and Total price count is <strong>({$result[0]->total_price})</strong></p>";

	// Always die in functions echoing ajax content
	die();
}

add_action( 'wp_ajax_rtp_ajax_wpveda_request', 'rtp_ajax_wpveda_request' );
