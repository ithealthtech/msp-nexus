<?php
/**
 * One "Company content" menu with a landing page, replacing thirteen top-level menus.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

use MspNexusCore\Content\Registrar;

final class ContentHub
{
    public function register_hooks(): void
    {
        // Priority 9 so the parent exists before post types attach their list screens to it.
        add_action('admin_menu', array($this, 'menu'), 9);
        add_action('admin_menu', array($this, 'order_nexus_menu'), 999);
    }

    public function menu(): void
    {
        add_menu_page(
            __('Company content', 'msp-nexus-core'),
            __('Company content', 'msp-nexus-core'),
            'edit_posts',
            Registrar::MENU_SLUG,
            array($this, 'render'),
            'dashicons-portfolio',
            26
        );
        add_submenu_page(Registrar::MENU_SLUG, __('Company content', 'msp-nexus-core'), __('Overview', 'msp-nexus-core'), 'edit_posts', Registrar::MENU_SLUG, array($this, 'render'));
    }

    /** Everyday screens first, maintenance and recovery tools last. */
    public function order_nexus_menu(): void
    {
        global $submenu;
        if (empty($submenu['msp-nexus']) || ! is_array($submenu['msp-nexus'])) {
            return;
        }
        $order = array('msp-nexus', 'msp-nexus-setup', 'msp-nexus-starters', 'msp-nexus-studio', 'edit.php?post_type=msp_nexus_layout', 'msp-nexus-branding', 'msp-nexus-portability', 'msp-nexus-performance', 'msp-nexus-privacy', 'msp-nexus-integrations', 'msp-nexus-woocommerce', 'msp-nexus-license', 'msp-nexus-diagnostics', 'msp-nexus-reset');
        $rank = array_flip($order);
        $items = array_values($submenu['msp-nexus']);
        usort($items, static function (array $a, array $b) use ($rank): int {
            return ($rank[$a[2]] ?? 500) <=> ($rank[$b[2]] ?? 500);
        });
        $submenu['msp-nexus'] = $items; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- reordering only.
    }

    /** @return array<string, string> */
    private function descriptions(): array
    {
        return array(
            'msp_service' => __('What you sell. Each service gets its own page and appears in service grids.', 'msp-nexus-core'),
            'msp_industry' => __('Markets you serve, with the regulations and software each one cares about.', 'msp-nexus-core'),
            'msp_case_study' => __('Client stories with measurable results. Kept as drafts until you verify them.', 'msp-nexus-core'),
            'msp_testimonial' => __('Short client quotes for sliders and proof sections.', 'msp-nexus-core'),
            'msp_team' => __('People and leadership profiles for About and team pages.', 'msp-nexus-core'),
            'msp_location' => __('Offices and service areas, with hours and contact details.', 'msp-nexus-core'),
            'msp_pricing_plan' => __('Plans and prices shown in pricing tables and comparisons.', 'msp-nexus-core'),
            'msp_resource' => __('Guides, checklists, and downloads for your resource library.', 'msp-nexus-core'),
            'msp_event' => __('Webinars and events with dates and registration links.', 'msp-nexus-core'),
            'msp_faq' => __('Questions and answers used by FAQ sections across the site.', 'msp-nexus-core'),
            'msp_outcome' => __('Business results you deliver, such as less downtime or audit readiness.', 'msp-nexus-core'),
            'msp_partner' => __('Technology vendors and alliances for logo walls.', 'msp-nexus-core'),
            'msp_certification' => __('Certifications and awards you can prove.', 'msp-nexus-core'),
        );
    }

    public function render(): void
    {
        if (! current_user_can('edit_posts')) {
            return;
        }
        $descriptions = $this->descriptions();
        ?>
        <div class="wrap msp-nexus-hub">
            <h1><?php esc_html_e('Company content', 'msp-nexus-core'); ?></h1>
            <p class="msp-nexus-hub__intro"><?php esc_html_e('Everything about your business lives here once, and every page that shows it stays in sync. Add a service here and it appears in every service grid automatically.', 'msp-nexus-core'); ?></p>
            <div class="msp-nexus-hub__grid">
            <?php
            foreach ($descriptions as $type => $description) :
                $object = get_post_type_object($type);
                if (! $object || ! current_user_can($object->cap->edit_posts)) {
                    continue;
                }
                $counts = wp_count_posts($type);
                $published = (int) ($counts->publish ?? 0);
                $drafts = (int) ($counts->draft ?? 0) + (int) ($counts->pending ?? 0);
                $icon = (string) (Registrar::types()[$type]['icon'] ?? 'dashicons-admin-post');
                ?>
                <div class="msp-nexus-hub__card">
                    <span class="dashicons <?php echo esc_attr($icon); ?>" aria-hidden="true"></span>
                    <h2><a href="<?php echo esc_url(admin_url('edit.php?post_type=' . $type)); ?>"><?php echo esc_html($object->labels->name); ?></a></h2>
                    <p><?php echo esc_html($description); ?></p>
                    <p class="msp-nexus-hub__count">
                        <?php
                        if (0 === $published + $drafts) {
                            esc_html_e('Nothing here yet.', 'msp-nexus-core');
                        } else {
                            /* translators: 1: published count, 2: draft count */
                            echo esc_html(sprintf(_n('%1$d published', '%1$d published', $published, 'msp-nexus-core'), $published) . ($drafts ? ' · ' . sprintf(_n('%d draft', '%d drafts', $drafts, 'msp-nexus-core'), $drafts) : ''));
                        }
                        ?>
                    </p>
                    <p class="msp-nexus-hub__actions">
                        <a class="button" href="<?php echo esc_url(admin_url('edit.php?post_type=' . $type)); ?>"><?php esc_html_e('View all', 'msp-nexus-core'); ?></a>
                        <a class="button button-primary" href="<?php echo esc_url(admin_url('post-new.php?post_type=' . $type)); ?>"><?php echo esc_html($object->labels->add_new_item); ?></a>
                    </p>
                </div>
            <?php endforeach; ?>
            </div>
            <?php
            $taxonomies = get_object_taxonomies(array_keys($descriptions), 'objects');
            if ($taxonomies) :
                ?>
                <h2><?php esc_html_e('Categories and tags', 'msp-nexus-core'); ?></h2>
                <p class="msp-nexus-hub__tax">
                <?php foreach ($taxonomies as $taxonomy) : ?>
                    <?php if ($taxonomy->show_ui && current_user_can($taxonomy->cap->manage_terms)) : ?>
                        <a class="button" href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=' . $taxonomy->name)); ?>"><?php echo esc_html($taxonomy->labels->name); ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
                </p>
            <?php endif; ?>
        </div>
        <style>.msp-nexus-hub__intro{font-size:14px;max-width:760px}.msp-nexus-hub__grid{display:grid;gap:16px;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));margin-top:20px;max-width:1280px}.msp-nexus-hub__card{background:#fff;border:1px solid #c3c4c7;border-radius:6px;display:flex;flex-direction:column;padding:18px 20px}.msp-nexus-hub__card .dashicons{color:#2271b1;font-size:26px;height:26px;width:26px}.msp-nexus-hub__card h2{font-size:16px;margin:10px 0 6px}.msp-nexus-hub__card h2 a{text-decoration:none}.msp-nexus-hub__card p{margin:0 0 10px}.msp-nexus-hub__count{color:#50575e;font-size:12px}.msp-nexus-hub__actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:auto!important}.msp-nexus-hub__tax{display:flex;flex-wrap:wrap;gap:8px}</style>
        <?php
    }
}
