<?php
/** @var array<string, mixed> $attributes */
$header = array_slice(array_map('trim', explode('|', (string) ($attributes['columns'] ?? ''))), 0, 8);
$rows = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', (string) ($attributes['rows'] ?? '')) ?: array())));
if (count($header) < 2 || ! $rows) {
    return;
}
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'msp-nexus-comparison-table')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><div class="msp-nexus-comparison-table__scroll" tabindex="0"><table><caption><?php echo esc_html((string) ($attributes['caption'] ?? '')); ?></caption><thead><tr><?php foreach ($header as $cell) : ?><th scope="col"><?php echo esc_html($cell); ?></th><?php endforeach; ?></tr></thead><tbody><?php foreach (array_slice($rows, 0, 50) as $row) : $cells = array_slice(array_map('trim', explode('|', $row)), 0, count($header)); ?><tr><?php foreach ($header as $index => $unused) : $cell = (string) ($cells[$index] ?? ''); if (0 === $index && ! empty($attributes['firstColumnHeader'])) : ?><th scope="row"><?php echo esc_html($cell); ?></th><?php else : ?><td data-label="<?php echo esc_attr((string) $header[$index]); ?>"><?php echo esc_html($cell); ?></td><?php endif; endforeach; ?></tr><?php endforeach; ?></tbody></table></div></div>
