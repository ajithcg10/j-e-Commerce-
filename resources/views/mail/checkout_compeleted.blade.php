<x-mail::message>
<h1 style="text-align: center; font-size: 24px; color: #333;">Thank you for your purchase!</h1>

@foreach ($orders as $order)
<x-mail::table>
<table>
    <tbody>
        <tr>
            <td>Seller</td> 
            <td>
                <a href="{{ url('/') }}">
                    {{ $order->vendor->store_name }}
                </a>
            </td>
        </tr>
        <tr>
            <td>Order #</td>
            <td>{{ $order->id }}</td>
        </tr>
        <tr>
            <td>Items</td>
            <td>{{ $order->orderItems->count() }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td>{{ \Illuminate\Support\Number::currency($order->total_price) }}</td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
        </tr>
    </thead>
<tbody>
@foreach ($order->orderItems as $item)
<tr>
    <td style="padding: 5px;">
        {{ $item->product->title }}
    </td>
    <td>{{ \Illuminate\Support\Number::currency($item->price) }}</td>
    <td>{{ $item->quantity }}</td>
</tr>
@endforeach
</tbody>
</table>
</x-mail::table>

<x-mail::button :url="url('/orders/' . $order->id)">
    View Order Details
</x-mail::button>
@endforeach

<x-mail::subcopy>
    If you have any questions, feel free to contact us.
</x-mail::subcopy>

<x-mail::panel>
    <h2 style="text-align: center; font-size: 20px; color: #333;">Your order has been successfully completed!</h2>
    <p style="text-align: center; font-size: 16px; color: #555;">
        Thank you for shopping with us. We hope you enjoy your purchase!
    </p>
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
