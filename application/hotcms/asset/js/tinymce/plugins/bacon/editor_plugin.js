(function(){tinymce.create('tinymce.plugins.BaconPlugin',{init:function(ed,url){ed.addCommand('mceBaconmore',function(){ed.windowManager.open({},{plugin_url:url,some_custom_arg:'custom arg'})});ed.addButton('bacon_adv',{title:'hotimg.desc',cmd:'mceBaconmore',image:url+'/img/hotimg.gif'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('bacon_adv',n.nodeName=='IMG')})},createControl:function(n,cm){return null},getInfo:function(){return{longname:'BaconCMS plugin',author:'Hot Tomali',authorurl:'http://www.hottomali.com',infourl:'http://www.hottomali.com',version:"1.0"}}});tinymce.PluginManager.add('bacon',tinymce.plugins.BaconPlugin)})();