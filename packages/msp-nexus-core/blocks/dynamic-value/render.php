<?php
/**
 * Dynamic data value renderer.
 *
 * @var array<string, mixed> $attributes
 */
$source = sanitize_key((string) ($attributes['source'] ?? 'post'));
$field = sanitize_key((string) ($attributes['field'] ?? 'title'));
$post_id = (int) get_the_ID();
$data = (new MspNexusCore\Content\DynamicData())->resolve($source, $field, $post_id);
$value = (string) ($data['value'] ?? '');
if ('' === trim($value)) {
    $value = sanitize_text_field((string) ($attributes['fallback'] ?? ''));
}
$output_type = sanitize_key((string) ($attributes['outputType'] ?? 'auto'));
if ('auto' === $output_type) {
    $output_type = in_array(($data['kind'] ?? ''), array('image', 'url'), true) ? (string) $data['kind'] : 'text';
}
if ('' === trim($value) && 'image' !== $output_type) {
    return;
}
$link_to = sanitize_key((string) ($attributes['linkTo'] ?? 'none'));
$url = 'custom' === $link_to ? esc_url((string) ($attributes['customUrl'] ?? '')) : esc_url((string) ($data['url'] ?? ''));
$content = '';
if ('image' === $output_type) {
    $size = sanitize_key((string) ($attributes['imageSize'] ?? 'large'));
    if (! in_array($size, array('thumbnail', 'medium', 'medium_large', 'large', 'full'), true)) {
        $size = 'large';
    }
    $media_id = (int) ($data['mediaId'] ?? 0);
    $alt = sanitize_text_field((string) ($attributes['altText'] ?? ''));
    if ($media_id > 0) {
        $image_attributes = array('loading' => 'lazy', 'decoding' => 'async');
        if ('' !== $alt) {
            $image_attributes['alt'] = $alt;
        }
        $content = (string) wp_get_attachment_image($media_id, $size, false, $image_attributes);
    } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
        $content = '<img src="' . esc_url($value) . '" alt="' . esc_attr($alt) . '" loading="lazy" decoding="async">';
    }
    if ('' === $content) {
        return;
    }
} elseif ('url' === $output_type) {
    $content = esc_html($value);
    if ('' === $url && filter_var($value, FILTER_VALIDATE_URL)) {
        $url = esc_url($value);
    }
} else {
    $content = esc_html((string) ($attributes['prefix'] ?? '')) . esc_html($value) . esc_html((string) ($attributes['suffix'] ?? ''));
}
if (in_array($link_to, array('auto', 'custom'), true) && '' !== $url) {
    $content = '<a href="' . $url . '">' . $content . '</a>';
}
$tag = sanitize_key((string) ($attributes['tagName'] ?? 'span'));
if ('image' === $output_type) {
    $tag = 'figure';
} elseif (! in_array($tag, array('span', 'p', 'div', 'h2', 'h3', 'h4'), true)) {
    $tag = 'span';
}
echo '<' . esc_attr($tag) . ' ' . get_block_wrapper_attributes(array('class' => 'msp-nexus-dynamic-value is-' . $output_type)) . '>' . $content . '</' . esc_attr($tag) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
