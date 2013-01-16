(function() {
   tinymce.create('tinymce.plugins.accordion', {
      init : function(ed, url) {
         ed.addButton('accordion', {
            title : 'Accordion',
            image : url+'/button.png',
            onclick : function() {
               var items = parseFloat(prompt("Number of item", "1"));

               for(var i = 0; i<items; i++){
                  ed.execCommand('mceInsertContent', false, '[accordion title="Your title"]Your content[/accordion]<br /><br />');
               }

            }
         });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : "Accordion",
            author : 'Kiwip Framework',
            authorurl : 'http://benjamincabanes.com',
            infourl : 'http://slapandthink.com',
            version : "1.0"
         };
      }
   });
   tinymce.PluginManager.add('accordion', tinymce.plugins.accordion);
})();