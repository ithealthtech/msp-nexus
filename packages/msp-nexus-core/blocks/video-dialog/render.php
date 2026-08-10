<?php
/** @var array<string, mixed> $attributes */
$video_url = esc_url_raw((string) ($attributes['videoUrl'] ?? ''));
if ('' === $video_url) {
    if (is_admin()) {
        echo '<p ' . get_block_wrapper_attributes() . '>' . esc_html__('Add a video URL to configure this dialog.', 'msp-nexus-core') . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    return;
}
$id = 'msp-nexus-video-' . substr(hash('sha256', $video_url), 0, 12);
$is_file = (bool) preg_match('/\.(?:mp4|webm|ogg)(?:\?.*)?$/i', $video_url);
$host = strtolower((string) wp_parse_url($video_url, PHP_URL_HOST));
if (! $is_file && (false !== strpos($host, 'youtube.com') || false !== strpos($host, 'youtu.be'))) {
    $video_id = '';
    if (false !== strpos($host, 'youtu.be')) {
        $video_id = trim((string) wp_parse_url($video_url, PHP_URL_PATH), '/');
    } else {
        parse_str((string) wp_parse_url($video_url, PHP_URL_QUERY), $query);
        $video_id = (string) ($query['v'] ?? '');
        if ('' === $video_id && preg_match('#/(?:embed|shorts)/([A-Za-z0-9_-]+)#', $video_url, $matches)) {
            $video_id = (string) $matches[1];
        }
    }
    if (preg_match('/^[A-Za-z0-9_-]{6,20}$/', $video_id)) {
        $video_url = 'https://www.youtube-nocookie.com/embed/' . $video_id . '?autoplay=1';
    }
} elseif (! $is_file && false !== strpos($host, 'vimeo.com') && preg_match('#/([0-9]{6,12})(?:$|[?/])#', $video_url, $matches)) {
    $video_url = 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1';
}
$poster_style = '' !== (string) ($attributes['posterUrl'] ?? '') ? "--msp-video-poster:url('" . esc_url_raw((string) $attributes['posterUrl']) . "')" : '';
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-video-dialog', 'data-nexus-video' => '', 'style' => $poster_style)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><button type="button" class="msp-nexus-video-dialog__trigger" aria-controls="<?php echo esc_attr($id); ?>"><span aria-hidden="true">&#9654;</span><strong><?php echo esc_html((string) ($attributes['buttonLabel'] ?? '')); ?></strong></button><dialog id="<?php echo esc_attr($id); ?>"><button type="button" data-video-close aria-label="<?php esc_attr_e('Close video', 'msp-nexus-core'); ?>">&times;</button><h2><?php echo esc_html((string) ($attributes['heading'] ?? '')); ?></h2><p class="msp-nexus-video-dialog__privacy"><?php echo esc_html((string) ($attributes['privacyNotice'] ?? '')); ?></p><div class="msp-nexus-video-dialog__frame" data-video-url="<?php echo esc_url($video_url); ?>" data-video-file="<?php echo $is_file ? '1' : '0'; ?>"></div></dialog></div>
