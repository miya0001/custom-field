<?php

class Custom_Field_Tests extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_class() {
		$test01 = new Test01( 'hello', 'Hello' );
		$test01->add( 'post' );
		$this->assertSame( 'post', $test01->admin_enqueue_scripts( 'hello' ) );
		$this->assertSame( 'post', $test01->meta_box_callback( array() ) );

		ob_start();
		do_action( 'save_post', 123 );
		$res = ob_get_contents();
		ob_end_flush();
		$this->assertSame( '123', $res );
	}
}

class Test01 extends \Miya\WP\Custom_Field
{
	public function admin_enqueue_scripts( $hook )
	{
		return $this->post_type;
	}

	public function meta_box_callback( $post )
	{
		return $this->post_type;
	}

	public function save_post( $post_id )
	{
		echo $post_id;
	}
}
