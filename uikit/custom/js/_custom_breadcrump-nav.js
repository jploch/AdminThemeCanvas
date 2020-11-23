// edit jp
$(document).ready(function () {

  console.log('CUSTOM LOADED');

  //		$('a.pw-logo-link').click(this.logoClickEvent);
  if ($('body').hasClass('pw-layout-sidenav-init')) {
    $('a.pw-logo-link').click(function (e) {
      var last_part = $(location).attr('href').substring(this.href.lastIndexOf('/') + 1);
      console.log(last_part);
      e.preventDefault();
      if (last_part == 'page/' || last_part == 'page/list/') {
        return false;
      }
      $('body').toggleClass('ui-sidebar-active');
      $('.ui-layout-toggler').trigger('click');
    });
  } else {
    $('a.pw-logo-link').click(function (e) {
      //             if($('body').hasClass('ProcessPageEdit')){
      //           e.preventDefault();
      //          $('.ProcessPageEdit .uk-breadcrumb .pw-panel').trigger('click');
      //                }
    });
  }

  // nav label
  var pwlanguage = $('html')[0].lang;
  var viewLink = 'View';
  var settingsLink = 'Settings';
  var pageLink = 'Page';

  if (pwlanguage == 'de') {
    viewLink = 'Ansehen';
    settingsLink = 'Einstellungen';
    pageLink = 'Bearbeiten';
  } else {
    viewLink = 'View';
    settingsLink = 'Settings';
    pageLink = 'Edit';
  }

  $('body').prepend($("#PageEditTabs"));

  $("#PageEditTabs").removeClass();
  $("#PageEditTabs li").removeClass();

  $(".ProcessPageEdit #PageEditTabs").first().addClass('pw-dropdown-custom-settings pw-dropdown-menu prnav pw-dropdown-ready ui-menu ui-widget ui-widget-content ui-corner-all');

  //$('.pw-dropdown-custom-settings').append($('#PageEditTabs'));

  $('.pw-dropdown-custom-settings').find('li').first().append($('#_ProcessPageEditSettings').first());

  $('.ProcessPageEdit .pw-primary-nav').append('<li class="ProcessPageEditSettings"><a href="#" title="' + settingsLink + '"><i class="fa fa-gear pw-nav-icon"></i></a></li>');

  $('.pw-primary-nav').prepend('<li class="breadcrumb-nav"></li>');

  $('.pw-primary-nav .breadcrumb-nav').prepend($('.uk-breadcrumb'));


  //move main nav setup, module, access to the right
  $('.uk-navbar-right .pw-user-nav').prepend($("li.page-28-").first());
  $('.uk-navbar-right .pw-user-nav').prepend($("li.page-21-").first());
  $('.uk-navbar-right .pw-user-nav').prepend($("li.page-22-").first());

  // change page link name
  //  $('.page-3- a').first().text(pageLink);
  //  $('.page-3- a').first().prepend('<i class="fa fa-fw fa-pencil pw-nav-icon"></i>');

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
  $('label.InputfieldStateToggle').parent().addClass('InputfieldFieldsetOpen FieldtypeFieldsetOpen InputfieldFieldset InputfieldStateWasCollapsed');
  //END expand field on click improved

  $(".ProcessPageEditSettings").on({
    mouseenter: function () {
      $('body').addClass('custom-settings-active');
      var dropdownOffset = $('.ProcessPageEditSettings').offset();
      console.log(dropdownOffset);
      $('.pw-dropdown-custom-settings').css('left', dropdownOffset.left);
    },
    mouseleave: function () {
      $('body').removeClass('custom-settings-active');
    }
  });
  $(".pw-dropdown-custom-settings").on({
    mouseenter: function () {
      $('body').addClass('custom-settings-active');
      //      $('.ProcessPageEditSettings').addClass('uk-active');
    },
    mouseleave: function () {
      $('body').removeClass('custom-settings-active');
      //      $('.ProcessPageEditSettings').removeClass('uk-active');
    }
  });

  $('.ProcessPageEditSettings').click(function (e) {
    $('#_ProcessPageEditSettings').trigger('click');
    $('.uk-navbar-container li').removeClass('uk-active');
    $('.ProcessPageEditSettings').toggleClass('uk-active');
  });

  $('.ProcessPageEdit .page-3-').click(function (e) {
    e.preventDefault();
    $('#_ProcessPageEditContent').trigger('click');
    $('.uk-navbar-container li').removeClass('uk-active');
    $(this).addClass('uk-active');
  });

  $('.pw-dropdown-custom-settings a').click(function (e) {
    $('.uk-navbar-container li').removeClass('uk-active');
    $('.ProcessPageEditSettings').addClass('uk-active');
  });


  $('#ProcessPageEditSettings').find('.Inputfields').first().prepend($('.InputfieldPageTitle'));


  // open view links in modal
  setTimeout(function () {
    $('.PageListActionView a').each(function (index) {
      //      $(this).removeClass('pw-modal-longclick');
    });
  }, 2000);

  // view links in page edit
  if ($('#_ProcessPageEditView').length) {

    $('.ProcessPageEdit .pw-primary-nav').append('<li class="ProcessPageEditView"><a href="#" title="' + viewLink + '"><i class="fa fa-eye pw-nav-icon fa-fw"></i></a></li>');


    $('.ProcessPageEditView').click(function (e) {
      e.preventDefault();
      //      $('.page-view-action-this a').first().trigger('click');
      $('.page-view-action-modal  .pw-modal-large').first().trigger('click');
    });
  }
});
