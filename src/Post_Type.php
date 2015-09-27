<?php
/**
 * Gamajo Post Type
 *
 * @package   Gamajo_Registerable
 * @author    Gary Jones
 * @link      http://github.com/gamajo/registerable
 * @copyright 2015 Gary Jones, Gamajo Tech
 * @license   GPL-2.0+
 * @version   1.0.0
 */

namespace Gamajo\Registerable;

/**
 * Custom post type registration.
 *
 * @package Gamajo_Registerable
 * @author  Gary Jones
 */
abstract class Post_Type implements Registerable {
	/**
	 * Post type ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Post type arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * Register the post type.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		if ( ! $this->args ) {
			$this->set_args();
		}

		register_post_type( $this->post_type, $this->args );
	}

	/**
	 * Unregister the post type.
	 *
	 * Since there is no unregister_post_type() function, the value is unset from the global instead.
	 *
	 * @since 1.0.0
	 *
	 * @global array $wp_post_types
	 */
	public function unregister() {
		global $wp_post_types;

		if ( isset( $wp_post_types[ $this->post_type ] ) ) {
			unset( $wp_post_types[ $this->post_type ] );
		}
	}

	/**
	 * Merge any provided arguments with the default ones for a post type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Post type arguments.
	 */
	public function set_args( $args = null ) {
		$this->args = wp_parse_args( $args, $this->default_args() );
	}

	/**
	 * Return post type arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return array Post type arguments.
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Return post type ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string Post type ID.
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Return post type updated messages.
	 *
	 * @since 1.0.0
	 *
	 * @return array Post type updated messages.
	 */
	abstract public function messages();

	/**
	 * Return post type default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return array Post type default arguments.
	 */
	abstract protected function default_args();

	/**
	 * Get the revision ID from the querystring.
	 *
	 * Validates as a positive integer. Used for message 5, when restoring
	 * from a previous revision.
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool Positive integer if valid, false otherwise.
	 */
	protected function get_revision_input() {
		return filter_input( INPUT_GET, 'revision', FILTER_VALIDATE_INT, [ 'options' => [ 'min_range' => 1 ] ] );
	}

	/**
	 * Add view or preview links to the end of specific messages.
	 *
	 * Only applies if post type is publicly queryable.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $messages Existing plain text post type messages.
	 * @param \WP_Post $post     Post object.
	 * @return array Post type messages, maybe with appended links.
	 */
	protected function maybe_add_message_links( array $messages, $post ) {
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object->publicly_queryable || ! isset( $messages['view'], $messages['preview'] ) ) {
			return $messages;
		}

		$permalink = get_permalink( $post->ID );
		// get_permalink() can return false.
		if ( ! $permalink ) {
			return $messages;
		}

		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );

		$view_link    = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), $messages['view'] );
		$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), $messages['preview'] );

		$messages[1]  .= $view_link;
		$messages[6]  .= $view_link;
		$messages[9]  .= $view_link;
		$messages[8]  .= $preview_link;
		$messages[10] .= $preview_link;

		return $messages;
	}
}