<?php
/**
 * Color_Mapping class
 *
 * @package PeachPay
 */

/**
 * Class defining color mapping functionality
 */
class Color_Mapping {
	/**
	 * The array of possible colors to be mapped to
	 *
	 * @var array
	 */
	private $colors = array(
		'#4dc9f6',
		'#f67019',
		'#f53794',
		'#537bc4',
		'#acc236',
		'#166a8f',
		'#00a950',
		'#58595b',
		'#8549ba',
	);

	/**
	 * An array with keys that point to colors
	 *
	 * @var array
	 */
	private $mapping = array();

	/**
	 * Counter for color array traversal
	 *
	 * @var int
	 */
	private $counter = 0;

	/**
	 * Getter for colors array
	 *
	 * @return array
	 */
	public function get_colors() {
		return $this->colors;
	}

	/**
	 * Function to add a key mapping to the next color in the array
	 *
	 * @param string $key new key for mapping.
	 */
	public function add_mapping( $key ) {
		if ( isset( $this->mapping[ $key ] ) ) {
			return;
		}
		$this->mapping[ $key ] = $this->counter;
		$this->counter++;
		if ( $this->counter >= count( $this->colors ) ) {
			$this->counter = 0;
		}
	}

	/**
	 * Function to get a color that the provided key is mapped to
	 *
	 * @param string $key key to get from mapping.
	 * @return string
	 */
	public function get_mapped_color( $key ) {
		if ( isset( $this->mapping[ $key ] ) ) {
			return $this->colors[ $this->mapping[ $key ] ];
		} else {
			return '#000000';
		}
	}
}
