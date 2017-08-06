# miya/custom-field

[![Build Status](https://travis-ci.org/miya0001/custom-field.svg?branch=master)](https://travis-ci.org/miya0001/custom-field)
[![Latest Stable Version](https://poser.pugx.org/miya/custom-field/v/stable)](https://packagist.org/packages/miya/custom-field)
[![Total Downloads](https://poser.pugx.org/miya/custom-field/downloads)](https://packagist.org/packages/miya/custom-field)
[![Latest Unstable Version](https://poser.pugx.org/miya/custom-field/v/unstable)](https://packagist.org/packages/miya/custom-field)
[![License](https://poser.pugx.org/miya/custom-field/license)](https://packagist.org/packages/miya/custom-field)

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
	 * @param string $hook The hook like `post.php` or so.
	 * @return none
	 */
	public function admin_enqueue_scripts( $hook ) {}

	/**
	 * Fires at the `meta_box_callback` hook.
	 *
	 * @param object $post The object of the post.
	 * @return none
	 */
	public function meta_box_callback( $post )
	{
		?>
			<?php wp_nonce_field( 'nonce-action', 'nonce-name' ); ?>
			<input type="text" name="input"
					value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_input', true ) ); ?>">
		<?php
	}

	/**
	 * Fires at the `save_post` hook.
	 *
	 * @param int $post_id The ID of the post.
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

The result is following.

![](https://www.evernote.com/l/ABXdwD3SniRG87vOmH0juQw6yY5vxS7V7_cB/image.png)
