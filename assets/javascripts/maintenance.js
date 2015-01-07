var Maintenance=function(t,i,s){var e=this;this.initialized=!1,this.switchedToUser=null,this.frontendUsername=i,this.index=-1,this.guestUrls=t,this.memberUrls=s,this.url=null,this.indicator=null,this.request=new Request({data:{theme_plus_compile_assets:1},method:"get",headers:{"X-Requested-With":!1},onRequest:function(){e.indicator.addClass("running")},onSuccess:function(){e.indicator.removeClass("running"),e.indicator.addClass("succeed"),e.run()},onFailure:function(){e.indicator.removeClass("running"),e.indicator.addClass("failed"),e.run()}})};Maintenance.prototype.switchToGuest=function(){this.switchedToUser===!1&&this.run();var t=this;this.url=location.pathname.replace("/main.php","/switch.php"),this.indicator=$$('li[data-url="switch_to_guest"]')[0],new Request({url:this.url,data:{REQUEST_TOKEN:Contao.request_token,FORM_SUBMIT:"tl_switch",unpublished:"hide",user:""},method:"post",headers:{"X-Requested-With":!1},onRequest:function(){t.indicator.addClass("running")},onSuccess:function(){t.indicator.removeClass("running"),t.indicator.addClass("succeed"),t.switchedToUser=!1,t.run()},onFailure:function(){t.indicator.removeClass("running"),t.indicator.addClass("failed"),t.switchedToUser=!1,t.run()}}).send()},Maintenance.prototype.switchToUser=function(){this.switchedToUser===!0&&this.run();var t=this;this.url=location.pathname.replace("/main.php","/switch.php"),this.indicator=$$('li[data-url="switch_to_member"]')[0],new Request({url:this.url,data:{REQUEST_TOKEN:Contao.request_token,FORM_SUBMIT:"tl_switch",unpublished:"hide",user:this.frontendUsername},method:"post",headers:{"X-Requested-With":!1},onRequest:function(){t.indicator.addClass("running")},onSuccess:function(){t.indicator.removeClass("running"),t.indicator.addClass("succeed"),t.switchedToUser=!0,t.run()},onFailure:function(){t.indicator.removeClass("running"),t.indicator.addClass("failed"),alert("Could not switch member, will continue without frontend member!"),t.switchedToUser=!0,t.run()}}).send()},Maintenance.prototype.run=function(){this.guestUrls.length?this.switchedToUser!==!1?this.switchToGuest():(this.url=this.guestUrls.shift(),this.indicator=$$('li[data-url="'+this.url+'"]')[0],this.request.send({url:this.url})):this.frontendUsername&&this.memberUrls.length&&(this.switchedToUser!==!0?this.switchToUser():(this.url=this.memberUrls.shift(),this.indicator=$$('li[data-url="'+this.url+'"]')[0],this.request.send({url:this.url})))};
//# sourceMappingURL=maintenance.js.map