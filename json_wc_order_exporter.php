<?php
/**
 * Plugin Name: JSON WC Order Exporter
 * Plugin URI: http://scootah.com/
 * Description: Export all WooCommerce orders as a JSON object
 * Version: 1.0
 * Author: Scott Grant
 * Author URI: http://scootah.com/
 */
class WP_JsonWcOrderExporter {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {
		$page = add_options_page(
			'JSON WC Order Exporter',
			'JSON WC Order Exporter',
			'manage_options',
			'json_wc_order_exporter',
			array( $this, 'plugin_page' )
		);
	}

	public function plugin_page() {
?>
<h1>JSON WC Order Exporter</h1>
<?
		if ( isset( $_GET['generate'] ) ) {
			global $wpdb;

			$min = intval( $_GET['min'] );
			$max = intval( $_GET['max'] );

			$result_obj = $wpdb->get_results(
				"SELECT * FROM $wpdb->posts WHERE post_type='shop_order' AND ID >= $min AND ID < $max ORDER BY ID",
				ARRAY_A
			);

			$order_obj = array();

			foreach ( $result_obj as $result ) {
				$meta = array_map( function( $a ){ return $a[0]; }, get_post_meta( $result['ID'] ) );

				foreach ( $meta as $k => $v ) {
					$result[$k] = $v;
				}

				$order_obj[] = json_encode( $result );
			}

			echo '<textarea style="width: 100%;">';
			echo "[\n\t" . implode( ",\n\t", $order_obj ) . "\n]";
			echo "</textarea>\n";
		}
?>
<p>
	<form method="get">
		<input type="hidden" name="page" value="json_wc_order_exporter" />
		Orders starting at: <input type="text" name="min" value="0" /><br />
		Orders less than: <input type="text" name="max" value="10000" /><br />
		<input type="submit" name="generate" value="Generate JSON Export" />
	</form>
</p>
<?
	}

}

$wp_json_wc_order_exporter = new WP_JsonWcOrderExporter();
