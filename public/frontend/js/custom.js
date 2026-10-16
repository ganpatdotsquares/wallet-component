/************Main**Top*Menu******************************/


$(function () {
    var html = $('html, body'),
        navContainer = $('.nav-container'),
        navToggle = $('.nav-toggle'),
        navDropdownToggle = $('.has-dropdown');

    // Nav toggle
    navToggle.on('click', function (e) {
        var $this = $(this);
        e.preventDefault();
        $this.toggleClass('is-active');
        navContainer.toggleClass('is-visible');
        html.toggleClass('nav-open');
    });

    // Nav dropdown toggle
    navDropdownToggle.on('click', function () {
        var $this = $(this);
        $this.toggleClass('is-active').children('ul').toggleClass('is-visible');
    });

    // Prevent click events from firing on children of navDropdownToggle
    navDropdownToggle.on('click', '*', function (e) {
        e.stopPropagation();
    });
});



/**************************************************/



const sections = document.querySelectorAll("section[id]");

// Add an event listener listening for scroll
window.addEventListener("scroll", navHighlighter);

function navHighlighter() {

    // Get current scroll position
    let scrollY = window.pageYOffset;

    // Now we loop through sections to get height, top and ID values for each
    sections.forEach(current => {
        const sectionHeight = current.offsetHeight;

        // Original:
        // const sectionTop = current.offsetTop - 50;
        //  
        // Alex Turnwall's update:
        // Updated original line (above) to use getBoundingClientRect instead of offsetTop, based on:
        // https://plainjs.com/javascript/styles/get-the-position-of-an-element-relative-to-the-document-24/
        // https://newbedev.com/difference-between-getboundingclientrect-top-and-offsettop
        // This allows the use of sections inside a relative parent, which I'm not using here, but needed for a project
        //
        const sectionTop = (current.getBoundingClientRect().top + window.pageYOffset) - 50;
        sectionId = current.getAttribute("id");

        /*
        - If our current scroll position enters the space where current section on screen is, add .active class to corresponding navigation link, else remove it
        - To know which link needs an active class, we use sectionId variable we are getting while looping through sections as an selector
        */
        if (
            scrollY > sectionTop &&
            scrollY <= sectionTop + sectionHeight
        ) {
            document.querySelector(".navigation a[href*=" + sectionId + "]").classList.add("active");
        } else {
            document.querySelector(".navigation a[href*=" + sectionId + "]").classList.remove("active");
        }
    });
}


/*********************Section**************************/






/*******************************************/

/*******************************************/




$(document).ready(function () {
    $('#talent-slider').owlCarousel({
        loop: true,
        margin: 20,
        nav: false,
        dots: true,
        autoplay: true,
        responsive: {
            0: {
                items: 1
            },
            767: {
                items: 3
            },
            1024: {
                items: 4
            }

        }
    })
});



$(document).ready(function () {
    $('#category-slider').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        dots: false,
        autoplay: true,
        responsive: {
            0: {
                items: 1
            },
            767: {
                items: 3
            },
            1024: {
                items: 4
            },
            1300: {
                items: 5
            }

        }
    })
});



$(document).ready(function () {
    $('#testimonials-slider').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        dots: true,
        autoplay: true,
        responsive: {
            0: {
                items: 1
            },
            767: {
                items: 1
            },
            1024: {
                items: 1
            }

        }
    })
});


$(document).ready(function () {
    $('#home-slider').owlCarousel({
        loop: true,
        margin: 0,
        nav: false,
        dots: true,
        autoplay: true,
        responsive: {
            0: {
                items: 1
            },
            767: {
                items: 1
            },
            1024: {
                items: 1
            }

        }
    })
});









// Enable the Tabs

$('#myTabs a').click(function (e) {
    e.preventDefault()
    $(this).tab('show')
});




/*$(function(){
$('.dropdown').hover(function() {
$(this).addClass('open');
$(this).find('.dropdown').stop(true, true).delay(0).fadeIn(0);
}, function() {
$(this).removeClass('open');
$(this).find('.dropdown').stop(true, true).delay(0).fadeOut(0);
});
});
*/






/*
  var affixElement = '.header';

$(affixElement).affix({
  offset: {
    // Distance of between element and top page
    top: function () {
      return (this.top = $(affixElement).offset().top)
    },
    // when start #footer 
    bottom: function () { 
      return (this.bottom = $('#footer').outerHeight(true))
    }
  }
});
*/

$(window).scroll(function () {
    var scroll = $(window).scrollTop();

    if (scroll >= 50) {
        $(".header-inner").addClass("header-sticky");
    } else {
        $(".header-inner").removeClass("header-sticky");



    }
});







/********Bootstrap**Modal****Start*************/

$('#myModal').on('shown.bs.modal', function () {
    $('#myInput').trigger('focus')
})

/********Bootstrap**Modal***End**************/




$("#accordion").on("hide.bs.collapse show.bs.collapse", e => {
    $(e.target)
        .prev()
        .find("i:last-child")
        .toggleClass("fa-minus fa-plus");
});












function openNav() {
    document.getElementById("mySidenav").style.width = "260px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}



$('#head-search-btn').click(function () {
    $(this).toggleClass('open-search');
    $('#search-box-dropdown').toggleClass('open-search-box');
});




function myFunction() {
    var element = document.getElementById("summary-panel");
    element.classList.toggle("open-summay");
}


$(function () {
    $('.dropdown').hover(function () {
        $(this).addClass('open');
        $(this).find('.dropdown').stop(true, true).delay(0).fadeIn(0);
    }, function () {
        $(this).removeClass('open');
        $(this).find('.dropdown').stop(true, true).delay(0).fadeOut(0);
    });
});