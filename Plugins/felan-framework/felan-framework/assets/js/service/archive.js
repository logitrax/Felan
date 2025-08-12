var SERVICE = SERVICE || {};
(function ($) {
  "use strict";

  var ajax_url = felan_template_vars.ajax_url,
    map_effects = felan_template_vars.map_effects,
    map_api_key = felan_template_vars.map_api_key,
    item_amount = felan_service_archive_vars.item_amount,
    range_min = 1,
    range_max = 1000,
    not_service = felan_service_archive_vars.not_service;

  var ajax_call = false;

  var menu_filter_wrap = $(".felan-menu-filter");
  var archive_service = $(".archive-service");
  var felan_map;
  var markers = [];
  //map
  var mapType = $(".maptype").data("maptype");
  var is_mobile = false;
  var has_map = "";

  if (mapType == "google_map") {
    var service_maps_filter = $("#jobs-map-filter");
  } else if (mapType == "openstreetmap") {
    var service_maps_filter = $("#maps");
  } else {
    var service_maps_filter = $("#map");
  }

  if (service_maps_filter.length) {
    has_map = "yes";
  }

  if (
    /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
      navigator.userAgent
    )
  ) {
    is_mobile = true;
  }

  var felan_hover_map_effects = function () {
    if (map_effects !== "" && has_map) {
      $(".map-event .area-service .felan-service-item").each(function () {
        var title = $(this).find(".btn-add-to-wishlist").data("service-id");

        if (mapType == "google_map") {
          $(this).on("mouseenter", function () {
            if (map_effects == "popup") {
              $('div[title="marker' + title + '"]')
                .trigger("click")
                .css("z-index", "2");
            } else if (map_effects == "shine") {
              $('div[title="marker' + title + '"]')
                .trigger("click")
                .addClass("mouseenter");
            }
          });

          $(this).on("mouseleave", function () {
            if (map_effects == "popup") {
              $('div[title="marker' + title + '"]').css("z-index", "0");
              infowindow.open(null, null);
            } else if (map_effects == "shine") {
              $('div[title="marker' + title + '"]')
                .trigger("click")
                .removeClass("mouseenter");
            }
          });
        } else if (mapType == "openstreetmap") {
          $(this).on("mouseenter", function () {
            if (map_effects == "popup") {
              $(".marker-" + title)
                .trigger("click")
                .css("z-index", "2");
            } else if (map_effects == "shine") {
              $(".marker-" + title)
                .trigger("click")
                .addClass("mouseenter");
            }
          });

          $(this).on("mouseleave", function () {
            if (map_effects == "popup") {
              $(".marker-" + title).css("z-index", "0");
              $(".leaflet-popup-close-button").trigger("click");
            } else if (map_effects == "shine") {
              $(".marker-" + title)
                .trigger("click")
                .removeClass("mouseenter");
            }
          });
        } else {
          $(this).on("mouseenter", function () {
            if (map_effects == "popup") {
              $("#marker-" + title)
                .trigger("click")
                .css("z-index", "2");
            } else if (map_effects == "shine") {
              $("#marker-" + title)
                .trigger("click")
                .addClass("mouseenter");
            }
          });

          $(this).on("mouseleave", function () {
            if (map_effects == "popup") {
              $(".marker-" + title).css("z-index", "0");
              $(".mapboxgl-popup-close-button").trigger("click");
            } else if (map_effects == "shine") {
              $("#marker-" + title)
                .trigger("click")
                .removeClass("mouseenter");
            }
          });
        }
      });
    }
  };

  SERVICE.elements = {
    init: function () {
      this.service_layout();
      this.pagination();
      this.display_clear();
      this.search_cate_location();
      this.slider_range();
      this.filter_clear_top();
      this.filter_clear();

      archive_service
        .find(".felan-menu-filter")
        .on("input", "input.input-control", function () {
          $(".felan-pagination").find('input[name="paged"]').val(1);
          $(".form-service-top-filter .btn-top-filter").removeData("clicked");
          ajax_call = true;
          SERVICE.elements.ajax_load(ajax_call);
        });

      archive_service.find("select.sort-by").on("change", function () {
        $(".felan-pagination").find('input[name="paged"]').val(1);
        $(".form-service-top-filter .btn-top-filter").removeData("clicked");
        ajax_call = true;
        SERVICE.elements.ajax_load(ajax_call);
      });

      function delay(callback, ms) {
        var timer = 0;
        return function () {
          var context = this,
            args = arguments;
          clearTimeout(timer);
          timer = setTimeout(function () {
            callback.apply(context, args);
          }, ms || 0);
        };
      }

      var menu_filter = $(".felan-menu-filter");
      menu_filter.find('input[name="service_filter_price_min"]').keyup(
        delay(function () {
          $(".felan-pagination").find('input[name="paged"]').val(1);
          $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");
          ajax_call = true;
          SERVICE.elements.ajax_load(ajax_call);
        }, 1000)
      );

      menu_filter.find('input[name="service_filter_price_max"]').keyup(
        delay(function () {
          $(".felan-pagination").find('input[name="paged"]').val(1);
          $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");
          ajax_call = true;
          SERVICE.elements.ajax_load(ajax_call);
        }, 1000)
      );

      menu_filter
        .find('select[name="service_time_type"]')
        .on("change", function () {
          $(".felan-pagination").find('input[name="paged"]').val(1);
          $(".form-jobs-top-filter .btn-top-filter").removeData("clicked");
          ajax_call = true;
          SERVICE.elements.ajax_load(ajax_call);
        });

      archive_service
        .find(".form-service-top-filter .btn-top-filter")
        .on("click", function (e) {
          e.preventDefault();
          $(".felan-pagination").find('input[name="paged"]').val(1);
          $(this).data("clicked", true);
          ajax_call = true;
          SERVICE.elements.ajax_load(ajax_call);
        });

      if (service_maps_filter.length > 0) {
        SERVICE.elements.ajax_load();
      }

      $('.btn-hide-map input[type="checkbox"]').on("change", function () {
        var elem = $(".archive-layout .inner-content");
        var ltf = $(".layout-top-filter .nav-bar");
        if ($(this).attr("checked")) {
          $("input[value='hide_map']").prop("checked", false);
        } else {
          $("input[value='hide_map']").prop("checked", true);
        }
        if (elem.hasClass("has-map")) {
          elem.removeClass("has-map");
          elem.addClass("no-map");
          ltf.removeClass("has-map");
          ltf.addClass("no-map");
        } else {
          elem.removeClass("no-map");
          elem.addClass("has-map");
          ltf.removeClass("no-map");
          ltf.addClass("has-map");
        }
        ajax_call = true;
        SERVICE.elements.ajax_load(ajax_call);
      });

      $(".locations-filter select").on("change", function () {
        ajax_call = true;
        SERVICE.elements.ajax_load(ajax_call);
      });
    },

      slider_range: function () {
          var archive_service = $(".archive-service");
          var min = parseInt(range_min);
          var max = parseInt(range_max);
          var timers = {};

          function delayShowData(type, values) {
              clearTimeout(timers[type]);
              timers[type] = setTimeout(function () {
                  $(".felan-pagination").find('input[name="paged"]').val(1);
                  $(this).data("clicked", true);
                  ajax_call = true;
                  SERVICE.elements.ajax_load(ajax_call);
              }, 500);
          }

          $("#slider-range").slider({
              range: true,
              min: min,
              max: max,
              step: 1,
              values: [min, max],
              slide: function (event, ui) {
                  $("#amount").val(ui.values[0] + " - " + ui.values[1]);
              },
              change: function (event, ui) {
                  var values_start = ui.values[0];
                  var values_end = ui.values[1];
                  if (values_start !== min || values_end !== max) {
                      archive_service.addClass("filter-active");
                  } else {
                      archive_service.removeClass("filter-active");
                  }
              },
              stop: function () {
                  delayShowData();
              },
          });
          $("#amount").val(
              $("#slider-range").slider("values", 0) +
              " - " +
              $("#slider-range").slider("values", 1)
          );
      },

    pagination: function () {
      $("body").on(
        "click",
        ".felan-pagination.ajax-call a.page-numbers",
        function (e) {
          e.preventDefault();
          archive_service
            .find(".felan-pagination .pagination")
            .addClass("active");
          archive_service
            .find(".felan-pagination li .page-numbers")
            .removeClass("current");
          $(this).addClass("current");
          var paged = $(this).text();
          var current_page = 1;
          if ($(".felan-pagination").find('input[name="paged"]').val()) {
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
          archive_service
            .find(".felan-pagination")
            .find('input[name="paged"]')
            .val(paged);
          ajax_call = true;
          if ($(this).attr("data-type") == "number") {
            SERVICE.elements.scroll_to(".area-service");
            SERVICE.elements.ajax_load(ajax_call);
          } else {
            SERVICE.elements.ajax_load(ajax_call, "loadmore");
          }
        }
      );
    },

    removeClassStartingWith: function (node, begin) {
      node.removeClass(function (index, className) {
        return (
          className.match(new RegExp("\\b" + begin + "\\S+", "g")) || []
        ).join(" ");
      });
    },

    service_layout: function () {
      archive_service.find(".service-layout a").on("click", function (event) {
        event.preventDefault();
        var layout = $(this).attr("data-layout");
        var type_pagination = $(".felan-pagination").attr("data-type");
        if (type_pagination == "loadmore") {
          $(".felan-pagination").find('input[name="paged"]').val(1);
        }
        $(this).closest(".service-layout").find(">a").removeClass("active");
        $(this).addClass("active");
        SERVICE.elements.removeClassStartingWith(
          $(".archive-layout>.inner-content"),
          "layout-"
        );
        $(this).closest(".inner-content").addClass(layout);

        $(".form-service-top-filter .btn-top-filter").removeData("clicked");

        $(".area-service .felan-service-item").each(function () {
          SERVICE.elements.removeClassStartingWith($(this), "layout-");
          $(this).addClass(layout);
        });

        ajax_call = true;
        SERVICE.elements.ajax_load(ajax_call);
      });
    },

    search_cate_location: function () {
      var form = $(".archive-layout .felan-form-location"),
        input = form.find(".archive-search-location"),
        field_select = form.find(".felan-select2");

      $("body").on(
        "mousedown",
        ".felan-form-location .icon-arrow i",
        function (e) {
          e.preventDefault();
          var select2_container = form.find(".select2.select2-container");
          if (select2_container.hasClass("select2-container--open")) {
            field_select.select2("close");
          } else {
            field_select.select2("open");
          }
          field_select.on("select2:select", function (e) {
            var data = e.params.data;
            input.val(data.text);
          });
        }
      );

      //Geo Location
      var locationBtn = form.find(".icon-location svg");

      locationBtn.on("click", () => {
        // Check if geolocation is supported by the browser
        if ("geolocation" in navigator) {
          // Use the geolocation API to get the user's current position
          navigator.geolocation.getCurrentPosition((position) => {
            // Get the latitude and longitude from the position object
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            if (mapType == "google_map") {
              var url = "";
            } else if (mapType == "openstreetmap") {
              var url =
                "https://nominatim.openstreetmap.org/reverse?lat=" +
                latitude +
                "&lon=" +
                longitude +
                "&format=jsonv2";
            } else {
              var url =
                "https://api.mapbox.com/geocoding/v5/mapbox.places/" +
                longitude +
                "," +
                latitude +
                ".json?access_token=" +
                map_api_key +
                "";
            }

            $.ajax({
              url: url,
              type: "GET",
              success: (result) => {
                // Set the value of the location input field to the address
                input.val(result.features[0].context[2].text);
              },
              error: (error) => {
                console.log(`Error: ${error}`);
              },
            });
          });
        } else {
          // Geolocation is not supported by the browser
          console.log("Geolocation is not supported by your browser");
        }
      });
    },

    display_clear: function () {
      var archive_service = $(".archive-service");
      if ($(".felan-menu-filter ul.filter-control li.active").length > 0) {
        $(".felan-nav-filter").addClass("active");
        archive_service.find(".felan-clear-filter").show();
      } else {
        $(".felan-nav-filter").removeClass("active");
        archive_service.find(".felan-clear-filter").hide();
      }

      $('.felan-menu-filter input[type="checkbox"]:checked').each(function () {
        if ($(this).length > 0) {
          $(".felan-nav-filter").addClass("active");
          $(this).closest(".entry-filter").addClass("open");
          archive_service.find(".felan-clear-filter").show();
        } else {
          $(".felan-nav-filter").removeClass("active");
          $(this).closest(".entry-filter").removeClass("open");
          archive_service.find(".felan-clear-filter").hide();
        }
      });
    },

    filter_clear_top: function () {
      archive_service.find(".felan-clear-top-filter").on("click", function () {
        $('.form-service-top-filter input[name="service_filter_search').val("");
        $('.form-service-top-filter input[name="service-search-location"]').val(
          ""
        );
        $(".form-service-top-filter .felan-select2").val("");
        $(".form-service-top-filter .felan-select2").select2("destroy");
		$('input[name="current_term"]').val("");
		$('input[name="type_term"]').val("");
        $(".form-service-top-filter .felan-select2").each(function () {
          var option = $(this).find("option");
          if (theme_vars.enable_search_box_dropdown == 1) {
            if (option.length > theme_vars.limit_search_box) {
              $(this).select2();
            } else {
              $(this).select2({
                minimumResultsForSearch: -1,
              });
            }
          } else {
            $(this).select2({
              minimumResultsForSearch: -1,
            });
          }
        });
        ajax_call = true;
        SERVICE.elements.ajax_load(ajax_call);
      });
    },

    filter_clear: function () {
      archive_service.find(".felan-clear-filter").on("click", function () {
        $(".felan-menu-filter ul.filter-control li").removeClass("active");
        $('.felan-menu-filter input[type="checkbox"]').prop("checked", false);
        $('.felan-menu-filter input[name="service_filter_price_min"]').val("");
        $('.felan-menu-filter input[name="service_filter_price_max"]').val("");
        $('.felan-menu-filter select[name="service_time_type"]')
          .val(null)
          .trigger("change");
        $(".felan-menu-filter .felan-select2").val("");
        $(".felan-menu-filter .felan-select2").select2("destroy");
        $(".felan-menu-filter .felan-select2").select2();
		$('input[name="service-search-location"]').val("");
		$('input[name="current_term"]').val("");
		$('input[name="type_term"]').val("");
		$('select[name="service-categories"]').val("");
		$('select[name="service-categories"]').select2("destroy");
		$('select[name="service-categories"]').select2();

      $("#slider-range").slider("values", 0, parseInt(range_min));
      $("#slider-range").slider("values", 1, parseInt(range_max));
      $("#amount").val(parseInt(range_min) + " - " + parseInt(range_max));

        ajax_call = true;
        SERVICE.elements.ajax_load(ajax_call);
      });
    },

    ajax_load: function (ajax_call, pagination) {
      var title,
        sort_by,
        categories,
          range_min,
          range_max,
          has_map_val,
          location,
        skills,
        language,
        current_term,
        type_term,
        price_min,
        price_max,
        time_type,
        rating,
        search_fields_sidebar,
        location_country,
        location_state,
        location_city,
        radius_cities,
        service_layout;
      var paged = 1;

      paged = archive_service
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val();
      title = archive_service.find('input[name="service_filter_search"]').val();
      current_term = $('input[name="current_term"]').val();
        has_map_val = $('input[name="has_map"]').val();
        type_term = $('input[name="type_term"]').val();

      service_layout = archive_service
        .find(".service-layout a.active")
        .attr("data-layout");

      sort_by = menu_filter_wrap
        .find(".sort-by.filter-control li.active a")
        .data("sort");

      var select_sort = $('.archive-layout select[name="sort_by"]').val();
      if (select_sort) {
        sort_by = select_sort;
      }

      search_fields_sidebar = $('input[name="search_fields_sidebar"]').val();
      var result_fields = $.parseJSON(search_fields_sidebar);

      location = $('input[name="service-search-location"]').val();
      location_country = $("select.felan-select-country").val();
      location_state = $("select.felan-select-state").val();
      location_city = $("select.felan-select-city").val();
      radius_cities = $(".felan-form-location")
        .find('input[name="service_number_radius"]')
        .val();

      if (result_fields.hasOwnProperty("service-categories")) {
        categories = $('input[name="service-categories_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        categories = $('select[name="service-categories"]').val();
      }

        if (result_fields.hasOwnProperty("service-price")) {
            range_min = archive_service.find("#slider-range").slider("values", 0);
            range_max = archive_service.find("#slider-range").slider("values", 1);
        }

      if (result_fields.hasOwnProperty("service-skills")) {
        skills = $('input[name="service-skills_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        skills = $('select[name="service-skills"]').val();
      }

      if (result_fields.hasOwnProperty("service-language")) {
        language = $('input[name="service-language_id[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        language = $('select[name="service-language"]').val();
      }

      price_min = menu_filter_wrap
        .find('input[name="service_filter_price_min"]')
        .val();
      price_max = menu_filter_wrap
        .find('input[name="service_filter_price_max"]')
        .val();
      time_type = menu_filter_wrap
        .find('select[name="service_time_type"]')
        .val();

      if (result_fields.hasOwnProperty("service-rating")) {
        rating = $('input[name="service_rating[]"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
      } else {
        rating = $('select[name="service-rating"]').val();
      }

		var search_custom_fields_sidebar = $('input[name="search_custom_fields_sidebar"]').val();
		var custom_fields = $.parseJSON(search_custom_fields_sidebar);

		let custom_fields_value = [];
		Object.entries(custom_fields).forEach(([key, value]) => {
			var ctf = $('input[name="' + value + '_id[]"]:checked')
			.map(function () {
				return $(this).val();
			})
			.get();
			custom_fields_value.push( {
				[value]: ctf
			} );
		});

      //Map
      var map_html = $(".maptype").clone();
      if (mapType == "google_map") {
        var marker_cluster = null,
          googlemap_default_zoom = felan_template_vars.googlemap_default_zoom,
          not_found = felan_template_vars.not_found,
          clusterIcon = felan_template_vars.clusterIcon,
          google_map_style = felan_template_vars.google_map_style,
          google_map_type = felan_template_vars.google_map_type,
          pin_cluster_enable = felan_template_vars.pin_cluster_enable;

        var infowindow = new google.maps.InfoWindow({
          maxWidth: 370,
        });

        var silver = [
          {
            featureType: "landscape",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "transit",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "poi",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "water",
            elementType: "labels",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            featureType: "road",
            elementType: "labels.icon",
            stylers: [
              {
                visibility: "off",
              },
            ],
          },
          {
            stylers: [
              {
                hue: "#00aaff",
              },
              {
                saturation: -100,
              },
              {
                gamma: 2.15,
              },
              {
                lightness: 12,
              },
            ],
          },
          {
            featureType: "road",
            elementType: "labels.text.fill",
            stylers: [
              {
                visibility: "on",
              },
              {
                lightness: 24,
              },
            ],
          },
          {
            featureType: "road",
            elementType: "geometry",
            stylers: [
              {
                lightness: 57,
              },
            ],
          },
        ];

        if (has_map) {
          var felan_search_map_option = {
            scrollwheel: true,
            scroll: { x: $(window).scrollLeft(), y: $(window).scrollTop() },
            zoom: parseInt(googlemap_default_zoom),
            mapTypeId: google_map_type,
            draggable: true,
            fullscreenControl: true,
            styles: silver,
            mapTypeControl: false,
            zoomControlOptions: {
              position: google.maps.ControlPosition.RIGHT_BOTTOM,
            },
            fullscreenControlOptions: {
              position: google.maps.ControlPosition.RIGHT_BOTTOM,
            },
          };
        }

        var felan_add_markers = function (props, map) {
          $.each(props, function (i, prop) {
            var latlng = new google.maps.LatLng(prop.lat, prop.lng),
              marker_url = prop.marker_icon,
              marker_size = new google.maps.Size(60, 60);
            var marker_icon = {
              url: marker_url,
              size: marker_size,
              scaledSize: new google.maps.Size(40, 40),
              origin: new google.maps.Point(-10, -10),
              anchor: new google.maps.Point(7, 27),
            };

            var marker = new google.maps.Marker({
              position: latlng,
              url: ".service-" + prop.id,
              map: map,
              service: prop.service,
              icon: marker_icon,
              draggable: false,
              title: "marker" + prop.id,
              animation: google.maps.Animation.DROP,
            });

            var prop_title = prop.data ? prop.data.post_title : prop.title;

            var contentString = document.createElement("div");
            contentString.className = "felan-marker";
            contentString.innerHTML = prop.service;

            var click_marker = false;

            marker.addListener("mouseover", function () {
              click_marker = true;
            });

            marker.addListener("mouseout", function () {
              click_marker = false;
            });

            google.maps.event.addListener(marker, "click", function () {

				if ( map_effects == "popup" ) {
					infowindow.close();
					infowindow.setContent(contentString);
					infowindow.open(map, marker);
				}

              var scale = Math.pow(2, map.getZoom()),
                offsety = 30 / scale || 0,
                projection = map.getProjection(),
                markerPosition = marker.getPosition(),
                markerScreenPosition =
                  projection.fromLatLngToPoint(markerPosition),
                pointHalfScreenAbove = new google.maps.Point(
                  markerScreenPosition.x,
                  markerScreenPosition.y - offsety
                ),
                aboveMarkerLatLng =
                  projection.fromPointToLatLng(pointHalfScreenAbove);
              map.panTo(aboveMarkerLatLng);

              var elem = $(marker.url);
              $(".area-service .felan-service-item").removeClass("highlight");
              if (
                elem.length > 0 &&
                click_marker &&
                $(".archive-service.map-event").length > 0
              ) {
                elem.addClass("highlight");
                $("html, body").animate(
                  {
                    scrollTop: elem.offset().top - 50,
                  },
                  500
                );
              }
            });

            markers.push(marker);
          });
        };

        var felan_my_location = function (map) {
          var my_location = {};
          var my_lat = "";
          var my_lng = "";

          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              function (position) {
                var pos = {
                  lat: position.coords.latitude,
                  lng: position.coords.longitude,
                };

                my_lat = position.coords.latitude;
                my_lng = position.coords.longitude;

                my_location = {
                  lat: parseFloat(my_lat),
                  lng: parseFloat(my_lng),
                };
              },
              function () {
                handleLocationError(true, infowindow, map.getCenter());
              }
            );
          } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infowindow, map.getCenter());
          }

          function CenterControl(controlDiv, map) {
            // Set CSS for the control border.
            const controlUI = document.createElement("div");
            controlUI.style.backgroundColor = "#fff";
            controlUI.style.border = "2px solid #fff";
            controlUI.style.borderRadius = "3px";
            controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
            controlUI.style.cursor = "pointer";
            controlUI.style.width = "40px";
            controlUI.style.height = "40px";
            controlUI.style.margin = "10px";
            controlUI.style.textAlign = "center";
            controlUI.title = "My location";
            controlDiv.appendChild(controlUI);

            // Set CSS for the control interior.
            const controlText = document.createElement("div");
            controlText.style.fontSize = "18px";
            controlText.style.lineHeight = "37px";
            controlText.style.paddingLeft = "5px";
            controlText.style.paddingRight = "5px";
            controlText.innerHTML = "<i class='fas fa-location'></i>";
            controlUI.appendChild(controlText);

            var marker_icon = {
              url: default_icon,
              scaledSize: new google.maps.Size(40, 40),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(7, 27),
            };

            // Setup the click event listeners: simply set the map to Chicago.
            controlUI.addEventListener("click", () => {
              var current_location = new google.maps.Marker({
                position: my_location,
                map,
                icon: marker_icon,
              });

              infowindow.setPosition(my_location);
              infowindow.setContent(
                '<div class="default-result">Your location.</div>'
              );
              //infowindow.open(map);
              map.panTo(my_location);
            });
          }

          const centerControlDiv = document.createElement("div");
          CenterControl(centerControlDiv, map);

          centerControlDiv.index = 1;
          map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(
            centerControlDiv
          );

          function handleLocationError(browserHasGeolocation, infowindow, pos) {
            infowindow.setPosition(pos);
            infowindow.setContent(
              browserHasGeolocation
                ? "Error: The Geolocation service failed."
                : "Error: Your browser doesn't support geolocation."
            );
            infowindow.open(map);
          }
        };

        if (!is_mobile) {
          felan_hover_map_effects();
        }
      } else if (mapType == "openstreetmap") {
        var felan_osm_add_markers = function (props, maps) {
          $(".maptype").remove();
          $(map_html).insertAfter("#pac-input");

          var osm_api = $("#maps").data("key");
          var osm_level = $("#maps").data("level");
          var osm_style = $("#maps").data("style");

          var features_info = [];
          var lng_args = [];
          var lat_args = [];

          $.each(props, function (i, prop) {
            features_info.push({
              type: "Feature",
              geometry: {
                type: "Point",
                coordinates: [prop.lat, prop.lng],
              },
              properties: {
                iconSize: [40, 40],
                id: prop.id,
                icon: prop.marker_icon,
                service: prop.service,
              },
            });

            lng_args.push(prop.lng);
            lat_args.push(prop.lat);
          });

          var stores = {
            type: "FeatureCollection",
            features: features_info,
          };

          var sum_lng = 0;
          for (var i = 0; i < lng_args.length; i++) {
            sum_lng += parseInt(lng_args[i], 10);
          }

          var avg_lng = 0;

          if (sum_lng / lng_args.length) {
            avg_lng = sum_lng / lng_args.length;
          }

          var sum_lat = 0;
          for (var i = 0; i < lat_args.length; i++) {
            sum_lat += parseInt(lat_args[i], 10);
          }

          var avg_lat = 0;

          if (sum_lat / lat_args.length) {
            avg_lat = sum_lat / lat_args.length;
          }

          var container = L.DomUtil.get("maps");
          if (container != null) {
            container._leaflet_id = null;
          }

          $(".leaflet-map-pane").remove();
          $(".leaflet-control-container").remove();

          var osm_map = new L.map("maps");

          osm_map.on("load", onMapLoad);

          osm_map.setView([avg_lat, avg_lng], osm_level);

          function onMapLoad() {
            var titleLayer_id = "mapbox/" + osm_style;

            L.tileLayer(
              "https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=" +
                osm_api,
              {
                attribution:
                  'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                id: titleLayer_id,
                tileSize: 512,
                zoomOffset: -1,
                accessToken: osm_api,
              }
            ).addTo(osm_map);

            /**
             * Add all the things to the page:
             * - The location listings on the side of the page
             * - The markers onto the map
             */
            addMarkers();
          }

          function flyToStore(currentFeature) {
            osm_map.flyTo(currentFeature.geometry.coordinates, osm_level);
          }

          /* This will let you use the .remove() function later on */
          if (!("remove" in Element.prototype)) {
            Element.prototype.remove = function () {
              if (this.parentNode) {
                this.parentNode.removeChild(this);
              }
            };
          }

          function addMarkers() {
            /* For each feature in the GeoJSON object above: */
            stores.features.forEach(function (marker) {
              /* Create a div element for the marker. */
              var el = document.createElement("div");
              /* Assign a unique `id` to the marker. */
              el.id = "marker-" + marker.properties.id;
              /* Assign the `marker` class to each marker for styling. */
              el.className = "marker";
              el.style.backgroundImage = "url(" + marker.properties.icon + ")";
              el.style.width = marker.properties.iconSize[0] + "px";
              el.style.height = marker.properties.iconSize[1] + "px";
              /**
               * Create a marker using the div element
               * defined above and add it to the map.
               **/
              properties: {
              }

              var icon = L.divIcon({
                className: "marker-" + marker.properties.id,
                html:
                  '<div><img src="' +
                  marker.properties.icon +
                  '" alt=""></div>',
                iconSize: [48, 48],
              });

              var markers = new L.marker(
                [
                  marker.geometry.coordinates[0],
                  marker.geometry.coordinates[1],
                ],
                { icon: icon }
              );

              markers.addTo(osm_map);

              if (map_effects == "popup") {
                markers.bindPopup(marker.properties.service);
              } else {
                markers.bindPopup();
              }

              el.addEventListener("click", function (e) {
                /* Fly to the point */
                flyToStore(marker);
                /* Highlight listing in sidebar */
                var activeItem = document.getElementsByClassName("active");
                e.stopPropagation();
                if (activeItem[0]) {
                  activeItem[0].classList.remove("active");
                }
              });
            });
          }

          if (!is_mobile) {
            felan_hover_map_effects();
          }
        };

        // End Openstreetmap
      } else {
        // Begin Mapbox
        var felan_mapbox_add_markers = function (props, map) {
          var mapbox_api = $("#map").data("key");
          var mapbox_level = $("#map").data("level");
          var mapType = $("#map").data("type");
          mapboxgl.accessToken = mapbox_api;
          $(".mapboxgl-canary").remove();
          $(".mapboxgl-canvas-container").remove();
          $(".mapboxgl-control-container").remove();
          var features_info = [];
          var lng_args = [];
          var lat_args = [];

          $.each(props, function (i, prop) {
            features_info.push({
              type: "Feature",
              geometry: {
                type: "Point",
                coordinates: [prop.lng, prop.lat],
              },
              properties: {
                iconSize: [48, 48],
                id: prop.id,
                icon: prop.marker_icon,
                service: prop.service,
              },
            });

            lng_args.push(prop.lng);
            lat_args.push(prop.lat);
          });

          var sum_lng = 0;
          for (var i = 0; i < lng_args.length; i++) {
            sum_lng += parseInt(lng_args[i], 10);
          }

          var avg_lng = 0;

          if (sum_lng / lng_args.length) {
            avg_lng = sum_lng / lng_args.length;
          }

          var sum_lat = 0;
          for (var i = 0; i < lat_args.length; i++) {
            sum_lat += parseInt(lat_args[i], 10);
          }

          var avg_lat = 0;

          if (sum_lat / lat_args.length) {
            avg_lat = sum_lat / lat_args.length;
          }

          var map = new mapboxgl.Map({
            container: "map",
            style: "mapbox://styles/mapbox/" + mapType,
            zoom: mapbox_level,
            center: [avg_lng, avg_lat],
          });

            map.scrollZoom.disable();
            map.addControl(new mapboxgl.NavigationControl());

          var stores = {
            type: "FeatureCollection",
            features: features_info,
          };

          /**
           * Wait until the map loads to make changes to the map.
           */
          map.on("load", function (e) {
            /**
             * This is where your '.addLayer()' used to be, instead
             * add only the source without styling a layer
             */
            map.addLayer({
              id: "locations",
              type: "symbol",
              /* Add a GeoJSON source containing service coordinates and information. */
              source: {
                type: "geojson",
                data: stores,
              },
              layout: {
                "icon-image": "",
                "icon-allow-overlap": true,
              },
            });

            /**
             * Add all the things to the page:
             * - The location listings on the side of the page
             * - The markers onto the map
             */
            addMarkers();
          });

          function flyToStore(currentFeature) {
            map.flyTo({
              center: currentFeature.geometry.coordinates,
              bearing: 0,
              duration: 0,
              speed: 0.2,
              curve: 1,
              easing: function (t) {
                return t;
              },
            });
          }

          function createPopUp(currentFeature) {
            var popUps = document.getElementsByClassName("mapboxgl-popup");
            /** Check if there is already a popup on the map and if so, remove it */
            if (popUps[0]) popUps[0].remove();

            var popup = new mapboxgl.Popup({ closeOnClick: false })
              .setLngLat(currentFeature.geometry.coordinates)
              .setHTML(currentFeature.properties.service)
              .addTo(map);
          }

          /* This will let you use the .remove() function later on */
          if (!("remove" in Element.prototype)) {
            Element.prototype.remove = function () {
              if (this.parentNode) {
                this.parentNode.removeChild(this);
              }
            };
          }

          map.on("click", function (e) {
            /* Determine if a feature in the "locations" layer exists at that point. */
            var features = map.queryRenderedFeatures(e.point, {
              layers: ["locations"],
            });

            /* If yes, then: */
            if (features.length) {
              var clickedPoint = features[0];

              /* Fly to the point */
              flyToStore(clickedPoint);

              /* Close all other popups and display popup for clicked store */
              createPopUp(clickedPoint);
            }
          });

          function addMarkers() {
            /* For each feature in the GeoJSON object above: */
            stores.features.forEach(function (marker) {
              /* Create a div element for the marker. */
              var el = document.createElement("div");
              /* Assign a unique `id` to the marker. */
              el.id = "marker-" + marker.properties.id;
              /* Assign the `marker` class to each marker for styling. */
              console.log(marker.properties.icon);
              el.className = "marker";
              el.style.backgroundImage = "url(" + marker.properties.icon + ")";
              el.style.width = marker.properties.iconSize[0] + "px";
              el.style.height = marker.properties.iconSize[1] + "px";
              /**
               * Create a marker using the div element
               * defined above and add it to the map.
               **/
              new mapboxgl.Marker(el, { offset: [0, -23] })
                .setLngLat(marker.geometry.coordinates)
                .addTo(map);

              el.addEventListener("click", function (e) {
                /* Fly to the point */
                flyToStore(marker);
                /* Close all other popups and display popup for clicked store */
                if (map_effects == "popup") {
                  createPopUp(marker);
                }
                /* Highlight listing in sidebar */
                var activeItem = document.getElementsByClassName("active");
                e.stopPropagation();
                if (activeItem[0]) {
                  activeItem[0].classList.remove("active");
                }
              });
            });
          }
        };

        if (!is_mobile) {
          felan_hover_map_effects();
        }
        // End Mapbox
      }

      SERVICE.elements.display_clear();
      var type_pagination = $(".felan-pagination").attr("data-type");
      $(".area-service .felan-service-item").addClass("skeleton-loading");

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_service_archive_ajax",
          paged: paged,
          title: title,
          item_amount: item_amount,
          sort_by: sort_by,
          current_term: current_term,
          type_term: type_term,
          rating: rating,
          location: location,
            range_min: range_min,
            range_max: range_max,
            has_map_val: has_map_val,
            location_country: location_country,
          location_state: location_state,
          location_city: location_city,
          radius_cities: radius_cities,
          categories: categories,
          skills: skills,
          language: language,
          price_min: price_min,
          price_max: price_max,
          time_type: time_type,
          service_layout: service_layout,
		  custom_fields_value: custom_fields_value
        },
        beforeSend: function () {
          archive_service
            .find(".felan-filter-search-map .felan-loading-effect")
            .fadeIn();
          if (
            archive_service
              .find(".form-service-top-filter .btn-top-filter")
              .data("clicked")
          ) {
            archive_service.find(".btn-top-filter .btn-loading").fadeIn();
          }
          if (type_pagination == "loadmore") {
            archive_service.find(".btn-loading").fadeIn();
          }
        },
        success: function (data) {
          archive_service.find(".btn-top-filter .btn-loading").fadeOut();
          archive_service
            .find(".felan-filter-search-map .felan-loading-effect")
            .fadeOut();
          $(".area-service .felan-service-item").removeClass(
            "skeleton-loading"
          );

          if (data.success === true) {
            if (ajax_call == true) {
              if (
                data.pagination_type == "number" ||
                pagination !== "loadmore"
              ) {
                archive_service.find(".area-service").html(data.service_html);
                archive_service
                  .find(".felan-pagination .pagination")
                  .html(data.pagination);
                archive_service.find(".result-count").html(data.count_post);
              } else {
                archive_service.find(".area-service").append(data.service_html);
                if (data.hidden_pagination) {
                  archive_service
                    .find(".felan-pagination .pagination")
                    .html("");
                }
                archive_service.find(".btn-loading").fadeOut();
                archive_service
                  .find(".felan-pagination .pagination")
                  .removeClass("active");
              }
            }
          } else {
            if (ajax_call == true) {
              if (
                data.pagination_type == "number" ||
                pagination !== "loadmore"
              ) {
                archive_service
                  .find(".area-service")
                  .html(
                    '<div class="felan-ajax-result">' + not_service + "</div>"
                  );
                archive_service.find(".result-count").html(data.count_post);
                archive_service.find(".felan-pagination .pagination").html("");
              } else {
                archive_service.find(".area-service").append(data.service_html);
                if (data.hidden_pagination) {
                  $(".felan-pagination .pagination").html("");
                }
                archive_service
                  .find(".felan-pagination .pagination")
                  .removeClass("active");
              }
            }
          }
          if (!is_mobile) {
            felan_hover_map_effects();
          }
          if (has_map) {
            if (mapType == "google_map") {
              felan_map = new google.maps.Map(
                document.getElementById("jobs-map-filter"),
                felan_search_map_option
              );

              if (google_map_style !== "") {
                var styles = JSON.parse(google_map_style);
                felan_map.setOptions({ styles: styles });
              }

              var mapPosition = new google.maps.LatLng(
                "34.0207305",
                "-118.6919226"
              );
              felan_map.setCenter(mapPosition);
              felan_map.setZoom(parseInt(googlemap_default_zoom));
              google.maps.event.addListener(
                felan_map,
                "tilesloaded",
                function () {
                  $(".felan-filter-search-map .felan-loading-effect").fadeOut();
                }
              );

              markers.forEach(function (marker) {
                marker.setMap(null);
              });

              markers = [];
              felan_add_markers(data.service, felan_map);
              felan_my_location(felan_map);
              felan_map.fitBounds(
                markers.reduce(function (bounds, marker) {
                  return bounds.extend(marker.getPosition());
                }, new google.maps.LatLngBounds())
              );
            } else if (mapType == "openstreetmap") {
              felan_osm_add_markers(data.service, maps);
            } else {
              felan_mapbox_add_markers(data.service, map);
            }
          }
        },
      });
    },
  };

  SERVICE.onReady = {
    init: function () {
      SERVICE.elements.init();
    },
  };

  SERVICE.onLoad = {
    init: function () {},
  };

  $(document).ready(function () {
    SERVICE.elements.init();
  });

  $(window).load(SERVICE.onLoad.init);
})(jQuery);
