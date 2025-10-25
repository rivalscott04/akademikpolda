<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i></a>
            <form role="search" class="navbar-form-custom" action="">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search"
                        id="top-search">
                </div>
            </form>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </form>
            </li>
        </ul>
    </nav>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Handler untuk tombol minimize
        $('.navbar-minimalize').click(function(event) {
            event.preventDefault();

            $("body").toggleClass("mini-navbar");

            // Jika menggunakan SmoothScroll
            if (typeof SmoothlyMenu === 'function') {
                SmoothlyMenu();
            }
        });
    });
</script>
