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

/**
 * An abstract class to register meta box to the edit screen of the WordPress.
 */
abstract class Custom_Field
{
	/**
	 * @var string $id The identifier of the meta box.
	 */
	protected $id;

	/**
	 * @var string $title The title of the meta box.
	 */
	protected $title;

	/**
	 * @var mixed $post_type The post_type to add meta box.
	 */
	protected $post_type;

	/**
	 * @var string $context The context within the screen where the boxes should display.
	 */
	protected $context = 'advanced';

	/**
	 * @var string $priority The priority within the context where the boxes should show ('high', 'low').
	 */
	protected $priority = 'default';

	/**
	 * @var array $options The additional arguments.
	 *        It will be passed as the callback arguments of the `add_meta_box()` too.
	 */
	protected $options;

	/**
	 * The constructor.
	 *
	 * @param string $id The identifier of the meta box.
	 * @param string $title The title of the meta box.
	 * @param array $options The additional arguments.
	 *        It will be passed as the callback arguments of the `add_meta_box()` too.
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
	 * Run `wp_enqueue_*()` in this function. It will be called from `enqueue_scripts()`.
	 *
	 * @param string $hook The hook like `post.php` or so.
	 */
	public function admin_enqueue_scripts( $hook )
	{
		// Nothing to do default.
	}

	/**
	 * Displays the form for the meta box. The nonce will be added automatically.
	 *
	 * @param object $post The object of the post.
	 * @param array $args The arguments passed from `add_meta_box()`.
	 */
	abstract public function form( $post, $args );

	/**
	 * Save the metadatas from the meta box created by `form()`. The nonce will be verified automatically.
	 *
	 * @param int $post_id The ID of the post.
	 */
	abstract public function save( $post_id );

	/**
	 * The callback function of the `add_meta_box()`.
	 *
	 * @param object $post The object of the post.
	 * @param array $args The argumets passed from `add_meta_box()`.
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
	 * Fires at the `admin_enqueue_scripts` hook.
	 *
	 * @param string $hook The hook like `post.php` or so.
	 */
	public function _admin_enqueue_scripts( $hook )
	{
		if ( $this->is_the_screen( $hook ) ) {
			$this->admin_enqueue_scripts( $hook );
		}
	}

	/**
	 * Registers the meta box to the edit screen of the `$post_type`.
	 *
	 * @param mixed $post_type The post_type to add meta box.
	 */
	public function add( $post_type )
	{
		$this->post_type = $post_type;

		add_action( 'admin_enqueue_scripts', array( $this, '_admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Registers the meta box to the edit screen of the `$post_type`.
	 *
	 * @param string $screen The screen name that is passed to `admin_enqueue_scripts` hook.
	 */
	protected function is_the_screen( $screen )
	{
		$current_screen = get_current_screen();
		if ( 'post-new.php' === $screen || 'post.php' === $screen ) {
			if ( is_array( $this->post_type ) ) {
				if ( in_array( $current_screen->post_type, $this->post_type, true ) ) {
					return true;
				}
			} else {
				if ( $this->post_type === $current_screen->post_type ) {
					return true;
				}
			}
		}

		return false;
	}
}
