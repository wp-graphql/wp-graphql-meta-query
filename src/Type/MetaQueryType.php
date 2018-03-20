<?php
namespace WPGraphQL\MetaQuery\Type;

use GraphQL\Type\Definition\EnumType;
use WPGraphQL\Type\WPEnumType;
use WPGraphQL\Type\WPInputObjectType;
use WPGraphQL\Types;

/**
 * Class MetaQueryType
 *
 * This sets up the input type to allow for MetaQueries.
 *
 * @package WPGraphQL\Type
 */
class MetaQueryType extends WPInputObjectType {

	/**
	 * The meta compare enum definitions
	 * @var array
	 */
	private static $meta_compare_enum = [];

	/**
	 * The meta type definitions
	 * @var array
	 */
	private static $meta_type = [];

	/**
	 * The fields definition
	 * @var array
	 */
	protected static $fields;

	/**
	 * MetaQueryType constructor.
	 *
	 * Creates the metaQuery input field which is used to query post objects via postmeta parameters
	 *
	 * @since 0.0.5
	 */
	public function __construct( $type_name ) {
		$config = [
			'name'   => $type_name . 'MetaQuery',
			'fields' => self::fields( $type_name ),
		];
		parent::__construct( $config );
	}

	/**
	 * Defines the fields for the type
	 * @param string $type_name
	 *
	 * @return mixed|null
	 */
	protected static function fields( $type_name ) {

		if ( empty( self::$fields[ $type_name ] ) ) :
			self::$fields[ $type_name ] = [
				'relation'  => [
					'type' => Types::relation_enum(),
				],
				'metaArray' => Types::list_of(
					new WPInputObjectType( [
						'name'   => $type_name . 'MetaArray',
						'fields' => function() use ( $type_name ) {
							$fields = [
								'key'     => [
									'type'        => Types::string(),
									'description' => __( 'Custom field key', 'wp-graphql' ),
								],
								'value'   => [
									'type'        => Types::string(),
									'description' => __( 'Custom field value', 'wp-graphql' ),
								],
								'compare' => [
									'type'        => self::meta_compare_enum( $type_name ),
									'description' => __( 'Custom field value', 'wp-graphql' ),
								],
								'type'    => [
									'type'        => self::meta_type_enum( $type_name ),
									'description' => __( 'Custom field value', 'wp-graphql' ),
								],
							];
							return $fields;
						},
					] )
				),
			];
		endif;
		return ! empty( self::$fields[ $type_name ] ) ? self::$fields[ $type_name ] : null;

	}

	/**
	 * meta_compare_enum
	 *
	 * Creates the metaCompare enum field
	 *
	 * @return EnumType
	 * @since 0.0.5
	 */
	private static function meta_compare_enum( $type_name ) {
		if ( empty( self::$meta_compare_enum[ $type_name ] ) ) {
			self::$meta_compare_enum[ $type_name ] = new WPEnumType( [
				'name'   => $type_name . 'MetaCompare',
				'values' => [
					'EQUAL_TO' => [
						'name'  => 'EQUAL_TO',
						'value' => '=',
					],
					'NOT_EQUAL_TO' => [
						'name'  => 'NOT_EQUAL_TO',
						'value' => '!=',
					],
					'GREATER_THAN' => [
						'name'  => 'GREATER_THAN',
						'value' => '>',
					],
					'GREATER_THAN_OR_EQUAL_TO' => [
						'name'  => 'GREATER_THAN_OR_EQUAL_TO',
						'value' => '>=',
					],
					'LESS_THAN' => [
						'name'  => 'LESS_THAN',
						'value' => '<',
					],
					'LESS_THAN_OR_EQUAL_TO' => [
						'name'  => 'LESS_THAN_OR_EQUAL_TO',
						'value' => '<=',
					],
					'LIKE' => [
						'name'  => 'LIKE',
						'value' => 'LIKE',
					],
					'NOT_LIKE' => [
						'name'  => 'NOT_LIKE',
						'value' => 'NOT LIKE',
					],
					'IN' => [
						'name'  => 'IN',
						'value' => 'IN',
					],
					'NOT_IN' => [
						'name'  => 'NOT_IN',
						'value' => 'NOT IN',
					],
					'BETWEEN' => [
						'name'  => 'BETWEEN',
						'value' => 'BETWEEN',
					],
					'NOT_BETWEEN' => [
						'name'  => 'NOT_BETWEEN',
						'value' => 'NOT BETWEEN',
					],
					'EXISTS' => [
						'name'  => 'EXISTS',
						'value' => 'EXISTS',
					],
					'NOT_EXISTS' => [
						'name'  => 'NOT_EXISTS',
						'value' => 'NOT EXISTS',
					],
				],
			] );
		} // End if().
		return ! empty( self::$meta_compare_enum[ $type_name ] ) ? self::$meta_compare_enum[ $type_name ] : null;
	}

	/**
	 * meta_type_enum
	 *
	 * Creates the metaType enum field
	 *
	 * @return EnumType|null
	 * @since 0.0.5
	 */
	private static function meta_type_enum( $type_name ) {
		if ( empty( self::$meta_type[ $type_name ] ) ) {
			self::$meta_type[ $type_name ] = new WPEnumType( [
				'name'   => $type_name . 'MetaType',
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
				],
			] );
		} // End if().
		return ! empty( self::$meta_type[ $type_name ] ) ? self::$meta_type[ $type_name ] : null;
	}

}
