{{-- src/resources/views/memoryvehicle/fuel/partials/form.blade.php --}}

<form method="POST"
      action="{{ route(isset($fuel) ? 'memory.vehicles.fuel.update' : 'memory.vehicles.fuel.store', [
            'vehicle' => $vehicle,
            'fuel' => $fuel ?? null // Provide default if editing
        ]) }}">

    @csrf
    @if(isset($fuel))
        @method('PUT')
    @endif

    @foreach ($fields as $field)
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="{{ $field['name'] }}">
                {{ trans("memoryapp::messages.vehicle.fuel." . $field['name']) }} @if($field['required'])
                    <span class="text-red-700">*</span>
                @endif
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error($field['name']) border-red-500 @enderror"
                id="{{ $field['name'] }}"
                type="{{ $field['type'] }}"
                name="{{ $field['name'] }}"
                value="{{ old($field['name'],
                        $field['name'] === 'date_of_fuel' && !isset($fuel) ? date('Y-m-d') :
                        ($field['name'] === 'fuel_type' && isset($vehicle) ? $vehicle->fuel_type :
                        ($fuel->{$field['name']} ?? null))) }}"
                placeholder="{{ trans("memoryapp::messages.vehicle.fuel." . $field['name']) }}"
                {{ $field['required'] ? 'required' : '' }}>

            @error($field['name'])
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
    @endforeach

    @include('memoryapp::main.partials.form.actions', [
        'buttonText' => isset($fuel) ? trans("memoryapp::messages.general.Update") : trans("memoryapp::messages.general.Save")
        ])
</form>
