(function ($) {
    "use strict";
    var ajax_url = felan_mess_image_vars.ajax_url,
        mess_image_title = felan_mess_image_vars.mess_image_title,
        mess_image_type = felan_mess_image_vars.mess_image_type,
        mess_image_file_size = felan_mess_image_vars.mess_image_file_size,
        // mess_image_id = felan_mess_image_vars.mess_image_id,
        mess_image_upload_nonce = felan_mess_image_vars.mess_image_upload_nonce;

	function initUploader() {
		var mess_image = $(".felan-fields-mess_image");
		$.each(mess_image, function () {


			const $wrapper = $(this);
			const mess_image_id = $wrapper.data("mess-image-id");

			if (!mess_image_id) {
				return;
			}

			const $button = $("#felan_select_mess_image_" + mess_image_id);

			// Prevent multiple re-initializations.
			if ($button.data("plupload-initialized")) {
				return;
			}

			var felan_mess_image = function () {
				var uploader_mess_image = new plupload.Uploader({
					browse_button: "felan_select_mess_image_" + mess_image_id,
					file_data_name: "felan_mess_image_upload_file_" + mess_image_id,
					drop_element: "felan_mess_image_view_" + mess_image_id,
					container: "felan_mess_image_container_" + mess_image_id,
					url:
					ajax_url +
					"?action=felan_mess_image_upload_ajax&nonce=" +
					mess_image_upload_nonce +
					"&mess_image_id=" +
					mess_image_id,
					filters: {
						mime_types: [
							{
								title: mess_image_title,
								extensions: mess_image_type,
							},
						],
						max_file_size: mess_image_file_size,
						prevent_duplicates: true,
					},
				});
				uploader_mess_image.init();

				$button.data("plupload-initialized", true);

				uploader_mess_image.bind("UploadProgress", function (up, file) {
					document.getElementById(
						"felan_select_mess_image_" + mess_image_id
					).innerHTML =
						'<span><i class="fal fa-spinner fa-spin large"></i></span>';

				});

				uploader_mess_image.bind("FilesAdded", function (up, files) {
					up.refresh();
					uploader_mess_image.start();
				});
				uploader_mess_image.bind("Error", function (up, err) {
					alert("Error #" + err.code + ": " + err.message);
				});

				var $image_id = $("#mess_image_id_" + mess_image_id).val();
				var $image_url = $("#mess_image_url_" + mess_image_id).val();
				if ($image_id && $image_url) {
					var $html =
						'<figure class="media-thumb media-thumb-wrap">' +
						'<img src="' +
						$image_url +
						'">' +
						'<div class="media-item-actions">' +
						'<a class="icon icon-mess_image-delete_' +
						mess_image_id +
						'" data-attachment-id="' +
						$image_id +
						'" href="#" ><i class="far fa-trash-alt large"></i></a>' +
						'<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
						"</div>" +
						"</figure>";
					$("#felan_mess_image_view_" + mess_image_id).html($html);
					$("#felan_add_mess_image_" + mess_image_id).hide();
				}

				uploader_mess_image.bind(
					"FileUploaded",
					function (up, file, ajax_response) {
						document.getElementById(
							"felan_drop_mess_image_" + mess_image_id
						).innerHTML = '<button type="button" class="tooltip" id="felan_select_mess_image_' + mess_image_id + '" data-title="Upload Image"><i class="far fa-images"></i></button>';
						var response = $.parseJSON(ajax_response.response);
						if (response.success) {
							$("#mess_image_url_" + mess_image_id).val(
								response.full_image
							);
							$("#mess_image_id_" + mess_image_id).val(
								response.attachment_id
							);
							var $html =
								'<figure class="media-thumb media-thumb-wrap">' +
								'<img src="' +
								response.full_image +
								'">' +
								'<div class="media-item-actions">' +
								'<a class="icon icon-mess_image-delete_' +
								mess_image_id +
								'" data-attachment-id="' +
								response.attachment_id +
								'" href="#" ><i class="far fa-trash-alt large"></i></a>' +
								'<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
								"</div>" +
								"</figure>";
							$("#felan_mess_image_view_" + mess_image_id).html($html);
							felan_thumbnai_delete();
							$("#mess_image_url-error_" + mess_image_id).hide();
						}
					}
				);
			};
			felan_mess_image();

			var felan_thumbnai_delete = function ($type) {
				$("body").on(
					"click",
					".icon-mess_image-delete_" + mess_image_id,
					function (e) {
						e.preventDefault();
						var $this = $(this),
							icon_delete = $this,
							mess_image = $this
								.closest("#felan_mess_image_view_" + mess_image_id)
								.find(".media-thumb-wrap"),
							attachment_id = $this.data("attachment-id"),
							$drop = $("#felan_drop_mess_image_" + mess_image_id);

						icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');

						$.ajax({
							type: "post",
							url: ajax_url,
							dataType: "json",
							data: {
								action: "felan_mess_image_remove_ajax",
								attachment_id: attachment_id,
								type: $type,
								removeNonce: mess_image_upload_nonce,
							},
							success: function (response) {
								if (response.success) {
									mess_image.remove();
									mess_image.hide();
									$("#mess_image_url-error_" + mess_image_id).show();
									$("#felan_add_mess_image_" + mess_image_id).show();
								}
								icon_delete.html(
									'<i class="fal fa-spinner fa-spin large"></i>'
								);
								$drop.css("display", "block");
								$("input#mess_image_url_" + mess_image_id).val("");
								$("input#mess_image_id_" + mess_image_id).val("");
							},
							error: function () {
								icon_delete.html('<i class="far fa-trash-alt large"></i>');
							},
						});
					}
				);
			};
			felan_thumbnai_delete();
		});
	}
    jQuery(document).ready(function () {
		initUploader();
    });
	document.addEventListener('felan:initUploader', function () {
		initUploader();
	})
})(jQuery);
