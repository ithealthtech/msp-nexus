<?php
/**
 * Updates from the public GitHub releases of MSP Nexus.
 *
 * Used when no licensed update channel is active. Each check reads the latest published release,
 * takes the theme and plugin package digests from that release's manifest, and only installs a
 * package whose SHA-256 matches and whose archive is the expected product.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Licensing;

final class GitHubUpdater
{
    private const CACHE = 'msp_nexus_github_release';
    private const PLUGIN = 'msp-nexus-core/msp-nexus-core.php';
    private const THEME = 'msp-nexus';

    public function register_hooks(): void
    {
        if (! $this->enabled()) {
            return;
        }
        add_filter('pre_set_site_transient_update_plugins', array($this, 'plugin_update'));
        add_filter('pre_set_site_transient_update_themes', array($this, 'theme_update'));
        add_filter('upgrader_pre_download', array($this, 'verify_download'), 10, 4);
        add_filter('plugins_api', array($this, 'plugin_info'), 10, 3);
        add_filter('auto_update_plugin', array($this, 'allow_plugin_auto_update'), 20, 2);
        add_filter('auto_update_theme', array($this, 'allow_theme_auto_update'), 20, 2);
        add_action('upgrader_process_complete', array($this, 'clear_cache'), 10, 0);
    }

    /** A licensed channel, when active, owns updates. Sites can also opt out with a constant. */
    private function enabled(): bool
    {
        if (defined('MSP_NEXUS_DISABLE_GITHUB_UPDATES') && constant('MSP_NEXUS_DISABLE_GITHUB_UPDATES')) {
            return false;
        }
        $state = get_site_option('msp_nexus_license_state', array());
        return ! (is_array($state) && ! empty($state['license_id']) && in_array((string) ($state['status'] ?? ''), array('active', 'grace'), true));
    }

    /** @param mixed $transient @return mixed */
    public function plugin_update($transient)
    {
        if (! is_object($transient) || empty($transient->checked)) {
            return $transient;
        }
        $release = $this->release();
        if (! $release) {
            return $transient;
        }
        $item = (object) array('id' => self::PLUGIN, 'slug' => 'msp-nexus-core', 'plugin' => self::PLUGIN, 'new_version' => $release['version'], 'package' => $release['plugin']['url'], 'url' => $release['html_url'], 'requires' => '6.7', 'requires_php' => '7.4.33', 'tested' => $release['tested']);
        if (version_compare(MSP_NEXUS_CORE_VERSION, $release['version'], '<')) {
            $transient->response[self::PLUGIN] = $item;
        } else {
            $item->new_version = MSP_NEXUS_CORE_VERSION;
            $item->package = '';
            $transient->no_update[self::PLUGIN] = $item;
        }
        return $transient;
    }

    /** @param mixed $transient @return mixed */
    public function theme_update($transient)
    {
        if (! is_object($transient) || ! isset($transient->checked[self::THEME])) {
            return $transient;
        }
        $release = $this->release();
        if (! $release) {
            return $transient;
        }
        $item = array('theme' => self::THEME, 'new_version' => $release['version'], 'package' => $release['theme']['url'], 'url' => $release['html_url'], 'requires' => '6.7', 'requires_php' => '7.4.33');
        if (version_compare((string) $transient->checked[self::THEME], $release['version'], '<')) {
            $transient->response[self::THEME] = $item;
        } else {
            $item['new_version'] = (string) $transient->checked[self::THEME];
            $item['package'] = '';
            $transient->no_update[self::THEME] = $item;
        }
        return $transient;
    }

    /**
     * Latest published release with both packages and their digests, cached for six hours.
     *
     * @return array<string, mixed>|null
     */
    private function release(): ?array
    {
        $cached = get_site_transient(self::CACHE);
        if (is_array($cached)) {
            return empty($cached['version']) ? null : $cached;
        }
        $release = $this->fetch_release();
        // A failed check is remembered for an hour so an outage or rate limit is not retried on every page load.
        set_site_transient(self::CACHE, $release ?? array(), $release ? 6 * HOUR_IN_SECONDS : HOUR_IN_SECONDS);
        return $release;
    }

    /** @return array<string, mixed>|null */
    private function fetch_release(): ?array
    {
        $repo = (string) apply_filters('msp_nexus_github_repository', 'ithealthtech/msp-nexus');
        if (! preg_match('#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#', $repo)) {
            return null;
        }
        $response = wp_safe_remote_get('https://api.github.com/repos/' . $repo . '/releases/latest', array('timeout' => 10, 'headers' => array('Accept' => 'application/vnd.github+json', 'User-Agent' => 'MSP-Nexus-Core/' . MSP_NEXUS_CORE_VERSION)));
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return null;
        }
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (! is_array($data) || ! empty($data['draft']) || ! empty($data['prerelease'])) {
            return null;
        }
        $version = ltrim((string) ($data['tag_name'] ?? ''), 'vV');
        if (! preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            return null;
        }
        $assets = array();
        foreach ((array) ($data['assets'] ?? array()) as $asset) {
            if (is_array($asset) && isset($asset['name'], $asset['browser_download_url'])) {
                $assets[(string) $asset['name']] = (string) $asset['browser_download_url'];
            }
        }
        $theme_file = 'msp-nexus-' . $version . '.zip';
        $plugin_file = 'msp-nexus-core-' . $version . '.zip';
        if (empty($assets['release-manifest.json']) || empty($assets[$theme_file]) || empty($assets[$plugin_file])) {
            return null;
        }
        $digests = $this->manifest_digests($assets['release-manifest.json']);
        if (empty($digests[$theme_file]) || empty($digests[$plugin_file])) {
            return null;
        }
        foreach (array($assets[$theme_file], $assets[$plugin_file]) as $url) {
            if (0 !== strpos($url, 'https://github.com/' . $repo . '/releases/download/')) {
                return null;
            }
        }
        return array(
            'version' => $version,
            'html_url' => esc_url_raw((string) ($data['html_url'] ?? 'https://github.com/' . $repo . '/releases')),
            'notes' => (string) ($data['body'] ?? ''),
            'published' => (string) ($data['published_at'] ?? ''),
            'tested' => get_bloginfo('version'),
            'theme' => array('url' => $assets[$theme_file], 'sha256' => $digests[$theme_file]),
            'plugin' => array('url' => $assets[$plugin_file], 'sha256' => $digests[$plugin_file]),
        );
    }

    /** @return array<string, string> File name => SHA-256 from the release manifest. */
    private function manifest_digests(string $url): array
    {
        $response = wp_safe_remote_get($url, array('timeout' => 10, 'headers' => array('User-Agent' => 'MSP-Nexus-Core/' . MSP_NEXUS_CORE_VERSION)));
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return array();
        }
        $manifest = json_decode(wp_remote_retrieve_body($response), true);
        $digests = array();
        foreach ((array) ($manifest['packages'] ?? array()) as $package) {
            $sha = strtolower((string) ($package['sha256'] ?? ''));
            if (is_array($package) && ! empty($package['file']) && preg_match('/^[a-f0-9]{64}$/', $sha)) {
                $digests[(string) $package['file']] = $sha;
            }
        }
        return $digests;
    }

    /**
     * Download an MSP Nexus package from the release and check its digest and contents before
     * WordPress extracts it.
     *
     * @param mixed $reply @param mixed $upgrader @param array<string, mixed> $hook_extra
     * @return mixed
     */
    public function verify_download($reply, string $package, $upgrader, array $hook_extra)
    {
        unset($upgrader, $hook_extra);
        if (false !== $reply) {
            return $reply;
        }
        $release = get_site_transient(self::CACHE);
        if (! is_array($release) || empty($release['version'])) {
            return false;
        }
        $product = null;
        foreach (array('theme' => self::THEME, 'plugin' => 'msp-nexus-core') as $key => $name) {
            if (hash_equals((string) $release[$key]['url'], $package)) {
                $product = array($name, (string) $release[$key]['sha256']);
            }
        }
        if (! $product) {
            return false;
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $file = download_url($package, 120);
        if (is_wp_error($file)) {
            return $file;
        }
        $actual = hash_file('sha256', $file);
        if (! is_string($actual) || ! hash_equals($product[1], strtolower($actual))) {
            wp_delete_file($file);
            return new \WP_Error('msp_nexus_hash_mismatch', __('The MSP Nexus update package does not match the digest published with its release, so it was not installed.', 'msp-nexus-core'));
        }
        if (! $this->archive_is($file, $product[0])) {
            wp_delete_file($file);
            return new \WP_Error('msp_nexus_identity_mismatch', __('The MSP Nexus update package does not contain the expected product, so it was not installed.', 'msp-nexus-core'));
        }
        return $file;
    }

    private function archive_is(string $file, string $product): bool
    {
        if (! class_exists('ZipArchive')) {
            return false;
        }
        $zip = new \ZipArchive();
        if (true !== $zip->open($file)) {
            return false;
        }
        $required = self::THEME === $product ? 'msp-nexus/style.css' : self::PLUGIN;
        $valid = false !== $zip->locateName($required);
        $zip->close();
        return $valid;
    }

    /**
     * Details for the "View details" link on the Plugins screen.
     *
     * @param mixed $result @param mixed $args
     * @return mixed
     */
    public function plugin_info($result, string $action, $args)
    {
        if ('plugin_information' !== $action || ! is_object($args) || 'msp-nexus-core' !== ($args->slug ?? '')) {
            return $result;
        }
        $release = $this->release();
        if (! $release) {
            return $result;
        }
        return (object) array(
            'name' => 'MSP Nexus Core',
            'slug' => 'msp-nexus-core',
            'version' => $release['version'],
            'author' => 'MSP Nexus',
            'homepage' => $release['html_url'],
            'requires' => '6.7',
            'requires_php' => '7.4.33',
            'tested' => $release['tested'],
            'last_updated' => $release['published'],
            'download_link' => $release['plugin']['url'],
            'sections' => array('changelog' => wpautop(esc_html($release['notes'])) . '<p><a href="' . esc_url($release['html_url']) . '">' . esc_html__('Full release notes', 'msp-nexus-core') . '</a></p>'),
        );
    }

    /** @param mixed $update @param mixed $item @return mixed */
    public function allow_plugin_auto_update($update, $item)
    {
        $plugin = is_object($item) ? (string) ($item->plugin ?? '') : '';
        return self::PLUGIN === $plugin && $this->automatic_updates_enabled() ? true : $update;
    }

    /** @param mixed $update @param mixed $item @return mixed */
    public function allow_theme_auto_update($update, $item)
    {
        $theme = is_object($item) ? (string) ($item->theme ?? '') : '';
        return self::THEME === $theme && $this->automatic_updates_enabled() ? true : $update;
    }

    private function automatic_updates_enabled(): bool
    {
        $settings = get_option('msp_nexus_settings', array());
        return is_array($settings) && ! empty($settings['automatic_updates']);
    }

    public function clear_cache(): void
    {
        delete_site_transient(self::CACHE);
    }
}
