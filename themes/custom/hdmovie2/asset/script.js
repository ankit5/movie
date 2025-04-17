$(function() {
    $('.lazy').Lazy();
    
});

$(function () {
  $('#BookmarkMe').click(function (e) {
      var sTitle = 'Hdmovie2 - Watch Online Bollywood, Hollywood, Netflix, Hindi Dubbed, Hotstar Movies', sURL = 'https://hdmovie20.lat';
      if (document.all && window.external) {
        window.external.AddFavorite (sURL,sTitle);
       }
       else if (window.sidebar) { 
         window.sidebar.addPanel(sTitle,sURL,'');
       } 
       else {
        alert (''
         +'Hdmovie2\n'
         +'Please press Ctrl+D to bookmark this page.'
        );
       }
  });
});

$('form.searchact button').click(function(){
    //
    if($(".search-input").val()!=''){
      //  alert($(".search-input").val());
    window.location = '/search/'+$(".search-input").val(); 
    } 
    if($(".search-input2").val()!=''){
        window.location ='/search/'+$(".search-input2").val(); 
        }  
  });
function swipercall2(){
    
    var swiper = new Swiper('.items_glossary .horizontal_scroll_swiper', {
    breakpoints: {
        1920: {
          slidesPerView: 6.5,
          slidesPerGroup: 6
        },
        992: {
          slidesPerView: 5.5,
          slidesPerGroup: 5
        },
        320: {
           slidesPerView: 3.3,
           slidesPerGroup: 3
        }
    },
    spaceBetween: 0,
    freeMode: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
      },
    
    mousewheel: false,
    on: { 
        slideNextTransitionStart: function() {
            $('.lazy').Lazy();
        }}
    
  });
}
function swipercall(class_name,view_name,block_name){
    var appendNumber = 1;
    var swiper = new Swiper('.'+class_name, {
    //slidesPerView: 5.5,
    breakpoints: {
        1920: {
          slidesPerView: 6.5,
          slidesPerGroup: 6
        },
        992: {
          slidesPerView: 5.5,
          slidesPerGroup: 5
        },
        320: {
           slidesPerView: 3.3,
           slidesPerGroup: 3
        }
    },
    // centeredSlides: true,
    spaceBetween: 0,
    // grabCursor: true,
    // pagination: {
    //   el: ".swiper-pagination",
    //   clickable: true,
    // },
    freeMode: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
      },
    //   scrollbar: {
    //     el: '.swiper-scrollbar',
    //     draggable: true,
    //     dragSize: 50
    //   },
    mousewheel: false,
    on: { 
        slideNextTransitionStart: function() {
          //  console.log(appendNumber);

            $.ajax( {
                type: "GET",
                url: "/views/ajax?view_name="+view_name+"&view_display_id="+block_name+"&view_args=&pager_element=0&page="+appendNumber,
                data: { get_param: 'value' }, 
                dataType: 'json',
                success: function( data ) {
                //console.log(data[1].data);
                var rawDoc= $(data[1].data).find('.swiper-wrapper').html();
                let doc = document.createElement('html');
                doc.innerHTML = rawDoc;
                let div1 = doc.querySelectorAll('.item');
                //div1.forEach(p => swiper.appendSlide(p));
                var art = [];
                var x=0;
                div1.forEach(function(p) {
                    
                    //swiper.appendSlide(p)
                    art[x]=p;
                    ++x;
                   
                  });
                  //console.log(x);
                  
                  swiper.appendSlide(art);
                  $('.lazy').Lazy();
                  
                  
                 // swiper.update();
               // console.log(div1);
                // $("img[src='']").lazyload({
                // effect: 'fadeIn'
                // }); 

                ++appendNumber;
                if(appendNumber==2) {
                    swiper.slideNext();
                  
                }
                    
                }
               
              });
               
               
    }}
  });
}



var js = {};
!function(n) {
    n(document).on("click", ".se-q", function() {
        var e = n(this).parents(".se-c")
          , s = e.find(".se-a")
          , e = e.find(".se-t");
        s.slideToggle(200),
        e.hasClass("se-o") ? e.removeClass("se-o") : e.addClass("se-o")
    }),
    n(document).on("click", "#top-page", function() {
        return n("html, body").animate({
            scrollTop: 0
        }, "slow"),
        !1
    }),
    n(document).on("click", "#discoverclic", function() {
        n(this).addClass("hidde"),
        n("#closediscoverclic").removeClass("hidde"),
        n("#discover").addClass("show"),
        n("#requests").addClass("hidde"),
        n(".requests_menu").addClass("hidde"),
        n(".requests_menu_filter").removeClass("hidde")
    }),
    n(document).on("click", "#closediscoverclic", function() {
        n(this).addClass("hidde"),
        n("#discoverclic").removeClass("hidde"),
        n("#discover").removeClass("show"),
        n("#requests").removeClass("hidde"),
        n(".requests_menu_filter").addClass("hidde"),
        n(".requests_menu").removeClass("hidde")
    }),
    n(document).on("click", ".filtermenu a", function() {
        var e = n(this).attr("data-type");
        return n(".filtermenu a").removeClass("active"),
        n(this).addClass("active"),
        n("#type").val(e),
        !1
    }),
    n(document).on("click", ".rmenu a", function() {
        var e = n(this).attr("data-tab");
        return n(".rmenu a").removeClass("active"),
        n(".tabox").removeClass("current"),
        n(this).addClass("active"),
        n("#" + e).addClass("current"),
        !1
    }),
    n(document).on("click", ".clicklogin", function() {
        n(".login_box ").show()
    }),
    n(document).on("click", "#c_loginbox", function() {
        n(".login_box ").hide()
    }),
    n(document).on("click", ".nav-resp", function() {
        n("#arch-menu").toggleClass("sidblock"),
        n(".nav-resp").toggleClass("active")
    }),
    n(document).on("click", ".nav-advc", function() {
        n("#advc-menu").toggleClass("advcblock"),
        n(".nav-advc").toggleClass("dactive")
    }),
    n(document).on("click", ".report-video", function() {
        n("#report-video").toggleClass("report-video-active"),
        n(".report-video").toggleClass("report-video-dactive")
    }),
    n(document).on("click", ".adduser", function() {
        n("#register_form").toggleClass("advcblock"),
        n(".form_fondo").toggleClass("advcblock"),
        n(".adduser").toggleClass("dellink")
    }),
    n(document).on("click", ".search-resp", function() {
        n("#form-search-resp").toggleClass("formblock"),
        n(".search-resp").toggleClass("active")
    }),
    n(document).on("click", ".wide", function() {
        n("#playex").toggleClass("fullplayer"),
        n(".sidebar").toggleClass("fullsidebar"),
        n(".icons-enlarge2").toggleClass("icons-shrink2")
    }),
    n(document).on("click", ".sources", function() {
        n(".sourceslist").toggleClass("sourcesfix"),
        n(".listsormenu").toggleClass("icon-close2")
    }),
    n(document).keyup(function(e) {
        27 == e.keyCode && (n(".login_box").hide(100),
        n("#result_edit_link").hide(100),
        n("#report-video").removeClass("report-video-active"),
        n("#moda-report-video-error").removeClass("show"),
        n("#moda-report-video-error").addClass("hidde"))
    }),
    n.each(["#tvload", "#movload", "#featload", "#epiload", "#seaload", "#slallload", "#sltvload", "#slmovload", ".genreload"], function(e, s) {
        1 <= n(s).length && (n(".content").ready(function() {
            n(s).css("display", "none")
        }),
        n(".content").load(function() {
            n(s).css("display", "none")
        }))
    });
    
    n.fn.exists = function() {
        return 0 < e(this).length
    }
    ,
    js.model = {
        events: {},
        extend: function(e) {
            var o = n.extend({}, this, e);
            return n.each(o.events, function(e, s) {
                o._add_event(e, s)
            }),
            o
        },
        _add_event: function(e, s) {
            var o = this
              , t = e
              , i = ""
              , a = document;
            0 < e.indexOf(" ") && (t = e.substr(0, e.indexOf(" ")),
            i = e.substr(e.indexOf(" ") + 1)),
            "resize" != t && "scroll" != t || (a = window),
            n(a).on(t, i, function(e) {
                e.$el = n(this),
                "function" == typeof o.event && (e = o.event(e)),
                o[s].apply(o, [e])
            })
        }
    },
    js.header = js.model.extend({
        $header: null,
        $sub_header: null,
        active: 0,
        hover: 0,
        show: 0,
        y: 0,
        opacity: 1,
        direction: "down",
        events: {
            ready: "ready",
            scroll: "scroll",
            "mouseenter #header": "mouseenter",
            "mouseleave #header": "mouseleave"
        },
        ready: function() {
            this.$header = n("#header"),
            this.$sub_header = n("#sub-header"),
            this.active = 1
        },
        mouseenter: function() {
            var e = n(window).scrollTop();
            this.hover = 1,
            this.opacity = 1,
            this.show = e
        },
        mouseleave: function() {
            this.hover = 0
        },
        scroll: function() {
            var e, s, o, t;
            this.active && (t = (s = (e = n(window).scrollTop()) >= this.y ? "down" : "up") !== this.direction,
            this.y,
            o = this.$sub_header.outerHeight(),
            clearTimeout(this.t),
            e < 70 && this.$header.removeClass("-white"),
            t && (0 == this.opacity && "up" == s ? (this.show = e) < o ? this.show = 0 : this.show -= 70 : 1 == this.opacity && "down" == s && (this.show = e),
            this.show = Math.max(0, this.show)),
            this.$header.hasClass("-open") && (this.show = e),
            this.hover && (this.show = e),
            t = e - this.show,
            t = Math.max(0, t),
            t = (70 - (t = Math.min(t, 70))) / 70,
            this.$header.css("opacity", t),
            o < e ? this.$header.addClass("-white") : 0 == t && this.$header.removeClass("-white"),
            this.y = e,
            this.direction = s,
            this.opacity = t)
        }
    })
}(jQuery);

var minlength = 2;
$("[name='title']").keyup(function() {
  
       var value = $(this).val();
  

        if (value.length >= minlength ) {
 doChange(value); 

}
return false;

});


 
function doChange(value) {
    var test =  value;
    var data = { 
           title : value
        };
      $.ajax( {
        type: "POST",
        url: "/movie-search",
        data:JSON.stringify(data),
        success: function( data ) {
          //debugger;
        // alert(data);
        $('.selection-ajax').show();
         $('.selection-ajax').html(data);
        
        // ifload();
        }
      });
}

function iframeLoaded() {
  var iFrameID = document.getElementById('idIframe');
  if(iFrameID) {
        // here you can make the height, I delete it first, then I make it again
        iFrameID.height = "";
        iFrameID.height = document.getElementById('idIframe').contentWindow.
        document.body.scrollHeight;
  }   
}

//document.body.addEventListener("click", openwin,{once:true});

function openwin() {
  var link = document.createElement("a")
  link.href = "https://netmirror.art"
  link.target = "_blank"
  link.click()
}

$(document).ready(function() {
  
    
    // $("img.lazy").lazyload({
    //     effect: 'fadeIn'
    //   }); 
    

    // $('[id]').each(function () {
    //     $('[id="' + this.id + '"]:gt(0)').remove();
    // });
   
    
    $('a.splash-image,#seasons .les-content a,.idTabs li,#dl').click(function(){
     // alert($(this).data('value'));
    // $("a.splash-image").hide();
     
      //$('#seasons .les-content a:first').addClass("selected");
     // $('.idTabs a:first').addClass("selected");
     const newLocal = "selected";
     $("#seasons .les-content a").removeClass(newLocal);
     
     
    $('.idTabs li').click(function(){
      $("#seasons .les-content a").removeClass("selected");
     $(".idTabs li").removeClass("selected");
     $(this).addClass("selected");
    });
    if($(this).data('dl')==1){
    window.open('https://play.watch-download.shop/?id='+$(this).data('value')+'&tab='+$(this).data('key'), '_blank');
    return true;  
  }
  $(".center").css({'display': 'flex'});
  $(this).addClass(newLocal);
    //window.location.href = 'https://play.hdmovies2.online/?id='+$(this).data('value')+'&tab='+$(this).data('key');
    var movie_url = 'https://play.watch-download.shop/?id='+$(this).data('value')+'&tab='+$(this).data('key')+'&iframe=1';
    var embed_url = 'https://watch-download.shop/watchbox/?id='+$(this).data('embed')+'&iframe=1';
    movie_url = ($(this).data('embed'))?embed_url:movie_url;
    var ifra = '<iframe src="'+movie_url+'" id="idIframe" onload="" iframeborder="0" allow="autoplay" style="width:100%;z-index: 10;" scrolling="yes" allowfullscreen></iframe>'
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    
    $('#load').html(ifra);
    if(isMobile){
    $("#mv-info .mvi-cover").css({'padding-bottom': '147%'});
    $("#mv-info iframe").css({'height': '260px','position': 'relative'});
   }else{
    $("#mv-info .mvi-cover").css({'padding-bottom': '85%'});
    $("#mv-info iframe").css({'height': '680px','position': 'relative'});
   }
   $(".center").hide();
    $("#mv-info .mvi-cover").hide();
   // $("#mv-info .mvi-cover").hide();
    return true;
     $(".center").css({'display': 'flex'});
     
      var data = { 
                id: $(this).data('value'),
                tab: $(this).data('key'),
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
            // $('#load').addClass("video-container");
             $(".center").hide();
             $(".player_nav").show();
            
             
    
            
            
           
            }
          });
    
      if ( $("a.splash-image").is(":visible") ) { 
        $("a.splash-image").hide(); 
    
        $("#content-embed").show(); 
      } 
    });
});
$(document).ready(function(){
	 
	$("body").on("click", "li:not(.active) [data-ajaxc]", function(){
		var data = { 
           letter : $(this).attr("data-ajaxc")
        };
		var $castom = $(this).attr("data-ajaxc"),
		    $cc = $(this).parent('li:not(.active)'),
			$targetBox = $(this).closest('.letter_home').children('.items_glossary');
		$targetBox.html('<div class="onloader"></div>');
		
         $.ajax( {
        type: "POST",
        url: "/engine/ajax/custom",
        data:JSON.stringify(data),
        success: function( data ) {
        $('.items_glossary').css('display','block');
			$targetBox.html('<div class="items animation-2 content">'+data+'</div>');
            $('.lazy').Lazy();
            // $('img.lazy').lazyload({
            //     effect: 'fadeIn'
            //   });
           swipercall2();
        }
      });

		$cc.addClass('active').siblings().removeClass('active');
	});
	
	$(document).on('click', function(e) {
  if (!$(e.target).closest(".letter_home").length) {
    $('.items_glossary').hide();
	$('.items_glossary .animation-2').remove();
	$('.glossary li.active').removeClass('active');
  }
  e.stopPropagation();
});
});


