(function() {
    tinymce.PluginManager.add('selectall_button', function( editor, url ) {
        editor.addButton( 'selectall_button', {
            title: 'Select All',
            icon: 'icon selectall-icon',
            onclick: function() {
                editor.selection.select(editor.getBody(), true);
            }
        });
    });
})();