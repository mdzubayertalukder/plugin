@php
$number = rand(10, 100);
@endphp
<td>
    <div class="input-group addon">
        <input type="text" name="carrier_condition[{{ $number }}][min_weight]" class="form-control style--two"
            placeholder="0.00">
        <div class="input-group-append">
            <div class="input-group-text px-3 bold">{{ translate('Kg') }}
            </div>
        </div>
    </div>
</td>
<td>
    <div class="input-group addon">
        <input type="text" name="carrier_condition[{{ $number }}][max_weight]" class="form-control style--two"
            placeholder="0.00">
        <div class="input-group-append">
            <div class="input-group-text px-3 bold">{{ translate('Kg') }}
            </div>
        </div>
    </div>
</td>
<td>
    <div class="input-group addon">
        <div class="input-group-prepend">
            <div class="input-group-text px-3 bold">{{ currencySymbol() }}</div>
        </div>
        <input type="text" name="carrier_condition[{{ $number }}][cost]" class="form-control"
            placeholder="0.00">

    </div>
</td>
<td>
    <a href="#" class="delete-weight-range text-danger" onclick="removeWeightRange(this)">
        <i class="icofont-ui-delete"></i>
    </a>
</td>
