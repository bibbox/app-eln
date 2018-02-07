(function() {
    tinymce.PluginManager.add('columns_button', function( editor, url ) {
        editor.addButton( 'columns_button', {
            title: 'Two columns',
            icon: 'icon columns-icon',
            onclick: function() {
                editor.insertContent( '<p>[columns count="2"]Insert your text here (between the two [columns] tags) to format it on two columns.<span style="color: #ff0000">This functionality doesn\'t work with IE!</span>[/columns]</p>' );
            }
        });
    });
})();