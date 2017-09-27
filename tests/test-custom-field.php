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

		$priority = self::get_property( 'post_type' );
		$obj = new Test( '1234', 'Hello World' );
		$obj->add( 'page' );

		$this->assertSame( 'page', $priority->getValue( $obj ) );

		$priority = self::get_property( 'post_type' );
		$obj = new Test( '1234', 'Hello World' );
		$obj->add( array( 'post', 'page' ) );

		$this->assertSame( array( 'post', 'page' ), $priority->getValue( $obj ) );
	}

	/**
	 * A test that callback function of the action hook should be registered.
	 */
	function test_action_should_be_registered()
	{
		$test = new Test( 'hello', 'Hello' );
		$test->add( 'post' );

		$this->assertSame( 10, has_action( 'admin_enqueue_scripts', array( $test, '_admin_enqueue_scripts' ) ) );
		$this->assertSame( 10, has_action( 'add_meta_boxes', array( $test, 'add_meta_boxes' ) ) );
		$this->assertSame( 10, has_action( 'save_post', array( $test, 'save_post' ) ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public
	function test_ccs_and_js_should_be_loaded()
	{
		$map = new Test( 'map', 'Map' );
		$map->add( 'page' );

		$GLOBALS['current_screen'] = convert_to_screen( 'page' );
		do_action( 'admin_enqueue_scripts', 'post-new.php' );
		$this->assertTrue( wp_style_is( 'test-css' ) );
		$this->assertTrue( wp_script_is( 'test-script' ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_test_ccs_and_js_should_be_loaded_on_multiple_post_type() {
		$map = new Test( 'map', 'Map' );
		$map->add( array( 'page', 'post' ) );

		$GLOBALS['current_screen'] = convert_to_screen( 'page' );
		do_action( 'admin_enqueue_scripts', 'post-new.php' );
		$this->assertTrue( wp_style_is( 'test-css' ) );
		$this->assertTrue( wp_script_is( 'test-script' ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_test_ccs_and_js_should_not_be_loaded() {
		$map = new Test( 'map', 'Map' );
		$map->add( array( 'post' ) );

		$GLOBALS['current_screen'] = convert_to_screen( 'page' );
		do_action( 'admin_enqueue_scripts', 'post-new.php' );
		$this->assertFalse( wp_style_is( 'test-css' ) );
		$this->assertFalse( wp_script_is( 'test-script' ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_test_ccs_and_js_should_be_loaded_with_cpt() {
		register_post_type( 'my-post-type' );

		$map = new Test( 'map', 'Map' );
		$map->add( array( 'my-post-type' ) );

		$GLOBALS['current_screen'] = convert_to_screen( 'my-post-type' );
		do_action( 'admin_enqueue_scripts', 'post-new.php' );
		$this->assertTrue( wp_style_is( 'test-css' ) );
		$this->assertTrue( wp_script_is( 'test-script' ) );

		// $GLOBALS['current_screen'] = convert_to_screen( 'page' );
		// do_action( 'admin_enqueue_scripts', 'post-new.php' );
		// $this->assertFalse( wp_style_is( 'test-css' ) );
		// $this->assertFalse( wp_script_is( 'test-script' ) );
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function test_test_ccs_and_js_should_not_be_loaded_with_cpt() {
		register_post_type( 'my-post-type' );

		$map = new Test( 'map', 'Map' );
		$map->add( array( 'my-post-type' ) );

		$GLOBALS['current_screen'] = convert_to_screen( 'post' );
		do_action( 'admin_enqueue_scripts', 'post-new.php' );
		$this->assertFalse( wp_style_is( 'test-css' ) );
		$this->assertFalse( wp_script_is( 'test-script' ) );
	}

	/**
	 * A test metabox should be added.
	 */
	function test_meta_box_should_be_added()
	{
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
	function test_meta_box_should_be_added_to_cpt()
	{
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
	function test_meta_box_should_be_added_to_cpt_to_side()
	{
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
	function test_meta_box_should_be_added_to_cpt_to_high_priority()
	{
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
	function test_form_should_be_called()
	{
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
		$nonce = wp_create_nonce( '_hello' );
		$this->assertRegExp( '#<input type="hidden" id="_hello" name="_hello" value="'.$nonce.'" />#', $res );
	}

	function test_meta_box_should_be_added_to_multiple_cpt() {
		global $wp_meta_boxes;

		$test = new Test( 'hello', 'Hello', array( 'context' => 'side', 'priority' => 'high' ) );
		$test->add( array( 'my_custom_post_type1', 'my_custom_post_type2' ) );

		do_action( 'add_meta_boxes' );

		$this->assertArrayHasKey( 'my_custom_post_type1', $wp_meta_boxes );
		$this->assertArrayHasKey( 'my_custom_post_type2', $wp_meta_boxes );
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
