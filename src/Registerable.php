<?php
/**
 * Gamajo Registerable Interface
 *
 * @package   Gamajo_Registerable
 * @author    Gary Jones
 * @link      http://github.com/gamajo/registerable
 * @copyright 2015 Gary Jones, Gary Jones
 * @license   GPL-2.0+
 * @version   1.0.0
 */

namespace Gamajo\Registerable;

/**
 * Handle registration for something like a post type or taxonomy.
 *
 * @package Gamajo_Registerable
 * @author  Gary Jones
 */
interface Registerable {
	public function register();
	public function unregister();
	public function set_args( $args = null );
	public function get_args();
}
