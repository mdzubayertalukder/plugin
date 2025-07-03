 <!--Product Discount-->
 @if ($action == 'edit_discount')
     <form id="edit-discount-form">
         <input type="hidden" name="id" value="{{ $product_details->id }}">
         <div class="form-row mb-20">
             <div class="col-12">
                 <label class="font-14 bold black ">{{ translate('Discount type') }} </label>
             </div>
             <div class="col-12">
                 <select class="theme-input-style" name="discount_amount_type">
                     <option value="{{ config('tlecommercecore.amount_type.flat') }}"
                         @if ($product_details->discount_type == config('tlecommercecore.amount_type.flat')) selected @endif>
                         {{ translate('Flat') }}</option>
                     <option value="{{ config('tlecommercecore.amount_type.percent') }}"
                         @if ($product_details->discount_type == config('tlecommercecore.amount_type.percent')) selected @endif> {{ translate('Percentage') }}
                     </option>
                 </select>
                 @if ($errors->has('discount_amount_type'))
                     <div class="invalid-input">{{ $errors->first('discount_amount_type') }}</div>
                 @endif
             </div>
         </div>
         <div class="form-row mb-20">
             <div class="col-12">
                 <label class="font-14 bold black ">{{ translate('Discount') }} </label>
             </div>
             <div class="col-12">
                 <input type="text" name="discount_amount" class="theme-input-style" placeholder="0.00"
                     value="{{ $product_details->discount_amount }}">
                 @if ($errors->has('discount_amount'))
                     <div class="invalid-input">{{ $errors->first('discount_amount') }}</div>
                 @endif
             </div>
         </div>
         <div class="form-row justify-content-end">
             <button class="btn rounded sm edit-discount-btn">{{ translate('Save Changes') }}</button>
         </div>
     </form>
 @endif
 <!--End Product Discount-->
 <!--Product Price-->
 @if ($action == 'edit_price')
     <form id="edit-price-form">
         <input type="hidden" name="id" value="{{ $product_details->id }}">
         <input type="hidden" name="has_variant" value="{{ $product_details->has_variant }}">
         <div
             class="single-product-price {{ $product_details->has_variant == config('tlecommercecore.product_variant.single') ? '' : 'd-none' }}">
             <div class="form-row mb-20">
                 <div class="col-12">
                     <label class="font-14 bold black ">{{ translate('Purchase Price') }} </label>
                 </div>
                 <div class="col-12">
                     <input type="text" name="purchase_price" class="theme-input-style"
                         placeholder="{{ translate('Type here') }}"
                         value="{{ $product_details->single_price != null ? $product_details->single_price->purchase_price : 0 }}">
                     @if ($errors->has('purchase_price'))
                         <div class="invalid-input">{{ $errors->first('purchase_price') }}</div>
                     @endif
                 </div>
             </div>
             <div class="form-row mb-20">
                 <div class="col-12">
                     <label class="font-14 bold black ">{{ translate('Unit Price') }} </label>
                 </div>
                 <div class="col-12">
                     <input type="text" name="unit_price" class="theme-input-style"
                         placeholder="{{ translate('Type here') }}"
                         value="{{ $product_details->single_price != null ? $product_details->single_price->unit_price : 0 }}">
                     @if ($errors->has('unit_price'))
                         <div class="invalid-input">{{ $errors->first('unit_price') }}</div>
                     @endif
                 </div>
             </div>
         </div>
         <div
             class="variant-product-price {{ $product_details->has_variant == config('tlecommercecore.product_variant.variable') ? '' : 'd-none' }}">
             <div class="variant-combination">
                 @if ($product_details->variations != null)
                     <div class="table-responsive">
                         <table class="dh-table">
                             <thead>
                                 <tr>
                                     <th>{{ translate('Variant') }}</th>
                                     <th>{{ translate('Purchase Price') }}</th>
                                     <th>{{ translate('Unit Price') }}</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($product_details->variations as $key => $combination)
                                     @php
                                         $variant_array = explode('/', trim($combination->variant, '/'));
                                         $name = '';
                                         foreach ($variant_array as $com_key => $variant) {
                                             $variant_com_array = explode(':', $variant);
                                             if ($variant_com_array[0] === 'color') {
                                                 $option_name = translate('Color');
                                                 $choice_name = \Plugin\TlcommerceCore\Models\Colors::find($variant_com_array[1])->translation('name');
                                             } else {
                                                 $option_name = \Plugin\TlcommerceCore\Models\ProductAttribute::find($variant_com_array[0])->translation('name');
                                                 $choice_name = \Plugin\TlcommerceCore\Models\AttributeValues::find($variant_com_array[1])->name;
                                             }
                                         
                                             $name .= $option_name . ':' . $choice_name . ' | ';
                                         }
                                     @endphp
                                     <tr>
                                         <td class="text-capitalize">
                                             <input type="hidden" name="variations[{{ $key }}][id]"
                                                 value="{{ $combination['id'] }}">
                                             <label class="control-label">{{ trim($name, ' | ') }}</label>
                                             <input type="hidden" value="{{ $combination->variant }}"
                                                 name="variations[{{ $key }}][code]">
                                         </td>
                                         <td>
                                             <input type="text" class="theme-input-style"
                                                 name="variations[{{ $key }}][purchase_price]"
                                                 value="{{ $combination->purchase_price }}">
                                         </td>
                                         <td>
                                             <input type="text" class="theme-input-style"
                                                 name="variations[{{ $key }}][unit_price]"
                                                 value="{{ $combination->unit_price }}">
                                         </td>
                                     </tr>
                                 @endforeach
                         </table>
                         <!-- End Variant combination -->
                     </div>
                 @else
                     <p class="alert alert-danger m-2">{{ translate('No variant selected yet') }}</p>
                 @endif
             </div>
         </div>
         <div class="form-row justify-content-end">
             <button class="btn rounded sm edit-price-btn">{{ translate('Save Changes') }}</button>
         </div>
     </form>
 @endif
 <!--End Product Price-->

 <!--Product Stock-->
 @if ($action == 'edit_stock')
     <form id="edit-stock-form">
         <input type="hidden" name="id" value="{{ $product_details->id }}">
         <input type="hidden" name="has_variant" value="{{ $product_details->has_variant }}">
         <div
             class="single-product-price {{ $product_details->has_variant == config('tlecommercecore.product_variant.single') ? '' : 'd-none' }}">
             <div class="form-row mb-20">
                 <div class="col-12">
                     <label class="font-14 bold black ">{{ translate('Quantity') }} </label>
                 </div>
                 <div class="col-12">
                     <input type="text" name="quantity" class="theme-input-style"
                         placeholder="{{ translate('Type here') }}"
                         value="{{ $product_details->single_price != null ? $product_details->single_price->quantity : 0 }}">
                     @if ($errors->has('quantity'))
                         <div class="invalid-input">{{ $errors->first('quantity') }}</div>
                     @endif
                 </div>
             </div>
         </div>
         <div
             class="variant-product-price {{ $product_details->has_variant == config('tlecommercecore.product_variant.variable') ? '' : 'd-none' }}">
             <div class="variant-combination">
                 @if ($product_details->variations != null)
                     <div class="table-responsive">
                         <table class="dh-table">
                             <thead>
                                 <tr>
                                     <th>{{ translate('Variant') }}</th>
                                     <th>{{ translate('Stock') }}</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($product_details->variations as $key => $combination)
                                     @php
                                         $variant_array = explode('/', trim($combination->variant, '/'));
                                         $name = '';
                                         foreach ($variant_array as $com_key => $variant) {
                                             $variant_com_array = explode(':', $variant);
                                             if ($variant_com_array[0] === 'color') {
                                                 $option_name = translate('Color');
                                                 $choice_name = \Plugin\TlcommerceCore\Models\Colors::find($variant_com_array[1])->translation('name');
                                             } else {
                                                 $option_name = \Plugin\TlcommerceCore\Models\ProductAttribute::find($variant_com_array[0])->translation('name');
                                                 $choice_name = \Plugin\TlcommerceCore\Models\AttributeValues::find($variant_com_array[1])->name;
                                             }
                                         
                                             $name .= $option_name . ':' . $choice_name . ' | ';
                                         }
                                     @endphp
                                     <tr>
                                         <td class="text-capitalize">
                                             <input type="hidden" name="variations[{{ $key }}][id]"
                                                 value="{{ $combination['id'] }}">
                                             <label class="control-label">{{ trim($name, ' | ') }}</label>
                                             <input type="hidden" value="{{ $combination->variant }}"
                                                 name="variations[{{ $key }}][code]">
                                         </td>
                                         <td>
                                             <input type="text" class="theme-input-style"
                                                 name="variations[{{ $key }}][quantity]"
                                                 value="{{ $combination->quantity }}">
                                         </td>
                                     </tr>
                                 @endforeach
                         </table>
                         <!-- End Variant combination -->
                     </div>
                 @else
                     <p class="alert alert-danger m-2">{{ translate('No variant selected yet') }}</p>
                 @endif
             </div>
         </div>
         <div class="form-row justify-content-end">
             <button class="btn rounded sm  edit-stock-btn">{{ translate('Save Changes') }}</button>
         </div>
     </form>
 @endif
 <!--End Product Stock-->
 <script>
     (function($) {
         "use strict";
         /**
          * Will edit discount
          * 
          **/
         $('.edit-discount-btn').on('click', function(e) {
             e.preventDefault();
             $.ajax({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 },
                 type: "POST",
                 data: $('#edit-discount-form').serialize(),
                 url: '{{ route('plugin.tlcommercecore.product.quick.update.discount') }}',
                 success: function(data) {
                     if (data.success) {
                         toastr.success(
                             '{{ translate('Product discount updated successfully') }}');
                         location.reload();
                     } else {
                         toastr.error('{{ translate('Product discount update faled') }}');
                     }
                 }
             });
         });
         /**
          * Will edit price
          * 
          **/
         $('.edit-price-btn').on('click', function(e) {
             e.preventDefault();
             $.ajax({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 },
                 type: "POST",
                 data: $('#edit-price-form').serialize(),
                 url: '{{ route('plugin.tlcommercecore.product.quick.update.price') }}',
                 success: function(data) {
                     if (data.success) {
                         toastr.success(
                             '{{ translate('Product price updated successfully') }}');
                         location.reload();
                     } else {
                         toastr.error('{{ translate('Product price update faled') }}');
                     }
                 }
             });
         });
         /**
          * Will edit stock
          * 
          **/
         $('.edit-stock-btn').on('click', function(e) {
             e.preventDefault();
             $.ajax({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 },
                 type: "POST",
                 data: $('#edit-stock-form').serialize(),
                 url: '{{ route('plugin.tlcommercecore.product.quick.update.stock') }}',
                 success: function(data) {
                     if (data.success) {
                         toastr.success(
                             '{{ translate('Product stock updated successfully') }}');
                         location.reload();
                     } else {
                         toastr.error('{{ translate('Product stock update faled') }}');
                     }
                 }
             });
         });
     })(jQuery);
 </script>
