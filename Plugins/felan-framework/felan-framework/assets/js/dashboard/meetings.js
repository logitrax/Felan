(function ($) {
  "use strict";
  $(document).ready(function () {
    var ajax_url = felan_meetings_vars.ajax_url;
    var $form = $("#felan-form-reschedule-meeting");
    var meetings_dashboard = $(".meetings-dashboard");
    var not_meetings = felan_meetings_vars.not_meetings;
    var tabs_upcoming = $("#tab-upcoming");
    var tabs_completed = $("#tab-completed");
    var mettings_action = $(".mettings-action-dashboard");
    var meetings_freelancer = $(".meetings-dashboard-freelancer");
    var form_popup = "#felan-form-reschedule-meeting";
    var btn_popup = ".btn-reschedule-meetings";
    var $btn_close = $(form_popup).find(".btn-close");
    var $bg_overlay = $(form_popup).find(".bg-overlay");
    var $btn_cancel = $(form_popup).find(".button-cancel");

    //Meetings Reschedule
    function close_popup(e) {
      e.preventDefault();
      $(form_popup).css({ opacity: "0", visibility: "hidden" });
    }

    $bg_overlay.click(close_popup);
    $btn_close.click(close_popup);
    $btn_cancel.click(close_popup);

    $("body").on("click", btn_popup, function (e) {
      e.preventDefault();
      var id = $(this).data("id");
      $form.find("#btn-meetings-reschedule").attr("applicants-id", id);
      $(form_popup).css({ opacity: "1", visibility: "unset" });
    });

    $("body").on("click", "#btn-meetings-reschedule", function (event) {
      event.preventDefault();
      var applicants_id = $(this).attr("applicants-id"),
        date = $form.find('input[name="date_meetings"]').val(),
        time = $form.find('select[name="time_meetings"]').val(),
        message = $form.find('textarea[name="message_meetings"]').val(),
        timeduration = $form.find('input[name="timeduration_meetings"]').val(),
        action_metting = $('input[name="mettings_action"]').val();

      console.log(action_metting);

      $.ajax({
        type: "POST",
        url: ajax_url,
        dataType: "json",
        data: {
          action: "felan_meetings_reschedule_ajax",
          date: date,
          applicants_id: applicants_id,
          time: time,
          message: message,
          timeduration: timeduration,
          action_metting: action_metting,
        },
        beforeSend: function () {
          $form.find(".btn-loading").fadeIn();
        },
        success: function (data) {
          if (data.success == true) {
            $form.find(".message_error").addClass("true");
            location.reload();
          }
          $form.find(".message_error").html(data.message);
          $form.find(".btn-loading").fadeOut();
        },
        error: function () {
          $form.find(".btn-loading").fadeOut();
        },
      });
    });

    //Meeting Settings
    $("body").on("click", "#btn-saved-meetings", function (event) {
      event.preventDefault();
      var form_settings = $("#felan-form-setting-meetings"),
        link = form_settings.find('input[name="zoomlink"]').val(),
        password = form_settings.find('input[name="zoompw"]').val();

      $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
          action: "felan_meetings_settings",
          link: link,
          password: password,
        },
        beforeSend: function () {
          form_settings.find(".btn-loading").fadeIn();
        },
        success: function () {
          form_settings.find(".btn-loading").fadeOut();
          location.reload();
        },
        error: function () {
          form_settings.find(".btn-loading").fadeOut();
        },
      });
    });

    //Tabs Upcoming
    $("body").on("click", ".btn-delete", function (e) {
      e.preventDefault();
      var item_id = $(this).attr("meeting-id");
      ajax_load_upcoming(item_id, "delete");
    });

    $("body").on("click", ".btn-completed", function (e) {
      e.preventDefault();
      var item_id = $(this).attr("meeting-id");
      ajax_load_upcoming(item_id, "completed");
    });

    $("body").on(
      "click",
      "#tab-upcoming .felan-pagination a.page-numbers",
      function (e) {
        e.preventDefault();
        tabs_upcoming
          .find(".felan-pagination li .page-numbers")
          .removeClass("current");
        $(this).addClass("current");
        var paged = $(this).text();
        var current_page = 1;
        if (
          tabs_upcoming
            .find(".felan-pagination")
            .find('input[name="paged"]')
            .val()
        ) {
          current_page = $(".felan-pagination")
            .find('input[name="paged"]')
            .val();
        }
        if ($(this).hasClass("next")) {
          paged = parseInt(current_page) + 1;
        }
        if ($(this).hasClass("prev")) {
          paged = parseInt(current_page) - 1;
        }
        tabs_upcoming
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(paged);
        ajax_load_upcoming();
      }
    );

    function ajax_load_upcoming(item_id = "", action_click = "") {
      var paged = 1,
        height = tabs_upcoming.find(".row").height(),
        item_amount = tabs_upcoming.find('select[name="item_amount"]').val();
      paged = tabs_upcoming
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_meetings_upcoming_dashboard",
          item_amount: item_amount,
          paged: paged,
          item_id: item_id,
          action_click: action_click,
        },
        beforeSend: function () {
          tabs_upcoming
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
          tabs_upcoming.find(".row").height(height);
        },
        success: function (data) {
          if (data.success === true) {
            tabs_upcoming.find(".pagination").html(data.pagination);
            tabs_upcoming.find(".row").fadeOut("fast", function () {
              tabs_upcoming.find(".row").html(data.meetings_html);
              tabs_upcoming.find(".row").fadeIn(300);
            });
            tabs_upcoming.find(".row").css("height", "auto");
          } else {
            tabs_upcoming
              .find(".row")
              .html('<span class="not-meetings">' + not_meetings + "</span>");
          }
          tabs_upcoming
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    }

    //Tabs Completed
    $("body").on("click", ".btn-delete", function (e) {
      e.preventDefault();
      var item_id = $(this).attr("meeting-id");
      ajax_load_completed(item_id, "delete");
    });

    $("body").on("click", ".btn-upcoming", function (e) {
      e.preventDefault();
      var item_id = $(this).attr("meeting-id");
      ajax_load_completed(item_id, "upcoming");
    });

    $("body").on(
      "click",
      "#tab-completed .felan-pagination a.page-numbers",
      function (e) {
        e.preventDefault();
        tabs_completed
          .find(".felan-pagination li .page-numbers")
          .removeClass("current");
        $(this).addClass("current");
        var paged = $(this).text();
        var current_page = 1;
        if (
          tabs_completed
            .find(".felan-pagination")
            .find('input[name="paged"]')
            .val()
        ) {
          current_page = $(".felan-pagination")
            .find('input[name="paged"]')
            .val();
        }
        if ($(this).hasClass("next")) {
          paged = parseInt(current_page) + 1;
        }
        if ($(this).hasClass("prev")) {
          paged = parseInt(current_page) - 1;
        }
        tabs_completed
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(paged);
        ajax_load_completed();
      }
    );

    function ajax_load_completed(item_id = "", action_click = "") {
      var paged = 1,
        height = tabs_completed.find(".row").height(),
        item_amount = tabs_completed.find('select[name="item_amount"]').val();
      paged = tabs_completed
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_meetings_completed_dashboard",
          item_amount: item_amount,
          paged: paged,
          item_id: item_id,
          action_click: action_click,
        },
        beforeSend: function () {
          tabs_completed
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
          tabs_completed.find(".row").height(height);
        },
        success: function (data) {
          if (data.success === true) {
            tabs_completed.find(".pagination").html(data.pagination);
            tabs_completed.find(".row").fadeOut("fast", function () {
              tabs_completed.find(".row").html(data.meetings_html);
              tabs_completed.find(".row").fadeIn(300);
            });
            tabs_completed.find(".row").css("height", "auto");
          } else {
            tabs_completed
              .find(".row")
              .html('<span class="not-meetings">' + not_meetings + "</span>");
          }
          tabs_completed
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    }

    //Tabs
    meetings_dashboard.find(".tab-list li").click(function () {
      var item_id = $(this).attr("meeting-id");
      var tab_href = $(this).find("a").attr("href");
      var tab_name = tab_href.replace("#", "");
      if (tab_name == "tab-upcoming") {
        ajax_load_upcoming(item_id, "");
      } else {
        ajax_load_completed(item_id, "");
      }
    });

    //Freelancer
    $("body").on(
      "click",
      ".meetings-dashboard-freelancer .felan-pagination a.page-numbers",
      function (e) {
        e.preventDefault();
        meetings_freelancer
          .find(".felan-pagination li .page-numbers")
          .removeClass("current");
        $(this).addClass("current");
        var paged = $(this).text();
        var current_page = 1;
        if (
          meetings_freelancer
            .find(".felan-pagination")
            .find('input[name="paged"]')
            .val()
        ) {
          current_page = $(".felan-pagination")
            .find('input[name="paged"]')
            .val();
        }
        if ($(this).hasClass("next")) {
          paged = parseInt(current_page) + 1;
        }
        if ($(this).hasClass("prev")) {
          paged = parseInt(current_page) - 1;
        }
        meetings_freelancer
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(paged);
        ajax_load_freelancer();
      }
    );

    function ajax_load_freelancer(item_id = "") {
      var paged = 1,
        height = meetings_freelancer.find(".row").height(),
        item_amount = meetings_freelancer
          .find('select[name="item_amount"]')
          .val();
      paged = meetings_freelancer
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_meetings_freelancer_dashboard",
          item_amount: item_amount,
          paged: paged,
          item_id: item_id,
        },
        beforeSend: function () {
          meetings_freelancer
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
          meetings_freelancer.find(".row").height(height);
        },
        success: function (data) {
          if (data.success === true) {
            meetings_freelancer.find(".pagination").html(data.pagination);
            meetings_freelancer.find(".row").fadeOut("fast", function () {
              meetings_freelancer.find(".row").html(data.meetings_html);
              meetings_freelancer.find(".row").fadeIn(300);
            });
            meetings_freelancer.find(".row").css("height", "auto");
          } else {
            meetings_freelancer
              .find(".row")
              .html('<span class="not-meetings">' + not_meetings + "</span>");
          }
          meetings_freelancer
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    }
  });
})(jQuery);
