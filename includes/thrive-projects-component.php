<?php
class BP_Projects_Component extends BP_Component {
    /**
     * [__construct description]
     */
    function __construct()
    {
        parent::start(
            thrive_component_id(), 
            thrive_component_name(), 
            thrive_include_dir()
        );
        
        $this->includes();
        $this->actions();
        
        return $this;
    }

    public function actions()
    {
        // enable thrive projects component
        buddypress()->active_components[$this->id] = '1';

        return;
    }

    /**
     * [includes description]
     * @return [type] [description]
     */
    public function includes()
    {
        $includes = array(
            'thrive-projects-screens.php'
        );

        parent::includes($includes);
    }

    /**
     * [setup_globals description]
     * @return [type] [description]
     */
    function setup_globals()
    {
        global $bp;

        // Define a slug, if necessary

        if (!defined('BP_PROJECTS_SLUG')) {
            define('BP_PROJECTS_SLUG', $this->id);
        }

        $globals = array(
            'slug' => BP_PROJECTS_SLUG,
            'root_slug' => isset($bp->pages->{$this->id}->slug) ? $bp->pages->{$this->id}->slug : BP_PROJECTS_SLUG,
            'has_directory' => true,
            'directory_title' => __('Projects', 'component directory title', 'thrive') ,
            'search_string' => __('Search Projects...', 'buddypress')
        );
        parent::setup_globals($globals);
    }

    function setup_nav() {
        $main_nav = array(
            'name' => __( 'Projects' ),
            'slug' => 'projects',
            'position' => 80,
            /* main nav screen function callback */
            'screen_function' => 'bp_projects_main_screen_function',
            'default_subnav_slug' => 'bp-projects-subnav'
        );
 
        // Add a few subnav items under the main tab
        $sub_nav[] = array(
            'name'            =>  __( 'Projects' ),
            'slug'            => 'projects-subnav',
            'parent_url'      => 'link to the parent url',
            'parent_slug'     => 'projects',
            /* sub nav screen function callback */
            'screen_function' => 'bp_projects_main_screen_function',
            'position'        => 10,
        );
 
        parent::setup_nav( $main_nav, $sub_nav );
    }
} // end class

function bp_setup_projects()
{
    buddypress()->projects = new BP_Projects_Component;
}

add_action('bp_loaded', 'bp_setup_projects', 1);



// ====
class BP_Projects_Group extends BP_Group_Extension {
 
/**
     * Here you can see more customization of the config options
     */
    function __construct() {
        $args = array(
            'slug' => 'group-extension-example-2',
            'name' => 'Group Extension Example 2',
            'nav_item_position' => 105,
            'screens' => array(
                'edit' => array(
                    'name' => 'GE Example 2',
                    // Changes the text of the Submit button
                    // on the Edit page
                    'submit_text' => 'Submit, suckaz',
                ),
                'create' => array(
                    'position' => 100,
                ),
            ),
        );
        parent::init( $args );
    }
 
    function display( $group_id = NULL ) {
        $group_id = bp_get_group_id();
        echo 'This plugin is 2x cooler!';
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
 
    /**
     * create_screen() is an optional method that, when present, will
     * be used instead of settings_screen() in the context of group
     * creation.
     *
     * Similar overrides exist via the following methods:
     *   * create_screen_save()
     *   * edit_screen()
     *   * edit_screen_save()
     *   * admin_screen()
     *   * admin_screen_save()
     */
    function create_screen( $group_id = NULL ) {
        $setting = groups_get_groupmeta( $group_id, 'group_extension_example_2_setting' );
 
        ?>
        Welcome to your new group! You are neat.
        Save your plugin setting here: <input type="text" name="group_extension_example_2_setting" value="<?php echo esc_attr( $setting ) ?>" />
        <?php
    }
 
}
bp_register_group_extension('BP_Projects_Group');
?>