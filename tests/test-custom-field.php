<?php

class Custom_Field_Tests extends WP_UnitTestCase
{
	/**
	 * A test that propaties should have values.
	 */
	function test_propaties_should_have_values()
	{
		$id = self::get_property( 'id' );
		$obj = new Test( '1234', 'Hello World' );

		$this->assertSame( '1234', $id->getValue( $obj ) );

		$title = self::get_property( 'title' );
		$obj = new Test( '1234', 'Hello World' );

		$this->assertSame( 'Hello World', $title->getValue( $obj ) );

		$context = self::get_property( 'context' );
		$obj = new Test( '1234', 'Hello World', array( 'hello' ) );

		$this->assertSame( 'advanced', $context->getValue( $obj ) );

		$priority = self::get_property( 'priority' );
		$obj = new Test( '1234', 'Hello World', array( 'hello' ) );

		$this->assertSame( 'default', $priority->getValue( $obj ) );

		$options = self::get_property( 'options' );
		$obj = new Test( '1234', 'Hello World', array(
			'context' => 'context value',
			'priority' => 'priority value'
		) );

		$this->assertSame( array(
			'context' => 'context value',
			'priority' => 'priority value'
		), $options->getValue( $obj ) );

		$context = self::get_property( 'context' );
		$obj = new Test( '1234', 'Hello World', array(
			'context' => 'context value',
			'priority' => 'priority value'
		) );

		$this->assertSame( 'context value', $context->getValue( $obj ) );

		$priority = self::get_property( 'priority' );
		$obj = new Test( '1234', 'Hello World', array(
			'context' => 'context value',
			'priority' => 'priority value'
		) );

		$this->assertSame( 'priority value', $priority->getValue( $obj ) );
	}

	/**
	 * A test that callback function of the action hook should be registered.
	 */
	function test_action_should_be_registered()
	{
		$test = new Test( 'hello', 'Hello' );
		$test->add( 'post' );

		$this->assertSame( 10, has_action( 'admin_enqueue_scripts', array( $test, 'admin_enqueue_scripts' ) ) );
		$this->assertSame( 10, has_action( 'add_meta_boxes', array( $test, 'add_meta_boxes' ) ) );
		$this->assertSame( 10, has_action( 'save_post', array( $test, 'save_post' ) ) );
	}

	/**
	 * A test style and js should be loaded.
	 */
	function test_admin_enqueue_scripts() {
		$test = new Test( 'hello', 'Hello' );
		$test->add( 'post' );

		do_action( 'admin_enqueue_scripts' );
		$this->assertTrue( wp_style_is( 'test-css' ) );
		$this->assertTrue( wp_script_is( 'test-script' ) );
	}

	/**
	 * A test metabox should be added.
	 */
	function test_meta_box_should_be_added() {
		global $wp_meta_boxes;

		$test = new Test( 'hello', 'Hello' );
		$test->add( 'post' );

		do_action( 'add_meta_boxes' );

		$metaboxes = $wp_meta_boxes['post']['advanced']['default'];
		$this->assertArrayHasKey( 'hello', $metaboxes );
		$this->assertSame( 'hello', $metaboxes['hello']['id'] );
		$this->assertSame( 'Hello', $metaboxes['hello']['title'] );
		$this->assertSame( $test, $metaboxes['hello']['callback'][0] );
		$this->assertSame( 'meta_box_callback', $metaboxes['hello']['callback'][1] );
	}

	/**
	 * A test metabox should be added.
	 */
	function test_meta_box_should_be_added_to_cpt() {
		global $wp_meta_boxes;

		$test = new Test( 'hello', 'Hello' );
		$test->add( 'my_custom_post_type' );

		do_action( 'add_meta_boxes' );

		$metaboxes = $wp_meta_boxes['my_custom_post_type']['advanced']['default'];
		$this->assertArrayHasKey( 'hello', $metaboxes );
		$this->assertSame( 'hello', $metaboxes['hello']['id'] );
		$this->assertSame( 'Hello', $metaboxes['hello']['title'] );
		$this->assertSame( $test, $metaboxes['hello']['callback'][0] );
		$this->assertSame( 'meta_box_callback', $metaboxes['hello']['callback'][1] );
	}

	/**
	 * A test metabox should be added.
	 */
	function test_meta_box_should_be_added_to_cpt_to_side() {
		global $wp_meta_boxes;

		$test = new Test( 'hello', 'Hello', array( 'context' => 'side' ) );
		$test->add( 'my_custom_post_type' );

		do_action( 'add_meta_boxes' );

		$metaboxes = $wp_meta_boxes['my_custom_post_type']['side']['default'];
		$this->assertArrayHasKey( 'hello', $metaboxes );
		$this->assertSame( 'hello', $metaboxes['hello']['id'] );
		$this->assertSame( 'Hello', $metaboxes['hello']['title'] );
		$this->assertSame( $test, $metaboxes['hello']['callback'][0] );
		$this->assertSame( 'meta_box_callback', $metaboxes['hello']['callback'][1] );
	}

	/**
	 * A test metabox should be added.
	 */
	function test_meta_box_should_be_added_to_cpt_to_high_priority() {
		global $wp_meta_boxes;

		$test = new Test( 'hello', 'Hello', array( 'context' => 'side', 'priority' => 'high' ) );
		$test->add( 'my_custom_post_type' );

		do_action( 'add_meta_boxes' );

		$metaboxes = $wp_meta_boxes['my_custom_post_type']['side']['high'];
		$this->assertArrayHasKey( 'hello', $metaboxes );
		$this->assertSame( 'hello', $metaboxes['hello']['id'] );
		$this->assertSame( 'Hello', $metaboxes['hello']['title'] );
		$this->assertSame( $test, $metaboxes['hello']['callback'][0] );
		$this->assertSame( 'meta_box_callback', $metaboxes['hello']['callback'][1] );
	}

	/**
	 * A test `form()` should be called.
	 */
	function test_form_should_be_called() {
		global $wp_meta_boxes;

		$test = new Test( 'hello', 'Hello', array( 'context' => 'side', 'priority' => 'high' ) );
		$test->add( 'my_custom_post_type' );

		do_action( 'add_meta_boxes' );

		$metaboxes = $wp_meta_boxes['my_custom_post_type']['side']['high'];
		$this->assertSame( $test, $metaboxes['hello']['callback'][0] );
		$this->assertSame( 'meta_box_callback', $metaboxes['hello']['callback'][1] );

		$post = $this->factory->post->create( array( 'post_title' => 'Test Post' ) );

		ob_start();
		call_user_func( array( $metaboxes['hello']['callback'][0], $metaboxes['hello']['callback'][1] ), $post, array() );
		$res = ob_get_contents();
		ob_end_clean();
		$this->assertRegExp( '#<input type="text">#', $res );
		$nonce = wp_create_nonce( 'hello' );
		$this->assertRegExp( '#<input type="hidden" id="hello" name="hello" value="'.$nonce.'" />#', $res );
	}

	protected static function get_property( $name )
	{
		$class = new ReflectionClass( 'Test' );

		$property = $class->getProperty( $name );
		$property->setAccessible( true );

		return $property;
	}
}

/**
 * This is a example extended class for phpunit
 */
class Test extends \Miya\WP\Custom_Field
{
	public function admin_enqueue_scripts( $hook )
	{
		wp_enqueue_style( 'test-css', 'path/to/style.css' );
		wp_enqueue_script( 'test-script', 'path/to/script.js' );
	}

	public function form( $post, $args )
	{
		echo '<input type="text">';
	}

	public function save( $post_id )
	{

	}
}
