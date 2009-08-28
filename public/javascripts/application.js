
jQuery.ajaxSetup({ 
  "beforeSend": function(xhr) {
    xhr.setRequestHeader("Accept", "text/javascript");
  }
})

function remoteFormSubmit(that) {
  
  $.post(that.action, $(that).serialize(), null, "script");
  return false;
}

