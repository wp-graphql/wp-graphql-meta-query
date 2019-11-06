<?php
/**
 * Plugin Name: WPGraphQL Meta Query
 * Plugin URI: https://github.com/wp-graphql/wp-graphql-meta-query
 * Description: Adds Meta Query support for the WPGraphQL plugin. Requires WPGraphQL version 0.0.23
 * or newer.
 * Author: Jason Bahl
 * Author URI: http://www.wpgraphql.com
 * Version: 0.1.1
 * Text Domain: wp-graphql-meta-query
 * Requires at least: 4.7.0
 * Tested up to: 4.7.1
 *
 * @package  WPGraphQLMetaQuery
 * @category WPGraphQL
 * @author   Jason Bahl
 * @version  0.1.1
 */

namespace WPGraphQL;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WPGraphQL\Registry\TypeRegistry;

class MetaQuery {

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
		 *
		 * @since 0.0.1
		 */
		$this->setup_constants();

		/**
		 * Included required files
		 *
		 * @since 0.0.1
		 */
		$this->includes();

		/**
		 * Filter the query_args for the PostObjectQueryArgsType
		 *
		 * @since 0.0.1
		 */
		add_filter( 'graphql_input_fields', [ $this, 'add_input_fields' ], 10, 4 );

		/**
		 * Filter the $allowed_custom_args for the PostObjectsConnectionResolver to map the
		 * metaQuery input to WP_Query terms
		 *
		 * @since 0.0.1
		 */
		add_filter( 'graphql_map_input_fields_to_wp_query', [ $this, 'map_input_fields' ], 10, 2 );

	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since  0.0.1
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'WPGRAPHQL_METAQUERY_VERSION' ) ) {
			define( 'WPGRAPHQL_METAQUERY_VERSION', '0.1.1' );
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

		// Whether to autoload the files or not
		if ( ! defined( 'WPGRAPHQL_METAQUERY_AUTOLOAD' ) ) {
			define( 'WPGRAPHQL_METAQUERY_AUTOLOAD', true );
		}

	}

	/**
	 * Include required files.
	 *
	 * Uses composer's autoload
	 *
	 * @access private
	 * @since  0.0.1
	 * @return void
	 */
	private function includes() {

		// Autoload Required Classes
		if ( defined( 'WPGRAPHQL_METAQUERY_AUTOLOAD' ) && true == WPGRAPHQL_METAQUERY_AUTOLOAD ) {
			require_once( WPGRAPHQL_METAQUERY_PLUGIN_DIR . 'vendor/autoload.php' );
		}

	}

	/**
	 * add_input_fields
	 *
	 * This adds the metaQuery input fields
	 *
	 * @param array        $fields
	 * @param string       $type_name
	 * @param array        $config
	 * @param TypeRegistry $type_registry
	 *
	 * @return mixed
	 * @since 0.0.1
	 * @throws \Exception
	 */
	public function add_input_fields( $fields, $type_name, $config, $type_registry ) {
		if ( isset( $config['queryClass'] ) && 'WP_Query' === $config['queryClass'] ) {
			$this->register_types( $type_name, $type_registry );
			$fields['metaQuery'] = [
				'type' => $type_name . 'MetaQuery',
			];
		}

		return $fields;
	}

	/**
	 * @param              $type_name
	 * @param TypeRegistry $type_registry
	 *
	 * @throws \Exception
	 */
	public function register_types( $type_name, TypeRegistry $type_registry ) {

		$type_registry->register_enum_type( $type_name . 'MetaTypeEnum', [
			'values' => [
				'NUMERIC' => [
					'name'  => 'NUMERIC',
					'value' => 'NUMERIC',
				],
				'BINARY' => [
					'name'  => 'BINARY',
					'value' => 'BINARY',
				],
				'CHAR' => [
					'name'  => 'CHAR',
					'value' => 'CHAR',
				],
				'DATE' => [
					'name'  => 'DATE',
					'value' => 'DATE',
				],
				'DATETIME' => [
					'name'  => 'DATETIME',
					'value' => 'DATETIME',
				],
				'DECIMAL' => [
					'name'  => 'DECIMAL',
					'value' => 'DECIMAL',
				],
				'SIGNED' => [
					'name'  => 'SIGNED',
					'value' => 'SIGNED',
				],
				'TIME' => [
					'name'  => 'TIME',
					'value' => 'TIME',
				],
				'UNSIGNED' => [
					'name'  => 'UNSIGNED',
					'value' => 'UNSIGNED',
				],
			]
		] );

		$type_registry->register_enum_type( $type_name . 'MetaCompareEnum', [
			'values' => [
				'EQUAL_TO'                 => [
					'name'  => 'EQUAL_TO',
					'value' => '=',
				],
				'NOT_EQUAL_TO'             => [
					'name'  => 'NOT_EQUAL_TO',
					'value' => '!=',
				],
				'GREATER_THAN'             => [
					'name'  => 'GREATER_THAN',
					'value' => '>',
				],
				'GREATER_THAN_OR_EQUAL_TO' => [
					'name'  => 'GREATER_THAN_OR_EQUAL_TO',
					'value' => '>=',
				],
				'LESS_THAN'                => [
					'name'  => 'LESS_THAN',
					'value' => '<',
				],
				'LESS_THAN_OR_EQUAL_TO'    => [
					'name'  => 'LESS_THAN_OR_EQUAL_TO',
					'value' => '<=',
				],
				'LIKE'                     => [
					'name'  => 'LIKE',
					'value' => 'LIKE',
				],
				'NOT_LIKE'                 => [
					'name'  => 'NOT_LIKE',
					'value' => 'NOT LIKE',
				],
				'IN'                       => [
					'name'  => 'IN',
					'value' => 'IN',
				],
				'NOT_IN'                   => [
					'name'  => 'NOT_IN',
					'value' => 'NOT IN',
				],
				'BETWEEN'                  => [
					'name'  => 'BETWEEN',
					'value' => 'BETWEEN',
				],
				'NOT_BETWEEN'              => [
					'name'  => 'NOT_BETWEEN',
					'value' => 'NOT BETWEEN',
				],
				'EXISTS'                   => [
					'name'  => 'EXISTS',
					'value' => 'EXISTS',
				],
				'NOT_EXISTS'               => [
					'name'  => 'NOT_EXISTS',
					'value' => 'NOT EXISTS',
				],
			]
		] );

		$type_registry->register_input_type( $type_name . 'MetaArray', [
			'fields' => [
				'key'     => [
					'type'        => 'String',
					'description' => __( 'Custom field key', 'wp-graphql' ),
				],
				'value'   => [
					'type'        => 'String',
					'description' => __( 'Custom field value', 'wp-graphql' ),
				],
				'compare' => [
					'type'        => $type_name . 'MetaCompareEnum',
					'description' => __( 'Custom field value', 'wp-graphql' ),
				],
				'type'    => [
					'type'        => $type_name . 'MetaTypeEnum',
					'description' => __( 'Custom field value', 'wp-graphql' ),
				],
			]
		] );

		$type_registry->register_input_type( $type_name . 'MetaQuery', [
			'fields' => [
				'relation'  => [
					'type' => 'RelationEnum',
				],
				'metaArray' => [
					'type' => [
						'list_of' => $type_name . 'MetaArray',
					],
				],
			]
		] );

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
		 *
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
		 *
		 * @since 0.0.1
		 */
		return $query_args;

	}

}

/**
 * Instantiate the MetaQuery class on graphql_init
 *
 * @return MetaQuery
 */
function graphql_init_meta_query() {
	return new MetaQuery();
}

add_action( 'graphql_init', '\WPGraphql\graphql_init_meta_query' );
