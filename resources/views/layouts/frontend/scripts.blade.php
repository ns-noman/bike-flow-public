<script src="{{ asset('public/frontend-assets') }}/js/modernizr.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/jquery-3.6.0.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/popper.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/bootstrap.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/owl.carousel.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/jquery.nice-select.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/jquery.waypoints.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/jquery.counterup.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/vegas.slider.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/mixitup.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/lightcase.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/wow.min.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/mobile-menu.js"></script>
<script src="{{ asset('public/frontend-assets') }}/js/script.js"></script>
<script src="{{ asset('public/frontend-assets') }}/plugins/toastr/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        $('.owl-carousel').owlCarousel({
            stagePadding: 30,
            smartSpeed: 450,
            animateOut: 'fadeOut',
            loop: true,
            margin: 5,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false
                },
                600: {
                    items: 2,
                    nav: false
                },
                768: {
                    items: 3,
                    nav: false
                },
                1000: {
                    items: 4,
                    nav: false
                }
            }
        });
    });

</script>