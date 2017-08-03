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

	private static $meta_compare_enum;
	private static $meta_type;
	protected static $fields;

	/**
	 * MetaQueryType constructor.
	 *
	 * Creates the metaQuery input field which is used to query post objects via postmeta parameters
	 *
	 * @since 0.0.5
	 */
	public function __construct() {
		$config = [
			'name'   => 'metaQuery',
			'fields' => self::fields(),
		];
		parent::__construct( $config );
	}

	protected static function fields() {

		if ( null === self::$fields ) :
			self::$fields = [
				'relation'  => [
					'type' => Types::relation_enum(),
				],
				'metaArray' => Types::list_of(
					new WPInputObjectType( [
						'name'   => 'metaArray',
						'fields' => function() {
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
									'type'        => self::meta_compare_enum(),
									'description' => __( 'Custom field value', 'wp-graphql' ),
								],
								'type'    => [
									'type'        => self::meta_type_enum(),
									'description' => __( 'Custom field value', 'wp-graphql' ),
								],
							];
							return $fields;
						},
					] )
				),
			];
		endif;
		return ! empty( self::$fields ) ? self::$fields : null;

	}

	/**
	 * meta_compare_enum
	 *
	 * Creates the metaCompare enum field
	 *
	 * @return EnumType
	 * @since 0.0.5
	 */
	private static function meta_compare_enum() {
		if ( null === self::$meta_compare_enum ) {
			self::$meta_compare_enum = new WPEnumType( [
				'name'   => 'metaCompare',
				'values' => [
					[
						'name'  => 'EQUAL_TO',
						'value' => '=',
					],
					[
						'name'  => 'NOT_EQUAL_TO',
						'value' => '!=',
					],
					[
						'name'  => 'GREATER_THAN',
						'value' => '>',
					],
					[
						'name'  => 'GREATER_THAN_OR_EQUAL_TO',
						'value' => '>=',
					],
					[
						'name'  => 'LESS_THAN',
						'value' => '<',
					],
					[
						'name'  => 'LESS_THAN_OR_EQUAL_TO',
						'value' => '<=',
					],
					[
						'name'  => 'LIKE',
						'value' => 'LIKE',
					],
					[
						'name'  => 'NOT_LIKE',
						'value' => 'NOT LIKE',
					],
					[
						'name'  => 'IN',
						'value' => 'IN',
					],
					[
						'name'  => 'NOT_IN',
						'value' => 'NOT IN',
					],
					[
						'name'  => 'BETWEEN',
						'value' => 'BETWEEN',
					],
					[
						'name'  => 'NOT_BETWEEN',
						'value' => 'NOT BETWEEN',
					],
					[
						'name'  => 'EXISTS',
						'value' => 'EXISTS',
					],
					[
						'name'  => 'NOT_EXISTS',
						'value' => 'NOT EXISTS',
					],
				],
			] );
		} // End if().
		return ! empty( self::$meta_compare_enum ) ? self::$meta_compare_enum : null;
	}

	/**
	 * meta_type_enum
	 *
	 * Creates the metaType enum field
	 *
	 * @return EnumType|null
	 * @since 0.0.5
	 */
	private static function meta_type_enum() {
		if ( null === self::$meta_type ) {
			self::$meta_type = new WPEnumType( [
				'name'   => 'metaType',
				'values' => [
					[
						'name'  => 'NUMERIC',
						'value' => 'NUMERIC',
					],
					[
						'name'  => 'BINARY',
						'value' => 'BINARY',
					],
					[
						'name'  => 'CHAR',
						'value' => 'CHAR',
					],
					[
						'name'  => 'DATE',
						'value' => 'DATE',
					],
					[
						'name'  => 'DATETIME',
						'value' => 'DATETIME',
					],
					[
						'name'  => 'DECIMAL',
						'value' => 'DECIMAL',
					],
					[
						'name'  => 'SIGNED',
						'value' => 'SIGNED',
					],
					[
						'name'  => 'TIME',
						'value' => 'TIME',
					],
					[
						'name'  => 'UNSIGNED',
						'value' => 'UNSIGNED',
					],
				],
			] );
		} // End if().
		return ! empty( self::$meta_type ) ? self::$meta_type : null;
	}

}
