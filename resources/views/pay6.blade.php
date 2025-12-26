<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<button id="pay6">Pay ₹6</button>

<script>
document.getElementById('pay6').onclick = async function () {

    const order = await fetch('/pay-6', {
        method: 'POST',
        headers: { 
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(res => res.json());

    var options = {
        key: "{{ config('services.razorpay.key') }}",
        order_id: order.order_id,
        name: "Course",
        description: "Upfront Payment",
        handler: async function (response) {

            const verify = await fetch('/verify-6', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(response)
            });

            if ((await verify.json()).status === 'success') {
                // Redirect to UPI AutoPay page
                window.location.href = "/enable-autopay";
            }
        }
    };

    new Razorpay(options).open();
};
</script>
