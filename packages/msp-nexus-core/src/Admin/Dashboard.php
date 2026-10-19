<?php
/**
 * MSP Nexus dashboard: welcome, quick actions, launch checklist, system status, and updates.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Admin;

use MspNexusCore\Content\Registrar;

final class Dashboard
{
    private const CHECK_UPDATES = 'msp_nexus_check_updates';

    public function register_hooks(): void
    {
        add_action('admin_post_' . self::CHECK_UPDATES, array($this, 'check_updates'));
    }

    /** Forgets the cached release lookups, then lets WordPress check again on the Updates screen. */
    public function check_updates(): void
    {
        if (! current_user_can('update_plugins')) {
            wp_die(esc_html__('You are not allowed to check for updates.', 'msp-nexus-core'), '', array('response' => 403));
        }
        check_admin_referer(self::CHECK_UPDATES);
        delete_site_transient('msp_nexus_github_release');
        delete_site_transient('msp_nexus_update_msp-nexus');
        delete_site_transient('msp_nexus_update_msp-nexus-core');
        delete_site_transient('update_themes');
        delete_site_transient('update_plugins');
        wp_safe_redirect(admin_url('update-core.php?force-check=1'));
        exit;
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
        $user = wp_get_current_user();
        $checklist = $this->checklist();
        $done = count(array_filter(array_column($checklist, 'done')));
        $total = count($checklist);
        $percent = $total > 0 ? (int) round($done / $total * 100) : 100;
        $status = $this->status();
        $updates = $this->updates();
        $branding = get_option('msp_nexus_branding', array());
        $branding = is_array($branding) ? $branding : array();
        $client = ! empty($branding['client_mode']);
        ?>
        <div class="wrap msp-nexus-dash">
            <div class="msp-nexus-dash__hero">
                <div>
                    <p class="msp-nexus-dash__eyebrow"><?php echo esc_html(get_bloginfo('name')); ?></p>
                    <h1><?php echo esc_html(sprintf(/* translators: %s: user display name */ __('Welcome, %s', 'msp-nexus-core'), $user->display_name)); ?></h1>
                    <p class="msp-nexus-dash__lede"><?php esc_html_e('Everything for your website in one place: company details, page designs, and the health of your site.', 'msp-nexus-core'); ?></p>
                </div>
                <div class="msp-nexus-dash__hero-actions">
                    <?php if (current_user_can('edit_theme_options')) : ?><a class="button button-primary button-hero" href="<?php echo esc_url(admin_url('site-editor.php')); ?>"><?php esc_html_e('Edit site', 'msp-nexus-core'); ?></a><?php endif; ?>
                    <a class="button button-hero" href="<?php echo esc_url(home_url('/')); ?>" target="_blank" rel="noopener"><?php esc_html_e('View site', 'msp-nexus-core'); ?></a>
                </div>
            </div>

            <div class="msp-nexus-dash__grid">
                <section class="msp-nexus-card msp-nexus-dash__actions" aria-labelledby="msp-nexus-dash-actions">
                    <header class="msp-nexus-card__head"><span class="msp-nexus-card__icon"><?php Shell::icon('dashboard'); ?></span><div><h2 id="msp-nexus-dash-actions"><?php esc_html_e('Quick actions', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('The places you will visit most.', 'msp-nexus-core'); ?></p></div></header>
                    <ul class="msp-nexus-tiles">
                        <?php foreach ($this->actions() as $action) : ?>
                            <li><a class="msp-nexus-tile" href="<?php echo esc_url($action['url']); ?>"><span class="msp-nexus-tile__icon"><?php Shell::icon($action['icon']); ?></span><span class="msp-nexus-tile__label"><?php echo esc_html($action['label']); ?></span><span class="msp-nexus-tile__desc"><?php echo esc_html($action['desc']); ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <section class="msp-nexus-card msp-nexus-dash__checklist" aria-labelledby="msp-nexus-dash-checklist">
                    <header class="msp-nexus-card__head"><span class="msp-nexus-card__icon"><?php Shell::icon('setup'); ?></span><div><h2 id="msp-nexus-dash-checklist"><?php esc_html_e('Launch checklist', 'msp-nexus-core'); ?></h2><p><?php echo esc_html(sprintf(/* translators: 1: completed items, 2: total items */ __('%1$d of %2$d complete', 'msp-nexus-core'), $done, $total)); ?></p></div></header>
                    <div class="msp-nexus-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr((string) $percent); ?>" aria-label="<?php esc_attr_e('Launch checklist progress', 'msp-nexus-core'); ?>"><span style="width:<?php echo esc_attr((string) $percent); ?>%"></span></div>
                    <ul class="msp-nexus-checklist">
                        <?php foreach ($checklist as $item) : ?>
                            <li class="<?php echo $item['done'] ? 'is-done' : 'is-todo'; ?>">
                                <span class="msp-nexus-checklist__mark"><?php Shell::icon($item['done'] ? 'check' : 'alert'); ?></span>
                                <span class="msp-nexus-checklist__label"><?php echo esc_html($item['label']); ?></span>
                                <?php if (! $item['done']) : ?><a href="<?php echo esc_url($item['url']); ?>"><?php esc_html_e('Fix', 'msp-nexus-core'); ?><span class="screen-reader-text">: <?php echo esc_html($item['label']); ?></span></a><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <section class="msp-nexus-card msp-nexus-dash__status" aria-labelledby="msp-nexus-dash-status">
                    <header class="msp-nexus-card__head"><span class="msp-nexus-card__icon"><?php Shell::icon('pulse'); ?></span><div><h2 id="msp-nexus-dash-status"><?php esc_html_e('System status', 'msp-nexus-core'); ?></h2><p><?php echo esc_html($status['summary']); ?></p></div></header>
                    <dl class="msp-nexus-stats">
                        <?php foreach ($status['rows'] as $row) : ?>
                            <div class="msp-nexus-stats__row is-<?php echo esc_attr($row['status']); ?>"><dt><?php echo esc_html($row['label']); ?></dt><dd><?php echo esc_html($row['detail']); ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                    <p class="msp-nexus-card__foot"><a href="<?php echo esc_url(admin_url('admin.php?page=msp-nexus-diagnostics')); ?>"><?php esc_html_e('Full diagnostics', 'msp-nexus-core'); ?> →</a></p>
                </section>

                <section class="msp-nexus-card msp-nexus-dash__updates" aria-labelledby="msp-nexus-dash-updates">
                    <header class="msp-nexus-card__head"><span class="msp-nexus-card__icon"><?php Shell::icon('refresh'); ?></span><div><h2 id="msp-nexus-dash-updates"><?php esc_html_e('Updates', 'msp-nexus-core'); ?></h2><p><?php echo esc_html($updates['summary']); ?></p></div></header>
                    <dl class="msp-nexus-stats">
                        <?php foreach ($updates['rows'] as $row) : ?>
                            <div class="msp-nexus-stats__row is-<?php echo esc_attr($row['status']); ?>"><dt><?php echo esc_html($row['label']); ?></dt><dd><?php echo esc_html($row['detail']); ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                    <?php if (current_user_can('update_plugins')) : ?>
                        <form class="msp-nexus-card__foot" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="<?php echo esc_attr(self::CHECK_UPDATES); ?>">
                            <?php wp_nonce_field(self::CHECK_UPDATES); ?>
                            <button type="submit" class="button"><?php esc_html_e('Check for updates now', 'msp-nexus-core'); ?></button>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=msp-nexus-license')); ?>"><?php esc_html_e('License & updates', 'msp-nexus-core'); ?> →</a>
                        </form>
                    <?php endif; ?>
                </section>

                <section class="msp-nexus-card msp-nexus-dash__help" aria-labelledby="msp-nexus-dash-help">
                    <header class="msp-nexus-card__head"><span class="msp-nexus-card__icon"><?php Shell::icon('help'); ?></span><div><h2 id="msp-nexus-dash-help"><?php esc_html_e('Help', 'msp-nexus-core'); ?></h2><p><?php esc_html_e('Guides and release notes.', 'msp-nexus-core'); ?></p></div></header>
                    <ul class="msp-nexus-links">
                        <?php if ($client && '' !== (string) ($branding['support_url'] ?? '')) : ?>
                            <li><a href="<?php echo esc_url((string) $branding['support_url']); ?>" target="_blank" rel="noopener"><?php Shell::icon('help'); ?><?php echo esc_html('' !== (string) ($branding['partner_name'] ?? '') ? sprintf(/* translators: %s: support partner name */ __('Contact %s', 'msp-nexus-core'), (string) $branding['partner_name']) : __('Contact support', 'msp-nexus-core')); ?></a></li>
                        <?php else : ?>
                            <li><a href="https://github.com/ithealthtech/msp-nexus#readme" target="_blank" rel="noopener"><?php Shell::icon('book'); ?><?php esc_html_e('Documentation', 'msp-nexus-core'); ?></a></li>
                            <li><a href="https://github.com/ithealthtech/msp-nexus/releases" target="_blank" rel="noopener"><?php Shell::icon('refresh'); ?><?php esc_html_e('Release notes', 'msp-nexus-core'); ?></a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo esc_url(admin_url('admin.php?page=msp-nexus-setup')); ?>"><?php Shell::icon('setup'); ?><?php esc_html_e('Guided setup', 'msp-nexus-core'); ?></a></li>
                    </ul>
                </section>
            </div>
        </div>
        <?php
    }

    /** @return array<int, array{label:string,desc:string,icon:string,url:string}> */
    private function actions(): array
    {
        $actions = array();
        if (current_user_can('edit_theme_options')) {
            $actions[] = array('label' => __('Edit site', 'msp-nexus-core'), 'desc' => __('Header, footer, and templates', 'msp-nexus-core'), 'icon' => 'layouts', 'url' => admin_url('site-editor.php'));
        }
        $actions[] = array('label' => __('Pages', 'msp-nexus-core'), 'desc' => __('Edit or add pages', 'msp-nexus-core'), 'icon' => 'content', 'url' => admin_url('edit.php?post_type=page'));
        if (current_user_can('edit_posts')) {
            $actions[] = array('label' => __('Company content', 'msp-nexus-core'), 'desc' => __('Services, team, reviews', 'msp-nexus-core'), 'icon' => 'book', 'url' => admin_url('admin.php?page=' . Registrar::MENU_SLUG));
        }
        $actions[] = array('label' => __('Settings', 'msp-nexus-core'), 'desc' => __('Phones, email, address', 'msp-nexus-core'), 'icon' => 'settings', 'url' => admin_url('admin.php?page=msp-nexus-settings'));
        $actions[] = array('label' => __('Starter sites', 'msp-nexus-core'), 'desc' => __('Ready-made page sets', 'msp-nexus-core'), 'icon' => 'starters', 'url' => admin_url('admin.php?page=msp-nexus-starters'));
        if (current_user_can('edit_theme_options')) {
            $actions[] = array('label' => __('Studio', 'msp-nexus-core'), 'desc' => __('Design tokens and popups', 'msp-nexus-core'), 'icon' => 'studio', 'url' => admin_url('admin.php?page=msp-nexus-studio'));
        }
        return $actions;
    }

    /** @return array<int, array{label:string,done:bool,url:string}> */
    private function checklist(): array
    {
        $settings = get_option('msp_nexus_settings', array());
        $settings = is_array($settings) ? $settings : array();
        $settings_url = admin_url('admin.php?page=msp-nexus-settings');
        return array(
            array('label' => __('Add your logo', 'msp-nexus-core'), 'done' => has_custom_logo() || (int) get_option('site_logo') > 0, 'url' => admin_url('site-editor.php')),
            array('label' => __('Enter your company name', 'msp-nexus-core'), 'done' => '' !== (string) ($settings['organization_name'] ?? ''), 'url' => $settings_url),
            array('label' => __('Add a phone number', 'msp-nexus-core'), 'done' => '' !== (string) ($settings['sales_phone'] ?? '') || '' !== (string) ($settings['support_phone'] ?? ''), 'url' => $settings_url),
            array('label' => __('Add a contact email', 'msp-nexus-core'), 'done' => '' !== (string) ($settings['sales_email'] ?? ''), 'url' => $settings_url),
            array('label' => __('Choose a homepage', 'msp-nexus-core'), 'done' => 'page' === get_option('show_on_front') && (int) get_option('page_on_front') > 0, 'url' => admin_url('options-reading.php')),
            array('label' => __('Publish a privacy policy', 'msp-nexus-core'), 'done' => '' !== get_privacy_policy_url(), 'url' => admin_url('options-privacy.php')),
            array('label' => __('Use readable page addresses', 'msp-nexus-core'), 'done' => '' !== (string) get_option('permalink_structure'), 'url' => admin_url('options-permalink.php')),
            array('label' => __('Let search engines find the site', 'msp-nexus-core'), 'done' => '1' === (string) get_option('blog_public'), 'url' => admin_url('options-reading.php')),
            array('label' => __('Serve the site over HTTPS', 'msp-nexus-core'), 'done' => 0 === strpos(home_url('/'), 'https://'), 'url' => admin_url('options-general.php')),
        );
    }

    /** @return array{summary:string,rows:array<int, array{label:string,status:string,detail:string}>} */
    private function status(): array
    {
        $checks = (new Diagnostics())->checks();
        $problems = count(array_filter($checks, static function (array $check): bool {
            return in_array($check['status'], array('fail', 'notice'), true);
        }));
        $wanted = array(__('WordPress', 'msp-nexus-core'), __('PHP', 'msp-nexus-core'), __('Active theme', 'msp-nexus-core'), __('HTTPS', 'msp-nexus-core'), __('Memory limit', 'msp-nexus-core'), __('Launch safety', 'msp-nexus-core'));
        $rows = array_values(array_filter($checks, static function (array $check) use ($wanted): bool {
            return in_array($check['label'], $wanted, true);
        }));
        $summary = 0 === $problems ? __('Everything looks good.', 'msp-nexus-core') : sprintf(/* translators: %d: number of checks needing attention */ _n('%d item needs attention.', '%d items need attention.', $problems, 'msp-nexus-core'), $problems);
        return array('summary' => $summary, 'rows' => $rows);
    }

    /** @return array{summary:string,rows:array<int, array{label:string,status:string,detail:string}>} */
    private function updates(): array
    {
        $theme = wp_get_theme('msp-nexus');
        $theme_version = $theme->exists() ? (string) $theme->get('Version') : '';
        $release = get_site_transient('msp_nexus_github_release');
        $latest = is_array($release) ? (string) ($release['version'] ?? '') : '';
        $behind = '' !== $latest && (version_compare(MSP_NEXUS_CORE_VERSION, $latest, '<') || ('' !== $theme_version && version_compare($theme_version, $latest, '<')));
        $auto = in_array('msp-nexus-core/msp-nexus-core.php', (array) get_site_option('auto_update_plugins', array()), true);
        $rows = array(
            array('label' => __('Theme', 'msp-nexus-core'), 'status' => 'info', 'detail' => '' !== $theme_version ? $theme_version : __('not installed', 'msp-nexus-core')),
            array('label' => __('Plugin', 'msp-nexus-core'), 'status' => 'info', 'detail' => MSP_NEXUS_CORE_VERSION),
            array('label' => __('Latest release', 'msp-nexus-core'), 'status' => $behind ? 'notice' : ('' !== $latest ? 'pass' : 'info'), 'detail' => '' !== $latest ? $latest : __('not checked yet', 'msp-nexus-core')),
            array('label' => __('Automatic updates', 'msp-nexus-core'), 'status' => $auto ? 'pass' : 'notice', 'detail' => $auto ? __('On', 'msp-nexus-core') : __('Off', 'msp-nexus-core')),
        );
        $summary = $behind ? __('A newer version is available.', 'msp-nexus-core') : ('' !== $latest ? __('You are up to date.', 'msp-nexus-core') : __('Run a check to see the latest version.', 'msp-nexus-core'));
        return array('summary' => $summary, 'rows' => $rows);
    }
}
