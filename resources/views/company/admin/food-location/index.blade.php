@extends('company.admin.main')
@section('title', 'Food Menu')
@section('content')

<div class="food-menu-index">
    <section>
        <main>
            @if (session('success-message'))
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class='bx bxs-check-circle me-2'></i>
                    <div>
                        {{ session('success-message') }}
                    </div>
                </div>
            @endif

            <div class="header"> 
                <div class="left"> 
                    <h1>Food Location</h1> 
                </div>
                <a href="{{ route('food-location-create') }}" class="create"> 
                    <span>Add Food Location </span> 
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table id="foodTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Food Name</th>
                                <th>Location</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($food as $index => $item)
                                <tr>
                                    <td>{{ $food->firstItem() + $index }}</td>
                                    <td>{{ $item->foodMenu->name ?? 'N/A' }}</td>
                                    <td>{{ $item->location->location_name ?? 'N/A' }}</td>
                                    <td>{{ $item->price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $food->links('partials.paginator') }}
                    </div>
                </div>
            </div>

        </main>
    </section>
</div>

@endsection

@section('scripts')
<!-- jQuery and DataTables CDN -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#foodTable').DataTable({
            "paging": false, 
            "ordering": true,
            "info": false,
            "searching": true,
            "columnDefs": [
                { "orderable": false, "targets": 0 } 
            ]
        });
    });
</script>
@endsection
