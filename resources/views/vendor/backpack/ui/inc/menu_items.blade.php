{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Hotels" icon="la la-question" :link="backpack_url('hotels')" />
<x-backpack::menu-item title="Rooms" icon="la la-question" :link="backpack_url('rooms')" />
<x-backpack::menu-item title="Room photos" icon="la la-question" :link="backpack_url('room-photos')" />
<x-backpack::menu-item title="Reservations" icon="la la-question" :link="backpack_url('reservations')" />

<x-backpack::menu-item title="Admins" icon="la la-question" :link="backpack_url('admins')" />
<x-backpack::menu-item title="Room unavailable dates" icon="la la-question" :link="backpack_url('room-unavailable-dates')" />
<x-backpack::menu-item title="Payments" icon="la la-question" :link="backpack_url('payments')" />
<x-backpack::menu-item title="Payment histories" icon="la la-question" :link="backpack_url('payment-histories')" />