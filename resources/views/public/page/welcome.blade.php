@extends('public.layout.base')

@section('page_title'){{ __('Home') }}@endsection

@section('vendor_stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">
@endsection

@section('header_stylesheets')
    <link rel="stylesheet" href="{{ asset('assets/public/css/home.css') }}?ver=1.1.5">
@endsection

@section('page_content')
    <section class="container-fluid slider p-0">
        <div class="hero-slider">
            @foreach($slides as $slide)
                <div>
                    <div class="slider-item" style="background-image: url({{ asset($slide->img) }})">
                        <div class="overlay"></div>
                        <div class="caption">
                            @if($slide->head)
                                <h2>{{ $slide->head }}</h2>
                            @endif
                            @if($slide->subh)
                                <p class="mb-3">{{ $slide->subh }}</p>
                            @endif
                            @if($slide->link)
                                <a href="{{ $slide->link }}" class="btn">
                                    {{ $slide->btn_text }}
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section><!-- End Hero -->

    <!-- ======= Section ======= -->
    <section class="about-sec pt-4">
        <div class="container-fluid p-0">
            <div class="bg-bar">
                <div class="overlay"></div>
                <img src="{{ asset('assets/img/honper-bgi.webp') }}" />
            </div>

            <div class="container-fluid honpers">
                <div class="row">
                    @foreach($persons as $person)
                        <div class="col-md-3 d-flex flex-column center honper">
                            <figure class="d-flex center">
                                <img src="{{ asset('assets/img/' . $person->img) }}" />
                            </figure>
                            <div class="caption text-center m-0">
                                <h4>{{ $person->name }}</h4>
                                <h5>({{ $person->title }})</h5>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="container-fluid pt-5">
                <div class="row">
                    <h2 class="fw-bold">
                        <span class="colthemb">U</span><span>-PREPARE</span>
                    </h2>
                </div>

                <div class="row">
                    <div class="col-lg-7 col-xl-8">
                        <p class="text-justify">
                            The Uttarakhand Disaster Resilience and Preparedness Project, funded by the World Bank with an
                            investment of US$200 million, is dedicated to safeguarding the lives and livelihoods of 10 million
                            people across the state by enhancing the climate and disaster resilience of critical public infrastructure
                            in Uttarakhand and encompasses a range of transformative initiatives. These include enhancing infrastructure
                            access, creating sophisticated early warning systems, and establishing strategically located fire stations to
                            protect communities. The project prioritizes fortifying infrastructure resilience, elevating emergency
                            preparedness, and implementing proactive measures for forest fire prevention and management. It emphasizes
                            efficient project management and places a strong focus on capacity building and training for local authorities
                            and communities, fostering a culture of disaster risk awareness and reduction. The use of innovative technologies
                            for advanced risk assessment is integral to the initiative. By collaborating closely with governmental,
                            non-governmental, and international partners, the project adopts a holistic approach to resilience. Integrating
                            these measures into development planning seeks to craft a safer and more resilient Uttarakhand, paving the way
                            for a secure and sustainable future for all.
                        </p>
                    </div>

                    <div class="col-lg-5 col-xl-4">
                        <div class="announcement-board h-100">
                            <div class="head text-center">
                                <h3 class="m-0 d-flex center text-white">
                                    <img src="{{ asset('assets/img/icons/megaphone-white.png') }}" >
                                    {!! request()->cookie('lang') === 'hi' ? 'घोषणा'  : 'ANNOUNCEMENTS' !!}
                                </h3>
                            </div>
                            <div class="body p-3">
                                <ul class="list-unstyled">
				    <li>
					<img class="me-2" src="{{ asset('assets/img/icons/bullet.png') }}">
					<a href="{{ url('tenders-and-notices') }}">Tenders & Notices</a>
				    </li>
				    {{--
                                    @if(isset($announcements))
                                        @forelse($announcements as $announcement)
                                            <li>
                                                <img class="me-2" src="{{ asset('assets/img/icons/bullet.png') }}">
                                                <a href="{{ route('announcement', $announcement->slug) }}"> {!! request()->cookie('lang') === 'hi' ? $announcement->hin_title : $announcement->eng_title !!}</a>
                                            </li>
                                        @empty
                                            <li>
                                                <img class="me-2" src="{{ asset('assets/img/icons/bullet.png')}}" >
                                                No Announcements Found
                                            </li>
                                        @endforelse
                                    @endif
				    --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="citizen-corner py-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-4">
                        <span class="d-block">CITIZEN CORNER</span>
                        <span class="hr"></span>
                    </h2>
                </div>
            </div>

            <div class="row">
                @foreach ($cc_items as $item)
                    <div class="col-md-4 d-flex justify-content-center mb-4">
                        <a href="{{ $item->link }}" class="cc-item p-4 d-flex align-items-center">
                            <img class="me-2" src="{{ asset('assets/img/icons/' . $item->img) }}" >
                            <h6 class="mb-0">{{ $item->name }}</h6>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="past-projects">
        <div class="container-fluid p-0">
            <div class="pps-slider">
                @foreach ($pps_items as $key=> $item)
                    <div>
                        <div class="pps-item prel">
                            <div class="bg w-100 h-100">
                                <div class="overlay w-100 h-100 {{ $item->bgc }}"></div>
                                <img class="w-100 h-100" src="{{ asset($item->img) }}" >
                            </div>
                            <div class="content d-flex flex-column align-items-center justify-content-between h-100 prel">
                                <h6 class="fw-bold text-white">{{ $item->name }}</h6>
                                <h2 class="fw-bold text-white">{{ $item->title }}</h2>
                                <a href="{{ $item->link }}" @class(['btn', 'btn-pp', 'btn-af'=> $key])>{{ $item->link_txt }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="components">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="comp-head d-flex align-items-end justify-content-between mb-3">
                        <div class="left mb-1">
                            <h3 class="mb-0">
                                <i class="bi bi-newspaper"></i>
                                COMPONENTS
                            </h3>
                        </div>
                        <div class="right">
                            &nbsp;
                            {{-- <a class="text-dark" href="#">
                                <i class="bi bi-chevron-double-right"></i>
                                Read more
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="components-slider">
                @if(isset($components))
                @forelse($components as $comp)
                    <div>
                        <div class="comps-item">
                            <div class="ci-img">
                                <img src="{{$comp->image}}" />
                            </div>
                            <div class="ci-content p-2">
                                <h4>{!! request()->cookie('lang') === 'hi' ? substr($comp->page_hin_title,0, 50) : substr($comp->page_eng_title,0, 50)!!}</h4>
                                <p class="mb-0">{!! request()->cookie('lang') === 'hi' 
                                    ? mb_substr($comp->hin_content, 0, 500) 
                                    : mb_substr($comp->eng_content, 0, 600) !!}
                                </p>
                            </div>
                            <div class="ci-rms text-center pb-3">
                                <a href="{{ $comp->link }}" class="rmore">
                                    <span>READ MORE</span>
                                    <i class="bi bi-caret-right-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
                @endif
            </div>
        </div>
    </section>

    <section class="videos">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="vid-head d-flex align-items-end justify-content-between mb-3">
                        <div class="left mb-1">
                            <h3 class="mb-0">
                                <i class="bi bi-camera-reels"></i>
                                VIDEOS
                            </h3>
                        </div>
                        <div class="right">
                            <a class="text-white" href="#">
                                <i class="bi bi-chevron-double-right"></i>
                                Click to U-PREPARE-YouTube-channel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="videos-slider">
                @for($v=0;$v<3;$v++)
                    @foreach($videos as $vid)
                        <div>
                            <div class="vid-item">
                                <div class="vid-img">
                                    <img src="{{ asset($vid->img) }}" />
                                    <a class="d-flex center" href="#">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                </div>
                                <div class="vid-content">
                                    <p class="mb-0">{{ $vid->text }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endfor
            </div>
        </div>
    </section>

    <section class="contact">
        <div class="contact-head text-center">
            <h3>REACH US</h3>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-lg-6 col-xl-5">
                    <a class="text-decoration-underline text-dark" href="#">Find us here</a>
                    <div class="map mb-2">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3442.544367980115!2d78.08280947495017!3d30.36389647476432!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3908d70048644c07%3A0xa0a0da3e097c93a4!2sUSDMA%20New%20Building%20IT%20park!5e0!3m2!1sen!2sin!4v1721896382781!5m2!1sen!2sin" class="w-100 h-100 border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <address>
                        <h5>5<sup>th</sup> FLOOR, USDMA NEW BUILDING</h5>
                        <ul class="list-unstyled mb-0">
                            <li>
                                <i class="bi bi-geo-alt-fill"></i>
                                36, IT Park, Dehradun, Uttarakhand, 248013
                            </li>
                            <li>
                                <i class="bi bi-telephone-fill"></i>
                                <a href="tel:18001804276">1800-180-4276</a>,
				<a href="tel:01352971663">0135-2971663</a>
                            </li>
                            <li>
                                <i class="bi bi-envelope-fill"></i>
                                <a href="mailto:upreparegrievance@gmail.com">upreparegrievance@gmail.com</a>
                            </li>
                        </ul>
                    </address>
                </div>
                <div class="col-xl-2 col-lg-1 d-lg-block d-none"></div>
                <div class="col-md-6 col-xl-4 mt-4 mt-md-0">
                    <h4 class="text-center">
                        <i class="bi bi-chat-left-dots"></i>
                        Feedback
                    </h4>
                    <form id="ajax-form" data-action="{{route('feedback')}}"  data-method="POST">
                        @csrf
                        <input type="text" class="form-control mb-3" placeholder="NAME*" name="name">
                        <input type="email" class="form-control mb-3" placeholder="E-MAIL*" name="email">
                        <select name="type" id="" class="form-control mb-3">
                            <option value="">Kindly Select Query Type</option>
                            <option value="inquiry">INQUIRY</option>
                            <option value="feedback">FEEDBACK</option>
                            <option value="others">OTHERS</option>
                        </select>
                        <input type="text" class="form-control mb-3" placeholder="SUBJECT" name="subject">
                        <textarea name="message" rows="4" class="form-control mb-3" placeholder="MESSAGE"></textarea>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-theme">Submit</button>
                        </div>
                    </form>
                </div>
                <div class="d-xl-block d-none col-xl-1"></div>
            </div>
        </div>
    </section>
@endsection

@section('footer_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script>

<script>
    $(document).on('submit', '#ajax-form', function (e) {
        e.preventDefault();
        const form = $(this);
        const actionUrl = form.data('action');
        const method = form.data('method');
        const formData = form.serializeArray(); 

        $.ajax({
            url: actionUrl,
            type: method,
            data: formData,
            success: function (response) {
                toastr.success(response.message, "Success");
                setTimeout(() => {
                    location.reload();
                }, 3000);
            },
            error: function (xhr) {
                if (xhr.status === 422) { // Validation error
                    const errors = xhr.responseJSON.errors;
                    $('.is-invalid').removeClass('is-invalid'); // Clear previous errors

                    // Loop through errors and display them
                    $.each(errors, function (key, value) {
                        const input = $(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').remove();
                        input.after(`<div class="invalid-feedback">${value[0]}</div>`);
                    });

                    // Show validation error toaster
                    toastr.error("Please check the highlighted fields and try again.", "Validation Error");
                } else {
                    // Show general error toaster
                    toastr.error("Something went wrong. Please try again.", "Error");
                }
            }
        });
    });
</script>
   
@endsection

@section('inpage_scripts')
    var homeSlider = tns({
        items: 1,
        slideBy: 'page',
        autoplay: true,
        container: '.hero-slider',
        navPosition: 'bottom',
        autoplayButton: false,
        autoplayButtonOutput: false,
        controlsText: [
            '<span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>',
            '<span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>'
        ]
    });

    var ppsSlider = tns({
        nav: 0,
        items: 1,
        speed: 1000,
        slideBy: 'page',
        autoplay: true,
        controls: false,
        container: '.pps-slider',
        navPosition: 'bottom',
        autoplayButton: false,
        autoplayButtonOutput: false,
    });

    var compSlider = tns({
        items: 4,
        slideBy: 'page',
        autoplay: true,
        controls: false,
        container: '.components-slider',
        responsive: {
            0: {
                items: 1
            },
            767: {
                items: 2
            },
            900: {
                items: 3
            },
            1100: {
                items: 4
            }
        },
        navPosition: 'bottom',
        autoplayButton: false,
        autoplayButtonOutput: false,
    })

    var vidSlider = tns({
        items: 4,
        slideBy: 'page',
        autoplay: true,
        controls: false,
        container: '.videos-slider',
        responsive: {
            0: {
                items: 1
            },
            767: {
                items: 2
            },
            900: {
                items: 3
            },
            1100: {
                items: 4
            }
        },
        navPosition: 'bottom',
        autoplayButton: false,
        autoplayButtonOutput: false,
    })
@endsection
