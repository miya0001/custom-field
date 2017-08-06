<?php

class Custom_Field_Tests extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_class() {
		$test01 = new Test01( 'hello', 'Hello' );
		$test01->add( 'post' );
		$this->assertSame( 'post', $test01->admin_enqueue_scripts( 'hello' ) );
		$this->assertSame( 'post', $test01->form( array(), array() ) );
	}
}

class Test01 extends \Miya\WP\Custom_Field
{
	public function admin_enqueue_scripts( $hook )
	{
		return $this->post_type;
	}

	public function form( $post, $args )
	{
		return $this->post_type;
	}

	public function save( $post_id )
	{

	}
}
