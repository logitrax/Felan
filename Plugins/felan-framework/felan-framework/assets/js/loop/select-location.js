jQuery(document).ready(function ($) {
  var ajax_url = felan_template_vars.ajax_url;

  $("select.felan-select-country").change(function () {
    var _this = $(this),
      post_type = _this.data("post-type"),
      country = _this.val(),
      state = $(".felan-select-state"),
      city = $(".felan-select-city");

    $.ajax({
      type: "post",
      url: ajax_url,
      dataType: "json",
      data: {
        action: "felan_select_country",
        post_type: post_type,
        country: country,
      },
      beforeSend: function () {
		state.val('');
		city.val('');
        state.parent(".form-group").addClass("load-spinner");
        state
          .parent(".form-group")
          .append('<i class="spinner fal fa-spinner fa-spin"></i>');
      },
      success: function (data) {
        if (data.success) {
          state.parent(".form-group").removeClass("load-spinner");
          state
            .parent(".form-group")
            .find(".spinner")
            .removeClass("fal fa-spinner fa-spin");
          state.find("option:not(:first-child)").remove();
          state.append(data.state_html);

          _this.each(function () {
            if (
              _this.val() !== "" ||
              $("select.felan-select-state").val() !== ""
            ) {
              $(".felan-nav-filter").addClass("active");
              _this.closest(".entry-filter").addClass("open");
              $(".archive-layout").find(".felan-clear-filter").show();
            } else {
              $(".felan-nav-filter").removeClass("active");
              _this.closest(".entry-filter").removeClass("open");
              $(".archive-layout").find(".felan-clear-filter").hide();
            }
          });
        }
      },
    });
  });

  $("select.felan-select-state").change(function () {
    var _this = $(this),
      post_type = _this.data("post-type"),
      state = _this.val(),
      city = $(".felan-select-city");

    $.ajax({
      type: "post",
      url: ajax_url,
      dataType: "json",
      data: {
        action: "felan_select_state",
        post_type: post_type,
        state: state,
      },
      beforeSend: function () {
		city.val('');
        city.parent(".form-group").addClass("load-spinner");
        city
          .parent(".form-group")
          .append('<i class="spinner fal fa-spinner fa-spin"></i>');
      },
      success: function (data) {
        if (data.success) {
          city.parent(".form-group").removeClass("load-spinner");
          city
            .parent(".form-group")
            .find(".spinner")
            .removeClass("fal fa-spinner fa-spin");
          city.find("option:not(:first-child)").remove();
          city.append(data.city_html);

          _this.each(function () {
            if (
              _this.val() !== "" ||
              $("select.felan-select-country").val() !== ""
            ) {
              $(".felan-nav-filter").addClass("active");
              _this.closest(".entry-filter").addClass("open");
              $(".archive-layout").find(".felan-clear-filter").show();
            } else {
              $(".felan-nav-filter").removeClass("active");
              _this.closest(".entry-filter").removeClass("open");
              $(".archive-layout").find(".felan-clear-filter").hide();
            }
          });
        }
      },
    });
  });
});
