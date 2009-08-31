
(function($){
  $.cupcake = {
    remoteFormSubmit: function(that) {
      var action = that.action + ".js";
      console.log($(that).serialize());
      
      if(that.method.toLowerCase() == "post") {
        $.post(action, $(that).serialize(), null, "script");
      } else {
        $.get(action, $(that).serialize(), null, "script");
      }
      return false;
    },
    linkToRemote: function(that) {
      /*
      * linkToRemote has two events. 
      * 1. It triggers linkToRemoteBefore right before it makes the ajax request
      * 2. It triggers liknToRemoteComplete when the ajax request is complete.
      * TODO: Needs clean as there is some code duplication, eeek!
      */
      var element = $(that);
      var href    = element.attr("href");
      if(href == undefined) {
        return false;
      } else {
        href += ".js";
      }
      var method  = element.attr("data-method");
      if(method == undefined) {
        method = "get";
      }
      var query = element.attr("data-query");
      if(query == undefined) {
        query = null;
      }
      var args = [that];

      $(document).trigger("cupcake.linkToRemoteBefore", [method, that]);
      if(method == "post") {
        $.post(href, query, function(data){    
          args.push("post");
          args = args.reverse();
          args.push(arguments[1]);
          args.push(arguments[0]);
          $(document).trigger("cupcake.linkToRemoteComplete", args);
        }, "script");
      } else {
        $.get(href, query, function(data){
          args.push("get");
          args = args.reverse();       
          args.push(arguments[1]);
          args.push(arguments[0]);
          $(document).trigger("cupcake.linkToRemoteComplete", args);
        }, "script");
      }
    }
  };
})(jQuery);




