 <form id="edit-shipping-rate-form">
     @csrf
     <input type="hidden" name="id" value="{{ $shipping_rate->id }}">
     <div class="form-row mb-20">
         <div class="col-sm-12">
             <label class="font-14 bold black">{{ translate('Title') }} </label>
         </div>
         <div class="col-sm-12">
             <input type="text" name="title" value="{{ $shipping_rate->title }}" class="theme-input-style"
                 placeholder="{{ translate('Enter Title') }}">
         </div>
     </div>
     <div class="form-row mb-20">
         <div class="col-sm-12">
             <label class="font-14 bold black">{{ translate('Shipping Cost') }} </label>
         </div>
         <div class="col-sm-12">
             <input type="number" name="cost" class="theme-input-style" placeholder="0.00"
                 value="{{ $shipping_rate->cost }}">
         </div>
     </div>

     <div class="form-row mb-20">
         <div class="col-sm-12">
             <label class="font-14 bold black">{{ translate('Locations') }} </label>
         </div>
         <div class="form-group w-100">
             <div class="input-group addon ov-hidden">
                 <input type="text" name="location_search" id="location_search" class="form-control style--two"
                     value="" placeholder="{{ translate('Search Location') }}">
                 <div class="input-group-append search-btn">
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
             <button class="btn long create-new-zone" type="submit">{{ translate('Save Changes') }}</button>
         </div>
     </div>
 </form>

 <script>
     (function($) {
         "use strict";
         let searched_location_page_number = 1;
         let location_page_number = 1;
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
                     type: 'location',
                     rate_id: "{{ $shipping_rate->id }}"
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
                     type: 'location',
                     rate_id: "{{ $shipping_rate->id }}"
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
     })(jQuery);
 </script>
