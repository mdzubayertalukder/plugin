 @foreach ($countries as $country)
     <li class="cl-item cl-item-open">
         @php
             $states = \Plugin\TlcommerceCore\Models\States::with(['state_translations'])
                 ->where('country_id', $country->id)
                 ->select('id', 'name')
                 ->get();
         @endphp
         <div class="cl-label-wrap">
             @if (\Plugin\TlcommerceCore\Repositories\ShippingRepository::countryInAnotherShippingRate($country->id))
                 <input class="label-checkbox js-check-all" type="checkbox" name="country_id[]"
                     id="country-{{ $country->id }}" value="{{ $country->id }}"
                     data-check-all="country-{{ $country->id }}" disabled>
                 <label class="cl-label-title black"
                     for="country-{{ $country->id }}"><span>{{ $country->translation('name') }}</span>
                 </label>
                 <span class="another-zone">{{ translate('In Another Rate') }}</span>
             @else
                 <input class="label-checkbox js-check-all" type="checkbox" name="country_id[]"
                     id="country-{{ $country->id }}" value="{{ $country->id }}"
                     data-check-all="country-{{ $country->id }}">
                 <label class="cl-label-title black"
                     for="country-{{ $country->id }}"><span>{{ $country->translation('name') }}</span>
                 </label>
             @endif

             <div class="d-flex">
                 <span class="country-found">{{ count($states) }} {{ translate('States') }}</span>
                 <span class="cl-label-tools">
                     <a href="#"><i class="icofont-simple-down"></i></i></a>
                 </span>
             </div>
         </div>
         <!-- State List -->
         @if (count($states) > 0)
             <ul class="js-check-all-target js-check-all" data-check-all="country-{{ $country->id }}">
                 @foreach ($states as $state)
                     @php
                         $cities = \Plugin\TlcommerceCore\Models\Cities::with(['city_translations'])
                             ->where('state_id', $state->id)
                             ->select('id', 'name')
                             ->get();
                     @endphp
                     <li class="cl-item">
                         <div class="cl-label-wrap">
                             @if (\Plugin\TlcommerceCore\Repositories\ShippingRepository::stateInAnotherShippingRate($state->id))
                                 <input class="label-checkbox" type="checkbox" name="state_id[]"
                                     id="state-{{ $state->id }}" value="{{ $state->id }}" disabled
                                     data-check-all="state-{{ $state->id }}">
                                 <label class="cl-label-title"
                                     for="state-{{ $state->id }}">{{ $state->translation('name') }}

                                 </label>
                                 <span class="another-zone">{{ translate('In another Rate') }}</span>
                             @else
                                 <input class="label-checkbox" type="checkbox" name="state_id[]"
                                     id="state-{{ $state->id }}" value="{{ $state->id }}"
                                     data-check-all="state-{{ $state->id }}">
                                 <label class="cl-label-title"
                                     for="state-{{ $state->id }}">{{ $state->translation('name') }}

                                 </label>
                             @endif
                             <div class="d-flex">
                                 <span class="country-found">{{ count($cities) }}
                                     {{ translate('Cities') }}
                                 </span>
                                 <span class="cl-label-tools">
                                     <a href="#"><i class="icofont-simple-down"></i></i></a>
                                 </span>
                             </div>
                         </div>
                         <!-- City List -->
                         @if (count($cities) > 0)
                             <ul class="js-check-all-target js-check-all" data-check-all="state-{{ $state->id }}">
                                 @foreach ($cities as $city)
                                     <li class="cl-item">
                                         <div class="cl-label-wrap">
                                             @if (\Plugin\TlcommerceCore\Repositories\ShippingRepository::cityInAnotherShippingRate($city->id))
                                                 <input class="label-checkbox" type="checkbox" name="city_id[]"
                                                     class="city-id" value="{{ $city->id }}"
                                                     id="city-{{ $city->id }}" disabled>
                                                 <label class="cl-label-title"
                                                     for="city-{{ $city->id }}">{{ $city->translation('name') }}</label>
                                                 <span class="another-zone">{{ translate('In Another Rate') }}</span>
                                             @else
                                                 <input class="label-checkbox" type="checkbox" name="city_id[]"
                                                     class="city-id" value="{{ $city->id }}"
                                                     id="city-{{ $city->id }}">
                                                 <label class="cl-label-title"
                                                     for="city-{{ $city->id }}">{{ $city->translation('name') }}</label>
                                             @endif
                                         </div>
                                     </li>
                                 @endforeach
                             </ul>
                         @endif
                     </li>
                 @endforeach
             </ul>
         @endif
     </li>
 @endforeach
 <script>
     (function($) {
         "use strict";
         $(document).ready(function() {

             $('.cl-item').each(function() {
                 if ($(this).find('> ul').length == 0) {
                     $(this).addClass('cl-item-no-sub');
                 }
             })
             /*Check All*/
             $(".js-check-all").on("change", function(event) {
                 var group = $(event.target).data("check-all");

                 var dataCheckGroup = '[data-check-all="' + group + '"]';

                 if ($(event.target).prop("checked")) {
                     $(".js-check-all-target")
                         .filter(dataCheckGroup)
                         .find('input[type="checkbox"]:not(:disabled)')
                         .prop("checked", true);
                     return;
                 }

                 $(".js-check-all-target")
                     .filter(dataCheckGroup)
                     .find('input[type="checkbox"]:not(:disabled)')
                     .prop("checked", false);
             });
         });
     })(jQuery);
 </script>
