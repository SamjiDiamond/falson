@extends('layouts.layouts')
@section('title', 'Reward')
@section('parentPageTitle', 'Promo Code')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <a href="{{ route('promo_codes.create') }}"
                           class="btn btn-primary">Create Reward</a>
                    </div>
                    <p class="text-muted mb-4 font-13">Promo Code List</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Amount</th>
                                <th>Generated For</th>
                                <th>Reuseable</th>
                                <th>Used</th>
                                <th>Used By</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($promoCodes as $promoCode)
                                <tr>
                                    <td>{{ $promoCode->id }}</td>
                                    <td>{{ $promoCode->code }}</td>
                                    <td>₦{{ number_format($promoCode->amount) }}</td>
                                    <td>{{ $promoCode->generated_for }}</td>
                                    <td>{{ (int) $promoCode->reuseable === 1 ? 'Yes' : 'No' }}</td>
                                    <td>{{ (int) $promoCode->used === 1 ? 'Yes' : 'No' }}</td>
                                    <td>{{ $promoCode->usedby }}</td>
                                    <td>{{ $promoCode->created_at }}</td>
                                    <td>
                                        <form action="{{ route('promo_codes.destroy', $promoCode->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this promo code?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $promoCodes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
