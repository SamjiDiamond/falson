@extends('layouts.layouts')
@section('title', 'Reward')
@section('parentPageTitle', 'Promo Code')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            <strong>{{ session('success') }}</strong>
                        </div>
                        <script type="text/javascript">
                            toastr.options = {
                                closeButton: true,
                                progressBar: true,
                                showMethod: 'slideDown',
                                timeOut: 4000
                            };
                            toastr.success('{{ session('success') }}', 'Success');
                        </script>
                    @endif
                    <div class="mb-3">
                        <a href="{{ route('promo_codes.create') }}"
                           class="btn btn-primary">{{ __('Create New Promo Code') }}</a>
                    </div>
                    <p class="text-muted mb-4 font-13">Promo Code List</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Reward') }}</th>
                                <th>{{ __('Max Redemptions') }}</th>
                                <th>{{ __('Redeemed') }}</th>
                                <th>{{ __('Start Date') }}</th>
                                <th>{{ __('End Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="gradeX">
                            @foreach ($promoCodes as $promoCode)
                                <tr>
                                    <td>{{ $promoCode->id }}</td>
                                    <td>{{ $promoCode->code }}</td>
                                    <td>{{ ucfirst($promoCode->type) }}</td>
                                    <td>
                                        @if ($promoCode->type === 'fixed')
                                            ₦{{ number_format($promoCode->reward_amount, 2) }}
                                        @elseif ($promoCode->type === 'percentage')
                                            {{ $promoCode->reward_amount }}%
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </td>
                                    <td>{{ $promoCode->max_redemptions ?? __('Unlimited') }}</td>
                                    <td>{{ $promoCode->redemptions_count }}</td>
                                    <td>{{ $promoCode->start_date ? $promoCode->start_date->format('Y-m-d H:i:s') : __('N/A') }}</td>
                                    <td>{{ $promoCode->end_date ? $promoCode->end_date->format('Y-m-d H:i:s') : __('N/A') }}</td>
                                    <td>
                                        @if ($promoCode->is_active)
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $promoCode->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <a href="{{ route('admin.promo_codes.edit', $promoCode->id) }}"
                                           class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                        <form action="{{ route('admin.promo_codes.destroy', $promoCode->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('{{ __('Are you sure you want to delete this promo code?') }}')">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
