(function (wp) {
  'use strict';

  if (!wp || !wp.plugins || !wp.editPost || !wp.data || !wp.blockEditor) return;

  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var useSelect = wp.data.useSelect;
  var __ = wp.i18n.__;
  var PluginSidebar = wp.editPost.PluginSidebar;
  var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
  var Button = wp.components.Button;
  var ButtonGroup = wp.components.ButtonGroup;
  var PanelBody = wp.components.PanelBody;
  var Notice = wp.components.Notice;
  var TextControl = wp.components.TextControl;
  var config = window.mspNexusWorkspace || {};
  var favoriteKey = 'msp-nexus-favorite-blocks';
  var defaultFavorites = ['core/group', 'core/heading', 'core/paragraph', 'msp-nexus/dynamic-value', 'msp-nexus/content-loop'];
  var styleClipboardKey = 'msp-nexus-style-clipboard';

  function readFavorites() {
    try {
      var value = JSON.parse(window.localStorage.getItem(favoriteKey) || '[]');
      return Array.isArray(value) && value.length ? value : defaultFavorites;
    } catch (error) {
      return defaultFavorites;
    }
  }

  function writeFavorites(value) {
    window.localStorage.setItem(favoriteKey, JSON.stringify(value));
    window.dispatchEvent(new window.CustomEvent('msp-nexus-favorites-changed'));
  }

  function blockTitle(name) {
    var type = wp.blocks.getBlockType(name);
    return type && type.title ? type.title : name.replace(/^[^/]+\//, '').replace(/-/g, ' ');
  }

  function flatten(blocks, depth, result) {
    blocks.forEach(function (block) {
      result.push({ block: block, depth: depth });
      if (block.innerBlocks && block.innerBlocks.length) flatten(block.innerBlocks, depth + 1, result);
    });
    return result;
  }

  function LayerRow(props) {
    var block = props.entry.block;
    var selected = props.selected === block.clientId;
    var editor = wp.data.dispatch('core/block-editor');
    return el('div', { className: 'msp-nexus-layer' + (selected ? ' is-selected' : ''), style: { '--msp-layer-depth': props.entry.depth } },
      el(Button, { className: 'msp-nexus-layer__select', onClick: function () { editor.selectBlock(block.clientId); } },
        el('span', { className: 'dashicons dashicons-' + ((wp.blocks.getBlockType(block.name) || {}).icon || 'block-default') }),
        el('span', {}, blockTitle(block.name))
      ),
      selected ? el('span', { className: 'msp-nexus-layer__actions' },
        el(Button, { size: 'small', label: __('Move up', 'msp-nexus-core'), icon: 'arrow-up-alt2', onClick: function () { editor.moveBlocksUp([block.clientId]); } }),
        el(Button, { size: 'small', label: __('Move down', 'msp-nexus-core'), icon: 'arrow-down-alt2', onClick: function () { editor.moveBlocksDown([block.clientId]); } }),
        el(Button, { size: 'small', label: __('Duplicate', 'msp-nexus-core'), icon: 'admin-page', onClick: function () { editor.duplicateBlocks([block.clientId]); } })
      ) : null
    );
  }

  function LayersPanel() {
    var state = useSelect(function (select) {
      var editor = select('core/block-editor');
      return { blocks: editor.getBlocks(), selected: editor.getSelectedBlockClientId(), selectedBlock: editor.getSelectedBlock() };
    }, []);
    var rows = flatten(state.blocks, 0, []);
    var editor = wp.data.dispatch('core/block-editor');
    var selectedName = state.selectedBlock ? state.selectedBlock.name : '';
    var favorites = readFavorites();

    function toggleFavorite() {
      if (!selectedName) return;
      var next = favorites.indexOf(selectedName) === -1 ? favorites.concat([selectedName]) : favorites.filter(function (name) { return name !== selectedName; });
      writeFavorites(next);
    }

    function makePattern() {
      if (!state.selectedBlock) return;
      var title = window.prompt(__('Name this synced pattern', 'msp-nexus-core'), blockTitle(state.selectedBlock.name));
      if (!title) return;
      wp.apiFetch({ path: '/wp/v2/blocks', method: 'POST', data: { title: title, status: 'publish', content: wp.blocks.serialize([state.selectedBlock]) } }).then(function () {
        wp.data.dispatch('core/notices').createSuccessNotice(__('Synced pattern created.', 'msp-nexus-core'), { type: 'snackbar' });
      }).catch(function () {
        wp.data.dispatch('core/notices').createErrorNotice(__('The synced pattern could not be created.', 'msp-nexus-core'), { type: 'snackbar' });
      });
    }

    return el(Fragment, {},
      el(PanelBody, { title: __('Layer navigator', 'msp-nexus-core'), initialOpen: true },
        rows.length ? el('div', { className: 'msp-nexus-layer-tree' }, rows.map(function (entry) { return el(LayerRow, { key: entry.block.clientId, entry: entry, selected: state.selected }); })) : el(Notice, { status: 'info', isDismissible: false }, __('Add a block to begin building.', 'msp-nexus-core'))
      ),
      el(PanelBody, { title: __('Selected block', 'msp-nexus-core'), initialOpen: true },
        state.selectedBlock ? el(Fragment, {},
          el('p', {}, el('strong', {}, blockTitle(selectedName)), el('br'), el('code', {}, selectedName)),
          el(ButtonGroup, {},
            el(Button, { variant: 'secondary', onClick: function () { editor.duplicateBlocks([state.selected]); } }, __('Duplicate', 'msp-nexus-core')),
            el(Button, { variant: 'secondary', onClick: toggleFavorite }, favorites.indexOf(selectedName) === -1 ? __('Favorite', 'msp-nexus-core') : __('Unfavorite', 'msp-nexus-core'))
          ),
          el('p', {}, el(ButtonGroup, {},
            el(Button, { variant: 'secondary', onClick: function () {
              var copied = {};
              Object.keys(state.selectedBlock.attributes || {}).forEach(function (key) { if (key === 'style' || key.indexOf('msp') === 0 || ['backgroundColor', 'textColor', 'gradient', 'fontSize', 'layout'].indexOf(key) !== -1) copied[key] = state.selectedBlock.attributes[key]; });
              window.localStorage.setItem(styleClipboardKey, JSON.stringify(copied));
              wp.data.dispatch('core/notices').createSuccessNotice(__('Block styles copied.', 'msp-nexus-core'), { type: 'snackbar' });
            } }, __('Copy styles', 'msp-nexus-core')),
            el(Button, { variant: 'secondary', onClick: function () {
              try { editor.updateBlockAttributes(state.selected, JSON.parse(window.localStorage.getItem(styleClipboardKey) || '{}')); } catch (error) { wp.data.dispatch('core/notices').createErrorNotice(__('No valid copied styles were found.', 'msp-nexus-core'), { type: 'snackbar' }); }
            } }, __('Paste styles', 'msp-nexus-core'))
          )),
          el('p', {}, el(Button, { variant: 'secondary', onClick: makePattern }, __('Create synced pattern', 'msp-nexus-core'))),
          el('p', {}, el(Button, { isDestructive: true, onClick: function () { editor.removeBlocks([state.selected], true); } }, __('Remove block', 'msp-nexus-core')))
        ) : el('p', {}, __('Select a block to see actions.', 'msp-nexus-core'))
      )
    );
  }

  function LibraryPanel() {
    var _favorites = useState(readFavorites());
    var favorites = _favorites[0];
    var setFavorites = _favorites[1];
    var _search = useState('');
    var search = _search[0];
    var setSearch = _search[1];
    useEffect(function () {
      function refresh() { setFavorites(readFavorites()); }
      window.addEventListener('msp-nexus-favorites-changed', refresh);
      return function () { window.removeEventListener('msp-nexus-favorites-changed', refresh); };
    }, []);
    var editor = wp.data.dispatch('core/block-editor');
    var items = favorites.filter(function (name) { return blockTitle(name).toLowerCase().indexOf(search.toLowerCase()) !== -1; });
    return el(PanelBody, { title: __('Favorite elements', 'msp-nexus-core'), initialOpen: true },
      el(TextControl, { label: __('Filter favorites', 'msp-nexus-core'), value: search, onChange: setSearch }),
      el('div', { className: 'msp-nexus-favorites' }, items.map(function (name) {
        return el(Button, { key: name, variant: 'secondary', onClick: function () { editor.insertBlock(wp.blocks.createBlock(name)); } }, blockTitle(name));
      }))
    );
  }

  function CanvasPanel() {
    var _dark = useState(window.localStorage.getItem('msp-nexus-studio-ui') === 'dark');
    var dark = _dark[0];
    var setDark = _dark[1];
    var _wireframe = useState(document.body.classList.contains('msp-nexus-studio-wireframe'));
    var wireframe = _wireframe[0];
    var setWireframe = _wireframe[1];
    function toggleDark() {
      var next = !dark;
      setDark(next);
      document.body.classList.toggle('msp-nexus-studio-ui-dark', next);
      window.localStorage.setItem('msp-nexus-studio-ui', next ? 'dark' : 'light');
    }
    function setDevice(device) {
      var dispatcher = wp.data.dispatch('core/edit-post');
      if (dispatcher.__experimentalSetPreviewDeviceType) dispatcher.__experimentalSetPreviewDeviceType(device);
      if (dispatcher.setDeviceType) dispatcher.setDeviceType(device);
    }
    return el(PanelBody, { title: __('Canvas tools', 'msp-nexus-core'), initialOpen: true },
      el('p', {}, el(ButtonGroup, {},
        ['Desktop', 'Tablet', 'Mobile'].map(function (device) { return el(Button, { key: device, variant: 'secondary', onClick: function () { setDevice(device); } }, device); })
      )),
      el('p', {}, el(Button, { variant: 'secondary', onClick: toggleDark }, dark ? __('Use light Studio UI', 'msp-nexus-core') : __('Use dark Studio UI', 'msp-nexus-core'))),
      el('p', {}, el(Button, { variant: 'secondary', onClick: function () { var next = !wireframe; setWireframe(next); document.body.classList.toggle('msp-nexus-studio-wireframe', next); } }, wireframe ? __('Hide block wireframes', 'msp-nexus-core') : __('Show block wireframes', 'msp-nexus-core'))),
      el('p', {}, el(ButtonGroup, {},
        el(Button, { variant: 'secondary', onClick: function () { wp.data.dispatch('core/editor').undo(); } }, __('Undo', 'msp-nexus-core')),
        el(Button, { variant: 'secondary', onClick: function () { wp.data.dispatch('core/editor').redo(); } }, __('Redo', 'msp-nexus-core'))
      )),
      el('p', {}, el('a', { href: config.layoutsUrl || '#' }, __('Manage reusable layouts', 'msp-nexus-core'))),
      el('p', { className: 'description' }, __('WordPress provides native drag and drop, undo/redo, autosave, revisions, preview, list view, and keyboard navigation. Nexus layers and tools operate on the same block document.', 'msp-nexus-core'))
    );
  }

  function RevisionsPanel() {
    var post = useSelect(function (select) {
      var editor = select('core/editor');
      return { id: editor.getCurrentPostId(), type: editor.getCurrentPostType(), saving: editor.isSavingPost(), dirty: editor.isEditedPostDirty() };
    }, []);
    var _revisions = useState([]); var revisions = _revisions[0]; var setRevisions = _revisions[1];
    var _loading = useState(false); var loading = _loading[0]; var setLoading = _loading[1];
    function loadRevisions() {
      if (!post.id || !post.type) return;
      var type = wp.data.select('core').getPostType(post.type);
      var base = type && type.rest_base ? type.rest_base : post.type;
      setLoading(true);
      wp.apiFetch({ path: '/wp/v2/' + encodeURIComponent(base) + '/' + post.id + '/revisions?context=edit&per_page=10' }).then(function (items) { setRevisions(Array.isArray(items) ? items : []); setLoading(false); }).catch(function () { setRevisions([]); setLoading(false); });
    }
    useEffect(loadRevisions, [post.id, post.type]);
    return el(PanelBody, { title: __('Save and revisions', 'msp-nexus-core'), initialOpen: false },
      el('p', {}, post.dirty ? __('Unsaved changes are present.', 'msp-nexus-core') : __('The document matches its latest save.', 'msp-nexus-core')),
      el(ButtonGroup, {},
        el(Button, { variant: 'primary', isBusy: post.saving, disabled: post.saving, onClick: function () { wp.data.dispatch('core/editor').savePost().then(loadRevisions); } }, __('Save revision', 'msp-nexus-core')),
        el(Button, { variant: 'secondary', disabled: loading, onClick: loadRevisions }, __('Refresh', 'msp-nexus-core'))
      ),
      revisions.length ? el('ol', { className: 'msp-nexus-revision-list' }, revisions.map(function (revision) {
        var date = new Date(revision.date || revision.modified || '');
        return el('li', { key: revision.id }, el('a', { href: (config.revisionUrl || '') + revision.id }, isNaN(date.getTime()) ? String(revision.id) : date.toLocaleString()));
      })) : el('p', { className: 'description' }, loading ? __('Loading revisions…', 'msp-nexus-core') : __('No revisions are available yet.', 'msp-nexus-core'))
    );
  }

  function Workspace() {
    return el(Fragment, {},
      el(PluginSidebarMoreMenuItem, { target: 'msp-nexus-studio-sidebar', icon: 'layout' }, __('Nexus Studio', 'msp-nexus-core')),
      el(PluginSidebar, { name: 'msp-nexus-studio-sidebar', title: __('Nexus Studio', 'msp-nexus-core'), icon: 'layout' },
        el(LayersPanel), el(LibraryPanel), el(CanvasPanel), el(RevisionsPanel)
      )
    );
  }

  wp.plugins.registerPlugin('msp-nexus-studio-workspace', { render: Workspace, icon: 'layout' });

  function openSidebar() {
    var dispatcher = wp.data.dispatch('core/edit-post');
    if (dispatcher.openGeneralSidebar) dispatcher.openGeneralSidebar('msp-nexus-studio-workspace/msp-nexus-studio-sidebar');
  }

  document.addEventListener('keydown', function (event) {
    if (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === 'l') { event.preventDefault(); openSidebar(); }
    if (event.ctrlKey && event.altKey && event.key.toLowerCase() === 'd') {
      var selected = wp.data.select('core/block-editor').getSelectedBlockClientId();
      if (selected) { event.preventDefault(); wp.data.dispatch('core/block-editor').duplicateBlocks([selected]); }
    }
    if (event.ctrlKey && event.altKey && event.key.toLowerCase() === 'c') {
      var block = wp.data.select('core/block-editor').getSelectedBlock();
      if (block) { var styles = {}; Object.keys(block.attributes || {}).forEach(function (key) { if (key === 'style' || key.indexOf('msp') === 0) styles[key] = block.attributes[key]; }); window.localStorage.setItem(styleClipboardKey, JSON.stringify(styles)); event.preventDefault(); }
    }
    if (event.ctrlKey && event.altKey && event.key.toLowerCase() === 'v') {
      var target = wp.data.select('core/block-editor').getSelectedBlockClientId();
      if (target) { try { wp.data.dispatch('core/block-editor').updateBlockAttributes(target, JSON.parse(window.localStorage.getItem(styleClipboardKey) || '{}')); } catch (error) {} event.preventDefault(); }
    }
  });

  document.addEventListener('contextmenu', function (event) {
    var blockNode = event.target.closest ? event.target.closest('[data-block]') : null;
    if (!blockNode || !blockNode.dataset.block) return;
    event.preventDefault();
    var clientId = blockNode.dataset.block;
    wp.data.dispatch('core/block-editor').selectBlock(clientId);
    document.querySelectorAll('.msp-nexus-context-menu').forEach(function (menu) { menu.remove(); });
    var menu = document.createElement('div');
    menu.className = 'msp-nexus-context-menu';
    menu.style.left = event.clientX + 'px';
    menu.style.top = event.clientY + 'px';
    [[__('Duplicate', 'msp-nexus-core'), 'duplicate'], [__('Move up', 'msp-nexus-core'), 'up'], [__('Move down', 'msp-nexus-core'), 'down'], [__('Remove', 'msp-nexus-core'), 'remove']].forEach(function (item) {
      var button = document.createElement('button');
      button.type = 'button';
      button.textContent = item[0];
      button.addEventListener('click', function () {
        var editor = wp.data.dispatch('core/block-editor');
        if (item[1] === 'duplicate') editor.duplicateBlocks([clientId]);
        if (item[1] === 'up') editor.moveBlocksUp([clientId]);
        if (item[1] === 'down') editor.moveBlocksDown([clientId]);
        if (item[1] === 'remove') editor.removeBlocks([clientId], true);
        menu.remove();
      });
      menu.appendChild(button);
    });
    document.body.appendChild(menu);
    window.setTimeout(function () { document.addEventListener('click', function close() { menu.remove(); document.removeEventListener('click', close); }); }, 0);
  });

  if (config.studioMode) {
    document.body.classList.add('msp-nexus-studio-editor-active');
    window.setTimeout(openSidebar, 300);
  }
  if (window.localStorage.getItem('msp-nexus-studio-ui') === 'dark') document.body.classList.add('msp-nexus-studio-ui-dark');
})(window.wp);
