<?php
/**
 * Elementor widgets that render native Nexus dynamic blocks.
 *
 * Loaded only after Elementor has initialized Widget_Base.
 *
 * @package MspNexusCore
 */

declare(strict_types=1);

namespace MspNexusCore\Integrations\Elementor;

abstract class BlockWidget extends \Elementor\Widget_Base
{
    public function get_categories(): array
    {
        return array('msp-nexus');
    }

    public function get_icon(): string
    {
        return 'eicon-wordpress';
    }

    /** @return array<string, mixed> */
    abstract protected function block_attributes(array $settings): array;

    abstract protected function block_name(): string;

    protected function render(): void
    {
        echo render_block(array('blockName' => $this->block_name(), 'attrs' => $this->block_attributes($this->get_settings_for_display()), 'innerBlocks' => array(), 'innerHTML' => '', 'innerContent' => array())); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    protected function number_control(string $key, string $label, int $default, int $min, int $max): void
    {
        $this->add_control($key, array('label' => $label, 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => $default, 'min' => $min, 'max' => $max));
    }
}

final class ServiceGridWidget extends BlockWidget
{
    public function get_name(): string { return 'msp-nexus-service-grid'; }
    public function get_title(): string { return __('Nexus Service Grid', 'msp-nexus-core'); }
    protected function block_name(): string { return 'msp-nexus/service-grid'; }
    protected function register_controls(): void { $this->start_controls_section('content', array('label' => __('Content', 'msp-nexus-core'))); $this->number_control('count', __('Services', 'msp-nexus-core'), 6, 1, 12); $this->number_control('columns', __('Columns', 'msp-nexus-core'), 3, 1, 4); $this->add_control('showExcerpt', array('label' => __('Show excerpts', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'default' => 'yes')); $this->end_controls_section(); }
    protected function block_attributes(array $settings): array { return array('count' => (int) $settings['count'], 'columns' => (int) $settings['columns'], 'showExcerpt' => 'yes' === $settings['showExcerpt']); }
}

final class FaqWidget extends BlockWidget
{
    public function get_name(): string { return 'msp-nexus-faq'; }
    public function get_title(): string { return __('Nexus FAQ', 'msp-nexus-core'); }
    protected function block_name(): string { return 'msp-nexus/faq-list'; }
    protected function register_controls(): void { $this->start_controls_section('content', array('label' => __('Content', 'msp-nexus-core'))); $this->number_control('count', __('Questions', 'msp-nexus-core'), 8, 1, 20); $this->add_control('openFirst', array('label' => __('Open first question', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::SWITCHER)); $this->end_controls_section(); }
    protected function block_attributes(array $settings): array { return array('count' => (int) $settings['count'], 'openFirst' => 'yes' === ($settings['openFirst'] ?? '')); }
}

final class ConsultationWidget extends BlockWidget
{
    public function get_name(): string { return 'msp-nexus-consultation'; }
    public function get_title(): string { return __('Nexus Consultation Form', 'msp-nexus-core'); }
    protected function block_name(): string { return 'msp-nexus/consultation-form'; }
    protected function register_controls(): void {}
    protected function block_attributes(array $settings): array { unset($settings); return array(); }
}

final class DynamicValueWidget extends BlockWidget
{
    public function get_name(): string { return 'msp-nexus-dynamic-value'; }
    public function get_title(): string { return __('Nexus Dynamic Value', 'msp-nexus-core'); }
    protected function block_name(): string { return 'msp-nexus/dynamic-value'; }
    protected function register_controls(): void { $this->start_controls_section('content', array('label' => __('Dynamic source', 'msp-nexus-core'))); $this->add_control('source', array('label' => __('Source', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'post')); $this->add_control('field', array('label' => __('Field', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'title')); $this->add_control('fallback', array('label' => __('Fallback', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::TEXT)); $this->end_controls_section(); }
    protected function block_attributes(array $settings): array { return array('source' => sanitize_key((string) $settings['source']), 'field' => sanitize_key((string) $settings['field']), 'fallback' => (string) $settings['fallback']); }
}

final class ContentLoopWidget extends BlockWidget
{
    public function get_name(): string { return 'msp-nexus-content-loop'; }
    public function get_title(): string { return __('Nexus Content Loop', 'msp-nexus-core'); }
    protected function block_name(): string { return 'msp-nexus/content-loop'; }
    protected function register_controls(): void { $this->start_controls_section('query', array('label' => __('Query', 'msp-nexus-core'))); $this->add_control('postType', array('label' => __('Post type', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'post')); $this->number_control('count', __('Items', 'msp-nexus-core'), 6, 1, 24); $this->number_control('columns', __('Columns', 'msp-nexus-core'), 3, 1, 4); $this->add_control('taxonomy', array('label' => __('Taxonomy', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::TEXT)); $this->add_control('terms', array('label' => __('Term slugs', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::TEXT)); $this->end_controls_section(); }
    protected function block_attributes(array $settings): array { return array('postType' => sanitize_key((string) $settings['postType']), 'count' => (int) $settings['count'], 'columns' => (int) $settings['columns'], 'taxonomy' => sanitize_key((string) $settings['taxonomy']), 'terms' => sanitize_text_field((string) $settings['terms'])); }
}

final class WooTemplateWidget extends BlockWidget
{
    public function get_name(): string { return 'msp-nexus-woo-template'; }
    public function get_title(): string { return __('Nexus Woo Template Element', 'msp-nexus-core'); }
    protected function block_name(): string { return 'msp-nexus/woocommerce-element'; }
    protected function register_controls(): void { $this->start_controls_section('content', array('label' => __('WooCommerce element', 'msp-nexus-core'))); $this->add_control('element', array('label' => __('Element key', 'msp-nexus-core'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'product-title', 'options' => array('shop-products' => __('Shop products', 'msp-nexus-core'), 'product-title' => __('Product title', 'msp-nexus-core'), 'product-images' => __('Product images', 'msp-nexus-core'), 'product-price' => __('Product price', 'msp-nexus-core'), 'add-to-cart' => __('Add to cart', 'msp-nexus-core'), 'product-tabs' => __('Product tabs', 'msp-nexus-core'), 'related-products' => __('Related products', 'msp-nexus-core'), 'cart' => __('Cart', 'msp-nexus-core'), 'checkout' => __('Checkout', 'msp-nexus-core'), 'account' => __('Account', 'msp-nexus-core')))); $this->end_controls_section(); }
    protected function block_attributes(array $settings): array { return array('element' => sanitize_key((string) $settings['element'])); }
}
