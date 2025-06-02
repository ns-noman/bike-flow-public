<!-- Start Footer Area -->
<footer class="footer">
    <!-- Footer Top -->
    <div class="footer-top pt-70 pb-20">
        <div class="container">
            <div class="row">
                <!-- Single -->
                <div class="col-lg-3 col-md-6 mb-30">
                    <div class="f-widgets-single">
                        <!-- Logo -->
                        <div class="flogo mb-20 py-2">
                            <a href="{{ route('home.index') }}">
                                <img src="{{ asset('public/uploads/basic-info/'.$basicInfo->logo) }}" alt="Magura Group">
                            </a>
                    </div>
                        <p><i class="fas fa-map-marker-alt"></i>{{ $basicInfo->address }}</p>
                    </div>
                </div>
                <!-- Single -->
                <div class="col-lg-3 col-md-6 mb-30">
                    <div class="f-widgets-single">
                        <h2>Contact</h2>
                        <p>
                            <i class="fa fa-phone"></i> {{ $basicInfo->telephone }}<br>
                            <!--<i class="fa fa-fax"></i> {{ $basicInfo->fax }}<br>-->
                            <i class="fa fa-mobile"></i> {{ $basicInfo->phone }}<br>
                            <a style="text-decoration: none; color: inherit; cursor: pointer;" href="mailto:{{ $basicInfo->email }}"><i class="fa fa-envelope"></i> {{ $basicInfo->email }}</a><br>
                            <a style="text-decoration: none; color: inherit; cursor: pointer;" href="{{ $basicInfo->web_link }}"><i class="fa fa-globe"></i> {{ $basicInfo->web_link }}</a><br>
                        </p>
                        <div class="f-social">
                            <span><a target="_blank" href="{{ $basicInfo->facebook_link }}"><i class="fab fa-facebook-f"></i></a></span>
                            <span><a target="_blank" href="{{ $basicInfo->twitter_link }}"><i class="fab fa-twitter"></i></a></span>
                            <span><a target="_blank" href="{{ $basicInfo->linkedin_link }}"><i class="fab fa-linkedin"></i></a></span>
                            <span><a target="_blank" href="{{ $basicInfo->youtube_link }}"><i class="fab fa-youtube"></i></a></span>
                        </div>
                    </div>
                </div>
                <!-- Single -->
                <div class="col-lg-2 col-md-6 mb-30">
                    <div class="f-widgets-single">
                        <h2>Pages</h2>
                        <ul>
                            @foreach ($pages as $page)
                                <li><a href="{{ $page['slug'] }}">{{ $page['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <!-- Single -->
                <div class="col-lg-3 col-md-6 mb-30">
                    <div class="f-widgets-single">
                        <h2>Newsletter</h2>
                        <p>Subscribe to our weekly Newsletter and receive updates via email.</p>
                        <div class="sub-c-form">
                            <form action="#">
                                <input type="email" name="email" placeholder="Enter Your Email">
                                <button type="submit"><i class="fas fa-paper-plane"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Bottom -->
    <div class="footer-bottom text-center pt-30 pb-30">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="copy-text">
                        <p>Copyright © 2024 <a href="https://maguragroup.com.bd/batl/">BATL</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Start Footer Area -->

<div class="scroll-area">
    <i class="bi bi-arrow-up"></i>
</div>