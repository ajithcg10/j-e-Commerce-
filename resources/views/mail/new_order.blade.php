<x-mail::message>
{{-- Header Section --}}
<table width="100%" style="background-color: #2c62a6; color: white; text-align: center; padding: 30px; border-radius: 8px;">
    <tr>
        <td>
            <h1 style="font-size: 28px; margin: 0; color: white;">🎉 Order Placed Successfully!</h1>
            <p style="margin-top: 10px; font-size: 16px;">Order No: R-{{ $order->id }}</p>
            <div style="margin-top: 20px;">
            </div>
        </td>
    </tr>
</table>

{{-- Order Summary --}}
<h2 style="font-size: 20px; margin-top: 30px; color: #2d3748;">
    📦 <span style="border-bottom: 2px solid #f44336;">Order Summary</span>
</h2>
<x-mail::table>
    <table style="width: 100%; border-collapse: collapse; font-size: 15px;">
        <tr>
            <td style="padding: 10px; font-weight: bold;">Order #</td>
            <td style="padding: 10px;">{{ $order->id }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: bold;">Order Date</td>
            <td style="padding: 10px;">{{ $order->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: bold;">Order Amount</td>
            <td style="padding: 10px;">{{ \Illuminate\Support\Number::currency($order->total_price) }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: bold;">Payment Fee</td>
            <td style="padding: 10px;">{{ \Illuminate\Support\Number::currency($order->online_payment_commission ?: 0) }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: bold;">Website Commission</td>
            <td style="padding: 10px;">{{ \Illuminate\Support\Number::currency($order->website_commission ?: 0) }}</td>
        </tr>
        <tr style="border-top: 1px solid #e2e8f0;">
            <td style="padding: 10px; font-weight: bold;">Your Earnings</td>
            <td style="padding: 10px;">{{ \Illuminate\Support\Number::currency($order->vendor_subtotal ?: 0) }}</td>
        </tr>
    </table>
</x-mail::table>

{{-- Ordered Items --}}
<h2 style="font-size: 20px; margin-top: 30px; color: #2d3748;">
    🛍️ <span style="border-bottom: 2px solid #f44336;">Ordered Items</span>
</h2>
<x-mail::table>
    <table style="width: 100%; border-collapse: collapse; font-size: 15px;">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th style="padding: 12px; text-align: left;">Product</th>
                <th style="padding: 12px; text-align: left;">Price</th>
                <th style="padding: 12px; text-align: left;">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 10px;">{{ $item->product->title }}</td>
                    <td style="padding: 10px;">{{ \Illuminate\Support\Number::currency($item->price) }}</td>
                    <td style="padding: 10px;">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-mail::table>

{{-- Message Panel --}}
<x-mail::panel>
    <p style="font-size: 14px; color: #555;">
        Thank you for shopping with us! If you have any questions or need help, feel free to contact our support team anytime.
    </p>
</x-mail::panel>

<p style="font-size: 14px;">Warm regards,</p>
<p style="font-size: 14px;">The {{ config('app.name') }} Team</p>
</x-mail::message>
