<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cart') }}
        </h2>
    </x-slot>

    <div>
        <p></p>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">

                @if ($cart && count($cart->courses) > 0)
                    @foreach ($cart->courses as $course)
                        <div class="bg-light mb-3 p-3 d-flex justify-content-between align-items-center rounded">
                            <h6 class="mb-0">
                                {{ $course->name }}
                                <small class="text-primary ms-2">{{ $course->price()}}</small>
                            </h6>
                            <a href="{{ route('cart.delete', $course) }}" class="btn btn-sm btn-danger">Remove</a>
                        </div>
                    @endforeach

                        <div class="bg-light mt-4 p-3 d-flex justify-content-between align-items-center rounded">
                            <h6 class="mb-0">
                                Total
                                <small class="text-primary ms-2">{{$cart->total()}}</small>
                            </h6>
                            <a href="{{route('direct.paymentMethod')}}" class="btn btn-sm btn-success">Checkout</a>
                        </div>
                @else
                    <div class="alert alert-info">Your Cart Is Empty</div>
                @endif



            </div>
        </div>
    </div>

    </div>

</x-app-layout>
