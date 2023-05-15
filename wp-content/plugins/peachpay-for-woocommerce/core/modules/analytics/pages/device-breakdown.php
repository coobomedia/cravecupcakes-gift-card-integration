<?php
/**
 * Handles payment methods section of PeachPay's analytics admin panel
 *
 * @package PeachPay
 */

if ( ! defined( 'PEACHPAY_ABSPATH' ) ) {
	exit;
}

require_once PEACHPAY_ABSPATH . 'core/modules/analytics/assets/php/class-color-mapping.php';

/**
 * Renders the analytics page.
 */
function peachpay_analytics_device_breakdown_html() {
	// Don't show the PeachPay settings to users who are not allowed to view
	// administration screens: https://wordpress.org/support/article/roles-and-capabilities/#read.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$now          = ( new DateTime() )->add( DateInterval::createFromDateString( '1 month' ) );
	$one_year_ago = ( new DateTime() )->sub( DateInterval::createFromDateString( '11 months' ) );
	$period       = new DatePeriod( $one_year_ago, DateInterval::createFromDateString( '1 month' ), $now );

	// Order query.
	$orders = wc_get_orders(
		array(
			'status'       => array_keys( wc_get_order_statuses() ),
			'date_created' => '>=' . $one_year_ago->getTimestamp(),
			'limit'        => -1,
			'orderby'      => 'payment_method',
			'order'        => 'ASC',
		)
	);

	// Get preliminary info.
	$user_agent_list = array();

	$offset = 0;
	foreach ( $orders as $order ) {
		// Eject orders without a payment method title.
		if ( ! method_exists( $order, 'get_customer_user_agent' ) ) {
			unset( $orders[ $offset ] );
			++$offset;
			continue;
		}

		$customer_user_agent = $order->get_customer_user_agent();
		if ( '' === $customer_user_agent ) {
			$customer_user_agent = 'Unset';
		}
		++$offset;
		array_push( $user_agent_list, $customer_user_agent );
	}

	?>
	<div class='pp-analytics-payment-methods-container'>
		<div class='pp-analytics-payment-methods-row'>
			<div class='pp-analytics-payment-methods-thin-graph'>
				<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Browsers', 'peachpay-for-woocommerce' ); ?></h1>
				<canvas id='pp-analytics-browser-type-pie-chart'></canvas>
			</div>
			<div class='pp-analytics-payment-methods-thin-graph'>
				<h1 class='pp-analytics-graph-title'><?php esc_html_e( 'Operating systems', 'peachpay-for-woocommerce' ); ?></h1>
				<canvas id='pp-analytics-os-type-pie-chart'></canvas>
			</div>
		</div>
	</div>
	<script>
		const parser = new UAParser();

		const color = [
			'#4dc9f6',
			'#f67019',
			'#f53794',
			'#537bc4',
			'#acc236',
			'#166a8f',
			'#00a950',
			'#58595b',
			'#8549ba',
			'#34eb46',
			'#9034e0',
			'#de2618',
			'#e3d922',
			'#28edd3',
			'#3928ed',
			'#636358', // Color for others. 
		];

		let raw_user_agent_string = <?php echo wp_json_encode( $user_agent_list ); ?>;
		let rawBrowserDataMap = new Map();
		let rawOSDataMap = new Map();
		let count = 0;
		raw_user_agent_string.forEach(raw_data => {
			parser.setUA(raw_data);
			var result = parser.getResult();
			if(rawBrowserDataMap.has(result.browser.name)) {
				rawBrowserDataMap.set(result.browser.name, rawBrowserDataMap.get(result.browser.name) + 1);
			} else {
				rawBrowserDataMap.set(result.browser.name, 1);
			}
			if(rawOSDataMap.has(result.os.name)) {
				rawOSDataMap.set(result.os.name, rawOSDataMap.get(result.os.name) + 1);
			} else {
				rawOSDataMap.set(result.os.name, 1);
			}
			count++;
		});

		const browserDataMapSort = new Map([...rawBrowserDataMap.entries()].sort((a, b) => b[1] - a[1]));
		const osDataMapSort = new Map([...rawOSDataMap.entries()].sort((a, b) => b[1] - a[1]));
		// console.log(browserDataMapSort, osDataMapSort);

		let pieBrowserData = new Array();
		let pieBrowserColor = new Array();
		let browserColorCount = 0;
		// 2. Sort and get the top 15 and items. Name and count. Put the rest in a 16th container (Other, count).
		browserDataMapSort.forEach((value, key) => {
			if(Object.keys(pieBrowserData).length < 15) {
				pieBrowserData[key] = value;
			} else {
				if(!pieBrowserData['Others']) {
					pieBrowserData['Others'] = 0;
				}
				pieBrowserData['Others'] += value;
			}
			if(browserColorCount < 15) {
				pieBrowserColor.push(color[browserColorCount]);
			}
			browserColorCount++;
		});

		if(browserColorCount > 15) {
			pieBrowserColor.push(color[15]); // Push color if Other is needed.
		}

		let pieOSData = new Array();
		let pieOSColor = new Array();
		let osColorCount = 0;
		// 2. Sort and get the top 15 and items. Name and count. Put the rest in a 16th container (Other, count).
		osDataMapSort.forEach((value, key) => {
			if(Object.keys(pieOSData).length < 15) {
				pieOSData[key] = value;
			} else {
				if(!pieOSData['Others']) {
					pieOSData['Others'] += value;
				}
			}
			if(osColorCount < 15) {
				pieOSColor.push(color[osColorCount]);
			}
			osColorCount++;
		});

		if(osColorCount > 15) {
			pieOSColor.push(color[15]); // Push color if Other is needed.
		}
		// 3. put them in a result array and then use that data set and form the pie chart.

		const browserPieChartContext = document.getElementById('pp-analytics-browser-type-pie-chart');
		const browserPieChart = new Chart(browserPieChartContext, {
			type: 'pie',
			data: {
				labels: Object.keys(pieBrowserData),
				datasets: [{
					data: Object.values(pieBrowserData),
					backgroundColor: pieBrowserColor,
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
				},
			}
		});

		const osPieChartContext = document.getElementById('pp-analytics-os-type-pie-chart');
		const osPieChart = new Chart(osPieChartContext, {
			type: 'pie',
			data: {
				labels: Object.keys(pieOSData),
				datasets: [{
					data: Object.values(pieOSData),
					backgroundColor: pieOSColor,
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
				},
			}
		});
	</script>
	<?php

}
