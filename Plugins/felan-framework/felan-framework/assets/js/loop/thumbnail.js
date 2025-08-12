(function ($) {
  "use strict";
  var ajax_url = felan_thumbnail_vars.ajax_url,
    thumbnail_title = felan_thumbnail_vars.thumbnail_title,
    thumbnail_type = felan_thumbnail_vars.thumbnail_type,
    thumbnail_file_size = felan_thumbnail_vars.thumbnail_file_size,
    thumbnail_text = felan_thumbnail_vars.thumbnail_text,
    thumbnail_url = felan_thumbnail_vars.thumbnail_url,
    thumbnail_upload_nonce = felan_thumbnail_vars.thumbnail_upload_nonce;

  jQuery(document).ready(function () {
    var felan_thumbnail = function () {
      var uploader_thumbnail = new plupload.Uploader({
        browse_button: "felan_select_thumbnail",
        file_data_name: "felan_thumbnail_upload_file",
        drop_element: "felan_thumbnail_view",
        container: "felan_thumbnail_container",
        url: thumbnail_url,
        filters: {
          mime_types: [
            {
              title: thumbnail_title,
              extensions: thumbnail_type,
            },
          ],
          max_file_size: thumbnail_file_size,
          prevent_duplicates: true,
        },
      });
      uploader_thumbnail.init();

      uploader_thumbnail.bind("UploadProgress", function (up, file) {
        document.getElementById("felan_select_thumbnail").innerHTML =
          '<span><i class="fal fa-spinner fa-spin large"></i></span>';
      });

      uploader_thumbnail.bind("FilesAdded", function (up, files) {
        up.refresh();
        uploader_thumbnail.start();
      });
      uploader_thumbnail.bind("Error", function (up, err) {
        document.getElementById("felan_thumbnail_errors").innerHTML +=
          "Error #" + err.code + ": " + err.message + "<br/>";
      });

      var $image_id = $("#felan_thumbnail_view").data("image-id");
      var $image_url = $("#felan_thumbnail_view").data("image-url");
      if ($image_id && $image_url) {
        var $html =
          '<figure class="media-thumb media-thumb-wrap">' +
          '<img src="' +
          $image_url +
          '">' +
          '<div class="media-item-actions">' +
          '<a class="icon icon-thumbnail-delete" data-attachment-id="' +
          $image_id +
          '" href="#" ><i class="far fa-trash-alt large"></i></a>' +
          '<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
          "</div>" +
          "</figure>";
        $("#felan_thumbnail_view").html($html);
        $("#felan_add_thumbnail").hide();
      }
      uploader_thumbnail.bind(
        "FileUploaded",
        function (up, file, ajax_response) {
          document.getElementById("felan_drop_thumbnail").style.display =
            "none";
          var response = $.parseJSON(ajax_response.response);
          if (response.success) {
            $("input.thumbnail_url").val(response.full_image);
            $("input.thumbnail_id").val(response.attachment_id);
            var $html =
              '<figure class="media-thumb media-thumb-wrap">' +
              '<img src="' +
              response.full_image +
              '">' +
              '<div class="media-item-actions">' +
              '<a class="icon icon-thumbnail-delete" data-attachment-id="' +
              response.attachment_id +
              '" href="#" ><i class="far fa-trash-alt large"></i></a>' +
              '<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
              "</div>" +
              "</figure>";
            $("#felan_thumbnail_view").html($html);
            felan_thumbnai_delete();
            $("#felan_add_thumbnail .la-upload").hide();
            $("#thumbnail_url-error").hide();
            if ($(".form-dashboard").hasClass("freelancer-profile-form")) {
              $("#freelancer-profile-form").find(".point-mark").change();
            }
          }
        }
      );
    };
    felan_thumbnail();

    var felan_thumbnai_delete = function ($type) {
      $("body").on("click", ".icon-thumbnail-delete", function (e) {
        e.preventDefault();
        var $this = $(this),
          icon_delete = $this,
          thumbnail = $this
            .closest("#felan_thumbnail_view")
            .find(".media-thumb-wrap"),
          attachment_id = $this.data("attachment-id"),
          $drop = $("#felan_drop_thumbnail");

        icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');

        $.ajax({
          type: "post",
          url: ajax_url,
          dataType: "json",
          data: {
            action: "felan_thumbnail_remove_ajax",
            attachment_id: attachment_id,
            type: $type,
            removeNonce: thumbnail_upload_nonce,
          },
          success: function (response) {
            if (response.success) {
              thumbnail.remove();
              thumbnail.hide();

              $("#thumbnail_url-error").show();
              $("#felan_add_thumbnail").show();
            }
            icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');
            $drop.css("display", "block");
            $("#felan_add_thumbnail .la-upload").show();
            $("#felan_select_thumbnail").html(thumbnail_text);
            $("input.thumbnail_url").val("");
            $("input.thumbnail_id").val("");

            if ($(".form-dashboard").hasClass("freelancer-profile-form")) {
              $("#freelancer-profile-form").find(".point-mark").change();
            }
          },
          error: function () {
            icon_delete.html('<i class="far fa-trash-alt large"></i>');
          },
        });
      });
    };
    felan_thumbnai_delete();
  });
})(jQuery);
