<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Razorpay Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        @auth
            <h2>Welcome, {{ Auth::user()->name }}!</h2>
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

            <button id="payBtn" class="btn btn-primary">Enable UPI AutoPay</button>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>

            <script>
            document.getElementById('payBtn').onclick = async function () {

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
                    name: "My Course",
                    description: "UPI AutoPay Subscription",
                    handler: function (response) {
                        alert('Mandate Approved');
                    }
                };

                var rzp = new Razorpay(options);
                rzp.open();
            };
            </script>
        @else
            <div class="alert alert-info">
                You need to login to access the payment features.
                <a href="{{ route('login') }}" class="btn btn-primary mt-2">Login</a>
            </div>
        @endauth
    </div>
</body>
</html>
