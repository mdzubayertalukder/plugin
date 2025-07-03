@extends('core::base.layouts.master')
@section('title')
{{ translate('bKash Payment') }}
@endsection
@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('custom_css')
<style>
    .bkash-payment-container {
        max-width: 500px;
        margin: 50px auto;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background: white;
    }

    .bkash-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .payment-amount {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        color: #E2136E;
        margin-bottom: 30px;
    }

    .bkash-button {
        background: #E2136E;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 5px;
        font-size: 16px;
        width: 100%;
        cursor: pointer;
        transition: background 0.3s;
    }

    .bkash-button:hover {
        background: #c1105c;
    }

    .bkash-button:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .loading {
        display: none;
        text-align: center;
        margin-top: 20px;
    }

    .error-message {
        color: #dc3545;
        text-align: center;
        margin-top: 10px;
        display: none;
    }
</style>
@endsection
@section('main_content')
<div class="bkash-payment-container">
    <div class="bkash-logo">
        <img src="https://seeklogo.com/images/B/bkash-logo-FBB258B90F-seeklogo.com.png" alt="bKash" height="60">
    </div>

    <div class="payment-amount">
        {{ translate('Amount to Pay') }}: {{ $total_payable_amount }} {{ $currency }}
    </div>

    <button id="bkash-button" class="bkash-button">
        {{ translate('Pay with bKash') }}
    </button>

    <div class="loading" id="loading">
        <i class="fa fa-spinner fa-spin"></i> {{ translate('Processing payment...') }}
    </div>

    <div class="error-message" id="error-message"></div>

    <!-- Hidden form data -->
    <input type="hidden" id="total_payable_amount" value="{{ $total_payable_amount }}">
    <input type="hidden" id="currency" value="{{ $currency }}">
    <input type="hidden" id="app_key" value="{{ $app_key }}">
    <input type="hidden" id="base_url" value="{{ $base_url }}">
</div>
@endsection

@section('custom_scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        'use strict';

        let paymentToken = null;
        let paymentID = null;

        const appKey = $('#app_key').val();
        const baseUrl = $('#base_url').val();
        const amount = $('#total_payable_amount').val();
        const currency = $('#currency').val();

        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // Get bKash token first
        function getBkashToken() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '{{ route("plugin.saas.bkash.get.token") }}',
                    type: 'POST',
                    data: {},
                    success: function(response) {
                        console.log('Token response:', response);
                        if (response.success) {
                            paymentToken = response.token;
                            resolve(response.token);
                        } else {
                            reject(response.message || 'Failed to get token');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Token request failed:', xhr.responseText);
                        if (xhr.status === 419) {
                            reject('Session expired. Please refresh the page and try again.');
                        } else if (xhr.status === 500) {
                            reject('Server error. Please check your bKash configuration.');
                        } else {
                            reject('Connection error: ' + (xhr.responseJSON?.message || error));
                        }
                    }
                });
            });
        }

        // Create payment
        function createPayment() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '{{ route("plugin.saas.bkash.create.payment") }}',
                    type: 'POST',
                    data: {
                        token: paymentToken
                    },
                    success: function(response) {
                        console.log('Create payment response:', response);
                        if (response.success) {
                            paymentID = response.data.paymentID;
                            resolve(response.data);
                        } else {
                            reject(response.message || 'Failed to create payment');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Create payment failed:', xhr.responseText);
                        reject('Failed to create payment: ' + (xhr.responseJSON?.message || error));
                    }
                });
            });
        }

        // Execute payment
        function executePayment() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '{{ route("plugin.saas.bkash.execute.payment") }}',
                    type: 'POST',
                    data: {
                        token: paymentToken,
                        paymentID: paymentID
                    },
                    success: function(response) {
                        console.log('Execute payment response:', response);
                        if (response.success) {
                            resolve(response);
                        } else {
                            reject(response.message || 'Failed to execute payment');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Execute payment failed:', xhr.responseText);
                        reject('Failed to execute payment: ' + (xhr.responseJSON?.message || error));
                    }
                });
            });
        }

        // Show error message
        function showError(message) {
            $('#error-message').text(message).show();
            $('#loading').hide();
            $('#bkash-button').prop('disabled', false);
        }

        // Hide error message
        function hideError() {
            $('#error-message').hide();
        }

        // Main payment flow
        $('#bkash-button').on('click', async function() {
            hideError();
            $(this).prop('disabled', true);
            $('#loading').show();

            try {
                // Step 1: Get token
                await getBkashToken();

                // Step 2: Create payment
                const paymentData = await createPayment();

                // Step 3: Redirect to bKash payment page
                if (paymentData.bkashURL) {
                    window.location.href = paymentData.bkashURL;
                } else {
                    // Step 4: Execute payment (for direct flow)
                    const result = await executePayment();

                    if (result.redirect_url) {
                        window.location.href = result.redirect_url;
                    } else {
                        showError('Payment completed but redirect failed');
                    }
                }

            } catch (error) {
                console.error('Payment error:', error);
                showError(error || 'Payment failed. Please try again.');
            }
        });
    });
</script>
@endsection