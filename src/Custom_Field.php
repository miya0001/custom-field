<?php
/**
 * Custom Field
 *
 * @package   miya/custom-field
 * @author    Takayuki Miyauchi
 * @license   GPL v2
 * @link      https://github.com/miya0001/gh-auto-updater
 */

namespace Miya\WP;

abstract class Custom_Field
{
	protected $ID;
	protected $label;
	protected $post_type;

	public function __construct( $config )
	{
		$this->config = $config;
	}

	abstract public function admin_enqueue_scripts();
	abstract public function save_post( $post_id );
	abstract public function meta_box_callback( $post );

	public function add_meta_boxes()
	{
		if ( empty( $this->config['ID'] ) || empty( $this->config['label'] ) ) {
			throw new Exception( '`ID` or `label` are not defined.' );
		}

		add_meta_box( $this->config['ID'],
			$this->config['label'],
			array( $this, 'meta_box_callback' ),
			$this->post_type
		);
	}

	public function add( $post_type )
	{
		$this->post_type = $post_type;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}
}
