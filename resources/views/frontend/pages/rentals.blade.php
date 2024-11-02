@extends('frontend.layout.app')
@section('content')
    <!-- Rentals Section -->
    <section class="py-5">
        <div class="container">
            <h1 class="text-center mb-4">Available Cars for Rent</h1>

            <!-- Filter Section -->
            <form id="filterForm" method="GET" action="{{ route('frontend.rentals') }}" >
                <div class="row mb-4">
                    <div class="col-md-4">
                        <select class="form-select" name="car_type" aria-label="Filter by Car Type" onchange="applyFilter()">
                            <option value="" selected disabled>Filter by Car Type</option>
                            <option value="" selected>All Car</option>
                            @foreach($carTypes as $type)
                                <option value="{{ $type }}" {{ request('car_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="brand" aria-label="Filter by Brand" onchange="applyFilter()">
                            <option selected disabled>Filter by Brand</option>
                            <option value="" selected>All Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="number" class="form-control" name="daily_rent" placeholder="Max Daily Rent price" value="{{ request('daily_rent') }}" onchange="applyFilter()">
                        </div>
                    </div>
                </div>
            </form>
            <!-- Car Listing -->
            <div class="row">
                <!-- Car 1 -->
            @foreach ($cars as $car)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="{{ url($car->image) ?? asset('images/no_image.jpg') }}" class="card-img-top" alt="Car Image">
                        <div class="card-body">
                            <h5 class="card-title">{{ $car->name }}</h5>
                            <p class="card-text">Brand: {{ $car->brand }} | Type: {{ $car->car_type }} | Year: {{ $car->year }}</p>
                            <p class="card-text">Daily Rent: ${{ $car->daily_rent_price }}</p>
                            <a href="{{ route('carDetails',$car->id) }}" class="btn btn-primary">Rent Now</a>
                        </div>
                    </div>
                </div>
            @endforeach

            </div>
        </div>
    </section>

    <script>
        function applyFilter() {
            document.getElementById('filterForm').submit();
        }
    </script>
@endsection
