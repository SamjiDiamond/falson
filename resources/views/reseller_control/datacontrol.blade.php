@extends('layouts.layouts')
@section('title', 'Reseller Data Plans')
@section('parentPageTitle', 'Services')

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
                    <p class="text-muted mb-4 font-13">Data Plans</p>

                    @php
                        $currentNetwork = isset($data[0]) ? $data[0]->network : (request()->route('network') ?? 'MTN');
                        $currentServer = request()->route('server');
                    @endphp

                    <div class="row mb-4 ml-1 mr-1">
                        <!-- Left: View Network/Server Filter -->
                        <div class="col-md-7 bg-light p-3 rounded" style="border: 1px solid #e3e6f0;">
                            <form id="filterForm" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="selectNetwork" class="mr-2 font-weight-bold">Network:</label>
                                    <select id="selectNetwork" class="form-control form-control-sm">
                                        <option value="MTN" {{ $currentNetwork == 'MTN' ? 'selected' : '' }}>MTN</option>
                                        <option value="AIRTEL" {{ $currentNetwork == 'AIRTEL' ? 'selected' : '' }}>AIRTEL</option>
                                        <option value="GLO" {{ $currentNetwork == 'GLO' ? 'selected' : '' }}>GLO</option>
                                        <option value="9MOBILE" {{ $currentNetwork == '9MOBILE' ? 'selected' : '' }}>9MOBILE</option>
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label for="selectServer" class="mr-2 font-weight-bold">Server:</label>
                                    <select id="selectServer" class="form-control form-control-sm">
                                        <option value="ALL" {{ $currentServer === null ? 'selected' : '' }}>All Servers</option>
                                        <option value="0" {{ $currentServer === '0' ? 'selected' : '' }}>No Server (0)</option>
                                        <option value="1" {{ $currentServer === '1' ? 'selected' : '' }}>HW (1)</option>
                                        <option value="3" {{ $currentServer === '3' ? 'selected' : '' }}>IYII (3)</option>
                                        <option value="4" {{ $currentServer === '4' ? 'selected' : '' }}>OGDAMS (4)</option>
                                        <option value="5" {{ $currentServer === '5' ? 'selected' : '' }}>UZOBEST (5)</option>
                                        <option value="7" {{ $currentServer === '7' ? 'selected' : '' }}>AUTOSYNCNG (7)</option>
                                    </select>
                                </div>
                                <button type="button" onclick="applyFilter()" class="btn btn-sm btn-primary">View Plans</button>
                            </form>
                        </div>

                        <!-- Right: Dynamic Bulk Action Dropdown & Create Button -->
                        <div class="col-md-5">
                            <div class="d-flex flex-column align-items-end justify-content-between h-100">
                                @if(isset($data[0]) && auth()->user()->canany(['data-plans-disable_all', 'data-plans-disable_cg', 'data-plans-disable_sme', 'data-plans-disable_dg']))
                                    <div class="form-inline bg-light p-3 rounded w-100 justify-content-end mb-2" style="border: 1px solid #e3e6f0;">
                                        <label for="bulkCategory" class="mr-2 font-weight-bold">Category:</label>
                                        <select id="bulkCategory" class="form-control form-control-sm mr-2">
                                            @can('data-plans-disable_all')
                                                <option value="ALL" data-status="{{ $all }}">All Plans</option>
                                            @endcan
                                            @can('data-plans-disable_cg')
                                                <option value="CG" data-status="{{ $cg }}">CG</option>
                                            @endcan
                                            @can('data-plans-disable_sme')
                                                <option value="SME" data-status="{{ $sme }}">SME</option>
                                                <option value="SME2" data-status="{{ $sme2 }}">SME2</option>
                                            @endcan
                                            @can('data-plans-disable_dg')
                                                <option value="DG" data-status="{{ $dg }}">DG</option>
                                                <option value="DATA COUPONS" data-status="{{ $dc }}">Coupons</option>
                                                <option value="DATA TRANSFER" data-status="{{ $dt }}">Transfer</option>
                                            @endcan
                                        </select>
                                        <button type="button" id="bulkActionButton" onclick="applyBulkAction()" class="btn btn-sm btn-secondary"></button>
                                    </div>
                                @endif
                                @can('data-plans-create')
                                    <a href="{{route('reseller.datanew')}}" class="btn btn-gradient-success">Create New Data Plan</a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <script type="text/javascript">
                        function applyFilter() {
                            var network = document.getElementById('selectNetwork').value;
                            var server = document.getElementById('selectServer').value;
                            var url = '';
                            
                            if (server === 'ALL') {
                                url = "{{ route('reseller.dataList', ':network') }}".replace(':network', network);
                            } else {
                                url = "{{ route('reseller.server_dataList', [':network', ':server']) }}"
                                    .replace(':network', network)
                                    .replace(':server', server);
                            }
                            
                            window.location.href = url;
                        }

                        @if(isset($data[0]))
                        function updateBulkButton() {
                            var select = document.getElementById('bulkCategory');
                            if (!select) return;
                            var option = select.options[select.selectedIndex];
                            var status = option.getAttribute('data-status');
                            var btn = document.getElementById('bulkActionButton');
                            
                            if (status === '1') {
                                btn.textContent = 'Disable Category';
                                btn.className = 'btn btn-sm btn-gradient-danger';
                            } else {
                                btn.textContent = 'Enable Category';
                                btn.className = 'btn btn-sm btn-success';
                            }
                        }

                        function applyBulkAction() {
                            var select = document.getElementById('bulkCategory');
                            var category = select.value;
                            var option = select.options[select.selectedIndex];
                            var status = option.getAttribute('data-status');
                            var newStatus = (status === '1') ? '0' : '1';
                            
                            var network = "{{ $currentNetwork }}";
                            var server = "{{ $server }}";
                            
                            var url = "{{ route('reseller.dataserveMultipleedit', [':network', ':type', ':status', ':server']) }}"
                                .replace(':network', network)
                                .replace(':type', category)
                                .replace(':status', newStatus)
                                .replace(':server', server);
                                
                            window.location.href = url;
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            var bulkCat = document.getElementById('bulkCategory');
                            if (bulkCat) {
                                updateBulkButton();
                                bulkCat.addEventListener('change', updateBulkButton);
                            }
                        });
                        @endif
                    </script>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Network</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Level1</th>
                                <th>Level2</th>
                                <th>Level3</th>
                                <th>Level4</th>
                                <th>Level5</th>
                                <th>Server</th>
                                <th>Status</th>
                                <th>Date Modified</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $da)
                            <tr class="gradeX">
                                <td>{{$da['id']}}</td>
                                <td class="center">{{$da['network']}}</td>

                                <td>{{$da['name']}}</td>
                                <td class="center">&#8358;{{number_format($da['price'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level1'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level2'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level3'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level4'])}}</td>
                                <td class="center">&#8358;{{number_format($da['level5'])}}</td>
                                <td>
                                    {{$da['server']}}
                                </td>
                                <td class="center">
                                    @if($da->status=="1")
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    {{$da['updated_at']}}
                                </td>

                                <td class="center">
                                    @can('reseller_data-action')
                                        <a class="btn {{$da->status =="1"? "btn-gradient-danger" : "btn-success" }}" href="{{route('reseller.datacontrolED',$da->id)}}">
                                            {{$da->status =="1"? "Disable" : "Enable" }}
                                        </a>

                                        <a href="{{route('reseller.datacontrolEdit',$da->id )}}"  class="btn btn-secondary">Modify</a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                        </div>
                    {{$data->links()}}

                </div>
            </div>
        </div>
    </div>
@endsection
