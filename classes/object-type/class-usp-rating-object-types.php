<?php

final class USP_Rating_Object_Types {

	/**
	 * Array of instances with all registered object types
	 */
	protected $types = [];

	public function __construct() {

		do_action( 'usp_rating_object_types', $this );

	}

	/**
	 * Register new object type
	 *
	 * @param object $object_type must extends \USP_Rating_Object_Type_Abstract
	 *
	 * @return bool
	 */
	public function add( $object_type ) {

		if ( $object_type instanceof USP_Rating_Object_Type_Abstract ) {

			if ( $this->get( $object_type->get_id() ) === false ) {

				$this->types[] = $object_type;
			}

			return true;
		}

		return false;

	}

	/**
	 * Get object type instance by id
	 *
	 * @param string $id
	 *
	 * return object|false - instance of object type or false
	 */
	public function get( $id = '' ) {

		if ( ! $id ) {
			return false;
		}

		foreach ( $this->types as $object_type ) {

			if ( $object_type->get_id() == $id ) {

				return $object_type;
			}
		}

		return false;

	}

	/**
	 * Get all object types
	 *
	 * @return array - all object types
	 */
	public function get_all() {

		return $this->types;

	}

	/**
	 * Remove registered object type by id
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function remove( $id ) {

		if ( ! $id ) {
			return false;
		}

		foreach ( $this->types as $key => $object_type ) {

			if ( $object_type->get_id() == $id ) {

				unset( $this->types[ $key ] );

				return true;
			}
		}

		return false;

	}

}
