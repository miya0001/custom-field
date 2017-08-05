# miya/custom-field

[![Build Status](https://travis-ci.org/miya0001/custom-field.svg?branch=master)](https://travis-ci.org/miya0001/custom-field)

An abstract class to create a custom field for WordPress.

## Install

```
$ composer require miya/custom-field
```

## Example

```
<?php

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

$text_field = new Text_Field( 'text', 'Text' );
$text_field->add( 'post' );


class Text_Field extends \Miya\WP\Custom_Field
{
	/**
	 * Fires at the `admin_enqueue_scripts` hook.
	 *
	 * @param none
	 * @return none
	 */
	public function admin_enqueue_scripts()
	{
		wp_enqueue_script( ... );
	}

	/**
	 * Fires at the `meta_box_callback` hook.
	 *
	 * @param object $post A object of the post.
	 * @return none
	 */
	public function meta_box_callback( $post )
	{
		?>
			<?php wp_nonce_field( 'nonce-action', 'nonce-name' ); ?>
			<input type="text" name="input" ...>
		<?php
	}

	/**
	 * Fires at the `save_post` hook.
	 *
	 * @param int $post_id An ID of the post.
	 * @return none
	 */
	public function save_post( $post_id )
	{
		if ( ! empty( $_POST['input'] ) && wp_verify_nonce( $_POST['nonce-name'], 'nonce-action' ) ) {
			update_post_meta( $post_id, '_input', $_POST['input'] );
		}
	}
}
```
