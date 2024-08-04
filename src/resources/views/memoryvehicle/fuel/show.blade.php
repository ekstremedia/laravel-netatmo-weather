{{-- src/resources/views/memoryvehicle/fuel/show.blade.php --}}

@extends('memoryapp::layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
            <h2 class="text-xl font-bold mb-4">Fuel Details</h2>

            <div class="mb-4">
                <strong>Name:</strong> {{ $vehicle->name }}
            </div>

            {{ $fuel }}

            <!-- Repeat for other vehicle attributes -->
            <div class="mb-4">
                <strong>Plate Number:</strong> {{ $vehicle->plate_number }}
            </div>

            <!-- ... other vehicle details ... -->

            <a href="{{ route('memory.vehicles.index') }}" class="text-blue-700 hover:text-blue-900">Back to List</a>
        </div>
    </div>
@endsection
