$(document).on('ready, pjax:complete', function(event) {

  var swiper2 = new Swiper('.swiper-container2', {
    slidesPerView: '5',
    spaceBetween: '12',
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 12
      },
      480: {
        slidesPerView: 2,
        spaceBetween: 12
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 12
      },
      1200: {
        slidesPerView: 5,
        spaceBetween: 12
      }
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
  });

var galleryThumbs = new Swiper('.gallery-thumbs', {
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesVisibility: true,
    watchSlidesProgress: true,
    direction: 'horizontal',
    breakpoints: {
      320: {
        slidesPerView: 2,
        spaceBetween: 10
      },
      1200: {
        slidesPerView: 4,
        spaceBetween: 10
      }
    }
});

var galleryTop = new Swiper('.gallery-top', {
    thumbs: {
      swiper: galleryThumbs
    },
    direction: 'horizontal',
    slidesPerView: 1,
  });
});

var swiper2 = new Swiper('.swiper-container2', {
  slidesPerView: '5',
  spaceBetween: '12',
  breakpoints: {
    320: {
      slidesPerView: 1,
      spaceBetween: 12
    },
    480: {
      slidesPerView: 2,
      spaceBetween: 12
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 12
    },
    1200: {
      slidesPerView: 5,
      spaceBetween: 12
    }
  },
  navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

var galleryThumbs = new Swiper('.gallery-thumbs', {
  spaceBetween: 10,
  slidesPerView: 4,
  freeMode: true,
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
  direction: 'horizontal',
});

var galleryTop = new Swiper('.gallery-top', {
  spaceBetween: 10,
  thumbs: {
    swiper: galleryThumbs
  },
  direction: 'horizontal',
  slidesPerView: 1,
});

$(function () {

  $('.headerSettingsText').click(function(){
    $('.headerSettingsOptions').toggleClass('active1')
  })

  $('.js-inputGroupSelectNav').on('change', function () {
    window.location.replace($(this).val());
  });
  $('input[name="inlineRadioOptions"]').on('change', function () {
    window.location.replace($(this).val());
  });
});
