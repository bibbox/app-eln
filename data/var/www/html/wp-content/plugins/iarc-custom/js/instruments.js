(function() {
    tinymce.PluginManager.add('instruments_button', function( editor, url ) {
        editor.addButton( 'instruments_button', {
            title: 'Insert Instruments Template',
            icon: 'icon instruments-icon',
            onclick: function() {
                editor.insertContent( '<p><span style="font-size: 16px"><span style="color: #ff0000"><em>Don&#39;t forget to fill in the title above</em></span></span></p><p>&nbsp;</p><p><span style="font-size: 20px">&lt;<em>Date maintenance/problem</em>&gt;</span></p><p>&nbsp;</p><p><u><span style="font-size: 24px"><span style="font-family: verdana, geneva, sans-serif">1 - Issue description</span></span></u></p><p>&nbsp;</p><p>&nbsp;</p><p><u><span style="font-size: 24px"><span style="font-family: verdana, geneva, sans-serif">2 - Issue solution</span></span></u></p>' );
            }
        });
    });
})();