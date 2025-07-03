 <div class="form-row mb-10">
     <div class="col-sm-3 mb-2">
         <input type="hidden" value="{{ $attribute->id }}"name="product_attributes[]" class="selected_attributes">
         <input type="text" value="{{ $attribute->translation('name', getLocale()) }}"
             class="theme-input-style selectec_options" disabled>
     </div>
     <div class="col-sm-9">
         <div class="form-group d-flex">
             <select class="form-control choice-options-select" name='attribute_{{ $attribute->id }}_selected[]'
                 onchange="variantConbination()" multiple>
                 @foreach ($attribute->attribute_values->where('status', config('settings.general_status.active')) as $values)
                     <option></option>
                     <option value="{{ $values->id }}">{{ $values->name }}</option>
                 @endforeach
             </select>
             <button class="align-self-center ml-1 bg-transparent black" onclick="removeProductChoiceOption(this)">
                 <i class="icofont-trash"></i>
             </button>
         </div>
     </div>
 </div>
 <script>
     (function($) {
         "use strict";
         /*select product choice otions*/
         $('.choice-options-select').select2({
             theme: "classic",
             placeholder: 'Nothing Selected',
             closeOnSelect: false
         });
     })(jQuery);
 </script>
