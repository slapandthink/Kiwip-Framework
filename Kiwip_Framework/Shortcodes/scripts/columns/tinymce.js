(function() {
   tinymce.create('tinymce.plugins.columns', {
      init : function(ed, url) {
         ed.addButton('columns', {
            title : 'Columns',
            image : url+'/button.png',
            onclick : function() {
               ed.windowManager.open({
                  file : url + '/options.php?folderUrl='+url,
                  width : 540,
                  height : 330,
                  title: 'Columns selection',
                  inline : 1  
               });
            }
         });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : "columns",
            author : 'Kiwip Framework',
            authorurl : 'http://benjamincabanes.com',
            infourl : 'http://slapandthink.com',
            version : "1.0"
         };
      }
   });
   tinymce.PluginManager.add('columns', tinymce.plugins.columns);
})();