<?php
	/**
		Plugin Name: Woo Orders Date Range Filter
		Description: Woo Orders Date Range Filter
		Version: 2.0.0
		Author: TheWPexperts 
		Author URI: http://www.thewpexperts.com/ 
		License: GPL 2.0
		Text Domain: woocommerce
	 */

class Woo_Orders_Filterby_Date_Range{
	const WOO_ORDERS_DATE_RANGE_FILTER_PLUGIN_VERSION = '1.0.0';
	protected static $Woo_Order_Date_Range_Filter_Class;
	public static function Woo_Order_Date_Range_Filter_Setup() {
		static::$Woo_Order_Date_Range_Filter_Class = get_called_class();

		define( 'WOO_ORDERS_FILTERBY_DATE_RANGE_DIR',     plugin_dir_path( __FILE__ ) );
		define( 'WOO_ORDERS_FILTERBY_DATE_RANGE_URL',     plugin_dir_url( __FILE__ ) );
		define( 'WOO_ORDER_FILTERBY_DATE_RANGE_INC_DIR', WOO_ORDERS_FILTERBY_DATE_RANGE_DIR . 'lib/' ); 
		add_action( 'restrict_manage_posts', array( static::$Woo_Order_Date_Range_Filter_Class, 'woo_order_add_filterby_daterange_select' ) );
		add_action( 'admin_enqueue_scripts', array( static::$Woo_Order_Date_Range_Filter_Class, 'woo_order_admin_menu_scripts' ) );
		add_filter( 'pre_get_posts',         array( static::$Woo_Order_Date_Range_Filter_Class, 'woo_order_filter_order_query' ), 10, 1 );
	}
	public static function woo_order_add_filterby_daterange_select() {
		$post_type = sanitize_text_field($_GET['post_type']);	
		if (!isset($_GET['post_type']) && $post_type !='shop_order') {
			return false;
		}
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'woo-orders-date-range-filter-style' );
		wp_enqueue_style( 'woo-orders-date-range-filter-date-selector-style' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'woo-orders-date-range-filter-script' );

		$date_predefined = isset( $_GET['date_predefined'] ) ? sanitize_text_field( $_GET['date_predefined'] ) : '';
		$date_from       = isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '';
		$date_to         = isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '';

		$intervals = self::woo_ordered_date_intervals();
		?>
		<div id="date-range-filter-interval" class="date-interval">

			<select class="field-predefined hide-if-no-js" name="date_predefined" data-placeholder="<?php _e( 'Show All Orders', 'date-range-filter' ); ?>">
				<option value="""><?php _e( 'Show All Time', 'date-range-filter' ); ?></option>
				<option value="custom" <?php selected( 'custom' === $date_predefined ); ?>><?php esc_attr_e( 'Orders By Date Range', 'date-range-filter' ) ?></option>
				<?php foreach ( $intervals as $key => $interval ) {
					printf(
						'<option value="%s" data-from="%s" data-to="%s" %s>%s</option>',
						esc_attr( $key ),
						esc_attr( $interval['start']->format( 'Y/m/d' ) ),
						esc_attr( $interval['end']->format( 'Y/m/d' ) ),
						selected( $key === $date_predefined ),
						esc_html( $interval['label'] )
					); // xss ok
				} ?>
			</select>

			<div class="date-inputs">
				<div class="box">
					<i class="date-remove dashicons"></i>
					<input type="text"
						   name="date_from"
						   class="field-from"
						   placeholder="<?php esc_attr_e( 'Start Date', 'default' ) ?>"
						   value="<?php echo esc_attr( $date_from ) ?>">
				</div>
				<span class="connector dashicons"></span>

				<div class="box">
					<i class="date-remove dashicons"></i>
					<input type="text"
						   name="date_to"
						   class="field-to"
						   placeholder="<?php esc_attr_e( 'End Date', 'default' ) ?>"
						   value="<?php echo esc_attr( $date_to ) ?>">
				</div>
			</div>

		</div>
		<?php
	}
	protected static function woo_ordered_date_intervals() {
		if ( ! class_exists( 'WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon' ) ) {
			require_once WOO_ORDER_FILTERBY_DATE_RANGE_INC_DIR . '/WooOrdersDateRangeFilterCarbon.php';
		} 

		$timezone = get_option( 'timezone_string' );

		if ( empty( $timezone ) ) {
			$gmt_offset = (int) get_option( 'gmt_offset' );
			$timezone   = timezone_name_from_abbr( null, $gmt_offset * 3600, true );
			if ( false === $timezone ) {
				$timezone = timezone_name_from_abbr( null, $gmt_offset * 3600, false );
			}
			if ( false === $timezone ) {
				$timezone = null;
			}
		}

		return apply_filters(
			'woo_orders_filterby_dates_range',
			array(
				'today' => array(
					'label' => esc_html__( 'Today Orders', 'default' ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->startOfDay(),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'yesterday' => array(
					'label' => esc_html__( 'Yesterday Orders', 'date-range-filter' ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->startOfDay()->subDay(),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->startOfDay()->subSecond(),
				),
				'last-7-days' => array(
					'label' => sprintf( esc_html__( 'Last %d Days Orders', 'date-range-filter' ), 7 ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->subDays( 7 ),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'last-14-days' => array(
					'label' => sprintf( esc_html__( 'Last %d Days Orders', 'date-range-filter' ), 14 ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->subDays( 14 ),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'last-30-days' => array(
					'label' => sprintf( esc_html__( 'Last %d Days Orders', 'date-range-filter' ), 30 ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->subDays( 30 ),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'this-month' => array(
					'label' => esc_html__( 'This Month Orders', 'date-range-filter' ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->day( 1 ),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'last-month' => array(
					'label' => esc_html__( 'Last Month Orders', 'date-range-filter' ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->day( 1 )->subMonth(),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->day( 1 )->subSecond(),
				),
				'last-3-months' => array(
					'label' => sprintf( esc_html__( 'Last %d Months Orders', 'date-range-filter' ), 3 ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->subMonths( 3 ),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'last-6-months' => array(
					'label' => sprintf( esc_html__( 'Last %d Months Orders', 'date-range-filter' ), 6 ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->subMonths( 6 ),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'last-12-months' => array(
					'label' => sprintf( esc_html__( 'Last %d Months Orders', 'date-range-filter' ), 12 ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->subMonths( 12 ),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'this-year' => array(
					'label' => esc_html__( 'This Year Orders', 'date-range-filter' ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->day( 1 )->month( 1 ),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->endOfDay(),
				),
				'last-year' => array(
					'label' => esc_html__( 'Last Year Orders', 'date-range-filter' ),
					'start' => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->day( 1 )->month( 1 )->subYear(),
					'end'   => WooOrdersDateRangeFilterCarbon\WooOrdersDateRangeFilterCarbon::today( $timezone )->day( 1 )->month( 1 )->subSecond(),
				),
			),
			$timezone
		);
	}
	public static function woo_order_admin_menu_scripts() {
		$post_type = sanitize_text_field($_GET['post_type']);
		if (!isset($_GET['post_type']) && $post_type !='shop_order') {
			return false;
		}
		wp_register_style( 'jquery-ui',WOO_ORDERS_FILTERBY_DATE_RANGE_URL . "css/woo-order-date-filter-jquery-ui.css", array(), '1.10.1' );
		wp_enqueue_style( 'woo-orders-date-range-filter-date-selector-style', WOO_ORDERS_FILTERBY_DATE_RANGE_URL . "css/woo-order-date-filter-datepicker.min.css", array( 'jquery-ui' ), self::WOO_ORDERS_DATE_RANGE_FILTER_PLUGIN_VERSION );
		wp_enqueue_style( 'woo-orders-date-range-filter-style', WOO_ORDERS_FILTERBY_DATE_RANGE_URL . "css/woo-order-date-filter-admin.min.css", array(), self::WOO_ORDERS_DATE_RANGE_FILTER_PLUGIN_VERSION );
		wp_register_script( 'woo-orders-date-range-filter-script', WOO_ORDERS_FILTERBY_DATE_RANGE_URL . "js/woo-order-date-filter-admin.min.js", array( 'jquery' ), self::WOO_ORDERS_DATE_RANGE_FILTER_PLUGIN_VERSION, true );
		wp_localize_script(
			'woo-orders-date-range-filter-script',
			'date_range_filter',
			array(
				'gmt_offset'     => get_option( 'gmt_offset' ),
			)
		);
	}
	public static function woo_order_filter_order_query( $wp_query ) {
		global $pagenow;
		if (
			is_admin()
			&& $wp_query->is_main_query()
			&& isset($_GET['post_type']) && sanitize_text_field($_GET['post_type']) =='shop_order' 
			&& ! empty( $_GET['date_from'] )
			&& ! empty( $_GET['date_to'] )
		) {
			$from = explode( '/', sanitize_text_field( $_GET['date_from'] ) );
			$to   = explode( '/', sanitize_text_field( $_GET['date_to'] ) );

			$from = array_map( 'intval', $from );
			$to   = array_map( 'intval', $to );

			if (
				3 === count( $to )
				&& 3 === count( $from )
			) {
				list( $year_from, $month_from, $day_from ) = $from;
				list( $year_to, $month_to, $day_to )       = $to;
			} else {
				return $wp_query;
			}
			$wp_query->set(
				'date_query',
				array(
					'after' => array(
						'year'  => $year_from,
						'month' => $month_from,
						'day'   => $day_from,
					),
					'before' => array(
						'year'  => $year_to,
						'month' => $month_to,
						'day'   => $day_to,
					),
					'inclusive' => apply_filters( 'woo_orders_filterby_date_range_query_is_inclusive', true ),
					'column'    => apply_filters( 'woo_orders_filterby_date_query_column', 'post_date' ),
				)
			);
		}
		return $wp_query;
	}
}

add_action( 'plugins_loaded', array( 'Woo_Orders_Filterby_Date_Range', 'Woo_Order_Date_Range_Filter_Setup' ) );
