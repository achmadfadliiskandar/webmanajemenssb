<nav id="sidebar">
    <div class="sidebar-header text-center">
        <h4 class="mb-0">SSB Beji Putra</h4>
    </div>

    <ul class="list-unstyled components">
        <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        </li>
        <li class="{{ Request::is('admin/user*') ? 'active' : '' }}">
            {{-- <a href="#"><i class="fas fa-users me-2"></i> Data Pengguna</a> --}}
            <a href="{{ route('admin.user') }}"><i class="fas fa-users me-2"></i> Data Pengguna</a>
        </li>
        <li class="{{ Request::is('admin/pemain*') ? 'active' : '' }}">
            <a href="{{ route('admin.pemain') }}"><i class="fa-regular fa-futbol me-2"></i> Data Pemain</a>
        </li>
        <li class="{{ Request::is('admin/jadwal*') ? 'active' : '' }}">
            {{-- <a href="#"><i class="fa-solid fa-calendar-days me-2"></i> Jadwal</a> --}}
            <a href="{{ route('admin.jadwal') }}"><i class="fa-regular fa-calendar me-2"></i> Agenda</a>
        </li>
        <li class="{{ Request::is('admin/absen*') ? 'active' : '' }}">
            <a href="{{ route('admin.absen') }}"><i class="fa-solid fa-list-check"></i> Absensi</a>
        </li>
        <li class="{{ Request::is('admin/penilaian*') ? 'active' : '' }}">
            <a href="{{ route('admin.penilaian') }}"><i class="fas fa-chart-bar me-2"></i> Penilaian</a>
        </li>
        <li>
            <livewire:admin.logout/>
        </li>
    </ul>
</nav>