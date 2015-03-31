<?php
/**
 * Plugin Name: Add Pinterest Follow Button
 * Version: 0.1
 * Description: Pinterest follow button plugin give an ability to maximise your followers of Pinterest account.
 * Author: Weblizar
 * Author URI: http://weblizar.com/plugins/
 * Plugin URI: http://weblizar.com/plugins/pinterest-follow-button-plugin/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

 /**
 * Constant Values & Variables
 */
define("WEBLIZAR_PINTEREST_PLUGIN_URL", plugin_dir_url(__FILE__));
define("WEBLIZAR_PINTEREST_TD", "weblizar_pf");

/**
 * Widget Code
 */

/**
 * Define Pinterest Widget Class
 */
class WeblizarAddPinterestFollowButton extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'weblizar_pf', // Base ID
            'Add Pinterest Follow Button', // Name
            array( 'description' => __( 'Display Pinterest Follow Button', WEBLIZAR_PINTEREST_TD ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        $WidgetTitle    		=   apply_filters( 'weblizar_widget_title', $instance['WidgetTitle'] );
        $PinterestName    		=   apply_filters( 'weblizar_pinterest_name', $instance['PinterestName'] );
        $PinterestProfileURL    =   apply_filters( 'weblizar_pinterest_profile_url', $instance['PinterestProfileURL'] );
        $ButtonSize    			=   apply_filters( 'weblizar_pinterest_button_size', $instance['ButtonSize'] );
		echo $args['before_widget'];
		if ( ! empty( $instance['WidgetTitle'] ) ) {
			echo $args['before_title'] . apply_filters( 'weblizar_widget_title', $instance['WidgetTitle'] ). $args['after_title'];
		}       
        ?>
        <a data-pin-do="buttonFollow" <?php if($ButtonSize == "large") { ?>data-pin-height="28" <?php } ?> href="<?php echo $PinterestProfileURL; ?>"><?php echo $PinterestName; ?></a>
		<script type="text/javascript" async defer src="<?php echo WEBLIZAR_PINTEREST_PLUGIN_URL."js/pinit.js"; ?>"></script>
        <?php
		echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        if ( isset( $instance[ 'WidgetTitle' ] ) ) {
            $WidgetTitle = $instance[ 'WidgetTitle' ];
        } else {
            $WidgetTitle = "Follow Us On Pinterest";
        }
		
		if ( isset( $instance[ 'PinterestName' ] ) ) {
            $PinterestName = $instance[ 'PinterestName' ];
        } else {
            $PinterestName = "Weblizar Pro Themes & Plugins";
        }

        if ( isset( $instance[ 'PinterestProfileURL' ] ) ) {
            $PinterestProfileURL = $instance[ 'PinterestProfileURL' ];
        } else {
            $PinterestProfileURL = "http://www.pinterest.com/lizarweb/";
        }

        if ( isset( $instance[ 'ButtonSize' ] ) ) {
            $ButtonSize = $instance[ 'ButtonSize' ];
        } else {
			$ButtonSize = "large";
		}
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'WidgetTitle' ); ?>"><?php _e( 'Widget Title' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'WidgetTitle' ); ?>" name="<?php echo $this->get_field_name( 'WidgetTitle' ); ?>" type="text" value="<?php echo esc_attr( $WidgetTitle ); ?>">
        </p>
		
		<p>
            <label for="<?php echo $this->get_field_id( 'PinterestName' ); ?>"><?php _e( 'Pinterest Account Name' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'PinterestName' ); ?>" name="<?php echo $this->get_field_name( 'PinterestName' ); ?>" type="text" value="<?php echo esc_attr( $PinterestName ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'PinterestProfileURL' ); ?>"><?php _e( 'Pinterest Profile URL' ); ?> (Required)</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'PinterestProfileURL' ); ?>" name="<?php echo $this->get_field_name( 'PinterestProfileURL' ); ?>" type="text" value="<?php echo esc_attr( $PinterestProfileURL ); ?>">
        </p>
		
        <p>
            <label for="<?php echo $this->get_field_id( 'ButtonSize' ); ?>"><?php _e( 'Button Size' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'ButtonSize' ); ?>" name="<?php echo $this->get_field_name( 'ButtonSize' ); ?>">
                <option value="large" <?php if($ButtonSize == "large") echo "selected=selected" ?>>Large</option>
                <option value="small" <?php if($ButtonSize == "small") echo "selected=selected" ?>>Small</option>
            </select>
        </p>

        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['WidgetTitle'] = ( ! empty( $new_instance['WidgetTitle'] ) ) ? strip_tags( $new_instance['WidgetTitle'] ) : 'Follow Us On Pinterest';
        $instance['PinterestName'] = ( ! empty( $new_instance['PinterestName'] ) ) ? strip_tags( $new_instance['PinterestName'] ) : 'Weblizar Pro Themes & Plugins';
        $instance['PinterestProfileURL'] = ( ! empty( $new_instance['PinterestProfileURL'] ) ) ? strip_tags( $new_instance['PinterestProfileURL'] ) : 'http://www.pinterest.com/lizarweb/';
        $instance['ButtonSize'] = ( ! empty( $new_instance['ButtonSize'] ) ) ? strip_tags( $new_instance['ButtonSize'] ) : 'large';
        return $instance;
    }

} // end of class WeblizarAddPinterestFollowButton

// register Add Pinterest Follow Button Widget
function WeblizarPinterestFollowButton() {
    register_widget( 'WeblizarAddPinterestFollowButton' );
}
add_action( 'widgets_init', 'WeblizarPinterestFollowButton' );
 ?>