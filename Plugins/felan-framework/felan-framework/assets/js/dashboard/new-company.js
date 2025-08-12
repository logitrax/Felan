(function ($) {
    "use strict";
    var submit_company = $(".add-new-company");
    var ajax_url = felan_new_company_vars.ajax_url;

    $(document).ready(function () {
        $("body").on("click", ".btn-update-company", function (e) {
            e.preventDefault();
            var $this = $(this),
                post_type = $this.data('post-type'),
                company_title = submit_company.find('input[name="company_title"]').val(),
                company_email = submit_company.find('input[name="company_email"]').val(),
                company_avatar_url = submit_company.find('input[name="company_avatar_url"]').val(),
                company_avatar_id = submit_company.find('input[name="company_avatar_id"]').val();

            submit_company.find(".message_error").removeClass("true").text("");

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "felan_add_new_company",
                    post_type: post_type,
                    company_title: company_title,
                    company_email: company_email,
                    company_avatar_url: company_avatar_url,
                    company_avatar_id: company_avatar_id,
                },
                beforeSend: function () {
                    $this.find(".btn-loading").fadeIn();
                },
                success: function (data) {
                    $this.find(".btn-loading").fadeOut();
                    if (data.success == true) {
                        submit_company.find(".message_error").addClass("true");
                        submit_company.find(".message_error").text(data.message);
                        $(".select-all-company select[name='jobs_select_company']").html(data.company_options);
                        $(".select-company select[name='project_select_company']").html(data.company_options);
                    } else {
                        submit_company.find(".message_error").text(data.message);
                    }
                }
            });
        });
    });
})(jQuery);
