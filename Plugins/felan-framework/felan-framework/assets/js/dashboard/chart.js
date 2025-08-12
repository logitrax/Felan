(function ($) {
  "use strict";

  var ajax_url = felan_template_vars.ajax_url;

  $(document).ready(function () {
    //Jobs
    $('select[name="chart-date"]').change(function () {
      var jobs_id = $("#felan-dashboard_chart").data("jobs-id");
      var number_days = $(this).find("option:selected").val();
      $.ajax({
        type: "POST",
        url: ajax_url,
        dataType: "json",
        data: {
          action: "felan_chart_ajax",
          jobs_id: jobs_id,
          number_days: number_days,
        },
        beforeSend: function () {
          $(".felan-chart-warpper")
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
        },
        success: function (response) {
          var ctx = document
            .getElementById("felan-dashboard_chart")
            .getContext("2d");
          if (
            window.dashboardChart !== undefined &&
            window.dashboardChart !== null
          ) {
            window.dashboardChart.destroy();
          }
          window.dashboardChart = new Chart(ctx, {
            type: "line",
            data: {
              labels: response.labels,
              datasets: [
                {
                  label: response.label_view,
                  data: response.values_view,
                  backgroundColor: "rgb(0, 116, 86)",
                  borderColor: "rgb(0, 116, 86)",
                },
                {
                  label: response.label_apply,
                  data: response.values_apply,
                  backgroundColor: "rgb(237,0,6)",
                  borderColor: "rgb(237,0,6)",
                },
              ],
            },
            options: {
              tooltips: {
                enabled: true,
                mode: "x-axis",
                cornerRadius: 4,
              },
            },
          });
          $(".felan-chart-warpper")
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    });

    if ($("#felan-dashboard_chart").length) {
      var $this = $("#felan-dashboard_chart"),
        labels = $this.data("labels"),
        values_view = $this.data("values_view"),
        label_view = $this.data("label_view"),
        values_apply = $this.data("values_apply"),
        label_apply = $this.data("label_apply");

      var ctx = document
        .getElementById("felan-dashboard_chart")
        .getContext("2d");
      if (
        window.dashboardChart !== undefined &&
        window.dashboardChart !== null
      ) {
        window.dashboardChart.destroy();
      }
      window.dashboardChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: [
            {
              label: label_view,
              data: values_view,
              backgroundColor: "rgb(0, 116, 86)",
              borderColor: "rgb(0, 116, 86)",
            },
            {
              label: label_apply,
              data: values_apply,
              backgroundColor: "rgb(237,0,6)",
              borderColor: "rgb(237,0,6)",
            },
          ],
        },
        options: {
          tooltips: {
            enabled: true,
            mode: "x-axis",
            cornerRadius: 4,
          },
        },
      });
    }

    //Project
    $('select[name="project-chart-date"]').change(function () {
      var project_id = $("#felan-dashboard_project_chart").data("project-id");
      var number_days = $(this).find("option:selected").val();
      $.ajax({
        type: "POST",
        url: ajax_url,
        dataType: "json",
        data: {
          action: "felan_chart_project_ajax",
          project_id: project_id,
          number_days: number_days,
        },
        beforeSend: function () {
          $(".felan-chart-project-warpper")
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
        },
        success: function (response) {
          var ctx = document
            .getElementById("felan-dashboard_project_chart")
            .getContext("2d");
          if (
            window.dashboardChart !== undefined &&
            window.dashboardChart !== null
          ) {
            window.dashboardChart.destroy();
          }
          window.dashboardChart = new Chart(ctx, {
            type: "line",
            data: {
              labels: response.labels,
              datasets: [
                {
                  label: response.label_view,
                  data: response.values_view,
                  backgroundColor: "rgb(0, 116, 86)",
                  borderColor: "rgb(0, 116, 86)",
                },
                {
                  label: response.label_apply,
                  data: response.values_apply,
                  backgroundColor: "rgb(237,0,6)",
                  borderColor: "rgb(237,0,6)",
                },
              ],
            },
            options: {
              tooltips: {
                enabled: true,
                mode: "x-axis",
                cornerRadius: 4,
              },
            },
          });
          $(".felan-chart-project-warpper")
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    });

    if ($("#felan-dashboard_project_chart").length) {
      var $this = $("#felan-dashboard_project_chart"),
        labels = $this.data("labels"),
        values_view = $this.data("values_view"),
        label_view = $this.data("label_view"),
        values_apply = $this.data("values_apply"),
        label_apply = $this.data("label_apply");

      var ctx = document
        .getElementById("felan-dashboard_project_chart")
        .getContext("2d");
      if (
        window.dashboardChart !== undefined &&
        window.dashboardChart !== null
      ) {
        window.dashboardChart.destroy();
      }
      window.dashboardChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: [
            {
              label: label_view,
              data: values_view,
              backgroundColor: "rgb(0, 116, 86)",
              borderColor: "rgb(0, 116, 86)",
            },
            {
              label: label_apply,
              data: values_apply,
              backgroundColor: "rgb(237,0,6)",
              borderColor: "rgb(237,0,6)",
            },
          ],
        },
        options: {
          tooltips: {
            enabled: true,
            mode: "x-axis",
            cornerRadius: 4,
          },
        },
      });
    }

    //Employer
    $('select[name="chart_employer"]').change(function () {
      var number_days = $(this).find("option:selected").val();
      $.ajax({
        type: "POST",
        url: ajax_url,
        dataType: "json",
        data: {
          action: "felan_chart_employer_ajax",
          number_days: number_days,
        },
        beforeSend: function () {
          $(".felan-chart-employer")
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
        },
        success: function (response) {
          var ctx = document
            .getElementById("felan-dashboard_employer")
            .getContext("2d");
          if (
            window.dashboardChart !== undefined &&
            window.dashboardChart !== null
          ) {
            window.dashboardChart.destroy();
          }
          var chartEl = document.getElementById("felan-dashboard_employer");
          chartEl.height = 265;
          window.dashboardChart = new Chart(ctx, {
            type: "line",
            data: {
              labels: response.labels_view,
              datasets: [
                {
                  label: response.label_view,
                  data: response.values_view,
                  backgroundColor: "rgb(10, 101, 252)",
                  borderColor: "rgb(10, 101, 252)",
                },
              ],
            },
            options: {
              tooltips: {
                enabled: true,
                mode: "x-axis",
                cornerRadius: 4,
              },
              plugins: {
                legend: {
                  display: false,
                },
              },
            },
          });
          $(".felan-chart-employer")
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    });

    if ($("#felan-dashboard_employer").length) {
      var $this = $("#felan-dashboard_employer"),
        labels = $this.data("labels"),
        values = $this.data("values"),
        label = $this.data("label");

      var ctx = document
        .getElementById("felan-dashboard_employer")
        .getContext("2d");
      if (
        window.dashboardChart !== undefined &&
        window.dashboardChart !== null
      ) {
        window.dashboardChart.destroy();
      }
      var chartEl = document.getElementById("felan-dashboard_employer");
      chartEl.height = 265;
      window.dashboardChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: [
            {
              label: label,
              data: values,
              backgroundColor: "rgb(10, 101, 252)",
              borderColor: "rgb(10, 101, 252)",
            },
          ],
        },
        options: {
          tooltips: {
            enabled: true,
            mode: "x-axis",
            cornerRadius: 4,
          },
          plugins: {
            legend: {
              display: false,
            },
          },
        },
      });
    }

    //Freelancer
    $('select[name="chart_freelancer"]').change(function () {
      var number_days = $(this).find("option:selected").val();
      $.ajax({
        type: "POST",
        url: ajax_url,
        dataType: "json",
        data: {
          action: "felan_chart_freelancer_ajax",
          number_days: number_days,
        },
        beforeSend: function () {
          $(".felan-chart-freelancer")
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
        },
        success: function (response) {
          var ctx = document
            .getElementById("felan-dashboard_freelancer")
            .getContext("2d");
          if (
            window.dashboardChart !== undefined &&
            window.dashboardChart !== null
          ) {
            window.dashboardChart.destroy();
          }
          var chartEl = document.getElementById("felan-dashboard_freelancer");
          chartEl.height = 280;
          window.dashboardChart = new Chart(ctx, {
            type: "line",
            data: {
              labels: response.labels_view,
              datasets: [
                {
                  label: response.label_view,
                  data: response.values_view,
                  backgroundColor: "rgb(10, 101, 252)",
                  borderColor: "rgb(10, 101, 252)",
                },
              ],
            },
            options: {
              tooltips: {
                enabled: true,
                mode: "x-axis",
                cornerRadius: 4,
              },
              plugins: {
                legend: {
                  display: false,
                },
              },
            },
          });
          $(".felan-chart-freelancer")
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    });
    if ($("#felan-dashboard_freelancer").length) {
      var $this = $("#felan-dashboard_freelancer"),
        labels = $this.data("labels"),
        values = $this.data("values"),
        label = $this.data("label");

      var ctx = document
        .getElementById("felan-dashboard_freelancer")
        .getContext("2d");
      if (
        window.dashboardChart !== undefined &&
        window.dashboardChart !== null
      ) {
        window.dashboardChart.destroy();
      }
      var chartEl = document.getElementById("felan-dashboard_freelancer");
      chartEl.height = 280;
      window.dashboardChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: [
            {
              label: label,
              data: values,
              backgroundColor: "rgb(10, 101, 252)",
              borderColor: "rgb(10, 101, 252)",
            },
          ],
        },
        options: {
          tooltips: {
            enabled: true,
            mode: "x-axis",
            cornerRadius: 4,
          },
          plugins: {
            legend: {
              display: false,
            },
          },
        },
      });
    }
  });
})(jQuery);
