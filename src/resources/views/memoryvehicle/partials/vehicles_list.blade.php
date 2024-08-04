{{-- src/resources/views/memoryvehicle/partials/vehicles_table.blade.php --}}
<div class="flex flex-col space-y-5">
    @foreach ($vehicles as $vehicle)
        @include('memoryapp::memoryvehicle.partials.vehicle', ['vehicle' => $vehicle])
    @endforeach
</div>
