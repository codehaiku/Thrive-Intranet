<?php
/**
 * This file contains the BP_Projects_Component
 * which is responsible for our project component
 * structre and at the same time make it possible
 * to be used in buddypress profiles
 *
 * @since               1.0
 * @package             Thrive Intranet
 * @subpackage          Projects
 * @author              dunhakdis
 */
if (!defined('ABSPATH')) die();

/**
 * Include our core functions
 */
require_once(plugin_dir_path(__FILE__) . '../core/functions.php');

/**
 * BP_Projects_Component
 * 
 * BP Projects Components extends the 
 * BP Component object which is provided
 * as a starting point for building custom
 * buddypress module
 *
 * @since  1.0
 * @uses  BP_Component the boilerplate
 */
class BP_Projects_Component extends BP_Component {
    
    /**
     * Holds the ID of our 'Projects' component
     */
    var $id = '';

    /**
     * Holds the name of our 'Projects' component
     */
    var $name = '';
    /**
     * Register our 'Projects' Component to BuddyPress Components
     */
    function __construct()
    {   
        $this->id = thrive_component_id();
        $this->name = thrive_component_name();

        parent::start(
            $this->id, 
            $this->name, 
            thrive_include_dir()
        );
        
        $this->includes();
        $this->actions();
        
        return $this;
    }

    /**
     * All actions and hooks that are related to
     * BP_Projects_Component are listed here
     *
     * @uses  buddypress()
     * @return void
     */
    private function actions()
    {
        // enable thrive projects component
        buddypress()->active_components[$this->id] = '1';

        return;
    }

    /**
     * Incudes all related screens and functions
     * related to our 'Projects' component
     * 
     * @return void
     */
    public function includes()
    {
        $includes = array(
            'project-screens.php'
        );

        parent::includes( $includes );

        return;
    }

    /**
     * All public objects that are accessible
     * to anyclass are listed here
     * 
     * @return void
     */
    public function setup_globals()
    {
        global $bp;

        // Define some slug here
        if (!defined('BP_PROJECTS_SLUG')) {
            define('BP_PROJECTS_SLUG', $this->id);
        }

        $globals = array(
            'slug' => BP_PROJECTS_SLUG,
            'root_slug' => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : BP_PROJECTS_SLUG,
            'has_directory' => true,
            'directory_title' => __('Projects', 'component directory title', 'thrive') ,
            'search_string' => __('Search Projects...', 'buddypress')
        );

        parent::setup_globals($globals);

        return;
    }

    /**
     * Set-up our buddypress navigation which
     * are accesible in members and groups nav
     * 
     * @return void
     */
    function setup_nav() {

        $main_nav = array(
            'name' => $this->name,
            'slug' => $this->id,
            'position' => 80,
            /* main nav screen function callback */
            'screen_function' => 'bp_projects_main_screen_function',
            'default_subnav_slug' => 'all'
        );
 
        // Add a few subnav items under the main tab
        $sub_nav[] = array(
            'name'            =>  __( 'My Projects' ),
            'slug'            => 'all',
            'parent_url'      => bp_loggedin_user_domain() . '' . $this->id . '/',
            'parent_slug'     => 'projects',
            'screen_function' => 'bp_projects_main_screen_function',
            'position'        => 10,
        );

        // Edit subnav
        $sub_nav[] = array(
            'name'            =>  __( 'New Project' ),
            'slug'            => 'new',
            'parent_url'      => bp_loggedin_user_domain() . '' . $this->id . '/',
            'parent_slug'     => 'projects',
            'screen_function' => 'bp_projects_main_screen_function_new_project',
            'position'        => 10,
        );
 
        parent::setup_nav( $main_nav, $sub_nav );

        return;
    }
} // end class

function thrive_setup_project_component() {
    buddypress()->projects = new BP_Projects_Component;
}

add_action('bp_loaded', 'thrive_setup_project_component', 1);

// ====
class BP_Projects_Group extends BP_Group_Extension {
 
/**
     * Here you can see more customization of the config options
     */
    function __construct() {
        $args = array(
            'slug' => 'projects',
            'name' => 'Projects',
            'nav_item_position' => 105,
            'screens' => array(
                'edit' => array(
                    'name' => 'Projects',
                    // Changes the text of the Submit button
                    // on the Edit page
                    'submit_text' => 'Submit, submit',
                ),
                'create' => array(
                    'position' => 100,
                ),
            ),
        );
        parent::init( $args );
    }
 
    function display( $group_id = NULL ) {

        $group_id = bp_get_group_id(); ?>
            
            <h3>
                <?php _e('Projects', 'thrive'); ?>
            </h3>
            
            <div id="thrive-intranet-projects">
                
                <?php thrive_new_project_modal( $group_id ); ?>

                <?php 
                    $args = array(
                        'meta_key'   => 'thrive_project_group_id',
                        'meta_value' => absint( $group_id )
                    ); 
                ?>

                <?php thrive_project_loop( $args ); ?>
            </div>

        <?php

        return;
    }
 
    function settings_screen( $group_id = NULL ) {
        
        $setting = groups_get_groupmeta( $group_id, 'group_extension_example_2_setting' );
 
        ?>
        Save your plugin setting here: <input type="text" name="group_extension_example_2_setting" value="<?php echo esc_attr( $setting ) ?>" />
        <?php
    }
 
    function settings_screen_save( $group_id = NULL ) {
        $setting = isset( $_POST['group_extension_example_2_setting'] ) ? $_POST['group_extension_example_2_setting'] : '';
        groups_update_groupmeta( $group_id, 'group_extension_example_2_setting', $setting );
    }
 

 
}
bp_register_group_extension('BP_Projects_Group');
?>