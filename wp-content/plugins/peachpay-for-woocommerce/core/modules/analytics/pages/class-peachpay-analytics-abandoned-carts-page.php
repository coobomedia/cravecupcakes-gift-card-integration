<?php
/**
 * Handles abandoned carts section of PeachPay's analytics admin panel
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

require_once PEACHPAY_ABSPATH . 'core/modules/analytics/assets/php/class-color-mapping.php';

/**
 * This class is responsible for loading the data for and rendering the Abandoned Carts analytics page.
 */
class PeachPay_Analytics_Abandoned_Carts_Page {

	/**
	 * Currencies for currency converter.
	 *
	 * @var Integer
	 */
	private static $currencies;
	/**
	 * Splash text for the currency converter.
	 *
	 * @var String
	 */
	private static $converted_to_base = '';

	/**
	 * X-Axis labels for bar graphs.
	 *
	 * @var Array
	 */
	private static $volume_labels = array();

	/**
	 * Local WordPress Database Manager for PeachPay.
	 *
	 * @var PeachPay_Database
	 */
	private static $local_db;

	/**
	 * Constructor magic function.
	 *
	 * @param PeachPay_Database $database The active database object.
	 */
	public function __construct( $database ) {
		self::$local_db = $database;
	}

	/**
	 * Checks whether or not the page should be displayed, builds the analytics graphs, and makes a call to render the final page.
	 */
	public function build_page() {
		// Don't show the PeachPay settings to users who are not allowed to view
		// administration screens: https://wordpress.org/support/article/roles-and-capabilities/#read.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->build_graphs();
		$this->render();
	}

	/**
	 * Builds data objects for graphs.
	 */
	private function build_graphs() {
		// Currency converter setup.
		$base                    = get_option( 'woocommerce_currency' );
		self::$converted_to_base = "Converted to $base";
		$converter               = wp_remote_get( peachpay_api_url() . "api/v1/getAllCurrency?from={$base}" );
		if ( is_wp_error( $converter ) || wp_remote_retrieve_response_code( $converter ) === 400 ) {
			$converter = array();
		} else {
			$converter = json_decode( $converter['body'], true )['rates'];
		}

		// Monthly interval.
		$now          = ( new DateTime() )->add( DateInterval::createFromDateString( '1 month' ) );
		$one_year_ago = ( new DateTime() )->sub( DateInterval::createFromDateString( '11 months' ) );
		$period       = new DatePeriod( $one_year_ago, DateInterval::createFromDateString( '1 month' ), $now );
		$counter      = 0;
		foreach ( $period as $date ) {
			self::$volume_labels[] = $date->format( 'F' );
			$counter++;
		}

		// Weekly interval.
		$now          = ( new DateTime() )->add( DateInterval::createFromDateString( '1 weeks' ) );
		$one_year_ago = ( new DateTime() )->sub( DateInterval::createFromDateString( '51 weeks' ) );
		$period       = new DatePeriod( $one_year_ago, DateInterval::createFromDateString( '1 week' ), $now );
		$counter      = 0;
		foreach ( $period as $date ) {
			$counter++;
		}

		// Get preliminary info.
		self::$currencies = array();

		ksort( self::$currencies );

		if ( count( self::$currencies ) > 1 ) {
			self::$currencies = array_merge( array( self::$converted_to_base => array() ), self::$currencies );
		}
	}

	/**
	 * HTML function.
	 * Renders the abandoned cart analytics page.
	 */
	private function render() {
		$recoverable_cart_count = wp_json_encode( self::$local_db->get_recoverable_cart_count() );
		$recoverable_cart_count = substr( $recoverable_cart_count, 1, strlen( $recoverable_cart_count ) - 2 );
		$recoverable_cart_count = strlen( $recoverable_cart_count ) < 1 ? '0' : $recoverable_cart_count;
		$abandoned_cart_count   = wp_json_encode( self::$local_db->get_abandoned_cart_count() );
		$abandoned_cart_count   = substr( $abandoned_cart_count, 1, strlen( $abandoned_cart_count ) - 2 );
		$abandoned_cart_count   = strlen( $abandoned_cart_count ) < 1 ? '0' : $abandoned_cart_count;
		?>
		<!-- Nav bar -->
		<div class='pp-analytics-payment-methods-container'>

			<div class='pp-analytics-statistic-row'>

				<span class='pp-analytics-statistic-button-flex' id='pp-analytics-recoverable-revenue'>
					<div class='pp-analytics-statistic-block'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Recoverable revenue', 'peachpay-for-woocommerce' ); ?></h1>
						<h2 class='pp-analytics-statistic'><?php echo '$' . number_format( self::$local_db->get_recoverable_total_ytd(), 2, '.', ' ' ); ?></h2>
					</div>
				</span>

				<span class='pp-analytics-statistic-button-flex' id='pp-analytics-carts'>
					<div class='pp-analytics-statistic-block'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Recoverable abandoned carts', 'peachpay-for-woocommerce' ); ?></h1>
						<h2 class='pp-analytics-statistic' id='pp-analytics-recoverable-carts'><?php echo $recoverable_cart_count; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
					</div>

					<div class='pp-analytics-statistic-block'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Total abandoned carts', 'peachpay-for-woocommerce' ); ?></h1>
						<h2 class='pp-analytics-statistic' id='pp-analytics-total-carts'><?php echo $abandoned_cart_count; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
					</div>
				</span>

				<span class='pp-analytics-statistic-button-flex' id='pp-analytics-total-revenue'>
					<div class='pp-analytics-statistic-block'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Total abandoned revenue', 'peachpay-for-woocommerce' ); ?></h1>
						<h2 class='pp-analytics-statistic'><?php echo '$' . number_format( self::$local_db->get_abandoned_total_ytd(), 2, '.', ' ' ); ?></h2>
					</div>
				</span>

				<span class='pp-analytics-statistic-button-flex' id='pp-analytics-abandonment-rate'>
					<div class='pp-analytics-statistic-block'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Cart abandonment rate', 'peachpay-for-woocommerce' ); ?></h1>
						<h2 class='pp-analytics-statistic'><?php echo number_format( self::$local_db->get_percent_abandoned(), 2, '.', ' ' ) . '%'; ?></h2>
					</div>
				</span>

			</div>

			<!-- Recoverable Revenue Graph -->
			<div class='pp-analytics-statistic-row' id='pp-analytics-recoverable-revenue-graph'>
				<div class='pp-analytics-payment-methods-full-graph'>
					<div class='pp-analytics-graph-header'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Recoverable revenue | Last 12 months', 'peachpay-for-woocommerce' ); ?></h1>
					</div>
					<canvas id='pp_recoverable_revenue_graph'></canvas>
				</div>
			</div>

			<!-- Cart Count Graph -->
			<div class='pp-analytics-statistic-row' id='pp-analytics-cart-count-graph'>
				<div class='pp-analytics-payment-methods-full-graph'>
					<div class='pp-analytics-graph-header'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Abandoned carts | Last 12 months', 'peachpay-for-woocommerce' ); ?></h1>
					</div>
					<canvas id='pp_cart_count_graph'></canvas>
				</div>
			</div>

			<!-- Total Abandoned Revenue Graph -->
			<div class='pp-analytics-statistic-row' id='pp-analytics-abandoned-revenue-graph'>
				<div class='pp-analytics-payment-methods-full-graph'>
					<div class='pp-analytics-graph-header'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Abandoned revenue | Last 12 months', 'peachpay-for-woocommerce' ); ?></h1>
					</div>
					<canvas id='pp_abandoned_revenue_graph'></canvas>
				</div>
			</div>

			<!-- Cart Abandon % Graph -->
			<div class='pp-analytics-statistic-row' id='pp-analytics-abandonment-rate-graph'>
				<div class='pp-analytics-payment-methods-thin-graph pp-analytics-payment-methods-full-graph'>
					<div class='pp-analytics-graph-header'>
						<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Cart abandonment rate', 'peachpay-for-woocommerce' ); ?></h1>
					</div>
					<canvas id='pp_abandon_percent_graph'></canvas>
				</div>
			</div>

		</div>

		<script>
			let interval_labels = [];
			interval_labels['YTD'] = <?php echo wp_json_encode( self::$volume_labels ); ?>;

			let currentInterval = 'YTD';

			let recoverable_revenue_data = <?php echo wp_json_encode( self::$local_db->get_recoverable_revenue_ytd() ); ?>;
			let cart_count_recoverable_data = <?php echo wp_json_encode( self::$local_db->get_abandoned_carts_ytd( true ) ); ?>;
			let cart_count_nonrecoverable_data = <?php echo wp_json_encode( self::$local_db->get_abandoned_carts_ytd( false ) ); ?>;
			let abandoned_revenue_data = <?php echo wp_json_encode( self::$local_db->get_abandoned_revenue_ytd() ); ?>;
			let recoverable_cart_count = <?php echo wp_json_encode( self::$local_db->get_recoverable_cart_count() ); ?>;
			let abandoned_cart_count = <?php echo wp_json_encode( self::$local_db->get_abandoned_cart_count() ); ?>;
			let unrecoverable_cart_count = abandoned_cart_count - recoverable_cart_count;
			let completed_order_count = <?php echo wp_json_encode( self::$local_db->get_completed_count() ); ?>;

			let base_currency = <?php echo wp_json_encode( self::$converted_to_base ); ?>;

			let volume_labels = <?php echo wp_json_encode( self::$volume_labels ); ?>;

			// Recoverable Revenue Graph
			const recoverable_revenue_ctx = document.getElementById('pp_recoverable_revenue_graph');
			const recoverable_revenue_graph = new Chart(recoverable_revenue_ctx, {
				type: 'bar',
				data: {
					labels: interval_labels['YTD'],
					datasets: [{
						data: recoverable_revenue_data,
						backgroundColor: [
							'#4fc3f7' // Blue 300
						]
					}]
				},
				options: {
					scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true,
							beginAtZero: true
						}
					},
					aspectRatio: 2.5,
					plugins: {
						legend: {
							display: false
						}
					}
				}
			});

			// Cart Count Graph
			const cart_count_ctx = document.getElementById('pp_cart_count_graph');
			const cart_count_graph = new Chart(cart_count_ctx, {
				type: 'bar',
				data: {
					labels: interval_labels['YTD'],
					datasets: [
						{
							label: 'Abandoned',
							data: cart_count_nonrecoverable_data,
							backgroundColor: [
								'#e57373' // red 300
							]
						},
						{
							label: 'Recoverable',
							data: cart_count_recoverable_data,
							backgroundColor: [
								'#4fc3f7' // blue 300
							]
						},
					]
				},
				options: {
					scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true,
							beginAtZero: true
						}
					},
					aspectRatio: 2.5,
					plugins: {
						legend: {
							display: false
						}
					}
				}
			});

			// Abandoned Revenue Graph
			const abandoned_revenue_ctx = document.getElementById('pp_abandoned_revenue_graph');
			const abandoned_revenue_graph = new Chart(abandoned_revenue_ctx, {
				type: 'bar',
				data: {
					labels: interval_labels['YTD'],
					datasets: [
						{
							label: 'Abandoned',
							data: abandoned_revenue_data,
							backgroundColor: [
								'#e57373' // red 300
							]
						},
						{
							label: 'Recoverable',
							data: recoverable_revenue_data,
							backgroundColor: [
								'#4fc3f7' // blue 300
							]
						},
					]
				},
				options: {
					scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true,
							beginAtZero: true
						}
					},
					aspectRatio: 2.5,
					plugins: {
						legend: {
							display: false
						}
					}
				}
			});

			// Cart Abandonment % Graph
			const abandon_percent_ctx = document.getElementById('pp_abandon_percent_graph');
			const abandon_percent_graph = new Chart(abandon_percent_ctx, {
				type: 'pie',
				data: {
					labels: ['Recoverable', 'Unrecoverable', 'Completed'],
					datasets: [{
						data: [recoverable_cart_count, unrecoverable_cart_count, completed_order_count],
						backgroundColor: ['#4fc3f7', '#e57373', '#81c784'],
					}],
				},
				options: {
					plugins: {
						legend: {
							position: 'bottom',
							align: 'start',
							labels: {
								boxWidth: 15,
							}
						},
					}
				}
			});

			// Hide inactive graphs.
			document.addEventListener('DOMContentLoaded', function(){
				document.getElementById('pp-analytics-cart-count-graph').style.display = 'none';
				document.getElementById('pp-analytics-abandoned-revenue-graph').style.display = 'none';
				document.getElementById('pp-analytics-abandonment-rate-graph').style.display = 'none';
			});

			let active_graph = 'pp-analytics-recoverable-revenue-graph';
			document.getElementById('pp-analytics-recoverable-revenue').addEventListener('click', changeGraph);
			document.getElementById('pp-analytics-recoverable-revenue').graph = 'pp-analytics-recoverable-revenue-graph';

			document.getElementById('pp-analytics-carts').addEventListener('click', changeGraph);
			document.getElementById('pp-analytics-carts').graph = 'pp-analytics-cart-count-graph';

			document.getElementById('pp-analytics-total-revenue').addEventListener('click', changeGraph);
			document.getElementById('pp-analytics-total-revenue').graph = 'pp-analytics-abandoned-revenue-graph';

			document.getElementById('pp-analytics-abandonment-rate').addEventListener('click', changeGraph);
			document.getElementById('pp-analytics-abandonment-rate').graph = 'pp-analytics-abandonment-rate-graph';

			function changeGraph(event) {
				const graph = event.currentTarget.graph;
				if(active_graph === graph) {
					return;
				}
				document.getElementById(active_graph).style.display = 'none';
				document.getElementById(graph).style.display = 'block';
				active_graph = graph;
			}

		</script>

		<?php
	}
}
