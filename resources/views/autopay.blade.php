<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<button id="autoPay">Enable AutoPay (₹4 next month)</button>

<script>
document.getElementById('autoPay').onclick = async function () {

    const res = await fetch('/create-subscription', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    const data = await res.json();

    var options = {
        key: "{{ config('services.razorpay.key') }}",
        subscription_id: data.subscription_id,
        name: "Course AutoPay",
        description: "₹4 auto-debit next month",
        handler: function () {
            alert("AutoPay enabled successfully!");
        }
    };

    new Razorpay(options).open();
};
</script>
