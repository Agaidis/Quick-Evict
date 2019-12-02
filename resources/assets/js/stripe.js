if (document.location.href.split('/')[3] === 'new-file') {
    let canvas = document.querySelector("canvas");
    let signaturePad = new SignaturePad(canvas, {});

//Clear button to remove signature drawing
    $('.clear_signature').on('click', function () {
        // Clears the canvas
        signaturePad.clear();
    });
// Create a Stripe client.
    var stripe = Stripe('pk_test_FTcQeimeSasisJpDTYgHEMTh');

// Create an instance of Elements.
    var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
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

// Create an instance of the card Element.
    var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');

// Handle real-time validation errors from the card Element.
    card.addEventListener('change', function (event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

// Handle form submission.
    let form = document.getElementById('pay_sign_submit');
    form.addEventListener('click', function (event) {
        console.log('made it in here');

        stripe.createToken(card).then(function (result) {
            if (result.error) {
                // Inform the user if there was an error.
                let errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server.
                console.log(result.token.id);
                $('stripe_token').val(result.token.id);
            }
        });

        let url = '';
        if ($('#file_type').val() === 'oop') {
            url = 'new-oop/pdf-data';
        } else if ($('#file_type').val() === 'ltc') {
            url = 'new-ltc/pdf-data';
        } else if ($('#file_type').val() === 'civil') {
            url = 'new-civil-complaint/pdf-data';
        } else {
            alert('Error with finding File Type. Contact Support');
        }
        if ($('#legal_checkbox').is(':checked')) {
            console.log($('#eviction_form').serialize());
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
                    console.log(data);
                    window.location.href = environmentPath + '/dashboard';
                },
                error: function (data) {
                    console.log(data)
                },
            });

        } else {
            alert('You need to check the Signature checkbox above to agree to the digital terms in order to continue.')
        }
    });

    // Submit the form with the token ID.
    function stripeTokenHandler(token) {






        // Submit the form
        // form.submit();
    }
}