  <!-- Jquery Ui js -->
  <script src="{{ asset('/public/backend/assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
  <!-- Summernote js -->
  <script src="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.js') }}"></script>
  <!--Select2-->
  <script src="{{ asset('/public/backend/assets/plugins/select2/select2.min.js') }}"></script>
  <script>
      const action_area_hide_widgets = ['banner'];
      (function($) {
          'use strict';
          initDropzone();

          $(document).ready(function() {

              setDivMaxHeight();
              $(window).resize(setDivMaxHeight);

              $(window).on('scroll', function() {
                  if ($(window).scrollTop() > 0) {
                      $('.builder-sidebar').addClass('fixed-sidebar fadeInRight animated');
                  } else {
                      $('.builder-sidebar').removeClass('fixed-sidebar fadeInRight animated');
                  }
              });

              // Initialize the ajax token
              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });

              //Display widget list
              $(document).on('click', '.load-widget-list', function(e) {
                  e.preventDefault();
                  loadWidgetList();
              });

              // Widget draggable initialise
              $('.widget-single').draggable({
                  revert: "invalid",
                  helper: "clone",
                  cursor: 'pointer',
                  zIndex: 10000,
                  start: function(event, ui) {
                      ui.helper.addClass("widget-placeholder");
                  }
              });

              // Sections sortable initialise
              $('.section-list').sortable({
                  cursor: "move",
                  revert: "invalid",
                  handle: '.drag-layout',
                  placeholder: 'widget-placeholder',
                  update: function(e, u) {
                      let data = $(this).sortable('serialize');
                      updateSectionOrder(data);
                      loadWidgetList();
                  }
              });

              // Widget Dropable and Sortable initialise
              droppableAndSortableInit();

              // Add New Section Modal show
              $(document).on('click', '#add_new_section_btn', function(e) {
                  e.preventDefault();
                  $('#layout-modal').modal('show');
              });

              // Select Layout
              $(document).on('change', 'input[name="section_layout"]', function(e) {
                  e.preventDefault();
                  let layout = $('input[name="section_layout"]:checked').val();
                  if (layout) {
                      let order = $('.section-list').children().length + 1;
                      createNewSection(layout, order);
                      $('input[name="section_layout"]:checked').prop('checked', false);
                  }
              });

              // Remove Section Modal Show
              $(document).on('click', '.remove-section', function(e) {
                  e.preventDefault();
                  $('#delete-modal').modal('show');
                  let section_id = $(this).parent().attr('id').replace('section_', '');
                  $('#delete-id').val(section_id);
                  $('#delete-btn').addClass('delete-section-btn');
              });

              // Remove Section button click
              $(document).on('click', '.delete-section-btn', function(e) {
                  e.preventDefault();
                  let section_id = $('#delete-id').val();
                  removeSection(section_id);
                  $('#delete-btn').removeClass('delete-section-btn');
              });

              // Edit section button click
              $(document).on('click', '.edit-section', function(e) {
                  e.preventDefault();
                  $(".single-section").removeClass('bg-primary-light');
                  $(".single-widget").removeClass('bg-primary-light');
                  $(this).parent().addClass('bg-primary-light');
                  let section_id = $(this).parent().attr('id').replace('section_', '');
                  getSectionProperties(section_id);
              });

              // Remove Widget Modal Show
              $(document).on('click', '.removeWidget', function(e) {
                  e.preventDefault();
                  $('#delete-modal').modal('show');
                  let layout_widget_id = $(this).parents(':eq(2)').data('layoutWidgetId');
                  let section_id = $(this).parents(':eq(5)').attr('id').replace('section_', '');

                  $('#delete-id').val(layout_widget_id);
                  $('#section-id').val(section_id);

                  $('#delete-btn').addClass('delete-widget-btn');
              });

              // Remove Widget button click
              $(document).on('click', '.delete-widget-btn', function(e) {
                  e.preventDefault();
                  let layout_widget_id = $('#delete-id').val();
                  let section_id = $('#section-id').val();
                  let widget_name = $('[data-layout-widget-id="' + layout_widget_id + '"]').data(
                      'widget');

                  removeWidget(layout_widget_id, section_id, widget_name);
                  $('#delete-btn').removeClass('delete-widget-btn');
              });

              // Edit Widget button click
              $(document).on('click', '.editWidget', function(e) {
                  e.preventDefault();
                  $(".single-section").removeClass('bg-primary-light');
                  $(".single-widget").removeClass('bg-primary-light');
                  $(this).parent().parent().parent().addClass('bg-primary-light');

                  let widget = $(this).parents(':eq(2)').data('widget');
                  let section_id = $(this).parents(':eq(5)').attr('id').replace('section_', '');
                  let layout_widget_id = $(this).parents(':eq(2)').data('layoutWidgetId');
                  let lang = '{{ getDefaultLang() }}';

                  getWidgetProperties(widget, layout_widget_id, section_id, lang);
              });

              // Search Widget and filter widget list
              let all_widgets = $('.widget-list').children();
              $(document).on('keyup', '#widget-search', function() {
                  let text = $(this).val().toLowerCase();

                  let search_widgets = all_widgets.filter((index, widget) => {
                      let widget_name = $(widget).find('.widget-title').text().toLowerCase();
                      return widget_name.includes(text)
                  });

                  $('.widget-list').empty().append(search_widgets);

                  $('.widget-single').draggable({
                      revert: "invalid",
                      helper: "clone",
                      cursor: 'pointer',
                      zIndex: 10000,
                      start: function(event, ui) {
                          ui.helper.addClass("widget-placeholder");
                      }
                  });
              });

              // Widget Translate Form
              $(document).on('click', '.lang', function(e) {
                  e.preventDefault();
                  let widget = $(this).data('widget');
                  let lang = $(this).data('lang');
                  let section_id = $('#properties-body').find('input[name="section_id"]').val();
                  let layout_widget_id = $('#properties-body').find(
                      'input[name="layout_has_widget_id"]').val();

                  getWidgetProperties(widget, layout_widget_id, section_id, lang);
              });

              // Submit properties form
              $(document).on('submit', '#properties-form', function(e) {
                  e.preventDefault();

                  // Check Required Field is Not Left Empty
                  if (emptyRequiredField()) {
                      toastr.error('Content Fields Are Required', 'Error');
                  } else {
                      $('.loader').removeClass('d-none').next().attr('disabled', true);
                      let data = $('#properties-form').serializeArray();
                      var updated_data = {};

                      //Modifying The Form Data
                      $.map(data, function(value) {
                          var name = value['name'];
                          var val = value['value'];

                          if (name.endsWith("[]")) {
                              name = name.slice(0, -2); // Remove "[]"
                              if (!updated_data[name]) {
                                  updated_data[name] = [val];
                              } else {
                                  updated_data[name].push(val);
                              }
                          } else {
                              updated_data[name] = val;
                          }
                      });

                      updated_data.page = '{{ $data['id'] }}';
                      saveProperties(updated_data);
                  }
              });

              // Color Field Value
              $(document).on('input', '.color-picker', function(e) {
                  let target = e.target;
                  $(target).closest('.addon').find('.color-input').val($(this).val());
              });

              // Range Selector
              $(document).on('input', '.range-selector', function() {
                  let input_filed = $(this).attr('id').replace('range_', '');
                  $('input[name="' + input_filed + '"]').val($(this).val());
              });

          });

          function setDivMaxHeight() {
              var screenHeight = $(window).height();
              $('.widget-list').css('max-height', (screenHeight - 265) + 'px');
              $('.widget-list').css('min-height', (screenHeight - 265) + 'px');

              $('.property-fields').css('max-height', (screenHeight - 267) + 'px');
              $('.property-fields').css('min-height', (screenHeight - 267) + 'px');
          }

          // Load Widget list
          function loadWidgetList() {
              $('#properties-section').find('h4').html("{{ translate('Available Widgets') }}");
              $(".single-section").removeClass('bg-primary-light');
              $(".single-widget").removeClass('bg-primary-light');

              if ($('.widget-list-wrapper').hasClass('d-none')) {
                  $('.widget-list-wrapper').removeClass('d-none');
              }

              if (!$('.properties-wrapper').hasClass('d-none')) {
                  $('.properties-wrapper').addClass('d-none');
              }
          }

          // Make Layout
          function appendSectionToPage(layout, section_id, layout_ids) {
              let colums = layout.split('_');
              let columns_markup = '';
              for (let i = 0; i < colums.length; i++) {
                  columns_markup +=
                      `<div class="col-${colums[i]} p-0 section-column" style="border:1px solid" data-section-layout-id="${layout_ids[i]}"></div>`;
              }
              let layout_markup = `
                        <div class ="row single-section p-1 rounded" id="section_${section_id}">
                            <a href="#" class="black my-auto drag-layout">
                                <i class="icofont-drag"></i>
                            </a>
                            <div class="row my-2 mx-0 col-11 bg-transparent layout-height">` + columns_markup + `</div>
                            <a href="#" class="black my-auto edit-section">
                                <i class="icofont-options"></i>
                            </a>
                            <a href="#" class="black my-auto ml-2 remove-section">
                                <i class="icofont-trash"></i>
                            </a>
                        </div>
                    `;

              $('.section-list').next().remove();
              $('.section-list').append(layout_markup);
              $('#layout-modal').modal('hide');
              droppableAndSortableInit();
          };



          // Will initialise the droppable and sortable of widget
          function droppableAndSortableInit() {
              // Section column droppable for widgets initialise
              $('.section-column').droppable({
                  accept: ".widget-single",
                  drop: function(event, ui) {
                      let widget = $(ui.draggable).clone();
                      const data = {
                          widget_id: $(widget).data('widgetId'),
                          section_layout_id: $(this).data('sectionLayoutId')
                      };
                      saveWidget(data, widget, this);
                  },
              });

              // Section columns are sortable initialise
              $('.section-column').sortable({
                  cursor: "move",
                  revert: "invalid",
                  handle: '.dragWidget',
                  connectWith: ".section-column",
                  placeholder: 'widget-placeholder',
                  update: function(event, ui) {
                      let ownlist = ui.sender == null;
                      if (!ownlist) {
                          let data = {
                              widget_id: $(ui.item).data('widgetId'),
                              layout_widget_id: $(ui.item).data('layoutWidgetId'),
                              new_layout_id: $(this).data('sectionLayoutId'),
                              prev_layout_id: $(ui.sender).data('sectionLayoutId'),
                              new_section_id: $(this).parents(':eq(1)').attr('id').replace('section_',
                                  ''),
                              prev_section_id: $(ui.sender).parents(':eq(1)').attr('id').replace(
                                  'section_', ''),
                              page_permalink: "{{ $data['permalink'] }}"
                          };
                          changeWidgetPosition(data);
                      } else {
                          updateWidgetOrder($(this).data('sectionLayoutId'));
                      }
                  }
              });
          };

          // Append new widget to layout
          function appendWidgetToLayout(widget, section, id) {
              $(widget).removeClass('mb-2 text-center widget-single ui-draggable ui-draggable-handle col-lg-6');
              $(widget).addClass('section-widget single-widget');
              $(widget).find('.card').addClass(
                  'card-body flex-row bg-transparent justify-content-between px-3 py-3 flex-wrap gap-10')

              $(widget).attr('data-layout-widget-id', id);
              $(widget).appendTo(section);
              let actionMarkup = `
                            <div class="widget-icons">
                                <a href="javascript:void(0);" class="black dragWidget"><i class="icofont-drag1"></i></a>
                                <a href="javascript:void(0);" class="black editWidget"><i class="icofont-options mx-1"></i></a>
                                <a href="javascript:void(0);" class="black removeWidget"><i class="icofont-trash"></i><a>
                            </div>`;
              $(widget).find('.card').append(actionMarkup);
          };

          // Dissable All Fields That Are Not For Translate
          function dissableNotTranslatedField() {

              $('#myTabContent .form-group, #myTabContent .form-row').each(function(index, element) {
                  if (!$(element).hasClass('translate-field')) {
                      $(element).addClass('area-disabled');
                  }
              });
          }

          //Check If Required Field is Empty
          function emptyRequiredField() {
              let empty = false;
              $("#properties-form :input[required]").each(function() {
                  if ($(this).val() === "") {
                      empty = true;
                      return false; // Exit the loop early
                  }
              });
              return empty;
          }

          /**
           * 
           * AJAX REQUESTS  
           * 
           * */

          //Create selected section and make layouts
          function createNewSection(layout, order) {
              $.ajax({
                  type: "post",
                  url: "{{ route('plugin.builder.pageSection.new') }}",
                  data: {
                      layout: layout,
                      page_id: "{{ $data['id'] }}",
                      order: order
                  },
                  success: function(response) {
                      toastr.success(response.message, 'Success');
                      appendSectionToPage(layout, response.data.section_id, response.data.layout_ids);
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Page Section Create Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  }
              });
          };

          //Remove selected sections
          function removeSection(section_id) {
              $.ajax({
                  type: "post",
                  url: "{{ route('plugin.builder.pageSection.remove') }}",
                  data: {
                      id: section_id,
                      page_id: "{{ $data['id'] }}",
                      page_permalink: "{{ $data['permalink'] }}"
                  },
                  success: function(response) {
                      toastr.success(response.message, 'Success');
                      $('#section_' + section_id).remove();
                      $('#delete-modal').modal('hide');
                      if ($('.section-list').children().length < 1) {
                          $('.section-list').after(
                              `<p class="alert alert-danger text-center">No Section Found</p>`);
                      }

                      loadWidgetList();
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Page Section Remove Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  }
              });
          };

          //Update Section Order
          function updateSectionOrder(data) {
              $.ajax({
                  type: 'post',
                  url: "{{ route('plugin.builder.pageSection.sorting') }}",
                  data: data,
                  success: function(response) {
                      toastr.success(response.message, 'Success');
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Page Section Remove Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  }
              });
          };

          // Get Section Properties
          function getSectionProperties(section_id) {
              $.ajax({
                  type: "post",
                  url: "{{ route('plugin.builder.pageSection.get.properties') }}",
                  data: {
                      section_id: section_id
                  },
                  success: function(response) {
                      $('.property-fields').html(response.data);
                      $('#save-properties').removeClass('d-none');
                      $('#properties-section').find('h4').html("{{ translate('Section Properties') }}");
                      $('#properties-body').find('input[name="type_key"]').val('section_id');
                      $('#properties-body').find('input[name="section_id"]').val(section_id);
                      $('#properties-body').find('input[name="layout_has_widget_id"]').val('');

                      if (!$('.widget-list-wrapper').hasClass('d-none')) {
                          $('.widget-list-wrapper').addClass('d-none');
                      }

                      $('.properties-wrapper').removeClass('d-none');

                      if ($('.properties-action-area').hasClass('d-none')) {
                          $('.properties-action-area').removeClass('d-none');
                      }
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Section Edit Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  }
              });
          };


          // Save to widget to database
          function saveWidget(data, widget, section) {
              $.ajax({
                  type: "post",
                  url: "{{ route('plugin.builder.pageSection.widget.add') }}",
                  data: {
                      section_layout_id: data.section_layout_id,
                      widget_id: data.widget_id
                  },
                  success: function(response) {
                      toastr.success(response.message, 'Success');
                      appendWidgetToLayout(widget, section, response.data.id);
                      updateWidgetOrder(data.section_layout_id);
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Widget Adding Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  }
              });
          };

          // Remove widget from layouts and database
          function removeWidget(layout_widget_id, section_id, widget_name) {
              $.ajax({
                  type: "post",
                  url: "{{ route('plugin.builder.pageSection.widget.remove') }}",
                  data: {
                      layout_widget_id: layout_widget_id,
                      section_id: section_id,
                      widget_name: widget_name,
                      page_permalink: "{{ $data['permalink'] }}"
                  },
                  success: function(response) {
                      toastr.success(response.message, 'Success');
                      let element = $('[data-layout-widget-id="' + layout_widget_id + '"]');
                      let layout_id = element.parent().data('sectionLayoutId');
                      element.remove();
                      $('#delete-modal').modal('hide');
                      updateWidgetOrder(layout_id);
                      loadWidgetList();
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Widget Removing Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  }
              });
          };

          // Update widget position by sorting widget
          function changeWidgetPosition(data) {
              $.ajax({
                  type: "post",
                  url: "{{ route('plugin.builder.pageSection.widget.updatePosition') }}",
                  data: {
                      ...data
                  },
                  success: function(response) {
                      toastr.success(response.message, 'Success');
                      updateWidgetOrder(data.new_layout_id);
                      loadWidgetList();
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Widget Position Update Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  }
              });
          };

          // Update Widget Order
          function updateWidgetOrder(layout_id) {
              $('.section-column').sortable("disable");
              let data = $('[data-section-layout-id="' + layout_id + '"]').children();
              let layout_widget_ids = [];
              data.each(function(index, element) {
                  layout_widget_ids.push($(element).data('layoutWidgetId'));
              });

              if (layout_widget_ids.length) {
                  $.ajax({
                      type: "post",
                      url: "{{ route('plugin.builder.pageSection.widget.order') }}",
                      data: {
                          layout_id: layout_id,
                          layout_widget_ids: layout_widget_ids
                      },
                      success: function(response) {
                          $('.section-column').sortable("enable");
                      },
                      error: function(xhr, status, error) {
                          let message = "{{ translate('Widget Order Request Failed') }}";
                          if (xhr.responseJSON) {
                              message = xhr.responseJSON.message;
                          }
                          toastr.error(message, 'ERROR!!');
                      }
                  });
              }
          };

          // Get Widget Properties Form
          function getWidgetProperties(widget, layout_widget_id, section_id, lang) {
              $.ajax({
                  type: "post",
                  url: "{{ route('plugin.builder.pageSection.widget.get.properties') }}",
                  data: {
                      widget_name: widget,
                      layout_widget_id: layout_widget_id,
                      lang: lang
                  },
                  success: function(response) {
                      $('.property-fields').html(response.data);
                      $('#properties-section').find('h4').html(widget.split('_').map((str) => str.charAt(
                              0).toUpperCase() + str.slice(1)).join(' ') + ' ' +
                          "{{ translate('Properties') }}");
                      $('#properties-body').find('input[name="type_key"]').val('layout_has_widget_id');
                      $('#properties-body').find('input[name="section_id"]').val(section_id);
                      $('#properties-body').find('input[name="layout_has_widget_id"]').val(
                          layout_widget_id);

                      if (!$('.widget-list-wrapper').hasClass('d-none')) {
                          $('.widget-list-wrapper').addClass('d-none');
                      }

                      $('.properties-wrapper').removeClass('d-none');

                      // Dissable Fields if not default language
                      if (lang != "{{ getDefaultLang() }}") {
                          dissableNotTranslatedField();
                      }

                      $('.properties-action-area').removeClass('d-none');
                      if (action_area_hide_widgets.includes(widget) && !$('.properties-action-area')
                          .hasClass('d-none')) {
                          $('.properties-action-area').addClass('d-none');
                      }
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Widget Edit Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  }
              });
          };

          // Save Section/Widget Properties
          function saveProperties(data) {
              let url = "{{ route('plugin.builder.pageSection.widget.update.properties') }}";
              if (data.type_key == 'section_id') {
                  url = "{{ route('plugin.builder.pageSection.update.properties') }}"
              }
              $.ajax({
                  type: "post",
                  url: url,
                  data: {
                      ...data
                  },
                  success: function(response) {
                      toastr.success(response.message, 'Success');
                  },
                  error: function(xhr, status, error) {
                      let message = "{{ translate('Properties Update Request Failed') }}";
                      if (xhr.responseJSON) {
                          message = xhr.responseJSON.message;
                      }
                      toastr.error(message, 'ERROR!!');
                  },
                  complete: function(param) {
                      $('.loader').addClass('d-none').next().attr('disabled', false);
                  }
              });
          };

          /**
           * 
           * End AJAX REQUESTS  
           * 
           * */


      })(jQuery);
  </script>
