<?php
/**
 * Plugin Name: WP GraphQL Meta Query
 * Plugin URI: https://github.com/wp-graphql/wp-graphql-meta-query
 * Description: Meta_Query support for the WPGraphQL plugin. Requires WPGraphQL version 0.0.15 or newer.
 * Author: Digital First Media, Jason Bahl
 * Author URI: http://www.wpgraphql.com
 * Version: 0.0.2
 * Text Domain: wp-graphql-meta-query
 * Requires at least: 4.7.0
 * Tested up to: 4.7.1
 *
 * @package WPGraphQLMetaQuery
 * @category Core
 * @author Digital First Media, Jason Bahl
 * @version 0.0.5
 */
namespace WPGraphQL;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WPGraphQL\MetaQuery\Type\MetaQueryType;

class MetaQuery {

	/**
	 * This holds the MetaQuery input type object
	 * @var $meta_query
	 */
	private static $meta_query;

	private $types;

	/**
	 * MetaQuery constructor.
	 *
	 * This hooks the plugin into the WPGraphQL Plugin
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		/**
		 * Setup plugin constants
		 * @since 0.0.1
		 */
		$this->setup_constants();

		/**
		 * Included required files
		 * @since 0.0.1
		 */
		$this->includes();

		$post_types = \WPGraphQL::$allowed_post_types;

		if ( ! empty( $post_types ) && is_array( $post_types ) ) {

			foreach ( $post_types as $post_type ) {

				$post_type_object = get_post_type_object( $post_type );

				$this->types[] = $post_type_object->graphql_plural_name;

			}

		};

		/**
		 * Filter the query_args for the PostObjectQueryArgsType
		 * @since 0.0.1
		 */
		add_filter( 'graphql_input_fields', [ $this, 'add_input_fields' ], 10, 3 );

		/**
		 * Filter the $allowed_custom_args for the PostObjectsConnectionResolver to map the
		 * metaQuery input to WP_Query terms
		 * @since 0.0.1
		 */
		add_filter( 'graphql_map_input_fields_to_wp_query', [ $this, 'map_input_fields' ], 10, 2 );

	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 0.0.1
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'WPGRAPHQL_METAQUERY_VERSION' ) ) {
			define( 'WPGRAPHQL_METAQUERY_VERSION', '0.0.2' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'WPGRAPHQL_METAQUERY_PLUGIN_DIR' ) ) {
			define( 'WPGRAPHQL_METAQUERY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'WPGRAPHQL_METAQUERY_PLUGIN_URL' ) ) {
			define( 'WPGRAPHQL_METAQUERY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'WPGRAPHQL_METAQUERY_PLUGIN_FILE' ) ) {
			define( 'WPGRAPHQL_METAQUERY_PLUGIN_FILE', __FILE__ );
		}

	}

	/**
	 * Include required files.
	 *
	 * Uses composer's autoload
	 *
	 * @access private
	 * @since 0.0.1
	 * @return void
	 */
	private function includes() {
		// Autoload Required Classes
		require_once( WPGRAPHQL_METAQUERY_PLUGIN_DIR . 'vendor/autoload.php' );
	}

	/**
	 * add_input_fields
	 *
	 * This adds the metaQuery input fields
	 *
	 * @param array $fields
	 * @param string $type_name
	 * @param array $config
	 *
	 * @return mixed
	 * @since 0.0.1
	 */
	public function add_input_fields( $fields, $type_name, $config ) {
		if ( isset( $config['queryClass'] ) && 'WP_Query' === $config['queryClass'] ) {
			$fields['metaQuery'] = self::meta_query( $type_name );
		}
		return $fields;
	}

	/**
	 * map_input_fields
	 *
	 * This maps the metaQuery input fields to the WP_Query
	 *
	 * @param $query_args
	 * @param $input_args
	 *
	 * @return mixed
	 * @since 0.0.1
	 */
	public function map_input_fields( $query_args, $input_args ) {

		/**
		 * check to see if the metaQuery came through with the input $args, and
		 * map it properly to the $queryArgs that are returned and passed to the WP_Query
		 * @since 0.0.1
		 */
		$meta_query = null;
		if ( ! empty( $input_args['metaQuery'] ) ) {
			$meta_query = $input_args['metaQuery'];
			if ( ! empty( $meta_query['metaArray'] ) && is_array( $meta_query['metaArray'] ) ) {
				if ( 2 < count( $meta_query['metaArray'] ) ) {
					unset( $meta_query['relation'] );
				}
				foreach ( $meta_query['metaArray'] as $meta_query_key => $value ) {
					$meta_query[] = [
						$meta_query_key => $value,
					];
				}
			}
			unset( $meta_query['metaArray'] );

		}
		if ( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
		}

		/**
		 * Retrun the $query_args
		 * @since 0.0.1
		 */
		return $query_args;

	}

	/**
	 * meta_query
	 * This returns the definition for the MetaQueryType
	 * @param string $type_name
	 * @return MetaQueryType
	 * @since 0.0.1
	 */
	public static function meta_query( $type_name ) {
		return self::$meta_query[ $type_name ] ? : ( self::$meta_query[ $type_name ] = new MetaQueryType( $type_name ) );
	}

}

/**
 * Instantiate the MetaQuery class on graphql_init
 * @return MetaQuery
 */
function graphql_init_meta_query() {
	return new \WPGraphQL\MetaQuery();
}

add_action( 'graphql_init', '\WPGraphql\graphql_init_meta_query' );