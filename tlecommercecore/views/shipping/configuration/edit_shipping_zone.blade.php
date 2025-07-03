 <form id="edit-zone-form">
     @csrf
     <div class="form-row mb-20">
         <div class="col-sm-12">
             <label class="font-14 bold black">{{ translate('Zone Name') }} </label>
         </div>
         <div class="col-sm-12">
             <input type="hidden" name="id" value="{{ $zone_info->id }}">
             <input type="hidden" name="profile_id" value="{{ $profile_info->id }}">
             <input type="text" name="name" class="theme-input-style" value="{{ $zone_info->name }}"
                 placeholder="{{ translate('Type Name') }}">
             @if ($errors->has('name'))
                 <div class="invalid-input">{{ $errors->first('name') }}</div>
             @endif
         </div>
     </div>
     <div class="form-row mb-20">
         <div class="col-sm-2 my-auto">
             <label class="font-14 bold black">{{ translate('Search') }} </label>
         </div>
         <div class="col-sm-10">
             <div class="input-group addon radius-50 ov-hidden">
                 <input type="text" name="location_search_edit" id="location_search_edit"
                     class="form-control style--two" value="" placeholder="{{ translate('Search') }}">
                 <div class="input-group-append search-btn-edit">
                     <span class="input-group-text bg-light pointer">
                         <i class="icofont-search"></i>
                     </span>
                 </div>
             </div>
         </div>
     </div>
     <div class="form-row mb-20">
         <div class="col-sm-12 edit-location-box">
             <ul class="cl-start-wrap pl-1 edit-location-options">
             </ul>
             <div class="d-flex justify-content-center edit-loader">
                 <button type="button" class="btn sm">{{ translate('Load More') }}</button>
             </div>
         </div>

     </div>

     <div class="form-row">
         <div class="col-12 text-right">
             <button class="btn long update-zone">{{ translate('Save Changes') }}</button>
         </div>
     </div>
 </form>
 <script>
     (function($) {
         "use strict";
         $(document).ready(function() {
             getEditZoneCountriesOptions();

             // Search field keyup event ajax call
             $('#location_search_edit').on('keypress', function(e) {
                 if (e.which == 13) {
                     e.preventDefault();
                     let value = $(this).val();
                     searched_location_page_number = 1;
                     if (value && value.length > 0) {
                         getSearchedLocationsEdit(value);
                     } else {
                         getEditZoneCountriesOptions();
                     }
                 }
             });

             // search button click ajax call
             $('.search-btn-edit').on('click', function() {
                 let value = $('#location_search_edit').val();
                 searched_location_page_number = 1;
                 if (value && value.length > 0) {
                     getSearchedLocationsEdit(value);
                 }
             })

             /**
              * Load location box
              * 
              **/
             $('.edit-loader button').on('click', function() {
                 let searchKey = $('#location_search_edit').val();
                 if (searchKey && searchKey.length > 0) {
                     if (searched_location_all_page_count == 0 || searched_location_page_number <=
                         searched_location_all_page_count) {
                         getSearchedLocationsEdit(searchKey);
                     }
                 } else {
                     getEditZoneCountriesOptions();
                 }
             });

         });


         /**
          * Get Searched Location options in Edit
          * 
          **/
         function getSearchedLocationsEdit(searchKey) {
             if (searched_location_page_number == 1) {
                 $('.edit-location-options').html('');
             }
             $.ajax({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 },
                 type: "POST",
                 data: {
                     page: searched_location_page_number,
                     perPage: 1,
                     key: searchKey,
                     profile_id: '{{ $profile_info->id }}',
                     zone_id: '{{ $zone_info->id }}'
                 },
                 url: '{{ route('plugin.tlcommercecore.shipping.search.location.ul.list.edit') }}',
                 success: function(response) {
                     if (response.success) {
                         if (response.found) {
                             $('.edit-location-options').append(response.list);
                             searched_location_page_number = searched_location_page_number + 1;
                             searched_location_all_page_count = response.totalPage;

                             if (searched_location_page_number > response.totalPage) {
                                 $('.edit-loader > button').prop('disabled', true);
                             } else {
                                 $('.edit-loader > button').prop('disabled', false);
                             }
                         } else {
                             let notFoundKey = "{{ translate('Not Found') }}";
                             $('.edit-location-options').html(`
                                <div class="text-center mt-5"> ${notFoundKey} </div>
                            `);
                         }
                     }
                 }
             });
         }

         /**
          * Get Location options
          * 
          **/
         function getEditZoneCountriesOptions() {

             $.ajax({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 },
                 type: "POST",
                 data: {
                     page: location_page_number,
                     perPage: 1,
                     profile_id: '{{ $profile_info->id }}',
                     zone_id: '{{ $zone_info->id }}'
                 },
                 url: '{{ route('plugin.tlcommercecore.shipping.location.ul.list.edit') }}',
                 success: function(response) {
                     if (response.success) {
                         $('.edit-location-options').append(response.list);
                         location_page_number = location_page_number + 1;
                     }
                 }
             });
         }
         /**
          * Update shipping zone
          * 
          * 
          * */
         $('.update-zone').click('on', function(e) {
             e.preventDefault();
             $.ajax({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 },
                 type: "POST",
                 data: $('#edit-zone-form').serialize(),
                 url: '{{ route('plugin.tlcommercecore.shipping.profile.zones.update') }}',
                 success: function(response) {
                     location.reload();
                 },
                 error: function(response) {
                     $.each(response.responseJSON.errors, function(field_name, error) {
                         $(document).find('[name=' + field_name + ']').after(
                             '<div class="invalid-input">' + error + '</div>')
                     })
                 }

             });
         })
     })(jQuery);
 </script>
