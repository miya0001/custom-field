<?php
/**
 * Custom Field
 *
 * @package   miya/custom-field
 * @author    Takayuki Miyauchi
 * @license   GPL v2
 * @link      https://github.com/miya0001/custom-field
 */

namespace Miya\WP;

abstract class Custom_Field
{
	protected $id;
	protected $title;
	protected $post_type;
	protected $context = 'advanced';
	protected $priority = 'default';
	protected $options;

	/**
	 * The constructor.
	 *
	 * @param string $id The identifier of the metabox.
	 * @param string $title The title of the metabox.
	 * @param array $options The additional arguments.
	 *        It will be passed as the callback arguments of the `add_meta_box()` too.
	 * @return none
	 */
	public function __construct( $id, $title, $options = array() )
	{
		$this->id = $id;
		$this->title = $title;

		if ( ! empty( $options['context'] ) ) {
			$this->context = $options['context'];
		}

		if ( ! empty( $options['priority'] ) ) {
			$this->priority = $options['priority'];
		}

		$this->options = $options;
	}

	/**
	 * Fires at the `admin_enqueue_scripts` hook.
	 *
	 * @param string $hook The hook like `post.php` or so.
	 * @return none
	 */
	public function admin_enqueue_scripts( $hook )
	{
		// Nothing to do default.
	}

	/**
	 * Displays the form for the metabox. The nonce will be added automatically.
	 *
	 * @param object $post The object of the post.
	 * @param array $args The argumets passed from `add_meta_box()`.
	 * @return none
	 */
	abstract public function form( $post, $args );

	/**
	 * Save the metadata from the `form()`. The nonce will be verified automatically.
	 *
	 * @param int $post_id The ID of the post.
	 * @return none
	 */
	abstract public function save( $post_id );

	/**
	 * The callback function of the `add_meta_box()`.
	 *
	 * @param object $post The object of the post.
	 * @param array $args The argumets passed from `add_meta_box()`.
	 * @return none
	 */
	public function meta_box_callback( $post, $args )
	{
		wp_nonce_field( $this->id, $this->id );
		$this->form( $post, $args );
	}

	/**
	 * Fires at the `save_post` hook.
	 *
	 * @param int $post_id The ID of the post.
	 * @return none
	 */
	public function save_post( $post_id )
	{
		if ( ! empty( $_POST[ $this->id ] ) && wp_verify_nonce( $_POST[ $this->id ], $this->id ) ) {
			$this->save(  $post_id );
		}
	}

	/**
	 * Fires at the `add_meta_boxes` hook.
	 *
	 * @param none
	 * @return none
	 */
	public function add_meta_boxes()
	{
		add_meta_box(
			$this->id,
			$this->title,
			array( $this, 'meta_box_callback' ),
			$this->post_type,
			$this->context,
			$this->priority,
			$this->options
		);
	}

	/**
	 * Registers the metabox to the edit screen of the `$post_type`.
	 *
	 * @param string $post_type The post_type to add metabox.
	 * @return none
	 */
	public function add( $post_type )
	{
		$this->post_type = $post_type;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}
}
