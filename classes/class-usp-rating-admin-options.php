<?php

class USP_Rating_Admin_Options {

  public function __construct() {

	add_filter('usp_options', [$this, 'init_options']);

  }

  public function init_options($options) {

	$USP_Rating = USP_Rating::get_instance();

	$object_types = $USP_Rating->get_object_types();

	$options->add_box('rating', array(
		'title' => __('Rating settings', 'userspace-rating'),
		'icon' => 'fa-thumbs-up'
	));

	foreach ($object_types as $object_type) {

	  $options->box('rating')->add_group($object_type->get_id(), array(
		  'title' => __('Rating', 'userspace-rating') . ' ' . $object_type->get_name()
	  ))->add_options($this->object_type_options($object_type));
	}

	$options->box('rating')->add_group('general', array(
		'title' => __('Extends options', 'userspace-rating'),
		'extend' => true
	))->add_options($this->extend_options());

	return $options;

  }

  /**
   * 
   * @param object $object_type
   * 
   * @return array - options for object type
   */
  private function object_type_options($object_type) {

	$sub_options = [];

	$sub_options[] = $this->rating_type_option($object_type);
	$sub_options[] = $this->schema_option($object_type);
	$sub_options[] = $this->rating_overall_option($object_type);
	$sub_options[] = $this->rating_points_option($object_type);
	$sub_options[] = $this->rating_user_option($object_type);

	$options = array(
		array(
			'type' => 'select',
			'slug' => 'rating_' . $object_type->get_id(),
			'values' => array(__('Disabled', 'userspace-rating'), __('Enabled', 'userspace-rating')),
			'childrens' => array(
				1 => $sub_options
			)
		)
	);

	return $options;

  }

  private function rating_type_option($object_type) {

	/**
	 * TODO
	 * 
	 * Типы рейтинга получать через USP_Rating->get_rating_types() и уже их регистрировать
	 */
	return array(
		'type' => 'select',
		'slug' => 'rating_type_' . $object_type->get_id(),
		'title' => __('Type of rating for', 'userspace-rating') . ' ' . $object_type->get_name(),
		'values' => array(
			__('Plus/minus', 'userspace-rating'),
			__('I like', 'userspace-rating'),
			__('Stars', 'userspace-rating')
		),
		'childrens' => [
			2 => [
				[
					'type' => 'runner',
					'slug' => 'rating_item_amount_' . $object_type->get_id(),
					'title' => __('Number of stars', 'userspace-rating'),
					'value_min' => 1,
					'value_max' => 20,
					'default' => 5
				]
			]
		]
	);

  }

  private function schema_option($object_type) {

	return array(
		'type' => 'select',
		'slug' => 'rating_shema_' . $object_type->get_id(),
		'title' => __('Rating markup', 'userspace-rating'),
		'values' => array(
			__('Disable', 'userspace-rating'),
			__('Enable', 'userspace-rating')
		),
		'notice' => __('If enabled, the standard markup on single pages along with the rating is displayed as <a href="http://schema.org" target="_blank">http://schema.org</a>', 'userspace-rating')
	);

  }

  private function rating_overall_option($object_type) {

	return array(
		'type' => 'select',
		'slug' => 'rating_overall_' . $object_type->get_id(),
		'title' => __('Overall rating', 'userspace-rating') . ' ' . $object_type->get_name(),
		'values' => array(__('Sum of votes', 'userspace-rating'), __('Number of positive and negative votes', 'userspace-rating'))
	);

  }

  private function rating_points_option($object_type) {

	return array(
		'type' => 'text',
		'slug' => 'rating_points_' . $object_type->get_id(),
		'title' => __('Points for ranking', 'userspace-rating') . ' ' . $object_type->get_name(),
		'notice' => __('set how many points will be awarded for a positive or negative vote for the publication', 'userspace-rating')
	);

  }

  private function rating_user_option($object_type) {

	/**
	 * TODO
	 * 
	 * В типе объекта рейтинга добавить методы для получения дефолтного шаблона вывода истории рейтинга
	 * и подсказки для отображения доступных переменных при формировании шаблона вывода истори рейтинга
	 */
	return array(
		'type' => 'select',
		'slug' => 'rating_user_' . $object_type->get_id(),
		'title' => sprintf(__('The influence of rating %s on the overall rating', 'userspace-rating'), $object_type->get_name()),
		'values' => array(__('No', 'userspace-rating'), __('Yes', 'userspace-rating')),
		'childrens' => array(
			1 => array(
				array(
					'type' => 'text',
					'slug' => 'rating_temp_' . $object_type->get_id(),
					'title' => __('Template of history output in the overall ranking', 'userspace-rating'),
					'default' => '%DATE% %USER% ' . __('has voted', 'userspace-rating') . ': %VALUE%',
					'notice' => ''
				)
			)
		)
	);

  }

  private function extend_options() {

	return array(
		array(
			'type' => 'select',
			'slug' => 'rating_results_can',
			'title' => __('View results', 'userspace-rating'),
			'values' => array(
				0 => __('All users', 'userspace-rating'),
				1 => __('Participants and higher', 'userspace-rating'),
				2 => __('Authors and higher', 'userspace-rating'),
				7 => __('Editors and higher', 'userspace-rating'),
				10 => __('only Administrators', 'userspace-rating')
			),
			'notice' => __('specify the user group which is allowed to view votes', 'userspace-rating')
		),
		array(
			'type' => 'select',
			'slug' => 'rating_delete_voice',
			'title' => __('Delete your vote', 'userspace-rating'),
			'values' => array(__('No', 'userspace-rating'), __('Yes', 'userspace-rating'))
		),
		array(
			'type' => 'select',
			'slug' => 'rating_custom',
			'title' => __('Tab "Other"', 'userspace-rating'),
			'values' => array(
				__('Disable', 'userspace-rating'),
				__('Enable', 'userspace-rating')
			),
			'notice' => __('If enabled, an additional "Other" tab will be created in the rating history, where all changes will be displayed via unregistered rating types', 'userspace-rating')
		)
	);

  }

}
