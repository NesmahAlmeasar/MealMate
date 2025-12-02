@extends('layouts.admin_app')

@section('title', 'Orders Management')

@section('content')
<div>
    <div class="page-header">
        <h1 class="page-title">Orders Management</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="table-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">رقم الطلب</th>
                        <th>العميل</th>
                        <th>المطعم</th>
                        <th>التاريخ والوقت</th>
                        <th>السعر الإجمالي</th>
                        <th class="col-status">الحالة</th>
                        <th class="col-actions">الإجراءات</th>
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
                                <div class="action-buttons">
                                    <a href="{{ route('admin.orders.show', $order->cart_id) }}" class="action-btn view-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 10px;">No orders found</p>
            <p>Orders will appear here once customers place them</p>
        </div>
    @endif
</div>
@endsection
