<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
</head>
<body>
    <form action="{{url('/checkout/vnpay')}}" method="POST">
        @csrf
        <input type="text" hidden="true" name="id" value= "25">
        <button type="submit" name="redirect">Payment VNPay</button>
    </form>
    <form action="{{url('/checkout/momopay')}}" method="POST">
        @csrf
        <input type="text" hidden="true" name="id" value= "25">
        <button type="submit" name="payUrl">Payment MOMO</button>
    </form>
</body>
</html>