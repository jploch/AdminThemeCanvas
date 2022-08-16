// edit jp
$(document).ready(function () {

  if (window.location !== window.parent.location || $('body').hasClass('modal')) {
    // The page is in an iframe
    return false;
  }

  //add placeholder to login inputs
  $('#login_name').attr('placeholder', $('#wrap_login_name label').text());
  $('#login_pass').attr('placeholder', $('#wrap_login_pass label').text());

  // title to settings tab
  $('.hide-title #ProcessPageEditSettings').find('.Inputfields').first().prepend($('.InputfieldPageTitle').first());

  // fix dropdowns on the right 
  $(document).on("mouseenter", ".pw-dropdown-menu", function (e) {

    var attr = $(this).attr('data-at');

    if (!(attr === 'right bottom')) {
      return;
    }

    var offset = $(this).offset();
    var dropdownOffset = offset.left;
    var dropID = $(this).attr('id');
    var style = $('<style id="dropstyle-' + dropID + '"> #' + dropID + '{ left:' + dropdownOffset + 'px!important; }</style>');

    if (!($('#dropstyle-' + dropID).length)) {
      $('html > head').append(style);
    }

  });

  // fix tree dropdown
  $(document).on("mouseenter", ".page-3-", function (e) {

    var offset = $(this).offset();
    var dropdownOffset = offset.left;
    var style = $('<style id="dropstyle-page"> .pw-dropdown-menu.page { left:' + dropdownOffset + 'px!important; }</style>');

    $('#dropstyle-page').remove();
    $('html > head').append(style);

  });

  // spawn page-tree ajax nav
  $('.page-8- a').first().trigger('mouseenter');
  $(".pw-dropdown-menu.page .subnav-list").addClass("show");
  $('.pw-dropdown-menu.page .nav-add').remove();
  $('.ProcessPageList #pw-content-head-buttons').append($('.pw-dropdown-menu.page').children().not('.page-8-'));
  $('.ProcessPageList #pw-content-head-buttons a').addClass('ui-button ui-widget ui-corner-all ui-state-default');
  $('.ProcessPageList #pw-content-head-buttons .pw-has-items-icon').hide();
  $('.ProcessPageList #pw-content-head-buttons li a').unwrap();
  $('.ProcessPageList #pw-content-head-buttons ul').hide();
  $('.pw-dropdown-menu.page li').hide();


  $(document).on("mouseenter", ".page-3-", function () {
    $('.pw-dropdown-menu.page').append($('.pw-dropdown-menu.page .subnav-list.navJSON li'));
    $('.pw-dropdown-menu.page .subnav-list.navJSON').removeClass('navJSON');
  });

  $(document).on("mouseenter", ".pw-dropdown-menu.page ul li", function () {

    var offset = $(this).offset();
    var dropdownOffset = offset.top;
    var dropdownHeight = $(this).parent().height() + dropdownOffset;
    var windowHeight = $(window).height() - 100;

    if ($(this).find('.pw-has-items').length) {
      $(this).parent().css('min-width', '100vw');
      $(this).parent().css('height', '60vh');

    } else {
      $(this).parent().css('height', '');
      $(this).parent().css('min-width', '');
    }

  });
  // END spawn page-tree ajax nav 


  // put tree add-new link on top
  $(document).ajaxComplete(function () {
    $(".pw-dropdown-menu.page ul li").each(function () {
      if ($(this).hasClass('pw-nav-add')) {
        $(this).parent().prepend($(this));
      }
    });
  });


  // END spawn page-tree ajax nav 

  //  hide content if only title exists
  setTimeout(function () {
    if (!$('#ProcessPageEditContent').find('.Inputfield').length) {
      //      $('#ProcessPageEditContent').css('visibility', 'hidden');
    }
    //to remember wich tab was open after save (edit or settings)
    if ($("#_ProcessPageEditContent").hasClass('pw-active')) {
      $('.pw-page-nav li').removeClass('uk-active');
      $('.pw-page-nav li.ProcessPageEditPage').addClass('uk-active');
      $('body').removeClass('settings-active');
      $('#_ProcessPageEditContent').trigger('click');
    } else {
      $('.pw-page-nav li').removeClass('uk-active');
      $('.pw-page-nav li.ProcessPageEditSettings').addClass('uk-active');
    }

  }, 100);


  $('a.pw-logo-link').click(this.logoClickEvent);

  if ($('body').hasClass('pw-layout-sidenav-init')) {
    $('a.pw-tree-nav').click(function (e) {
      var last_part = $(location).attr('href').substring(this.href.lastIndexOf('/') + 1);
      console.log(last_part);
      e.preventDefault();
      if (last_part == 'page/' || last_part == 'page/list/') {
        return false;
      }
      $('body').toggleClass('ui-sidebar-active');
      $('.ui-layout-toggler').trigger('click');
    });
  }
  if ($('body').hasClass('tree-panel-active')) {
    $('a.pw-tree-nav').click(function (e) {
      if ($('body').hasClass('ProcessPageEdit')) {
        e.preventDefault();
        $('.ProcessPageEdit .uk-breadcrumb .pw-panel').trigger('click');
      }
    });
  }


  // option title in breadcrump
  $('.breadcrumb-with-title.ProcessPageEdit .uk-breadcrumb').append('<li><a href="../" class="breadcrump-title"></a></li>');
  $('.breadcrumb-with-title.ProcessPageEdit .breadcrump-title').append($('#pw-content-head h1').text());

  // add headline to SEO tab
  $('#Inputfield_seo_tab .InputfieldHeader').first().prepend('<h3>SEO</h3>')


  $('body.ProcessPageEdit').prepend($("#PageEditTabs"));
  $(".ProcessPageEdit #PageEditTabs").removeClass();
  $(".ProcessPageEdit #PageEditTabs li").removeClass();
  $(".ProcessPageEdit #PageEditTabs").attr('');

  $(".ProcessPageEdit #PageEditTabs").addClass('pw-dropdown-custom-settings prnav pw-dropdown-menu pw-dropdown-disabled ui-menu ui-widget-content ui-widget ui-corner-all');
  $('.ProcessPageEdit #PageEditTabs li').addClass('ui-menu-item');
  $('.ProcessPageEdit #PageEditTabs li a').addClass('ui-corner-all');


  //  $('.pw-dropdown-custom-settings').find('li').first().append($('#_ProcessPageEditSettings'));

  //search
  $(".pw-search-input").focusin(function () {
    console.log('search is active');
    $('.pw-search-form').addClass('search-active');
  });

  $(".pw-search-input").focusout(function () {
    console.log('search is not active');
    $('.pw-search-form').removeClass('search-active');
  });

  //expand field on click improved
  //     $('label.InputfieldStateToggle').parent().addClass('InputfieldFieldsetOpen FieldtypeFieldsetOpen InputfieldFieldset InputfieldStateWasCollapsed');
  //END expand field on click improved

  $(document).on("mouseenter", ".ProcessPageEditSettings, .pw-dropdown-custom-settings", function () {
    var dropdownOffset = $('.ProcessPageEditSettings').offset();
    $('.pw-dropdown-custom-settings').css('left', dropdownOffset.left);
    $('body').addClass('custom-settings-active');
  });

  $(document).on("mouseleave", ".ProcessPageEditSettings, .pw-dropdown-custom-settings", function () {
    $('body').removeClass('custom-settings-active');
  });

  $('.ProcessPageEditSettings').click(function (e) {
    e.preventDefault();
    $('body').addClass('settings-active');
    $('#_ProcessPageEditSettings').trigger('click');
    $('.uk-navbar-container li').removeClass('uk-active');
    $('.ProcessPageEditSettings').toggleClass('uk-active');
  });

  $('.ProcessPageEditPage').click(function (e) {
    e.preventDefault();
    $('body').removeClass('settings-active');
    $('#_ProcessPageEditContent').trigger('click');
    $('.uk-navbar-container li').removeClass('uk-active');
    $(this).addClass('uk-active');
  });

  $('#_ProcessPageEditContent').click(function (e) {
    $('body').removeClass('settings-active');
  });

  $('.pw-dropdown-custom-settings li a').click(function (e) {
    //          e.preventDefault();
    if ($(this).is('#_ProcessPageEditContent')) {
      return;
    }
    $('body').addClass('settings-active');
    $('.uk-navbar-container li').removeClass('uk-active');
    //    $('.ProcessPageEditSettings').addClass('uk-active');
  });

  $(document).on('click', '.ProcessPageEditPage, .ProcessPageEditSettings', function (e) {
    e.preventDefault();
    $('html, body').stop().animate({
      scrollTop: 0
    }, 300, 'swing',
      function () { });
  });

  // open view links in new tab
  $(document).on("mouseenter", ".PageListActionView a", function () {
    $(this).attr('target', '_blank');
  });

  // view links in page edit
  if ($('#_ProcessPageEditView').length) {
    var viewUrl = $('#_ProcessPageEditView').attr('href');
    $('.ProcessPageEditView').show();
    $('.ProcessPageEditView a').attr('href', viewUrl);
    $('.ProcessPageEditView a').attr('target', '_blank');
  }

  // open view links in modal
  //     $(document).on("mouseenter", ".PageListActionView a", function () {
  //          $(this).removeClass('pw-modal-longclick');
  //     });

  // view links in page edit
  //     if ($('#_ProcessPageEditView').length) {
  //          $('.ProcessPageEditView').show();
  //          $('.ProcessPageEditView').click(function (e) {
  //               e.preventDefault();
  //               $('.page-view-action-modal  .pw-modal-large').first().trigger('click');
  //          });
  //     }

});

// END edit jp
