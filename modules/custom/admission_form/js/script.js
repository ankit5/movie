(function($, Drupal, drupalSettings) {

 $('input[type="file"]').each(function(idx, item) {
   $(item).attr('accept', 'image/*;capture=camera');
});

 if(performance.navigation.type == 2){
   location.reload(true);
}
//console.log("asd");
 /**
   * Add new custom command.
   */
 window.onclick = function(event) {
 
  $("div[data-drupal-messages]").hide();
 // $("p").removeClass("intro");
  $("body").removeClass("model-box2");
}

 $(document).ajaxStop(function() {
        console.log('ajax is done');
       // $("body").addClass("model-box");
    });
$( document ).ajaxComplete(function( event, xhr, settings ) {
  
  console.log( xhr.responseText);
});

 Drupal.behaviors.myModuleName = {
    attach: function (context, settings) {
      

    }
  };

})(jQuery, Drupal, drupalSettings);