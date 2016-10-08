<?php
namespace ataylorme\CMB2_additional_fields;
/**
 * Plugin Name: CMB2 Additional Fields
 * Author Name: Andrew Taylor
 * Plugin URL: https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress/wiki/Adding-your-own-field-types
 */


/**
 * CMB2 HTML5 Number Field
 *
 * @param $field_args
 * @param $escaped_value
 * @param $object_id
 * @param $object_type
 * @param $field_type_object
 */
function render_text_number( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
	echo $field_type_object->input( array( 'class' => 'cmb2_text_small', 'type' => 'number' ) );
}

add_action( 'cmb2_render_text_number', __NAMESPACE__ . '\render_text_number', 10, 5 );

/**
 * CMB2 Validate HTML5 Number Field
 *
 * @param $override
 * @param $new
 *
 * @return float|string
 */
function validate_text_number( $override, $new ) {
	$new = round( preg_replace( '/[^0-9\.]/', '', $new ), 2 );

	return ( 0 == $new ) ? '' : $new;
}

add_filter( 'cmb2_validate_text_number', __NAMESPACE__ . '\validate_text_number', 10, 2 );

/**
 * CMB2 Time Duration Field
 *
 * @param $field_args
 * @param $value
 * @param $object_id
 * @param $object_type
 * @param $field_type_object
 */
function cmb2_render_time_duration_field( $field_args, $value, $object_id, $object_type, $field_type_object ) {

	// Default values
	$value = wp_parse_args( $value, array(
		'hours'   => 0,
		'minutes' => 0,
	) );

	$hour_options = '';
	for ( $i = 0; $i <= 12; $i ++ ) {
		$hour_options .= '<option value="' . $i . '" ' . selected( $value['hours'], $i, false ) . '>' . $i . '</option>';
	}
	?>
	<div class="alignleft" style="margin-right: 1em;">
		<label for="<?php echo $field_type_object->_id( '_hours' ); ?>'">
			<?php _e( 'Hours', 'ataylorme' ); ?>:&nbsp;
		</label>
		<?php echo $field_type_object->select( array(
			'name'    => $field_type_object->_name( '[hours]' ),
			'id'      => $field_type_object->_id( '_hours' ),
			'desc'    => '',
			'options' => $hour_options,
		) ); ?>
	</div>

	<?php
	$minute_options = '';
	for ( $i = 0; $i <= 55; $i = $i + 5 ) {
		$minute_options .= '<option value="' . $i . '" ' . selected( $value['minutes'], $i, false ) . '>' . $i . '</option>';
	}
	?>
	<div class="alignleft">
		<label for="<?php echo $field_type_object->_id( '_minutes' ); ?>'">
			<?php _e( 'Minutes', 'ataylorme' ); ?>:&nbsp;
		</label>
		<?php echo $field_type_object->select( array(
			'name'    => $field_type_object->_name( '[minutes]' ),
			'id'      => $field_type_object->_id( '_minutes' ),
			'desc'    => '',
			'options' => $minute_options,
		) ); ?>
	</div>
	<div style="clear:both;">
		<?php echo $field_type_object->_desc( true ); ?>
	</div>
	<?php

}

add_action( 'cmb2_render_time_duration', __NAMESPACE__ . '\cmb2_render_time_duration_field', 10, 5 );