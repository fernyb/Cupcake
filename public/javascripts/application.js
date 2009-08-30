
(function($){
  $.cupcake = {
    remoteFormSubmit: function(that) {
      var action = that.action + ".js";
      if(that.method.toLowerCase() == "post") {
        $.post(action, $(that).serialize(), null, "script");
      } else {
        $.get(action, $(that).serialize(), null, "script");
      }
      return false;
    }
  };
})(jQuery);




