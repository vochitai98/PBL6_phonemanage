<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
</head>

<body>
    <form action="{{url('/checkout/vnpay')}}" method="POST">
        <!-- Thông tin đơn hàng -->
    <input type="label" name="vnp_Amount" value="10000">
    <div></div> <!-- Tổng giá trị thanh toán (đơn vị: VNĐ) -->
    <input type="label" name="vnp_OrderInfo" value="Mua hàng tại Shopee">
    <div></div>
    <input type="label" name="vnp_TxnRef" value="ORDER123">
    <div></div>
    <input type="label" name="vnp_Command" value="pay">
    <div></div>
    <input type="label" name="vnp_CreateDate" value="20211205120000">
    <div></div>
        @csrf
        <button type="submit" name="redirect">Payment VNPay</button>
    </form>
    <!-- <form action="{{url('/checkout/momopay')}}" method="POST">
        @csrf
        <input type="text" hidden="true" name="id" value= "25">
        <button type="submit" name="payUrl">Payment MOMO</button>
    </form> -->
</body>
</html>