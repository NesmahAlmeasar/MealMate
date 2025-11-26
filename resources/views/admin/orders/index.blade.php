@extends('layouts.admin_app')

@section('title', 'Orders Management')

@push('styles')
<style>
    .orders-container {
        padding: 20px;
    }
    .orders-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .orders-table {
        width: 100%;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .orders-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .orders-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }
    .orders-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    .orders-table tr:hover {
        background: #f8f9fa;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
    }
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    .status-processing {
        background: #cfe2ff;
        color: #084298;
    }
    .status-completed {
        background: #d1e7dd;
        color: #0f5132;
    }
    .status-cancelled {
        background: #f8d7da;
        color: #842029;
    }
    .btn-view {
        background: #17a2b8;
        color: white;
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 13px;
    }
    .no-orders {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
</style>
@endpush

@section('content')
<div class="orders-container">
    <div class="orders-header">
        <h1>Orders Management</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($orders->count() > 0)
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Client</th>
                    <th>Restaurant</th>
                    <th>Date & Time</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td><strong>#{{ $order->cart_id }}</strong></td>
                        <td>
                            @if($order->client && $order->client->user)
                                {{ $order->client->user->Fname }} {{ $order->client->user->Lname }}
                                <br><small style="color: #666;">{{ $order->client->user->phone ?? 'N/A' }}</small>
                            @else
                                <span style="color: #999;">Unknown Client</span>
                            @endif
                        </td>
                        <td>
                            @if($order->restaurant)
                                {{ $order->restaurant->name }}
                            @else
                                <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td>
                            {{ $order->date->format('Y-m-d') }}<br>
                            <small style="color: #666;">{{ $order->time }}</small>
                        </td>
                        <td><strong>${{ number_format($order->total_price, 2) }}</strong></td>
                        <td>
                            <span class="status-badge status-{{ strtolower($order->state) }}">
                                {{ ucfirst($order->state) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->cart_id) }}" class="btn-view">View Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-orders">
            <p style="font-size: 18px; margin-bottom: 10px;">No orders found</p>
            <p>Orders will appear here once customers place them</p>
        </div>
    @endif
</div>
@endsection
