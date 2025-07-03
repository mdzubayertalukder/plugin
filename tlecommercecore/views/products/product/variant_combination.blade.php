  @if (count($combinations[0]) > 0)
      <div class="table-responsive">
          <!-- Variant Combination Table -->
          <table class="table-bordered dh-table">
              <thead>
                  <tr>
                      <th>{{ translate('Variant') }}</th>
                      <th>{{ translate('Purchase Price') }}</th>
                      <th>{{ translate('Unit Price') }}</th>
                      <th>{{ translate('SKU') }}</th>
                      <th>{{ translate('Quantity') }}</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach ($combinations as $key => $combination)
                      @php
                          $name = '';
                          $code = '';
                          $sku = '';
                          $lstKey = array_key_last($combination);
                          
                          foreach ($combination as $option_id => $choice_id) {
                              if ($option_id != 'color') {
                                  $option_name = \Plugin\TlcommerceCore\Models\ProductAttribute::find($option_id)->translation('name');
                                  $choice_name = \Plugin\TlcommerceCore\Models\AttributeValues::find($choice_id)->name;
                              } else {
                                  $option_name = translate('Color');
                                  $choice_name = \Plugin\TlcommerceCore\Models\Colors::find($choice_id)->translation('name');
                              }
                              $name .= $option_name . ':' . $choice_name;
                              $code .= $option_id . ':' . $choice_id . '/';
                              $sku .= '-' . $choice_name;
                              if ($lstKey != $option_id) {
                                  $name .= ' | ';
                                  $choice_name .= ' - ';
                              }
                          }
                      @endphp
                      <tr>
                          <td>
                              <label class="control-label">{{ $name }}</label>
                              <input type="hidden" value="{{ $code }}"
                                  name="variations[{{ $key }}][code]">
                          </td>
                          <td>
                              <input type="number" class="theme-input-style"
                                  name="variations[{{ $key }}][purchase_price]" value="0">
                          </td>
                          <td>
                              <input type="number" class="theme-input-style"
                                  name="variations[{{ $key }}][unit_price]" value="0">
                          </td>
                          <td>
                              <input type="text" class="theme-input-style"
                                  name="variations[{{ $key }}][sku]" value="{{ $sku }}"
                                  placeholder="{{ translate('Sku') }}">
                          </td>
                          <td>
                              <input type="number" class="theme-input-style"
                                  name="variations[{{ $key }}][quantity]" value="0">
                          </td>
                      </tr>
                  @endforeach
          </table>
          <!-- End Variant combination -->
      </div>
  @else
      <p class="alert alert-danger m-2">{{ translate('No variant selected yet') }}</p>
  @endif
