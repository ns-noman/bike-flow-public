@extends('layouts.frontend.master')
@section('content')

	<!-- Start Hero Area -->
	<style>
		.section-bg .carousel-inner .carousel-caption h1 {
            font-size: 50px !important;
            color: #fff !important;
            font-size: 18px;
            text-shadow: 8px 8px 16px rgba(0, 0, 0, 1); /* Adjust values as needed */
            font-family: "Times New Roman";
        }

		.section-bg .carousel-inner .carousel-caption p{
			font-size: 30px;
			color: #d4dee4;
		}
		.section-bg .carousel-inner .carousel-caption {
			left: 50%; 
			transform: translate(-50%, 0); /* Center horizontally */
			bottom: 20px;
			font-size: 1.5rem;
			opacity: 0; /* Start with hidden */
			transition: opacity 0.5s ease, transform 0.5s ease;
		}
	
		.section-bg .carousel-inner .carousel-caption.show {
			opacity: 1; /* Show when active */
			transform: translate(-50%, 0); /* No movement */
		}
	
		.section-bg .carousel-inner .carousel-item {
			position: relative; /* Ensure item is positioned */
		}
	
		.section-bg .carousel-inner .carousel-item img {
			width: 100%;
			height: auto;
		}
	</style>
	
	<section class="section-bg">
		<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-pause="false">
			<div class="carousel-indicators">
                @foreach($data['sliders'] as $key => $slider)
				    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : '' }}" aria-label="{{ $slider['title'] }}"></button>
                @endforeach
			</div>
			<div class="carousel-inner">
                @foreach($data['sliders'] as $key => $slider)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}" data-bs-interval="3000">
                        <img src="{{ asset('public/uploads/sliders/'.$slider['image']) }}" class="d-block" alt="...">
                        <div class="carousel-caption d-none d-md-block">
                            <!--<h1>{{ $slider['title'] }}</h1>-->
                            <!--<p>{{ $slider['description'] }}</p>-->
                        </div>
                    </div>
                @endforeach
			</div>
			<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
	</section>
	<!-- Animation Trigger Script -->
	<script>
		const carousel = document.getElementById('carouselExampleCaptions');

		carousel.addEventListener('slide.bs.carousel', function (event) {
			const captions = carousel.querySelectorAll('.carousel-caption');
			captions.forEach(caption => {
				caption.classList.remove('show'); // Remove the show class from all captions
			});
		});
	
		carousel.addEventListener('slid.bs.carousel', function (event) {
			const activeCaption = event.relatedTarget.querySelector('.carousel-caption');
			if (activeCaption) {
				activeCaption.classList.add('show'); // Add the show class to the active caption
			}
		});
	
		// Initialize the first caption to be visible
		const initialCaption = carousel.querySelector('.carousel-item.active .carousel-caption');
		if (initialCaption) {
			initialCaption.classList.add('show');
		}
	</script>
	<!-- End Hero Area -->



	<!-- Start About Area -->
	<section class="about-area section-padding">
		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<div class="about-img">
						<img src="{{ asset('public/frontend-assets/img/corporate-profile.jpg') }}" alt="img">
						<!--<div class="about-vedio">-->
						<!--	<div class="video-btn">-->
						<!--		<a data-rel="lightcase" href="{{ $data['basicInfo']['video_embed_1'] }}">-->
						<!--			<i class="fas fa-play"></i>-->
						<!--		</a>-->
						<!--	</div>-->
						<!--</div>	-->
					</div>
				</div>
				<div class="col-lg-6">
					<div class="about-content">
						<h4 class="sc_subtitle"><span>WELCOME TO MAGURA 	GROUP</span></h4>
						{{-- <h2 class="sc_title"></h2> --}}
						<p>With a legacy of over 25 years, Magura Group has evolved into a diverse enterprise, serving Bangladesh through its trusted ventures in food, clothing, housing, education, and healthcare. Known for its quality products and fair pricing, the Group has built a strong reputation, reflecting its commitment to enhancing lives across multiple sectors.</p>
						<p>Magura Group has shown its adaptability by embracing technology, establishing Bangladesh Advanced Technologies Ltd. to support the nation’s digital transformation and keep pace with global advancements. This innovative outlook underscores the Group's understanding of modern demands and its proactive approach to meeting them.</p>
						<p>Beyond business success, Magura Group plays a key role in Bangladesh's economic development by creating jobs, investing in local industries, and fostering export-driven growth. Each branch of the Group operates with a mission to uplift communities, contribute to the economy, and support a better quality of life for people across the nation.</p>
						<p>As Magura Group looks to the future, it carries forward a sense of optimism, aiming to deepen its social commitment to the people and progress of Bangladesh. Their pledge is clear: to continue growing while staying rooted in their core values of integrity, innovation, and social responsibility.</p>
						<a class="button-1" href="{{ route('home.index', ['corporate-profiles']) }}">About Us</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End About Area -->
    
	<!-- Start Counter Area -->
	<section class="section-padding-2 counter-area" style="background-image:url({{ asset('public/frontend-assets/img/bg-1.png') }});">
		<div class="container">
			<div class="row">
				<!-- Single -->
				<div class="col-lg-3 col-md-6 mb-30">
					<div class="counter-item">
						<div class="icon">
							<i class="fas fa-dollar-sign"></i>
						</div>
						<div class="content">
							<div class="title">
								<h2 class="counter">{{ number_format($data['basicInfo']['assets_value'], $decimals = 0, $dec_point = ".", $thousands_sep = ",") }}</h2>
								<h3>+</h3>
							</div>
							<p>Million Dollars Assets's Value</p>
						</div>
					</div>
				</div>
				<!-- Single -->
				<div class="col-lg-3 col-md-6 mb-30">
					<div class="counter-item">
						<div class="icon">
							<i class="fas fa-users"></i>
						</div>
						<div class="content">
							<div class="title">
								<h2 class="counter">{{ number_format($data['basicInfo']['total_employees'], $decimals = 0, $dec_point = ".", $thousands_sep = ",") }}</h2>
								<h3>+</h3>
							</div>
							<p>Total Employees</p>
						</div>
					</div>
				</div>
				<!-- Single -->
				<div class="col-lg-3 col-md-6 mb-30">
					<div class="counter-item">
						<div class="icon">
							<i class="fas fa-city"></i>
						</div>
						<div class="content">
							<div class="title">
								<h2 class="counter">{{ $data['basicInfo']['total_companies'] }}</h2>
								<!-- <h3>+</h3> -->
							</div>
							<p>Affiliated companies</p>
						</div>
					</div>
				</div>
				<!-- Single -->
				<div class="col-lg-3 col-md-6 mb-30">
					<div class="counter-item">
						<div class="icon">
							<i class="fas fa-briefcase"></i>
						</div>
						<div class="content">
							<div class="title">
								<h2 class="counter">{{ Date('Y') - $data['basicInfo']['start_year'] }}</h2>
								<h3>+</h3>
							</div>
							<p>Years in Operation</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Start Counter Area -->


	<!-- Start Services Area -->
	<section class="section-padding section-bg">
		<div class="container">
			<!-- Section Headding -->
			<div class="row">
				<div class="col-lg-12 text-center">
					<div class="section-headding">
						<h4 class="sc_subtitle"><span>Our Services</span></h4>
						<h2 class="sc_title">Our Main Focus</h2>
					</div>
				</div>
			</div>
			<div class="row">
                @foreach($data['serviceTypes'] as $key => $serviceType)
                    <div class="col-lg-4 col-md-6 mb-30">
                        <div class="info-box">
                            <div class="icon">{!! $serviceType['icon'] !!}</div>
                            <div class="content">
                                <h2><a href="{{ route('home.index', ['services', $serviceType['id']]) }}">{{ $serviceType['title'] }}</a></h2>
                                <p style="height: 120px;">{{ $serviceType['description'] }}</p>
                                <a class="get_link" href="{{ route('home.index', ['services', $serviceType['id']]) }}">View Details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
			</div>
		</div>
	</section>
	<!-- End Services Area -->

	<!-- Start Featured Product Area -->
	<section class="section-padding" style="background: #b6faf6;">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
					<div class="section-headding">
						<h4 class="sc_subtitle"><span>Products</span></h4>
						<h2 class="sc_title">Our Products</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="featured-property owl-carousel">
                    @foreach($data['products'] as $key => $product)
                        <div class="product-item">
                            <div class="thumbnail">
                                <a href="{{ route('home.index', ['products', $product['id']]) }}">
                                    <img style="height: 255.75px;" src="{{ asset("public/uploads/products/". $product['image']) }}" alt="img">
                                </a>
                                <div class="product-overly">
                                    <ul>
                                        <li><a href="{{ route('home.index', ['products', $product['id']]) }}" title="Views:2021"><i class="bi bi-eye"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="content">
                                <h2 class="title"><a href="{{ route('home.index', ['products', $product['id']]) }}">{{ $product['title'] }}</a></h2>
                            </div>
                        </div>
                    @endforeach
				</div>
			</div>
		</div>
	</section>
	<!-- End Featured Product Area -->


	<!-- Start Video Popup Area -->
	<div class="vedio-popup-area" style="background-image:url({{ asset('public/frontend-assets/img/mg-headoffice.jpg') }});">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="vedio-popup-btn">
						<div class="video-btn">
							<a data-rel="lightcase" href="{{ $data['basicInfo']['video_embed_2'] }}"><i class="fas fa-play"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Video Popup Area -->


	<!-- Start Category Area -->
	<section class="section-padding-2"  style="background: #94fdec;">
		<div class="container">
			<!-- Section Headding -->
			<div class="row">
				<div class="col-lg-12 mb-50 text-center">
					<div class="section-headding">
						<h4 class="sc_subtitle"><span>Companies</span></h4>
						<h2 class="sc_title">Our Companies</h2>
					</div>
				</div>
			</div>

			
			<style>
				.owl-carousel .item a {
				  height: 150px;
				  color: #080808;
				  background: #1cf5f5;
				  border-radius: 10px;
				  box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.7);
				  display: flex;
				  align-items: center;
				  padding: 5px;
				  text-decoration:none;
				}
				.owl-carousel .item h4 {
				  margin: 0;
				  font-size: 22px;
				  color: #080808;
				  text-transform:uppercase;
				  font-weight:bold;
				}
				.owl-carousel .item img {
				  width: 75px;
				  height: 75px;
				}
				.owl-carousel .item {
				  display: flex;
				  align-items: center;
				  padding: 5px;
				  height: 170px;
				  width: auto;
				}
			  
				/* Responsive layout adjustments */
				@media (max-width: 767px) {
				  .owl-carousel .item img {
					width: 50px;
					height: 50px;
				  }
				  .owl-carousel .item a {
					flex-direction: column;
					text-align: center;
				  }
				  .owl-carousel .item h4 {
					font-size: 14px;
				  }
				  .owl-carousel .item a {
					padding: 5px;
				  }
				}
			</style>
			<div class="row">
				<div class="owl-carousel owl-theme">
                    @foreach($data['companies'] as $key => $company)
                        <div class="item">
                            <a href="{{ $company['site_link'] }}">
                                <img src="{{ asset("public/uploads/companies/". $company['logo']) }}" alt="">
                                <h4>{{ $company['title'] }}</h4>
                            </a>
                        </div>
                    @endforeach
				</div>
			</div>
		</div>
	</section>
	<!-- End Category Area -->
	<style>
		.blog-item .content p {
			min-height: 90px;
			overflow: hidden;
		}
		.blog-item .content h3 {
			min-height: 60px;
			overflow: hidden;
		}
	</style>
	<!-- Start Blog Area -->
	<section class="section-padding-2">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 text-center">
					<div class="section-headding">
						<h4 class="sc_subtitle"><span>Blog & News</span></h4>
						<h2 class="sc_title">Latest Blog</h2>
					</div>
				</div>
			</div>
			<div class="row">
				@foreach ($data['blogs'] as $blog)
					<div class="col-lg-4 col-md-6">
						<div class="blog-item">
							<div class="thumnail">
								<a href="{{ route('home.index', ['blogs', $blog['id']]) }}">
									<img style="width: 387px; height: 259px" src="{{ asset("public/uploads/blogs/". $blog['image']) }}" alt="{{ $blog['alt'] }}">
								</a>
							</div>
							<div class="content">
								@php
									$date = \Carbon\Carbon::parse($blog['created_at']);
									$day = $date->day;
									$month = $date->format('M');
									$year = $date->year;
								@endphp
								<div class="post-date">
									<span>{{ $day }}</span>
									{{ $month }}, {{ $year }}
								</div>
								<div class="blog-meta">
									<a href="#"><i class="fas fa-user"></i> {{ $blog['admin']['name'] }}</a>
									<a href="#"><i class="fas fa-tags"></i> {{ $blog['blogcategory']['title'] }}</a>
								</div>
								<h3><a href="{{ route('home.index', ['blogs', $blog['id']]) }}">{{ Str::limit($blog['title'], 50, '...') }}</a></h3>
								<p>{{ Str::limit($blog['short_description'], 120, '...') }}</p>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>
	<!-- End Blog Area -->

	<!-- Start Instagram Feed -->
	<div class="ins-feed">
		<div class="instagram-feed owl-carousel">
			@foreach ($data['galleris'] as $gallery)
				<div class="ins-single">
					<a data-rel="lightcase:myCollection:instagram" href="{{ asset("public/uploads/galleries/". $gallery['image']) }}">
						<img src="{{ asset("public/uploads/galleries/". $gallery['image']) }}" alt="{{ $gallery['alt'] }}">
					</a>
				</div>
			@endforeach
		</div>
	</div>
	<!-- End Instagram Feed -->
@endsection