 @if ($colors)
     <div class="card-body">
         @foreach ($colors as $key => $color)
             @php
                 $color_name = \Plugin\TlcommerceCore\Models\Colors::find($color)->translation('name');
             @endphp
             <div class="form-row mb-20">
                 <div class="col-sm-3">
                     <label class="font-14 bold black">{{ $color_name }} </label>
                 </div>
                 <div class="col-sm-9">
                     @include('core::base.includes.media.media_input_multi_select', [
                         'input' => 'color_' . $color . '_image',
                         'data' => old('color_' . $color . '_image'),
                         'indicator' => $color,
                         'container_id' => '#multi_input_' . $color,
                         'user_filter' => $file_user_filter,
                     ])
                     @if ($errors->has('color_' . $color . '_image'))
                         <div class="invalid-input">{{ $errors->first('color_' . $color . '_image') }}</div>
                     @endif
                 </div>
             </div>
         @endforeach
     </div>
 @else
     <p class="alert alert-danger m-2">{{ translate('No color variant selected yet') }}</p>
 @endif
