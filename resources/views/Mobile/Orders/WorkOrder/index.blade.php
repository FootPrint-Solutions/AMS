{{-- Work Order Custom Css --}}
<link rel="stylesheet" href="{{ asset('css/work-order.css') }}">

<div class="container">
    <h3>
        Work Order
    </h3>

    {{-- form search with side icon --}}
    <div class="row">
        <div class="col">
            <div class="top-nav-search-custom-mobile">
                <form>
                    <input type="text" class="form-control" placeholder="Search here">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
        <div class="col text-justify">
            {{-- button rounded with dot icon --}} <i class="material-icons">face</i>

            <button class="btn btn-primary rounded-circle float-right mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"
                    fill="#e8eaed">
                    <path
                        d="M263.79-408Q234-408 213-429.21t-21-51Q192-510 213.21-531t51-21Q294-552 315-530.79t21 51Q336-450 314.79-429t-51 21Zm216 0Q450-408 429-429.21t-21-51Q408-510 429.21-531t51-21Q510-552 531-530.79t21 51Q552-450 530.79-429t-51 21Zm216 0Q666-408 645-429.21t-21-51Q624-510 645.21-531t51-21Q726-552 747-530.79t21 51Q768-450 746.79-429t-51 21Z" />
                </svg>
            </button>
        </div>
    </div>


</div>
