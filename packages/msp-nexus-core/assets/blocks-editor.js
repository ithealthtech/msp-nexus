(function (blocks, blockEditor, components, element, i18n, serverSideRender) {
  'use strict';

  var el = element.createElement;
  var useEffect = element.useEffect;
  var useState = element.useState;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var RangeControl = components.RangeControl;
  var ToggleControl = components.ToggleControl;
  var ServerSideRender = serverSideRender;
  var __ = i18n.__;

  blocks.registerBlockType('msp-nexus/service-grid', {
    edit: function (props) {
      return el(element.Fragment, {},
        el(InspectorControls, {},
          el(PanelBody, { title: __('Service grid settings', 'msp-nexus-core'), initialOpen: true },
            el(RangeControl, { label: __('Number of services', 'msp-nexus-core'), min: 1, max: 12, value: props.attributes.count, onChange: function (value) { props.setAttributes({ count: value }); } }),
            el(RangeControl, { label: __('Columns', 'msp-nexus-core'), min: 1, max: 4, value: props.attributes.columns, onChange: function (value) { props.setAttributes({ columns: value }); } }),
            el(ToggleControl, { label: __('Show excerpts', 'msp-nexus-core'), checked: props.attributes.showExcerpt, onChange: function (value) { props.setAttributes({ showExcerpt: value }); } })
          )
        ),
        el(ServerSideRender, { block: 'msp-nexus/service-grid', attributes: props.attributes })
      );
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/faq-list', {
    edit: function (props) {
      return el(element.Fragment, {},
        el(InspectorControls, {},
          el(PanelBody, { title: __('FAQ list settings', 'msp-nexus-core'), initialOpen: true },
            el(RangeControl, { label: __('Number of FAQs', 'msp-nexus-core'), min: 1, max: 20, value: props.attributes.count, onChange: function (value) { props.setAttributes({ count: value }); } }),
            el(ToggleControl, { label: __('Open the first question', 'msp-nexus-core'), checked: props.attributes.openFirst, onChange: function (value) { props.setAttributes({ openFirst: value }); } })
          )
        ),
        el(ServerSideRender, { block: 'msp-nexus/faq-list', attributes: props.attributes })
      );
    },
    save: function () { return null; }
  });

  ['msp-nexus/consultation-form', 'msp-nexus/breadcrumbs'].forEach(function (name) {
    blocks.registerBlockType(name, {
      edit: function (props) { return el(ServerSideRender, { block: name, attributes: props.attributes }); },
      save: function () { return null; }
    });
  });

  blocks.registerBlockType('msp-nexus/announcement-panel', {
    edit: function (props) {
      return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Announcement settings', 'msp-nexus-core'), initialOpen: true },
        el(components.SelectControl, { label: __('Presentation', 'msp-nexus-core'), value: props.attributes.mode, options: [{ label: __('Inline banner', 'msp-nexus-core'), value: 'banner' }, { label: __('Timed modal', 'msp-nexus-core'), value: 'modal' }], onChange: function (value) { props.setAttributes({ mode: value }); } }),
        el(components.TextControl, { label: __('Heading', 'msp-nexus-core'), value: props.attributes.heading, onChange: function (value) { props.setAttributes({ heading: value }); } }),
        el(components.TextareaControl, { label: __('Message', 'msp-nexus-core'), value: props.attributes.body, onChange: function (value) { props.setAttributes({ body: value }); } }),
        el(components.TextControl, { label: __('Button label', 'msp-nexus-core'), value: props.attributes.buttonLabel, onChange: function (value) { props.setAttributes({ buttonLabel: value }); } }),
        el(components.TextControl, { label: __('Button URL', 'msp-nexus-core'), value: props.attributes.buttonUrl, onChange: function (value) { props.setAttributes({ buttonUrl: value }); } }),
        el(RangeControl, { label: __('Delay in seconds', 'msp-nexus-core'), min: 0, max: 60, value: props.attributes.delaySeconds, onChange: function (value) { props.setAttributes({ delaySeconds: value }); } }),
        el(RangeControl, { label: __('Dismissal frequency in days', 'msp-nexus-core'), min: 0, max: 365, value: props.attributes.frequencyDays, onChange: function (value) { props.setAttributes({ frequencyDays: value }); } }),
        el(components.TextControl, { label: __('Required path prefix', 'msp-nexus-core'), value: props.attributes.includePath, onChange: function (value) { props.setAttributes({ includePath: value }); } }),
        el(components.TextControl, { label: __('Excluded referrer text', 'msp-nexus-core'), value: props.attributes.excludedReferrer, onChange: function (value) { props.setAttributes({ excludedReferrer: value }); } }),
        el(ToggleControl, { label: __('Require stored marketing consent', 'msp-nexus-core'), checked: props.attributes.requireConsent, onChange: function (value) { props.setAttributes({ requireConsent: value }); } })
      )), el(ServerSideRender, { block: 'msp-nexus/announcement-panel', attributes: props.attributes }));
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/content-directory', {
    edit: function (props) {
      return el(element.Fragment, {},
        el(InspectorControls, {}, el(PanelBody, { title: __('Directory settings', 'msp-nexus-core'), initialOpen: true },
          el(components.SelectControl, { label: __('Content type', 'msp-nexus-core'), value: props.attributes.contentType, options: [
            { label: __('Resources', 'msp-nexus-core'), value: 'msp_resource' }, { label: __('Case studies', 'msp-nexus-core'), value: 'msp_case_study' },
            { label: __('Business outcomes', 'msp-nexus-core'), value: 'msp_outcome' }, { label: __('Events', 'msp-nexus-core'), value: 'msp_event' },
            { label: __('Team', 'msp-nexus-core'), value: 'msp_team' }, { label: __('Locations', 'msp-nexus-core'), value: 'msp_location' }
          ], onChange: function (value) { props.setAttributes({ contentType: value }); } }),
          el(RangeControl, { label: __('Items per page', 'msp-nexus-core'), min: 1, max: 24, value: props.attributes.count, onChange: function (value) { props.setAttributes({ count: value }); } }),
          el(RangeControl, { label: __('Columns', 'msp-nexus-core'), min: 1, max: 4, value: props.attributes.columns, onChange: function (value) { props.setAttributes({ columns: value }); } }),
          el(ToggleControl, { label: __('Show filters', 'msp-nexus-core'), checked: props.attributes.showFilters, onChange: function (value) { props.setAttributes({ showFilters: value }); } })
        )), el(ServerSideRender, { block: 'msp-nexus/content-directory', attributes: props.attributes }));
    },
    save: function () { return null; }
  });

  function DynamicValueEdit(props) {
      var _schema = useState(null);
      var schema = _schema[0];
      var setSchema = _schema[1];
      useEffect(function () {
        var postType = 'post';
        if (window.wp.data && window.wp.data.select('core/editor')) postType = window.wp.data.select('core/editor').getCurrentPostType() || 'post';
        window.wp.apiFetch({ path: '/msp-nexus/v1/dynamic-fields?post_type=' + encodeURIComponent(postType) }).then(setSchema).catch(function () { setSchema({ sources: [] }); });
      }, []);
      var sources = schema && Array.isArray(schema.sources) ? schema.sources : [];
      var sourceOptions = sources.map(function (source) { return { label: source.label, value: source.value }; });
      var selectedSource = sources.filter(function (source) { return source.value === props.attributes.source; })[0];
      var fieldOptions = selectedSource && Array.isArray(selectedSource.fields) ? selectedSource.fields : [];
      return el(element.Fragment, {},
        el(InspectorControls, {}, el(PanelBody, { title: __('Dynamic value settings', 'msp-nexus-core'), initialOpen: true },
          sourceOptions.length ? el(components.SelectControl, { label: __('Source', 'msp-nexus-core'), value: props.attributes.source, options: sourceOptions, onChange: function (value) {
            var nextSource = sources.filter(function (source) { return source.value === value; })[0];
            props.setAttributes({ source: value, field: nextSource && nextSource.fields[0] ? nextSource.fields[0].value : '' });
          } }) : el(components.TextControl, { label: __('Source', 'msp-nexus-core'), value: props.attributes.source, onChange: function (value) { props.setAttributes({ source: value }); } }),
          fieldOptions.length ? el(components.SelectControl, { label: __('Dynamic field', 'msp-nexus-core'), value: props.attributes.field, options: fieldOptions, onChange: function (value) { props.setAttributes({ field: value }); } }) : el(components.TextControl, { label: __('Field key', 'msp-nexus-core'), value: props.attributes.field, onChange: function (value) { props.setAttributes({ field: value }); } }),
          el(components.SelectControl, { label: __('Output', 'msp-nexus-core'), value: props.attributes.outputType, options: [
            { label: __('Automatic', 'msp-nexus-core'), value: 'auto' }, { label: __('Text', 'msp-nexus-core'), value: 'text' },
            { label: __('Image', 'msp-nexus-core'), value: 'image' }, { label: __('URL', 'msp-nexus-core'), value: 'url' }
          ], onChange: function (value) { props.setAttributes({ outputType: value }); } }),
          el(components.TextControl, { label: __('Fallback', 'msp-nexus-core'), value: props.attributes.fallback, onChange: function (value) { props.setAttributes({ fallback: value }); } }),
          el(components.TextControl, { label: __('Prefix', 'msp-nexus-core'), value: props.attributes.prefix, onChange: function (value) { props.setAttributes({ prefix: value }); } }),
          el(components.TextControl, { label: __('Suffix', 'msp-nexus-core'), value: props.attributes.suffix, onChange: function (value) { props.setAttributes({ suffix: value }); } }),
          el(components.SelectControl, { label: __('HTML element', 'msp-nexus-core'), value: props.attributes.tagName, options: ['span', 'p', 'div', 'h2', 'h3', 'h4'].map(function (value) { return { label: value, value: value }; }), onChange: function (value) { props.setAttributes({ tagName: value }); } }),
          el(components.SelectControl, { label: __('Link', 'msp-nexus-core'), value: props.attributes.linkTo, options: [{ label: __('No link', 'msp-nexus-core'), value: 'none' }, { label: __('Automatic source link', 'msp-nexus-core'), value: 'auto' }, { label: __('Custom URL', 'msp-nexus-core'), value: 'custom' }], onChange: function (value) { props.setAttributes({ linkTo: value }); } }),
          props.attributes.linkTo === 'custom' ? el(components.TextControl, { label: __('Custom URL', 'msp-nexus-core'), value: props.attributes.customUrl, onChange: function (value) { props.setAttributes({ customUrl: value }); } }) : null,
          props.attributes.outputType === 'image' || props.attributes.outputType === 'auto' ? el(element.Fragment, {},
            el(components.SelectControl, { label: __('Image size', 'msp-nexus-core'), value: props.attributes.imageSize, options: ['thumbnail', 'medium', 'medium_large', 'large', 'full'].map(function (value) { return { label: value.replace('_', ' '), value: value }; }), onChange: function (value) { props.setAttributes({ imageSize: value }); } }),
            el(components.TextControl, { label: __('Image alt text override', 'msp-nexus-core'), help: __('Leave empty to use the Media Library alt text.', 'msp-nexus-core'), value: props.attributes.altText, onChange: function (value) { props.setAttributes({ altText: value }); } })
          ) : null
        )), el(ServerSideRender, { block: 'msp-nexus/dynamic-value', attributes: props.attributes }));
  }

  blocks.registerBlockType('msp-nexus/dynamic-value', {
    edit: DynamicValueEdit,
    save: function () { return null; }
  });

  function ContentLoopEdit(props) {
      var _options = useState({ postTypes: [], taxonomies: [] });
      var options = _options[0];
      var setOptions = _options[1];
      var _layouts = useState([]);
      var layouts = _layouts[0];
      var setLayouts = _layouts[1];
      var _metaFields = useState([]);
      var metaFields = _metaFields[0];
      var setMetaFields = _metaFields[1];
      useEffect(function () {
        window.Promise.all([window.wp.apiFetch({ path: '/msp-nexus/v1/query-options' }), window.wp.apiFetch({ path: '/msp-nexus/v1/loop-layouts' })]).then(function (values) { setOptions(values[0]); setLayouts(values[1]); }).catch(function () {});
      }, []);
      useEffect(function () {
        window.wp.apiFetch({ path: '/msp-nexus/v1/dynamic-fields?post_type=' + encodeURIComponent(props.attributes.postType) }).then(function (schema) {
          var source = (schema.sources || []).filter(function (item) { return item.value === 'post_meta'; })[0];
          setMetaFields(source ? source.fields : []);
        }).catch(function () { setMetaFields([]); });
      }, [props.attributes.postType]);
      var taxonomies = (options.taxonomies || []).filter(function (taxonomy) { return taxonomy.types.indexOf(props.attributes.postType) !== -1; });
      var taxonomyOptions = [{ label: __('None', 'msp-nexus-core'), value: '' }].concat(taxonomies.map(function (taxonomy) { return { label: taxonomy.label, value: taxonomy.value }; }));
      var layoutOptions = [{ label: __('Default card', 'msp-nexus-core'), value: '' }].concat(layouts);
      return el(element.Fragment, {},
        el(InspectorControls, {},
          el(PanelBody, { title: __('Content query', 'msp-nexus-core'), initialOpen: true },
          options.postTypes.length ? el(components.SelectControl, { label: __('Post type', 'msp-nexus-core'), value: props.attributes.postType, options: options.postTypes, onChange: function (value) { props.setAttributes({ postType: value, taxonomy: '', taxonomy2: '', relatedTaxonomy: '' }); } }) : el(components.TextControl, { label: __('Post type', 'msp-nexus-core'), value: props.attributes.postType, onChange: function (value) { props.setAttributes({ postType: value }); } }),
          el(components.SelectControl, { label: __('First taxonomy', 'msp-nexus-core'), value: props.attributes.taxonomy, options: taxonomyOptions, onChange: function (value) { props.setAttributes({ taxonomy: value }); } }),
          el(components.TextControl, { label: __('Term slugs', 'msp-nexus-core'), help: __('Separate multiple slugs with commas.', 'msp-nexus-core'), value: props.attributes.terms, onChange: function (value) { props.setAttributes({ terms: value }); } }),
          el(components.SelectControl, { label: __('Second taxonomy', 'msp-nexus-core'), value: props.attributes.taxonomy2, options: taxonomyOptions, onChange: function (value) { props.setAttributes({ taxonomy2: value }); } }),
          el(components.TextControl, { label: __('Second term slugs', 'msp-nexus-core'), value: props.attributes.terms2, onChange: function (value) { props.setAttributes({ terms2: value }); } }),
          el(components.SelectControl, { label: __('Taxonomy relationship', 'msp-nexus-core'), value: props.attributes.taxRelation, options: [{ label: __('Match all groups', 'msp-nexus-core'), value: 'AND' }, { label: __('Match any group', 'msp-nexus-core'), value: 'OR' }], onChange: function (value) { props.setAttributes({ taxRelation: value }); } }),
          el(components.SelectControl, { label: __('Related-content taxonomy', 'msp-nexus-core'), value: props.attributes.relatedTaxonomy, options: taxonomyOptions, onChange: function (value) { props.setAttributes({ relatedTaxonomy: value }); } }),
          metaFields.length ? el(components.SelectControl, { label: __('Custom-field filter', 'msp-nexus-core'), value: props.attributes.metaKey, options: [{ label: __('None', 'msp-nexus-core'), value: '' }].concat(metaFields), onChange: function (value) { props.setAttributes({ metaKey: value }); } }) : el(components.TextControl, { label: __('Custom-field key', 'msp-nexus-core'), value: props.attributes.metaKey, onChange: function (value) { props.setAttributes({ metaKey: value }); } }),
          el(components.SelectControl, { label: __('Custom-field comparison', 'msp-nexus-core'), value: props.attributes.metaCompare, options: ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS'].map(function (value) { return { label: value, value: value }; }), onChange: function (value) { props.setAttributes({ metaCompare: value }); } }),
          el(components.TextControl, { label: __('Custom-field value', 'msp-nexus-core'), value: props.attributes.metaValue, onChange: function (value) { props.setAttributes({ metaValue: value }); } }),
          el(components.TextControl, { label: __('Include post IDs', 'msp-nexus-core'), value: props.attributes.includeIds, onChange: function (value) { props.setAttributes({ includeIds: value }); } }),
          el(components.TextControl, { label: __('Exclude post IDs', 'msp-nexus-core'), value: props.attributes.excludeIds, onChange: function (value) { props.setAttributes({ excludeIds: value }); } }),
          el(components.TextControl, { label: __('Author IDs', 'msp-nexus-core'), value: props.attributes.authors, onChange: function (value) { props.setAttributes({ authors: value }); } }),
          el(RangeControl, { label: __('Offset', 'msp-nexus-core'), min: 0, max: 100, value: props.attributes.offset, onChange: function (value) { props.setAttributes({ offset: value }); } }),
          el(ToggleControl, { label: __('Exclude current post', 'msp-nexus-core'), checked: props.attributes.excludeCurrent, onChange: function (value) { props.setAttributes({ excludeCurrent: value }); } }),
          el(RangeControl, { label: __('Items', 'msp-nexus-core'), min: 1, max: 24, value: props.attributes.count, onChange: function (value) { props.setAttributes({ count: value }); } }),
          el(components.SelectControl, { label: __('Order by', 'msp-nexus-core'), value: props.attributes.orderBy, options: ['menu_order', 'date', 'title', 'modified', 'rand', 'comment_count', 'meta_value', 'meta_value_num'].map(function (value) { return { label: value.replace('_', ' '), value: value }; }), onChange: function (value) { props.setAttributes({ orderBy: value }); } }),
          el(components.SelectControl, { label: __('Order', 'msp-nexus-core'), value: props.attributes.order, options: [{ label: __('Ascending', 'msp-nexus-core'), value: 'ASC' }, { label: __('Descending', 'msp-nexus-core'), value: 'DESC' }], onChange: function (value) { props.setAttributes({ order: value }); } }),
          el(ToggleControl, { label: __('Show pagination', 'msp-nexus-core'), checked: props.attributes.showPagination, onChange: function (value) { props.setAttributes({ showPagination: value }); } })
          ),
          el(PanelBody, { title: __('Loop item design', 'msp-nexus-core'), initialOpen: true },
          el(components.SelectControl, { label: __('Visual loop-item layout', 'msp-nexus-core'), help: __('Create published Loop layouts under Nexus layouts, then select one here.', 'msp-nexus-core'), value: props.attributes.templateSlug, options: layoutOptions, onChange: function (value) { props.setAttributes({ templateSlug: value }); } }),
          el(components.SelectControl, { label: __('Presentation', 'msp-nexus-core'), value: props.attributes.layout, options: [{ label: __('Grid', 'msp-nexus-core'), value: 'grid' }, { label: __('List', 'msp-nexus-core'), value: 'list' }, { label: __('Masonry', 'msp-nexus-core'), value: 'masonry' }], onChange: function (value) { props.setAttributes({ layout: value }); } }),
          el(RangeControl, { label: __('Columns', 'msp-nexus-core'), min: 1, max: 4, value: props.attributes.columns, onChange: function (value) { props.setAttributes({ columns: value }); } }),
          el(ToggleControl, { label: __('Show featured images', 'msp-nexus-core'), checked: props.attributes.showImage, onChange: function (value) { props.setAttributes({ showImage: value }); } }),
          el(ToggleControl, { label: __('Show excerpts', 'msp-nexus-core'), checked: props.attributes.showExcerpt, onChange: function (value) { props.setAttributes({ showExcerpt: value }); } }),
          el(ToggleControl, { label: __('Show dates', 'msp-nexus-core'), checked: props.attributes.showMeta, onChange: function (value) { props.setAttributes({ showMeta: value }); } }),
          el(components.TextControl, { label: __('Link label', 'msp-nexus-core'), value: props.attributes.buttonLabel, onChange: function (value) { props.setAttributes({ buttonLabel: value }); } }),
          el(components.TextControl, { label: __('No-results message', 'msp-nexus-core'), value: props.attributes.noResultsText, onChange: function (value) { props.setAttributes({ noResultsText: value }); } })
          )
        ), el(ServerSideRender, { block: 'msp-nexus/content-loop', attributes: props.attributes }));
  }

  blocks.registerBlockType('msp-nexus/content-loop', {
    edit: ContentLoopEdit,
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/layout-region', {
    edit: function (props) {
      return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Layout region settings', 'msp-nexus-core'), initialOpen: true },
        el(components.TextControl, { label: __('Published layout slug', 'msp-nexus-core'), value: props.attributes.layoutSlug, onChange: function (value) { props.setAttributes({ layoutSlug: value }); } }),
        el(components.SelectControl, { label: __('Layout area', 'msp-nexus-core'), value: props.attributes.area, options: ['loop', 'mega_menu', 'header', 'footer', 'template', 'popup'].map(function (value) { return { label: value.replace('_', ' '), value: value }; }), onChange: function (value) { props.setAttributes({ area: value }); } })
      )), el(ServerSideRender, { block: 'msp-nexus/layout-region', attributes: props.attributes }));
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/menu-panel', {
    edit: function (props) {
      return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Mega menu settings', 'msp-nexus-core'), initialOpen: true },
        el(components.TextControl, { label: __('Menu label', 'msp-nexus-core'), value: props.attributes.label, onChange: function (value) { props.setAttributes({ label: value }); } }),
        el(components.TextControl, { label: __('Mega-menu layout slug', 'msp-nexus-core'), value: props.attributes.layoutSlug, onChange: function (value) { props.setAttributes({ layoutSlug: value }); } }),
        el(components.TextControl, { label: __('Panel width', 'msp-nexus-core'), value: props.attributes.width, onChange: function (value) { props.setAttributes({ width: value }); } }),
        el(components.SelectControl, { label: __('Panel alignment', 'msp-nexus-core'), value: props.attributes.alignment, options: [{ label: __('Start', 'msp-nexus-core'), value: 'start' }, { label: __('Center', 'msp-nexus-core'), value: 'center' }, { label: __('End', 'msp-nexus-core'), value: 'end' }], onChange: function (value) { props.setAttributes({ alignment: value }); } }),
        el(ToggleControl, { label: __('Open on pointer hover', 'msp-nexus-core'), checked: props.attributes.hoverOpen, onChange: function (value) { props.setAttributes({ hoverOpen: value }); } }),
        el(ToggleControl, { label: __('Close after outside click', 'msp-nexus-core'), checked: props.attributes.closeOutside, onChange: function (value) { props.setAttributes({ closeOutside: value }); } })
      )), el(ServerSideRender, { block: 'msp-nexus/menu-panel', attributes: props.attributes }));
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/offcanvas-menu', {
    edit: function (props) {
      return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Off-canvas menu settings', 'msp-nexus-core'), initialOpen: true },
        el(components.TextControl, { label: __('Button label', 'msp-nexus-core'), value: props.attributes.buttonLabel, onChange: function (value) { props.setAttributes({ buttonLabel: value }); } }),
        el(components.TextControl, { label: __('Mega-menu layout slug', 'msp-nexus-core'), value: props.attributes.layoutSlug, onChange: function (value) { props.setAttributes({ layoutSlug: value }); } }),
        el(components.SelectControl, { label: __('Panel position', 'msp-nexus-core'), value: props.attributes.position, options: [{ label: __('Right', 'msp-nexus-core'), value: 'right' }, { label: __('Left', 'msp-nexus-core'), value: 'left' }], onChange: function (value) { props.setAttributes({ position: value }); } }),
        el(components.TextControl, { label: __('Panel width', 'msp-nexus-core'), help: __('Use px, rem, vw, or %.', 'msp-nexus-core'), value: props.attributes.width, onChange: function (value) { props.setAttributes({ width: value }); } }),
        el(ToggleControl, { label: __('Show text label', 'msp-nexus-core'), checked: props.attributes.showLabel, onChange: function (value) { props.setAttributes({ showLabel: value }); } }),
        el(ToggleControl, { label: __('Close after navigation', 'msp-nexus-core'), checked: props.attributes.closeOnNavigate, onChange: function (value) { props.setAttributes({ closeOnNavigate: value }); } })
      )), el(ServerSideRender, { block: 'msp-nexus/offcanvas-menu', attributes: props.attributes }));
    },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/metric-counter', {
    edit: function (props) { return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Metric settings', 'msp-nexus-core'), initialOpen: true },
      el(components.TextControl, { label: __('Label', 'msp-nexus-core'), value: props.attributes.label, onChange: function (value) { props.setAttributes({ label: value }); } }),
      el(components.TextControl, { label: __('Value', 'msp-nexus-core'), type: 'number', value: props.attributes.value, onChange: function (value) { props.setAttributes({ value: Number(value) }); } }),
      el(RangeControl, { label: __('Decimal places', 'msp-nexus-core'), min: 0, max: 3, value: props.attributes.decimals, onChange: function (value) { props.setAttributes({ decimals: value }); } }),
      el(components.TextControl, { label: __('Prefix', 'msp-nexus-core'), value: props.attributes.prefix, onChange: function (value) { props.setAttributes({ prefix: value }); } }),
      el(components.TextControl, { label: __('Suffix', 'msp-nexus-core'), value: props.attributes.suffix, onChange: function (value) { props.setAttributes({ suffix: value }); } }),
      el(RangeControl, { label: __('Animation duration', 'msp-nexus-core'), min: 0, max: 5000, step: 100, value: props.attributes.duration, onChange: function (value) { props.setAttributes({ duration: value }); } })
    )), el(ServerSideRender, { block: 'msp-nexus/metric-counter', attributes: props.attributes })); },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/tabs', {
    edit: function (props) { return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Tab settings', 'msp-nexus-core'), initialOpen: true },
      el(components.TextareaControl, { label: __('Tabs', 'msp-nexus-core'), help: __('One tab per line: Label|Content', 'msp-nexus-core'), rows: 8, value: props.attributes.items, onChange: function (value) { props.setAttributes({ items: value }); } }),
      el(components.SelectControl, { label: __('Orientation', 'msp-nexus-core'), value: props.attributes.orientation, options: [{ label: __('Horizontal', 'msp-nexus-core'), value: 'horizontal' }, { label: __('Vertical', 'msp-nexus-core'), value: 'vertical' }], onChange: function (value) { props.setAttributes({ orientation: value }); } })
    )), el(ServerSideRender, { block: 'msp-nexus/tabs', attributes: props.attributes })); },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/comparison-table', {
    edit: function (props) { return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Comparison table settings', 'msp-nexus-core'), initialOpen: true },
      el(components.TextControl, { label: __('Caption', 'msp-nexus-core'), value: props.attributes.caption, onChange: function (value) { props.setAttributes({ caption: value }); } }),
      el(components.TextControl, { label: __('Column headings', 'msp-nexus-core'), help: __('Separate cells with |.', 'msp-nexus-core'), value: props.attributes.columns, onChange: function (value) { props.setAttributes({ columns: value }); } }),
      el(components.TextareaControl, { label: __('Rows', 'msp-nexus-core'), help: __('One row per line; separate cells with |.', 'msp-nexus-core'), rows: 8, value: props.attributes.rows, onChange: function (value) { props.setAttributes({ rows: value }); } }),
      el(ToggleControl, { label: __('Use first column as row headings', 'msp-nexus-core'), checked: props.attributes.firstColumnHeader, onChange: function (value) { props.setAttributes({ firstColumnHeader: value }); } })
    )), el(ServerSideRender, { block: 'msp-nexus/comparison-table', attributes: props.attributes })); },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/progress-meter', {
    edit: function (props) { return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Progress settings', 'msp-nexus-core'), initialOpen: true },
      el(components.TextControl, { label: __('Label', 'msp-nexus-core'), value: props.attributes.label, onChange: function (value) { props.setAttributes({ label: value }); } }),
      el(RangeControl, { label: __('Value', 'msp-nexus-core'), min: 0, max: 100, value: props.attributes.value, onChange: function (value) { props.setAttributes({ value: value }); } }),
      el(ToggleControl, { label: __('Show percentage', 'msp-nexus-core'), checked: props.attributes.showValue, onChange: function (value) { props.setAttributes({ showValue: value }); } }),
      el(components.TextControl, { label: __('Bar color', 'msp-nexus-core'), type: 'color', value: props.attributes.color, onChange: function (value) { props.setAttributes({ color: value }); } })
    )), el(ServerSideRender, { block: 'msp-nexus/progress-meter', attributes: props.attributes })); },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/icon-card', {
    edit: function (props) { return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Icon card settings', 'msp-nexus-core'), initialOpen: true },
      el(components.SelectControl, { label: __('Icon', 'msp-nexus-core'), value: props.attributes.icon, options: ['shield', 'cloud', 'support', 'network', 'strategy', 'identity', 'continuity', 'data'].map(function (value) { return { label: value, value: value }; }), onChange: function (value) { props.setAttributes({ icon: value }); } }),
      el(components.TextControl, { label: __('Heading', 'msp-nexus-core'), value: props.attributes.heading, onChange: function (value) { props.setAttributes({ heading: value }); } }),
      el(components.TextareaControl, { label: __('Body', 'msp-nexus-core'), value: props.attributes.body, onChange: function (value) { props.setAttributes({ body: value }); } }),
      el(components.TextControl, { label: __('Link label', 'msp-nexus-core'), value: props.attributes.linkLabel, onChange: function (value) { props.setAttributes({ linkLabel: value }); } }),
      el(components.TextControl, { label: __('Link URL', 'msp-nexus-core'), value: props.attributes.linkUrl, onChange: function (value) { props.setAttributes({ linkUrl: value }); } })
    )), el(ServerSideRender, { block: 'msp-nexus/icon-card', attributes: props.attributes })); },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/video-dialog', {
    edit: function (props) { return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Video dialog settings', 'msp-nexus-core'), initialOpen: true },
      el(components.TextControl, { label: __('Heading', 'msp-nexus-core'), value: props.attributes.heading, onChange: function (value) { props.setAttributes({ heading: value }); } }),
      el(components.TextControl, { label: __('Button label', 'msp-nexus-core'), value: props.attributes.buttonLabel, onChange: function (value) { props.setAttributes({ buttonLabel: value }); } }),
      el(components.TextControl, { label: __('Video or embed URL', 'msp-nexus-core'), value: props.attributes.videoUrl, onChange: function (value) { props.setAttributes({ videoUrl: value }); } }),
      el(components.TextControl, { label: __('Poster image URL', 'msp-nexus-core'), value: props.attributes.posterUrl, onChange: function (value) { props.setAttributes({ posterUrl: value }); } }),
      el(components.TextareaControl, { label: __('Privacy notice', 'msp-nexus-core'), value: props.attributes.privacyNotice, onChange: function (value) { props.setAttributes({ privacyNotice: value }); } })
    )), el(ServerSideRender, { block: 'msp-nexus/video-dialog', attributes: props.attributes })); },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/content-carousel', {
    edit: function (props) { return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Carousel settings', 'msp-nexus-core'), initialOpen: true },
      el(components.TextControl, { label: __('Heading', 'msp-nexus-core'), value: props.attributes.heading, onChange: function (value) { props.setAttributes({ heading: value }); } }),
      el(components.TextareaControl, { label: __('Slides', 'msp-nexus-core'), help: __('One per line: Title|Description|URL', 'msp-nexus-core'), rows: 9, value: props.attributes.items, onChange: function (value) { props.setAttributes({ items: value }); } }),
      el(ToggleControl, { label: __('Auto play', 'msp-nexus-core'), checked: props.attributes.autoPlay, onChange: function (value) { props.setAttributes({ autoPlay: value }); } }),
      el(RangeControl, { label: __('Interval in milliseconds', 'msp-nexus-core'), min: 3000, max: 30000, step: 500, value: props.attributes.interval, onChange: function (value) { props.setAttributes({ interval: value }); } })
    )), el(ServerSideRender, { block: 'msp-nexus/content-carousel', attributes: props.attributes })); },
    save: function () { return null; }
  });

  blocks.registerBlockType('msp-nexus/lottie-animation', {
    edit: function (props) { return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Lottie animation settings', 'msp-nexus-core'), initialOpen: true },
      el(components.TextControl, { label: __('Lottie JSON URL', 'msp-nexus-core'), help: __('Use a reviewed HTTPS or same-origin JSON file.', 'msp-nexus-core'), value: props.attributes.jsonUrl, onChange: function (value) { props.setAttributes({ jsonUrl: value }); } }),
      el(components.TextControl, { label: __('Accessible label', 'msp-nexus-core'), value: props.attributes.label, onChange: function (value) { props.setAttributes({ label: value }); } }),
      el(ToggleControl, { label: __('Loop', 'msp-nexus-core'), checked: props.attributes.loop, onChange: function (value) { props.setAttributes({ loop: value }); } }),
      el(ToggleControl, { label: __('Auto play', 'msp-nexus-core'), checked: props.attributes.autoPlay, onChange: function (value) { props.setAttributes({ autoPlay: value }); } }),
      el(ToggleControl, { label: __('Play on hover', 'msp-nexus-core'), checked: props.attributes.playOnHover, onChange: function (value) { props.setAttributes({ playOnHover: value }); } }),
      el(ToggleControl, { label: __('Start when visible', 'msp-nexus-core'), checked: props.attributes.startWhenVisible, onChange: function (value) { props.setAttributes({ startWhenVisible: value }); } }),
      el(RangeControl, { label: __('Playback speed', 'msp-nexus-core'), min: 0.1, max: 4, step: 0.1, value: props.attributes.speed, onChange: function (value) { props.setAttributes({ speed: value }); } })
    )), el(ServerSideRender, { block: 'msp-nexus/lottie-animation', attributes: props.attributes })); },
    save: function () { return null; }
  });

  var wooElements = [
    ['shop-title', __('Shop title', 'msp-nexus-core')], ['category-description', __('Category description', 'msp-nexus-core')],
    ['shop-filters', __('Shop filters', 'msp-nexus-core')], ['shop-products', __('Shop products', 'msp-nexus-core')],
    ['product-title', __('Product title', 'msp-nexus-core')], ['product-images', __('Product images', 'msp-nexus-core')],
    ['product-price', __('Product price', 'msp-nexus-core')], ['add-to-cart', __('Add to cart', 'msp-nexus-core')],
    ['breadcrumbs', __('Shop breadcrumbs', 'msp-nexus-core')], ['product-reviews', __('Product reviews', 'msp-nexus-core')],
    ['product-stock', __('Product stock', 'msp-nexus-core')], ['product-meta', __('Product meta', 'msp-nexus-core')],
    ['product-rating', __('Product rating', 'msp-nexus-core')], ['product-brands', __('Product brands', 'msp-nexus-core')],
    ['short-description', __('Short description', 'msp-nexus-core')], ['product-tabs', __('Product tabs', 'msp-nexus-core')],
    ['product-content', __('Product content', 'msp-nexus-core')], ['additional-information', __('Additional information', 'msp-nexus-core')],
    ['upsells', __('Upsells', 'msp-nexus-core')], ['related-products', __('Related products', 'msp-nexus-core')],
    ['notices', __('Shop notices', 'msp-nexus-core')], ['order-steps', __('Order steps', 'msp-nexus-core')],
    ['cart', __('Cart', 'msp-nexus-core')], ['cart-totals', __('Cart totals', 'msp-nexus-core')],
    ['cross-sells', __('Cross-sells', 'msp-nexus-core')], ['checkout', __('Checkout', 'msp-nexus-core')],
    ['order-overview', __('Order overview', 'msp-nexus-core')], ['order-details', __('Order details', 'msp-nexus-core')],
    ['account', __('Customer account', 'msp-nexus-core')], ['mini-cart', __('Mini cart', 'msp-nexus-core')],
    ['side-cart', __('Side cart', 'msp-nexus-core')], ['wishlist', __('Wishlist', 'msp-nexus-core')],
    ['login-dropdown', __('Account login dropdown', 'msp-nexus-core')], ['shop-sidebar', __('Off-canvas shop sidebar', 'msp-nexus-core')],
    ['view-options', __('Shop view options', 'msp-nexus-core')], ['free-shipping-progress', __('Free-shipping progress', 'msp-nexus-core')]
  ];

  blocks.registerBlockType('msp-nexus/woocommerce-element', {
    edit: function (props) {
      return el(element.Fragment, {}, el(InspectorControls, {},
        el(PanelBody, { title: __('WooCommerce element settings', 'msp-nexus-core'), initialOpen: true },
          el(components.SelectControl, { label: __('Element', 'msp-nexus-core'), value: props.attributes.element, options: wooElements.map(function (item) { return { value: item[0], label: item[1] }; }), onChange: function (value) { props.setAttributes({ element: value }); } }),
          el(components.TextControl, { label: __('Optional heading', 'msp-nexus-core'), value: props.attributes.heading, onChange: function (value) { props.setAttributes({ heading: value }); } }),
          el(RangeControl, { label: __('Product count', 'msp-nexus-core'), min: 1, max: 24, value: props.attributes.count, onChange: function (value) { props.setAttributes({ count: value }); } }),
          el(RangeControl, { label: __('Columns', 'msp-nexus-core'), min: 1, max: 6, value: props.attributes.columns, onChange: function (value) { props.setAttributes({ columns: value }); } }),
          props.attributes.element === 'shop-filters' ? el(element.Fragment, {},
            el(ToggleControl, { label: __('Show product counts', 'msp-nexus-core'), checked: props.attributes.showCounts, onChange: function (value) { props.setAttributes({ showCounts: value }); } }),
            el(ToggleControl, { label: __('Submit when a filter changes', 'msp-nexus-core'), checked: props.attributes.instantFilters, onChange: function (value) { props.setAttributes({ instantFilters: value }); } })
          ) : null,
          props.attributes.element === 'free-shipping-progress' ? el(components.TextControl, { label: __('Threshold override', 'msp-nexus-core'), type: 'number', help: __('Use 0 to inherit the Nexus WooCommerce setting.', 'msp-nexus-core'), value: props.attributes.shippingThreshold, onChange: function (value) { props.setAttributes({ shippingThreshold: Number(value) }); } }) : null
        )
      ), el(ServerSideRender, { block: 'msp-nexus/woocommerce-element', attributes: props.attributes }));
    },
    save: function () { return null; }
  });

  if (blocks.registerBlockVariation) {
    wooElements.forEach(function (item) {
      blocks.registerBlockVariation('msp-nexus/woocommerce-element', {
        name: item[0], title: item[1], description: __('WooCommerce template element for Nexus Studio.', 'msp-nexus-core'),
        attributes: { element: item[0] }, scope: ['inserter', 'transform']
      });
    });
  }

  blocks.registerBlockType('msp-nexus/purchase-gate', {
    edit: function (props) {
      var blockProps = blockEditor.useBlockProps({ className: 'msp-nexus-purchase-gate is-editor' });
      var innerProps = blockEditor.useInnerBlocksProps(blockProps, { template: [['core/heading', { level: 2, content: __('Purchased resource', 'msp-nexus-core') }], ['core/paragraph', { content: __('Add protected blocks here.', 'msp-nexus-core') }]] });
      return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Purchase access', 'msp-nexus-core'), initialOpen: true },
        el(components.TextControl, { label: __('Product IDs', 'msp-nexus-core'), help: __('Comma-separated WooCommerce product IDs.', 'msp-nexus-core'), value: props.attributes.productIds, onChange: function (value) { props.setAttributes({ productIds: value }); } }),
        el(components.SelectControl, { label: __('Required purchase', 'msp-nexus-core'), value: props.attributes.match, options: [{ label: __('Any listed product', 'msp-nexus-core'), value: 'any' }, { label: __('All listed products', 'msp-nexus-core'), value: 'all' }], onChange: function (value) { props.setAttributes({ match: value }); } }),
        el(components.TextareaControl, { label: __('Locked message', 'msp-nexus-core'), value: props.attributes.fallback, onChange: function (value) { props.setAttributes({ fallback: value }); } })
      )), el('div', innerProps));
    },
    save: function () { return el('div', blockEditor.useInnerBlocksProps.save(blockEditor.useBlockProps.save())); }
  });

  blocks.registerBlockType('msp-nexus/product-showcase', {
    edit: function (props) {
      return el(element.Fragment, {}, el(InspectorControls, {}, el(PanelBody, { title: __('Product showcase settings', 'msp-nexus-core'), initialOpen: true },
        el(RangeControl, { label: __('Products', 'msp-nexus-core'), min: 1, max: 12, value: props.attributes.count, onChange: function (value) { props.setAttributes({ count: value }); } }),
        el(RangeControl, { label: __('Columns', 'msp-nexus-core'), min: 1, max: 4, value: props.attributes.columns, onChange: function (value) { props.setAttributes({ columns: value }); } }),
        el(components.TextControl, { label: __('Product category slug', 'msp-nexus-core'), value: props.attributes.category, onChange: function (value) { props.setAttributes({ category: value }); } }),
        el(ToggleControl, { label: __('Featured products only', 'msp-nexus-core'), checked: props.attributes.featuredOnly, onChange: function (value) { props.setAttributes({ featuredOnly: value }); } }),
        el(ToggleControl, { label: __('Show ratings', 'msp-nexus-core'), checked: props.attributes.showRating, onChange: function (value) { props.setAttributes({ showRating: value }); } })
      )), el(ServerSideRender, { block: 'msp-nexus/product-showcase', attributes: props.attributes }));
    },
    save: function () { return null; }
  });
})(window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n, window.wp.serverSideRender);
