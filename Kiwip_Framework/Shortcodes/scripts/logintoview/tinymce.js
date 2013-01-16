(function() {
   tinymce.create('tinymce.plugins.logintoview', {
      init : function(ed, url) {
         ed.addButton('logintoview', {
            title : 'Login to view',
            image : url+'/button.png',
            onclick : function() {
                ed.execCommand('mceInsertContent', false, '[logintoview]Your content[/logintoview]<br /><br />');
            }
         });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : "Logintoview",
            author : 'Kiwip Framework',
            authorurl : 'http://benjamincabanes.com',
            infourl : 'http://slapandthink.com',
            version : "1.0"
         };
      }
   });
   tinymce.PluginManager.add('logintoview', tinymce.plugins.logintoview);
})();