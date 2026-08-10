<?php
/**
 * Nexus Studio launchpad and visual inspection bridge.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

use MspNexusCore\Studio\Layouts;

final class Studio
{
    private const SEED_ACTION = 'msp_nexus_seed_layouts';

    public function register_hooks(): void
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_bar_menu', array($this, 'admin_bar'), 80);
        add_action('wp_enqueue_scripts', array($this, 'overlay'));
        add_action('admin_post_' . self::SEED_ACTION, array($this, 'seed'));
    }

    public function menu(): void
    {
        add_submenu_page('msp-nexus', __('Nexus Studio', 'msp-nexus-core'), __('Studio', 'msp-nexus-core'), 'edit_theme_options', 'msp-nexus-studio', array($this, 'render'));
    }

    public function admin_bar(\WP_Admin_Bar $bar): void
    {
        if (is_admin() || ! current_user_can('edit_theme_options')) {
            return;
        }
        $bar->add_node(
            array(
                'id' => 'msp-nexus-studio',
                'title' => __('Inspect in Nexus Studio', 'msp-nexus-core'),
                'href' => add_query_arg('nexus-studio', '1'),
            )
        );
        if (is_singular() && current_user_can('edit_post', get_queried_object_id())) {
            $bar->add_node(array('id' => 'msp-nexus-edit-content', 'parent' => 'msp-nexus-studio', 'title' => __('Edit this content', 'msp-nexus-core'), 'href' => add_query_arg('nexus-studio', '1', (string) get_edit_post_link(get_queried_object_id(), ''))));
        }
        $bar->add_node(array('id' => 'msp-nexus-edit-site', 'parent' => 'msp-nexus-studio', 'title' => __('Open Site Editor', 'msp-nexus-core'), 'href' => admin_url('site-editor.php')));
    }

    public function overlay(): void
    {
        if (! isset($_GET['nexus-studio']) || '1' !== sanitize_text_field(wp_unslash((string) $_GET['nexus-studio'])) || ! current_user_can('edit_theme_options')) {
            return;
        }
        wp_enqueue_style('msp-nexus-studio');
        wp_enqueue_script('msp-nexus-studio-overlay', plugins_url('assets/studio-overlay.js', MSP_NEXUS_CORE_FILE), array(), MSP_NEXUS_CORE_VERSION, true);
        wp_localize_script(
            'msp-nexus-studio-overlay',
            'mspNexusStudio',
            array(
                'siteEditor' => admin_url('site-editor.php'),
                'contentEditor' => is_singular() ? add_query_arg('nexus-studio', '1', (string) get_edit_post_link(get_queried_object_id(), '')) : '',
                'exitUrl' => remove_query_arg('nexus-studio'),
                'labels' => array('site' => __('Edit site design', 'msp-nexus-core'), 'content' => __('Edit page content', 'msp-nexus-core'), 'exit' => __('Exit inspection', 'msp-nexus-core')),
            )
        );
    }

    public function render(): void
    {
        if (! current_user_can('edit_theme_options')) {
            return;
        }
        $counts = wp_count_posts(Layouts::POST_TYPE);
        $published = isset($counts->publish) ? (int) $counts->publish : 0;
        $cards = array(
            array(__('Visual design', 'msp-nexus-core'), __('Edit global styles, templates, headers, and footers in the native visual canvas.', 'msp-nexus-core'), admin_url('site-editor.php'), __('Open Site Editor', 'msp-nexus-core')),
            array(__('Patterns and MSP elements', 'msp-nexus-core'), __('Insert portable MSP sections, page starters, and responsive layout variations.', 'msp-nexus-core'), admin_url('site-editor.php?path=/patterns'), __('Browse design library', 'msp-nexus-core')),
            array(__('Conditional layouts', 'msp-nexus-core'), sprintf(__('Build templates, regions, mega menus, loops, and popups. Published layouts: %d.', 'msp-nexus-core'), $published), admin_url('edit.php?post_type=' . Layouts::POST_TYPE), __('Manage layouts', 'msp-nexus-core')),
            array(__('Navigation', 'msp-nexus-core'), __('Manage menus with WordPress Navigation and insert Nexus menu-panel blocks where richer content is needed.', 'msp-nexus-core'), admin_url('site-editor.php?path=/navigation'), __('Edit navigation', 'msp-nexus-core')),
            array(__('Starter sites', 'msp-nexus-core'), __('Apply an original MSP, security, co-managed IT, or cloud consulting profile.', 'msp-nexus-core'), admin_url('admin.php?page=msp-nexus-starters'), __('Choose a starter', 'msp-nexus-core')),
            array(__('Portable presets', 'msp-nexus-core'), __('Export or import settings and reusable Nexus layouts as reviewed JSON bundles.', 'msp-nexus-core'), admin_url('admin.php?page=msp-nexus-portability'), __('Open portability tools', 'msp-nexus-core')),
        );
        ?>
        <div class="wrap msp-nexus-admin-studio">
            <h1><?php esc_html_e('Nexus Studio', 'msp-nexus-core'); ?></h1>
            <p class="description"><?php esc_html_e('A portable visual workflow built on WordPress blocks. Nexus controls enhance the editor without converting content into proprietary shortcodes.', 'msp-nexus-core'); ?></p>
            <div class="msp-nexus-studio-grid">
                <?php foreach ($cards as $card) : ?><section class="card"><h2><?php echo esc_html($card[0]); ?></h2><p><?php echo esc_html($card[1]); ?></p><p><a class="button button-primary" href="<?php echo esc_url($card[2]); ?>"><?php echo esc_html($card[3]); ?></a></p></section><?php endforeach; ?>
            </div>
            <h2><?php esc_html_e('Front-end inspection', 'msp-nexus-core'); ?></h2>
            <p><?php esc_html_e('Visit any public page while signed in and choose “Inspect in Nexus Studio” from the toolbar. The overlay identifies editable page regions and links back to the correct WordPress editor.', 'msp-nexus-core'); ?></p>
            <h2><?php esc_html_e('Reusable layout starters', 'msp-nexus-core'); ?></h2>
            <p><?php esc_html_e('Create editable draft examples for a conditional page template, header, footer, services mega menu, consultation popup, and service loop. Existing layouts are not changed.', 'msp-nexus-core'); ?></p>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post"><input type="hidden" name="action" value="<?php echo esc_attr(self::SEED_ACTION); ?>"><?php wp_nonce_field(self::SEED_ACTION); ?><?php submit_button(__('Create draft layout starters', 'msp-nexus-core'), 'secondary', 'submit', false); ?></form>
        </div>
        <style>.msp-nexus-studio-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;max-width:1100px}.msp-nexus-studio-grid .card{margin:0;padding:20px;max-width:none}.msp-nexus-studio-grid h2{margin-top:0}</style>
        <?php
    }

    public function seed(): void
    {
        if (! current_user_can('edit_theme_options')) {
            wp_die(esc_html__('You are not allowed to create layouts.', 'msp-nexus-core'));
        }
        check_admin_referer(self::SEED_ACTION);
        $records = array(
            array('nexus-conditional-page', __('Conditional page template', 'msp-nexus-core'), 'template', '<!-- wp:template-part {"slug":"header","area":"header"} /--><!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} --><main class="wp-block-group"><!-- wp:post-content /--></main><!-- /wp:group --><!-- wp:template-part {"slug":"footer","area":"footer"} /-->'),
            array('nexus-client-header', __('Client header', 'msp-nexus-core'), 'header', '<!-- wp:group {"align":"full","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} --><div class="wp-block-group alignfull"><!-- wp:site-logo {"width":180} /--><!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} /--></div><!-- /wp:group -->'),
            array('nexus-client-footer', __('Client footer', 'msp-nexus-core'), 'footer', '<!-- wp:group {"align":"full","backgroundColor":"navy","textColor":"white","layout":{"type":"constrained"}} --><div class="wp-block-group alignfull has-white-color has-navy-background-color has-text-color has-background"><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:site-title {"level":2} /--><!-- wp:site-tagline /--></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p>Support, sales, and privacy links belong here.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->'),
            array('nexus-services-menu', __('Services mega menu', 'msp-nexus-core'), 'mega_menu', '<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Operate</h3><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li><a href="/services/managed-it/">Managed IT</a></li><li><a href="/services/co-managed-it/">Co-managed IT</a></li></ul><!-- /wp:list --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Protect</h3><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li><a href="/services/cybersecurity/">Cybersecurity</a></li><li><a href="/services/continuity/">Continuity</a></li></ul><!-- /wp:list --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Transform</h3><!-- /wp:heading --><!-- wp:list --><ul class="wp-block-list"><li><a href="/services/cloud/">Cloud</a></li><li><a href="/services/data-ai/">Data and AI</a></li></ul><!-- /wp:list --></div><!-- /wp:column --></div><!-- /wp:columns -->'),
            array('nexus-consultation-popup', __('Consultation popup', 'msp-nexus-core'), 'popup', '<!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:heading --><h2 class="wp-block-heading">Build a clearer technology plan.</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Share the operating challenge, material risk, or decision ahead.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Plan a consultation</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->'),
            array('nexus-service-loop', __('Service loop region', 'msp-nexus-core'), 'loop', '<!-- wp:msp-nexus/content-loop {"postType":"msp_service","count":6,"columns":3,"orderBy":"menu_order","order":"ASC"} /-->'),
        );
        $created = 0;
        foreach ($records as $record) {
            if (get_page_by_path($record[0], OBJECT, Layouts::POST_TYPE)) {
                continue;
            }
            $post_id = wp_insert_post(array('post_type' => Layouts::POST_TYPE, 'post_status' => 'draft', 'post_name' => $record[0], 'post_title' => $record[1], 'post_content' => $record[3]), true);
            if (! is_wp_error($post_id)) {
                update_post_meta((int) $post_id, '_msp_nexus_layout_area', $record[2]);
                update_post_meta((int) $post_id, '_msp_nexus_condition_context', 'all');
                update_post_meta((int) $post_id, '_msp_nexus_condition_priority', 10);
                ++$created;
            }
        }
        wp_safe_redirect(add_query_arg('seeded', (string) $created, admin_url('admin.php?page=msp-nexus-studio')));
        exit;
    }
}
