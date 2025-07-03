 <div class="card">
     <div class="card-body">
         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Shop Name') }} </label>
             </div>
             <div class="col-sm-9">
                 <input type="text" name="shop_name" class="theme-input-style shop-name"
                     placeholder="{{ translate('Enter Shop Name') }}" value="{{ getEcommerceSetting('shop_name') }}"
                     required>
             </div>
         </div>
         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Shop Link') }} </label>
             </div>
             <div class="col-sm-9">
                 <a href="{{ url('/shop') }}/{{ getEcommerceSetting('shop_slug') }}"
                     target="_blank">{{ url('') }}/shop/<span
                         id="permalink">{{ getEcommerceSetting('shop_slug') }}</span>
                     <span class="btn custom-btn ml-1 permalink-edit-btn">
                         {{ translate('Edit') }}
                     </span>
                 </a>
                 <input type="hidden" name="shop_slug" id="permalink_input_field"
                     value="{{ getEcommerceSetting('shop_slug') }}" required>
                 <div class="permalink-editor d-none">
                     <input type="text" class="theme-input-style" id="permalink-updated-input"
                         placeholder="{{ translate('Type here') }}">
                     <button type="button" class="btn long mt-2 btn-danger permalink-cancel-btn"
                         data-dismiss="modal">{{ translate('Cancel') }}</button>
                     <button type="button" class="btn long mt-2 permalink-save-btn">{{ translate('Save') }}</button>
                 </div>
             </div>
         </div>
         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Shop Logo') }} </label>
             </div>
             <div class="col-md-8">
                 @include('core::base.includes.media.media_input', [
                     'input' => 'shop_logo',
                     'data' => getEcommerceSetting('shop_logo'),
                 ])
             </div>
         </div>

         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Shop Banner') }} </label>
             </div>
             <div class="col-md-8">
                 @include('core::base.includes.media.media_input', [
                     'input' => 'shop_banner',
                     'data' => getEcommerceSetting('shop_banner'),
                 ])
             </div>
         </div>
         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Shop Phone') }} </label>
             </div>
             <div class="col-sm-9">
                 <input type="text" name="shop_phone" class="theme-input-style"
                     placeholder="{{ translate('Enter Shop Phone') }}" value="{{ getEcommerceSetting('shop_phone') }}"
                     required>
             </div>
         </div>

         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Shop Address') }} </label>
             </div>
             <div class="col-sm-9">
                 <input type="text" name="shop_address" class="theme-input-style"
                     placeholder="{{ translate('Enter Shop Address') }}"
                     value="{{ getEcommerceSetting('shop_address') }}">
             </div>
         </div>

         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Meta Title') }} </label>
             </div>
             <div class="col-sm-9">
                 <input type="text" name="shop_meta_title" class="theme-input-style"
                     placeholder="{{ translate('Enter Meta Title') }}"
                     value="{{ getEcommerceSetting('shop_meta_title') }}" required>
             </div>
         </div>

         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Meta Image') }} </label>
             </div>
             <div class="col-md-8">
                 @include('core::base.includes.media.media_input', [
                     'input' => 'shop_meta_image',
                     'data' => getEcommerceSetting('shop_meta_image'),
                 ])
             </div>
         </div>
         <div class="form-row mb-20">
             <div class="col-sm-3">
                 <label class="font-14 bold black">{{ translate('Meta Description') }}
                 </label>
             </div>
             <div class="col-sm-9">
                 <textarea name="shop_meta_description" class="theme-input-style">{{ getEcommerceSetting('shop_meta_description') }}</textarea>
             </div>
         </div>
     </div>
 </div>
