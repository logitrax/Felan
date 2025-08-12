(function ($) {
  "use strict";
  var ajax_url = felan_avatar_vars.ajax_url,
    avatar_title = felan_avatar_vars.avatar_title,
    avatar_type = felan_avatar_vars.avatar_type,
    avatar_file_size = felan_avatar_vars.avatar_file_size,
    avatar_text = felan_avatar_vars.avatar_text,
    avatar_url = felan_avatar_vars.avatar_url,
    avatar_upload_nonce = felan_avatar_vars.avatar_upload_nonce;

  jQuery(document).ready(function () {
    var felan_avatar = function () {
      var uploader_avatar = new plupload.Uploader({
        browse_button: "felan_select_avatar",
        file_data_name: "felan_avatar_upload_file",
        drop_element: "felan_avatar_view",
        container: "felan_avatar_container",
        url: avatar_url,
        filters: {
          mime_types: [
            {
              title: avatar_title,
              extensions: avatar_type,
            },
          ],
          max_file_size: avatar_file_size,
          prevent_duplicates: true,
        },
      });
      uploader_avatar.init();

      uploader_avatar.bind("UploadProgress", function (up, file) {
        $("#felan_add_avatar .la-upload").hide();
        document.getElementById("felan_select_avatar").innerHTML =
          '<span><i class="fal fa-spinner fa-spin large"></i></span>';
      });

      uploader_avatar.bind("FilesAdded", function (up, files) {
        up.refresh();
        uploader_avatar.start();
      });
      uploader_avatar.bind("Error", function (up, err) {
        document.getElementById("felan_avatar_errors").innerHTML +=
          "Error #" + err.code + ": " + err.message + "<br/>";
      });

      var $image_id = $("#felan_avatar_view").data("image-id");
      var $image_url = $("#felan_avatar_view").data("image-url");
      if ($image_id && $image_url) {
        var $html =
          '<figure class="media-thumb media-thumb-wrap">' +
          '<img src="' +
          $image_url +
          '">' +
          '<div class="media-item-actions">' +
          '<a class="icon icon-avatar-delete" data-attachment-id="' +
          $image_id +
          '" href="#" ><i class="far fa-trash-alt large"></i></a>' +
          '<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
          "</div>" +
          "</figure>";
        $("#felan_avatar_view").html($html);
        $("#felan_add_avatar").hide();
      }
      uploader_avatar.bind("FileUploaded", function (up, file, ajax_response) {
        document.getElementById("felan_drop_avatar").style.display = "none";
        var response = $.parseJSON(ajax_response.response);
        if (response.success) {
          $("input.avatar_url").val(response.full_image);
          $("input.avatar_id").val(response.attachment_id);
          var $html =
            '<figure class="media-thumb media-thumb-wrap">' +
            '<img src="' +
            response.full_image +
            '">' +
            '<div class="media-item-actions">' +
            '<a class="icon icon-avatar-delete" data-attachment-id="' +
            response.attachment_id +
            '" href="#" ><i class="far fa-trash-alt large"></i></a>' +
            '<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
            "</div>" +
            "</figure>";
          $("#felan_avatar_view").html($html);
          felan_avatar_delete();
          $("#felan_add_avatar .la-upload").hide();
          $("#avatar_url-error").hide();
          if ($(".form-dashboard").hasClass("freelancer-profile-form")) {
            $("#freelancer-profile-form").find(".point-mark").change();
          }
        }

        //Company
        var $company_avatar_url = $("#submit_company_form input.avatar_url");
        console.log($company_avatar_url.val());
        var $about = $(".about-company-dashboard");
        $about
          .find(".img-company")
          .html('<img src="' + $company_avatar_url.val() + '" alt="">');
      });
    };
    felan_avatar();

    var felan_avatar_delete = function ($type) {
      $("body").on("click", ".icon-avatar-delete", function (e) {
        e.preventDefault();
        var $this = $(this),
          icon_delete = $this,
          avatar = $this
            .closest("#felan_avatar_view")
            .find(".media-thumb-wrap"),
          attachment_id = $this.data("attachment-id"),
          $drop = $("#felan_drop_avatar");

        icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');

        $.ajax({
          type: "post",
          url: ajax_url,
          dataType: "json",
          data: {
            action: "felan_avatar_remove_ajax",
            attachment_id: attachment_id,
            type: $type,
            removeNonce: avatar_upload_nonce,
          },
          success: function (response) {
            if (response.success) {
              avatar.remove();
              avatar.hide();

              $("#avatar_url-error").show();
              $("#felan_add_avatar").show();
            }
            icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');
            $drop.css("display", "block");
            $("#felan_add_avatar .la-upload").show();
            $("#felan_select_avatar").html(avatar_text);
            $("input.avatar_url").val("");
            $("input.avatar_id").val("");
            if ($(".form-dashboard").hasClass("freelancer-profile-form")) {
              $("#freelancer-profile-form").find(".point-mark").change();
            }

            //Company
            var $about = $(".about-company-dashboard");
            $about.find(".img-company").html('<i class="far fa-camera"></i>');
          },
          error: function () {
            icon_delete.html('<i class="far fa-trash-alt large"></i>');
          },
        });
      });
    };
    felan_avatar_delete();
  });
})(jQuery);
