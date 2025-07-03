 <link href="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.css') }}" rel="stylesheet" />
 <style>
     .product-select-box {
         border: 1px dotted;
         padding: 5px 8px;
     }

     .status-list li span.badge {
         line-height: unset;
     }

     .product-title {
         display: block;
         display: -webkit-box;
         -webkit-line-clamp: 1;
         -webkit-box-orient: vertical;
         overflow: hidden;
         text-overflow: ellipsis;
     }
 </style>
 <form id="order-status-update-form">
     <p class="text-error"></p>
     <input type="hidden" name="order_id" value="{{ $order_details->id }}">
     <!--Product checkbox-->
     <div class="form-row">
         <div class="d-flex justify-content-between label w-100">
             <label class="font-14 bold black">{{ translate('Select Product') }}</label>
             <div class="d-flex align-items-center">
                 <label class="custom-checkbox position-relative mr-2">
                     <input type="checkbox" id="selectAllItems" name="select_all" class="select-all-products">
                     <span class="checkmark"></span>
                 </label>
                 <label for="selectAllItems">{{ translate('Select All') }}</label>
             </div>
         </div>
         <div class="invalid-input mb-1 mt-0 products-error"></div>
         @foreach ($order_details->products as $key => $product)
             <div class="col-12 col-lg-6 mb-20">
                 <div class="product-select-box">
                     <label class="d-flex justify-content-between m-0 py-1">
                         <span class="label-title">
                             @if ($product->product_details != null)
                                 <div class="align-items-center d-flex product-info">
                                     <div class="image"><img src="{{ $product->image }}"
                                             alt="{{ $product->product_details->name }}" class="img-60 radius-0">
                                     </div>
                                     <div class="description">
                                         <div class="info">
                                             <h5 class="product-title" title="{{ $product->product_details->name }}">
                                                 {{ $product->product_details->name }}</h5>
                                             <p class="mb-0 font-weight-normal">
                                                 {{ $product->variant_id }}</p>
                                         </div>
                                         <div class="delivery-status">
                                             <span class="font-weight-light">{{ translate('Delivery status') }}:</span>
                                             <span
                                                 class="badge {{ $product->delivery_status_label() }} text-capitalize">{{ $product->delivery_status_label() }}</span>

                                         </div>
                                         <div class="payment-status">
                                             <span class="font-weight-light">{{ translate('Payment status') }}:</span>
                                             <span
                                                 class="badge {{ $product->payment_status_label() }} text-capitalize">{{ $product->payment_status_label() }}</span>

                                         </div>
                                         @if ($product->shipping_rate_info != null)
                                             <div class="shipping">
                                                 <div class="shipping-info d-flex">
                                                     <span
                                                         class="font-weight-light">{{ translate('Shipping') }}:</span>
                                                     <span class="font-weight-light">{!! currencyExchange($product->delivery_cost) !!}</span>
                                                     <span class="font-weight-light d-flex">(
                                                         @if ($product->shipping_rate_info->carrier_id != null)
                                                             @if ($product->shipping_rate_info->carrier != null)
                                                                 <span class="black">
                                                                     {{ $product->shipping_rate_info->carrier['name'] }}</span>
                                                             @endif
                                                         @else
                                                             <span>
                                                                 {{ $product->shipping_rate_info->name }}</span>
                                                         @endif
                                                         @if ($product->shipping_rate_info->shipping_medium != null)
                                                             <span>{{ translate('Via') }}</span>
                                                             <span>{{ $product->shipping_rate_info->shippied_by() }}</span>
                                                         @endif
                                                         )
                                                     </span>
                                                 </div>
                                             </div>
                                         @endif
                                     </div>
                                 </div>
                             @endif

                         </span>
                         <input type="checkbox" class="order-product-id" value="{{ $product->id }}"
                             @disabled($product->delivery_status == config('tlecommercecore.order_delivery_status.delivered')) data-item="{{ $product }}" name="product[]">
                     </label>
                     <div class="tracking-id tracking-id-{{ $product->id }}">
                         <label class="font-14 bold black">Tracking id</label>
                         <input type="text" value="{{ $product->tracking_id }}" name="{{ $product->id }}-tracking"
                             class="theme-input-style">
                     </div>
                 </div>
             </div>
         @endforeach
     </div>
     <!--End product checkbox-->

     <div class="form-row">
         <!--Delivery status-->
         <div class="form-group col-12 col-lg-6 mb-20">
             <label class="font-14 bold black">{{ translate('Delivery Status') }}<span
                     class="text text-danger">*</span></label>
             <select class="theme-input-style" name="delivery_status" id="delivery_status">
                 <option value="">{{ translate('Select delivery status') }}</option>
                 <option value="{{ config('tlecommercecore.order_delivery_status.pending') }}">
                     {{ translate('Pending') }}
                 </option>
                 <option value="{{ config('tlecommercecore.order_delivery_status.processing') }}">
                     {{ translate('Processing') }}
                 </option>
                 <option value="{{ config('tlecommercecore.order_delivery_status.ready_to_ship') }}">
                     {{ translate('Ready to ship') }}
                 </option>
                 <option value="{{ config('tlecommercecore.order_delivery_status.shipped') }}">
                     {{ translate('Shipped') }}
                 </option>
                 <option value="{{ config('tlecommercecore.order_delivery_status.delivered') }}">
                     {{ translate('Delivered') }}
                 </option>
                 <option value="{{ config('tlecommercecore.order_delivery_status.cancelled') }}">
                     {{ translate('Cancelled') }}
                 </option>
             </select>
             <div class="delivery-status-error invalid-input"></div>
         </div>
         <!--End delivery status-->
         <!--Payment status-->
         <div class="form-group col-12 col-lg-6">
             <label class="font-14 bold black">{{ translate('Payment Status') }}<span
                     class="text text-danger">*</span></label>
             <select class="theme-input-style" name="payment_status" id="payment_status">
                 <option value="">{{ translate('Select payment status') }}</option>
                 <option value="{{ config('tlecommercecore.order_payment_status.unpaid') }}">
                     {{ translate('Unpaid') }}
                 </option>
                 <option value="{{ config('tlecommercecore.order_payment_status.paid') }}">
                     {{ translate('Paid') }}
                 </option>
             </select>
             <div class="payment-status-error invalid-input"></div>
         </div>
         <!--End payment status-->
     </div>
     <!--Comment-->
     <div class="form-row mb-20">
         <label class="font-14 bold black col-12">{{ translate('Comment') }}</label>
         <div class="editor-wrap col-12">
             <textarea name="comment" id="order-comment" class="theme-input-style h-25" rows="2"></textarea>
         </div>
     </div>
     <!--End comment-->
     <div class="form-row">
         <div class="col-12 text-right">
             <button class="btn long update-order-status rounded">{{ translate('Update') }}</button>
         </div>
     </div>
 </form>
 <script src="{{ asset('/public/backend/assets/plugins/summernote/summernote-lite.js') }}"></script>
 <script>
     (function($) {
         "use strict";
         $("#order-comment").summernote({
             tabsize: 2,
             height: 200,
             codeviewIframeFilter: false,
             codeviewFilter: true,
             codeviewFilterRegex: /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
             toolbar: [
                 ["style", ["style"]],
                 ["font", ["fontname", "bold", "underline", "clear"]],
                 ["color", ["color"]],
                 ['insert', ['link']],
                 ["view", ["fullscreen", "codeview", "help"]],
             ],
             callbacks: {
                 onChangeCodeview: function(contents, $editable) {
                     let code = $(this).summernote('code')
                     code = code.replace(
                         /<\/*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|ilayer|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|t(?:itle|extarea)|xml)[^>]*>|on\w+\s*=\s*"[^"]*"|on\w+\s*=\s*'[^']*'|on\w+\s*=\s*[^\s>]+/gi,
                         '')
                     $(this).val(code)
                 }
             }
         });
         /**
          * 
          * Select all products
          **/
         $('.select-all-products').on('change', function(e) {
             if ($('.select-all-products').is(":checked")) {
                 $(".order-product-id").prop("checked", true);
             } else {
                 $(".order-product-id").prop("checked", false);
             }
             checkPaymentAndDeliveryStatus();
         });

         $('.order-product-id').on('change', function(e) {
             checkPaymentAndDeliveryStatus();
         });
         /**
          * 
          * Select product and manage delivery and payment status
          * 
          **/
         function checkPaymentAndDeliveryStatus() {
             $(".payment-status-error").html('');
             $(".delivery-status-error").html('');
             var selected_items = [];
             $('input[name^="product"]:checked').each(function() {
                 selected_items.push($(this).data('item'));
             });

             if (selected_items.length > 0) {
                 if (selected_items.length == 1) {
                     $("#payment_status").val(selected_items[0].payment_status);
                     $("#delivery_status").val(selected_items[0].delivery_status);
                 } else {
                     //delivery status
                     let match_delivery_item = selected_items.filter(item => item.delivery_status == selected_items[
                             0]
                         .delivery_status)
                     if (match_delivery_item.length == selected_items.length) {
                         $("#delivery_status").val(selected_items[0].delivery_status);
                     } else {
                         $("#delivery_status").val("");
                         $(".delivery-status-error").html('your selected items have differrent delivery status');
                     }

                     //payment status
                     let match_payment_item = selected_items.filter(item => item.payment_status == selected_items[0]
                         .payment_status)
                     if (match_payment_item.length == selected_items.length) {
                         $("#payment_status").val(selected_items[0].payment_status);
                     } else {
                         $("#payment_status").val("");
                         $(".payment-status-error").html('your selected items have differrent payment status');
                     }
                 }

             } else {
                 $("#payment_status").val("");
                 $("#delivery_status").val("");
             }
         }
         /**
          * Will update delivery status
          * 
          **/
         $('.update-order-status').on('click', function(e) {
             e.preventDefault();
             $(".products-error").html('');
             $(".payment-status-error").html('');
             $(".delivery-status-error").html('');
             let errors = [];
             let payment_status = $("#payment_status").val();
             let delivery_status = $("#delivery_status").val();

             var selected_items = [];
             $('input[name^="product"]:checked').each(function() {
                 selected_items.push($(this).data('item'));
             });

             if (selected_items.length < 1) {
                 $(".products-error").html('Please select  product ');
                 errors.push('products_error');
             }


             if (!payment_status) {
                 $(".payment-status-error").html('Please select a payment status');
                 errors.push('payment_error');
             }

             if (!delivery_status) {
                 $(".delivery-status-error").html('Please select a payment status');
                 errors.push('delivery_error');
             }


             if (delivery_status == {{ config('tlecommercecore.order_delivery_status.delivered') }} &&
                 payment_status !=
                 {{ config('tlecommercecore.order_payment_status.paid') }}) {
                 $(".payment-status-error").html('please make payment before delivered');
                 errors.push('payment_delivery_error');
             }

             if (errors.length == 0) {
                 $.ajax({
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                     },
                     type: "POST",
                     data: $('#order-status-update-form').serialize(),
                     url: '{{ route('plugin.tlcommercecore.orders.status.update') }}',
                     success: function(response) {
                         if (response.success) {
                             toastr.success(
                                 '{{ translate('Order status updated successfully') }}');
                             $("#order-status--update-modal").modal('hide');
                             location.reload();
                         } else {
                             toastr.error('{{ translate('Update Failed ') }}');
                         }
                     },
                     error: function(response) {
                         toastr.error('{{ translate('Update Failed ') }}');
                     }
                 });
             }

         });
     })(jQuery);
 </script>
