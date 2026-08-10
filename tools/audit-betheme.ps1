param(
    [Parameter(Mandatory = $true)]
    [string] $PackageRoot,

    [Parameter(Mandatory = $true)]
    [string] $OutputRoot
)

$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.IO.Compression.FileSystem

$null = New-Item -ItemType Directory -Path $OutputRoot -Force

$textExtensions = @(
    '.php', '.phtml', '.inc', '.js', '.mjs', '.cjs', '.jsx', '.ts', '.tsx',
    '.css', '.scss', '.sass', '.less', '.json', '.xml', '.html', '.htm',
    '.txt', '.md', '.markdown', '.pot', '.po', '.mo-source', '.svg', '.yml',
    '.yaml', '.ini', '.conf', '.config', '.csv', '.tsv', '.sql', '.sh',
    '.ps1', '.bat', '.cmd', '.htaccess'
)

$featurePatterns = [ordered]@{
    'licensing'       = 'license|licence|purchase[_ -]?code|register(ed|ation)?|activation|deactivation'
    'updates'         = 'update|upgrade|changelog|version[_ -]?check|transient'
    'api_remote'      = 'wp_remote_|curl_|api\.|rest_|register_rest_route|ajax'
    'security'        = 'nonce|current_user_can|sanitize_|esc_|validate|permission_callback|prepared|prepare\('
    'builder'         = 'builder|bebuilder|visual[_ -]?builder|layout|section|wrap|preset|revision'
    'dynamic_data'    = 'dynamic[_ -]?data|post[_ -]?meta|custom[_ -]?field|placeholder|query[_ -]?loop'
    'theme_options'   = 'theme[_ -]?options|mfn_opts|customize_register|settings_fields|register_setting'
    'import_export'   = 'import|export|wxr|demo[_ -]?data|starter[_ -]?content'
    'performance'     = 'performance|defer|async|lazy|preload|minif|cache|critical[_ -]?css|webp'
    'accessibility'   = 'accessib|aria-|screen-reader|skip[_ -]?link|reduced-motion|keyboard|focus-visible'
    'privacy_gdpr'    = 'gdpr|privacy|cookie|consent|do[_ -]?not[_ -]?track'
    'seo_schema'      = 'seo|schema|json-ld|breadcrumb|open[_ -]?graph|canonical|meta[_ -]?description'
    'responsive'      = 'responsive|breakpoint|mobile|tablet|desktop|media query|@media'
    'woocommerce'     = 'woocommerce|wc_|product|cart|checkout|shop'
    'elementor'       = 'elementor'
    'visual_composer' = 'visual[_ -]?composer|wpbakery|vc_'
    'wpml_i18n'       = 'wpml|polylang|load_theme_textdomain|__\(|_e\(|esc_html__|rtl|translation'
    'menus_headers'   = 'mega[_ -]?menu|nav_menu|header|sticky|sidemenu|action[_ -]?bar'
    'templates'       = 'template|template_part|header|footer|archive|single|taxonomy'
    'post_types'      = 'register_post_type|register_taxonomy|post[_ -]?type|portfolio|testimonial|client'
    'forms_search'    = 'form|search|live[_ -]?search|contact|input|textarea|select'
    'media_motion'    = 'slider|carousel|lottie|video|audio|gallery|lightbox|parallax|animation'
    'admin_onboarding'= 'dashboard|setup|wizard|status|support|admin_menu|welcome'
    'white_label'     = 'white[_ -]?label|branding'
    'multisite'       = 'multisite|is_multisite|network_admin'
}

$manifest = [System.Collections.Generic.List[object]]::new()
$symbols = [System.Collections.Generic.List[object]]::new()
$hooks = [System.Collections.Generic.List[object]]::new()
$urls = [System.Collections.Generic.List[object]]::new()
$featureHits = [System.Collections.Generic.List[object]]::new()

function Get-Sha256Hex {
    param([byte[]] $Bytes)
    $sha = [System.Security.Cryptography.SHA256]::Create()
    try {
        return ([System.BitConverter]::ToString($sha.ComputeHash($Bytes))).Replace('-', '').ToLowerInvariant()
    }
    finally {
        $sha.Dispose()
    }
}

function Get-Subsystem {
    param([string] $Path)
    $normalized = $Path.Replace('\', '/')
    if ($normalized -match '(^|/)visual-builder/') { return 'visual-builder' }
    if ($normalized -match '(^|/)muffin-options/') { return 'theme-options' }
    if ($normalized -match '(^|/)functions/builder/') { return 'builder-engine' }
    if ($normalized -match '(^|/)functions/importer/') { return 'importer' }
    if ($normalized -match '(^|/)functions/admin/') { return 'admin-dashboard' }
    if ($normalized -match '(^|/)functions/plugins/elementor/') { return 'elementor' }
    if ($normalized -match '(^|/)functions/plugins/') { return 'plugin-integrations' }
    if ($normalized -match '(^|/)functions/post-types/') { return 'post-types' }
    if ($normalized -match '(^|/)functions/widgets/') { return 'widgets' }
    if ($normalized -match '(^|/)functions/modules/') { return 'modules' }
    if ($normalized -match '(^|/)woocommerce/') { return 'woocommerce-overrides' }
    if ($normalized -match '(^|/)bbpress/') { return 'bbpress-overrides' }
    if ($normalized -match '(^|/)tribe-events/') { return 'events-overrides' }
    if ($normalized -match '(^|/)includes/') { return 'template-includes' }
    if ($normalized -match '(^|/)vc_templates/') { return 'visual-composer' }
    if ($normalized -match '(^|/)languages/') { return 'localization' }
    if ($normalized -match '(^|/)(css|js|assets|fonts|images)/') { return 'front-end-assets' }
    if ($normalized -match 'slider revolution demos') { return 'slider-demo-packages' }
    if ($normalized -match 'Licensing') { return 'licensing-documents' }
    if ($normalized -match 'documentation') { return 'documentation' }
    if ($normalized -match 'child') { return 'child-theme' }
    return 'theme-root'
}

function Test-ReadableText {
    param(
        [string] $Name,
        [byte[]] $Bytes
    )
    $extension = [System.IO.Path]::GetExtension($Name).ToLowerInvariant()
    if ($textExtensions -contains $extension) { return $true }
    if ($Bytes.Length -eq 0) { return $false }
    $sampleLength = [Math]::Min($Bytes.Length, 4096)
    for ($i = 0; $i -lt $sampleLength; $i++) {
        if ($Bytes[$i] -eq 0) { return $false }
    }
    $printable = 0
    for ($i = 0; $i -lt $sampleLength; $i++) {
        $b = $Bytes[$i]
        if (($b -ge 9 -and $b -le 13) -or ($b -ge 32 -and $b -le 126) -or $b -ge 128) {
            $printable++
        }
    }
    return (($printable / $sampleLength) -ge 0.92)
}

function Convert-BytesToText {
    param([byte[]] $Bytes)
    $utf8 = [System.Text.UTF8Encoding]::new($false, $false)
    return $utf8.GetString($Bytes)
}

function Analyze-Entry {
    param(
        [string] $Container,
        [string] $EntryPath,
        [byte[]] $Bytes
    )

    if ($null -eq $Bytes) { $Bytes = [byte[]]::new(0) }
    $isText = Test-ReadableText -Name $EntryPath -Bytes $Bytes
    $extension = [System.IO.Path]::GetExtension($EntryPath).ToLowerInvariant()
    $subsystem = Get-Subsystem -Path ("$Container/$EntryPath")
    $lineCount = 0
    $nonEmptyLines = 0
    $maxLineLength = 0
    $text = $null

    if ($isText) {
        $text = Convert-BytesToText -Bytes $Bytes
        $reader = [System.IO.StringReader]::new($text)
        try {
            while (($line = $reader.ReadLine()) -ne $null) {
                $lineCount++
                if (-not [string]::IsNullOrWhiteSpace($line)) { $nonEmptyLines++ }
                if ($line.Length -gt $maxLineLength) { $maxLineLength = $line.Length }
            }
        }
        finally {
            $reader.Dispose()
        }

        foreach ($patternName in $featurePatterns.Keys) {
            $matches = [regex]::Matches($text, $featurePatterns[$patternName], [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
            if ($matches.Count -gt 0) {
                $featureHits.Add([pscustomobject]@{
                    Container = $Container
                    EntryPath = $EntryPath
                    Subsystem = $subsystem
                    Feature = $patternName
                    Hits = $matches.Count
                })
            }
        }

        if ($extension -in @('.php', '.phtml', '.inc')) {
            foreach ($m in [regex]::Matches($text, '(?m)^\s*(?:abstract\s+|final\s+)?class\s+([A-Za-z_][A-Za-z0-9_]*)')) {
                $symbols.Add([pscustomobject]@{ Container=$Container; EntryPath=$EntryPath; Kind='class'; Name=$m.Groups[1].Value })
            }
            foreach ($m in [regex]::Matches($text, '(?m)^\s*(?:public\s+|protected\s+|private\s+|static\s+|final\s+|abstract\s+)*function\s+&?\s*([A-Za-z_][A-Za-z0-9_]*)\s*\(')) {
                $symbols.Add([pscustomobject]@{ Container=$Container; EntryPath=$EntryPath; Kind='function'; Name=$m.Groups[1].Value })
            }
            foreach ($m in [regex]::Matches($text, '(?:add_action|add_filter|do_action|apply_filters)\s*\(\s*["'']([^"'']+)["'']')) {
                $hooks.Add([pscustomobject]@{ Container=$Container; EntryPath=$EntryPath; Hook=$m.Groups[1].Value; Context=$m.Value.Split('(')[0] })
            }
        }

        foreach ($m in [regex]::Matches($text, 'https?://[^\s"''<>\)]+', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)) {
            $urlValue = $m.Value.TrimEnd('.', ',', ';', ':', ']', '}')
            $hostValue = ''
            try { $hostValue = ([uri]$urlValue).Host } catch { }
            if ($hostValue) {
                $urls.Add([pscustomobject]@{ Container=$Container; EntryPath=$EntryPath; Host=$hostValue.ToLowerInvariant(); Url=$urlValue })
            }
        }
    }

    $manifest.Add([pscustomobject]@{
        Container = $Container
        EntryPath = $EntryPath
        Subsystem = $subsystem
        Extension = $extension
        Bytes = $Bytes.Length
        Sha256 = Get-Sha256Hex -Bytes $Bytes
        IsReadableText = $isText
        Lines = $lineCount
        NonEmptyLines = $nonEmptyLines
        MaxLineLength = $maxLineLength
    })
}

function Read-StreamBytes {
    param([System.IO.Stream] $Stream)
    $memory = [System.IO.MemoryStream]::new()
    try {
        $Stream.CopyTo($memory)
        return ,$memory.ToArray()
    }
    finally {
        $memory.Dispose()
    }
}

$looseFiles = Get-ChildItem -LiteralPath $PackageRoot -Recurse -File -Force | Where-Object { $_.Extension -ne '.zip' }
foreach ($file in $looseFiles) {
    $relative = $file.FullName.Substring($PackageRoot.Length).TrimStart('\')
    Analyze-Entry -Container 'loose-files' -EntryPath $relative -Bytes ([System.IO.File]::ReadAllBytes($file.FullName))
}

$archives = Get-ChildItem -LiteralPath $PackageRoot -Recurse -File -Filter '*.zip' -Force
foreach ($archiveFile in $archives) {
    $relativeArchive = $archiveFile.FullName.Substring($PackageRoot.Length).TrimStart('\')
    $archive = [System.IO.Compression.ZipFile]::OpenRead($archiveFile.FullName)
    try {
        foreach ($entry in $archive.Entries) {
            if ([string]::IsNullOrEmpty($entry.Name)) { continue }
            $stream = $entry.Open()
            try {
                $bytes = Read-StreamBytes -Stream $stream
            }
            finally {
                $stream.Dispose()
            }
            Analyze-Entry -Container $relativeArchive -EntryPath $entry.FullName -Bytes $bytes
        }
    }
    finally {
        $archive.Dispose()
    }
}

$manifestPath = Join-Path $OutputRoot 'betheme-coverage-manifest.csv'
$featurePath = Join-Path $OutputRoot 'betheme-feature-hits.csv'
$symbolPath = Join-Path $OutputRoot 'betheme-symbol-inventory.csv'
$hookPath = Join-Path $OutputRoot 'betheme-hook-inventory.csv'
$urlPath = Join-Path $OutputRoot 'betheme-external-url-inventory.csv'

$manifest | Sort-Object Container, EntryPath | Export-Csv -LiteralPath $manifestPath -NoTypeInformation -Encoding utf8
$featureHits | Sort-Object Feature, Container, EntryPath | Export-Csv -LiteralPath $featurePath -NoTypeInformation -Encoding utf8
$symbols | Sort-Object Kind, Name, Container, EntryPath | Export-Csv -LiteralPath $symbolPath -NoTypeInformation -Encoding utf8
$hooks | Sort-Object Hook, Context, Container, EntryPath | Export-Csv -LiteralPath $hookPath -NoTypeInformation -Encoding utf8
$urls | Sort-Object Host, Url, Container, EntryPath -Unique | Export-Csv -LiteralPath $urlPath -NoTypeInformation -Encoding utf8

$summary = [ordered]@{
    packageRoot = $PackageRoot
    generatedAt = (Get-Date).ToString('o')
    archives = $archives.Count
    looseFiles = $looseFiles.Count
    totalEntries = $manifest.Count
    readableTextEntries = @($manifest | Where-Object IsReadableText).Count
    binaryEntries = @($manifest | Where-Object { -not $_.IsReadableText }).Count
    totalBytes = ($manifest | Measure-Object Bytes -Sum).Sum
    readableTextBytes = ($manifest | Where-Object IsReadableText | Measure-Object Bytes -Sum).Sum
    sourceLines = ($manifest | Measure-Object Lines -Sum).Sum
    nonEmptySourceLines = ($manifest | Measure-Object NonEmptyLines -Sum).Sum
    symbols = $symbols.Count
    hooks = $hooks.Count
    externalUrls = $urls.Count
    byContainer = @($manifest | Group-Object Container | Sort-Object Name | ForEach-Object {
        [ordered]@{
            name = $_.Name
            files = $_.Count
            textFiles = @($_.Group | Where-Object IsReadableText).Count
            lines = ($_.Group | Measure-Object Lines -Sum).Sum
            bytes = ($_.Group | Measure-Object Bytes -Sum).Sum
        }
    })
    bySubsystem = @($manifest | Group-Object Subsystem | Sort-Object Name | ForEach-Object {
        [ordered]@{
            name = $_.Name
            files = $_.Count
            textFiles = @($_.Group | Where-Object IsReadableText).Count
            lines = ($_.Group | Measure-Object Lines -Sum).Sum
            bytes = ($_.Group | Measure-Object Bytes -Sum).Sum
        }
    })
    byExtension = @($manifest | Group-Object Extension | Sort-Object Count -Descending | ForEach-Object {
        [ordered]@{
            extension = $_.Name
            files = $_.Count
            textFiles = @($_.Group | Where-Object IsReadableText).Count
            lines = ($_.Group | Measure-Object Lines -Sum).Sum
            bytes = ($_.Group | Measure-Object Bytes -Sum).Sum
        }
    })
    byFeature = @($featureHits | Group-Object Feature | Sort-Object Name | ForEach-Object {
        [ordered]@{
            feature = $_.Name
            files = @($_.Group.EntryPath | Sort-Object -Unique).Count
            hits = ($_.Group | Measure-Object Hits -Sum).Sum
        }
    })
    externalHosts = @($urls | Group-Object Host | Sort-Object Count -Descending | Select-Object -First 100 | ForEach-Object {
        [ordered]@{ host=$_.Name; references=$_.Count }
    })
}

$summary | ConvertTo-Json -Depth 8 | Set-Content -LiteralPath (Join-Path $OutputRoot 'betheme-audit-summary.json') -Encoding utf8

$summaryLines = [System.Collections.Generic.List[string]]::new()
$summaryLines.Add('# Betheme Package Coverage Summary')
$summaryLines.Add('')
$summaryLines.Add("- Archives scanned: $($summary.archives)")
$summaryLines.Add("- Loose files scanned: $($summary.looseFiles)")
$summaryLines.Add("- Total file entries inventoried: $($summary.totalEntries)")
$summaryLines.Add("- Readable text/source entries: $($summary.readableTextEntries)")
$summaryLines.Add("- Binary entries: $($summary.binaryEntries)")
$summaryLines.Add("- Readable source lines scanned: $($summary.sourceLines)")
$summaryLines.Add("- Non-empty readable source lines: $($summary.nonEmptySourceLines)")
$summaryLines.Add("- PHP classes/functions detected: $($summary.symbols)")
$summaryLines.Add("- WordPress hook references detected: $($summary.hooks)")
$summaryLines.Add('')
$summaryLines.Add('## Subsystem coverage')
$summaryLines.Add('')
$summaryLines.Add('| Subsystem | Files | Text files | Lines | Bytes |')
$summaryLines.Add('|---|---:|---:|---:|---:|')
foreach ($item in $summary.bySubsystem) {
    $summaryLines.Add("| $($item.name) | $($item.files) | $($item.textFiles) | $($item.lines) | $($item.bytes) |")
}
$summaryLines.Add('')
$summaryLines.Add('## Feature signal coverage')
$summaryLines.Add('')
$summaryLines.Add('| Feature | Files | Pattern hits |')
$summaryLines.Add('|---|---:|---:|')
foreach ($item in $summary.byFeature) {
    $summaryLines.Add("| $($item.feature) | $($item.files) | $($item.hits) |")
}
$summaryLines.Add('')
$summaryLines.Add('The CSV manifest contains one record per loose file or archive entry, including SHA-256, byte count, text/binary classification, line counts, extension, and subsystem classification.')
$summaryLines | Set-Content -LiteralPath (Join-Path $OutputRoot 'BETHEME_COVERAGE_SUMMARY.md') -Encoding utf8

Write-Output ($summary | ConvertTo-Json -Depth 4)
