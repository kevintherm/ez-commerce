<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="/dashboard">{{ env('APP_NAME') }}</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
            class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..."
                aria-describedby="btnNavbarSearch" />
            <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="/home"><i class="fa-solid fa-house"></i>
                        Beranda</a></li>
                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-bell"></i>
                        Notifikasi</a></li>
                <li><a class="dropdown-item" href="/cart"><i class="fa-solid fa-cart-shopping"></i>
                        Keranjang</a></li>
                <li class="dropdown-divider"></li>
                <li><a href="/dashboard/profile"
                        class="dropdown-item {{ request()->is('dashboard/profile*') ? 'active' : '' }}"><i
                            class="fa-solid fa-user"></i>
                        Profil</a></li>
                <li><a class="dropdown-item {{ request()->is('dashboard/shop*') ? 'active' : '' }}"
                        href="/dashboard/shop"><i class="fa-solid fa-shop"></i>
                        Toko</a></li>
                <li>
                    <a role="button" class="dropdown-item"
                        onclick="Swal.fire({title:'Confirm Logout',text:'Are You Sure?',icon:'question',showCancelButton:true,confirmButtonText:'Yes',cancelButtonText: 'Cancel',reverseButtons: true}).then((result)=>{if(result.isConfirmed)location='/logout?token={{ csrf_token() }}'})">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
