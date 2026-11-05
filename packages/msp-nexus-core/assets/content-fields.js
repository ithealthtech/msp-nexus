/**
 * Details panel for MSP Nexus content types: edits each type's own fields (price, button URL,
 * role, address...) in the block editor's document settings. Fields come from
 * MspNexusCore\Content\Registrar::FIELDS, passed in as mspNexusContentFields.
 */
(function (wp, config) {
  'use strict';

  if (!wp || !config || !config.fields || !config.fields.length) {
    return;
  }

  var el = wp.element.createElement;
  var useSelect = wp.data.useSelect;
  var useDispatch = wp.data.useDispatch;
  var TextControl = wp.components.TextControl;
  var TextareaControl = wp.components.TextareaControl;
  var ToggleControl = wp.components.ToggleControl;
  var Panel = (wp.editor && wp.editor.PluginDocumentSettingPanel) || (wp.editPost && wp.editPost.PluginDocumentSettingPanel);

  if (!Panel || !wp.plugins) {
    return;
  }

  function Details() {
    var meta = useSelect(function (select) {
      return select('core/editor').getEditedPostAttribute('meta') || {};
    }, []);
    var editPost = useDispatch('core/editor').editPost;

    function set(key, value) {
      var next = {};
      next[key] = value;
      editPost({ meta: next });
    }

    return el(
      Panel,
      { name: 'msp-nexus-content-fields', title: config.title, className: 'msp-nexus-content-fields' },
      config.fields.map(function (field) {
        var value = meta[field.key] || '';
        var common = { key: field.key, label: field.label, __nextHasNoMarginBottom: true };
        if ('toggle' === field.kind) {
          return el(ToggleControl, Object.assign(common, {
            checked: '1' === value,
            onChange: function (on) { set(field.key, on ? '1' : ''); }
          }));
        }
        if ('textarea' === field.kind) {
          return el(TextareaControl, Object.assign(common, {
            value: value,
            rows: 3,
            onChange: function (v) { set(field.key, v); }
          }));
        }
        return el(TextControl, Object.assign(common, {
          value: value,
          type: 'url' === field.kind ? 'url' : 'text',
          __next40pxDefaultSize: true,
          onChange: function (v) { set(field.key, v); }
        }));
      })
    );
  }

  wp.plugins.registerPlugin('msp-nexus-content-fields', { render: Details });
}(window.wp, window.mspNexusContentFields));
