{{-- resources/views/subscribe.blade.php --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mt-5">
    <h2>Course Subscription - ₹6 upfront + ₹4/month AutoPay</h2>
    <button id="subscribe-btn" class="btn btn-success">Subscribe Now</button>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('subscribe-btn').onclick = function() {
    fetch("{{ route('create.subscription') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) return alert(data.error);

        const options = {
            key: data.key,
            subscription_id: data.subscription_id,
            name: "HRMS Course",
            description: "₹6 upfront + ₹4/month UPI AutoPay",
            theme: { color: "#528FF0" },
            handler: function(response) {
                verifyPayment(response);
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();
    });
};

function verifyPayment(response) {
    fetch("{{ route('verify.payment') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(response)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('✅ Subscription activated! ₹4 will auto-debit monthly.');
            window.location.reload();
        } else {
            alert('❌ Payment failed');
        }
    });
}
</script>
<style>
    #subscribe-btn {
        padding: 10px 20px;
        font-size: 18px;
    }
</style>