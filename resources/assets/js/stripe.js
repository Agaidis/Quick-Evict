if (document.location.href.split('/')[3] === 'new-file') {

        let canvas = document.querySelector("canvas");
        let signaturePad = new SignaturePad(canvas, {});
        let isSlateHouse = $('#user_email').val();
        let payType = $('#user_pay_type').val();

//Clear button to remove signature drawing
        $('.clear_signature').on('click', function () {
            // Clears the canvas
            signaturePad.clear();
        });
// Create a Stripe client.

        console.log('User in stripe js', payType);
        if (payType === 'free') {
            stripe = Stripe('pk_test_FTcQeimeSasisJpDTYgHEMTh');
        } else if (payType === 'full_payment') {
            stripe = Stripe('pk_live_FProm7L9gLEjNsFLawYCp32x');
        }
//

// Create an instance of Elements.
        let elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
        let style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

    const options = {
        layout: {
            type: 'tabs',
            defaultCollapsed: false,
        }
    };

// Create an instance of the card Element.
        let card = elements.create('payment', options);

// Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

// Handle real-time validation errors from the card Element.
        card.addEventListener('change', function (event) {
            let displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

// Handle form submission.
        let form = document.getElementById('pay_sign_submit');
        form.addEventListener('click', function (event) {
            let hcaptchaVal = $('[name=h-captcha-response]').val();
            if (hcaptchaVal === "" || hcaptchaVal === undefined) {
                event.preventDefault();
                alert("Please complete the hCaptcha");
            } else {

                $('#rented_by_val').val($('input[name=rented_by]:checked').val());

                if ($('#user_pay_type').val() === 'free') {
                    let url = '';
                    if ($('#file_type').val() === 'oop' || $('#file_type').val() === 'oopA') {
                        url = 'new-oop/pdf-data';
                    } else if ($('#file_type').val() === 'ltc' || $('#file_type').val() === 'ltcA') {
                        url = 'new-ltc/pdf-data';
                    } else if ($('#file_type').val() === 'civil') {
                        url = 'new-civil-complaint/pdf-data';
                    } else {
                        alert('Error with finding File Type. Contact Support');
                    }
                    if ($('#legal_checkbox').is(':checked')) {
                        $('#modal_signature').modal('toggle');
                        let $body = $("body");
                        $body.addClass("loading");
                        let dataURL = signaturePad.toDataURL(); // save image as PNG
                        $('#signature_source').val(dataURL);

                        let formData = $('#eviction_form').serialize();

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                            },
                            url: url,
                            type: 'POST',
                            data: formData,
                            success: function (data) {
                                window.location.href = environmentPath + '/dashboard';
                            },
                            error: function (data) {
                                console.log(data);
                            },
                        });

                    } else {
                        alert('You need to check the Signature checkbox above to agree to the digital terms in order to continue.')
                    }
                } else {
                    stripe.createToken(card).then(function (result) {
                        if (result.error) {
                            // Inform the user if there was an error.
                            let errorElement = document.getElementById('card-errors');
                            errorElement.textContent = result.error.message;
                        } else {
                            // Send the token to your server.
                            let mainForm = document.getElementById('eviction_form');
                            let hiddenInput = document.createElement('input');
                            hiddenInput.setAttribute('type', 'hidden');
                            hiddenInput.setAttribute('name', 'stripeToken');
                            hiddenInput.setAttribute('value', result.token.id);
                            mainForm.appendChild(hiddenInput);

                            let url = '';
                            if ($('#file_type').val() === 'oop' || $('#file_type').val() === 'oopA') {
                                url = 'new-oop/pdf-data';
                            } else if ($('#file_type').val() === 'ltc' || $('#file_type').val() === 'ltcA') {
                                url = 'new-ltc/pdf-data';
                            } else if ($('#file_type').val() === 'civil') {
                                url = 'new-civil-complaint/pdf-data';
                            } else {
                                alert('Error with finding File Type. Contact Support');
                            }
                            if ($('#legal_checkbox').is(':checked')) {
                                $('#modal_signature').modal('toggle');
                                let $body = $("body");
                                $body.addClass("loading");
                                let dataURL = signaturePad.toDataURL(); // save image as PNG
                                $('#signature_source').val(dataURL);

                                let formData = $('#eviction_form').serialize();

                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    beforeSend: function (xhr) {
                                        xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                                    },
                                    url: url,
                                    type: 'POST',
                                    data: formData,
                                    success: function (data) {
                                        window.location.href = environmentPath + '/dashboard';
                                    },
                                    error: function (data) {
                                    },
                                });

                            } else {
                                alert('You need to check the Signature checkbox above to agree to the digital terms in order to continue.')
                            }
                        }
                    });
                }
            }
        });
}