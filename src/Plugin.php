<?php
/**
 * Plugin
 *
 * @since   1.0.0
 * @link    {URL}
 * @license GPLv2 or later
 * @package Emoji
 * @author  {AUTHOR}
 */

namespace Emoji;

/**
 * Class Plugin
 *
 * @package Emoji
 */
class Plugin {

	/**
	 * Plugin slug
	 */
	const SLUG = 'emoji';
	/**
	 * Plugin version
	 */
	const VERSION = '1.0.0';

	/**
	 * DIC
	 *
	 * @var \Emoji\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder
	 */
	private $container_builder;

	/**
	 * Plugin constructor.
	 *
	 * @param \Emoji\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder $container_builder DIC.
	 */
	public function __construct( $container_builder ) {
		$this->container_builder = $container_builder;
	}

	/**
	 * Run plugin
	 *
	 * @throws \Exception Invalid service name.
	 */
	public function run() {
		( $this->container_builder->get( 'front' ) )->hooks();
		( $this->container_builder->get( 'admin' ) )->hooks();
		( $this->container_builder->get( 'shortcode' ) )->register();
	}

	/**
	 * Get plugin service.
	 *
	 * @param string $service_name Service name. You can see it in the dependencies/services.php file.
	 *
	 * @throws \Exception Invalid service name.
	 */
	public function get_service( $service_name ) {
		$this->container_builder->get( $service_name );
	}

}