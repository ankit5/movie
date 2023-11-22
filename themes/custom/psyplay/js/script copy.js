 (function($, Drupal, drupalSettings) {

 'use strict';


Drupal.behaviors.customConfig = {
    attach: function (context, settings) {
   
    
   //  $('#autocomplete').on('autocompleteclose', function(event, node) {
  
    $("a.pop-trailer").on("click", function(e) {
      var url = $(this).attr('href');
      
        $('#iframe-trailer').attr('src', url);
    });
    $(document).on("click", function(e) {
        if ($(e.target).is("a.pop-trailer") === false) {
            $('#iframe-trailer').attr('src', '');
        }
    });

 


}
      
    
  }

const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
if (isMobile) {
  // alert('Mobile');
}

 
$(document).click(function (e)
{

  var container = $("#selection-ajax");
  
 

   if (!container.is(e.target) && container.has(e.target).length === 0)
  {
 container.fadeOut('slow');
 container.hide();

   }

});




  var minlength = 2;
$('#autocomplete').keyup(function() {
  
       var value = $(this).val();
  

        if (value.length >= minlength ) {
 doChange(); 

}
return false;

});

$('#genrate-link').click(function(){
alert("da");
});
 
function doChange() {
    var test =  $("#autocomplete").val();
    var data = { 
           title : $("#autocomplete").val()
        };
      $.ajax( {
        type: "POST",
        url: "/movie-search",
        data:JSON.stringify(data),
        success: function( data ) {
          //debugger;
        // alert(data);
        $('#selection-ajax').show();
         $('#selection-ajax').html(data);
        // ifload();
        }
      });
}

   $(document).ready(function() {
     var traler = 32;
     var list_dl = $("#list-dl").height() + 60;
    
  if(list_dl==null) var list_dl = 0;
    
    $('.mvi-content').css({'top': $("#mvi-cover").height()});
 $('.idTabs a:first').addClass("selected");

  var mvi_content = $("#mvi-content").height();
  mvi_content = mvi_content + list_dl + traler;

 $("#load").height(mvi_content);
$('a.splash-image,.idTabs a,#seasons .les-content a').click(function(){
 // alert($(this).data('value'));
  
  $('#seasons .les-content a:first').addClass("selected");
   
  $('#seasons .les-content a').click(function(){
    $("#seasons .les-content a").removeClass("selected");
$(this).addClass("selected");

});
 $('.idTabs a').click(function(){
  $("#load").height($("#load").height()-176);
 $(".idTabs a").removeClass("selected");
 $(this).addClass("selected");
});
 
 $(".center").css({'display': 'flex'});
 
  var data = { 
            id: $(this).data('value'),
            tab: $(this).data('key')
        };
   $.ajax( {
        type: "POST",
        url: "/ajaxpost",
        data:JSON.stringify(data),
      //  dataType: "text",
        success: function( data ) {
          //debugger;
         const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
         $('#load').html(data);
         $(".center").hide();

        if (isMobile){
         ifload_mobile();
        }else{
          console.log("web load");
        ifload();
      }
        
       
        }
      });

  if ( $("a.splash-image").is(":visible") ) { 
    $("a.splash-image").hide(); 

    $("#content-embed").show(); 
  } 
});

    function ifload_mobile() {
     // alert("Ad");
    //  ifload_mobile = function(){};
      var traler = 32;
       var list_dl = $("#list-dl").height() + 62;
       if($("#list-dl").height()==null) var list_dl = 136;
      $('#mvi-content').css({'padding-bottom': list_dl + traler+'px'});
var mvi_content = $("#mvi-content").height();
var keyheight = $("#mv-keywords").height();

if(keyheight==null){
  keyheight = 0;
 }else { }
 
 var top = parseInt($('#mvi-content').css('top'));;
 //alert(top);
 mvi_content = top + mvi_content + traler + list_dl;
 
$("#load").height(mvi_content);

     focus();
  const listener = window.addEventListener('mouseout', () => {
  
  setTimeout(() => {
     if (document.activeElement.tagName === "IFRAME") {
     // alert("ad2");
   var checkCounter = 0;

     var top = parseInt($('#mvi-content').css('top'));
    //  $('.mvi-content').css({'top': top+21 });
  checkCounter = checkCounter + 1;
  //alert(checkCounter);
if($("#seasons").height()) return true;
    $(".player_nav").show();
    $(".dl-des").show();
    var player_nav = $(".player_nav").height()+20;
  
    if(keyheight==null){
   
  }else {

  }
   $('#mvi-content').css({'padding-bottom': 70 + traler +'px'});
   mvi_content = mvi_content + player_nav + 60 ;
    $("#load").height(mvi_content);
   }
    window.removeEventListener('mouseout', listener);
  });
}, { once: true }); 
  
     
    }
//////////////
  function ifload() {
      $(".player_nav").hide();
    $(".dl-des").hide();
    //  ifload = function(){};
      var traler = 32;
       var list_dl = $("#list-dl").height() + 62;
       if($("#list-dl").height()==null) var list_dl = 136;
      $('#mvi-content').css({'padding-bottom': list_dl + traler+'px'});
var mvi_content = $("#mvi-content").height();
var keyheight = $("#mv-keywords").height();
if(keyheight==null){
  keyheight = 0;
 }else { }
 
 var top = parseInt($('#mvi-content').css('top'));;
 //alert(top);
 mvi_content = top + mvi_content + traler + list_dl;
 
$("#load").height(mvi_content);

    window.focus()

window.addEventListener("blur", () => {
  setTimeout(() => {
    if (document.activeElement.tagName === "IFRAME") {
     // alert("frame click");
   var checkCounter = 0;

     var top = parseInt($('#mvi-content').css('top'));
     // $('.mvi-content').css({'top': top+21 });
  checkCounter = checkCounter + 1;
  //alert(checkCounter);
if($("#seasons").height()) return true;
    $(".player_nav").show();
    $(".dl-des").show();
    var player_nav = $(".player_nav").height()+20;
  
    if(keyheight==null){
   
  }else {

  }
   $('#mvi-content').css({'padding-bottom': 80 + traler +'px'});
   mvi_content = mvi_content + player_nav + 60 ;
    $("#load").height(mvi_content);
   }
  });
}, { once: true }); 
  
     
    }
/////////////













        //alert("ad");
        var swiper = new Swiper('#slider', {
          pagination: '.swiper-pagination',
          paginationClickable: true,
          loop: true,
          autoplay: 4000
        });
        $('a.jt').each(function() {
          $(this).qtip({
            content: {
              text: $(this).next('#hidden_tip')
            },
            position: {
              my: 'top left',
              at: 'top right',
              viewport: $(window),
              effect: !1,
              target: 'mouse',
              adjust: {
                mouse: !1
              },
              show: {
                effect: !1
              }
            },
            hide: {
              fixed: !0
            },
            style: {
              classes: 'qtip-light qtip-bootstrap',
              width: 320
            }
          })
        });
        $('img.lazy').lazyload({
          effect: 'fadeIn'
        });

        function a() {
          $(this).find(".sub-container").css("display", "block")
        }

        function b() {
          $(this).find(".sub-container").css("display", "none")
        }
        $("#search a.box-title").click(function() {
          $("#search .box").toggleClass("active")
        }), $(".mobile-menu").click(function() {
          $("#menu,.mobile-menu").toggleClass("active"), $("#search, .mobile-search").removeClass("active")
        }), $(".mobile-search").click(function() {
          $("#search,.mobile-search").toggleClass("active"), $("#menu, .mobile-menu").removeClass("active")
        }), $(".filter-toggle").click(function() {
          $("#filter").toggleClass("active"), $(".filter-toggle").toggleClass("active")
        }), $(".bp-btn-light").click(function() {
          $(".bp-btn-light, #overlay, #media-player, #content-embed, #comment-area").toggleClass("active")
        }), $("#overlay").click(function() {
          $(".bp-btn-light, #overlay, #media-player, #content-embed, #comment-area").removeClass("active")
        }), $(".bp-btn-auto").click(function() {
          $(".bp-btn-auto").toggleClass("active")
        }), $("#toggle, .cac-close").click(function() {
          $("#comment").toggleClass("active")
        }), $(".top-menu> li").bind("mouseover", a), $(".top-menu> li").bind("mouseout", b);
        var c = 0;
       
        $('a[data-toggle=tab]').on('shown.bs.tab', function(a) {
          $(window).trigger('scroll');
        });
        $('#sug-nav li:first').addClass('active');
        $('#sug-cont div:first').addClass('active');
      });

   })(jQuery, Drupal, drupalSettings);
      
