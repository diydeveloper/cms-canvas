/*
* @example An iframe-based dialog with custom button handling logics.
*/
( function() {
    CKEDITOR.plugins.add( 'mediaembed',
    {
        requires: [ 'iframedialog' ],
        init: function( editor )
        {
           var me = this;
           CKEDITOR.dialog.add( 'MediaEmbedDialog', function (ck)
           {
              return {
                 title : 'Embed Media Dialog',
                 minWidth : 550,
                 minHeight : 200,
                 contents :
                       [
                          {
                             id : 'embed_media_dialog',
                             label : 'Embed Media',
                             expand : true,
                             elements :
                                   [
                                      {
                                          type : 'textarea',
                                          label : 'Paste Embed Code Here:',
                                          id : 'embedmediatextarea',
                                          rows : 7,
                                          cols : 40
                                    }
                                   ]
                          }
                       ],
                  onOk : function()
                 {
                      var contentField = this.getContentElement( 'embed_media_dialog', 'embedmediatextarea' );
                      var content = contentField.getValue();

                      final_html = '<div class="media_embed">'+content+'</div>';
                      ck.insertHtml(final_html);
                 }
              };
           } );

            editor.addCommand( 'MediaEmbed', new CKEDITOR.dialogCommand( 'MediaEmbedDialog' ) );

            editor.ui.addButton( 'MediaEmbed',
            {
                label: 'Embed Media',
                command: 'MediaEmbed',
                icon: this.path + 'images/icon.gif'
            } );
        }
    } );
} )();
