@extends('company.admin.main')

@section('title', 'Food Location')

@section('content')

<div class="food-menu-index">
    <section>
        <main>

            <!-- ✅ Success Message -->
            @if (session('success-message'))
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class='bx bxs-check-circle me-2 fs-5'></i>
                    <div>{{ session('success-message') }}</div>
                </div>
            @endif

            <!-- ✅ Header Section -->
            <div class="header"> 
                <div class="left"> 
                    <h1>Food Location</h1> 
                </div>
                <a href="{{ route('food-location-create') }}" class="create"> 
                    <span>Add Food Location </span> 
                </a>
            </div>

            <!-- ✅ Data Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <!-- ✅ Table Wrapper -->
                    <div class="table-responsive">
                        <table id="foodTable" class="table align-middle table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 5%;">#</th>
                                    <th scope="col">Food Name</th>
                                    <th scope="col">Location</th>
                                    <th scope="col" style="width: 15%;">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($food as $index => $item)
                                    <tr>
                                        <td>{{ $food->firstItem() + $index }}</td>
                                        <td>{{ $item->foodMenu->name ?? 'N/A' }}</td>
                                        <td>{{ $item->location->location_name ?? 'N/A' }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class='bx bx-info-circle me-1'></i> No Food Locations Found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- ✅ Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $food->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>

        </main>
    </section>
</div>

@endsection


@section('scripts')
<!-- ✅ jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#foodTable').DataTable({
            paging: false,       // disable DataTable pagination (Laravel handles it)
            ordering: true,      // enable column sorting
            info: false,         // hide info text
            searching: true,     // enable search
            columnDefs: [
                { orderable: false, targets: 0 } // disable sorting on first column
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search food or location..."
            }
        });
    });
</script>
@endsection
