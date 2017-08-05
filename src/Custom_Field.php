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
	protected $options;

	/**
	 * The constructor.
	 *
	 * @param string $id An identifier of the metabox.
	 * @param string $id An identifier of the metabox.
	 * @return none
	 */
	public function __construct( $id, $title, $options = array() )
	{
		$this->id = $id;
		$this->title = $title;
		$this->options = $options;
	}

	/**
	 * Fires at the `admin_enqueue_scripts` hook.
	 *
	 * @param string $hook The hook like `post.php` or so.
	 * @return none
	 */
	abstract public function admin_enqueue_scripts( $hook );

	/**
	 * The callback function of the `add_meta_box()`.
	 *
	 * @param object $post The object of the post.
	 * @return none
	 */
	abstract public function meta_box_callback( $post );

	/**
	 * Fires at the `save_post` hook.
	 *
	 * @param int $post_id The ID of the post.
	 * @return none
	 */
	abstract public function save_post( $post_id );

	/**
	 * Fires at the `add_meta_boxes` hook.
	 *
	 * @param none
	 * @return none
	 */
	public function add_meta_boxes()
	{
		add_meta_box( $this->id,
			$this->title,
			array( $this, 'meta_box_callback' ),
			$this->post_type
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
