{{--{{ $vehicle }}--}}
<div class="mx-4 rounded-xl bg-white p-3 shadow-xl sm:rounded-lg">
    <div class="flex items-center justify-between space-x-4">
        <div class="flex w-full justify-between">
            <div class="text-2xl">
                <a href="{{ route('memory.vehicles.show', $vehicle->uuid) }}"
                   class="text-blue-600 hover:text-blue-900">
                    {{ $vehicle->brand }} {{ $vehicle->model }}
                </a>
            </div>
            <div class="">
                <a href="{{ route('memory.vehicles.show', $vehicle->uuid) }}">
                    @include('memoryapp::memoryvehicle.partials.plate', ['plate' => $vehicle->plate_number])
                </a>
            </div>
        </div>
        <a href="{{ route('memory.vehicles.edit', $vehicle) }}"
           class="text-green-600 hover:text-indigo-900" title="{{ trans('memoryapp::messages.general.Edit') }}">
            <i class="fa fa-square-pen fa-fw text-2xl "></i>
        </a>
    </div>
    <div class="flex flex-col rounded-lg border bg-indigo-50 px-2 py-2 shadow lg:w-1/2 space-y-2">
        <h2 class="text-left text-blue-800 w-full bg-blue-100 px-2 py-1 rounded">
            {{ trans('memoryapp::messages.vehicle.fuel.fuel') }} @if($vehicle->fuel_type) <small>({{ $vehicle->fuel_type  }})</small>@endif
        </h2>
        <div class="flex flex-row items-end">
            <div class="flex w-full items-center justify-between">
                @if($vehicle->latestFuel)
                    <table class="w-full">
                        <thead>
                        <tr>
                            <th>
                                {{ trans('memoryapp::messages.general.Date') }}
                            </th>
                            <th>
                                {{ trans('memoryapp::messages.vehicle.fuel.quantity') }}
                            </th>
                            <th>
                                {{ trans('memoryapp::messages.general.total_sum') }}
                            </th>
                        </tr>
                        </thead>
                        <tr class="text-center">
                            <td>
                                <a href="{{ route('memory.vehicles.fuel.show', ['vehicle' => $vehicle->uuid, 'fuel' => $vehicle->latestFuel->uuid]) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    {{ $vehicle->latestFuel->date_of_fuel }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('memory.vehicles.fuel.show', ['vehicle' => $vehicle->uuid, 'fuel' => $vehicle->latestFuel->uuid]) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    {{ $vehicle->latestFuel->fuel_quantity }} l
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('memory.vehicles.fuel.show', ['vehicle' => $vehicle->uuid, 'fuel' => $vehicle->latestFuel->uuid]) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    {{ $vehicle->latestFuel->total_price }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('memory.vehicles.fuel.edit', ['vehicle' => $vehicle->uuid, 'fuel' => $vehicle->latestFuel->uuid]) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fa fa-edit"></i>
                            </td>
                        </tr>
                    </table>
                @else
                    <div class="italic">
                        {{ trans('memoryapp::messages.vehicle.fuel.no_fuel') }}
                    </div>
                @endif
            </div>
            <a href="{{ route('memory.vehicles.fuel.create', ['vehicle' => $vehicle->uuid]) }}"
               class="whitespace-nowrap rounded bg-green-200 px-2 py-1 text-green-900 shadow transition duration-300 hover:bg-green-500 hover:text-indigo-900 border border-green-600/30 ">
                <i class="fa-solid fa-fw fa-gas-pump"></i>
                {{ trans('memoryapp::messages.general.New') }}
            </a>
        </div>
    </div>
</div>
